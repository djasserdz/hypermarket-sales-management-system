<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupermarketResource\Pages;
use App\Filament\Resources\SupermarketResource\RelationManagers;
use App\Models\supermarket;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupermarketResource extends Resource
{
    protected static ?string $model = supermarket::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Supermarkets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Supermarket Details')->description('Add SuperMarket Details')->schema([
                    TextInput::make("name")->required(),
                    TextInput::make('street_name')->required(),
                    TextInput::make('state')->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('location.street_name'),
                TextColumn::make('location.state'),
            ])
            ->filters([
                SelectFilter::make('state')->relationship('location', 'state'),
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
            'index' => Pages\ListSupermarkets::route('/'),
            'create' => Pages\CreateSupermarket::route('/create'),
            'edit' => Pages\EditSupermarket::route('/{record}/edit'),
        ];
    }
}
