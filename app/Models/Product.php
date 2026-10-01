<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    public function scopePriceBetween(Builder $query, ?float $min, ?float $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('price', '>=', $min))
            ->when($max !== null, fn (Builder $q) => $q->where('price', '<=', $max));
    }

    public function scopeByCategory(Builder $query, ?int $categoryId): Builder
    {
        return $query->when(
            $categoryId !== null,
            fn (Builder $q) => $q->where('category_id', $categoryId)
        );
    }

    public function scopeStockLevel(Builder $query, ?string $level): Builder
    {
        return $query->when($level === 'in_stock', fn (Builder $q) => $q->where('stock', '>', 10))
            ->when($level === 'low', fn (Builder $q) => $q->whereBetween('stock', [1, 10]))
            ->when($level === 'out', fn (Builder $q) => $q->where('stock', 0));
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format((float) $this->price, 2, '.', ''),
        );
    }

    protected function sku(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtoupper($value),
        );
    }
}
