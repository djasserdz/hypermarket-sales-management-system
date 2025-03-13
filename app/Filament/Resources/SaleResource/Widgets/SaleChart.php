<?php

use Filament\Widgets\ChartWidget;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SaleChart extends ChartWidget
{
    protected static ?string $heading = 'Sales Over Time';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {

        $salesData = Sale::selectRaw('DATE(sales.created_at) as date, SUM(sale_items.quantity * products.price) as total_sales')
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();


        return [
            'datasets' => [
                [
                    'label' => 'Total Sales',
                    'data' => $salesData->pluck('total_sales'),
                    'borderColor' => '#4F46E5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.5)',
                ],
            ],
            'labels' => $salesData->pluck('date'),
        ];
    }
}
