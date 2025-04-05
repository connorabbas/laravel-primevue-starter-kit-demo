# Laravel & PrimeVue Starter Kit - Admin Role

## Setup

```
git clone -b feature/admin-role https://github.com/connorabbas/laravel-primevue-starter-kit.git
```

## Features

-   Roles/Permissions system via [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v6/introduction)
- `Admin` role seeded by default
- Artisan command to register Users and assign their roles
- Example Users index page utilizing `useLazyDataTable()` composable

## Register new Admin User

Since there is no registration page for admins, use the following artisan command:

```
php artisan user:register
```

## Changes

[Compare against the master branch](https://github.com/connorabbas/laravel-primevue-starter-kit/compare/master...feature/admin-role)
