<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $categories = Category::factory()->count(5)->create();
        $suppliers = Supplier::factory()->count(5)->create();

        Product::factory()
            ->count(20)
            ->recycle($categories)
            ->create()
            ->each(function (Product $product) use ($suppliers) {
                $product->suppliers()->attach(
                    $suppliers->random(fake()->numberBetween(1, 3))->pluck('id')
                );
            });
    }
}
