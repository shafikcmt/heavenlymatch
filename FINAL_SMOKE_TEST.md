# HeavenlyMatch — Final Production Smoke Test Checklist

**Environment:** cPanel shared hosting  
**Build verified:** `npm run build` → clean (2412 modules, 17.66 s)  
**Migrations:** 19/19 Ran  
**Routes:** All confirmed via `php artisan route:list`

Legend: `[ ]` = not tested · `[x]` = passed · `[!]` = failed / blocker

---

## 1. Pre-Deployment (Local / Staging)

### 1.1 Environment Hardening
- [ ] `APP_ENV=production` in `.env`
- [ ] `APP_DEBUG=false` in `.env`
- [ ] `APP_URL` set to live HTTPS domain
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `MAIL_*` credentials verified (SMTP host, port, user, pass, from)
- [ ] `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` set to production OAuth app
- [ ] `FACEBOOK_CLIENT_ID`, `FACEBOOK_CLIENT_SECRET` set to production OAuth app
- [ ] Social OAuth redirect URIs updated to `https://yourdomain.com/auth/google/callback` etc.
- [ ] `DB_*` credentials point to production database
- [ ] `.env` not tracked in git (`git status` confirms untracked)

### 1.2 Build Assets
- [ ] `npm run build` completes without errors
- [ ] `public/build/manifest.json` exists and non-empty
- [ ] `public/build/assets/app-*.js` and `app-*.css` present

### 1.3 Laravel Cache
```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```
- [ ] All commands exit without error
- [ ] `storage/app/public` symlink present in `public/`

### 1.4 File Permissions (cPanel)
- [ ] `storage/` writable (755 dirs, 644 files)
- [ ] `bootstrap/cache/` writable
- [ ] `public/build/` readable

---

## 2. Public Pages

| Route | Expected | Result |
|---|---|---|
| `GET /` | Homepage loads, hero + features visible | `[ ]` |
| `GET /login` | Login form renders | `[ ]` |
| `GET /register` | Registration form renders | `[ ]` |
| `GET /pricing` | Membership plans table visible | `[ ]` |
| `GET /how-it-works` | How-it-works page renders | `[ ]` |
| `GET /privacy` | Privacy policy renders | `[ ]` |
| `GET /terms` | Terms of service renders | `[ ]` |
| `GET /contact` | Contact form renders | `[ ]` |
| `GET /about` | About page renders | `[ ]` |
| `GET /robots.txt` | Returns plain text robots rules | `[ ]` |
| `GET /sitemap.xml` | Returns valid XML sitemap | `[ ]` |

---

## 3. Auth Flows

### 3.1 Registration
- [ ] `GET /register` — form renders (name, email, gender, password)
- [ ] Submit valid data → redirects to `/verify-email`
- [ ] Verification email received with valid signed link
- [ ] Clicking link → email verified, redirect to `/biodata/wizard`
- [ ] Submitting duplicate email → validation error shown

### 3.2 Login
- [ ] `GET /login` — form renders
- [ ] Valid credentials → redirect to `/dashboard`
- [ ] Invalid credentials → error message shown
- [ ] Banned account → "banned" message shown (no dashboard access)
- [ ] Suspended account → "suspended" message shown

### 3.3 Logout
- [ ] POST `/logout` → session cleared, redirect to `/`

### 3.4 Password Reset
- [ ] `GET /forgot-password` — form renders
- [ ] Submit email → reset email received
- [ ] `GET /reset-password/{token}` — new password form renders
- [ ] Submit new password → login works with new password

### 3.5 Social Login
- [ ] Google/Facebook buttons visible on `/login` (when enabled in admin settings)
- [ ] `GET /auth/google/redirect` — redirects to Google OAuth
- [ ] `GET /auth/google/callback` — route exists (confirmed in route:list)
- [ ] `GET /auth/facebook/redirect` — redirects to Facebook OAuth
- [ ] `GET /auth/facebook/callback` — route exists (confirmed in route:list)
- [ ] Successful Google OAuth → user created/logged in, redirect to dashboard
- [ ] Social login disabled in admin → buttons hidden on `/login`

---

## 4. User Dashboard

| Route | Expected | Result |
|---|---|---|
| `GET /dashboard` | Dashboard with checklist/stats renders | `[ ]` |
| `GET /biodata/wizard` | Wizard step 1 renders | `[ ]` |
| `GET /biodata/wizard/2` | Wizard step 2 renders | `[ ]` |
| `GET /dashboard/profile` | Own profile card renders | `[ ]` |
| `GET /search` | Search page with filters renders | `[ ]` |
| `GET /matches` | Algorithmic matches list renders | `[ ]` |
| `GET /interests/received` | Received interests list renders | `[ ]` |
| `GET /interests/sent` | Sent interests list renders | `[ ]` |
| `GET /inbox` | Inbox thread list renders | `[ ]` |
| `GET /shortlist` | Shortlisted profiles renders | `[ ]` |
| `GET /notifications` | Notifications list renders | `[ ]` |
| `GET /upgrade` | Membership plans page renders | `[ ]` |
| `GET /settings` | Settings page renders | `[ ]` |
| `GET /verify/identity` | Identity verification checklist renders | `[ ]` |
| `GET /profile/{id}` | Public profile view renders | `[ ]` |

### 4.1 Biodata Wizard
- [ ] All steps 1–8 navigate forward/back
- [ ] Bangladesh division → district → upazila cascade works
- [ ] Final step submit → biodata saved, pending admin approval
- [ ] Dashboard shows "biodata pending approval" status

### 4.2 Profile & Photos
- [ ] `GET /profile/photos` — photo upload page renders
- [ ] Upload photo → preview shows before save
- [ ] Photo visibility toggle (public / connections only) works
- [ ] Private photo served via `/photo/{id}/{index}` (not raw storage URL)

### 4.3 Interest Flow
- [ ] Send interest → recipient gets notification
- [ ] Accept interest → conversation created, accessible in `/inbox`
- [ ] Withdraw interest → removed from sent list

### 4.4 Inbox / Messaging
- [ ] `/inbox` lists conversations
- [ ] `/inbox/{conversationId}` shows messages
- [ ] Send message → appears in thread
- [ ] Unread badge updates in AppLayout nav

### 4.5 Shortlist
- [ ] Toggle shortlist on profile card → adds/removes from `/shortlist`

### 4.6 Notifications
- [ ] Notifications appear in `/notifications`
- [ ] Unread badge in nav clears after mark-all-read
- [ ] Notification action links point to correct pages

### 4.7 Settings
- [ ] Update display name / language → saved, flash shown
- [ ] Change password → new password works on next login
- [ ] Preferred language switch (EN/BN) → UI relabels

---

## 5. Admin Panel

| Route | Expected | Result |
|---|---|---|
| `GET /admin/login` | Admin login form renders | `[ ]` |
| `GET /admin` | Admin dashboard renders (admin session required) | `[ ]` |
| `GET /admin/biodatas` | Biodata approval list renders | `[ ]` |
| `GET /admin/biodatas/{id}` | Biodata detail renders | `[ ]` |
| `GET /admin/payments` | Payment approval list renders | `[ ]` |
| `GET /admin/reports` | Reports list renders | `[ ]` |
| `GET /admin/users` | User management list renders | `[ ]` |
| `GET /admin/settings` | Admin settings page renders | `[ ]` |

### 5.1 Admin Biodata Approval
- [ ] Approve biodata → user status changes to "approved"
- [ ] Reject biodata → user notified
- [ ] Approved user appears in search results

### 5.2 Admin Payment Approval
- [ ] Payment screenshot viewable via `/admin/payments/screenshot/{id}` (admin auth required)
- [ ] Approve payment → user membership activated
- [ ] Reject payment → user notified

### 5.3 Admin Social Login Settings
- [ ] `GET /admin/settings` — Google/Facebook toggle visible
- [ ] Disable Google → Google button disappears from `/login`
- [ ] Enable Google → Google button appears on `/login`

### 5.4 Admin User Management
- [ ] Ban user → user cannot log in (banned message shown)
- [ ] Unban user → user can log in again
- [ ] Suspend user → user cannot log in
- [ ] Verify user → verification badge shown on profile

---

## 6. Core End-to-End Flow

Run this sequence top to bottom. Each step depends on the previous.

- [ ] **Register** — new user with valid email
- [ ] **Verify email** — click link in email, redirected to wizard
- [ ] **Complete biodata** — fill all required wizard steps, submit
- [ ] **Admin approves biodata** — login to `/admin/biodatas`, approve
- [ ] **Search** — approved user appears in `/search` results
- [ ] **View profile** — click profile card, `/profile/{id}` renders
- [ ] **Send interest** — interest sent, recipient gets notification
- [ ] **Accept interest** — recipient accepts, conversation created
- [ ] **Message** — both users exchange messages in `/inbox`
- [ ] **Photo access request** — request sent, owner approves, photos visible
- [ ] **Membership payment** — user submits manual payment with screenshot
- [ ] **Admin approves payment** — membership activated
- [ ] **Premium features** — premium badge visible, premium-only features unlock

---

## 7. Security Checks

- [ ] `GET /.env` → returns **403** or **404** (never 200)
- [ ] `GET /storage/app/private/payments/...` → returns **403** (private disk)
- [ ] Private photos not accessible via raw `/storage/` URL (only via `/photo/{id}` with auth)
- [ ] `APP_DEBUG=false` confirmed — no stack traces on error pages
- [ ] `/admin` without admin session → redirects to `/admin/login`
- [ ] `/dashboard` without auth → redirects to `/login`
- [ ] CSRF token required on all POST/PUT/DELETE forms (Inertia default — verify no CSRF errors)
- [ ] HTTPS enforced — `http://` redirects to `https://`
- [ ] `HSTS` header present (check via browser DevTools → Network → Response Headers)
- [ ] Payment screenshot only accessible via `admin.payments.screenshot` route (admin auth)

---

## 8. Shared Hosting / cPanel Checks

### 8.1 Deployment Checklist
- [ ] `.htaccess` present in `public/` (Laravel default — enables URL rewriting)
- [ ] `public/` set as document root in cPanel
- [ ] `php artisan migrate --force` run on production DB
- [ ] `php artisan config:cache` run after setting `.env`
- [ ] `php artisan route:cache` run (confirms no closure-based routes — PublicPageController used)
- [ ] `php artisan view:cache` run
- [ ] `php artisan storage:link` run (`public/storage` symlink)

### 8.2 Asset Loading
- [ ] Homepage CSS loads (no 404 on `/build/assets/app-*.css`)
- [ ] Homepage JS loads (no 404 on `/build/assets/app-*.js`)
- [ ] Images load from `/storage/` (symlink working)

### 8.3 Email (SMTP)
- [ ] Registration → verification email delivered to inbox (not spam)
- [ ] Password reset → email delivered
- [ ] Check `MAIL_MAILER=smtp` and correct host/port in `.env`

### 8.4 File Writes
- [ ] Photo upload → file saved to `storage/app/private/photos/`
- [ ] Payment screenshot upload → file saved to `storage/app/private/payments/`
- [ ] Session writes working (no session errors)
- [ ] Log writes working (`storage/logs/laravel.log` writable)

### 8.5 Scheduler / Cron (note — manual setup required)
- Add this line in cPanel Cron Jobs:
  ```
  * * * * * php /home/{username}/yourdomain.com/artisan schedule:run >> /dev/null 2>&1
  ```
- [ ] Cron job added in cPanel
- [ ] Scheduler runs without error (check `storage/logs/laravel.log`)

---

## 9. Post-Deploy Final Checks

- [ ] No 500 errors in `storage/logs/laravel.log`
- [ ] No JavaScript console errors on homepage
- [ ] Language switcher EN ↔ BN works on public pages
- [ ] Language switcher works on dashboard pages
- [ ] Mobile responsive — homepage, login, dashboard render on 375px viewport
- [ ] Admin can upload homepage marketing images via `/admin/settings`
- [ ] Featured profile slider visible on homepage (if profiles exist)

---

## Summary

| Area | Status |
|---|---|
| Core MVP | Complete |
| Full QA | Clean |
| Social Login QA | Complete |
| Homepage QA | Complete |
| Build | `npm run build` ✓ clean |
| Migrations | 19/19 Ran |
| Routes | All confirmed |
| **Production smoke test** | **Pending live deploy** |

---

*Generated: 2026-05-25 — HeavenlyMatch Stage 2 Production Smoke Test*
