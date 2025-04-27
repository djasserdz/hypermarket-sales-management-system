<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\stock;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Laravel\Prompts\text;

class StockResource extends Resource
{
    protected static ?string $model = stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Stock Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Stock Informations')->description('Add the Product Supermarket and Quantity')->schema([
                    Select::make('supermarket_id')->relationship('supermarket', 'name')->searchable()->required()->preload(),
                    Select::make('product_id')->relationship('product', 'name')->searchable()->required()->preload(),
                    TextInput::make('quantity')->required()->numeric(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name'),
                TextColumn::make('supermarket.name'),
                TextColumn::make('quantity'),
            ])
            ->defaultSort("created_at","desc")
            ->filters([
                SelectFilter::make('product')->relationship('product', 'name'),
                SelectFilter::make('supermarket')->relationship('supermarket', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
