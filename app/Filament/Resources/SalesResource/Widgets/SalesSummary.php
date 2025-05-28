<?php

namespace App\Filament\Widgets;

use App\Models\sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\LineChartWidget;
use Filament\Widgets\BarChartWidget;

class SalesSummary extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getCards(): array
    {
        $today = now()->toDateString();

        $todaySales = sale::whereDate('created_at', $today)
            ->with('products')
            ->get()
            ->sum(function ($sale) {
                return $sale->products->sum(fn($product) => $product->pivot->quantity * $product->price);
            });

        $totalTransactions = sale::whereDate('created_at', $today)->count();

        $salesLast7Days = sale::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $salesChartLast7Days = [];
        foreach (range(6, 0) as $daysAgo) {
            $date = now()->subDays($daysAgo)->toDateString();
            $salesChartLast7Days[] = $salesLast7Days[$date] ?? 0;
        }

        $salesAmountLast7Days = sale::where('created_at', '>=', now()->subDays(6))
            ->with('products')
            ->get()
            ->groupBy(function ($sale) {
                return $sale->created_at->toDateString();
            })
            ->map(function ($sales) {
                return $sales->sum(function ($sale) {
                    return $sale->products->sum(fn($product) => $product->pivot->quantity * $product->price);
                });
            });

        $salesAmountChartLast7Days = [];
        foreach (range(6, 0) as $daysAgo) {
            $date = now()->subDays($daysAgo)->toDateString();
            $salesAmountChartLast7Days[] = (float) ($salesAmountLast7Days[$date] ?? 0);
        }

        return [
            Card::make('Total Sales Today', 'DZD' . number_format($todaySales, 2))
            ->color('success')
            ->chart($salesAmountChartLast7Days)
            ->description('In the last 7 days')
            ->descriptionIcon('heroicon-o-arrow-up') // Replaced with another icon
            ->extraAttributes(['class' => 'hover:shadow-lg transition duration-300']),
        

            Card::make('Total Transactions Today', $totalTransactions)
                ->color('primary')
                ->chart($salesChartLast7Days)
                ->description('Transactions in the last 7 days')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->extraAttributes(['class' => 'hover:shadow-lg transition duration-300']),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            SalesLineChart::make(),
            SalesBarChart::make(),
        ];
    }
}
