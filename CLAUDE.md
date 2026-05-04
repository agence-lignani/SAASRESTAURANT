# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

- **Laravel 13** + **Filament 5** (admin panel at `/admin`)
- **PHP 8.3+** — requires `ext-intl` (use `composer install --ignore-platform-req=ext-intl` if missing temporarily)
- **Node.js 20.19+ or 22.12+** — required by Vite 8
- **SQLite** (default, in-memory for tests) or MySQL for production

## Commands

```bash
# Initial setup
composer install && cp .env.example .env && php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve

# Development (Laravel + Vite together)
composer run dev

# Run all tests
php artisan test

# Run a single test file
php artisan test tests/Feature/SomeTest.php

# Run a specific test method
php artisan test --filter=test_method_name

# PHP linting
./vendor/bin/pint

# Frontend build
npm run build   # production
npm run dev     # watch mode
```

Seed credentials: `admin@example.test` / `password`

## Architecture

### Multi-tenancy

The app is multi-tenant: each `Restaurant` is a tenant. Tenant resolution for the public site happens in `ResolveRestaurantTenant` middleware — it matches `public_host` against the HTTP `Host` header, falling back to the first restaurant by `id` (which means `localhost` always uses the seed restaurant).

For the Filament admin, `BindFilamentRestaurant` binds `app('filament.restaurant')` from the authenticated user's restaurants. All Filament resources that are restaurant-scoped use the `BelongsToCurrentRestaurant` trait on `getEloquentQuery()`.

### Roles

Users can hold one of four roles (`owner`, `editor`, `reservation`, `server`) per restaurant, stored in the `restaurant_user` pivot. The active role is read from the session via `FilamentAccess::role()` and enforced in every Policy class under `app/Policies/`.

### Public Site

All public routes are under the `tenant` + `published` + `record_site_page_view` middleware group. Controllers live in `app/Http/Controllers/Site/`. Views use the `bistro` layout (`resources/views/layouts/bistro.blade.php`), which injects CSS variables from `BistroManifest::cssVariablesForRestaurant()`.

### Site Content / Page Sections

Editable content for each page is stored as JSON in `restaurant_page_contents.content`. The flow is:

1. `SiteContentDefaults` provides fallback values built from restaurant fields.
2. `SiteContentResolver::forRestaurant()` deep-merges custom overrides over defaults.
3. `SiteContentNormalizer` ensures the merged array conforms to the expected shape.
4. `HomeSectionCatalog` / `PageSectionCatalog` define the available and ordered sections for home/inner pages.

### Theme (Bistro)

`themes/bistro/manifest.php` holds design tokens (colors, radii, shadows). `BistroManifest` loads these and merges per-restaurant overrides from `restaurant_theme_settings`. CSS variables are emitted inline in the Bistro layout.

### Chat Assistant (F20)

`config/llm.php` controls the driver: `fake` (deterministic, no network, used in tests) or `openai_compat` (OpenAI-compatible endpoint). `LlmChatCompletionService` calls the LLM; `MenuContextBuilder` builds the system prompt from the restaurant's menu.

### Menu Photo Import

A queue job (`ProcessMenuPhotoImport`) extracts text from photos via the `MenuImportExtractor` contract (either `TesseractMenuImportExtractor` for real OCR or `StubMenuImportExtractor` for tests), then `MenuOcrTextParser` + LLM parse the text into a draft, and `ApplyMenuDraftToMenu` upserts categories/items.

### Reservations

`BookingSetting` per restaurant controls capacity, manual confirmation, and external provider credentials. `ExternalReservationSyncService` syncs from providers (OpenTable, TheFork, Zenchef) via the `ExternalReservationProvider` contract. `ReservationObserver` triggers email notifications on status changes. `SendReservationReminders` is a scheduled console command.

### Filament Resource Structure

Each Filament resource follows the pattern:
```
app/Filament/Resources/{ResourceName}/
  {ResourceName}Resource.php     ← table columns, navigation, policies
  Pages/                         ← List, Create, Edit, View pages
  Schemas/                       ← Form and Infolist definitions
  Tables/                        ← Table column/filter definitions
```

### Testing

Tests use SQLite in-memory. The `LLM_DRIVER=fake` and `MENU_IMPORT_DRIVER=stub` env vars are set in `phpunit.xml` to prevent network calls. Test suites: `Unit` (`tests/Unit/`) and `Feature` (`tests/Feature/`).
