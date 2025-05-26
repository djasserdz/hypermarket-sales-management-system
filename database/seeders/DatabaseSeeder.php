<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Categorie;
use App\Models\Supplier;
use App\Models\Supermarket;
use App\Models\CashRegister;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a specific admin user
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Use Hash::make for password
        ]);

        User::factory()->cashier()->create([
            'name' => 'dj',
            'email' => 'dj@gmail.com',
            'password' => Hash::make('dj'), // Use Hash::make for password
        ]);
        User::factory()->manager()
            ->create([
                'name'=>'hichem',
                'email'=>'hichem@gmail.com',
                'password' => Hash::make('hichem'),
            ]);

        // Create additional admin users
        User::factory()->count(2)->admin()->create();

        // Create cashier users
        User::factory()->count(10)->cashier()->create();

        // Create manager users
        User::factory()->count(3)->manager()->create();
        // Create predefined categories
        $definedCategories = [
            'Fresh Produce', 'Dairy & Eggs', 'Bakery & Bread', 'Meat & Seafood', 'Pantry Staples',
            'Snacks & Confectionery', 'Beverages', 'Frozen Foods', 'Household Supplies', 'Personal Care',
            'Baby Care', 'Pet Supplies', 'Electronics & Appliances', 'Home & Kitchen', 'Clothing & Apparel',
            'Health & Wellness', 'Cleaning Supplies', 'Office & School Supplies', 'Toys & Games', 'Seasonal Items'
        ];

        $categories = [];
        foreach ($definedCategories as $categoryName) {
            $categories[] = Categorie::firstOrCreate(['name' => $categoryName]);
        }

        // Create suppliers
        Supplier::factory()->count(8)->create();

        // Create supermarkets (managers are created by SupermarketFactory)
        $supermarkets = Supermarket::factory()->count(4)->create();

        // Create cash registers for each supermarket
        $supermarkets->each(function ($supermarket_item) {
            CashRegister::factory()->count(3)->create([
                'supermarket_id' => $supermarket_item->id,
            ]);
        });

        // Create products (Ensure products are created before stock)
        $products = Product::factory()->count(100)->create();

        // Create stock for each supermarket
        $supermarkets->each(function (Supermarket $supermarket) use ($products) {
            // For each supermarket, stock a random selection of products (e.g., 30 to 70 products)
            $productsToStock = $products->random(fake()->numberBetween(30, min(70, $products->count())));

            foreach ($productsToStock as $product) {
                // Ensure a product is not stocked twice in the same supermarket by this seeder run
                // This check is basic. For absolute certainty across multiple seeder runs without fresh migration,
                // you might use firstOrCreate on Stock model with supermarket_id and product_id.
                $existingStock = Stock::where('supermarket_id', $supermarket->id)
                                    ->where('product_id', $product->id)
                                    ->exists();
                if (!$existingStock) {
                    Stock::factory()->create([
                        'supermarket_id' => $supermarket->id,
                        'product_id' => $product->id,
                    ]);
                }
            }
        });

        // Optional: Output a success message to the console
        // $this->command->info('Database seeded successfully!');
    }
}
