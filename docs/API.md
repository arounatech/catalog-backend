# Catalog Backend API Documentation

Base URL for local development:

```text
http://127.0.0.1:8000/api
```

---

## Health Check

Health check is a simple endpoint used to make sure the backend API is running.

### Ping API

```http
GET /ping
```

Full local URL:

```text
http://127.0.0.1:8000/api/ping
```

Example response:

```json
{
  "message": "API is working",
  "status": "success"
}
```

---

## Authentication Types

This backend has two separate authentication systems:

```text
Admin authentication
User authentication
```

Admins use:

```text
POST /admin/auth/login
GET  /admin/auth/me
POST /admin/auth/logout
```

Users use:

```text
POST /user/auth/register
POST /user/auth/login
GET  /user/auth/me
POST /user/auth/logout
```

---

## Public APIs

Public APIs do not need authentication.

```text
GET  /public/services
GET  /public/services/{id}

GET  /public/portfolios
GET  /public/portfolios/{id}

GET  /public/projects
GET  /public/projects/{id}

POST /public/service-requests
```

---

## User APIs

User APIs need a user Bearer token.

```text
GET  /user/dashboard
GET  /user/service-requests
POST /user/service-requests
```

---

## Admin APIs

Admin APIs need an admin Bearer token.

```text
GET /admin/dashboard

GET    /admin/services
POST   /admin/services
GET    /admin/services/{id}
PATCH  /admin/services/{id}
DELETE /admin/services/{id}

GET    /admin/projects
POST   /admin/projects
GET    /admin/projects/{id}
PATCH  /admin/projects/{id}
DELETE /admin/projects/{id}

GET    /admin/project-images
POST   /admin/project-images
GET    /admin/project-images/{id}
PATCH  /admin/project-images/{id}
DELETE /admin/project-images/{id}

GET    /admin/portfolios
POST   /admin/portfolios
GET    /admin/portfolios/{id}
PATCH  /admin/portfolios/{id}
DELETE /admin/portfolios/{id}

GET    /admin/settings
POST   /admin/settings
GET    /admin/settings/{id}
PATCH  /admin/settings/{id}
DELETE /admin/settings/{id}

GET    /admin/service-requests
GET    /admin/service-requests/{id}
PATCH  /admin/service-requests/{id}
DELETE /admin/service-requests/{id}
```

---

## Media Uploads

Uploaded images are stored in:

```text
storage/app/public
```

Public image URLs are returned like this:

```json
{
  "image": "services/example.png",
  "image_url": "http://localhost/storage/services/example.png"
}
```

---

## Security Rules

```text
Admin token cannot access user routes.
User token cannot access admin routes.
```