<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Supermarket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'street_address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'supermarket_id' => Supermarket::factory(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }
} 