import { Link } from '@inertiajs/react'
import { MapPin, GraduationCap, Briefcase, Heart, Star, Flame, CheckCircle2 } from 'lucide-react'
import type { ProfileCard as ProfileCardType } from '@/types'
import { cn, cmToFeetInches, relativeTime, scoreRingColor } from '@/lib/utils'

interface Props {
  profile: ProfileCardType
  onShortlist?: (id: string) => void
  onInterest?: (id: string) => void
  className?: string
}

function MatchRing({ score }: { score: number }) {
  const r = 18
  const circ = 2 * Math.PI * r
  const offset = circ - (score / 100) * circ

  return (
    <div className="relative h-11 w-11">
      <svg viewBox="0 0 44 44" className="-rotate-90">
        <circle cx="22" cy="22" r={r} fill="none" stroke="#e2e8f0" strokeWidth="3" />
        <circle
          cx="22" cy="22" r={r} fill="none"
          stroke={scoreRingColor(score)}
          strokeWidth="3"
          strokeLinecap="round"
          strokeDasharray={circ}
          strokeDashoffset={offset}
          className="transition-all duration-700"
        />
      </svg>
      <span className="absolute inset-0 flex items-center justify-center text-[10px] font-bold text-slate-800">
        {score}
      </span>
    </div>
  )
}

export function ProfileCard({ profile, onShortlist, onInterest, className }: Props) {
  const isActive = profile.last_active_at
    ? Date.now() - new Date(profile.last_active_at).getTime() < 3600_000
    : false

  return (
    <article
      className={cn(
        'group relative flex flex-col rounded-2xl border border-slate-200 bg-white overflow-hidden transition-shadow hover:shadow-lg',
        profile.is_boosted && 'diamond-glow',
        className,
      )}
    >
      {/* Photo area */}
      <Link href={`/profile/${profile.registration_id}`} className="block relative aspect-[3/4] bg-slate-100 overflow-hidden">
        {profile.has_photo && profile.photo_url ? (
          <img
            src={profile.photo_url}
            alt={profile.name}
            className={cn(
              'h-full w-full object-cover transition-transform duration-300 group-hover:scale-105',
              'private-photo',
              profile.photo_visibility === 'blurred' && 'blur-lg',
            )}
            draggable={false}
          />
        ) : (
          <div className="h-full w-full flex items-center justify-center">
            <img
              src={`/images/avatar-${profile.gender}.svg`}
              alt={profile.name}
              className="h-28 w-28 opacity-60"
            />
          </div>
        )}

        {/* Overlays */}
        <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />

        {/* Match ring top-right */}
        {profile.match_score != null && (
          <div className="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded-full p-0.5">
            <MatchRing score={profile.match_score} />
          </div>
        )}

        {/* Boosted flame */}
        {profile.is_boosted && (
          <div className="absolute top-2 left-2 rounded-full bg-orange-500 p-1.5">
            <Flame size={12} className="text-white" />
          </div>
        )}

        {/* Active indicator */}
        {isActive && (
          <div className="absolute bottom-2 right-2 flex items-center gap-1 rounded-full bg-white/90 px-2 py-0.5">
            <span className="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse" />
            <span className="text-[10px] font-medium text-emerald-700">Online</span>
          </div>
        )}

        {/* Platform badge */}
        {profile.platform_mode === 'islamic' && (
          <div className="absolute bottom-2 left-2 rounded-full bg-emerald-500/90 backdrop-blur-sm px-2 py-0.5 text-[10px] font-bold text-white">
            ☪️ Halal
          </div>
        )}
      </Link>

      {/* Card body */}
      <div className="flex flex-col flex-1 p-4 gap-2">
        {/* Name + verified */}
        <div className="flex items-center gap-1.5 flex-wrap">
          <Link
            href={`/profile/${profile.registration_id}`}
            className="font-semibold text-slate-900 hover:text-primary-600 transition-colors text-sm truncate"
          >
            {profile.name}
          </Link>
          {profile.is_verified && (
            <CheckCircle2 size={14} className="text-blue-500 shrink-0" />
          )}
        </div>

        {/* Age, marital status */}
        <p className="text-xs text-slate-500">
          {profile.age ? `${profile.age} yrs` : '—'}
          {profile.marital_status ? ` · ${profile.marital_status.replace('_', ' ')}` : ''}
        </p>

        {/* Location */}
        {(profile.district || profile.division || profile.residing_country) && (
          <p className="flex items-center gap-1 text-xs text-slate-500">
            <MapPin size={11} className="shrink-0" />
            {[profile.district, profile.division, profile.residing_country !== 'Bangladesh' ? profile.residing_country : null]
              .filter(Boolean).join(', ')}
          </p>
        )}

        {/* Education + Occupation chips */}
        <div className="flex flex-wrap gap-1">
          {profile.highest_qualification && (
            <span className="flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
              <GraduationCap size={9} />
              {profile.highest_qualification.replace('_', ' ')}
            </span>
          )}
          {profile.occupation && (
            <span className="flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
              <Briefcase size={9} />
              {profile.occupation}
            </span>
          )}
          {profile.height_cm && (
            <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
              {cmToFeetInches(profile.height_cm)}
            </span>
          )}
        </div>

        {/* Action buttons */}
        <div className="mt-auto pt-2 flex gap-2">
          <button
            onClick={() => onShortlist?.(profile.registration_id)}
            className="flex-1 flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 py-2 text-xs font-medium text-slate-600 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-600 transition-colors"
          >
            <Star size={13} />
            Save
          </button>
          <button
            onClick={() => onInterest?.(profile.registration_id)}
            className="flex-1 flex items-center justify-center gap-1.5 rounded-xl bg-primary-600 py-2 text-xs font-semibold text-white hover:bg-primary-700 transition-colors"
          >
            <Heart size={13} />
            Interest
          </button>
        </div>
      </div>
    </article>
  )
}
