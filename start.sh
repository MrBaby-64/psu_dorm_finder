#!/bin/bash

# Render Start Script for PSU Dorm Finder
# This script handles the startup process for deployment

echo "🚀 Starting PSU Dorm Finder..."

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Create storage symlink
echo "🔗 Creating storage symlink..."
php artisan storage:link

# Start the Laravel application
echo "🌐 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=$PORT