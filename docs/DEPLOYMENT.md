# Deployment Checklist

This checklist is used before deploying the Catalog Backend to production.

## 1. Server Requirements

- PHP 8.4 or compatible version
- Composer
- MySQL or MariaDB
- Web server: Nginx or Apache
- Git
- Required PHP extensions:
  - mbstring
  - dom
  - fileinfo
  - mysql
  - sqlite
  - openssl
  - tokenizer
  - xml
  - ctype
  - json

## 2. Clone Repository

```bash
git clone https://github.com/arounatech/catalog-backend.git
cd catalog-backend
```

For development or staging:

```bash
git checkout develop
```

For final production release:

```bash
git checkout main
```

## 3. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

## 4. Environment File

Copy the example environment file:

```bash
cp .env.example .env
```

Update production values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_database_password

FILESYSTEM_DISK=public
```

Set a strong initial admin password:

```env
INITIAL_ADMIN_NAME="Admin User"
INITIAL_ADMIN_EMAIL="admin@example.com"
INITIAL_ADMIN_PASSWORD="use-a-strong-password"
```

Never commit the real `.env` file.

## 5. Generate App Key

```bash
php artisan key:generate
```

## 6. Run Migrations

```bash
php artisan migrate --force
```

## 7. Seed Initial Admin and Permissions

```bash
php artisan db:seed --force
```

## 8. Create Storage Link

```bash
php artisan storage:link
```

## 9. Optimize Laravel

```bash
php artisan optimize
```

## 10. Folder Permissions

Make sure these folders are writable by the web server:

```text
storage
bootstrap/cache
```

## 11. Final Checks Before Deployment

Run before deployment:

```bash
php artisan test
vendor/bin/pint --test
php artisan route:list --path=api
```

## 12. Security Checklist

- `APP_DEBUG=false`
- Real `.env` is not committed
- Strong database password
- Strong initial admin password
- HTTPS enabled
- Server firewall configured
- Storage folder permissions configured
- Public document root points to Laravel `/public`
- Server blocks direct access to sensitive files
- Backups are configured for database and uploaded media

## 13. Production Cache Commands

After deployment, clear and rebuild cache:

```bash
php artisan optimize:clear
php artisan optimize
```

## 14. API Health Check

Check the API health endpoint:

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

## 15. Important Production Notes

- Do not run development commands on production unless needed.
- Do not commit `.env`.
- Do not commit uploaded files from `storage/app/public`.
- Do not commit `vendor`.
- Use `main` branch only after final review.
- Keep `develop` as the active development branch.
