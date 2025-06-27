ARG NODE_VERSION=22

# Base Image
# https://serversideup.net/open-source/docker-php/docs
FROM serversideup/php:8.4-fpm-nginx-alpine AS base
USER root
RUN install-php-extensions bcmath gd pgsql

# Composer Stage
FROM base AS composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Assets Stage
FROM node:${NODE_VERSION}-alpine AS build-assets
WORKDIR /var/www/html
COPY package*.json ./
RUN npm ci
COPY --from=composer /var/www/html/vendor/tightenco/ziggy ./vendor/tightenco/ziggy
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# Production Image
FROM base AS release
WORKDIR /var/www/html
COPY --chown=www-data:www-data --from=composer /var/www/html/vendor ./vendor
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=build-assets /var/www/html/public/build ./public/build
ENV PHP_OPCACHE_ENABLE=1
ENV SSL_MODE=mixed
USER www-data