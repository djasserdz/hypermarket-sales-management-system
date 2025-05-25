<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransfersResource\Pages;
use App\Models\Transfers;
use Filament\Tables\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransfersResource extends Resource
{
    protected static ?string $model = Transfers::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Stock Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make("product.name")->label("Product Name"),
                TextColumn::make("fromSupermarket.name")->label("From Supermarket"),
                TextColumn::make("toSupermarket.name")->label("Destination Supermarket"),
                TextColumn::make("quantity")->label("Quantity"),
                TextColumn::make("status")->label("Status"),
            ])
            ->defaultSort("created_at","desc")
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('approve')
                    ->label("Approve")
                    ->color("success")
                    ->action(function (Transfers $record) {
                        $record->update([
                            'status' => 'in_transit',
                        ]);
                        Notification::make()
                            ->title('Transfer Approved')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->hidden(fn (Transfers $record) => $record->status !== 'pending'),

                Action::make('deliver')
                    ->label("Mark as Delivered")
                    ->color("primary")
                    ->action(function (Transfers $record) {
                        $record->update([
                            'status' => 'delivered',
                        ]);
                        Notification::make()
                            ->title('Transfer Marked as Delivered')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->hidden(fn (Transfers $record) => $record->status !== 'in_transit'),
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
            'index' => Pages\ListTransfers::route('/'),
            'create' => Pages\CreateTransfers::route('/create'),
            'edit' => Pages\EditTransfers::route('/{record}/edit'),
        ];
    }
}
