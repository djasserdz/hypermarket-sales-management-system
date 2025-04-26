<?php

namespace App\Filament\Widgets;

use App\Models\sale;
use Filament\Widgets\BarChartWidget;

class SalesBarChart extends BarChartWidget
{
    protected static ?string $heading = 'Transactions Over Last 7 Days';

    protected function getData(): array
    {
        $transactions = sale::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $data = [];
        foreach (range(6, 0) as $daysAgo) {
            $date = now()->subDays($daysAgo)->toDateString();
            $data[] = $transactions[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Transactions',
                    'data' => $data,
                    'backgroundColor' => '#3B82F6',
                ],
            ],
            'labels' => collect(range(6, 0))
                ->map(fn($daysAgo) => now()->subDays($daysAgo)->format('D')) // Mon, Tue, etc.
                ->toArray(),
        ];
    }
}
