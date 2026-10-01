<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

class AuthPaths
{
    #[OA\Post(
        path: '/register',
        summary: 'Register a new user',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RegisterRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Registered successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 429, description: 'Too many requests'),
        ]
    )]
    public function register(): void {}

    #[OA\Post(
        path: '/login',
        summary: 'Login and receive an API token',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login successful'),
            new OA\Response(response: 422, description: 'Invalid credentials'),
            new OA\Response(response: 429, description: 'Too many requests'),
        ]
    )]
    public function login(): void {}

    #[OA\Post(
        path: '/logout',
        summary: 'Revoke the current access token',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Logged out successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(): void {}
}
