ARG NODE_VERSION=22

# Base Image
# https://serversideup.net/open-source/docker-php/docs
FROM serversideup/php:8.4-fpm-nginx-alpine AS base
USER root
RUN install-php-extensions bcmath gd pgsql

# Node Image
FROM node:${NODE_VERSION}-alpine AS node

# Development Image
FROM base AS development
ARG USER_ID
ARG GROUP_ID
ARG NODE_VERSION
USER root
RUN apk add --no-cache curl git bash gnupg postgresql-client openssh-client \
    && apk add --no-cache --virtual .build-deps build-base autoconf \
    && rm -rf /var/cache/apk/* \
    && apk del .build-deps
COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin
RUN usermod -s /bin/bash www-data \
    && git config --global --add safe.directory /var/www/html \
    && mkdir -p /home/www-data/.ssh \
    && chown www-data:www-data /home/www-data/.ssh \
    && docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID \
    && docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx
USER www-data

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