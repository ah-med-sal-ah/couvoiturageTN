# CovoiturageTN - Backend (Laravel API)

Laravel 13 + SQLite REST API for the CovoiturageTN carpooling app. Consumed
by the Vue 3 SPA in `../frontend`. See `../README.md` for the full picture
and how to run both sides together.

## Conventions used in this codebase

- **Auth:** Sanctum personal access tokens (`Authorization: Bearer <token>`),
  not cookie/session SPA auth. Login is by `username` + `password` - there
  is no email field or password-reset flow (not part of the current scope).
- **Users are User, not roles.** There's no "passenger" or "driver" account
  type - every user can publish either. `status` (`passenger` | `driver`)
  lives on `Publication`, not `User`. `is_admin` is the only role-like flag.
- **Routes:** everything except `POST /register` and `POST /login` requires
  `auth:sanctum` (see `routes/api.php`). There is no public, unauthenticated
  read access to publications/locations by design.
- **Validation:** Form Requests under `app/Http/Requests`, grouped by
  domain (`Auth/`, `User/`, `Publication/`). Password policy is enforced
  via Laravel's `Password` rule (`min(9)->mixedCase()->numbers()->symbols()`)
  - keep frontend and backend password rules in sync if either changes.
  Frontend also validates client-side; the backend is the source of truth.
- **Errors:** `bootstrap/app.php` normalizes API error responses (clean
  JSON, no stack traces/internal exception detail) regardless of
  `APP_DEBUG`. Keep new exception handling going through that same path
  rather than letting Laravel's default HTML error pages leak through.
- **Resources:** `app/Http/Resources` control exactly what's exposed;
  `PublicationResource` intentionally exposes only a minimal `author`
  subset (id, name, photo, gender) of the owning user, not the full
  `UserResource`.
- **Locations:** `database/seeders/LocationSeeder.php` seeds all 24
  governorates and their delegations (name_fr/name_ar). It's a working
  dataset, not verified 1:1 against the official INS delegation list -
  worth double-checking before this becomes a production data source.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```
