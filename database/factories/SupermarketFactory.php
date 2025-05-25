<?php

namespace Database\Factories;

use App\Models\Supermarket;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supermarket>
 */
class SupermarketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Supermarket',
            'manager_id' => User::factory()->manager(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Supermarket $supermarket) {
            Location::factory()->create([
                'supermarket_id' => $supermarket->id,
                // 'name' field in LocationFactory already generates a name,
                // or you can customize it here if needed, e.g., based on supermarket name.
                // 'name' => $supermarket->name . ' Location',
            ]);
        });
    }
}
