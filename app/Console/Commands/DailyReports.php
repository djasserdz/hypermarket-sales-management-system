<?php

namespace App\Console\Commands;

use App\Mail\DailySalesAdminReport;
use App\Mail\DailySalesReport;
use App\Models\SaleReport;
use App\Models\User;
use App\Models\supermarket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class DailyReports extends Command
{
    protected $signature = 'app:daily-reports';
    protected $description = 'Generate and send daily sales reports for each supermarket and a general report for admins';

    public function handle()
    {
        $this->info("Generating daily sales reports...");

        $today = Carbon::today();
        $todayFile = $today->format('Y-m-d');

        $supermarkets = supermarket::with('manager')->get();
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
                ->whereDate('sale_items.created_at', $today)
                ->groupBy('products.id', 'products.name', 'products.price')
                ->orderBy('products.name')
                ->get();

            $totalMoney = $salesData->sum('total_price');
            $totalQuantity = $salesData->sum('total_quantity');

            $managerReport = [
                'date' => $todayFile,
                'type' => 'manager',
                'supermarket' => $supermarket->name,
                'products' => $salesData,
                'total_money' => $totalMoney,
                'total_products_sold' => $totalQuantity,
            ];

            

            // Add to general report for admins
            $generalReportData->push([
                'supermarket' => $supermarket->name,
                'report' => $salesData,
                'total_money' => $totalMoney,
            ]);
            

            // Collect all product data for admin report
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

            // Send report to manager
            if ($supermarket->manager && $supermarket->manager->email) {
                Mail::to($supermarket->manager->email)->send(new DailySalesReport($managerReport));
                $this->info("Sent manager report to: {$supermarket->manager->email}");
            }

            // Save manager report JSON file
            $fileName = "daily-sales-{$supermarket->id}-{$todayFile}.json";
            $filePath = "Daily-report/{$todayFile}/{$fileName}";
            Storage::disk('public')->makeDirectory("Daily-report/{$todayFile}");
            Storage::disk('public')->put($filePath, json_encode($managerReport, JSON_PRETTY_PRINT));

            SaleReport::create([
                'file_path' => $filePath,
                'report_date' => $today,
            ]);
        }

       

        // Prepare admin report
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            $adminReport = [
                'date' => $todayFile,
                'type' => 'admin',
                'all_products' => $allProductsData->sortBy('name')->values()->toArray(),
                'supermarkets_breakdown' => $generalReportData->toArray(),
                'total_money' => $generalReportData->sum('total_money'),
                'total_products_sold' => $allProductsData->sum('total_quantity'),
            ];

            foreach ($admins as $admin) {
                if ($admin->email) {
                    Mail::to($admin->email)->send(new DailySalesAdminReport($adminReport));
                    $this->info("Sent admin report to: {$admin->email}");
                }
            }

            // Save admin report JSON file
            $generalFileName = "daily-sales-general-{$todayFile}.json";
            $generalFilePath = "Daily-report/{$todayFile}/{$generalFileName}";
            Storage::disk('public')->put($generalFilePath, json_encode($adminReport, JSON_PRETTY_PRINT));

            SaleReport::create([
                'file_path' => $generalFilePath,
                'report_date' => $today,
            ]);
        }

        $this->info("Daily sales report generation completed.");
    }
}
