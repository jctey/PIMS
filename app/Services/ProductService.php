<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductService
{
    private const SHOW_CACHE_TTL_SECONDS = 60;

    /**
     * @param  array{category_id?: int|null, min_price?: float|null, max_price?: float|null, stock?: string|null, per_page?: int}  $filters
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        return Product::query()
            ->with(['category', 'suppliers'])
            ->byCategory($filters['category_id'] ?? null)
            ->priceBetween($filters['min_price'] ?? null, $filters['max_price'] ?? null)
            ->stockLevel($filters['stock'] ?? null)
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function show(Product $product): Product
    {
        return Cache::remember(
            $this->cacheKey($product->id),
            self::SHOW_CACHE_TTL_SECONDS,
            fn () => $product->load(['category', 'suppliers']),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create(collect($data)->except('supplier_ids')->all());

            if (! empty($data['supplier_ids'])) {
                $product->suppliers()->sync($data['supplier_ids']);
            }

            return $product->load(['category', 'suppliers']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $updated = DB::transaction(function () use ($product, $data) {
            $product->update(collect($data)->except('supplier_ids')->all());

            if (array_key_exists('supplier_ids', $data)) {
                $product->suppliers()->sync($data['supplier_ids'] ?? []);
            }

            return $product->load(['category', 'suppliers']);
        });

        $this->forgetCache($product->id);

        return $updated;
    }

    public function delete(Product $product): void
    {
        $productId = $product->id;
        $product->delete();
        $this->forgetCache($productId);
    }

    private function cacheKey(int $productId): string
    {
        return "products.{$productId}";
    }

    private function forgetCache(int $productId): void
    {
        Cache::forget($this->cacheKey($productId));
    }
}
