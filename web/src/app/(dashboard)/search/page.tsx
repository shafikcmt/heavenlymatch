"use client";

import { useState, useCallback } from "react";
import { useSearchStore } from "@/stores/useSearchStore";
import { ProfileCard } from "@/components/profile/ProfileCard";
import { ProfileCardSkeleton } from "@/components/profile/ProfileCardSkeleton";
import { Button } from "@/components/ui/button";
import { SlidersHorizontal, X, ChevronDown } from "lucide-react";
import type { ProfileCard as ProfileCardType } from "@/types/api";

// ── Mock data for now — replace with useQuery hook when API is wired ────────────
const MOCK_PROFILES: ProfileCardType[] = Array.from({ length: 12 }, (_, i) => ({
  id: String(i + 1),
  registrationId: `HM00000${i + 1}`,
  name: ["Fatema Rahman", "Nusrat Jahan", "Sumaiya Akter", "Ayesha Siddiqua"][i % 4] ?? "Profile",
  displayName: null,
  age: 22 + (i % 10),
  gender: "FEMALE",
  maritalStatus: "never_married",
  religion: "Islam",
  sect: i % 3 === 0 ? "Hanafi" : null,
  highestQualification: ["graduation", "post_graduation", "hsc"][i % 3] ?? null,
  occupation: ["Teacher", "Doctor", "Engineer", "Business"][i % 4] ?? null,
  homeDivisionId: null,
  homeDistrictId: null,
  residingCountryId: i % 4 === 0 ? 1 : null,
  heightCm: 155 + (i % 15),
  isFeatured: i % 5 === 0,
  isVerified: i % 3 === 0,
  isBoosted: i % 7 === 0,
  platformMode: i % 3 === 0 ? "ISLAMIC" : "GENERAL",
  photoVisibility: "MEMBERS_ONLY",
  hasPhoto: false,
  photoId: null,
  completenessScore: 60 + (i % 40),
  lastActive: i % 2 === 0 ? new Date(Date.now() - 1800000).toISOString() : null,
  matchScore: 45 + (i % 55),
  scoreBreakdown: null,
}));

// ── Filter sidebar ─────────────────────────────────────────────────────────────

function FilterSection({ title, children }: { title: string; children: React.ReactNode }) {
  const [open, setOpen] = useState(true);
  return (
    <div className="border-b border-slate-100 pb-4">
      <button
        className="flex w-full items-center justify-between py-2 text-sm font-semibold text-slate-800"
        onClick={() => setOpen((v) => !v)}
      >
        {title}
        <ChevronDown size={14} className={`transition-transform ${open ? "rotate-180" : ""}`} />
      </button>
      {open && <div className="mt-2 space-y-2">{children}</div>}
    </div>
  );
}

function FilterSidebar({ onClose }: { onClose?: () => void }) {
  const { filters, setFilter, resetFilters } = useSearchStore();

  return (
    <div className="flex h-full flex-col">
      <div className="flex items-center justify-between mb-4">
        <h2 className="font-bold text-slate-900">Filters</h2>
        <div className="flex gap-2">
          <button
            onClick={resetFilters}
            className="text-xs text-blue-600 hover:underline"
          >
            Reset all
          </button>
          {onClose && (
            <button onClick={onClose} className="text-slate-400 hover:text-slate-600">
              <X size={18} />
            </button>
          )}
        </div>
      </div>

      <div className="flex-1 overflow-y-auto space-y-4 pr-1">
        <FilterSection title="Looking for">
          <div className="grid grid-cols-2 gap-2">
            {([["FEMALE", "👩 Bride"], ["MALE", "👨 Groom"]] as const).map(([v, label]) => (
              <button
                key={v}
                onClick={() => setFilter("gender", v)}
                className={`rounded-lg border py-2 text-sm font-medium transition-colors ${
                  filters.gender === v
                    ? "border-blue-600 bg-blue-50 text-blue-700"
                    : "border-slate-200 text-slate-600 hover:border-slate-300"
                }`}
              >
                {label}
              </button>
            ))}
          </div>
        </FilterSection>

        <FilterSection title="Age range">
          <div className="flex items-center gap-2">
            <input
              type="number"
              min={18} max={70}
              value={filters.ageMin ?? 18}
              onChange={(e) => setFilter("ageMin", Number(e.target.value))}
              className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
              placeholder="Min"
            />
            <span className="text-slate-400">–</span>
            <input
              type="number"
              min={18} max={70}
              value={filters.ageMax ?? 50}
              onChange={(e) => setFilter("ageMax", Number(e.target.value))}
              className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
              placeholder="Max"
            />
          </div>
        </FilterSection>

        <FilterSection title="Religion">
          {["Islam", "Christian", "Buddhist", "Hindu"].map((r) => (
            <label key={r} className="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
              <input type="checkbox" className="rounded border-slate-300" />
              {r}
            </label>
          ))}
        </FilterSection>

        <FilterSection title="Marital status">
          {["Never Married", "Divorced", "Widowed"].map((m) => (
            <label key={m} className="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
              <input type="checkbox" className="rounded border-slate-300" />
              {m}
            </label>
          ))}
        </FilterSection>

        <FilterSection title="Location">
          <select className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700">
            <option value="">Any country</option>
            <option value="BD">🇧🇩 Bangladesh</option>
            <option value="UK">🇬🇧 United Kingdom</option>
            <option value="AE">🇦🇪 UAE</option>
            <option value="US">🇺🇸 USA</option>
            <option value="AU">🇦🇺 Australia</option>
          </select>
        </FilterSection>

        <FilterSection title="Education">
          <select className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700">
            <option value="">Any level</option>
            <option value="hsc">HSC</option>
            <option value="graduation">Graduation</option>
            <option value="post_graduation">Post Graduation</option>
            <option value="phd">PhD</option>
          </select>
        </FilterSection>

        <FilterSection title="Practicing Muslims only">
          <label className="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
            <input
              type="checkbox"
              className="rounded border-slate-300"
              checked={filters.isPracticing ?? false}
              onChange={(e) => setFilter("isPracticing", e.target.checked || undefined)}
            />
            Practicing Muslims only
          </label>
        </FilterSection>

        <FilterSection title="Verified only">
          <label className="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
            <input type="checkbox" className="rounded border-slate-300" />
            Show verified profiles only
          </label>
        </FilterSection>
      </div>

      <div className="mt-4 pt-4 border-t border-slate-100">
        <Button className="w-full">Apply Filters</Button>
      </div>
    </div>
  );
}

// ── Results grid ───────────────────────────────────────────────────────────────

function ResultsGrid({ profiles, isLoading }: { profiles: ProfileCardType[]; isLoading: boolean }) {
  const handleShortlist = useCallback((id: string) => {
    console.log("Shortlist", id);
  }, []);
  const handleInterest = useCallback((id: string) => {
    console.log("Interest", id);
  }, []);

  if (isLoading) {
    return (
      <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        {Array.from({ length: 12 }).map((_, i) => (
          <ProfileCardSkeleton key={i} />
        ))}
      </div>
    );
  }

  return (
    <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      {profiles.map((p) => (
        <ProfileCard
          key={p.registrationId}
          profile={p}
          onShortlist={handleShortlist}
          onInterest={handleInterest}
        />
      ))}
    </div>
  );
}

// ── Page ───────────────────────────────────────────────────────────────────────

export default function SearchPage() {
  const [showMobileFilters, setShowMobileFilters] = useState(false);
  const [sort, setSort] = useState("match_score");
  const isLoading = false;

  return (
    <div className="max-w-screen-xl mx-auto">
      <div className="flex gap-6">
        {/* Sidebar — desktop */}
        <aside className="hidden lg:block w-72 shrink-0">
          <div className="sticky top-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <FilterSidebar />
          </div>
        </aside>

        {/* Main content */}
        <div className="flex-1 min-w-0">
          {/* Toolbar */}
          <div className="mb-5 flex items-center justify-between gap-3 flex-wrap">
            <div className="flex items-center gap-3">
              <button
                onClick={() => setShowMobileFilters(true)}
                className="flex lg:hidden items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
              >
                <SlidersHorizontal size={15} />
                Filters
              </button>
              <p className="text-sm text-slate-500">
                <span className="font-semibold text-slate-900">{MOCK_PROFILES.length}</span> profiles found
              </p>
            </div>

            <select
              value={sort}
              onChange={(e) => setSort(e.target.value)}
              className="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm"
            >
              <option value="match_score">Match Score ↓</option>
              <option value="newest">Newest First</option>
              <option value="last_active">Last Active</option>
              <option value="featured">Featured First</option>
            </select>
          </div>

          <ResultsGrid profiles={MOCK_PROFILES} isLoading={isLoading} />

          {/* Load more */}
          <div className="mt-8 flex justify-center">
            <Button variant="outline">Load more profiles</Button>
          </div>
        </div>
      </div>

      {/* Mobile filter drawer */}
      {showMobileFilters && (
        <div className="fixed inset-0 z-50 flex lg:hidden">
          <div
            className="absolute inset-0 bg-black/40"
            onClick={() => setShowMobileFilters(false)}
          />
          <div className="relative ml-auto h-full w-80 bg-white p-5 shadow-xl overflow-y-auto">
            <FilterSidebar onClose={() => setShowMobileFilters(false)} />
          </div>
        </div>
      )}
    </div>
  );
}
