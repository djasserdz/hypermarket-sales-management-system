<?php

namespace Database\Factories;

use App\Models\sale;
use App\Models\cashRegister;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cash_register_id' => cashRegister::factory(),
            'payment_method' => fake()->randomElement(['cash', 'card']),
        ];
    }
}
