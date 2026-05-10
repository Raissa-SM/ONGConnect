# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**ONGConnect** is a Laravel 12 REST API that matches volunteers with NGOs (ONGs) in the Alto Vale do Itajaí region. It is a semester extension project for Unidavi 2026. The API is consumed by a frontend client (not in this repo).

All work lives in the `ONGConnect/` subdirectory — run all artisan and composer commands from there.

## Commands

All commands must be run from the `ONGConnect/` directory.

```bash
# First-time setup
composer run setup          # install, .env, key:generate, migrate, npm install+build

# Development (starts server, queue, log tail, and Vite concurrently)
composer run dev

# Run all tests
composer run test

# Run a single test file
php artisan test --filter=ExampleTest

# Lint with Laravel Pint
./vendor/bin/pint

# Migrate and seed
php artisan migrate
php artisan db:seed

# Regenerate OpenAPI docs
php artisan l5-swagger:generate
# Docs available at: http://localhost:8000/api/documentation
```

## Architecture

### Database & Auth
- SQLite by default (`DB_CONNECTION=sqlite`). No extra setup needed for local dev.
- Authentication uses **Laravel Sanctum** (Bearer tokens). Login returns a token; pass it as `Authorization: Bearer <token>`.
- `User` has a `tipo_perfil` field (enum `TipoPerfil`: `ong` | `voluntario`) that determines which profile sub-table the user has.
- On registration, the corresponding `ONG` or `Voluntario` record is automatically created alongside the `User`.

### Domain Model
The core entities and their relationships:

```
User ──1:1── ONG ──1:N── Demanda ──N:M── Categoria
User ──1:1── Voluntario ──N:M── Categoria
Demanda ──1:N── Inscricao ──1:N── Avaliacao
```

- **Demanda** status flow: `rascunho` → `aberta` → `encerrada` / `arquivada`
- **Inscricao** status flow: `pendente` → `aceita` / `recusada` → `concluida` / `cancelada`
- **Avaliacao** is only possible once an `Inscricao` reaches `concluida`. Mutual: ONG evaluates volunteer and vice-versa (`autor_tipo` enum: `ong` | `voluntario`).
- `Voluntario.mediaAvaliacoes()` only returns a rating once there are ≥ 3 reviews from ONGs.

### Pivot Tables
- `categoria_voluntario` — links `Voluntario` ↔ `Categoria`
- `categoria_demanda` — links `Demanda` ↔ `Categoria`

### Geo / Match System (`app/Support/Geo.php`)
Haversine-formula helpers for distance, proximity factor (0–1 within a 50 km radius), and radius check. The **Match** feature (`/api/match/sugestoes`, `/api/match/score`) is planned for **Etapa 4** — controllers are stubs returning placeholder messages.

### API Structure (`routes/api.php`)
- **Public routes** (no auth): list/show categorias, ONGs, voluntarios, demandas, and public avaliacoes.
- **Protected routes** (`auth:sanctum`): writes to categorias, ONG/voluntario profile edits, all demanda writes, inscricao workflow, avaliacoes writes, match, and dashboard.
- Controllers are in `app/Http/Controllers/Api/`.

### Authorization (Policies)
- `ONGPolicy::update` — only the ONG's own user can update.
- `VoluntarioPolicy::update` — only the voluntario's own user can update.
- Policies are registered explicitly in `AppServiceProvider::boot()` via `Gate::policy()`.

### Request Validation
Form Request classes are in `app/Http/Requests/` organized by domain (`Auth/`, `ONG/`, `Voluntario/`, `Categoria/`).

### API Resources
`app/Http/Resources/` — `ONGResource`, `VoluntarioResource`, `CategoriaResource` shape JSON output. Always use resources instead of returning models directly.

### OpenAPI Docs
Annotations use PHP 8 attributes (`#[OA\...]`) from `darkaonline/l5-swagger`. The base tags and security scheme are declared on the abstract `Controller` class. Every new endpoint needs its own `#[OA\...]` attribute block.

## Implementation Stages

The project is built in stages. Controllers for future stages return placeholder JSON messages:
- **Etapa 1** (done): Migrations, models, Sanctum, enums.
- **Etapa 2** (done): Auth, ONG/Voluntario CRUD, Categorias, Form Requests, Resources, Policies.
- **Etapa 3** (done): `DemandaController`, `InscricaoController`, `DemandaPolicy`, `InscricaoPolicy`, `DemandaResource`, `InscricaoResource`, Form Requests em `app/Http/Requests/Demanda/` e `Inscricao/`, `DemandaSeeder`.
- **Etapa 4** (done): `MatchController` com algoritmo de score (60 % categoria + 40 % proximidade Haversine). `app/Support/Geo.php` já existia.
- **Etapa 5** (done): `AvaliacaoController`, `DashboardController`, `AvaliacaoPolicy`, `AvaliacaoResource`, `StoreAvaliacaoRequest`.

Seeders run in dependency order: `CategoriaSeeder` → `ONGSeeder` → `VoluntarioSeeder` → `DemandaSeeder`. Future stages will add `InscricaoSeeder` and `AvaliacaoSeeder`.
- **Etapa 6** (done): Full Blade frontend. Web controllers in `app/Http/Controllers/Web/`, middleware `EnsureIsVoluntario` / `EnsureIsOng`, Apple-inspired design via Tailwind 4 + Vite.

## Frontend (Blade — Etapa 6)

Run `npm run dev` (or `composer run dev`) so Vite compiles CSS/JS during development. Run `npm run build` once for production.

### Design System
Apple-inspired: `#F5F5F7` page background, white surface cards, `#0071E3` primary blue, `#1D1D1F` / `#86868B` text, `system-ui` font (renders as SF Pro on Apple devices), pill buttons (`rounded-full`), `rounded-2xl` cards with `shadow-sm`, generous whitespace.

CSS tokens defined in `resources/css/app.css` under `@theme`: `--color-primary`, `--color-page`, `--color-surface`, `--color-ink`, `--color-ink-2`, `--color-border`, `--color-success`, `--color-danger`.

### Auth Strategy
Web routes use Laravel session auth (`Auth::login()` / `Auth::attempt()`), completely separate from API Sanctum Bearer tokens. The `auth` middleware (session) guards web routes; `auth:sanctum` (Bearer token) guards API routes. No conflict.

### Custom Middleware (registered as aliases in `bootstrap/app.php`)
- `voluntario` → `App\Http\Middleware\EnsureIsVoluntario`
- `ong` → `App\Http\Middleware\EnsureIsOng`

### Web Route Groups
- **Public**: `/`, `/demandas`, `/ongs` + show pages
- **Guest only**: `/login`, `/registro`
- **`auth` + `voluntario`**: `/dashboard`, `/perfil`, `/inscricoes`, `/match`
- **`auth` + `ong`**: `/dashboard/ong`, `/perfil/ong`, `/minhas-demandas/*`
- **`auth` (both)**: `POST /inscricoes/{id}/avaliar`

### Views Structure
```
resources/views/
├── layouts/app.blade.php       # Master layout (sticky nav, toasts, footer)
├── home/index.blade.php        # Landing page with live stats
├── auth/{login,registro}.blade.php
├── demandas/{index,show,minhas,criar,editar}.blade.php
├── ongs/{index,show}.blade.php
├── dashboard/{voluntario,ong}.blade.php
├── perfil/{voluntario,ong}.blade.php
├── inscricoes/{minhas,demanda}.blade.php
└── match/sugestoes.blade.php
```

Web controllers live in `app/Http/Controllers/Web/` and query models directly — no internal HTTP calls to the API.
