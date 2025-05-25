<?php

namespace Database\Factories;

use App\Models\Stock;
use App\Models\Supermarket;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supermarket_id' => Supermarket::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(50, 500),
        ];
    }
} 