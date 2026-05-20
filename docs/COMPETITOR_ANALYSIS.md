# Phase 3 — Competitor Analysis & HeavenlyMatch Feature Synthesis

## Competitor Analysis Matrix

### 1. Shaadi.com
| Category | Details |
|---|---|
| **Best Features** | Smart Match algorithm, Who Viewed, Contact requests with read receipts, Profile Highlight, Astrological match |
| **UX Strengths** | Polished card-based browse, mobile app is excellent, search filters are extensive |
| **Monetization** | Tiered subscriptions (3/6/12 months), Pay-per-contact, SMS packs, Profile Spotlight |
| **Trust** | Mobile verification mandatory, ID verification optional, human moderator review |
| **Matching** | Interest-based + algorithm-based daily suggestions |
| **Weakness** | Heavy Hindu-centric features, paid wall aggressive on free tier, Indian content heavy |
| **HeavenlyMatch Opportunity** | BD/Muslim-first experience, Halal mode, Guardian module — differentiation is clear |

### 2. Muslima.com (Cupid Media)
| Category | Details |
|---|---|
| **Best Features** | Muslim-specific filters (hijab, prayer, sect), Global Muslim reach, Extensive search, Translate messages |
| **UX Strengths** | Simple clean UI, works well on slow connections |
| **Monetization** | Premium subscription, free browsing limited |
| **Trust** | Profile approval, photo moderation |
| **Matching** | Basic mutual interest matching |
| **Weakness** | Outdated design (2010-era), no Bangladeshi-specific location data, no guardian module, no real-time chat |
| **HeavenlyMatch Opportunity** | BD-specific districts/upazilas, Bengali UI, family involvement, better UX |

### 3. BangladeshiMatrimony.com (BharatMatrimony)
| Category | Details |
|---|---|
| **Best Features** | BD-specific location drill-down, local profession lists, Bengali UI, Astro matching |
| **UX Strengths** | Mobile app widely used in BD, Bangla language support |
| **Monetization** | Plans priced in BDT, bKash accepted |
| **Trust** | ID verification, local support team |
| **Weakness** | Hindu-focused parent brand, no Islamic mode, generic UI, outdated matching |
| **HeavenlyMatch Opportunity** | Muslim-first, Ordeekdin-style privacy, guardian module |

### 4. Ordeekdin.com
| Category | Details |
|---|---|
| **Best Features** | Biodata-first (no photo-forward approach), guardian/wali system, photos blurred by default, request-to-view photo, Islamic pledge system, family values first |
| **UX Strengths** | Trust-focused design, religious community love it, strict modesty controls |
| **Monetization** | Subscription only, no pay-per-contact |
| **Trust** | Guardian verification, admin biodata review, photo access requests |
| **Matching** | Manual browse + basic filters |
| **Weakness** | No AI matching, no real-time features, basic UI, no mobile app, search is limited |
| **HeavenlyMatch Opportunity** | Ordeekdin's values + Shaadi's technology = winning combination |

### 5. Nikkah Forever
| Category | Details |
|---|---|
| **Best Features** | Islamic purpose clearly stated, guardian involvement prominent, success stories |
| **UX Strengths** | Clean minimalist design, focus on serious seekers |
| **Monetization** | Subscription tiers |
| **Trust** | Profile screening, email verification |
| **Weakness** | Small user base, no BD-specific features, limited search |
| **HeavenlyMatch Opportunity** | NF's Islamic credibility + BD/NRB user base |

### 6. Pure Matrimony
| Category | Details |
|---|---|
| **Best Features** | "Halal way to find a spouse", Islamic scholars advisory, wali system, Islamic-only profiles, no mixing |
| **UX Strengths** | Mission-focused, trusted by scholars |
| **Monetization** | Monthly subscription |
| **Trust** | Strict profile review, Islamic compliance focus |
| **Weakness** | Very restrictive (no casual browsing), small BD presence, limited tech features |
| **HeavenlyMatch Opportunity** | Adopt Islamic mode as a toggle — serve both markets |

---

## HeavenlyMatch Synthesis — Best-of-Breed Features

| Feature | Source | HeavenlyMatch Implementation |
|---|---|---|
| AI Weighted Match Score | Shaadi | 10-factor engine, 0-100 score ring, daily top-5 |
| Biodata-first approach | Ordeekdin | Platform mode toggle: Islamic mode defaults to biodata-first |
| Photo blur by default | Ordeekdin | `photoVisibility`: BLURRED in Islamic mode, per-photo control |
| Photo access request | Ordeekdin/Pure | `PhotoAccessRequest` table, owner approves/denies |
| Guardian/Wali module | Ordeekdin/NF | SMS to guardian on interest, OTP guardian verification |
| BD district/upazila drill-down | BangladeshiMatrimony | Full location hierarchy table |
| Bengali UI | BangladeshiMatrimony | next-intl i18n, bn/en toggle |
| Islamic filters (sect, prayer, beard, hijab) | Muslima/Ordeekdin | Full religious fields in Profile schema |
| Verified badges (Email/Phone/ID) | All | Multi-tier `VerificationRequest` workflow |
| Who Viewed My Profile | Shaadi | `ProfileView` table, premium feature gate |
| Profile Boost | Shaadi | `ProfileBoost` table + scheduler |
| bKash/Nagad | BangladeshiMatrimony | Direct BD mobile banking integration |
| Success Stories | All | `SuccessStory` model + SEO landing pages |
| Saved Search | Shaadi | `SavedSearch` model with alert emails |
| NRB section | Bangladeshi Matrimony | `residingCountry` with popular countries filter |
| Pay-per-contact | Shaadi | `ContactUnlock` table + payment flow |

---

## Subscription Feature Matrix

| Feature | Free | Silver | Gold | Diamond |
|---|---|---|---|---|
| Create profile | ✓ | ✓ | ✓ | ✓ |
| Browse profiles | Limited (10/day) | 50/day | Unlimited | Unlimited |
| Send interests | 3/day | 10/day | Unlimited | Unlimited |
| Receive interests | ✓ | ✓ | ✓ | ✓ |
| Send messages | ✗ | 10/day | 50/day | Unlimited |
| View contact info | ✗ | ✗ | 5/month | 20/month |
| Photo access request | 2/day | 5/day | 20/day | Unlimited |
| Advanced search filters | Basic only | + Sect, District | + Income, Occupation | All filters |
| Who viewed my profile | ✗ | ✗ | ✓ | ✓ |
| Profile boost | ✗ | 1/month | 3/month | 5/month |
| Priority placement | ✗ | ✗ | ✓ | Top placement |
| Verified badge priority | ✗ | ✗ | ✓ | ✓ |
| Daily AI matches | 3 | 5 | 10 | 20 |
| Save searches | 1 | 3 | 10 | Unlimited |

### Recommended Pricing (BDT)

| Plan | 1 Month | 3 Months | 6 Months | 12 Months |
|---|---|---|---|---|
| Silver | ৳799 | ৳1,999 (৳666/mo) | ৳3,499 (৳583/mo) | ৳5,999 (৳500/mo) |
| Gold | ৳1,499 | ৳3,999 (৳1,333/mo) | ৳6,999 (৳1,166/mo) | ৳11,999 (৳1,000/mo) |
| Diamond | ৳2,999 | ৳7,499 (৳2,500/mo) | ৳12,999 (৳2,166/mo) | ৳21,999 (৳1,833/mo) |

### International (USD)
| Plan | 1 Month | 3 Months |
|---|---|---|
| Silver | $9 | $22 |
| Gold | $16 | $42 |
| Diamond | $29 | $79 |

### Additional Revenue Streams
| Product | Price (BDT) | Notes |
|---|---|---|
| Contact Unlock (1) | ৳199 | One-time contact reveal |
| Contact Bundle (5) | ৳799 | 25% saving |
| Profile Boost (24h) | ৳299 | Top search placement |
| Profile Spotlight (7d) | ৳999 | Highlighted card across site |
| Premium Verification Fast-Track | ৳499 | 24h admin review SLA |
