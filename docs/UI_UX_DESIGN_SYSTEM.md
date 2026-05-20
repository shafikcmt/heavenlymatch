# HeavenlyMatch — UI/UX Design System & Page Specifications

## Design Principles

| Principle | Implementation |
|---|---|
| **Premium Trust** | Clean whitespace, verified badges, real photo counts, success stats |
| **Cultural Sensitivity** | Arabic calligraphy accents, warm Islamic color palette option, Bengali typography |
| **Mobile-First** | All layouts designed at 375px first, scaled up |
| **Conversion-Optimized** | CTAs above fold, social proof numbers, urgency for premium features |
| **Privacy-Visible** | Show lock icons and privacy indicators prominently — trust signal |

---

## Color Palette

### General Mode
```
Primary:    #1B4FD8  (Deep Royal Blue — trust, stability)
Secondary:  #10B981  (Emerald Green — growth, success)
Accent:     #F59E0B  (Amber Gold — premium, warmth)
Background: #F8FAFC  (Slate 50 — clean)
Surface:    #FFFFFF
Text:       #0F172A  (Slate 900)
Muted:      #64748B  (Slate 500)
Border:     #E2E8F0  (Slate 200)
```

### Islamic Mode (warm/modest variant)
```
Primary:    #2D6A4F  (Deep Forest Green — Islamic tradition)
Secondary:  #B8860B  (Dark Goldenrod — classic Islamic art)
Accent:     #8B4513  (Saddle Brown — warm earth)
Background: #FAFAF5  (Ivory — modesty, purity)
Surface:    #FFFFFF
```

---

## Typography

```
Headings (Latin):  Inter Variable (weights: 400, 500, 600, 700, 800)
Headings (Bengali): Hind Siliguri (weights: 400, 500, 600, 700)
Body:              Inter 400/500
Mono:              JetBrains Mono (registration IDs, codes)
```

---

## Component Specifications

### ProfileCard
```
Width:       280px (mobile) / 320px (desktop) / responsive grid
Aspect:      3:4 photo area
Layers:
  1. Photo (blurred or clear based on privacy)
  2. Gradient overlay (bottom 40% — text legibility)
  3. Match score ring (top-right: 56px circle, stroke-dasharray SVG)
  4. Content area:
     - Name + Age + Verified badge
     - District, Country (with flag emoji for NRB)
     - Occupation + Education chip
     - Last active indicator (green dot if < 1hr)
  5. Action row: Shortlist ♡ | Interest → | View Profile
  6. Premium badge if Diamond member
  7. Boost indicator (flame icon + "Featured" label)
```

### Match Score Ring
```
SVG circle: 56×56px
Stroke:     6px
Background: slate-200
Fill:       gradient from #10B981 (green) to #1B4FD8 (blue)
Threshold colors:
  0-40:   #EF4444 (red)
  41-65:  #F59E0B (amber)
  66-80:  #10B981 (green)
  81-100: #1B4FD8 (blue, premium glow)
Center text: "87%" (font-bold, 12px)
```

### Verified Badge
```
Sizes: sm (16px) | md (20px) | lg (24px)
Variants:
  email:  blue check circle
  phone:  green phone circle
  id:     gold shield check  ← most prestigious
  full:   rainbow gradient shield  ← "Fully Verified"
Tooltip: "Identity Verified by HeavenlyMatch"
```

---

## Page Designs

### Landing Page (/)
```
Section 1: Hero
  - Full-width video/image background (tasteful, couple-neutral, nature/architecture)
  - H1: "Find Your Perfect Match" / বাংলায়: "আপনার জীবনসঙ্গী খুঁজুন"
  - H2: "The most trusted halal matrimony platform for Bangladeshis worldwide"
  - Quick Search bar: [Looking for ▼] [Age Min-Max] [Division ▼] [Search →]
  - Trust bar: "50,000+ Profiles | 5,000+ Successful Matches | 100% Verified"
  - CTA: [Get Started Free] [Browse Profiles]
  - Language toggle: EN | বাংলা (top right)

Section 2: Mode Select
  - Two large cards side by side:
    LEFT: "General Matrimony" — Icon: ring+heart — "Browse freely, modern approach"
    RIGHT: "Islamic / Halal Mode" ★ Popular — Icon: mosque — "Guardian involved, photos private, biodata-first"
  - Clicking selects mode before registration

Section 3: How It Works
  - 4-step illustration: Create → Verify → Connect → Succeed

Section 4: Featured Profiles
  - 4-column grid of approved, featured profiles (blurred photos for non-logged-in)
  - "Sign up to see full profiles" overlay on hover

Section 5: Trust Signals
  - "Why HeavenlyMatch?"
    ✓ NID/Passport Verified Profiles
    ✓ Guardian/Wali Involvement
    ✓ Photos Stay Private Until You Decide
    ✓ Bangladeshi & NRB Friendly
    ✓ Secure Encrypted Communication
    ✓ Admin-Approved Every Profile

Section 6: Success Stories carousel (3 couples)

Section 7: Pricing preview (3 plans, monthly)

Section 8: FAQ accordion (5 questions)

Section 9: Footer (links, social, newsletter)
```

### Dashboard (/home)
```
Layout: Left sidebar (240px fixed) + Main content

Sidebar:
  - Avatar + Name + Registration ID
  - Completeness bar: "Profile 72% Complete" [Complete Now →]
  - Subscription badge (Gold, expiry date)
  - Nav items:
    🏠 Home (active)
    🔍 Browse Matches
    🔎 Search
    💌 Inbox (unread badge)
    ❤️ Interests (pending badge)
    ⭐ Shortlist
    👁️ Who Viewed Me [Premium]
    ⬆️ Upgrade
    ⚙️ Settings

Main Content:
  Row 1: "Today's Best Matches" — 5 cards with match score rings (horizontal scroll)
  Row 2: Activity Feed
    - "Sarah B. viewed your profile 2 hours ago" (blurred avatar)
    - "You have 3 new interests"
    - "Your profile is 72% complete. Add your photo to get 3x more views."
  Row 3: "Recently Active Near You" — 4-card grid
  Row 4: "Success Stories" banner (conversion CTA)
```

### Search Page (/search)
```
Layout: Filter sidebar (280px) + Results grid (responsive)

Filter Sidebar:
  Section: Basic
    - Looking for: [Bride / Groom] toggle
    - Age: dual range slider [18 ——●——●—— 50]
    - Religion: checkbox list
    - Marital Status: multi-select chips

  Section: Location
    - Country: searchable dropdown (BD | UK | UAE | USA ...)
    - Division: conditional dropdown (if BD selected)
    - District: conditional dropdown [Gold+]
    - Open to NRB: toggle

  Section: Education [Gold+]
    - Level: select (SSC → PhD)
    - Method: General / Islamic / Both

  Section: Occupation [Gold+]
    - Category: select
    - Monthly Income: range slider (BDT)

  Section: Lifestyle [Gold+]
    - Prayer habits: select
    - Hijab/Beard: select (shown based on search gender)
    - Verified profiles only: toggle

  [Apply Filters] button (sticky bottom)
  [Save This Search] button

Results Area:
  - Filter chips row (active filters with × remove)
  - Sort: [Match Score ▼] | Newest | Last Active | Featured
  - Result count: "Showing 240 profiles"
  - Grid: 3 cols desktop / 2 cols tablet / 1 col mobile
  - Infinite scroll with skeleton loading
  - "Upgrade to Gold for more filters" banner (non-premium users)
```

### Profile Detail Page (/profile/[id])
```
Layout: 2-col (photo gallery left 40% / details right 60%) on desktop, stacked mobile

Left Panel:
  - Photo carousel (numbered: 1/3, 2/3...)
    - Each photo: blurred or clear + watermark overlay
    - "Request Photo Access" CTA if blurred (Islamic mode)
  - Match score large ring (80px)
  - Action buttons:
    [♡ Shortlist] [→ Send Interest] [💬 Message] (if connected)
  - Verified badges row

Right Panel (tabs):
  Tab 1: Overview
    - Name, Age, Registration ID, Platform Mode badge
    - Location chips (Division, District, Country/flag)
    - "Last active 2h ago" indicator
    - Quick stats grid: Height | Education | Occupation | Income

  Tab 2: Religious Profile
    - Religion, Sect, Prayer habits, Hijab/Beard status
    - Quran recitation, Islamic education, Hajj status
    - Beliefs section

  Tab 3: Family & Background
    - Family type, Financial status, Parents info
    - Brothers/Sisters count
    - Family religious condition

  Tab 4: What I'm Looking For
    - Partner preferences (age range, location, education, religion)
    - Expectations text

  Tab 5: Contact (Premium / Unlocked users only)
    - Guardian phone (shown after connection accepted + Islamic mode approval)
    - Guardian email
    - [Unlock Contact — ৳199] CTA if not unlocked

Sticky bottom bar (mobile):
  [♡ Shortlist] [→ Interest] [⬆️ Upgrade to Message]
```

### Pricing Page (/pricing)
```
Header: "Choose Your Plan"
Billing toggle: [Monthly] [3 Months (-17%)] [6 Months (-31%)] [12 Months (-50%)]

3 Plan Cards (Silver / Gold / Diamond):
  - Name + tier badge
  - Price (large) + per-month breakdown
  - "Most Popular" ribbon on Gold
  - Feature list with ✓/✗/limit
  - CTA button: [Get Gold] etc.
  - Payment methods shown: [bKash] [Nagad] [SSLCommerz] [Visa/MC] [Stripe]

Below plans:
  "Pay-per-use options":
    - Contact Unlock ৳199/profile
    - Profile Boost ৳299/24h
    - Spotlight ৳999/7 days

FAQ accordion below pricing
Money-back guarantee badge: "3-day refund if not satisfied"
```

---

## Mobile Navigation (Bottom Tab Bar)
```
Tab 1: Home (🏠)
Tab 2: Search (🔍)
Tab 3: Matches (✨) — match score badge
Tab 4: Inbox (💌) — unread count badge
Tab 5: Profile (👤)
```

---

## Animations (Framer Motion)
```
ProfileCard mount:    fade up (y: 20 → 0, opacity: 0 → 1, duration: 0.3s, stagger 0.05s)
Match score ring:     draw stroke on mount (1.5s ease-out)
Photo reveal:         blur: 20px → 0, scale: 1.05 → 1 (0.5s, triggered by access grant)
Interest sent:        heart particle burst (confetti-style)
Page transitions:     slide left/right between dashboard sections
Modal open:           scale: 0.95 → 1, opacity: 0 → 1, backdrop blur
```

---

## Accessibility
- Color contrast: WCAG AA minimum on all text (4.5:1)
- Focus rings visible on all interactive elements
- ARIA labels on icon-only buttons
- Screen reader announcements for real-time notifications
- Reduced motion: respect `prefers-reduced-motion`
- Touch targets: minimum 44×44px on mobile
