<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Users Management';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make(fn($record) => $record ? 'Edit User' : 'Create User')
                ->schema([
                    TextInput::make('name')->required(),

                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->unique(ignoreRecord: true),

                    TextInput::make('password')
                        ->password()
                        ->nullable()
                        ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                        ->dehydrated(fn($state) => filled($state)),

                    Select::make('role')
                        ->options([
                            'admin' => 'admin',
                            'cashier' => 'cashier',
                            'manager' => 'manager',
                        ])
                        ->required()
                        ->default(fn($record) => $record ? $record->role : null),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('role')->badge(),
                TextColumn::make('created_at'),
            ])
            ->defaultSort("created_at","desc")
            ->filters([
                SelectFilter::make('role')->options([
                    'admin' => 'Admin',
                    'cashier' => 'Cashier',
                    'manager' => 'Manager'
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
