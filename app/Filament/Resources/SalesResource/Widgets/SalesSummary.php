<?php

namespace App\Filament\Widgets;

use App\Models\sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class SalesSummary extends BaseWidget
{
    protected function getCards(): array
    {

        $todaySales = sale::whereDate('created_at', now())->with('products')->get()->sum(function ($sale) {
            return $sale->products->sum(fn($product) => $product->pivot->quantity * $product->price);
        });

        $totalTransactions = sale::whereDate('created_at', now())->count();

        return [
            Card::make('Total Sales Today', '$' . number_format($todaySales, 2))
                ->color('success')
                ->chart([10, 20, 30, 50, 70, 100]),

            Card::make('Total Transactions', $totalTransactions)
                ->color('primary')
                ->chart([2, 5, 8, 12, 20]),
        ];
    }
}
