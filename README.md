# Catalog Backend

A Laravel API backend for a catalog-based website with admin management, public catalog APIs, user authentication, service requests, media uploads, dashboards, and role-based admin permissions.

## Features

### Public APIs

- View published services
- View published portfolios
- View published projects
- Submit guest service requests

### User APIs

- User registration
- User login/logout
- User profile endpoint
- User dashboard
- User service request dashboard
- Authenticated service request creation

### Admin APIs

- Admin login/logout
- Admin dashboard
- Services CRUD
- Projects CRUD
- Project images CRUD
- Portfolios CRUD
- Settings CRUD
- Service request management
- Admin-only access protection
- Role-based permissions using Spatie Laravel Permission

### Media Uploads

The backend supports image uploads for:

- Service images
- Project cover images
- Project gallery images

Uploaded files are stored in:

```text
storage/app/public
```

Public image URLs are returned in API responses.

---

## Tech Stack

- Laravel
- PHP
- MySQL / MariaDB
- Laravel Sanctum
- Spatie Laravel Permission
- REST API architecture

---

## Authentication Structure

This project has two separate authentication flows:

```text
Admin authentication
User authentication
```

Admins use the `admins` table.

Users use the `users` table.

Admin and user tokens are separated with middleware:

```text
ensure.admin
ensure.user
```

This means:

```text
Admin token cannot access user routes.
User token cannot access admin routes.
```

---

## Role-Based Access Control

Admin permissions are managed with Spatie Laravel Permission.

Default admin roles:

```text
super_admin
admin
editor
viewer
```

Example permissions:

```text
service.view
service.create
service.update
service.delete

project.view
project.create
project.update
project.delete

setting.view
setting.create
setting.update
setting.delete
```

Normal users do not use RBAC.

---

## API Documentation

API endpoints are documented here:

```text
docs/API.md
```

Setup instructions are available here:

```text
docs/SETUP.md
```

---

## Local Setup

Install dependencies:

```bash
composer install
```

Copy environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Run migrations:

```bash
php artisan migrate
```

Create storage link:

```bash
php artisan storage:link
```

Seed the database:

```bash
php artisan db:seed
```

Start local server:

```bash
php artisan serve
```

Local API base URL:

```text
http://127.0.0.1:8000/api
```

---

## Health Check

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

---

## Testing

Run tests:

```bash
php artisan test
```

---

## Git Branches

```text
main     → stable branch
develop  → active development branch
```

Current MVP work is developed on:

```text
develop
```

---

## Project Status

Backend MVP includes:

```text
Admin auth
User auth
Admin/User security separation
Admin RBAC
Catalog CRUD APIs
Public APIs
Service request workflow
Media uploads
Admin dashboard
User dashboard
API documentation
Setup guide
```

---

## License

This project is built with Laravel.