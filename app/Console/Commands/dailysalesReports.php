<?php

namespace App\Console\Commands;

use App\Models\sale;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class dailysalesReports extends Command
{
    protected $signature = 'report:daily-sales'; // Command name
    protected $description = 'Generate and save the daily sales report';

    public function handle()
    {
        $this->info("Daily sales report generated");
        $today = Carbon::today();

        $sale_report = DB::table('sale_items')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->join('products', 'sale_items.product_id', '=', 'products.id')
        ->select(
        'products.id',
        'products.name',
        DB::raw('SUM(sale_items.quantity) as total_quantity'),
        DB::raw('SUM(products.price * sale_items.quantity) as total_price')
    )
    ->whereDate('sale_items.created_at', $today)
    ->groupBy('products.id', 'products.name')
    ->orderBy('products.name')
    ->get();

$total_money_today = $sale_report->sum('total_price');

$report = [
    'report' => $sale_report,
    'total_money' => $total_money_today,
];

dump($report);


        

        /*$report = sale::with(['products', 'cashRegister'])
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
            dump($report);*/
    }
}
