<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class GenerateDailySalesReport extends Command
{
    protected $signature = 'report:daily-sales'; // Command name
    protected $description = 'Generate and save the daily sales report';

    public function handle()
    {
        $this->info("Daily sales report generated");
        return;
        $today = Carbon::today();


        $report = Sale::with(['products', 'cashRegister'])
            ->whereDate('created_at', $today)
            ->get()
            ->groupBy(fn($sale) => $sale->cashRegister->supermarket_id ?? 'default')
            ->map(function ($sales, $supermarketId) {
                return [
                    'supermarket_id' => $supermarketId,
                    'total_sales' => $sales->count(),
                    'total_revenue' => $sales->sum('total_price'),
                    'products' => $sales->flatMap(function ($sale) {
                        return $sale->products->mapWithKeys(function ($product) {
                            return [$product->name => ($product->pivot->quantity ?? 0)];
                        });
                    })->toArray(),
                ];
            });
            dd($report);
    }
}
