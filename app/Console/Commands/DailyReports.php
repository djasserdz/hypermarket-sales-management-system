<?php

namespace App\Console\Commands;

use App\Jobs\SendDailyReportEmailJob;
use App\Mail\DailySalesAdminReport;
use App\Mail\DailySalesReport;
use App\Models\SaleReport;
use App\Models\User;
use App\Models\Supermarket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DailyReports extends Command
{
    protected $signature = 'report:daily-sales {--no-email}';
    protected $description = 'Generate and send daily sales reports for each supermarket and a general report for admins';

    public function handle()
    {
        $this->info("Generating daily sales reports for yesterday...");

        $yesterday = Carbon::today()->timezone(config('app.timezone'))->startOfDay();
        $yesterdayFileFormat = $yesterday->format('Y-m-d');

        $supermarkets = Supermarket::with('manager')->get();
        $allProductsData = collect();
        $generalReportData = collect();

        foreach ($supermarkets as $supermarket) {
            $salesData = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('cash_registers', 'sales.cash_register_id', '=', 'cash_registers.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.price',
                    DB::raw('SUM(sale_items.quantity) as total_quantity'),
                    DB::raw('SUM(products.price * sale_items.quantity) as total_price')
                )
                ->where('cash_registers.supermarket_id', $supermarket->id)
                ->whereDate('sale_items.created_at', $yesterday)
                ->groupBy('products.id', 'products.name', 'products.price')
                ->orderBy('products.name')
                ->get();

            $totalMoney = $salesData->sum('total_price');
            $totalQuantity = $salesData->sum('total_quantity');

            $managerReport = [
                'date' => $yesterdayFileFormat,
                'type' => 'manager',
                'supermarket' => $supermarket->name,
                'report' => $salesData,
                'total_money' => $totalMoney,
                'total_products_sold' => $totalQuantity,
            ];

            // Add to general report for admins
            $generalReportData->push([
                'supermarket' => $supermarket->name,
                'report' => $salesData,
                'total_money' => $totalMoney,
            ]);

            // Aggregate product data
            foreach ($salesData as $item) {
                $existing = $allProductsData->firstWhere('id', $item->id);
                if ($existing) {
                    $existing->total_quantity += $item->total_quantity;
                    $existing->total_price += $item->total_price;
                } else {
                    $allProductsData->push((object)[
                        'id' => $item->id,
                        'name' => $item->name,
                        'price' => $item->price,
                        'total_quantity' => $item->total_quantity,
                        'total_price' => $item->total_price,
                    ]);
                }
            }

            // Send email (if not disabled)
            if (!$this->option('no-email') && $supermarket->manager && $supermarket->manager->email) {
                SendDailyReportEmailJob::dispatch($supermarket->manager->email, new DailySalesReport($managerReport));
                $this->info("Queued manager report for: {$supermarket->manager->email}");
            }

            // Save manager report to JSON file
            $fileName = "daily-sales-{$supermarket->id}-{$yesterdayFileFormat}.json";
            $filePath = "Daily-report/{$yesterdayFileFormat}/{$fileName}";
            Storage::disk('public')->makeDirectory("Daily-report/{$yesterdayFileFormat}");

            $jsonData = json_encode($managerReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if ($jsonData === false) {
                $this->error("Failed to encode JSON for supermarket ID {$supermarket->id}");
                continue;
            }
            Storage::disk('public')->put($filePath, $jsonData);

            SaleReport::updateOrCreate(
                ['file_path' => $filePath, 'report_date' => $yesterday],
                ['file_url' => Storage::disk('public')->url($filePath)]
            );
        }

        // Prepare and send admin report
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            $adminReport = [
                'date' => $yesterdayFileFormat,
                'type' => 'admin',
                'all_products' => $allProductsData->sortBy('name')->values()->toArray(),
                'supermarkets_breakdown' => $generalReportData->toArray(),
                'total_money' => $generalReportData->sum('total_money'),
                'total_products_sold' => $allProductsData->sum('total_quantity'),
            ];

            foreach ($admins as $admin) {
                if (!$this->option('no-email') && $admin->email) {
                    SendDailyReportEmailJob::dispatch($admin->email, new DailySalesAdminReport($adminReport));
                    $this->info("Queued admin report for: {$admin->email}");
                }
            }

            // Save admin report JSON
            $generalFileName = "daily-sales-general-{$yesterdayFileFormat}.json";
            $generalFilePath = "Daily-report/{$yesterdayFileFormat}/{$generalFileName}";
            $adminJsonData = json_encode($adminReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if ($adminJsonData === false) {
                $this->error("Failed to encode admin report JSON.");
                return;
            }

            Storage::disk('public')->put($generalFilePath, $adminJsonData);

            SaleReport::updateOrCreate(
                ['file_path' => $generalFilePath, 'report_date' => $yesterday],
                ['file_url' => Storage::disk('public')->url($generalFilePath)]
            );
        }

        $this->info("Daily sales report generation for yesterday completed.");
    }
}
