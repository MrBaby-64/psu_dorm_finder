#!/bin/bash

# Render Build Script for PSU Dorm Finder
# This script handles the build process for deployment

echo "🚀 Starting build process..."

# Install PHP dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
echo "🎨 Installing NPM dependencies and building assets..."
npm install
npm run build

# Clear all caches first
echo "🧹 Clearing all caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Cache Laravel configuration for better performance
echo "⚡ Caching Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build process completed successfully!"