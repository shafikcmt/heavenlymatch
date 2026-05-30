/// <reference path="../../types/ziggy.d.ts" />
import { Link, router, usePage } from '@inertiajs/react'
import { Star, MessageSquare, CheckCircle2, MoreVertical, X } from 'lucide-react'
import type { PageProps, ProfileCard as ProfileCardType } from '@/types'
import { cn, cmToFeetInches } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import { useState } from 'react'

interface Props {
  profile: ProfileCardType
  index?: number
  pageFrom?: number
  isShortlisted?: boolean
  interestSent?: boolean
  className?: string
}

function CompletionGuard({ nextStepUrl, onClose }: { nextStepUrl: string; onClose: () => void }) {
  const { t } = useTranslation()
  return (
    <div
      className="fixed inset-0 z-50 flex items-end justify-center bg-black/50 sm:items-center"
      onClick={onClose}
    >
      <div
        className="w-full max-w-sm rounded-t-3xl bg-white p-6 shadow-xl sm:rounded-2xl"
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
            className="flex-1 rounded-xl border border-slate-200 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50"
          >
            {t('dashboard', 'interest_block_dismiss')}
          </button>
          <Link
            href={nextStepUrl}
            className="flex-1 rounded-xl bg-primary-700 py-2.5 text-sm font-semibold text-white text-center hover:bg-primary-800"
          >
            {t('dashboard', 'interest_block_cta')}
          </Link>
        </div>
      </div>
    </div>
  )
}

export function MobileProfileCard({
  profile,
  index,
  pageFrom = 1,
  isShortlisted = false,
  interestSent = false,
  className,
}: Props) {
  const { completion } = usePage<PageProps>().props
  const { t } = useTranslation()

  const [shortlisted, setShortlisted]     = useState(isShortlisted)
  const [interested, setInterested]       = useState(interestSent)
  const [showGuard, setShowGuard]         = useState(false)
  const [shortlistBusy, setShortlistBusy] = useState(false)
  const [interestBusy, setInterestBusy]   = useState(false)

  const serialNumber = index !== undefined ? pageFrom + index : null

  const toggleShortlist = (e: React.MouseEvent) => {
    e.preventDefault()
    e.stopPropagation()
    if (shortlistBusy) return
    setShortlisted(v => !v)
    setShortlistBusy(true)
    router.post(
      route('shortlist.toggle'),
      { target_id: profile.registration_id },
      {
        preserveState: true,
        preserveScroll: true,
        onError: () => setShortlisted(v => !v),
        onFinish: () => setShortlistBusy(false),
      },
    )
  }

  const handleInterest = (e: React.MouseEvent) => {
    e.preventDefault()
    e.stopPropagation()
    if (interested || interestBusy) return
    if (!completion?.can_send_interest) {
      setShowGuard(true)
      return
    }
    setInterested(true)
    setInterestBusy(true)
    router.post(
      route('interests.store'),
      { receiver_id: profile.registration_id },
      {
        preserveState: true,
        preserveScroll: true,
        onError: () => setInterested(false),
        onFinish: () => setInterestBusy(false),
      },
    )
  }

  // Build compact detail string like reference: "26 yrs, 5 ft 1 in, Sunni, Unmarried, Bachelors, Executive, Dhaka, Bangladesh"
  const details = [
    profile.age ? `${profile.age} ${t('common', 'yrs')}` : null,
    profile.height_cm ? cmToFeetInches(profile.height_cm) : null,
    profile.religion ?? null,
    profile.marital_status ? t('biodata', profile.marital_status) : null,
    profile.highest_qualification ? t('biodata', `qual_${profile.highest_qualification}`) : null,
    profile.occupation ?? null,
    profile.district ?? null,
    profile.division ?? null,
    profile.residing_country !== 'Bangladesh' ? profile.residing_country : 'Bangladesh',
  ].filter(Boolean).join(', ')

  const profileUrl = route('profile.show', { registrationId: profile.registration_id })

  return (
    <>
      {showGuard && completion && (
        <CompletionGuard nextStepUrl={completion.next_step_url} onClose={() => setShowGuard(false)} />
      )}

      <article
        className={cn(
          'relative bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200 active:scale-[0.99]',
          profile.is_boosted && 'ring-1 ring-orange-400',
          className,
        )}
      >
        {/* Featured ribbon */}
        {profile.is_featured && (
          <div className="absolute top-0 left-0 z-10">
            <span className="inline-block bg-violet-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-br-xl">
              FEATURED
            </span>
          </div>
        )}

        {/* Card main row */}
        <Link href={profileUrl} className="flex gap-3 p-3 pt-4">
          {/* Profile image */}
          <div className="relative shrink-0">
            <div
              className="h-[82px] w-[82px] rounded-full overflow-hidden bg-slate-100 border-2 border-white shadow-sm"
              style={{ boxShadow: '0 0 0 2px #e2e8f0' }}
            >
              {profile.has_photo && profile.photo_url ? (
                <img
                  src={profile.photo_url}
                  alt={profile.name}
                  className={cn(
                    'h-full w-full object-cover',
                    profile.blurred && 'blur-md',
                  )}
                  draggable={false}
                  onError={e => { (e.target as HTMLImageElement).src = `/images/avatar-${profile.gender}.svg` }}
                />
              ) : (
                <div
                  className={cn(
                    'h-full w-full flex items-center justify-center',
                    profile.gender === 'female'
                      ? 'bg-gradient-to-br from-rose-50 to-pink-100'
                      : 'bg-gradient-to-br from-blue-50 to-sky-100',
                  )}
                >
                  <img
                    src={`/images/avatar-${profile.gender}.svg`}
                    alt=""
                    className="h-10 w-10 opacity-40"
                  />
                </div>
              )}
            </div>

            {/* Serial number badge */}
            {serialNumber !== null && (
              <span className="absolute -bottom-1 left-1/2 -translate-x-1/2 rounded-full bg-slate-700 px-1.5 py-0.5 text-[9px] font-bold text-white leading-none">
                #{String(serialNumber).padStart(2, '0')}
              </span>
            )}
          </div>

          {/* Info */}
          <div className="flex-1 min-w-0">
            {/* ID row */}
            <div className="flex items-start justify-between gap-1">
              <div className="flex items-center gap-1.5 flex-wrap min-w-0">
                <span className="text-[11px] text-slate-500 font-mono">{profile.registration_id}</span>
                {profile.is_premium && (
                  <span className="text-[10px] font-bold text-teal-600 uppercase tracking-wide">
                    Platinum ⚡
                  </span>
                )}
              </div>
              <button
                aria-label="More options"
                onClick={e => { e.preventDefault(); e.stopPropagation() }}
                className="shrink-0 p-1 rounded-full hover:bg-slate-100 transition-colors text-slate-400"
              >
                <MoreVertical size={15} />
              </button>
            </div>

            {/* Name */}
            <p className="text-[15px] font-bold text-slate-900 leading-tight mt-0.5 truncate">
              {profile.name}
            </p>

            {/* Detail line */}
            <p className="text-xs text-slate-500 mt-1 leading-relaxed line-clamp-3">
              {details || '—'}
            </p>
          </div>
        </Link>

        {/* Action row */}
        <div className="flex border-t border-slate-100">
          <button
            onClick={toggleShortlist}
            disabled={shortlistBusy}
            aria-label={shortlisted ? t('dashboard', 'shortlist_remove') : t('dashboard', 'shortlist_add')}
            className={cn(
              'flex flex-1 items-center justify-center gap-1.5 py-2.5 text-xs font-semibold transition-colors active:bg-slate-50 disabled:opacity-60',
              shortlisted ? 'text-amber-500' : 'text-rose-700',
            )}
          >
            <Star
              size={14}
              className={cn(shortlisted ? 'fill-amber-500' : 'fill-rose-700')}
              strokeWidth={0}
            />
            {t('dashboard', 'shortlist_add') || 'Shortlist'}
          </button>

          <div className="w-px bg-slate-100" />

          <Link
            href={profileUrl}
            className="flex flex-1 items-center justify-center gap-1.5 py-2.5 text-xs font-semibold text-rose-700 transition-colors active:bg-slate-50"
          >
            <MessageSquare size={14} className="fill-rose-700" strokeWidth={0} />
            {t('dashboard', 'chat_now') || 'Chat Now'}
          </Link>

          <div className="w-px bg-slate-100" />

          <button
            onClick={handleInterest}
            disabled={interested || interestBusy}
            aria-label={t('dashboard', 'interest_btn')}
            className={cn(
              'flex flex-1 items-center justify-center gap-1.5 py-2.5 text-xs font-semibold transition-colors active:bg-slate-50 disabled:opacity-60',
              interested ? 'text-emerald-600' : 'text-rose-700',
            )}
          >
            <CheckCircle2
              size={14}
              className={cn(interested ? 'text-emerald-600' : 'fill-rose-700 text-rose-700')}
              strokeWidth={interested ? 2 : 0}
            />
            {interested
              ? (t('dashboard', 'interest_sent_label') || 'Sent')
              : (t('dashboard', 'interest_btn') || 'Send Interest')}
          </button>
        </div>
      </article>
    </>
  )
}
