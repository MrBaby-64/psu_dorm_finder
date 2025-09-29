# Use official PHP image
FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libmariadb-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader

# Copy package.json for Node.js dependencies
COPY package.json package-lock.json* ./
RUN npm ci --only=production

# Copy the rest of the application
COPY . .

# Run final composer install and build assets
RUN composer install --no-dev --optimize-autoloader && \
    npm run build && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap/cache

# Expose the port that Render will use
EXPOSE $PORT

# Create a startup script
RUN echo '#!/bin/bash\n\
set -e\n\
echo "Database configuration:"\n\
echo "DB_CONNECTION=$DB_CONNECTION"\n\
echo "DB_HOST=$DB_HOST"\n\
echo "DB_DATABASE=$DB_DATABASE"\n\
echo "Starting Laravel application..."\n\
php artisan config:clear\n\
php artisan migrate --force\n\
php artisan storage:link\n\
exec php artisan serve --host=0.0.0.0 --port=$PORT' > /start.sh && chmod +x /start.sh

# Start the application
CMD ["/start.sh"]