import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import {
  MapPin, Briefcase, GraduationCap, Heart, Send, Flag,
  Clock, ChevronLeft, User, BookOpen, Users, Handshake,
} from "lucide-react";
import { MatchScoreRing } from "@/components/profile/MatchScoreRing";
import { VerifiedBadge } from "@/components/profile/VerifiedBadge";
import { Button } from "@/components/ui/button";

export const metadata: Metadata = { title: "View Profile" };

// ── Placeholder — swap with real API fetch ─────────────────────────────────────

async function getProfile(registrationId: string) {
  // TODO: fetch from Laravel API with server-side auth header
  if (!registrationId.startsWith("HM")) return null;
  return {
    registrationId,
    name: "Fatema Rahman",
    age: 24,
    gender: "FEMALE" as const,
    maritalStatus: "never_married",
    religion: "Islam",
    sect: "Hanafi",
    isPracticing: true,
    prayersInfo: "5_times",
    quranRecitation: "fluent",
    hijabInfo: "wears_hijab",
    isIslamicallyEducated: false,
    heightCm: 162,
    complexion: "fair",
    bloodGroup: "A+",
    occupation: "Teacher",
    occupationCategory: "education",
    monthlyIncome: 25000,
    highestQualification: "graduation",
    division: "Dhaka",
    district: "Dhaka",
    residingCountry: "Bangladesh",
    isNrb: false,
    fatherName: "Abdul Rahman",
    fatherAlive: true,
    motherName: "Nasima Begum",
    motherAlive: true,
    brothers: 2,
    sisters: 1,
    familyType: "nuclear",
    familyFinancialStatus: "middle",
    aboutMe: "I am a practising Muslimah looking for a God-fearing husband who will help me grow in deen and dunya. I love reading Islamic books and spending time with family.",
    profileHeadline: "Practising Muslimah | Teacher | Dhaka",
    diet: "halal_only",
    smoking: "never",
    lastActive: new Date(Date.now() - 3600000).toISOString(),
    completenessScore: 84,
    isVerified: true,
    isFeatured: false,
    isBoosted: false,
    platformMode: "ISLAMIC" as const,
    photoVisibility: "BLURRED" as const,
    matchScore: 88,
    partnerAgeMin: 24,
    partnerAgeMax: 32,
    partnerReligion: "Islam",
    partnerSect: "Hanafi or Ahle Hadith",
    partnerNationality: "Bangladeshi",
    partnerExpectations: "Looking for a practising Muslim who is kind, family-oriented, and has a stable job. He should be willing to support my career.",
  };
}

// ── Sub-components ─────────────────────────────────────────────────────────────

function InfoRow({ label, value }: { label: string; value: string | number | null | undefined }) {
  if (!value) return null;
  return (
    <div className="flex items-start justify-between gap-4 py-2 border-b border-slate-50 last:border-0">
      <span className="text-sm text-slate-500 shrink-0">{label}</span>
      <span className="text-sm font-medium text-slate-800 text-right">{value}</span>
    </div>
  );
}

type Tab = "overview" | "religious" | "family" | "partner";

// ── Page ──────────────────────────────────────────────────────────────────────

export default async function ProfileDetailPage({
  params,
  searchParams,
}: {
  params: Promise<{ registrationId: string }>;
  searchParams: Promise<{ tab?: string }>;
}) {
  const { registrationId } = await params;
  const { tab } = await searchParams;
  const profile = await getProfile(registrationId);

  if (!profile) notFound();

  const activeTab = (tab as Tab) ?? "overview";

  const tabs: { key: Tab; label: string; icon: React.ElementType }[] = [
    { key: "overview",  label: "Overview",  icon: User },
    { key: "religious", label: "Religious",  icon: BookOpen },
    { key: "family",    label: "Family",     icon: Users },
    { key: "partner",   label: "Looking For", icon: Handshake },
  ];

  return (
    <div className="max-w-5xl mx-auto">
      {/* Back */}
      <Link
        href="/search"
        className="mb-4 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-blue-700 transition-colors"
      >
        <ChevronLeft size={16} />
        Back to search
      </Link>

      <div className="flex flex-col lg:flex-row gap-6">
        {/* ── Left panel ──────────────────────────────────────────────────────── */}
        <div className="lg:w-72 shrink-0 space-y-4">
          {/* Photo area */}
          <div className="relative aspect-[3/4] rounded-2xl overflow-hidden bg-slate-100 border border-slate-200">
            {/* Placeholder avatar */}
            <div className="absolute inset-0 flex items-center justify-center text-slate-300">
              <svg viewBox="0 0 80 80" className="h-32 w-32" fill="currentColor">
                <circle cx="40" cy="30" r="18" />
                <ellipse cx="40" cy="70" rx="28" ry="18" />
              </svg>
            </div>

            {profile.photoVisibility === "BLURRED" && (
              <div className="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-black/30 backdrop-blur-xl text-white text-center p-4">
                <p className="text-sm font-semibold">Photo is private</p>
                <p className="text-xs opacity-80">Request access to see photos</p>
                <button className="mt-1 rounded-lg bg-white/20 px-4 py-2 text-xs font-semibold hover:bg-white/30 transition-colors">
                  Request Photo Access
                </button>
              </div>
            )}

            {profile.matchScore !== null && (
              <div className="absolute top-3 right-3">
                <MatchScoreRing score={profile.matchScore} size={60} />
              </div>
            )}
          </div>

          {/* Verification badges */}
          <div className="flex flex-wrap gap-2">
            {profile.isVerified && <VerifiedBadge level="id" size="md" showLabel />}
            <VerifiedBadge level="email" size="md" showLabel />
            <VerifiedBadge level="phone" size="md" showLabel />
          </div>

          {/* Actions */}
          <div className="space-y-2">
            <Button className="w-full gap-2">
              <Send size={15} />
              Send Interest
            </Button>
            <Button variant="outline" className="w-full gap-2">
              <Heart size={15} />
              Shortlist
            </Button>
            <button className="w-full flex items-center justify-center gap-1.5 text-xs text-slate-400 hover:text-red-500 transition-colors py-1">
              <Flag size={12} />
              Report profile
            </button>
          </div>

          {/* Quick stats */}
          <div className="rounded-2xl border border-slate-200 bg-white p-4 space-y-1">
            <p className="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Quick info</p>
            <InfoRow label="Age" value={profile.age ? `${profile.age} years` : null} />
            <InfoRow label="Height" value={profile.heightCm ? `${profile.heightCm} cm` : null} />
            <InfoRow label="Blood group" value={profile.bloodGroup} />
            <InfoRow label="Complexion" value={profile.complexion} />
            <InfoRow
              label="Last active"
              value={profile.lastActive
                ? new Date(profile.lastActive).toLocaleDateString()
                : null}
            />
          </div>
        </div>

        {/* ── Right panel ─────────────────────────────────────────────────────── */}
        <div className="flex-1 min-w-0">
          {/* Header */}
          <div className="mb-4">
            <div className="flex items-start justify-between gap-3 flex-wrap">
              <div>
                <h1 className="text-2xl font-bold text-slate-900">{profile.name}</h1>
                {profile.profileHeadline && (
                  <p className="text-slate-500 text-sm mt-0.5">{profile.profileHeadline}</p>
                )}
              </div>
              <span className={`rounded-full px-3 py-1 text-xs font-semibold ${
                profile.platformMode === "ISLAMIC"
                  ? "bg-emerald-100 text-emerald-700"
                  : "bg-blue-100 text-blue-700"
              }`}>
                {profile.platformMode === "ISLAMIC" ? "☪️ Islamic Mode" : "🌐 General Mode"}
              </span>
            </div>

            <div className="mt-3 flex flex-wrap gap-3 text-sm text-slate-500">
              <span className="flex items-center gap-1">
                <MapPin size={13} />
                {[profile.district, profile.division, profile.residingCountry].filter(Boolean).join(", ")}
              </span>
              {profile.occupation && (
                <span className="flex items-center gap-1">
                  <Briefcase size={13} />
                  {profile.occupation}
                </span>
              )}
              {profile.highestQualification && (
                <span className="flex items-center gap-1">
                  <GraduationCap size={13} />
                  {profile.highestQualification}
                </span>
              )}
              <span className="flex items-center gap-1 text-emerald-600">
                <Clock size={13} />
                Active recently
              </span>
            </div>
          </div>

          {/* About me */}
          {profile.aboutMe && (
            <div className="mb-5 rounded-2xl bg-slate-50 border border-slate-100 p-4">
              <p className="text-sm text-slate-700 leading-relaxed">{profile.aboutMe}</p>
            </div>
          )}

          {/* Tabs */}
          <div className="flex gap-1 border-b border-slate-200 mb-5 overflow-x-auto">
            {tabs.map(({ key, label, icon: Icon }) => (
              <Link
                key={key}
                href={`/profile/${registrationId}?tab=${key}`}
                className={`flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 transition-colors ${
                  activeTab === key
                    ? "border-blue-600 text-blue-700"
                    : "border-transparent text-slate-500 hover:text-slate-800"
                }`}
              >
                <Icon size={14} />
                {label}
              </Link>
            ))}
          </div>

          {/* Tab content */}
          <div className="rounded-2xl border border-slate-200 bg-white p-5">
            {activeTab === "overview" && (
              <div className="space-y-1">
                <InfoRow label="Registration ID" value={profile.registrationId} />
                <InfoRow label="Marital status" value={profile.maritalStatus.replace("_", " ")} />
                <InfoRow label="Religion" value={profile.religion} />
                <InfoRow label="Sect" value={profile.sect} />
                <InfoRow label="Diet" value={profile.diet?.replace("_", " ")} />
                <InfoRow label="Smoking" value={profile.smoking} />
                <InfoRow label="Nationality" value={profile.residingCountry} />
                <InfoRow label="NRB" value={profile.isNrb ? "Yes (Non-resident Bangladeshi)" : "No"} />
                <InfoRow label="Monthly income" value={profile.monthlyIncome ? `৳${profile.monthlyIncome.toLocaleString()}` : null} />
                <InfoRow label="Profile completeness" value={`${profile.completenessScore}%`} />
              </div>
            )}

            {activeTab === "religious" && (
              <div className="space-y-1">
                <InfoRow label="Religion" value={profile.religion} />
                <InfoRow label="Sect" value={profile.sect} />
                <InfoRow label="Practicing" value={profile.isPracticing ? "Yes" : "No"} />
                <InfoRow label="Prayer habit" value={profile.prayersInfo?.replace("_", " ")} />
                <InfoRow label="Quran recitation" value={profile.quranRecitation} />
                <InfoRow label="Hijab" value={profile.hijabInfo?.replace("_", " ")} />
                <InfoRow label="Islamically educated" value={profile.isIslamicallyEducated ? "Yes" : "No"} />
              </div>
            )}

            {activeTab === "family" && (
              <div className="space-y-1">
                <InfoRow label="Father" value={profile.fatherName} />
                <InfoRow label="Father alive" value={profile.fatherAlive ? "Yes" : "No"} />
                <InfoRow label="Mother" value={profile.motherName} />
                <InfoRow label="Mother alive" value={profile.motherAlive ? "Yes" : "No"} />
                <InfoRow label="Brothers" value={profile.brothers} />
                <InfoRow label="Sisters" value={profile.sisters} />
                <InfoRow label="Family type" value={profile.familyType} />
                <InfoRow label="Financial status" value={profile.familyFinancialStatus?.replace("_", " ")} />
              </div>
            )}

            {activeTab === "partner" && (
              <div className="space-y-1">
                <InfoRow label="Age range" value={`${profile.partnerAgeMin} – ${profile.partnerAgeMax} years`} />
                <InfoRow label="Religion" value={profile.partnerReligion} />
                <InfoRow label="Sect preference" value={profile.partnerSect} />
                <InfoRow label="Nationality" value={profile.partnerNationality} />
                {profile.partnerExpectations && (
                  <div className="pt-3">
                    <p className="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">In their own words</p>
                    <p className="text-sm text-slate-700 leading-relaxed italic">
                      &ldquo;{profile.partnerExpectations}&rdquo;
                    </p>
                  </div>
                )}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
