# Legacy Cleanup List

Files and routes identified as unused/legacy. Do NOT delete yet — confirm no active references
before removing. These are candidates for cleanup after Phase 1B stabilization.

## Blade Views (legacy, no active Inertia routes)

| File | Status | Notes |
|------|--------|-------|
| `resources/views/` (any `.blade.php` other than `app.blade.php`) | Legacy | Inertia uses `resources/views/app.blade.php` as root; all other Blade views are orphaned |

## Route Names (no longer registered / legacy names)

| Route Name | Status | Notes |
|-----------|--------|-------|
| `myhome` | UNUSED | Was old home dashboard — superseded by `dashboard` |
| `welcome` | UNUSED | Old marketing welcome page |
| `register.show` | UNUSED | Old Blade register form — now `register` (Inertia) |
| `email.verify.notice` | UNUSED | Now `verification.notice` |
| `biodata.create` | UNUSED | Old Blade controller route — now `biodata.wizard` |
| `inbox` | UNUSED | Now `inbox.index` |
| `upgrade` | UNUSED | Now `upgrade.plans` |
| `faq` | UNUSED | Not registered |
| `guide` | UNUSED | Not registered |
| `matches` | UNUSED | Now `matches.index` |
| `search` | UNUSED | Now `search.index` |
| `sent` | UNUSED | Now `interests.sent` |
| `/interests` (bare) | FIXED | Updated to `/interests/received` in AppLayout nav |
| `/home` | FIXED | `RouteServiceProvider::HOME` updated to `/dashboard` |

## Controllers (orphaned / legacy)

| File | Status | Notes |
|------|--------|-------|
| `app/Http/Controllers/BiodataController.php` | ORPHANED | Old Blade-based biodata controller; no active routes. Do NOT modify or delete until confirmed safe. |
| `app/Http/Controllers/Auth/CustomLoginController.php` | REVIEW | Check if this is still imported anywhere; may be a leftover from early auth refactor |

## Database Artifacts

| Item | Status | Notes |
|------|--------|-------|
| `2014_10_12_000000_create_users_table.php` | UNUSED | Default Laravel stub — `users` table is not used; `registrations` table is the auth model |
| `database/seeders/DatabaseSeeder.php` → `DummyMatrimonySeeder` call | REVIEW | Remove `DummyMatrimonySeeder` from production seeding; keep for local dev only |

## Accidental Files in Project Root

| File | Status | Notes |
|------|--------|-------|
| `get()` | GARBAGE | Looks like a mistyped file — verify and delete |
| `join('registrations'` | GARBAGE | Looks like a mistyped file — verify and delete |

---

*Last updated: 2026-05-22. Review this list before any production deployment.*
