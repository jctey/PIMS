<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class ProductPaths
{
    #[OA\Get(
        path: '/products',
        summary: 'List products with filters and pagination',
        security: [['bearerAuth' => []]],
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'min_price', in: 'query', required: false, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'max_price', in: 'query', required: false, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(
                name: 'stock',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['in_stock', 'low', 'out'])
            ),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated product list'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 429, description: 'Too many requests'),
        ]
    )]
    public function index(): void {}

    #[OA\Post(
        path: '/products',
        summary: 'Create a product',
        security: [['bearerAuth' => []]],
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductStoreRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product created'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(): void {}

    #[OA\Get(
        path: '/products/{id}',
        summary: 'Get a product by ID',
        security: [['bearerAuth' => []]],
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product details'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(): void {}

    #[OA\Put(
        path: '/products/{id}',
        summary: 'Update a product',
        security: [['bearerAuth' => []]],
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductUpdateRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Product updated'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/products/{id}',
        summary: 'Soft-delete a product',
        security: [['bearerAuth' => []]],
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Product deleted'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(): void {}
}
