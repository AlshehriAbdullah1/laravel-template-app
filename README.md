# Laravel 12 API Template

**Clean Architecture + Modular Monolith** starter for building large‑scale, API‑first backends. Ships with:

- Laravel **12** (routing/middleware configured in `bootstrap/app.php`)
- **Sanctum** token auth (mobile, server‑to‑server)
- **Spatie Permissions** (RBAC)
- **Spatie Query Builder** (filter/sort/include)
- **Predis/Redis** (cache/queue/session)
- **Telescope** (local/staging observability)
- JSON‑only responses, request correlation (`X‑Request‑Id`), optional **Idempotency** middleware

> For a deeper walkthrough, see the **Developer Guide** (add it to `/docs/Developer-Guide.md`).

---

## Table of contents

- [Architecture](#architecture)
- [Requirements](#requirements)
- [Quick start](#quick-start)
- [Environment](#environment)
- [Run & verify](#run--verify)
- [Auth quick demo](#auth-quick-demo)
- [Project structure](#project-structure)
- [Add a new feature](#add-a-new-feature)
- [Observability (Telescope)](#observability-telescope)
- [Queues & jobs](#queues--jobs)
- [Production checklist](#production-checklist)
- [Troubleshooting](#troubleshooting)

---

## Architecture

**Clean Architecture** with strict boundaries:

- **Domain** – Entities, Value Objects, Repository *interfaces* (business rules, pure PHP)
- **Application** – Use cases (commands/queries), DTOs, app‑level contracts
- **Infrastructure** – Eloquent models, repository implementations, HTTP/Redis/PG adapters
- **Interfaces** – HTTP layer: Controllers, Requests, Resources, Middleware, versioned routes

API is **versioned** at `/api/v1/...` and uses middleware grouping/throttling defined in `bootstrap/app.php`.

---

## Requirements

- PHP **8.2+** (tested with 8.4)
- Composer
- SQLite (dev) or PostgreSQL (prod) with `pdo_pgsql`
- Redis for cache/queue/session in prod (Predis client)

---

## Quick start

```bash
# 1) Install dependencies
composer install

# 2) Configure environment
cp .env.example .env
php artisan key:generate

# 3) Local database (SQLite)
# Windows PowerShell
ni database/database.sqlite -ItemType File
# macOS/Linux
# touch database/database.sqlite

# 4) Migrate (+ optional seed for roles/permissions)
php artisan migrate
php artisan db:seed --class=PermissionSeeder   # optional

# 5) Run the API
php artisan serve
```

---

## Environment

**Local (.env)**

```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

TELESCOPE_ENABLED=true
```

**Production (.env)**

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.example.com

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=app
DB_USERNAME=app
DB_PASSWORD=secret

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_CLIENT=predis
REDIS_HOST=redis
REDIS_PORT=6379

TELESCOPE_ENABLED=false
```

Switch DBs by updating `.env` and running `php artisan migrate` (ensure `pdo_pgsql` is enabled for PostgreSQL).

---

## Run & verify

- **Ping**: `GET /api/v1/ping` → `{ "pong": true }`
- **List routes**: `php artisan route:list`

Routing/middleware groups and exception rendering live in `` (Laravel 12 style).

---

## Auth quick demo

**Register**

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Alice","email":"alice@example.com","password":"secret123","password_confirmation":"secret123"}'
```

**Login**

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"alice@example.com","password":"secret123"}'
```

**Me**

```bash
curl http://127.0.0.1:8000/api/v1/auth/me \
  -H "Authorization: Bearer <TOKEN>"
```

Add RBAC with Spatie Permissions and protect routes with `->middleware('permission:...')`.

---

## Project structure

```
app/
├─ Application/                  # Use cases, DTOs, app contracts
├─ Domain/                       # Entities + repository interfaces
├─ Infrastructure/               # Eloquent models, repos, providers, security
├─ Interfaces/Http/              # Controllers, Requests, Resources, Middleware, routes_v1.php
├─ Models/                       # Thin aliases (optional for BC)
└─ Providers/                    # App & Telescope providers

bootstrap/app.php                # routing, middleware, JSON errors, rate limiting
routes/api.php                   # mounts app/Interfaces/Http/routes_v1.php under /api/v1
```

**Cross‑cutting middleware**

- `ForceJsonResponse` – forces JSON `Accept`
- `RequestId` – adds `X-Request-Id` (log correlation)
- `Idempotency` (optional) – cache first POST response by `Idempotency-Key`

---

## Add a new feature

1. **Domain** – Define entity + repository interface
2. **Application** – Create use case in `Application/<Feature>/UseCases` with `execute(...)`
3. **Infrastructure** – Eloquent model + repository implementation, bind interface → implementation in `InfrastructureServiceProvider`
4. **Interfaces** – Form Requests, Resource, Controller, and route(s) in `app/Interfaces/Http/routes_v1.php`

> See the Product example and full instructions in the Developer Guide.

---

## Observability (Telescope)

- Install once: `php artisan telescope:install && php artisan migrate`
- Access UI at `/telescope` (open by default in `local`), tighten auth for other envs in `App\Providers\TelescopeServiceProvider`
- Toggle watchers in `config/telescope.php`; keep disabled in production unless debugging

---

## Queues & jobs

- Dev: `QUEUE_CONNECTION=sync`
- Prod: `QUEUE_CONNECTION=redis` and run workers:
  ```bash
  php artisan queue:work --tries=3 --max-time=3600
  ```
- Consider **Horizon** (Linux/containers) for queue monitoring

---

## Production checklist

- `APP_DEBUG=false`, HTTPS, trusted proxies configured
- PostgreSQL, Redis (cache/queue/session), workers supervised
- CORS configured; if SPA cookie mode, set `SANCTUM_STATEFUL_DOMAINS` & `SESSION_DOMAIN`
- Read replicas & PgBouncer for scale; tune rate limits in `bootstrap/app.php`
- Telescope disabled or gated; log shipping with `request_id` correlation

---

## Troubleshooting

- **Route missing** → ensure `api:` mapping exists in `bootstrap/app.php`; run `php artisan route:list`
- **Class not found** → check PSR‑4 in `composer.json`, then `composer dump-autoload`
- **405 Method Not Allowed** → wrong HTTP verb; compare with `route:list`
- **Telescope migration collision** → remove duplicate migrations or reset SQLite DB

---

## License

MIT (or your choice). Update this section to match your project.

---

## Credits

Built on Laravel 12 with first‑class packages: Sanctum, Spatie Permissions, Spatie Query Builder, Predis, and Telescope.

