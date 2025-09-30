# 🚀 Render Deployment Guide for PSU Dorm Finder

## ✅ Critical Environment Variables in Render Dashboard

Set these in **Render Dashboard → Your Service → Environment**:

```env
# Application
APP_NAME=PSU Dorm Finder
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app.onrender.com

# Database (PostgreSQL from Render)
DB_CONNECTION=pgsql
DB_HOST=your-postgres-host.oregon-postgres.render.com
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_password
DB_SSLMODE=require

# Session & Cache (IMPORTANT!)
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# File Storage
FILESYSTEM_DISK=public

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

## 🔑 How to Generate APP_KEY

Run locally:
```bash
php artisan key:generate --show
```

Copy the output (starts with `base64:`) and paste into Render ENV.

## 📊 Database Setup

1. **Create PostgreSQL Database in Render:**
   - Go to Render Dashboard
   - New → PostgreSQL
   - Copy the connection details

2. **Use Internal Connection:**
   - Use the **Internal Database URL** from Render
   - Format: `postgres://user:password@host/database`
   - Or use individual fields as shown above

## 🐛 Troubleshooting Render Deployment

### Instance Failed Error
**Symptoms:** "Instance failed: xxxxx"

**Solutions:**
1. Check Render logs for specific error
2. Verify `APP_KEY` is set correctly
3. Check database credentials
4. Make sure `DB_SSLMODE=require` is set

### Database Connection Error
**Symptoms:** "SQLSTATE[08006]" or connection refused

**Solutions:**
1. Use Internal Database URL (not External)
2. Verify `DB_SSLMODE=require`
3. Check PostgreSQL is running
4. Wait 60 seconds after database creation

### Cache/Session Errors
**Symptoms:** "Undefined table: cache"

**Solutions:**
1. Use `SESSION_DRIVER=file` and `CACHE_DRIVER=file`
2. OR ensure migrations ran (check logs)
3. Container will continue even if cache fails now

### APP_KEY Error
**Symptoms:** "No application encryption key"

**Solutions:**
1. Generate key: `php artisan key:generate --show`
2. Add to Render ENV variables
3. Must start with `base64:`

## ✅ Deployment Checklist

- [ ] PostgreSQL database created in Render
- [ ] All ENV variables set in Render dashboard
- [ ] `APP_KEY` generated and set
- [ ] Database credentials correct (Internal URL)
- [ ] `DB_SSLMODE=require` is set
- [ ] `SESSION_DRIVER=file` and `CACHE_DRIVER=file`
- [ ] GitHub repository connected
- [ ] Auto-deploy enabled
- [ ] Wait for first deployment (5-10 minutes)

## 📝 What Happens During Deployment

1. **Build Stage (Docker):**
   - Installs PHP, Apache, PostgreSQL drivers
   - Installs Composer dependencies
   - Installs NPM dependencies
   - Builds frontend assets
   - Creates startup script

2. **Startup Stage (Container):**
   - Checks APP_KEY
   - Waits for database connection (up to 60 sec)
   - Runs database migrations
   - Creates cache/sessions tables
   - Links storage directory
   - Seeds database (optional)
   - Starts Apache web server

3. **Running:**
   - Application available at your Render URL
   - Logs available in Render dashboard
   - Auto-deploys on git push

## 🎯 After Successful Deployment

1. **Visit your app:** `https://your-app.onrender.com`
2. **Register admin account** (first user)
3. **Test login** for all roles
4. **Check logs** in Render dashboard

## 🔄 Re-deploying

```bash
git add -A
git commit -m "Your message"
git push origin master
```

Render auto-deploys in ~5 minutes.

## 📞 If Still Having Issues

1. **Check Render Logs:**
   - Render Dashboard → Your Service → Logs
   - Look for red error messages

2. **Common Issues:**
   - Database connection = Check credentials
   - APP_KEY missing = Generate and set it
   - Migrations failing = Check database access
   - Container exiting = Check startup script logs

3. **Emergency Reset:**
   - Delete and recreate Render service
   - Recreate PostgreSQL database
   - Re-set all ENV variables
   - Re-deploy

## ✅ System is Production-Ready!

Your PSU Dorm Finder now:
- ✅ Works with PostgreSQL 17
- ✅ Bulletproof startup script
- ✅ All admin features working
- ✅ All landlord features working
- ✅ Booking & inquiry system working
- ✅ Real-world dorm finder functionality

Good luck! 🎓🏠