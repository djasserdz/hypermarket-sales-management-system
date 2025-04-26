<?php

namespace App\Filament\Widgets;

use App\Models\sale;
use Filament\Widgets\LineChartWidget;

class SalesLineChart extends LineChartWidget
{
    protected static ?string $heading = 'Sales Amount Over Last 7 Days';

    protected function getData(): array
    {
        $sales = sale::where('created_at', '>=', now()->subDays(6))
            ->with('products')
            ->get()
            ->groupBy(function ($sale) {
                return $sale->created_at->format('Y-m-d');
            })
            ->map(function ($sales) {
                return $sales->sum(function ($sale) {
                    return $sale->products->sum(fn($product) => $product->pivot->quantity * $product->price);
                });
            });

        $data = [];
        foreach (range(6, 0) as $daysAgo) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            $data[] = $sales[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales ($)',
                    'data' => $data,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                ],
            ],
            'labels' => collect(range(6, 0))
                ->map(fn($daysAgo) => now()->subDays($daysAgo)->format('D')) // Mon, Tue, etc.
                ->toArray(),
        ];
    }
}
