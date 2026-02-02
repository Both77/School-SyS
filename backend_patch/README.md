# Backend Patch (Laravel API) — Admin + Teacher only

This folder is a **patch** (custom files) to drop into a fresh Laravel project.

## Quick start
1) Create a new Laravel project (Laravel 10/11):
   - `composer create-project laravel/laravel school-sms-api`
2) Install Sanctum:
   - `composer require laravel/sanctum`
   - `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
3) Copy the contents of this `backend_patch/` into your Laravel project root (merge folders).
4) Configure `.env`:
   - Set DB credentials
   - `FRONTEND_URL=http://localhost:5173`
5) Run migrations + seed admin:
   - `php artisan migrate`
   - `php artisan db:seed --class=AdminUserSeeder`
6) Start API:
   - `php artisan serve`

## Default admin
Email: admin@school.com
Password: admin123

## Teacher permissions
- View Students/Classes/Subjects/Routines/Notices
- Mark attendance

Admin has full CRUD.
