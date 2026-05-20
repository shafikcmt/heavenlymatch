# HeavenlyMatch — Master Development Roadmap v2

## Team Structure (Recommended)

| Role | Count | Responsibility |
|---|---|---|
| Full-Stack Lead | 1 | Architecture, API, matching engine |
| Frontend Engineer | 1-2 | Next.js, UI components, mobile |
| Backend Engineer | 1 | Auth, payments, notifications |
| UI/UX Designer | 1 | Figma designs, component library |
| QA Engineer | 1 (part-time) | Test coverage, regression |
| DevOps | 1 (part-time) | CI/CD, infra, monitoring |
| Product Manager | 1 | Roadmap, user feedback |

---

## Estimated Budget

| Item | Monthly Cost (USD) |
|---|---|
| Vercel Pro (hosting) | $20 |
| PostgreSQL (Supabase/Neon) | $25-75 |
| Redis (Upstash) | $10-30 |
| AWS S3 (private photos, 100GB) | $3 |
| Cloudflare R2 alternative | $0-5 |
| Twilio SMS (1000 OTPs) | $15 |
| Resend email (10K/mo) | $0-20 |
| Sentry error monitoring | $0-26 |
| **Total infrastructure** | **~$73-$194/mo** |

Development estimate: 4-6 months (2-person team) to production MVP.

---

## Phase 1 — Foundation & Infrastructure (Weeks 1-3)

### Goals
Stand up the development environment, database, and authentication.

### Tasks
- [ ] Initialize Next.js 15 project with TypeScript strict mode
- [ ] Configure Prisma + PostgreSQL, run initial migration
- [ ] Seed location data (64 districts, 8 divisions, all upazilas)
- [ ] Seed subscription plans + popular countries
- [ ] Implement JWT auth (access token 15min + refresh token 7 days)
- [ ] Registration flow: 4-step wizard (info → email OTP → phone OTP → password)
- [ ] OAuth: Google, Facebook
- [ ] Middleware: auth guard, admin guard, i18n routing (bn/en)
- [ ] Docker compose dev environment
- [ ] GitHub Actions: lint → type-check → test → build

### Deliverables
- Working auth (register, login, logout, refresh)
- Prisma schema migrated
- CI/CD pipeline live
- Dev environment documented

---

## Phase 2 — Profile & Biodata (Weeks 4-7)

### Goals
Users can complete their full biodata and it appears on the platform.

### Tasks
- [ ] 10-step biodata wizard (React Hook Form + Zod per step)
- [ ] Auto-save progress (draft in localStorage + server)
- [ ] Profile completeness score computation (observer on save)
- [ ] Photo upload: client → presigned S3 URL → notify server → admin queue
- [ ] Photo management page (reorder, set primary, delete)
- [ ] Profile detail view with all biodata sections
- [ ] Dual mode toggle (General / Islamic) in settings
- [ ] Admin biodata approval queue
- [ ] Bengali field labels + i18n
- [ ] `BiodataObserver` computing `completenessScore` on every save

### Deliverables
- Full biodata form live
- Admin can approve/reject biodatas
- Photos uploaded to private S3

---

## Phase 3 — Privacy System (Weeks 5-6, parallel with Phase 2)

### Tasks
- [ ] `GET /api/photo/[userId]/[index]` proxy endpoint
- [ ] HMAC photo token issuance (`POST /api/photo/token`)
- [ ] `resolveVisibility()` — enforce all 5 rules in privacy matrix
- [ ] `PrivatePhoto` React component (blur + watermark + unlock CTA)
- [ ] Photo access request flow (Islamic mode)
- [ ] Per-photo visibility settings (Public / Members Only / Blurred)
- [ ] Profile visibility settings page

---

## Phase 4 — Search & Discovery (Weeks 8-10)

### Tasks
- [ ] Advanced search API (`GET /api/profiles?filters...`)
- [ ] 15+ filter parameters with Prisma query builder
- [ ] Premium filter gate (district/income/occupation for Gold+)
- [ ] Search results page with filter sidebar
- [ ] Active filter chips (remove individual filters)
- [ ] URL-synced filters (Zustand + searchParams)
- [ ] Save search feature
- [ ] Search analytics logging
- [ ] Full-text search on name/district (PostgreSQL `tsvector`)
- [ ] Infinite scroll with TanStack Query + cursor pagination

---

## Phase 5 — AI Matching Engine (Weeks 9-11)

### Tasks
- [ ] Port `MatchingEngine` TypeScript class to production
- [ ] `GET /api/matches` — serve from `match_scores` table
- [ ] `GET /api/matches/daily` — 5 daily picks with score breakdown
- [ ] `ComputeMatchScoresJob` BullMQ job
- [ ] BullMQ scheduler: nightly at 02:00 BDT
- [ ] Cold-start preference inference for new users
- [ ] Match score ring UI component (SVG circular progress)
- [ ] Score breakdown popover (hover/tap shows per-factor detail)
- [ ] Daily match email (Resend template)

---

## Phase 6 — Interests & Connections (Weeks 10-12)

### Tasks
- [ ] `POST /api/interests` send interest with optional message
- [ ] `PATCH /api/interests/[id]` accept / decline
- [ ] Interest expiry (auto-expire after 30 days, BullMQ job)
- [ ] Guardian notification on interest sent (Islamic mode)
- [ ] Guardian approve flow (guardian receives SMS with OTP link)
- [ ] Guardian setup page (`/settings/guardian`)
- [ ] Received interests page (card-based with accept/decline)
- [ ] Sent interests page with status badges
- [ ] Interest limit enforcement per subscription tier

---

## Phase 7 — Secure Messaging (Weeks 12-14)

### Tasks
- [ ] `Conversation` created only after interest accepted
- [ ] Real-time chat via WebSocket (Pusher Channels or Ably)
- [ ] Message send / receive with delivery status
- [ ] Read receipts
- [ ] Message rate limiting (tier-based)
- [ ] Report message
- [ ] Block user from chat
- [ ] Typing indicator
- [ ] No direct email/phone in messages (auto-detect + mask)
- [ ] Contact info only revealed after `ContactUnlock` paid event

---

## Phase 8 — Notifications (Weeks 11-13)

### Tasks
- [ ] In-app notification bell with unread count
- [ ] Email notifications via Resend (interest, accepted, message, payment)
- [ ] SMS via Twilio (OTP, guardian alerts, match reminders)
- [ ] Web Push Notifications (PWA, user opt-in)
- [ ] Notification preferences page (per-channel, per-type)
- [ ] Notification templates in Bengali + English
- [ ] BullMQ queues: `email-queue`, `sms-queue`, `push-queue`

---

## Phase 9 — Premium & Payments (Weeks 14-16)

### Tasks
- [ ] Pricing page (plan comparison table)
- [ ] Plan selection + gateway selection
- [ ] SSLCommerz integration (BDT)
- [ ] bKash Direct API integration
- [ ] Nagad integration
- [ ] Stripe integration (USD, for NRB users)
- [ ] Webhook handlers with signature verification
- [ ] Payment status state machine (pending → processing → completed)
- [ ] Membership activation on payment success
- [ ] Subscription expiry worker (BullMQ)
- [ ] Feature gate middleware (check subscription tier)
- [ ] Profile Boost purchase + activate
- [ ] Contact Unlock purchase flow
- [ ] Admin payment reconciliation dashboard
- [ ] Payment receipt email

---

## Phase 10 — Verification System (Weeks 15-17)

### Tasks
- [ ] Verification center UI (`/verification`)
- [ ] NID/Passport upload to private S3
- [ ] Selfie upload
- [ ] Admin verification review queue
- [ ] Approve → set `identityVerificationStatus: VERIFIED` → award badge
- [ ] Verified badge display (Email ✓, Phone ✓, ID ✓, Fully ✓)
- [ ] Premium fast-track verification purchase
- [ ] Verification rejection with reason

---

## Phase 11 — Reporting & Safety (Weeks 16-18)

### Tasks
- [ ] Report profile modal (reason + description)
- [ ] Block user (hide from all views, no messages)
- [ ] Admin moderation queue
- [ ] Auto-flag messages with phone/email patterns
- [ ] Rate limiting: auth (5/min), interest (20/hour), messages (per tier)
- [ ] DDOS protection via Cloudflare
- [ ] GDPR: data export + account deletion pipeline

---

## Phase 12 — Admin Dashboard (Weeks 14-18, parallel)

### Tasks
- [ ] Admin auth + RBAC (SuperAdmin / Admin / Moderator / Support / Finance)
- [ ] Dashboard analytics (KPIs: MAU, new users, conversions, revenue)
- [ ] User management (search, filter, block, make admin, view payments)
- [ ] Biodata approval queue (approve / reject with note / feature)
- [ ] Verification review queue
- [ ] Report handling
- [ ] Payment management + manual payment verification
- [ ] Subscription plans management
- [ ] System settings key/value editor
- [ ] Admin audit log viewer
- [ ] Bulk operations (bulk approve, bulk email)
- [ ] PostHog analytics integration

---

## Phase 13 — CMS & SEO (Weeks 17-19)

### Tasks
- [ ] Blog post CRUD in admin
- [ ] Success story submission + moderation
- [ ] SEO landing pages (Bangladeshi Matrimony, Muslim Matrimony, NRB)
- [ ] Dynamic OG meta for public profiles (Next.js RSC generateMetadata)
- [ ] JSON-LD structured data for profiles and blog posts
- [ ] Sitemap generation (`/sitemap.xml`)
- [ ] robots.txt
- [ ] Google Search Console integration

---

## Phase 14 — Launch Preparation (Weeks 19-20)

### Tasks
- [ ] Staging environment (Vercel preview → staging.heavenlymatch.com)
- [ ] E2E tests with Playwright (auth, biodata, interest, payment)
- [ ] Load testing with k6 (100 concurrent users)
- [ ] Security audit (OWASP checklist)
- [ ] Sentry error monitoring
- [ ] PostHog product analytics
- [ ] Beta launch to 500 invited users
- [ ] Feedback collection + critical fixes
- [ ] Full production deploy

---

## Phase 15 — Growth (Post-Launch)

### Tasks
- [ ] Mobile app (React Native) sharing API layer
- [ ] WhatsApp Business notification integration
- [ ] NRB targeting campaigns (UK, UAE, USA, Australia, Malaysia, Canada)
- [ ] Partnership with Islamic centers and mosques
- [ ] Referral program with credit system
- [ ] A/B testing matching weight configurations
- [ ] Elasticsearch migration for full-text search at scale

---

## Priority Matrix

| Priority | Feature | Impact | Effort |
|---|---|---|---|
| P0 | Auth + Profile + Biodata | Critical (no platform without it) | Medium |
| P0 | Search | Critical (core value) | Medium |
| P0 | Interests + basic chat | Critical (core value) | Medium |
| P0 | Photo privacy system | Differentiator | Low |
| P1 | AI Matching | Differentiator | Medium |
| P1 | Payments (SSLCommerz + bKash) | Revenue | High |
| P1 | Guardian module | Differentiator | Medium |
| P1 | NID Verification | Trust | Medium |
| P1 | Admin dashboard | Operations | Medium |
| P2 | Stripe (NRB) | Revenue expansion | Low |
| P2 | Real-time chat | Engagement | High |
| P2 | Success stories | Trust/SEO | Low |
| P3 | Blog/CMS | SEO | Low |
| P3 | Mobile app | Scale | Very High |

---

## MVP Definition (8 weeks to soft launch)

The minimum viable product for a real user launch:

1. ✅ Register + email/phone verify
2. ✅ Complete 10-step biodata
3. ✅ Photo upload with privacy controls
4. ✅ Browse and search profiles (basic filters)
5. ✅ Send/accept interests
6. ✅ Basic messaging (post-acceptance)
7. ✅ SSLCommerz + bKash payment for one plan
8. ✅ Admin approval workflow
9. ✅ Guardian module (Islamic mode)
10. ✅ Mobile-responsive UI

Everything else (AI matching, Stripe, blog, referrals) is post-MVP.
