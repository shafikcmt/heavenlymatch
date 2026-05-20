# HeavenlyMatch — Deployment Guide

## Local XAMPP (development)

```bash
# 1. Clone and enter project
cd /xampp/htdocs/heavenlymatch   # Windows: D:\Projects\heavenlymatch

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Set up .env
cp .env.example .env
php artisan key:generate

# 4. Configure .env for XAMPP
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=heavenlymatch
# DB_USERNAME=root
# DB_PASSWORD=
# QUEUE_CONNECTION=sync
# FILESYSTEM_DISK=local
# CACHE_DRIVER=file

# 5. Run migrations
php artisan migrate:fresh --seed

# 6. Create storage symlink
php artisan storage:link

# 7. Publish Sanctum config
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# 8. Publish Intervention Image config (for GD driver)
php artisan vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravelRecent"

# 9. Cache config for faster boot
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## cPanel Shared Hosting (production)

### 1. Upload files

```bash
# On local machine — zip everything except node_modules and .git
zip -r heavenlymatch.zip . \
  --exclude "*.git*" \
  --exclude "node_modules/*" \
  --exclude "vendor/*" \
  --exclude "storage/app/*" \
  --exclude ".env"
```

Upload `heavenlymatch.zip` via cPanel File Manager to `~/heavenlymatch/`.
Extract, then move `public/` contents into `public_html/` (or set document root via cPanel → Domains).

### 2. Document root configuration

In cPanel → Domains → your domain → Document Root, set to:
```
/home/USERNAME/heavenlymatch/public
```

### 3. Install dependencies via cPanel Terminal

```bash
cd ~/heavenlymatch
composer install --no-dev --optimize-autoloader
```

### 4. Configure production .env

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cpanelusername_heavenlymatch
DB_USERNAME=cpanelusername_dbuser
DB_PASSWORD=your_db_password

QUEUE_CONNECTION=sync      # sync is safest on shared hosting
CACHE_DRIVER=file          # file cache works without Redis
SESSION_DRIVER=file

FILESYSTEM_DISK=local      # swap to s3 when ready for cloud storage

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="HeavenlyMatch"
```

### 5. Laravel setup commands

```bash
cd ~/heavenlymatch
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 6. cPanel Cron job (Laravel Scheduler)

In cPanel → Cron Jobs, add:

```
* * * * * /usr/local/bin/php /home/USERNAME/heavenlymatch/artisan schedule:run >> /dev/null 2>&1
```

This triggers the scheduler every minute. The scheduler itself ensures jobs (match score computation, boost expiry, notifications) run at the correct intervals defined in `app/Console/Kernel.php`.

### 7. File permissions

```bash
cd ~/heavenlymatch
chmod -R 755 storage bootstrap/cache
chmod -R 644 storage/logs
```

### 8. Storage driver migration to S3/R2 (when ready)

Update `.env`:
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=heavenlymatch-media
AWS_URL=https://your-bucket.s3.ap-southeast-1.amazonaws.com

# For Cloudflare R2:
AWS_ENDPOINT=https://ACCOUNT_ID.r2.cloudflarestorage.com
```

Then run:
```bash
php artisan config:cache
# PhotoPrivacyService::presignedUrl() will automatically use S3 temporary URLs
```

---

## Next.js frontend (web/)

```bash
cd web
cp .env.local.example .env.local

# Configure .env.local:
# NEXT_PUBLIC_API_URL=https://yourdomain.com/api
# DATABASE_URL=mysql://root@localhost:3306/heavenlymatch

npm install
npm run build
npm start          # or deploy to Vercel / Cloudflare Pages
```

---

## Post-deploy checklist

- [ ] `APP_KEY` is set (run `php artisan key:generate` if blank)  
- [ ] Database migrated (`php artisan migrate --force`)  
- [ ] Storage linked (`php artisan storage:link`)  
- [ ] Cron job added in cPanel  
- [ ] `.env` has `APP_DEBUG=false` in production  
- [ ] `config:cache` and `route:cache` run  
- [ ] `public/images/avatar-male.svg` and `avatar-female.svg` exist  
- [ ] Sanctum config published  
- [ ] Intervention Image config published (GD or Imagick driver)  
