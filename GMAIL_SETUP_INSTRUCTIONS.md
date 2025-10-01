# 📧 Gmail SMTP Setup Instructions for PSU Dorm Finder

## ✅ What Has Been Fixed

1. **Remember Me** - ✅ Working (checkbox added to login page)
2. **Forgot Password** - ✅ Code ready (needs Gmail configuration)

---

## 🔧 Step 1: Get Gmail App Password

### Option A: Using Personal Gmail

1. Go to your Google Account: https://myaccount.google.com/
2. Click **Security** (left sidebar)
3. Under "How you sign in to Google", enable **2-Step Verification** (if not enabled)
4. After enabling 2FA, go back to Security
5. Click **App passwords** (under "How you sign in to Google")
6. Select:
   - App: **Mail**
   - Device: **Windows Computer** (or Other)
7. Click **Generate**
8. Copy the 16-character password (it will look like: `abcd efgh ijkl mnop`)
9. **Save this password** - you won't see it again!

### Option B: Using Gmail for Business (Workspace)

1. Go to: https://admin.google.com/
2. Navigate to **Security** → **App passwords**
3. Follow similar steps as above

---

## 🛠️ Step 2: Update Your .env File

Open `.env` in your project root and update these lines:

```env
# CHANGE FROM:
MAIL_MAILER=log

# CHANGE TO:
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-actual-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-actual-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Example (with fake credentials):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=johndoe@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=johndoe@gmail.com
MAIL_FROM_NAME="PSU Dorm Finder"
```

**⚠️ IMPORTANT:**
- Use the **App Password**, NOT your regular Gmail password
- Remove spaces from the app password: `abcdefghijklmnop`
- Keep quotes around `MAIL_FROM_NAME` only

---

## 🚀 Step 3: Clear Cache and Test

Run these commands in your terminal:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🧪 Step 4: Test Forgot Password

1. Start your Laravel server:
   ```bash
   php artisan serve
   ```

2. Go to: http://127.0.0.1:8000/login

3. Click **"Forgot your password?"**

4. Enter your email address

5. Check your Gmail inbox for the password reset email

---

## 🔒 For Production (Render)

In your Render dashboard, add these environment variables:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=PSU Dorm Finder
```

This ensures the same code works on both localhost (MySQL) and production (PostgreSQL)!

---

## 🐛 Troubleshooting

### Problem: "Failed to authenticate on SMTP server"
**Solution:** Make sure you're using the App Password, not your regular Gmail password

### Problem: "Connection refused"
**Solution:** Check if port 587 is blocked by firewall. Try port 465 with:
```env
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

### Problem: "Password reset link not showing in email"
**Solution:** Already fixed! The email template has been created.

### Problem: No email received
**Solution:** 
1. Check spam/junk folder
2. Verify MAIL_FROM_ADDRESS matches MAIL_USERNAME
3. Check Laravel logs: `storage/logs/laravel.log`

---

## ✨ Testing Remember Me

1. Go to login page
2. Check the **"Remember me"** checkbox
3. Login
4. Close browser completely
5. Open browser again and visit your site
6. You should still be logged in! ✅

---

## 📝 How It Works (Technical)

### Remember Me:
- When checked, Laravel creates a "remember_token" in the database
- Token is stored in browser cookie for 5 years (Laravel default)
- Works on both MySQL and PostgreSQL automatically

### Forgot Password:
- User enters email → Checks database for email existence
- Creates token in `password_reset_tokens` table (both MySQL & PostgreSQL)
- Sends email with reset link
- Link expires after 60 minutes
- User clicks link → Sets new password → Old token deleted

### Database Compatibility:
- All queries use Eloquent ORM
- Works identically on MySQL (localhost) and PostgreSQL (production)
- No database-specific code needed! 🎉

---

## 🎨 What Was Added/Fixed

### Files Created:
1. `resources/views/auth/login.blade.php` - Complete login page with Remember Me
2. `resources/views/emails/password-reset.blade.php` - Beautiful password reset email

### Files Already Working:
1. `app/Http/Requests/Auth/LoginRequest.php:44` - Remember Me logic
2. `app/Http/Controllers/Auth/PasswordResetLinkController.php` - Forgot password handler
3. `app/Http/Controllers/Auth/NewPasswordController.php` - New password handler
4. `app/Notifications/CustomPasswordResetNotification.php` - Email notification

### Database Tables Used:
1. `users` table - stores remember_token
2. `password_reset_tokens` table - stores reset tokens (auto-created by Laravel)

---

## 🎯 Next Steps

1. Follow Step 1 to get Gmail App Password
2. Update .env file (Step 2)
3. Run cache clear commands (Step 3)
4. Test both features (Step 4)
5. Deploy to production with environment variables

---

Need help? Check Laravel logs at: `storage/logs/laravel.log`
