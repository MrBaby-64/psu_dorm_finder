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
RUN npm run build 2>&1 || { echo "NPM build failed, but continuing..."; } \
    && composer install --no-dev --optimize-autoloader --no-interaction

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

# Create startup script with robust error handling
RUN echo '#!/bin/bash\n\
echo "=== PSU Dorm Finder Starting ==="\n\
echo "Environment: $APP_ENV"\n\
echo "Database: $DB_CONNECTION"\n\
echo "Mail Mailer: $MAIL_MAILER"\n\
echo "Mail Host: $MAIL_HOST"\n\
\n\
# Verify APP_KEY exists\n\
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then\n\
    echo "ERROR: APP_KEY not set! Generating..."\n\
    php artisan key:generate --force || echo "Key generation failed"\n\
fi\n\
\n\
# Clear ALL caches including bootstrap cache files\n\
echo "Clearing caches..."\n\
rm -f bootstrap/cache/config.php bootstrap/cache/routes-*.php bootstrap/cache/services.php\n\
php artisan config:clear 2>/dev/null || true\n\
php artisan route:clear 2>/dev/null || true\n\
php artisan view:clear 2>/dev/null || true\n\
php artisan cache:clear 2>/dev/null || true\n\
\n\
# Rebuild config cache with fresh environment variables\n\
echo "Caching configuration..."\n\
php artisan config:cache 2>/dev/null || echo "Config cache failed"\n\
\n\
# Verify mail configuration\n\
echo "Verifying mail config..."\n\
php artisan tinker --execute="echo config('"'"'mail.default'"'"') . PHP_EOL;" 2>/dev/null || true\n\
\n\
# Test Laravel can boot\n\
echo "Testing Laravel..."\n\
php artisan --version || { echo "Laravel failed to boot"; sleep 10; exit 1; }\n\
\n\
# Wait for database to be ready\n\
echo "Waiting for database..."\n\
for i in {1..30}; do\n\
    if php artisan db:show 2>/dev/null; then\n\
        echo "Database connected!"\n\
        break\n\
    fi\n\
    echo "Waiting for DB... attempt $i/30"\n\
    sleep 2\n\
done\n\
\n\
# Run migrations with proper error handling\n\
echo "Running migrations..."\n\
php artisan migrate --force 2>&1\n\
MIGRATION_EXIT_CODE=$?\n\
if [ $MIGRATION_EXIT_CODE -ne 0 ]; then\n\
    echo "===== MIGRATION FAILED =====" >&2\n\
    echo "Exit code: $MIGRATION_EXIT_CODE" >&2\n\
    php artisan migrate:status 2>&1 || true\n\
    echo "Attempting migration again with verbose output..." >&2\n\
    php artisan migrate --force -vvv 2>&1 || true\n\
fi\n\
echo "Migration complete (exit code: $MIGRATION_EXIT_CODE)"\n\
\n\
# Create storage link\n\
php artisan storage:link 2>/dev/null || true\n\
\n\
# Seed database (optional)\n\
php artisan db:seed --class=DatabaseSeeder --force 2>/dev/null || true\n\
\n\
# Set permissions\n\
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true\n\
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true\n\
\n\
# Start supervisor\n\
service supervisor start 2>/dev/null || true\n\
\n\
echo "=== Application Ready ==="\n\
\n\
# Start Apache\n\
exec apache2-foreground' > /start.sh && chmod +x /start.sh

# Expose port 80 for Apache
EXPOSE 80

# Health check (disabled initially to allow startup)
# HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
#     CMD curl -f http://localhost/ || exit 1

# Start the application
CMD ["/start.sh"]