<?php

namespace App\Console\Commands;

use App\Mail\DailySalesReport;
use App\Models\sale;
use App\Models\SaleReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class dailysalesReports extends Command 
{
    protected $signature = 'report:daily-sales'; // Command name
    protected $description = 'Generate and send the daily sales report';

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

        $emailRecipients = ['grndjasser@gmail.com']; 
        Mail::to($emailRecipients)->send(new DailySalesReport($report));

        $this->info("Daily sales report sent via email");

        $today_file = now()->format('Y-m-d');
        $filename = 'daily-sales-' . $today_file . '.json';
        $filePath = "Daily-report/{$today_file}/" . $filename;
        
        Storage::disk('public')->makeDirectory("Daily-report/{$today_file}", 0755);
        
        Storage::disk('public')->put($filePath, json_encode($report, JSON_PRETTY_PRINT));
        
        SaleReport::create([
            'file_path' => $filePath,
            'report_date' => today(),
        ]);

        $this->info("Daily sales report saved to storage and database.");
    }
}
