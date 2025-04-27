<?php

namespace App\Filament\Resources\SaleReportResource\Pages;

use App\Filament\Resources\SaleReportResource;
use Filament\Resources\Pages\Page;
use App\Models\SaleReport;
use Illuminate\Support\Facades\Storage;

class ViewSaleReport extends Page
{
    protected static string $resource = SaleReportResource::class;

    public SaleReport $record;
    public array $reportContent = [];

    public function mount(SaleReport $record): void
    {
        $this->record = $record;



        $content = Storage::disk("public")->get($record->file_path);

        $this->reportContent = json_decode($content, true) ?? [];
    }

    protected static string $view = 'filament.resources.sale-report-resource.pages.view-sale-report';
}
