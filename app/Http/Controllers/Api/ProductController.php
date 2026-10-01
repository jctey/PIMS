<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
    ) {}

    public function index(Request $request): ProductCollection
    {
        $products = $this->products->list([
            'category_id' => $request->integer('category_id') ?: null,
            'min_price' => $request->filled('min_price') ? (float) $request->input('min_price') : null,
            'max_price' => $request->filled('max_price') ? (float) $request->input('max_price') : null,
            'stock' => $request->input('stock'),
            'per_page' => $request->integer('per_page', 15),
        ]);

        return new ProductCollection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->products->create($request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($this->products->show($product));
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product = $this->products->update($product, $request->validated());

        return new ProductResource($product);
    }

    public function destroy(Product $product): Response
    {
        $this->products->delete($product);

        return response()->noContent();
    }
}
