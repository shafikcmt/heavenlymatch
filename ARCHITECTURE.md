# HeavenlyMatch — Architecture & Development Roadmap

## Tech Stack Decision

| Layer | Technology | Rationale |
|---|---|---|
| Frontend | Next.js 14 (App Router) | SEO via RSC, fast LCP, easy i18n (bn/en) |
| Backend | Laravel 11 (REST API) | Existing codebase, mature ecosystem |
| Database | MySQL 8 (primary) | Existing; add composite indexes |
| Cache | Redis | Session, queue, match score cache |
| Queue | Laravel Horizon (Redis) | Background jobs, notifications |
| Media | AWS S3 + Cloudinary | Private photos (S3) + transforms (Cloudinary) |
| WebSockets | Laravel Reverb / Pusher | Real-time chat |
| Search | MySQL full-text or Meilisearch | Advanced profile search |
| Payments | SSLCommerz + bKash + Nagad + Stripe | BD local + NRB international |
| Email | SendGrid | Transactional + marketing |
| SMS | Twilio / Vonage | OTP + guardian notifications |

---

## Directory Structure (after migration to Next.js frontend)

```
heavenlymatch/
├── api/                          ← Laravel 11 (renamed from root or subdir)
│   ├── app/
│   │   ├── Console/
│   │   │   └── Kernel.php        ← Scheduler
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Api/
│   │   │   │   │   ├── AuthController.php
│   │   │   │   │   ├── MatchController.php
│   │   │   │   │   ├── PhotoController.php
│   │   │   │   │   ├── ChatController.php
│   │   │   │   │   ├── GuardianController.php
│   │   │   │   │   ├── PaymentController.php
│   │   │   │   │   └── ProfileController.php
│   │   │   │   └── Admin/
│   │   │   ├── Middleware/
│   │   │   └── Resources/        ← API Resource transformers
│   │   ├── Jobs/
│   │   │   ├── ComputeMatchScoresJob.php
│   │   │   ├── SendDailyMatchEmailJob.php
│   │   │   └── ProcessNidVerificationJob.php
│   │   ├── Services/
│   │   │   ├── MatchingEngine.php
│   │   │   ├── PhotoPrivacyService.php
│   │   │   ├── PaymentGatewayService.php
│   │   │   └── GuardianNotificationService.php
│   │   └── Models/
│   ├── database/migrations/
│   └── routes/api.php
│
└── web/                          ← Next.js 14 App Router
    ├── app/
    │   ├── (public)/             ← No auth required
    │   │   ├── page.tsx          ← Landing page
    │   │   ├── about/
    │   │   └── profiles/[id]/    ← Public profile view (SEO)
    │   ├── (auth)/               ← Auth routes
    │   │   ├── login/
    │   │   ├── register/
    │   │   └── verify/
    │   └── (dashboard)/          ← Protected
    │       ├── layout.tsx
    │       ├── matches/
    │       ├── search/
    │       ├── inbox/
    │       ├── profile/
    │       │   ├── biodata/
    │       │   └── settings/
    │       ├── upgrade/
    │       └── admin/
    ├── components/
    │   ├── ui/                   ← shadcn/ui base components
    │   ├── profile/
    │   │   ├── ProfileCard.tsx
    │   │   ├── PrivatePhoto.tsx  ← Blur/reveal logic
    │   │   └── VerifiedBadge.tsx
    │   ├── match/
    │   │   ├── MatchScore.tsx
    │   │   └── MatchFilters.tsx
    │   ├── chat/
    │   │   └── ConversationView.tsx
    │   └── guardian/
    │       └── GuardianPanel.tsx
    ├── lib/
    │   ├── api.ts                ← Typed API client
    │   └── auth.ts               ← NextAuth / session
    └── hooks/
        ├── useMatches.ts
        └── usePhoto.ts
```

---

## Development Roadmap

### Phase 1 — Foundation (Weeks 1-3)
**Goal:** Hardened API, new schema live, working Next.js scaffold.

- [ ] Run new migrations on staging database
- [ ] Upgrade Laravel to 11, add `MatchScore`, `ConnectionRequest`, `Guardian`, `PhotoAccessRequest` models
- [ ] Bind `MatchingEngine` in `AppServiceProvider`
- [ ] Register API routes (`/api/matches/*`, `/api/photo/*`)
- [ ] Enable Sanctum SPA authentication for Next.js
- [ ] Scaffold Next.js 14 project in `/web` with shadcn/ui + Tailwind
- [ ] Build typed `ApiClient` class in Next.js with automatic token refresh
- [ ] Migrate landing page from Blade to Next.js (preserve SEO content)
- [ ] Add `completeness_score` computation on `BiodataObserver`

### Phase 2 — Core UX (Weeks 4-7)
**Goal:** Premium match browsing, secure photo system, search.

- [ ] Build `ProfileCard` component with match score ring, verified badge, blurred photo
- [ ] Implement `PrivatePhoto` component with token-based URL and CSS blur override
- [ ] Build Advanced Search page with sidebar filters (basic free / extended premium)
- [ ] Build Match Feed (`/matches`) with infinite scroll using React Query
- [ ] Daily Matches section on dashboard (5 AI picks with score breakdown)
- [ ] Shortlist / Favourites system
- [ ] Profile completeness wizard (auto-prompts missing fields)
- [ ] Photo upload: client-side preview → S3 via signed URL → admin review queue

### Phase 3 — Communication (Weeks 8-10)
**Goal:** Full secure chat + Guardian/Wali module.

- [ ] Connection request send/accept/decline flow
- [ ] Guardian OTP verification on signup
- [ ] Guardian SMS notification on connection request (Islamic mode)
- [ ] Guardian notification level settings panel
- [ ] Real-time chat using Laravel Reverb (WebSockets)
- [ ] Message delivery/read receipts
- [ ] Report & block functionality
- [ ] Photo access request flow (Islamic mode)

### Phase 4 — Monetisation (Weeks 11-13)
**Goal:** Complete payment system, profile boost, pay-per-contact.

- [ ] SSLCommerz integration (local BDT)
- [ ] bKash & Nagad mobile banking integration
- [ ] Stripe integration (NRB / international)
- [ ] Subscription management (Silver/Gold/Diamond with feature gates)
- [ ] Profile Boost purchase + `ExpireBoostsCommand`
- [ ] Pay-per-contact biodata unlock (contact info revealed after payment)
- [ ] Admin payment dashboard with refund handling
- [ ] Automated membership expiry notifications (7 days, 3 days, 1 day before)

### Phase 5 — Trust & Safety (Weeks 14-16)
**Goal:** NID verification pipeline, moderation, SEO.

- [ ] NID/Passport upload flow with admin review panel
- [ ] `ProcessNidVerificationJob` (optional: Bangladesh NID API if available)
- [ ] Verified badge display across the platform
- [ ] Anti-scraping: rate limiting on photo & search endpoints, CAPTCHA on auth
- [ ] Admin moderation queue (reports, abusive messages, suspicious profiles)
- [ ] Dynamic OG meta tags for public profiles (Next.js RSC)
- [ ] Sitemap generation for approved public profiles
- [ ] Google Analytics 4 + Hotjar integration
- [ ] Launch beta to 500 test users

### Phase 6 — Growth (Ongoing)
- [ ] Mobile app (React Native, sharing Next.js API layer)
- [ ] Email marketing sequences (SendGrid)
- [ ] WhatsApp Business notification channel
- [ ] A/B test matching weight tuning
- [ ] Partner with local Islamic centres for trust-building campaigns
- [ ] NRB community targeting (UK, USA, UAE, Australia, Malaysia)

---

## Database Schema Summary

### `registrations` (auth + account)
Key new fields: `platform_mode`, `photo_visibility`, `identity_verification_status`,
`is_boosted`, `boost_expires_at`, `two_factor_enabled`

### `biodatas` (full profile)
Key new fields: `division`, `district`, `upazila`, `residing_country`, `residing_city`,
`visa_status`, `religion`, `sect`, `is_practicing`, `height_cm`, `weight_kg`, `photos` (JSON),
`partner_age_min/max`, `partner_height_cm_min/max`, `partner_income_min/max`,
`partner_religion`, `partner_sect`, `diet`, `family_type`, `completeness_score`, `last_active_at`

### New tables
| Table | Purpose |
|---|---|
| `connection_requests` | Interest / connection with guardian flow |
| `conversations` | Secure chat sessions |
| `messages` | Individual chat messages |
| `guardians` | Guardian/Wali module |
| `shortlists` | Saved profiles |
| `profile_views` | View tracking |
| `photo_access_requests` | Islamic mode photo unlock |
| `match_scores` | Pre-computed AI scores (refreshed nightly) |
| `biodata_unlocks` | Pay-per-contact log |
| `user_notifications` | In-app notifications |
| `profile_boosts` | Boost purchases |

---

## Matching Algorithm — Scoring Weights

| Factor | Weight | Notes |
|---|---|---|
| Age compatibility | 18 | Range-based with mid-point bonus |
| Location | 16 | Country > Division > District |
| Religion & Sect | 15 | Hard-zero if religion mismatch with strict preference |
| Education | 12 | Hierarchy-based |
| Occupation | 10 | Category grouping |
| Height | 8 | Range-based |
| Lifestyle & Values | 8 | Prayer, diet, family type |
| Family background | 7 | Financial status, income range |
| Recent activity | 6 | Recency bonus to surface active users |

Tune weights via `MatchingEngine::WEIGHTS` constant.

---

## Photo Privacy Matrix

| `platform_mode` | `photo_visibility` | Has Connection | Has Photo Access Grant | Result |
|---|---|---|---|---|
| general | public | any | any | Clear |
| general | members_only | yes | any | Clear |
| general | members_only | no | any | Blurred |
| general | blurred | any | any | Always blurred |
| islamic | any | any | yes | Clear |
| islamic | any | any | no | Always blurred |

All photos served through `/api/photo/{id}` endpoint — never direct S3 URLs.
Short-lived HMAC tokens prevent URL scraping.

---

## Security Checklist

- [x] Photos served via signed proxy (no direct storage URLs)
- [x] Phone/email never revealed in chat until connection accepted
- [x] NID images stored in private S3 bucket (no public URL)
- [x] Rate limiting on auth, search, and photo endpoints
- [x] Guardian OTP verification before activating guardian role
- [x] HMAC photo tokens (15-minute expiry)
- [x] Watermark on delivered photos (registration ID + site name)
- [x] RBAC: user / premium_user / moderator / admin
- [ ] 2FA (TOTP) for admin accounts
- [ ] GDPR / PDPA compliant data deletion pipeline
- [ ] Penetration test before Phase 5 launch
