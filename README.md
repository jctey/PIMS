# Product Inventory Management System (PIMS)

REST API for managing products, categories, and suppliers — built with **Laravel 11** and **Laravel Sanctum**.

## Features

- Models: `Product`, `Category`, `Supplier` with Eloquent relationships
- Full Product CRUD with filtering (category, price range, stock level) and pagination
- Authentication via Laravel Sanctum (API tokens)
- Form Request validation and API Resources
- Eloquent scopes, accessor/mutator, and soft deletes on products
- Service layer (`ProductService`, `AuthService`) — controllers stay thin
- Feature tests for auth and product endpoints
- Docker Compose setup (PHP-FPM, Nginx, MySQL)
- **Rate limiting** on auth and authenticated API routes
- **Caching** for product show responses
- **OpenAPI / Swagger** documentation (L5-Swagger)
- Simple **Vue 3** frontend to browse inventory data

## Tech Stack

| Layer | Choice |
|---|---|
| Framework | Laravel 11.x |
| Auth | Laravel Sanctum |
| Database | MySQL 8 (Docker) / SQLite in-memory (tests) |
| HTTP | Nginx + PHP 8.3 FPM |
| Tests | PHPUnit Feature tests |
| Frontend | Vue 3 + Vite + Vue Router |

## Requirements

- Docker & Docker Compose **or** PHP 8.2+, Composer, MySQL
- Node.js 18+ (for the Vue frontend)
- Git

## Quick Start (Docker — recommended)

```bash
# 1. Clone and enter the project
git clone <your-repo-url> PIMS
cd PIMS

# 2. Environment
cp .env.example .env

# 3. Build & start containers
docker compose up -d --build

# 4. Install dependencies & prepare app
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

API base URL: **http://localhost:8000/api**  
Swagger UI: **http://localhost:8000/api/documentation**

### Vue frontend (browse data)

**Option A — Docker**

```bash
docker compose up -d --build
```

Open **http://localhost:5173**

Frontend container proxies `/api` to the nginx service inside the compose network.

**Option B — Local Node**

With the API containers running:

```bash
cd frontend
npm install
npm run dev
```

Open **http://localhost:5173**  
Vite proxies `/api` to `http://localhost:8000` by default.

- Login with `test@example.com` / `password`
- View product list, filters, pagination, and product detail

### Seeded user

| Field | Value |
|---|---|
| Email | `test@example.com` |
| Password | `password` |

Seeder also creates sample categories, suppliers, and products.

## Local Setup (without Docker)

```bash
cp .env.example .env
# Edit DB_* to point at your MySQL instance
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Authentication

### Register

```http
POST /api/register
Content-Type: application/json

{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

### Login

```http
POST /api/login
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "password"
}
```

Response includes a `token`. Send it on protected routes:

```http
Authorization: Bearer {token}
```

### Logout

```http
POST /api/logout
Authorization: Bearer {token}
```

## Product API

All product routes require authentication.

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/products` | List products (filter + paginate) |
| `POST` | `/api/products` | Create product |
| `GET` | `/api/products/{id}` | Show product |
| `PUT/PATCH` | `/api/products/{id}` | Update product |
| `DELETE` | `/api/products/{id}` | Soft-delete product |

### Query filters (`GET /api/products`)

| Param | Example | Description |
|---|---|---|
| `category_id` | `1` | Filter by category |
| `min_price` | `10` | Minimum price |
| `max_price` | `100` | Maximum price |
| `stock` | `in_stock` / `low` / `out` | Stock level (`>10`, `1–10`, `0`) |
| `per_page` | `15` | Page size (default 15) |
| `page` | `2` | Page number |

Example:

```http
GET /api/products?category_id=1&min_price=10&max_price=100&stock=low&per_page=10
Authorization: Bearer {token}
```

### Create / update body

```json
{
  "name": "Wireless Mouse",
  "sku": "wm-100",
  "description": "Ergonomic wireless mouse",
  "price": 29.99,
  "stock": 50,
  "category_id": 1,
  "supplier_ids": [1, 2]
}
```

Notes:

- `sku` is stored uppercase via a mutator
- Responses include `formatted_price`
- Soft-deleted products are excluded from list/show

## Project Structure (key paths)

```
app/
  Http/
    Controllers/Api/     # Thin HTTP controllers
    Requests/            # Form Request validation
    Resources/           # API response formatting
  Models/                # Eloquent models, scopes, casts
  OpenApi/               # Swagger/OpenAPI specs (not in controllers)
    OpenApiSpec.php
    Paths/
    Schemas/
  Services/              # Business logic
database/
  migrations/
  factories/
  seeders/
tests/Feature/           # API feature tests
docker/nginx/            # Nginx config
compose.yaml             # Docker services
```

## Bonus Features

### Rate limiting

| Routes | Limit |
|---|---|
| `POST /api/login`, `POST /api/register` | 5 requests / minute |
| Authenticated API routes | 60 requests / minute |

Exceeding the limit returns HTTP `429 Too Many Requests`.

### Caching

- `GET /api/products/{id}` results are cached for **60 seconds** (`products.{id}`)
- Cache is invalidated on product update and soft-delete

### API documentation (Swagger)

Interactive docs are available at:

**http://localhost:8000/api/documentation**

OpenAPI annotations live under `app/OpenApi/` (not in controllers):

- `OpenApiSpec.php` — info, server, security scheme
- `Paths/` — endpoint definitions
- `Schemas/` — reusable request schemas

Regenerate after annotation changes:

```bash
docker compose exec app php artisan l5-swagger:generate
```

In Swagger UI, use **Authorize** and paste a Bearer token from `/login`.

## Running Tests

Tests use SQLite in-memory (`phpunit.xml`) so they do not touch the Docker MySQL data.

```bash
docker compose exec app php artisan test
```

Or filter:

```bash
docker compose exec app php artisan test --filter=ProductApiTest
```

## Useful Commands

```bash
docker compose up -d          # start
docker compose down           # stop
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan route:list --path=api
```

## License

MIT
