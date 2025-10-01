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

# CRITICAL: Remove cached config files physically to ensure fresh load
echo "🧹 Removing cached config files..."
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes-*.php
rm -f bootstrap/cache/services.php

# Clear all caches
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

# Verify mail config is loaded correctly
echo "📧 Verifying mail configuration..."
php artisan tinker --execute="echo 'MAIL_MAILER: ' . config('mail.default') . PHP_EOL; echo 'MAIL_HOST: ' . config('mail.mailers.smtp.host') . PHP_EOL; echo 'MAIL_USERNAME: ' . config('mail.mailers.smtp.username') . PHP_EOL;"

echo "✅ Build process completed successfully!"