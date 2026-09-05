# CovoiturageTN

A Tunisian carpooling web application. Users publish rides as **Passenger**
or **Driver** and browse others' publications, filtered by departure and
arrival location.

This is **Part 1** of the project: accounts, publications, locations, and
the core navigation/UI shell. See the development report delivered with
this phase for what's implemented, what's intentionally out of scope, and
what the next phase should cover.

## Stack

- **Backend:** Laravel 13, SQLite, Sanctum token authentication, REST API
- **Frontend:** Vue 3 (Composition API), Vite, Pinia, vue-router, vue-i18n, Tailwind CSS

## Project structure

```
/backend    Laravel API (routes/api.php, app/Http/Controllers/Api, ...)
/frontend   Vue 3 SPA (src/views, src/components, src/stores, ...)
/images     Supplied visual assets, reused by the frontend (frontend/public/images)
```

## Running locally

Two servers, run side by side.

### Backend (Laravel API - http://127.0.0.1:8000)

```bash
cd backend
composer install
cp .env.example .env   # already configured for SQLite
php artisan key:generate
php artisan migrate --seed   # creates tables, seeds Tunisian locations + the admin account
php artisan storage:link     # exposes uploaded profile photos
php artisan serve
```

### Frontend (Vue/Vite - http://127.0.0.1:5173)

```bash
cd frontend
npm install
npm run dev
```

The Vite dev server proxies `/api` and `/storage` to `http://127.0.0.1:8000`
(see `frontend/vite.config.js`), so the frontend talks to the backend
same-origin in development - no CORS configuration needed for local dev.

Open **http://127.0.0.1:5173**.

## Initial administrator account

```
Username: Ahmed
Password: Ahmed*123
```

Seeded by `database/seeders/AdminUserSeeder.php`, which reads
`ADMIN_USERNAME` / `ADMIN_PASSWORD` from `backend/.env` (defaults shown
above) rather than hard-coding credentials anywhere in the frontend.

## Tests performed for Part 1

- Full API smoke test via curl: register, login (valid/invalid), token
  auth on protected routes, logout + token revocation, profile update,
  password update (weak password rejected, strong password accepted),
  publication create/list/filter/show, location search.
- Frontend build (`npm run build`) passes with no errors.
- End-to-end browser walkthrough (Playwright): register -> home feed ->
  create publication -> Passenger/Driver tabs -> publication details ->
  profile -> language switch to Arabic (RTL) and back -> mobile viewport
  (bottom nav + hidden menu).
