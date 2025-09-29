#!/bin/bash

# Exit on error
set -e

echo "🚀 Starting PSU Dorm Finder deployment..."

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies
echo "📦 Installing Node dependencies..."
npm ci --only=production

# Build assets
echo "🔨 Building assets..."
npm run build

# Generate Laravel app key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --no-interaction
fi

# Clear and cache configurations
echo "⚡ Optimizing Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force --no-interaction

# Create storage link
echo "🔗 Creating storage symlink..."
php artisan storage:link

# Set proper permissions
echo "🔐 Setting file permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Seed database if needed (only on first deploy)
if [ "$SEED_DATABASE" = "true" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --no-interaction
fi

echo "✅ Deployment completed successfully!"
echo "🌐 PSU Dorm Finder is ready at: $APP_URL"