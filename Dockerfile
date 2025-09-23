# ---------- Stage 1: PHP deps (Composer) ----------
FROM composer:2 AS vendor
WORKDIR /app
# Layer-cache: copy only composer files first
COPY composer.json composer.lock ./
# Install WITHOUT scripts (artisan isn't available in this stage)
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --no-scripts

# ---------- Stage 2: Frontend assets (Vite) ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
# copy the rest of the app (no vendor needed to build assets)
COPY resources ./resources
COPY vite.config.js postcss.config.js tailwind.config.js ./ 
# If you import from public/ (e.g. images), include it
COPY public ./public
RUN npm run build

# ---------- Stage 3: Final runtime (PHP 8.2) ----------
FROM php:8.2-cli-alpine AS runtime
WORKDIR /var/www/html

# System libs & PHP extensions commonly needed by Laravel + Postgres
RUN apk add --no-cache \
      bash curl git unzip icu-dev oniguruma-dev libzip-dev zlib-dev libpq-dev \
  && docker-php-ext-configure intl \
  && docker-php-ext-install -j$(nproc) \
      pdo_pgsql bcmath intl pcntl exif zip opcache

# (Optional) If you also use MySQL locally, uncomment:
# RUN docker-php-ext-install pdo_mysql

# Copy app source
COPY . .

# Bring in vendor from Composer stage and built assets from Node stage
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Make storage/cache writable and use non-root user
RUN adduser -D appuser \
  && mkdir -p storage bootstrap/cache \
  && chown -R appuser:appuser storage bootstrap/cache
USER appuser

# Entrypoint handles artisan tasks & server boot
COPY --chown=appuser:appuser entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENV PORT=8000 \
    APP_ENV=production \
    APP_DEBUG=false

EXPOSE 8000
CMD ["/entrypoint.sh"]
