<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupermarketResource\Pages;
use App\Filament\Resources\SupermarketResource\RelationManagers;
use App\Models\Supermarket;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
    protected static ?string $model = Supermarket::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Supermarkets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Supermarket Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(20),
                        Select::make('manager_id')
                        ->label('manager')
                        ->options(
                            User::where('role','manager')->pluck('name','id')
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                    ]),
                Section::make('Location Details')
                    ->schema([
                        TextInput::make('location.street_name')
                            ->required()
                            ->maxLength(30)
                            ->label('Street Name'),

                        TextInput::make('location.state')
                            ->required()
                            ->maxLength(20)
                            ->label('State'),

                        TextInput::make('location.latitude')
                            ->disabled()
                            ->dehydrated()
                            ->label('Latitude'),

                        TextInput::make('location.longitude')
                            ->disabled()
                            ->dehydrated()
                            ->label('Longitude'),
                    ]),
            ]);
    }

    private static function fetchcordinates() {}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('manager.name')
                ->label('Manager'),
                TextColumn::make('location.street_name')
                    ->label('Street Name'),
                TextColumn::make('location.state')
                    ->label('State'),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->defaultSort("created_at","desc")
            ->filters([
                SelectFilter::make('state')->relationship('location', 'state'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
