<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_products(): void
    {
        $this->getJson('/api/products')->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_a_product(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $category = Category::factory()->create();
        $suppliers = Supplier::factory()->count(2)->create();

        $payload = [
            'name' => 'Wireless Mouse',
            'sku' => 'wm-100',
            'description' => 'Ergonomic wireless mouse',
            'price' => 29.99,
            'stock' => 50,
            'category_id' => $category->id,
            'supplier_ids' => $suppliers->pluck('id')->all(),
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Wireless Mouse')
            ->assertJsonPath('data.sku', 'WM-100')
            ->assertJsonPath('data.formatted_price', '29.99')
            ->assertJsonPath('data.category.id', $category->id);

        $this->assertDatabaseHas('products', [
            'sku' => 'WM-100',
            'category_id' => $category->id,
        ]);

        $product = Product::where('sku', 'WM-100')->first();
        $this->assertCount(2, $product->suppliers);
    }

    public function test_creating_a_product_requires_valid_data(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/products', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'sku', 'price', 'stock', 'category_id']);
    }

    public function test_products_can_be_filtered_by_category_and_price(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $electronics = Category::factory()->create();
        $furniture = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $electronics->id,
            'price' => 50,
            'sku' => 'ELEC-50',
        ]);

        Product::factory()->create([
            'category_id' => $electronics->id,
            'price' => 150,
            'sku' => 'ELEC-150',
        ]);

        Product::factory()->create([
            'category_id' => $furniture->id,
            'price' => 80,
            'sku' => 'FURN-80',
        ]);

        $response = $this->getJson("/api/products?category_id={$electronics->id}&min_price=40&max_price=100");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.sku', 'ELEC-50');
    }

    public function test_soft_deleted_product_is_not_accessible(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $product = Product::factory()->create();

        $this->deleteJson("/api/products/{$product->id}")->assertNoContent();

        $this->assertSoftDeleted('products', ['id' => $product->id]);

        $this->getJson("/api/products/{$product->id}")->assertNotFound();
    }
}
