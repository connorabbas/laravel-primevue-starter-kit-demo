############################################
# Base Image
############################################

# Use Alpine-based PHP 8.4 FPM with NGINX
FROM serversideup/php:8.4-fpm-nginx-alpine AS base

USER root

# Install PHP extensions
RUN install-php-extensions bcmath gd pgsql

############################################
# Development Image
############################################
FROM base AS development

# Pass USER_ID and GROUP_ID as build arguments
ARG USER_ID=1000
ARG GROUP_ID=1000
ARG NODE_VERSION=22

# Switch to root for package installation and user setup
USER root

# Install system dependencies using apk (Alpine package manager)
RUN apk add --no-cache \
    curl \
    git \
    bash \
    gnupg \
    postgresql-dev \
    && apk add --no-cache --virtual .build-deps \
    && rm -rf /var/cache/apk/*

# Install Node.js and npm
RUN apk add --no-cache nodejs npm \
    && npm install -g npm

# Configure Git
RUN git config --global --add safe.directory /var/www/html

# Configure www-data user with bash shell
RUN usermod -s /bin/bash www-data

# Set user ID and group ID for www-data
RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx

# Drop privileges back to www-data
USER www-data

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

# Copy application files
COPY --chown=www-data:www-data . /var/www/html

ENV SSL_MODE=mixed

USER www-data