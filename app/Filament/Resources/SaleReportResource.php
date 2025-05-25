<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleReportResource\Pages;
use App\Models\SaleReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;

class SaleReportResource extends Resource
{
    protected static ?string $model = SaleReport::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    
    protected static ?string $navigationLabel = 'Sales Reports';
    
    protected static ?string $modelLabel = 'Sales Report';
    
    protected static ?string $pluralModelLabel = 'Sales Reports';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('file_path')
                    ->required()
                    ->disabled(),
                Forms\Components\DatePicker::make('report_date')
                    ->required()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('report_date')
                    ->label('Report Date')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('report_type')
                    ->label('Report Type')
                    ->getStateUsing(function (SaleReport $record): string {
                        if (str_contains($record->file_path, 'general')) {
                            return 'General (Admin)';
                        }
                        return 'Individual Supermarket';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'General (Admin)' => 'success',
                        'Individual Supermarket' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Revenue')
                    ->getStateUsing(function (SaleReport $record): string {
                        $reportContent = self::getReportContent($record);
                        if ($reportContent && isset($reportContent['total_money'])) {
                            return '$' . number_format($reportContent['total_money'], 2);
                        }
                        return 'N/A';
                    })
                    ->sortable(false),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Generated At')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('report_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($query, $date) => $query->whereDate('report_date', '>=', $date))
                            ->when($data['until'], fn ($query, $date) => $query->whereDate('report_date', '<=', $date));
                    }),
                Tables\Filters\SelectFilter::make('report_type')
                    ->options([
                        'general' => 'General (Admin)',
                        'individual' => 'Individual Supermarket',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'general') {
                            return $query->where('file_path', 'like', '%general%');
                        } elseif ($data['value'] === 'individual') {
                            return $query->where('file_path', 'not like', '%general%');
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('download_json')
                    ->label('Download JSON')
                    ->icon('heroicon-o-code-bracket-square')
                    ->action(function (SaleReport $record) {
                        try {
                            if (Storage::disk('public')->exists($record->file_path)) {
                                $fileName = basename($record->file_path);
                                return response()->streamDownload(function () use ($record) {
                                    echo Storage::disk('public')->get($record->file_path);
                                }, $fileName, [
                                    'Content-Type' => 'application/json',
                                ]);
                            }
                            Notification::make()
                                ->title('Error')
                                ->body('Report JSON file not found.')
                                ->danger()
                                ->send();
                            return null;
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to download JSON: ' . $e->getMessage())
                                ->danger()
                                ->send();
                            return null;
                        }
                    })
                    ->color('gray'),
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('Download PDF')
                    ->action(function (SaleReport $record) {
                        try {
                            $reportContent = self::getReportContent($record);
                            
                            if (!$reportContent) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Report file not found or corrupted.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $reportType = str_contains($record->file_path, 'general') ? 'General (Admin)' : 'Individual Supermarket';
                            
                            $pdf = Pdf::loadView('reports.sale-report-pdf', [
                                'report' => $record,
                                'content' => $reportContent,
                                'reportType' => $reportType,
                                'reportDate' => is_string($record->report_date) ? $record->report_date : $record->report_date->format('Y-m-d')
                            ]);

                            $filename = 'sales-report-' . (is_string($record->report_date) ? $record->report_date : $record->report_date->format('Y-m-d')) . '-' . time() . '.pdf';
                            
                            return response()->streamDownload(function() use ($pdf) {
                                echo $pdf->output();
                            }, $filename, [
                                'Content-Type' => 'application/pdf',
                            ]);
                            
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to generate PDF: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('report_date', 'desc');
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
            'view' => Pages\ViewSaleReport::route('/{record}'),
        ];
    }

    /**
     * Get the report content from JSON file
     */
    public static function getReportContent(SaleReport $record): ?array
    {
        try {
            if (Storage::disk('public')->exists($record->file_path)) {
                $content = Storage::disk('public')->get($record->file_path);
                return json_decode($content, true);
            }
        } catch (\Exception $e) {
            //\Log::error('Error reading report file: ' . $e->getMessage());
        }
        
        return null;
    }
}