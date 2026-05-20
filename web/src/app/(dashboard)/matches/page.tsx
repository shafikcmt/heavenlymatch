import Link from "next/link";
import { Sparkles, RefreshCw } from "lucide-react";
import { ProfileCard } from "@/components/profile/ProfileCard";
import { ProfileCardSkeleton } from "@/components/profile/ProfileCardSkeleton";
import { Button } from "@/components/ui/button";
import type { ProfileCard as ProfileCardType } from "@/types/api";

// ── Mock data — replace with server fetch when Laravel API is wired ──────────
const DAILY_PICKS: ProfileCardType[] = Array.from({ length: 5 }, (_, i) => ({
  id: `d${i}`,
  registrationId: `HM10000${i + 1}`,
  name: ["Ayesha Rahman", "Nadia Islam", "Sabrina Hossain", "Tanjina Akter", "Maimuna Begum"][i] ?? "Profile",
  displayName: null,
  age: 23 + i,
  gender: "FEMALE",
  maritalStatus: "never_married",
  religion: "Islam",
  sect: i % 2 === 0 ? "Hanafi" : "Ahle Hadith",
  highestQualification: ["graduation", "post_graduation", "phd", "graduation", "hsc"][i] ?? null,
  occupation: ["Doctor", "Engineer", "Teacher", "Business", "Government Job"][i] ?? null,
  homeDivisionId: null,
  homeDistrictId: null,
  residingCountryId: i === 1 ? 2 : null,
  heightCm: 158 + i * 2,
  isFeatured: true,
  isVerified: i % 2 === 0,
  isBoosted: false,
  platformMode: "GENERAL",
  photoVisibility: "MEMBERS_ONLY",
  hasPhoto: false,
  photoId: null,
  completenessScore: 70 + i * 5,
  lastActive: new Date(Date.now() - i * 3600000).toISOString(),
  matchScore: 92 - i * 4,
  scoreBreakdown: { religion: 20, age: 15, location: 12, education: 10, lifestyle: 8 },
}));

const MORE_MATCHES: ProfileCardType[] = Array.from({ length: 12 }, (_, i) => ({
  id: `m${i}`,
  registrationId: `HM20000${i + 1}`,
  name: ["Fatema", "Zarin", "Mariam", "Halima"][i % 4] + " " + ["Rahman", "Islam", "Hossain", "Akter"][i % 4],
  displayName: null,
  age: 20 + (i % 15),
  gender: "FEMALE",
  maritalStatus: "never_married",
  religion: "Islam",
  sect: null,
  highestQualification: ["graduation", "hsc", "post_graduation"][i % 3] ?? null,
  occupation: ["Student", "Teacher", "Nurse", "Business"][i % 4] ?? null,
  homeDivisionId: null,
  homeDistrictId: null,
  residingCountryId: null,
  heightCm: 152 + (i % 20),
  isFeatured: false,
  isVerified: i % 4 === 0,
  isBoosted: i % 9 === 0,
  platformMode: i % 3 === 0 ? "ISLAMIC" : "GENERAL",
  photoVisibility: "MEMBERS_ONLY",
  hasPhoto: false,
  photoId: null,
  completenessScore: 50 + (i % 50),
  lastActive: i % 3 === 0 ? new Date(Date.now() - 900000).toISOString() : null,
  matchScore: 55 + (i % 40),
  scoreBreakdown: null,
}));

// ── Score breakdown popover (simplified) ─────────────────────────────────────

function ScoreBreakdown({ breakdown }: { breakdown: Record<string, number> }) {
  return (
    <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-md text-sm space-y-2 w-52">
      <p className="font-semibold text-slate-900 mb-3">Score breakdown</p>
      {Object.entries(breakdown).map(([factor, pts]) => (
        <div key={factor} className="flex items-center justify-between">
          <span className="capitalize text-slate-600">{factor}</span>
          <div className="flex items-center gap-2">
            <div className="w-20 h-1.5 rounded-full bg-slate-100 overflow-hidden">
              <div
                className="h-full rounded-full bg-blue-600"
                style={{ width: `${(pts / 20) * 100}%` }}
              />
            </div>
            <span className="text-slate-700 font-medium w-6 text-right">{pts}</span>
          </div>
        </div>
      ))}
    </div>
  );
}

// ── Page ──────────────────────────────────────────────────────────────────────

export default function MatchesPage() {
  return (
    <div className="max-w-5xl mx-auto space-y-10">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 flex items-center gap-2">
            <Sparkles size={22} className="text-amber-500" />
            Your Matches
          </h1>
          <p className="text-sm text-slate-500 mt-0.5">
            AI-curated matches updated daily at 2:00 AM
          </p>
        </div>
        <Button variant="outline" size="sm" className="hidden sm:flex gap-2">
          <RefreshCw size={14} />
          Refresh
        </Button>
      </div>

      {/* Today's picks */}
      <section>
        <div className="flex items-center justify-between mb-4">
          <h2 className="font-semibold text-slate-900">
            Today&apos;s Top 5 Picks 🌟
          </h2>
          <Link
            href="/matches/daily"
            className="text-sm text-blue-700 hover:underline"
          >
            View all →
          </Link>
        </div>

        <div className="flex gap-4 overflow-x-auto pb-2 -mx-1 px-1">
          {DAILY_PICKS.map((p) => (
            <div key={p.registrationId} className="w-52 shrink-0">
              <ProfileCard profile={p} />
            </div>
          ))}
        </div>
      </section>

      {/* All matches */}
      <section>
        <div className="flex items-center justify-between mb-4">
          <h2 className="font-semibold text-slate-900">All Matches</h2>
          <div className="flex items-center gap-2">
            <select className="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700">
              <option value="score">Highest score first</option>
              <option value="active">Recently active</option>
              <option value="new">Newest profiles</option>
            </select>
          </div>
        </div>

        <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          {MORE_MATCHES.map((p) => (
            <ProfileCard key={p.registrationId} profile={p} />
          ))}
        </div>

        <div className="mt-8 flex justify-center">
          <Button variant="outline">Load more matches</Button>
        </div>
      </section>

      {/* Score key */}
      <section className="rounded-2xl border border-slate-200 bg-white p-5">
        <h3 className="font-semibold text-slate-900 mb-3 text-sm">How match scores work</h3>
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
          {[
            { label: "Excellent 81–100%", color: "bg-blue-600" },
            { label: "Good 66–80%", color: "bg-emerald-500" },
            { label: "Fair 41–65%", color: "bg-amber-400" },
            { label: "Low 0–40%", color: "bg-red-400" },
          ].map((item) => (
            <div key={item.label} className="flex items-center gap-2 text-slate-600">
              <span className={`h-2.5 w-2.5 rounded-full shrink-0 ${item.color}`} />
              {item.label}
            </div>
          ))}
        </div>
      </section>
    </div>
  );
}
