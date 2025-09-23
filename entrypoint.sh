#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

# Ensure .env exists (ephemeral inside container; real config comes from env vars)
if [ ! -f .env ]; then
  cp .env.example .env || true
fi

# If APP_KEY not provided via env, generate one into .env
if ! php -r "exit((int) (getenv('APP_KEY') !== false));"; then
  php artisan key:generate --force || true
fi

# Make sure framework storage dirs exist (idempotent)
mkdir -p storage/framework/{cache,sessions,views}
php artisan storage:link || true

# Discover providers (we skipped composer scripts during build)
php artisan package:discover --ansi || true

# Cache config & routes (don’t fail the boot if not ready)
php artisan config:cache || true
php artisan route:cache || true

# Run database migrations (safe to keep non-fatal on first boot)
php artisan migrate --force --no-interaction || true

echo "Starting Laravel on 0.0.0.0:${PORT:-8000}"
exec php -S 0.0.0.0:${PORT:-8000} -t public
