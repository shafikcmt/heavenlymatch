/// <reference path="../../types/ziggy.d.ts" />
import { Link, router, usePage } from '@inertiajs/react'
import { MapPin, GraduationCap, Briefcase, Heart, Star, Flame, CheckCircle2, X } from 'lucide-react'
import type { PageProps, ProfileCard as ProfileCardType } from '@/types'
import { cn, cmToFeetInches, scoreRingColor } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import { useState } from 'react'

interface Props {
  profile: ProfileCardType
  isShortlisted?: boolean
  interestSent?: boolean
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

function CompletionGuard({ nextStepUrl, onClose }: { nextStepUrl: string; onClose: () => void }) {
  const { t } = useTranslation()
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" onClick={onClose}>
      <div
        className="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl"
        onClick={e => e.stopPropagation()}
      >
        <div className="flex items-center justify-between mb-3">
          <h3 className="font-bold text-slate-900">{t('dashboard', 'interest_block_title')}</h3>
          <button onClick={onClose} className="text-slate-400 hover:text-slate-600">
            <X size={18} />
          </button>
        </div>
        <p className="text-sm text-slate-600 mb-5">{t('dashboard', 'interest_block_body')}</p>
        <div className="flex gap-3">
          <button
            onClick={onClose}
            className="flex-1 rounded-xl border border-slate-200 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors"
          >
            {t('dashboard', 'interest_block_dismiss')}
          </button>
          <Link
            href={nextStepUrl}
            className="flex-1 rounded-xl bg-primary-600 py-2 text-sm font-semibold text-white text-center hover:bg-primary-700 transition-colors"
          >
            {t('dashboard', 'interest_block_cta')}
          </Link>
        </div>
      </div>
    </div>
  )
}

export function ProfileCard({ profile, isShortlisted = false, interestSent = false, className }: Props) {
  const { completion } = usePage<PageProps>().props
  const { t } = useTranslation()

  const [shortlisted, setShortlisted]     = useState(isShortlisted)
  const [interested, setInterested]       = useState(interestSent)
  const [showGuard, setShowGuard]         = useState(false)
  const [shortlistBusy, setShortlistBusy] = useState(false)
  const [interestBusy, setInterestBusy]   = useState(false)

  const isActive = profile.last_active_at
    ? Date.now() - new Date(profile.last_active_at).getTime() < 3_600_000
    : false

  const toggleShortlist = () => {
    if (shortlistBusy) return
    setShortlisted(v => !v) // optimistic
    setShortlistBusy(true)
    router.post(
      route('shortlist.toggle'),
      { target_id: profile.registration_id },
      {
        preserveState: true,
        preserveScroll: true,
        onError: () => setShortlisted(v => !v), // revert on error
        onFinish: () => setShortlistBusy(false),
      },
    )
  }

  const handleInterest = () => {
    if (interested || interestBusy) return
    if (!completion?.can_send_interest) {
      setShowGuard(true)
      return
    }
    setInterested(true) // optimistic
    setInterestBusy(true)
    router.post(
      route('interests.store'),
      { receiver_id: profile.registration_id },
      {
        preserveState: true,
        preserveScroll: true,
        onError: () => setInterested(false), // revert on error
        onFinish: () => setInterestBusy(false),
      },
    )
  }

  return (
    <>
      {showGuard && completion && (
        <CompletionGuard
          nextStepUrl={completion.next_step_url}
          onClose={() => setShowGuard(false)}
        />
      )}

      <article
        className={cn(
          'group relative flex flex-col rounded-2xl border border-slate-200 bg-white overflow-hidden transition-shadow hover:shadow-lg',
          profile.is_boosted && 'ring-2 ring-orange-400',
          className,
        )}
      >
        {/* Photo area */}
        <Link href={route('profile.show', { registrationId: profile.registration_id })} className="block relative aspect-[3/4] bg-slate-100 overflow-hidden">
          {profile.has_photo && profile.photo_url ? (
            <img
              src={profile.photo_url}
              alt={profile.name}
              className={cn(
                'h-full w-full object-cover transition-transform duration-300 group-hover:scale-105',
                profile.photo_visibility === 'blurred' && 'blur-lg',
              )}
              draggable={false}
              onError={e => { (e.target as HTMLImageElement).style.display = 'none' }}
            />
          ) : (
            <div className="h-full w-full flex items-center justify-center bg-slate-100">
              <img
                src={`/images/avatar-${profile.gender}.svg`}
                alt={profile.name}
                className="h-28 w-28 opacity-50"
                onError={e => { (e.target as HTMLImageElement).style.display = 'none' }}
              />
            </div>
          )}

          {/* Gradient overlay */}
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
              <span className="text-[10px] font-medium text-emerald-700">{t('dashboard', 'online_now')}</span>
            </div>
          )}

          {/* Platform badge */}
          {profile.platform_mode === 'islamic' && (
            <div className="absolute bottom-2 left-2 rounded-full bg-emerald-500/90 backdrop-blur-sm px-2 py-0.5 text-[10px] font-bold text-white">
              ☪ {t('dashboard', 'halal_badge')}
            </div>
          )}
        </Link>

        {/* Card body */}
        <div className="flex flex-col flex-1 p-4 gap-2">
          {/* Name + verified */}
          <div className="flex items-center gap-1.5 flex-wrap">
            <Link
              href={route('profile.show', { registrationId: profile.registration_id })}
              className="font-semibold text-slate-900 hover:text-primary-600 transition-colors text-sm truncate"
            >
              {profile.name}
            </Link>
            {profile.is_verified && (
              <CheckCircle2 size={14} className="text-blue-500 shrink-0" />
            )}
            {profile.is_premium && (
              <Star size={13} className="text-amber-500 fill-amber-400 shrink-0" />
            )}
          </div>

          {/* Age, marital status */}
          <p className="text-xs text-slate-500">
            {profile.age ? `${profile.age} yrs` : '—'}
            {profile.marital_status ? ` · ${profile.marital_status.replace(/_/g, ' ')}` : ''}
          </p>

          {/* Location */}
          {(profile.district || profile.division || profile.residing_country) && (
            <p className="flex items-center gap-1 text-xs text-slate-500">
              <MapPin size={11} className="shrink-0" />
              {[profile.district, profile.division, profile.residing_country !== 'Bangladesh' ? profile.residing_country : null]
                .filter(Boolean).join(', ')}
            </p>
          )}

          {/* Chips */}
          <div className="flex flex-wrap gap-1">
            {profile.highest_qualification && (
              <span className="flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                <GraduationCap size={9} />
                {profile.highest_qualification.replace(/_/g, ' ')}
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
              onClick={toggleShortlist}
              disabled={shortlistBusy}
              aria-label={shortlisted ? t('dashboard', 'shortlist_remove') : t('dashboard', 'shortlist_add')}
              className={cn(
                'flex-1 flex items-center justify-center gap-1.5 rounded-xl border py-2 text-xs font-medium transition-colors disabled:opacity-60',
                shortlisted
                  ? 'border-amber-300 bg-amber-50 text-amber-600 hover:bg-amber-100'
                  : 'border-slate-200 text-slate-600 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-600',
              )}
            >
              <Star size={13} className={shortlisted ? 'fill-amber-500' : ''} />
              {t('dashboard', 'save_profile')}
            </button>

            <button
              onClick={handleInterest}
              disabled={interested || interestBusy}
              aria-label={t('dashboard', 'interest_btn')}
              className={cn(
                'flex-1 flex items-center justify-center gap-1.5 rounded-xl py-2 text-xs font-semibold transition-colors disabled:opacity-70',
                interested
                  ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
                  : 'bg-primary-600 text-white hover:bg-primary-700',
              )}
            >
              <Heart size={13} className={interested ? 'fill-emerald-500' : ''} />
              {interested ? t('dashboard', 'interest_sent_label') : t('dashboard', 'interest_btn')}
            </button>
          </div>
        </div>
      </article>
    </>
  )
}
