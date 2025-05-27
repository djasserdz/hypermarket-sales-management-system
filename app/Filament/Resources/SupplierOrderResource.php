<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierOrderResource\Pages;
use App\Models\SupplierOrder;
use App\Models\Product;
use App\Models\Supermarket;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB; // Added for transaction

class SupplierOrderResource extends Resource
{
    protected static ?string $model = SupplierOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Stock Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $product = Product::find($state);
                        if ($product) {
                            $set('supplier_id', $product->supplier_id);
                        }
                    }),
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->disabled() // Disabled as it's derived from product
                    ->required(),
                Select::make('supermarket_id')
                    ->relationship('supermarket', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('quantity_ordered')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                Select::make('status')
                    ->options([
                        'pending_approval' => 'Pending Approval',
                        'ordered' => 'Ordered',
                        'shipped' => 'Shipped',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending_approval')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('product.name')->searchable()->sortable(),
                TextColumn::make('supplier.name')->searchable()->sortable(),
                TextColumn::make('supermarket.name')->label('For Supermarket')->searchable()->sortable(),
                TextColumn::make('quantity_ordered'),
                TextColumn::make('status')->badge()
                    ->colors([
                        'gray' => 'pending_approval',
                        'warning' => 'ordered',
                        'info' => 'shipped',
                        'success' => 'received',
                        'danger' => 'cancelled',
                    ]),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_approval' => 'Pending Approval',
                        'ordered' => 'Ordered',
                        'shipped' => 'Shipped',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('supermarket_id')
                    ->label('Supermarket')
                    ->relationship('supermarket', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('place_order')
                    ->label('Place Order')
                    ->color('warning')
                    ->icon('heroicon-o-paper-airplane')
                    ->action(function (SupplierOrder $record) {
                        $record->update(['status' => 'ordered']);
                        Notification::make()->title('Order Placed')->success()->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (SupplierOrder $record) => $record->status === 'pending_approval'),
                Action::make('mark_shipped')
                    ->label('Mark as Shipped')
                    ->color('info')
                    ->icon('heroicon-o-truck')
                    ->action(function (SupplierOrder $record) {
                        $record->update(['status' => 'shipped']);
                        Notification::make()->title('Order Marked as Shipped')->success()->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (SupplierOrder $record) => $record->status === 'ordered'),
                Action::make('mark_received')
                    ->label('Mark as Received')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (SupplierOrder $record) {
                        DB::transaction(function () use ($record) {
                            $record->update(['status' => 'received']);

                            $stock = Stock::firstOrNew([
                                'product_id' => $record->product_id,
                                'supermarket_id' => $record->supermarket_id,
                            ]);
                            $stock->quantity += $record->quantity_ordered;
                            $stock->save();
                        });
                        Notification::make()->title('Order Marked as Received & Stock Updated')->success()->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (SupplierOrder $record) => $record->status === 'shipped'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupplierOrders::route('/'),
            'create' => Pages\CreateSupplierOrder::route('/create'),
            'edit' => Pages\EditSupplierOrder::route('/{record}/edit'),
        ];
    }
} 