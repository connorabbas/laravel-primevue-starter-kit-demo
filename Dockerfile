ARG NODE_VERSION=22

# Base Image
# https://serversideup.net/open-source/docker-php/docs
FROM serversideup/php:8.4-fpm-nginx-alpine AS base
USER root
RUN install-php-extensions bcmath gd pgsql

# Node Image
FROM node:${NODE_VERSION}-alpine AS node

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

# Development Image
FROM base AS development
ARG USER_ID
ARG GROUP_ID
USER root
RUN apk add --no-cache curl git bash gnupg postgresql-client openssh-client \
    && apk add --no-cache --virtual .build-deps build-base autoconf \
    && install-php-extensions xdebug \
    && rm -rf /var/cache/apk/* \
    && apk del .build-deps
COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin
COPY .devcontainer/.bashrc /home/www-data/.bashrc
COPY .devcontainer/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN usermod -s /bin/bash www-data \
    && git config --global --add safe.directory /var/www/html \
    && mkdir -p /home/www-data/.ssh \
    && chown www-data:www-data /home/www-data/.ssh \
    && chown www-data:www-data /home/www-data/.bashrc \
    && docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID \
    && docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx
WORKDIR /var/www/html
USER www-data

# Production Image
FROM base AS release
WORKDIR /var/www/html
COPY --chown=www-data:www-data --from=composer /var/www/html/vendor ./vendor
COPY --chown=www-data:www-data --from=build-assets /var/www/html/public/build ./public/build
COPY --chown=www-data:www-data . .
USER www-data