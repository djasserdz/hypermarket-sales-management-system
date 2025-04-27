<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesResource\Pages;
use App\Models\Sale as ModelsSale;
use App\Models\sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class SalesResource extends Resource
{
    protected static ?string $model = sale::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationGroup = 'Sales Management';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Your form schema
            ]);
    }
    
    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id')->label('Sale ID'),
                TextColumn::make('cashRegister.id')->label('Cash Register'),
                TextColumn::make('cashier_name')
                    ->label('Sold By')
                    ->getStateUsing(function ($record) {
                        return optional($record->cashierAtTimeOfSale())->name ?? '—';
                    }),
                TextColumn::make('created_at'),
            ])
            ->defaultSort("created_at", "desc")
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Action::make('viewProducts')
                    ->label('View Products')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => SalesResource::getUrl('view-products', ['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSales::route('/create'),
            'edit' => Pages\EditSales::route('/{record}/edit'),
            'view-products' => Pages\ViewSaleProducts::route('/{record}'),
        ];
    }
}