#!/usr/bin/env bash
set -euo pipefail

# Usage: chmod +x docker-deploy.sh && ./docker-deploy.sh

# Make sure we're in the project root
cd "$(dirname "$0")"

# Maintenance mode
echo ":: Entering maintenance mode"
docker compose exec laravel php artisan down || true

# Pull latest code
echo ":: Fetching latest from Git"
git pull origin master

# Rebuild & restart container
echo ":: Rebuilding app image & restarting"
docker compose up -d --build laravel

# Wait for successful health check
echo ":: Waiting for app to report healthy..."
until docker compose ps laravel | grep -q "(healthy)"; do
  sleep 3
done

# Run database migrations
echo ":: Running migrations"
docker compose exec laravel php artisan migrate --force

# Optimize & cache
echo ":: Caching config, routes, and views"
docker compose exec laravel php artisan cache:clear
docker compose exec laravel php artisan optimize:clear
docker compose exec laravel php artisan optimize

# Live again
echo ":: Bringing application back up"
docker compose exec laravel php artisan up || true

echo "✅  Deployment complete!"
