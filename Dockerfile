# Use official PHP with Apache for production
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libmariadb-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    supervisor \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers

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

# Create necessary directories and set permissions
RUN mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache storage/app/public bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configure Apache document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create Apache configuration
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Build assets and optimize for production
# Note: Cache operations are skipped here and done at runtime after DB is ready
RUN npm run build \
    && composer install --no-dev --optimize-autoloader

# Create supervisor configuration for queue workers
RUN mkdir -p /etc/supervisor/conf.d
RUN echo '[program:laravel-queue]\n\
process_name=%(program_name)s_%(process_num)02d\n\
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600\n\
autostart=true\n\
autorestart=true\n\
stopasgroup=true\n\
killasgroup=true\n\
user=www-data\n\
numprocs=2\n\
redirect_stderr=true\n\
stdout_logfile=/var/www/html/storage/logs/worker.log\n\
stopwaitsecs=3600' > /etc/supervisor/conf.d/laravel-queue.conf

# Create startup script
RUN echo '#!/bin/bash\n\
set -e\n\
echo "=== PSU Dorm Finder Starting ==="\n\
echo "Environment: $APP_ENV"\n\
echo "Database: $DB_CONNECTION"\n\
echo "Host: $DB_HOST"\n\
echo "Database: $DB_DATABASE"\n\
\n\
# Verify APP_KEY is set and valid\n\
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then\n\
    echo "ERROR: APP_KEY is not set or invalid!"\n\
    echo "Generating new APP_KEY..."\n\
    php artisan key:generate --force\n\
else\n\
    echo "APP_KEY is configured"\n\
fi\n\
\n\
# Clear any cached config that might conflict\n\
echo "Clearing application caches..."\n\
php artisan config:clear || echo "Config clear skipped"\n\
php artisan view:clear || echo "View clear skipped"\n\
\n\
# Test basic Laravel boot\n\
echo "Testing Laravel application boot..."\n\
php artisan --version || { echo "Laravel boot failed - check APP_KEY and configuration"; exit 1; }\n\
\n\
# Run migrations (this will create cache and sessions tables)\n\
echo "Running database migrations..."\n\
php artisan migrate --force || { echo "Migration failed - check database connection"; exit 1; }\n\
\n\
# Now safe to clear cache after tables exist\n\
echo "Clearing cache after migration..."\n\
php artisan cache:clear || echo "Cache clear skipped"\n\
\n\
# Create storage link\n\
echo "Creating storage link..."\n\
php artisan storage:link || echo "Storage link already exists or failed"\n\
\n\
# Seed database if needed\n\
echo "Checking database setup..."\n\
php artisan db:seed --class=DatabaseSeeder --force || echo "Seeding skipped or completed"\n\
\n\
# Set final permissions\n\
echo "Setting permissions..."\n\
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache\n\
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache\n\
\n\
# Start supervisor for queue workers\n\
echo "Starting queue workers..."\n\
service supervisor start\n\
\n\
echo "=== Application Ready ==="\n\
\n\
# Start Apache in foreground\n\
exec apache2-foreground' > /start.sh && chmod +x /start.sh

# Expose port 80 for Apache
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start the application
CMD ["/start.sh"]