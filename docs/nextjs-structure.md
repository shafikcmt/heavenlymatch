# HeavenlyMatch — Next.js 15 App Architecture

## Directory Structure

```
web/                                  ← Next.js 15 App (separate repo or monorepo pkg)
├── app/
│   ├── layout.tsx                    ← Root layout (font, theme, providers)
│   ├── globals.css
│   │
│   ├── (marketing)/                  ← Public SEO pages — no auth
│   │   ├── layout.tsx                ← Marketing header/footer
│   │   ├── page.tsx                  ← Landing page
│   │   ├── about/page.tsx
│   │   ├── pricing/page.tsx
│   │   ├── how-it-works/page.tsx
│   │   ├── success-stories/
│   │   │   ├── page.tsx
│   │   │   └── [slug]/page.tsx
│   │   ├── blog/
│   │   │   ├── page.tsx
│   │   │   └── [slug]/page.tsx
│   │   ├── profiles/                 ← Public profile previews (SEO)
│   │   │   └── [registrationId]/page.tsx
│   │   ├── contact/page.tsx
│   │   ├── privacy/page.tsx
│   │   └── terms/page.tsx
│   │
│   ├── (auth)/                       ← Authentication flows
│   │   ├── layout.tsx
│   │   ├── register/
│   │   │   ├── page.tsx              ← Step 1: Looking for / name / gender
│   │   │   ├── verify-email/page.tsx ← Step 2: OTP
│   │   │   ├── verify-phone/page.tsx ← Step 3: SMS OTP
│   │   │   └── password/page.tsx     ← Step 4: Set password
│   │   ├── login/page.tsx
│   │   ├── forgot-password/page.tsx
│   │   └── oauth/
│   │       └── callback/[provider]/route.ts
│   │
│   ├── (dashboard)/                  ← Protected: auth + email verified
│   │   ├── layout.tsx                ← Sidebar nav + top bar
│   │   ├── home/page.tsx             ← Dashboard home: daily matches + activity feed
│   │   ├── matches/
│   │   │   ├── page.tsx              ← AI match feed (infinite scroll)
│   │   │   └── daily/page.tsx        ← Today's 5 picks
│   │   ├── search/
│   │   │   ├── page.tsx              ← Advanced search + filter sidebar
│   │   │   └── saved/page.tsx        ← Saved searches
│   │   ├── profile/
│   │   │   ├── [registrationId]/page.tsx ← View any profile
│   │   │   └── edit/
│   │   │       ├── page.tsx          ← Edit overview
│   │   │       ├── step/[step]/page.tsx ← 10-step biodata wizard
│   │   │       └── photos/page.tsx   ← Photo management
│   │   ├── inbox/
│   │   │   ├── page.tsx              ← Conversation list
│   │   │   └── [conversationId]/page.tsx ← Chat view
│   │   ├── interests/
│   │   │   ├── received/page.tsx     ← Incoming interests
│   │   │   └── sent/page.tsx         ← Outgoing interests
│   │   ├── shortlist/page.tsx
│   │   ├── who-viewed/page.tsx       ← Premium feature
│   │   ├── upgrade/
│   │   │   ├── page.tsx              ← Pricing / plan selection
│   │   │   └── payment/page.tsx      ← Payment processing
│   │   ├── notifications/page.tsx
│   │   ├── settings/
│   │   │   ├── page.tsx              ← Account settings
│   │   │   ├── privacy/page.tsx      ← Photo/profile visibility
│   │   │   ├── guardian/page.tsx     ← Guardian/Wali setup
│   │   │   ├── security/page.tsx     ← Password, 2FA, devices
│   │   │   ├── notifications/page.tsx
│   │   │   └── delete-account/page.tsx
│   │   └── verification/
│   │       ├── page.tsx              ← Verification center
│   │       └── submit/page.tsx       ← NID/passport upload
│   │
│   └── (admin)/                      ← Admin panel
│       ├── layout.tsx                ← Admin sidebar
│       ├── dashboard/page.tsx
│       ├── users/
│       │   ├── page.tsx
│       │   └── [id]/page.tsx
│       ├── biodatas/
│       │   ├── pending/page.tsx      ← Approval queue
│       │   ├── featured/page.tsx
│       │   └── [id]/page.tsx
│       ├── verifications/
│       │   ├── pending/page.tsx
│       │   └── [id]/page.tsx
│       ├── reports/page.tsx
│       ├── payments/page.tsx
│       ├── plans/page.tsx
│       ├── settings/page.tsx
│       ├── blog/page.tsx
│       └── analytics/page.tsx
│
├── api/                              ← Next.js API routes (or use NestJS separately)
│   ├── auth/
│   │   ├── register/route.ts
│   │   ├── login/route.ts
│   │   ├── refresh/route.ts
│   │   ├── logout/route.ts
│   │   └── oauth/[provider]/route.ts
│   ├── users/
│   │   └── [id]/route.ts
│   ├── profiles/
│   │   ├── route.ts                  ← GET (search), POST (create)
│   │   ├── [id]/route.ts             ← GET, PATCH, DELETE
│   │   └── [id]/biodata/
│   │       └── [step]/route.ts       ← Partial biodata update
│   ├── matches/
│   │   ├── route.ts                  ← GET top matches
│   │   ├── search/route.ts           ← Filtered search
│   │   └── daily/route.ts
│   ├── interests/
│   │   ├── route.ts                  ← POST send interest
│   │   └── [id]/route.ts             ← PATCH accept/decline
│   ├── conversations/
│   │   ├── route.ts
│   │   └── [id]/
│   │       ├── route.ts
│   │       └── messages/route.ts
│   ├── photo/
│   │   ├── token/route.ts
│   │   ├── [profileUserId]/[photoIndex]/route.ts ← Proxy + privacy enforcement
│   │   ├── upload/route.ts           ← Generate S3 presigned upload URL
│   │   └── access/
│   │       ├── route.ts              ← POST request access
│   │       └── [id]/route.ts         ← PATCH respond (grant/deny)
│   ├── payments/
│   │   ├── intent/route.ts           ← Create payment intent
│   │   └── webhook/
│   │       ├── stripe/route.ts
│   │       ├── sslcommerz/route.ts
│   │       └── bkash/route.ts
│   ├── notifications/route.ts
│   ├── shortlists/route.ts
│   ├── reports/route.ts
│   └── admin/
│       ├── users/route.ts
│       ├── biodatas/[id]/route.ts
│       └── verifications/[id]/route.ts
│
├── components/
│   ├── ui/                           ← shadcn/ui base (Button, Input, Card, etc.)
│   ├── layout/
│   │   ├── DashboardSidebar.tsx
│   │   ├── TopBar.tsx
│   │   ├── MarketingHeader.tsx
│   │   └── Footer.tsx
│   ├── profile/
│   │   ├── ProfileCard.tsx           ← Card with match score, verified badge, blur
│   │   ├── ProfileCardSkeleton.tsx
│   │   ├── PrivatePhoto.tsx          ← Token-based photo with blur/watermark
│   │   ├── MatchScoreRing.tsx        ← Circular progress showing 0-100%
│   │   ├── VerifiedBadge.tsx
│   │   ├── ProfileDetailView.tsx     ← Full profile page
│   │   └── ProfileCompletenessBar.tsx
│   ├── biodata/
│   │   ├── BiodataWizard.tsx         ← 10-step form orchestrator
│   │   ├── steps/
│   │   │   ├── Step1GeneralInfo.tsx
│   │   │   ├── Step2Address.tsx
│   │   │   ├── Step3Education.tsx
│   │   │   ├── Step4Family.tsx
│   │   │   ├── Step5Personal.tsx
│   │   │   ├── Step6Occupation.tsx
│   │   │   ├── Step7Marriage.tsx
│   │   │   ├── Step8Partner.tsx
│   │   │   ├── Step9Pledge.tsx
│   │   │   └── Step10Contact.tsx
│   │   └── BiodataProgressBar.tsx
│   ├── search/
│   │   ├── SearchFilters.tsx         ← Sidebar with all filter controls
│   │   ├── SearchResults.tsx
│   │   ├── FilterChips.tsx           ← Active filter pills
│   │   └── SaveSearchModal.tsx
│   ├── match/
│   │   ├── MatchFeed.tsx             ← Infinite scroll match list
│   │   ├── DailyMatches.tsx          ← Today's 5 AI picks
│   │   └── ScoreBreakdownPopover.tsx ← Tooltip showing factor breakdown
│   ├── interest/
│   │   ├── SendInterestModal.tsx
│   │   ├── InterestCard.tsx
│   │   └── GuardianNoticeModal.tsx   ← Islamic mode: guardian will be notified
│   ├── chat/
│   │   ├── ConversationList.tsx
│   │   ├── MessageThread.tsx
│   │   ├── MessageBubble.tsx
│   │   └── ChatInput.tsx
│   ├── guardian/
│   │   ├── GuardianSetupForm.tsx
│   │   ├── GuardianPanel.tsx         ← Show guardian status on dashboard
│   │   └── GuardianOtpVerify.tsx
│   ├── payment/
│   │   ├── PlanCard.tsx
│   │   ├── PricingTable.tsx
│   │   ├── PaymentForm.tsx
│   │   └── GatewaySelector.tsx
│   ├── verification/
│   │   ├── VerificationStatus.tsx
│   │   ├── NidUploadForm.tsx
│   │   └── VerificationBadges.tsx
│   └── admin/
│       ├── ApprovalCard.tsx
│       ├── UserManagementTable.tsx
│       ├── PaymentReconciliation.tsx
│       └── ModeratorQueue.tsx
│
├── lib/
│   ├── prisma.ts                     ← Prisma client singleton
│   ├── auth.ts                       ← NextAuth / custom JWT helpers
│   ├── api-client.ts                 ← Typed fetch wrapper for API routes
│   ├── matching-engine.ts            ← (copied from docs/)
│   ├── photo-privacy.ts              ← (copied from docs/)
│   ├── payments/
│   │   ├── stripe.ts
│   │   ├── sslcommerz.ts
│   │   ├── bkash.ts
│   │   └── nagad.ts
│   ├── notifications/
│   │   ├── email.ts                  ← Resend integration
│   │   ├── sms.ts                    ← Twilio/Vonage
│   │   └── push.ts                   ← Web Push
│   └── validations/
│       ├── profile.ts                ← Zod schemas
│       ├── auth.ts
│       └── search.ts
│
├── hooks/
│   ├── useAuth.ts
│   ├── useProfilePhoto.ts
│   ├── useMatches.ts
│   ├── useSearch.ts
│   ├── useConversation.ts
│   └── useNotifications.ts
│
├── stores/
│   ├── useAuthStore.ts               ← Zustand: user session
│   ├── useSearchStore.ts             ← Zustand: search filters (URL-synced)
│   └── useUIStore.ts                 ← Zustand: modal states, sidebar
│
├── types/
│   ├── api.ts                        ← API response types
│   ├── profile.ts
│   └── match.ts
│
├── i18n/
│   ├── bn.json                       ← Bengali translations
│   └── en.json
│
├── middleware.ts                     ← Auth guard + i18n routing
├── next.config.ts
├── tailwind.config.ts
├── tsconfig.json
└── package.json
```

## Key Package Versions

```json
{
  "dependencies": {
    "next": "15.x",
    "react": "19.x",
    "typescript": "5.x",
    "@prisma/client": "5.x",
    "tailwindcss": "3.x",
    "@shadcn/ui": "latest",
    "framer-motion": "11.x",
    "zustand": "4.x",
    "@tanstack/react-query": "5.x",
    "react-hook-form": "7.x",
    "zod": "3.x",
    "next-auth": "5.x",
    "@aws-sdk/client-s3": "3.x",
    "@aws-sdk/s3-request-presigner": "3.x",
    "resend": "latest",
    "twilio": "5.x",
    "stripe": "15.x",
    "bullmq": "5.x",
    "ioredis": "5.x",
    "sharp": "0.33.x",
    "jose": "5.x"
  }
}
```
