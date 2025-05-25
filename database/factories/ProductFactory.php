<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Categorie;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // More plausible product names
        $productAdjective = fake()->randomElement([
            'Premium', 'Organic', 'Artisanal', 'Gourmet', 'Family Size', 'Value Pack', 'Everyday', 'Fresh', 'Frozen', 'Imported'
        ]);
        $productNoun = fake()->randomElement([
            'Selection', 'Choice', 'Delight', 'Essentials', 'Harvest', 'Pantry', 'Market', 'Special', 'Treats', 'Goods'
        ]);
        $productType = fake()->randomElement([
            'Biscuits', 'Sauce', 'Cheese', 'Yogurt', 'Juice', 'Coffee', 'Tea', 'Cereal', 'Pasta', 'Rice', 'Oil', 'Shampoo', 'Soap'
        ]);

        return [
            // Ensure overall name is unique by combining with a unique number if many products are generated.
            // Or, rely on database constraints if you are seeding specific products per category.
            'name' => fake()->unique()->company() . ' ' . $productAdjective . ' ' . $productType, 
            'barcode' => fake()->unique()->ean13(),
            'price' => fake()->randomFloat(2, 0.5, 200),
            // Assign a category_id from existing categories
            'category_id' => Categorie::inRandomOrder()->first()->id ?? Categorie::factory(),
            'supplier_id' => Supplier::factory(),
        ];
    }
}
