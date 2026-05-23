---
name: project-phase2b-biodata-wizard
description: "Phase 2B Biodata Wizard UX + Partial Profile Access — wizard overhaul, completion service, dashboard checklist (2026-05-22)"
metadata:
  type: project
---

# Phase 2B — Biodata Wizard UX + Partial Profile Access (2026-05-22)

**Why:** Remove hard biodata-redirect wall so users can use the platform with incomplete profiles. Add section-level progress tracking, smarter dashboard UI, and a friendlier wizard with save-draft and searchable dropdowns.

## Key Decisions

- `CheckBiodataCompletion` middleware: soft-blocks only `interests.store`, `upgrade.checkout`, `upgrade.manual.submit` when score < 30%. All other authenticated routes allowed through.
- `ProfileCompletionService` (new service): computes `percentage`, `completed_sections`, `missing_sections`, `next_step`, `next_step_url`, `can_send_interest`, `can_be_publicly_listed`, `has_photo`.
- `HandleInertiaRequests` shares `completion` as a lazy closure (runs only when accessed).

## Files Changed

### Backend
- `app/Http/Middleware/CheckBiodataCompletion.php` — soft-block only 3 routes
- `app/Services/ProfileCompletionService.php` — new service (9 sections, 20 scored fields)
- `app/Http/Middleware/HandleInertiaRequests.php` — added `completion` shared prop (lazy closure)
- `app/Http/Controllers/Biodata/BiodataWizardController.php` — `save_draft` support: if `$request->boolean('save_draft')`, redirect back to same step with success flash
- `lang/en/biodata.php` — fixed step_labels (6=Lifestyle,7=Marriage,8=Partner,9=Photos), added `step_helper[1-9]`, `section_label[*]`, `completion_cta`, `completion_benefit`
- `lang/bn/biodata.php` — same fixes in Bengali

### Frontend
- `resources/js/types/index.d.ts` — added `CompletionData` interface, added `completion: CompletionData | null` to `PageProps`
- `resources/js/data/bangladesh.ts` — new: `BD_DIVISIONS` (8 divisions), `BD_DISTRICTS` (all 64 districts mapped by division)
- `resources/js/components/ui/SearchableSelect.tsx` — new: searchable dropdown with filter-as-you-type, keyboard support (Enter/Escape), clear button, disabled state, free-text mode
- `resources/js/pages/Biodata/Wizard.tsx` — major UX overhaul:
  - Overall completion % progress bar at top (from `biodata.completeness_score`)
  - Step dots with click-back for completed steps
  - Card header with step number, title (from translations), helper text (from `step_helper.N`)
  - "Save Draft" button in header (posts with `save_draft=true`)
  - Cascading division→district for step 2 (BD_DIVISIONS / BD_DISTRICTS)
  - Character counter for about_me and partner_expectations (max 1000)
  - `SearchableSelect` for all dropdown fields
- `resources/js/pages/Dashboard/Index.tsx` — section checklist under completion banner (uses `completion` from shared props), "Continue → step X" CTA using `completion.next_step_url`
- `resources/js/layouts/AppLayout.tsx` — mini progress bar in sidebar bottom (amber, links to `next_step_url`) visible when profile < 100%

## How to Apply
- `php artisan optimize:clear` ✓
- `npm run build` ✓ (10.16s, 0 errors)
- Test: visit `/biodata/wizard/1` → see progress bar, helper text, Save Draft button
- Test: save draft → stays on same step with success flash
- Test: dashboard → completion checklist shows, CTA points to next incomplete section
- Test: AppLayout sidebar shows mini amber progress bar for incomplete profiles
- Test: Step 2 location → selecting Division populates district dropdown

## How to Apply (continued)
- The `completion` lazy prop is nil for guests; always check `completion &&` before use in React
- `t('biodata', 'step_helper.1')` returns the helper text for step 1 (dot-notation through nested PHP array)
- `t('biodata', 'section_label.general')` returns section label for dashboard checklist
