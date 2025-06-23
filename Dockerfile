ARG NODE_VERSION=22

############################################
# Base Image
############################################
# https://serversideup.net/open-source/docker-php/docs
# Use Alpine-based PHP 8.4 FPM with NGINX
FROM serversideup/php:8.4-fpm-nginx-alpine AS base

USER root

# Install PHP extensions
RUN install-php-extensions bcmath gd pgsql

############################################
# Node Image (for development)
############################################
FROM node:${NODE_VERSION}-alpine AS node

############################################
# Development Image
############################################
FROM base AS development

ARG USER_ID
ARG GROUP_ID
ARG NODE_VERSION

# Setup Node/NPM
COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin

# Switch to root for package installation and user setup
USER root

# Install system dependencies using apk (Alpine package manager)
RUN apk add --no-cache \
    curl \
    git \
    bash \
    gnupg \
    postgresql-dev \
    openssh-client \
    && apk add --no-cache --virtual .build-deps build-base autoconf \
    && rm -rf /var/cache/apk/*

# Configure www-data user with bash shell
RUN usermod -s /bin/bash www-data

# Configure Git (for dev container use)
RUN git config --global --add safe.directory /var/www/html
RUN mkdir -p /home/www-data/.ssh \
    && chown www-data:www-data /home/www-data/.ssh

# Set user ID and group ID for www-data
RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx

# Drop privileges back to www-data
USER www-data

############################################
# Build assets (for production)
############################################
FROM node:${NODE_VERSION}-alpine AS assets
WORKDIR /var/www/html
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

############################################
# CI Image
############################################
FROM base AS ci

USER root
# Configure PHP-FPM to run as www-data
RUN echo "user = www-data" >> /usr/local/etc/php-fpm.d/docker-php-serversideup-pool.conf && \
    echo "group = www-data" >> /usr/local/etc/php-fpm.d/docker-php-serversideup-pool.conf

############################################
# Production Image
############################################
FROM base AS release

WORKDIR /var/www/html

# Composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application files
COPY --chown=www-data:www-data . /var/www/html

# Copy built assets
COPY --chown=www-data:www-data --from=assets /var/www/html/public/build /var/www/html/public/build

ENV SSL_MODE=mixed

USER www-data