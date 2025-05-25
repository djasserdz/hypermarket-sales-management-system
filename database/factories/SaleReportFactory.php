<?php

namespace Database\Factories;

use App\Models\SaleReport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleReport>
 */
class SaleReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reportDate = fake()->date();
        return [
            'file_path' => 'reports/sales/sales_report_' . Str::slug($reportDate) . '_' . Str::random(8) . '.pdf',
            'report_date' => $reportDate,
        ];
    }
} 