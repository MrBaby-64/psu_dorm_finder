# Render Deployment Guide

Since automated deployment with render.yaml is having issues, please configure manually in the Render dashboard:

## Step 1: Create New Web Service

1. Go to your Render dashboard
2. Click "New +" → "Web Service"
3. Connect your GitHub repository: `MrBaby-64/psu_dorm_finder`
4. Choose branch: `master`

## Step 2: Configure Build & Deploy

**Environment**: `PHP`
**Region**: `Oregon (US West)`
**Branch**: `master`
**Build Command**:
```bash
composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**Start Command**:
```bash
php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT
```

## Step 3: Environment Variables

Set these in the Render dashboard:

### Basic Laravel Configuration
- `APP_ENV` = `production`
- `APP_DEBUG` = `false`
- `APP_KEY` = (Generate new key)
- `APP_URL` = `https://YOUR_APP_NAME.onrender.com`

### Database Configuration (PostgreSQL)
- `DB_CONNECTION` = `pgsql`
- `DB_HOST` = (From your PostgreSQL service)
- `DB_PORT` = (From your PostgreSQL service)
- `DB_DATABASE` = (From your PostgreSQL service)
- `DB_USERNAME` = (From your PostgreSQL service)
- `DB_PASSWORD` = (From your PostgreSQL service)

### Laravel Session & Cache
- `LOG_CHANNEL` = `stderr`
- `LOG_LEVEL` = `info`
- `SESSION_DRIVER` = `database`
- `CACHE_DRIVER` = `file`
- `QUEUE_CONNECTION` = `database`

### reCAPTCHA (Optional)
- `NOCAPTCHA_SITEKEY` = `6LdontgrAAAAADF6Wl923X1kXTDVBZdBU5oFHImD`
- `NOCAPTCHA_SECRET` = `6LdontgrAAAAACkN0f77aL7bOsGo1PfcAFFHxs3N`

### Email Configuration
- `MAIL_MAILER` = `log`
- `MAIL_FROM_ADDRESS` = `noreply@YOUR_APP_NAME.onrender.com`
- `MAIL_FROM_NAME` = `PSU Dorm Finder`

## Step 4: Create PostgreSQL Database

1. Create a new PostgreSQL service in Render
2. Name it `psu-dorm-finder-db`
3. Use the database connection details in your web service environment variables

## Step 5: Deploy

1. Click "Create Web Service"
2. Monitor the deployment logs
3. Once successful, your app will be available at the provided URL

## Troubleshooting

- If build fails, check that Node.js and PHP versions are compatible
- If database connection fails, verify all DB_* environment variables
- Check deployment logs for specific error messages