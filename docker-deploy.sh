#!/usr/bin/env bash
set -euo pipefail

# ────────────────────────────────────────────────────────────────
# Simple manual deploy script for a Docker-based Laravel app
# Usage: chmod +x docker-deploy.sh && ./docker-deploy.sh
# ────────────────────────────────────────────────────────────────

# 1) Make sure we're in the project root (where docker-deploy.sh lives)
cd "$(dirname "$0")"

# 2) Maintenance mode
echo ":: Entering maintenance mode"
docker compose exec laravel php artisan down || true

# 3) Pull latest code
echo ":: Fetching latest from Git"
git pull origin master

# 4) Rebuild & restart container
echo ":: Rebuilding app image & restarting"
docker compose up -d --build laravel

echo ":: Waiting for app to report healthy..."
until docker compose ps laravel | grep -q "(healthy)"; do
  sleep 3
done

# 5) Run database migrations
echo ":: Running migrations"
docker compose exec laravel php artisan migrate --force

# 6) Optimize & cache
echo ":: Caching config, routes, and views"
docker compose exec laravel php artisan config:cache
docker compose exec laravel php artisan route:cache
docker compose exec laravel php artisan view:cache

# 7) Live again
echo ":: Bringing application back up"
docker compose exec laravel php artisan up || true

echo "✅  Deployment complete!"
