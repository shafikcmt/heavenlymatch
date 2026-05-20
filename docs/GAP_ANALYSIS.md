# HeavenlyMatch — Phase 1: Gap Analysis Report

## 1. Existing Architecture Summary

| Layer | Technology | Status |
|---|---|---|
| Backend Framework | Laravel 9 (PHP 8.0) | Functional, needs upgrade to 11 |
| Frontend | Blade templates + Vite | No React/Next.js — full rebuild required |
| Database | MySQL + Eloquent ORM | Working, schema gaps below |
| Auth | Custom Registration model + Sanctum | Works, missing OAuth refresh tokens, 2FA |
| Queue | Not configured | Missing — no job processing |
| Cache | Not configured | Missing — heavy DB queries uncached |
| Search | No implementation | Search form has no backend handler |
| Real-time | Not implemented | No WebSockets / Pusher |
| File Storage | Local disk only | No S3/cloud storage |
| Testing | PHPUnit skeleton | 0% coverage |

---

## 2. Critical Bugs (Must Fix Before Migration)

| # | Bug | Severity | Location |
|---|---|---|---|
| 1 | Profile interactions (shortlist/interest) stored in SESSION only — lost on logout | CRITICAL | `ProfileInteractionController.php` |
| 2 | Welcome page search form has no backend route/controller | HIGH | `welcome.blade.php` + `routes/web.php` |
| 3 | No email notifications for interest, chat, payment events | HIGH | Missing service |
| 4 | Membership foreign key not constrained — orphan records possible | MEDIUM | Migration `000004` |
| 5 | `UserAttribute` model not related in `Biodata` model — no join | MEDIUM | `Biodata.php` |
| 6 | Payment gateway callbacks have no signature verification | HIGH | `PaymentController.php` |
| 7 | Admin routes have no rate limiting | HIGH | `routes/web.php` |

---

## 3. Security Audit Summary

| Risk | Description | OWASP Category |
|---|---|---|
| Mass Assignment | `Biodata::$fillable` has 128 fields — risky blanket assignment | A03 |
| No CSP headers | No Content-Security-Policy middleware configured | A05 |
| Session-only interactions | Interaction data destroyed if session expires | Data Integrity |
| Photo storage | Photos in `public/` disk — directly URL-accessible | A01 |
| No rate limiting on auth | Login/register endpoints unlimited retries | A07 |
| No audit log | No record of admin actions | A09 |
| Twilio secret in `.env` | Good, but `.env.example` exposes field names | A02 |

---

## 4. Missing Business Features vs Target Platform

| Feature | Current Status | Target | Priority |
|---|---|---|---|
| **Search system** | 0% — form renders but no backend | Full filtered search with 15+ criteria | P0 |
| **Database-persisted interests** | 0% — session only | `connection_requests` table | P0 |
| **Real-time chat** | 0% | WebSocket chat after mutual interest | P0 |
| **Guardian/Wali module** | 0% — `guardian_mobile` field only | Full guardian OTP, notifications | P1 |
| **AI matching engine** | 0% | Weighted 9-factor score, daily top-5 | P1 |
| **Photo privacy system** | 0% — groom_photo is plain path | Blur, signed URL, access requests | P1 |
| **NID/Passport verification** | 0% | Upload → admin review → verified badge | P1 |
| **Email notifications** | 0% | Interest, chat, payment, match emails | P1 |
| **Push notifications** | 0% | Web push + mobile push | P2 |
| **Profile boost** | 0% | Paid featured placement | P2 |
| **Pay-per-contact unlock** | 0% | One-time payment to see contact info | P2 |
| **Referral system** | 0% | Invite friends, earn credits | P3 |
| **Success stories** | 0% | Published couple testimonials | P3 |
| **CMS / Blog** | 0% | SEO-driven content marketing | P3 |
| **Saved searches** | 0% | Persist search filters | P2 |
| **Profile completeness meter** | 0% | Visual progress, fill-in prompts | P2 |
| **bKash/Nagad integration** | 0% | Local mobile payment | P1 |
| **Stripe integration** | 0% | NRB international payment | P1 |
| **2FA / TOTP** | 0% | Admin accounts mandatory | P1 |
| **GDPR data export/delete** | 0% | Legal requirement | P2 |

---

## 5. Performance Bottlenecks

1. **No caching** — `SystemSetting` has 10-min cache but all biodata queries are raw DB
2. **No indexes** on high-frequency search columns: `district`, `occupation`, `birth_date`
3. **Eager loading missing** — N+1 queries in profile listing views
4. **No pagination** on profile lists
5. **No CDN** for static assets or profile photos
6. **Synchronous email** — email sent inline in request cycle (blocks response)

---

## 6. Refactoring Priorities for Migration

### Keep (migrate to TypeScript equivalents):
- 10-step biodata field design (very comprehensive for BD context)
- Registration ID format (HM000001)
- Membership plan seeder data
- Admin approval workflow logic
- Payment transaction state machine

### Rebuild from scratch:
- All interaction logic (new DB tables)
- Authentication (JWT + refresh tokens)
- Search (new Prisma queries with proper indexes)
- Frontend (Next.js 15 App Router)
- File storage (S3 + signed URLs)
- Notification system (queue-based)
