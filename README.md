# Laravel, Inertia.js, & PrimeVue Starter Kit

## About

![Static Badge](https://img.shields.io/badge/Laravel%20-%20v11%20-%20%23f9322c) ![Static Badge](https://img.shields.io/badge/Inertia.js%20-%20v2%20-%20%236b46c1) ![Static Badge](<https://img.shields.io/badge/Vue.js%20-%20v3.5%20-%20rgb(66%20184%20131)>) ![Static Badge](<https://img.shields.io/badge/PrimeVue%20-%20v4%20-%20rgb(16%20185%20129)>)

A basic authentication starter kit using [Laravel](https://laravel.com/docs/master), [Intertia.js](https://inertiajs.com/), and [PrimeVue](https://primevue.org/). An equivalent to using [Laravel Breeze](https://laravel.com/docs/master/starter-kits#laravel-breeze), but with the added benefit of all the PrimeVue components at your disposal.

Do you need a separate Vue SPA front-end instead of using Inertia.js? Consider using the [Vue SPA w/ PrimeVue & Laravel Breeze API Starter Kit](https://github.com/connorabbas/primevue-spa-laravel-api) instead.

## Features

-   Same auth structure and features as Laravel Breeze with User Profile page
-   Need an admin panel? [There's a branch for that.](https://github.com/connorabbas/laravel-inertia-primevue/tree/feature/admin-panel)
-   Need SSR support? [There's a branch for that.](https://github.com/connorabbas/laravel-inertia-primevue/tree/feature/ssr)
-   Pre-configured [Auto Import](https://primevue.org/autoimport/) PrimeVue components
-   Wrapper components for PrimeVue's `Breadcrumb`, `Menu`, `MenuBar`, & `PanelMenu` utilizing Inertia's `Link` component
-   Light/Dark mode toggle
-   `usePaginatedData()` & `useLazyDataTable()` composables for use with PrimeVue's `Paginator` & `DataTable` components for easy pagination/filtering/sorting (example usage in `feature/admin-panel` branch)
-   Easily customizable theming
-   Configured to use TypeScript (not required)

## Theme

This starter kit provides a light/dark mode toggle by default, and custom theme functionality provided by the powerful **PrimeVue V4** theming system, using styled mode and custom design token values.

The starting point for customizing your theme will be the `resources/js/theme-preset.js` module file. To quickly change the look and feel of your site, swap the [primary](https://primevue.org/theming/styled/#primary) values with a different set of [colors](https://primevue.org/theming/styled/#colors), change the [surface](https://primevue.org/theming/styled/#surface) `colorScheme` values (slate, gray, neutral, etc.), or completely change the [preset theme](https://primevue.org/theming/styled/#presets) (Aura used by default).

Please reference the [PrimeVue Styled Mode Docs](https://primevue.org/theming/styled/) to fully understand how this system works, and how to further customize your theme to make it your own.

## PrimeVue v4 w/ Tailwind CSS

If you have used a previous version of this project using **PrimeVue V3**, you'll know that [PrimeFlex](https://primeflex.org/) was used instead of [Tailwind CSS](https://tailwindcss.com/) for utility class styling. With V4 however, the PrimeTek team has officially suggested [Moving from PrimeFlex to Tailwind CSS](https://primevue.org/guides/primeflex/).

For this reason PrimeFlex has been removed, and Tailwind has been added into the project, along with the [tailwindcss-primeui](https://primevue.org/tailwind/#plugin) plugin. CSS layers have also been implemented so the Tailwind utilities can [override](https://primevue.org/tailwind/#override) the PrimeVue component styling when needed.

---

## Usage with Docker

This starter kit is configured to use Docker Compose for local development with just a few extra steps, powered by images & configuration from [Laravel Sail](https://laravel.com/docs/master/sail). With this setup, you do not need PHP, Composer, PostgreSQL or Node.js installed on your machine to get up and running with this project.

### Setup

1. In a new directory (outside of your Laravel project) create a `docker-compose.yml` file to create a reverse proxy container using [Traefik](https://doc.traefik.io/traefik/getting-started/quick-start/). You can clone/reference this [example implementation](https://github.com/connorabbas/traefik-docker-compose/blob/master/docker-compose.yml).

2. Step into the directory containing the new compose file, and spin up the Traefik container:
    ```
    docker compose up -d
    ```
3. Update Laravel app `.env`

    ```env
    # Use any desired domain ending with .localhost
    # Match with value used in docker-compose.local.yml
    APP_URL=http://primevue-inertia.localhost

    DB_CONNECTION=pgsql
    DB_HOST=pgsql # name of service
    DB_PORT=5432
    DB_DATABASE=laravel
    DB_USERNAME=sail
    DB_PASSWORD=password

    # Update port values as needed when running multiple projects
    APP_PORT=8000
    VITE_PORT=5173
    FORWARD_DB_PORT=5432
    ```

4. Build the Laravel app container using one of the following techniques:
    - Build manually using docker compose CLI (like above)
    - Use [Laravel Sail](https://laravel.com/docs/master/sail)
    - Build as a [VS Code Dev Container](https://code.visualstudio.com/docs/devcontainers/tutorial) using the `Dev Containers: Reopen in Container` command

### Additional configuration

If you wish to add additional services, or swap out PostgreSQL with an alternative database, you can reference the [Laravel Sail stubs](https://github.com/laravel/sail/tree/1.x/stubs) and update the `docker-compose.local.yml` file as needed.
