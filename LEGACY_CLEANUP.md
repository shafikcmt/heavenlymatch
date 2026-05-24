# Legacy Cleanup — Status Report

_Last updated: 2026-05-24 (Stage 1 stabilization)_

---

## Controllers — DONE

All orphaned legacy controllers have been moved to `app/Http/Controllers/Legacy/`
with namespace updated and a LEGACY doc-block added. The original files were deleted.

| Original Location | New Location (Legacy/) | Superseded By | Safe to Delete |
|---|---|---|---|
| `Controllers/BiodataController.php` | `Legacy/BiodataController.php` | `Biodata\BiodataWizardController` | YES |
| `Controllers/PaymentController.php` | `Legacy/PaymentController.php` | `Payment\PaymentController` | YES |
| `Controllers/RegistrationController.php` | `Legacy/RegistrationController.php` | `Auth\RegisterController` | YES |
| `Controllers/EmailVerificationController.php` | `Legacy/EmailVerificationController.php` | `Auth\LoginController` (MustVerifyEmail) | YES |
| `Controllers/MembershipController.php` | `Legacy/MembershipController.php` | `Payment\PaymentController::plans()` | YES |
| `Controllers/ProfileInteractionController.php` | `Legacy/ProfileInteractionController.php` | `Dashboard\ShortlistController`, `Dashboard\InterestController` | YES |
| `Controllers/Auth/CustomLoginController.php` | `Legacy/Auth/CustomLoginController.php` | `Auth\LoginController` | YES |
| `Controllers/Admin/AdminAuthController.php` | `Legacy/Admin/AdminAuthController.php` | `Admin\AdminLoginController` | YES |

---

## Broken Route References — FIXED

| Old Route Name | Correct Route Name | Fixed In |
|---|---|---|
| `myhome` | `dashboard` | `Middleware/RedirectIfAuthenticatedUser.php` |
| `biodata.create` | `biodata.wizard` | Legacy controllers only (dead code) |
| `email.verify.notice` | `verification.notice` | Legacy controllers only (dead code) |
| `email.verify.code` | `verification.send` | Legacy Blade views only (dead code) |
| `payments.show/.success/.fail/.cancel` | `upgrade.status` / `upgrade.success` | Legacy controllers only (dead code) |
| `inbox` | `inbox.index` | Legacy controllers only (dead code) |

Active files with no broken routes: all checked ✓

---

## Blade Views — ORPHANED (not deleted yet)

These Blade views are not rendered by any active Inertia route.
They exist because the project started with Blade and migrated to Inertia.
Safe to delete from `resources/views/pages/user-dashboard/` and `resources/views/auth/`
(except `resources/views/app.blade.php` — the Inertia root, KEEP).

| Path | Status |
|---|---|
| `resources/views/pages/` (entire directory) | ORPHANED — old Blade dashboard UI |
| `resources/views/layouts/` | ORPHANED — old Blade layout files |
| `resources/views/components/` | ORPHANED — old Blade components |
| `resources/views/auth/` (except do NOT delete app.blade.php) | ORPHANED — old Blade auth pages |
| `resources/views/admin/` | ORPHANED — old Blade admin pages |
| `resources/views/app.blade.php` | **KEEP** — Inertia root template |

---

## Database Artifacts

| Item | Status |
|---|---|
| `2014_10_12_000000_create_users_table.php` | UNUSED — `users` table not in use; `registrations` is auth table. Keep migration (no harm), do not apply. |
| `password_resets` table | Uses `registrations` provider — OK. |

---

## Garbage Files — DELETED

| File | Action |
|---|---|
| `get()` (project root) | DELETED |
| `join('registrations'` (project root) | DELETED |

---

## Payment Status — CONFIRMED CORRECT

- `pending` + `external_transaction_id IS NULL` = awaiting user submission
- `pending` + `external_transaction_id NOT NULL` = submitted, awaiting admin review (`pendingReview()` scope)
- `paid` = admin approved, membership activated
- `failed` = admin rejected
- `refunded` / `cancelled` = gateway flow (future use)
- `submitted` status: DOES NOT EXIST in migration enum. Old root `PaymentController` (now in Legacy/) was the only user.

---

## Storage Security — FIXED

Payment screenshots are now stored in `private` disk (`storage/app/private/`).
Admin views screenshots via authenticated route: `GET /admin/payments/screenshot/{id}`.
Old `asset('storage/...')` URL replaced with `route('admin.payments.screenshot', $id)`.
