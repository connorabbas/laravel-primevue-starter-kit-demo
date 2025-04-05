# Laravel & PrimeVue Starter Kit - Admin Role

## Setup

```
git clone -b feature/admin-role https://github.com/connorabbas/laravel-primevue-starter-kit.git
```

## Features

-   Roles/Permissions system via [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v6/introduction)
-   `Admin` role seeded by default
-   Artisan command to register new Users (and optionally assign their roles)
-   Example Users index page utilizing `useLazyDataTable()` composable

## Register new Admin User

Since there is no registration page for admins, use the following artisan command:

```
php artisan user:register
```

And assign the Admin role.

## Admin Pages

A separate Admin dashboard page, and a Users index page are provided by default, protected by the `role:Admin` middleware

The Users index page provides an example using the `useLazyDataTable()` composable and PrimeVue's `<DataTable />` component. To seed the users table with test data (locally) you can run:

```bash
php artisan db:seed
```

## Changes

[Compare against the master branch](https://github.com/connorabbas/laravel-primevue-starter-kit/compare/master...feature/admin-role)
