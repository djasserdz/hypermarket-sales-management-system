<?php

namespace Database\Factories;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categorie>
 */
class CategorieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Fresh Produce', 'Dairy & Eggs', 'Bakery & Bread', 'Meat & Seafood', 'Pantry Staples',
            'Snacks & Confectionery', 'Beverages', 'Frozen Foods', 'Household Supplies', 'Personal Care',
            'Baby Care', 'Pet Supplies', 'Electronics & Appliances', 'Home & Kitchen', 'Clothing & Apparel',
            'Health & Wellness', 'Cleaning Supplies', 'Office & School Supplies', 'Toys & Games', 'Seasonal Items'
        ];

        return [
            'name' => fake()->unique()->randomElement($categories) // Use unique() with randomElement from the list
        ];
    }

    /**
     * Get a specific category name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }
}
