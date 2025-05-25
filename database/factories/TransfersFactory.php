<?php

namespace Database\Factories;

use App\Models\Transfers;
use App\Models\product;
use App\Models\supermarket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transfers>
 */
class TransfersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fromSupermarket = supermarket::factory()->create();
        $toSupermarket = supermarket::factory()->create();

        // Ensure from and to are different
        while ($toSupermarket->id === $fromSupermarket->id) {
            $toSupermarket = supermarket::factory()->create();
        }

        return [
            'product_id' => product::factory(),
            'from_supermarket' => $fromSupermarket->id,
            'to_supermarket' => $toSupermarket->id,
            'quantity' => fake()->numberBetween(1, 50),
            'status' => fake()->randomElement(['pending', 'in_transit', 'delivered']),
        ];
    }
} 