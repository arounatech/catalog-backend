# Catalog Backend Setup Guide

## 1. Clone the project

```bash
git clone https://github.com/arounatech/catalog-backend.git
cd catalog-backend
```

## 2. Install PHP dependencies

```bash
composer install
```

## 3. Create environment file

```bash
cp .env.example .env
```

Then update database values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=catalog_backend
DB_USERNAME=root
DB_PASSWORD=
```

## 4. Generate application key

```bash
php artisan key:generate
```

## 5. Run migrations

```bash
php artisan migrate
```

## 6. Create storage link

```bash
php artisan storage:link
```

## 7. Create initial admin

Set these values in `.env`:

```env
INITIAL_ADMIN_NAME="Admin User"
INITIAL_ADMIN_EMAIL="admin@example.com"
INITIAL_ADMIN_PASSWORD="change-this-password"
```

Then run:

```bash
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=AdminRolePermissionSeeder
```

## 8. Run tests

```bash
php artisan test
```

## 9. Start local server

```bash
php artisan serve
```

Local API base URL:

```text
http://127.0.0.1:8000/api
```

## Main Test Endpoint

```http
GET /api/ping
```

Expected response:

```json
{
  "message": "API is working",
  "status": "success"
}
```