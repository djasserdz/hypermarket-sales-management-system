<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleReportResource\Pages;
use App\Filament\Resources\SaleReportResource\RelationManagers;
use App\Models\SaleReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class SaleReportResource extends Resource
{
    protected static ?string $model = SaleReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Sales Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('report_date')->label('Report Date'),
            Tables\Columns\TextColumn::make('file_path')
                ->label('Download')
                ->url(fn ($record) => Storage::disk('public')->url($record->file_path))
                ->openUrlInNewTab()
                ->copyable(),
            Tables\Columns\TextColumn::make('created_at')->since(),
        ])
        ->defaultSort("created_at","desc")
        ->filters([])
        ->actions([
            Tables\Actions\Action::make('View')
                ->label('View Report')
                ->url(fn (SaleReport $record) => SaleReportResource::getUrl('view', ['record' => $record]))
        ])
        ->bulkActions([]);
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
        'index' => Pages\ListSaleReports::route('/'),
        'view' => Pages\ViewSaleReport::route('/{record}/view'),
    ];
}

}
