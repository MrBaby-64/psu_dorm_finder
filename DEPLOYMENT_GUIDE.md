# PSU Dorm Finder - Render Deployment Guide

## 🚀 Deployment Steps

### 1. Prerequisites
- Git repository connected to Render
- PostgreSQL database configured
- Domain: https://psu-dorm-finder.onrender.com

### 2. Environment Variables in Render
Set these environment variables in your Render dashboard:

```env
APP_NAME=PSU Dorm Finder
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:q/ChcGREoCbLnAH04F7EE9RrPxlPwwvFTSjm5L87/Gs=
APP_URL=https://psu-dorm-finder.onrender.com

# Database (Render will auto-populate these from your PostgreSQL service)
DB_CONNECTION=pgsql
DATABASE_URL=${DATABASE_URL}

# Logging
LOG_CHANNEL=stderr
LOG_LEVEL=info

# Cache & Sessions
CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# reCAPTCHA
NOCAPTCHA_SITEKEY=6LdontgrAAAAADF6Wl923X1kXTDVBZdBU5oFHImD
NOCAPTCHA_SECRET=6LdontgrAAAAACkN0f77aL7bOsGo1PfcAFFHxs3N

# Email (Configure with your SMTP settings)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@psu-dorm-finder.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Build & Start Commands

**Build Command:**
```bash
composer install --no-dev --optimize-autoloader && npm ci --only=production && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**Start Command:**
```bash
php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT
```

### 4. Database Setup
1. Create a PostgreSQL database in Render
2. Migrations will run automatically on deployment
3. Create admin user manually or via seeder

### 5. File Storage
- Uses local storage with symlinks
- Images stored in `storage/app/public`
- Accessible via `/storage` route

## 🔧 Troubleshooting

### Common Issues:

1. **500 Errors on Admin/Landlord Pages**
   - Fixed with error handling in controllers
   - Check logs: `heroku logs --tail` equivalent in Render

2. **Database Connection Issues**
   - Ensure DATABASE_URL is properly set
   - Check PostgreSQL service status

3. **Asset Loading Issues**
   - Run `php artisan storage:link`
   - Check APP_URL matches your domain

4. **reCAPTCHA Not Working**
   - Verify NOCAPTCHA_SITEKEY and NOCAPTCHA_SECRET
   - Check domain whitelist in Google reCAPTCHA console

### Logs Access:
Check deployment logs in Render dashboard or via CLI

### Manual Commands (if needed):
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recreate caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
php artisan storage:link

# Run migrations
php artisan migrate --force
```

## 🎯 Post-Deployment Checklist

1. ✅ Site loads at https://psu-dorm-finder.onrender.com
2. ✅ Registration works (both tenant and landlord)
3. ✅ reCAPTCHA displays and validates
4. ✅ Rate limiting works (try 6+ registrations)
5. ✅ Admin dashboard loads without errors
6. ✅ Landlord properties page loads
7. ✅ Image uploads work
8. ✅ Email functionality (password reset)

## 📧 Email Configuration

For production email, update these variables with your SMTP provider:
- Gmail: Use app password, not regular password
- Other SMTP: Update host, port, encryption accordingly

## 🔒 Security Notes

- APP_DEBUG is set to false
- Database credentials auto-managed by Render
- reCAPTCHA keys included for bot protection
- Rate limiting enabled for registration

## 🆘 Support

If deployment fails:
1. Check Render build logs
2. Verify all environment variables
3. Ensure PostgreSQL database is connected
4. Check Laravel logs in Render dashboard