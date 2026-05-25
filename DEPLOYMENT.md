# HeavenlyMatch — Shared Hosting Deployment Guide

> Target: cPanel / LiteSpeed / Apache shared hosting (PHP 8.2+, MySQL 8.0+)

---

## 1. Pre-Deployment Checklist (run locally)

```bash
# 1. Ensure the build is clean and assets are compiled
npm run build

# 2. Verify no migration is missing
php artisan migrate:status

# 3. Clear all local caches
php artisan optimize:clear

# 4. Commit everything (including public/build/)
git status
```

> **Important:** The compiled `public/build/` directory must be committed to git or uploaded manually. The server does not run Node.js.

---

## 2. Server Directory Structure

On shared hosting, Laravel must live **outside** `public_html` to keep `.env`, `vendor/`, and `storage/` private.

```
~/
├── laravel/                  ← full Laravel project (git clone here)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/               ← this folder becomes public_html
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                  ← never web-accessible
│   └── ...
└── public_html/              ← symlink or manual copy of laravel/public/
```

### Option A — Symlink (recommended, if cPanel allows it)

In cPanel Terminal or SSH:
```bash
# Remove existing public_html (back it up first if it has files)
mv ~/public_html ~/public_html_backup

# Symlink public/ to public_html
ln -s ~/laravel/public ~/public_html
```

### Option B — Manual copy (if symlinks are blocked)

1. Upload all files from `laravel/public/` into `public_html/`
2. Edit `public_html/index.php` — change the two path constants:
   ```php
   // Find and update these two lines:
   require __DIR__.'/../vendor/autoload.php';
   // Change to:
   require __DIR__.'/../../laravel/vendor/autoload.php';

   $app = require_once __DIR__.'/../bootstrap/app.php';
   // Change to:
   $app = require_once __DIR__.'/../../laravel/bootstrap/app.php';
   ```
3. Also copy `public/.htaccess` into `public_html/` if it isn't there already.

---

## 3. Upload Files

### Via Git (recommended)

```bash
# On the server (SSH / cPanel Terminal):
cd ~/laravel
git clone https://github.com/yourusername/heavenlymatch.git .
# or pull latest:
git pull origin master
```

### Via FTP/File Manager

Upload the entire project (excluding `node_modules/` and `.git/`) into `~/laravel/`.

---

## 4. Environment Configuration

Copy `.env.example` to `.env` on the server and set production values:

```bash
cp .env.example .env
nano .env   # or edit in cPanel File Manager
```

Minimum required production values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_KEY=                          # generated in step 5

DB_HOST=localhost
DB_DATABASE=cpanel_dbname
DB_USERNAME=cpanel_dbuser
DB_PASSWORD=strong_password_here

SESSION_DRIVER=file
SESSION_DOMAIN=yourdomain.com
SESSION_SECURE_COOKIE=true

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com

LOG_LEVEL=error
LOG_CHANNEL=single
```

---

## 5. Install Dependencies

```bash
cd ~/laravel

# Install PHP packages (no dev packages in production)
composer install --no-dev --optimize-autoloader
```

> If `composer` is not in PATH, use: `php -d memory_limit=512M /usr/local/bin/composer install --no-dev`

---

## 6. Generate App Key

Only run this if `APP_KEY` is blank in `.env`. **Never regenerate the key on a live site with existing sessions.**

```bash
php artisan key:generate
```

---

## 7. Database Setup

### Import existing database (from local export):

```bash
# In cPanel → phpMyAdmin: import your .sql dump
# Or via SSH:
mysql -u cpanel_dbuser -p cpanel_dbname < heavenlymatch_dump.sql
```

### Run migrations (fresh server with empty database):

```bash
php artisan migrate --force
```

### Seed required data (first deploy only):

```bash
php artisan db:seed --force
```

---

## 8. Storage Setup

### Create storage symlink (Option A — SSH):

```bash
cd ~/laravel
php artisan storage:link
# Creates: public/storage → storage/app/public
```

### Manual symlink (Option B — if artisan can't write to public/):

```bash
ln -s ~/laravel/storage/app/public ~/public_html/storage
```

### Set correct permissions:

```bash
chmod -R 755 ~/laravel/storage
chmod -R 755 ~/laravel/bootstrap/cache
```

> Private photos are stored in `storage/app/private/` — this directory is outside `public_html` and is never web-accessible. No extra configuration needed.

---

## 9. Cache & Optimize

Run these after every deployment:

```bash
cd ~/laravel

php artisan optimize:clear      # clears config/route/view/event caches

php artisan config:cache        # caches config (faster boot)
php artisan route:cache         # caches routes (all routes use classes, safe to cache)
php artisan view:cache          # pre-compiles Blade views
php artisan event:cache         # caches event listeners
```

---

## 10. Cron Job (Laravel Scheduler)

Add one cron entry in **cPanel → Cron Jobs**:

```
* * * * * /usr/local/bin/php /home/yourusername/laravel/artisan schedule:run >> /dev/null 2>&1
```

> Replace `yourusername` with your actual cPanel username.
> Replace `/usr/local/bin/php` with your server's PHP 8.2 path (check in cPanel → MultiPHP or Terminal: `which php`).

The scheduler runs:
- `ComputeMatchScoresJob` — nightly AI match score pre-computation

---

## 11. Queue Workers (Optional)

For `sync` queue (default), no worker is needed. Emails and notifications send inline.

To use the `database` queue (faster page responses):

```env
QUEUE_CONNECTION=database
```

Then add a second cron entry to process jobs every minute:

```
* * * * * /usr/local/bin/php /home/yourusername/laravel/artisan queue:work --once >> /dev/null 2>&1
```

---

## 12. Social Login Setup (Google & Facebook)

Social login is **optional**. Buttons are hidden automatically when credentials are missing or when disabled by admin.

### Google OAuth

1. Go to [Google Cloud Console](https://console.developers.google.com/) → Create a project → Enable "Google+ API"
2. Create OAuth 2.0 credentials (Web application)
3. Add Authorized redirect URI: `https://yourdomain.com/auth/google/redirect`
4. Add to production `.env`:
```env
GOOGLE_CLIENT_ID=your_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/redirect
```

### Facebook OAuth

1. Go to [Facebook Developers](https://developers.facebook.com/apps/) → Create App → Consumer
2. Add "Facebook Login" product → Settings
3. Add Valid OAuth Redirect URI: `https://yourdomain.com/auth/facebook/redirect`
4. Add to production `.env`:
```env
FACEBOOK_CLIENT_ID=your_app_id
FACEBOOK_CLIENT_SECRET=your_app_secret
FACEBOOK_REDIRECT_URI=https://yourdomain.com/auth/facebook/redirect
```

### Admin enable/disable

Go to **Admin Panel → Settings → Social Login** section to toggle Google and Facebook login on/off without touching `.env`.

A provider is shown only when:
- `.env` credentials are all present AND
- Admin has not disabled it in settings

### Common callback errors

| Error | Cause | Fix |
|-------|-------|-----|
| `redirect_uri_mismatch` | OAuth app's allowed URIs don't match your domain | Add exact URL to allowed redirect URIs in Google/Facebook console |
| `invalid_client` | Wrong client_id / client_secret | Re-copy credentials from console |
| Blank page after callback | `APP_DEBUG=false` hiding error | Check `storage/logs/laravel.log` |
| Social login button missing | Credentials not set in `.env` | Set all three env keys (id, secret, redirect) |
| Button hidden after enable | Admin toggled it off | Admin Panel → Settings → Social Login |

---

## 13. SMTP Mail Setup

### Using cPanel email account:

| Setting          | Value                          |
|-----------------|-------------------------------|
| MAIL_HOST        | mail.yourdomain.com           |
| MAIL_PORT        | 465 (SSL) or 587 (TLS)        |
| MAIL_USERNAME    | noreply@yourdomain.com        |
| MAIL_ENCRYPTION  | ssl (port 465) or tls (587)   |

### Using Gmail (app password required):

| Setting          | Value                    |
|-----------------|--------------------------|
| MAIL_HOST        | smtp.gmail.com           |
| MAIL_PORT        | 587                      |
| MAIL_ENCRYPTION  | tls                      |
| MAIL_USERNAME    | your@gmail.com           |
| MAIL_PASSWORD    | 16-char app password     |

### Using Zoho Mail (free tier available):

| Setting          | Value               |
|-----------------|---------------------|
| MAIL_HOST        | smtp.zoho.com       |
| MAIL_PORT        | 465                 |
| MAIL_ENCRYPTION  | ssl                 |

---

## 14. PHP Version

Verify PHP 8.2+ is selected for the domain in **cPanel → MultiPHP Manager**.

Required PHP extensions (usually enabled by default on quality shared hosts):
- `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd` or `imagick`

---

## 15. Security Checklist

- [ ] `APP_DEBUG=false` in production `.env`
- [ ] `APP_KEY` is set (32-char base64 string)
- [ ] `.env` is blocked from web access (`.htaccess` already handles this)
- [ ] `storage/` is outside `public_html`
- [ ] `vendor/` is outside `public_html`
- [ ] `SESSION_SECURE_COOKIE=true` (HTTPS only)
- [ ] `SESSION_DOMAIN=yourdomain.com` set
- [ ] Admin account password changed from any seeded default
- [ ] HTTPS enabled on domain (cPanel → SSL/TLS → Let's Encrypt)
- [ ] Private disk photos served only through signed URLs (`/photo/{id}` route)

---

## 16. Post-Deployment Verification

```bash
# Check for any config errors
php artisan about

# Verify routes cached correctly
php artisan route:list | head -20

# Send a test email
php artisan tinker
# >>> Mail::raw('Test', fn($m) => $m->to('you@email.com')->subject('Test'));
```

Visit:
- `https://yourdomain.com` — homepage loads
- `https://yourdomain.com/login` — auth works
- `https://yourdomain.com/admin/login` — admin panel accessible

---

## 17. Common Errors & Fixes

| Error | Cause | Fix |
|-------|-------|-----|
| 500 on all pages | `.env` missing or `APP_KEY` blank | Run `php artisan key:generate` |
| 500 on all pages | `storage/` not writable | `chmod -R 755 storage bootstrap/cache` |
| White screen / blank | `APP_DEBUG=true` shows errors; `APP_DEBUG=false` hides them | Check `storage/logs/laravel.log` |
| "No application encryption key" | `APP_KEY=` blank | `php artisan key:generate` |
| Photos not serving | `storage:link` not run | Run `php artisan storage:link` or manual symlink |
| Emails not sending | Wrong SMTP credentials | Test with `php artisan tinker` + `Mail::raw(...)` |
| "Class not found" | Autoloader not updated | `composer dump-autoload --optimize` |
| Route cache error | Closure-based routes (none in this project) | N/A — all routes use controller classes |
| Session not persisting | `SESSION_DOMAIN` mismatch | Set `SESSION_DOMAIN=yourdomain.com` |
| CSRF mismatch | Cookie domain wrong | Check `SESSION_DOMAIN` and `SESSION_SECURE_COOKIE` |

---

## 18. Re-Deployment (Updates)

After pushing new code:

```bash
cd ~/laravel

git pull origin master

composer install --no-dev --optimize-autoloader

php artisan migrate --force          # only if migrations changed

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

> If you changed `.env` values, always run `php artisan config:cache` after.
