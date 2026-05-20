"use client";

import Link from "next/link";
import { Heart, Send, Eye, Flame, MapPin, GraduationCap, Briefcase } from "lucide-react";
import { cn } from "@/lib/utils";
import { MatchScoreRing } from "./MatchScoreRing";
import { VerifiedBadge } from "./VerifiedBadge";
import type { ProfileCard as ProfileCardType } from "@/types/api";

function timeAgo(iso: string | null): string {
  if (!iso) return "recently";
  const diff = (Date.now() - new Date(iso).getTime()) / 1000;
  if (diff < 3600)  return `${Math.floor(diff / 60)}m ago`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
  return `${Math.floor(diff / 86400)}d ago`;
}

function heightLabel(cm: number | null) {
  if (!cm) return null;
  const totalInches = Math.round(cm / 2.54);
  const ft = Math.floor(totalInches / 12);
  const inch = totalInches % 12;
  return `${ft}′${inch}″`;
}

interface ProfileCardProps {
  profile: ProfileCardType;
  onShortlist?: (id: string) => void;
  onInterest?: (id: string) => void;
  className?: string;
}

export function ProfileCard({ profile, onShortlist, onInterest, className }: ProfileCardProps) {
  const isRecentlyActive =
    profile.lastActive &&
    Date.now() - new Date(profile.lastActive).getTime() < 3_600_000;

  return (
    <div
      className={cn(
        "group relative flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md hover:border-blue-200",
        profile.isBoosted && "diamond-glow",
        className
      )}
    >
      {/* ── Photo area ────────────────────────────────────────────────────────── */}
      <div className="relative aspect-[3/4] bg-slate-100 overflow-hidden">
        {/* Placeholder avatar */}
        <div className="absolute inset-0 flex items-center justify-center text-slate-300">
          <svg viewBox="0 0 80 80" className="h-20 w-20" fill="currentColor">
            <circle cx="40" cy="30" r="18" />
            <ellipse cx="40" cy="70" rx="28" ry="18" />
          </svg>
        </div>

        {/* Gradient overlay */}
        <div className="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-black/70 to-transparent" />

        {/* Match score ring */}
        {profile.matchScore !== null && (
          <div className="absolute top-3 right-3">
            <MatchScoreRing score={profile.matchScore} size={52} />
          </div>
        )}

        {/* Boost badge */}
        {profile.isBoosted && (
          <div className="absolute top-3 left-3 flex items-center gap-1 rounded-full bg-amber-500/90 px-2.5 py-1 text-xs font-bold text-white backdrop-blur-sm">
            <Flame size={12} />
            Featured
          </div>
        )}

        {/* Active indicator */}
        {isRecentlyActive && (
          <span className="absolute bottom-3 left-3 flex items-center gap-1 text-xs text-white font-medium">
            <span className="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.8)]" />
            Active {timeAgo(profile.lastActive)}
          </span>
        )}
      </div>

      {/* ── Content ───────────────────────────────────────────────────────────── */}
      <div className="flex flex-1 flex-col gap-2 p-4">
        {/* Name + Age + Verified */}
        <div className="flex items-start justify-between gap-2">
          <div>
            <h3 className="font-bold text-slate-900 leading-tight line-clamp-1">
              {profile.displayName ?? profile.name}
            </h3>
            {profile.age && (
              <p className="text-sm text-slate-500">{profile.age} yrs</p>
            )}
          </div>
          {profile.isVerified && (
            <VerifiedBadge level="id" size="sm" />
          )}
        </div>

        {/* Location */}
        <div className="flex items-center gap-1 text-xs text-slate-500">
          <MapPin size={12} className="shrink-0" />
          <span className="line-clamp-1">
            {[profile.homeDistrictId ? "District" : null, profile.residingCountryId ? "Country" : "Bangladesh"]
              .filter(Boolean)
              .join(", ")}
          </span>
        </div>

        {/* Education + Occupation chips */}
        <div className="flex flex-wrap gap-1.5">
          {profile.highestQualification && (
            <span className="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">
              <GraduationCap size={10} />
              {profile.highestQualification}
            </span>
          )}
          {profile.occupation && (
            <span className="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">
              <Briefcase size={10} />
              <span className="line-clamp-1">{profile.occupation}</span>
            </span>
          )}
          {profile.heightCm && (
            <span className="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">
              {heightLabel(profile.heightCm)}
            </span>
          )}
        </div>

        {/* Action row */}
        <div className="mt-auto flex items-center gap-2 pt-2">
          <button
            onClick={(e) => { e.preventDefault(); onShortlist?.(profile.registrationId); }}
            aria-label="Shortlist"
            className="flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-400 hover:border-rose-300 hover:text-rose-500 transition-colors"
          >
            <Heart size={14} />
          </button>
          <button
            onClick={(e) => { e.preventDefault(); onInterest?.(profile.registrationId); }}
            aria-label="Send interest"
            className="flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-400 hover:border-blue-300 hover:text-blue-600 transition-colors"
          >
            <Send size={14} />
          </button>
          <Link
            href={`/profile/${profile.registrationId}`}
            className="ml-auto flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition-colors"
          >
            <Eye size={12} />
            View
          </Link>
        </div>
      </div>
    </div>
  );
}
