<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'PIMS API',
    description: 'Product Inventory Management System REST API built with Laravel 11 and Sanctum.'
)]
#[OA\Server(url: '/api', description: 'API base path')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum Token',
    description: 'Use the token returned by /login or /register'
)]
class OpenApiSpec
{
}
