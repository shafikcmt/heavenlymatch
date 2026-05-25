# HeavenlyMatch — Shared Hosting Compatibility Audit

**Date:** 2026-05-25  
**Stack:** Laravel 11.36 + Inertia.js + React (TypeScript) + MySQL  
**Target:** cPanel / Apache / LiteSpeed shared hosting  

---

## Overall Status: ✅ PASS WITH WARNINGS

No critical blockers found. The application is fully deployable on standard cPanel shared hosting without Docker, Redis, Supervisor, WebSocket server, S3, or Node.js server. Two minor warnings require attention before going live (documented below).

---

## Pass / Fail Table

| # | Category | Check | Status | Notes |
|---|----------|-------|--------|-------|
| 1 | PHP | PHP 8.2+ required | ✅ PASS | `composer.json` requires `^8.2` |
| 2 | PHP | Required extensions all standard | ✅ PASS | See §Extensions below |
| 3 | PHP | `composer install --no-dev` works | ✅ PASS | No system-level daemon packages |
| 4 | PHP | No daemon dependencies | ✅ PASS | No Redis, no Pusher, no Supervisor |
| 5 | Routing | `route:list` produces clean list | ✅ PASS | 80+ routes, all class-based |
| 6 | Routing | `route:cache` safe | ✅ PASS | Zero closure routes, cache confirmed working |
| 7 | Routing | `.htaccess` Apache/LiteSpeed ready | ✅ PASS | Security headers, gzip, caching already set |
| 8 | Frontend | `npm run build` passes locally | ✅ PASS | 9.41s, 0 TypeScript errors |
| 9 | Frontend | `public/build/` committed | ✅ PASS | All assets pre-compiled |
| 10 | Frontend | No Node.js on server | ✅ PASS | Server only serves static `public/build/` files |
| 11 | Storage | Private photos not web-exposed | ✅ PASS | `storage/app/private/`, never symlinked |
| 12 | Storage | Payment screenshots protected | ✅ PASS | Served via signed route, not direct URL |
| 13 | Storage | `storage:link` documented | ✅ PASS | DEPLOYMENT.md §8 |
| 14 | Storage | Permissions documented | ✅ PASS | 755 on storage + bootstrap/cache |
| 15 | Queue | No Redis required | ✅ PASS | Default: `QUEUE_CONNECTION=sync` |
| 16 | Queue | No Supervisor required | ✅ PASS | `sync` processes inline |
| 17 | Queue | Scheduler cron documented | ✅ PASS | DEPLOYMENT.md §10 |
| 18 | Queue | `runInBackground()` removed from expire cmd | ✅ PASS | **Fixed in this audit** (proc_open risk) |
| 19 | Queue | `jobs` table migration for database mode | ⚠️ WARN | Run `queue:table` + migrate if switching to database |
| 20 | Cache | File cache driver | ✅ PASS | `CACHE_DRIVER=file` default |
| 21 | Session | File session driver | ✅ PASS | `SESSION_DRIVER=file` default |
| 22 | Session | `SESSION_DOMAIN` guidance in `.env.example` | ✅ PASS | Production section documented |
| 23 | Mail | SMTP supported | ✅ PASS | cPanel / Gmail / Zoho options documented |
| 24 | Mail | Email verification via SMTP | ✅ PASS | Uses Laravel's built-in MustVerifyEmail |
| 25 | Database | MySQL / MariaDB compatible | ✅ PASS | utf8mb4_unicode_ci, strict mode |
| 26 | Database | `migrate --force` clean | ✅ PASS | 19 migrations, all `Ran` |
| 27 | Database | Seeder production safety | ⚠️ WARN | `DummyMatrimonySeeder` is dev-only — see §Seeders |
| 28 | Security | `.env` blocked from web | ✅ PASS | `.htaccess` blocks `.env`, `*.log`, `composer.*` |
| 29 | Security | `APP_DEBUG=false` default | ✅ PASS | `config/app.php` defaults to `false` |
| 30 | Security | `APP_KEY` required | ✅ PASS | Blank in `.env.example`, keygen step documented |
| 31 | Security | HTTPS guidance | ✅ PASS | DEPLOYMENT.md §14 — Let's Encrypt via cPanel |
| 32 | Security | Admin routes protected | ✅ PASS | `admin` middleware on all `/admin/*` routes |
| 33 | Security | Upload validation | ✅ PASS | PhotoUploadController validates MIME + size |
| 34 | Security | Ignition debug routes in production | ✅ PASS | `require-dev` only — absent after `--no-dev` |
| 35 | cPanel | Laravel outside `public_html` | ✅ PASS | DEPLOYMENT.md §2 (symlink or manual copy) |
| 36 | cPanel | `public/index.php` path adjustment | ✅ PASS | DEPLOYMENT.md §2 Option B documents path rewrite |
| 37 | cPanel | Broadcasting off (no WebSocket) | ✅ PASS | `BROADCAST_DRIVER=null` for production |

---

## Critical Blockers

**None.** The application will run on standard cPanel shared hosting as-is.

---

## Non-Blocking Warnings

### ⚠️ W1 — `jobs` table missing for database queue

**Condition:** Only relevant if you change `QUEUE_CONNECTION=database` in production.  
**Impact:** `QUEUE_CONNECTION=sync` (default) works without any jobs table. No action needed unless you want async queue processing.  
**Fix if needed:**
```bash
php artisan queue:table
php artisan migrate
```
Then add a cron: `* * * * * php /home/user/laravel/artisan queue:work --once`

---

### ⚠️ W2 — `DummyMatrimonySeeder` must NOT run in production

**Condition:** `DatabaseSeeder::run()` calls both `DummyMatrimonySeeder` and `AdminUserSeeder`.  
**Impact:** Running `php artisan db:seed --force` in production would insert ~30 fake profiles with test data.  
**Fix:** In production, seed only the admin user:
```bash
php artisan db:seed --class=AdminUserSeeder --force
```
Do NOT run `php artisan db:seed` (without `--class`) in production.

---

### ⚠️ W3 — `twilio/sdk` installed but phone routes not registered

**Condition:** `twilio/sdk ^8.8` is in `composer.json` and `PhoneVerificationController` exists but no route is wired to it.  
**Impact:** The SDK installs during `composer install --no-dev` (~4 MB) but is never called. No runtime effect.  
**Action:** No action required for MVP. If phone verification is not planned, the package can be removed in a future cleanup to reduce vendor size.

---

### ⚠️ W4 — `BROADCAST_DRIVER=log` in dev `.env`

**Condition:** `config/broadcasting.php` reads `BROADCAST_DRIVER` and defaults to `null`. The dev `.env.example` sets `log`.  
**Impact:** In dev, broadcast events are logged. In production, if `BROADCAST_DRIVER` is not explicitly set to `null`, broadcast attempts log to `laravel.log` (harmless but noisy since no broadcasting UI exists).  
**Fix:** Ensure production `.env` includes:
```
BROADCAST_DRIVER=null
```
This is already in the production overrides section of `.env.example`.

---

## Server Requirements

### PHP Version
- **Minimum:** PHP 8.2
- **Recommended:** PHP 8.2 or 8.3
- **Set in:** cPanel → MultiPHP Manager

### Required PHP Extensions

| Extension | Purpose | Availability |
|-----------|---------|-------------|
| `pdo_mysql` | Database | Universal |
| `mbstring` | String encoding | Universal |
| `openssl` | Encryption, HTTPS | Universal |
| `tokenizer` | PHP parsing | Universal |
| `xml` | XML parsing | Universal |
| `ctype` | Character types | Universal |
| `json` | JSON encoding | Universal |
| `bcmath` | Membership pricing | Nearly universal |
| `fileinfo` | MIME type detection | Nearly universal |
| `gd` | Photo resizing (intervention/image v2) | Nearly universal |
| `curl` | External HTTP calls (mail, Twilio) | Universal |
| `zip` | Composer dependency extraction | Nearly universal |

**Verify in cPanel:** cPanel → PHP Info (check phpinfo page) or cPanel → PHP Extensions.  
**If `gd` is missing:** Photos can't be resized on upload. Contact host to enable GD or switch to `imagick`.

### Minimum Disk Space
- Application code: ~80 MB (with vendor/)
- `public/build/` assets: ~6 MB
- Growth: `storage/` grows with uploaded photos and logs

### PHP Settings (already configured in `.htaccess`)
- `upload_max_filesize = 10M`
- `memory_limit = 256M`
- `post_max_size = 12M`

---

## Required Writable Directories

Set these to `755` before launching:

```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

Specific paths that must be writable:
| Path | Purpose |
|------|---------|
| `storage/app/` | File uploads (public) |
| `storage/app/private/` | Photos + payment screenshots |
| `storage/framework/cache/` | File cache |
| `storage/framework/sessions/` | File sessions |
| `storage/framework/views/` | Compiled Blade views |
| `storage/logs/` | Application logs |
| `bootstrap/cache/` | Config/route/service caches |

---

## cPanel Setup Notes

### Directory structure
```
/home/username/
├── laravel/                ← clone or upload here
│   ├── public/             ← this becomes public_html
│   ├── storage/            ← NEVER inside public_html
│   ├── vendor/             ← NEVER inside public_html
│   └── .env                ← NEVER inside public_html
└── public_html/            ← symlink to ~/laravel/public/
```

### Symlink command (SSH / cPanel Terminal)
```bash
rm -rf ~/public_html
ln -s ~/laravel/public ~/public_html
```

### If symlinks are blocked (manual copy)
Copy all files from `laravel/public/` into `public_html/`, then edit `public_html/index.php`:
```php
// Change:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
// To:
require __DIR__.'/../../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../../laravel/bootstrap/app.php';
```

### Storage link
```bash
cd ~/laravel
php artisan storage:link
# Creates: public/storage → storage/app/public
```
If artisan can't write to `public/`:
```bash
ln -s ~/laravel/storage/app/public ~/public_html/storage
```

### Cron job (cPanel → Cron Jobs)
Add **one** entry:
```
* * * * * /usr/local/bin/php /home/username/laravel/artisan schedule:run >> /dev/null 2>&1
```
Replace `username` with your actual cPanel username.  
Find your PHP path: `which php` in Terminal, or check cPanel → MultiPHP CLI.

---

## Exact Live Server Commands (in order)

Run these after uploading files to the server:

```bash
cd ~/laravel

# 1. Install PHP dependencies (no dev packages)
composer install --no-dev --optimize-autoloader

# 2. Generate application key (first deploy only — NEVER on a live site with existing users)
php artisan key:generate

# 3. Run database migrations
php artisan migrate --force

# 4. Seed admin user ONLY (not DummyMatrimonySeeder)
php artisan db:seed --class=AdminUserSeeder --force

# 5. Create storage symlink
php artisan storage:link

# 6. Set permissions
chmod -R 755 storage bootstrap/cache

# 7. Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 8. Verify app is responding
php artisan about
```

---

## Re-Deployment Commands (subsequent deploys)

```bash
cd ~/laravel

git pull origin master

composer install --no-dev --optimize-autoloader

# Only run if new migrations exist
php artisan migrate --force

# Re-cache after every deploy
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Post-Deploy Test URL List

After deployment, verify each URL returns the expected response:

| URL | Expected | Tests |
|-----|----------|-------|
| `https://yourdomain.com/` | Homepage renders | Public page + SEO |
| `https://yourdomain.com/login` | Login form | Auth routes |
| `https://yourdomain.com/register` | Register form | Guest middleware |
| `https://yourdomain.com/forgot-password` | Password form | Mail setup |
| `https://yourdomain.com/pricing` | Plans page | Public route |
| `https://yourdomain.com/robots.txt` | Robots text | SEO routes |
| `https://yourdomain.com/sitemap.xml` | XML sitemap | SEO routes |
| `https://yourdomain.com/admin/login` | Admin login | Admin routes |
| `https://yourdomain.com/dashboard` | Redirect to login | Auth middleware |
| `https://yourdomain.com/.env` | 403/404 | .htaccess security |
| `https://yourdomain.com/storage/` | Symlink working | Storage link |

---

## Rollback Notes

If a deployment breaks the site:

### Quick rollback (git)
```bash
cd ~/laravel
git log --oneline -5          # find last working commit hash
git checkout <commit-hash>    # switch to that commit
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database rollback (if migrations ran)
```bash
php artisan migrate:rollback   # rolls back last batch
# Or to a specific batch:
php artisan migrate:rollback --step=1
```

### Emergency: disable site
```bash
php artisan down --message="Back soon" --retry=60
# Re-enable:
php artisan up
```

### If `.env` / key was regenerated accidentally (breaks existing sessions)
- All users will be logged out (sessions invalidated)
- No data is lost — sessions are re-created on next login
- If `APP_KEY` was changed: passwords and encrypted data remain intact (passwords use bcrypt, not the app key)

---

## Scheduler Jobs Reference

| Job | Schedule | Background | Notes |
|-----|----------|-----------|-------|
| `memberships:expire` | Daily 18:00 UTC (midnight BDT) | No | Marks expired memberships |
| `ComputeMatchScoresJob` | Daily 20:00 UTC (02:00 BDT) | No | Pre-computes AI match scores |
| `boosts:expire` | Every 30 min | No | Deactivates expired profile boosts |
| `notify:daily-matches` | Daily 03:00 UTC (09:00 BDT) | No | Sends daily match emails |
| `notify:reengagement` | Weekly Sun 04:00 UTC | No | Re-engagement emails |
| `messages:purge --days=90` | Weekly | No | Purges old soft-deleted messages |

All jobs use `->withoutOverlapping()`. None require Supervisor or Redis.

---

## What Does NOT Work on Shared Hosting (intentional)

| Feature | Status | Reason |
|---------|--------|--------|
| WebSocket / real-time | Not used | No Pusher/Echo — polling used for inbox instead |
| Google OAuth | Optional | Works if `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` are set |
| Phone (SMS) verification | Disabled | Route not registered; requires Twilio credentials |
| Automated payment gateway | Not used | Manual bKash/Nagad payment is the only method |
| S3 / cloud storage | Not needed | Photos stored on disk in `storage/app/private/` |
| Redis cache | Not needed | File cache is sufficient for this scale |
