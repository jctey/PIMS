<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductStoreRequest',
    required: ['name', 'sku', 'price', 'stock', 'category_id'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Wireless Mouse'),
        new OA\Property(property: 'sku', type: 'string', example: 'wm-100'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
        new OA\Property(property: 'stock', type: 'integer', example: 50),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(
            property: 'supplier_ids',
            type: 'array',
            items: new OA\Items(type: 'integer'),
            example: [1, 2]
        ),
    ]
)]
#[OA\Schema(
    schema: 'ProductUpdateRequest',
    properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'sku', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'price', type: 'number', format: 'float'),
        new OA\Property(property: 'stock', type: 'integer'),
        new OA\Property(property: 'category_id', type: 'integer'),
        new OA\Property(
            property: 'supplier_ids',
            type: 'array',
            items: new OA\Items(type: 'integer')
        ),
    ]
)]
#[OA\Schema(
    schema: 'LoginRequest',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
    ]
)]
#[OA\Schema(
    schema: 'RegisterRequest',
    required: ['name', 'email', 'password', 'password_confirmation'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Jane Doe'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password'),
    ]
)]
class Schemas
{
}
