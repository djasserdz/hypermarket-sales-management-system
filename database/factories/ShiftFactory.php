<?php

namespace Database\Factories;

use App\Models\shift;
use App\Models\User;
use App\Models\cashRegister;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_at = fake()->dateTimeBetween('-1 month', 'now');
        $end_at = fake()->optional(0.8)->dateTimeBetween($start_at, Carbon::parse($start_at)->addHours(8));

        return [
            'user_id' => User::factory()->cashier(),
            'cash_register_id' => cashRegister::factory(),
            'start_at' => $start_at,
            'end_at' => $end_at,
        ];
    }
} 