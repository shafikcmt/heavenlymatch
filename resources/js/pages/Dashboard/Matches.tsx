/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { ProfileCard } from '@/components/profile/ProfileCard'
import { Button } from '@/components/ui/Button'
import { type ProfileCard as ProfileCardType } from '@/types'
import { useTranslation } from '@/lib/i18n'
import { TrendingUp, ChevronDown } from 'lucide-react'
import { useState } from 'react'

interface Props {
  matches: ProfileCardType[]
  hasBiodata: boolean
  membershipTier: string
}

const SCORE_LABEL: Record<string, string> = {
  age:        'score_age',
  location:   'score_location',
  religion:   'score_religion',
  education:  'score_education',
  occupation: 'score_occupation',
}

function ScoreBreakdown({ breakdown, t }: { breakdown: Record<string, number>; t: (ns: string, k: string) => string }) {
  const [open, setOpen] = useState(false)
  const entries = Object.entries(breakdown).filter(([, v]) => v > 0)
  if (entries.length === 0) return null

  return (
    <div className="mt-2 px-1">
      <button
        onClick={() => setOpen(v => !v)}
        className="flex items-center gap-1 text-xs text-primary-600 hover:text-primary-700 font-medium"
      >
        {t('dashboard', 'score_breakdown')}
        <ChevronDown size={12} className={open ? 'rotate-180 transition-transform' : 'transition-transform'} />
      </button>
      {open && (
        <div className="mt-1.5 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 space-y-1">
          {entries.map(([key, val]) => (
            <div key={key} className="flex items-center justify-between text-xs">
              <span className="text-slate-500">{t('dashboard', SCORE_LABEL[key] ?? key)}</span>
              <div className="flex items-center gap-2">
                <div className="w-16 h-1.5 rounded-full bg-slate-200 overflow-hidden">
                  <div className="h-full rounded-full bg-primary-500" style={{ width: `${Math.min(100, val)}%` }} />
                </div>
                <span className="text-slate-700 font-medium w-6 text-right">{val}</span>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}

export default function Matches({ matches, hasBiodata, membershipTier }: Props) {
  const { t } = useTranslation()
  const isPremium = membershipTier === 'premium'

  return (
    <AppLayout>
      <Head title={t('dashboard', 'matches_title')} />

      <div className="max-w-6xl mx-auto px-4 py-8">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-bold text-slate-900">{t('dashboard', 'matches_title')}</h1>
            <p className="text-sm text-slate-500 mt-1">{t('dashboard', 'ranked_by_compatibility')}</p>
          </div>
          {!isPremium && (
            <Link href={route('upgrade.plans')}>
              <Button variant="premium" size="sm">
                {t('dashboard', 'unlock_all_matches')}
              </Button>
            </Link>
          )}
        </div>

        {/* Soft prompt when no biodata — don't hard-block */}
        {!hasBiodata && (
          <div className="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-5 flex gap-4 items-start">
            <div className="h-10 w-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
              <TrendingUp size={20} className="text-blue-600" />
            </div>
            <div className="flex-1">
              <p className="font-semibold text-blue-900 text-sm">{t('dashboard', 'matches_need_biodata_title')}</p>
              <p className="text-xs text-blue-700 mt-1">{t('dashboard', 'matches_need_biodata_body')}</p>
            </div>
            <Link href={route('biodata.wizard', { step: 1 })}>
              <Button size="sm">{t('dashboard', 'start_biodata')}</Button>
            </Link>
          </div>
        )}

        {matches.length === 0 ? (
          <div className="text-center py-20">
            <div className="text-5xl mb-4">💑</div>
            <p className="text-slate-500">
              {hasBiodata
                ? t('dashboard', 'no_matches')
                : t('dashboard', 'matches_need_biodata_body')}
            </p>
            {!hasBiodata && (
              <Link href={route('biodata.wizard', { step: 1 })} className="mt-4 inline-block">
                <Button size="lg">{t('dashboard', 'start_biodata')}</Button>
              </Link>
            )}
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {matches.map(profile => (
              <div key={profile.registration_id}>
                <ProfileCard profile={profile} />
                {profile.score_breakdown && Object.keys(profile.score_breakdown).length > 0 && (
                  <ScoreBreakdown breakdown={profile.score_breakdown} t={t} />
                )}
              </div>
            ))}
          </div>
        )}

        {!isPremium && matches.length >= 10 && (
          <div className="mt-10 rounded-2xl bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 p-8 text-center">
            <p className="text-lg font-bold text-amber-900 mb-2">
              {t('dashboard', 'matches_free_limit')}
            </p>
            <Link href={route('upgrade.plans')}>
              <Button variant="premium">{t('dashboard', 'upgrade_now')} →</Button>
            </Link>
          </div>
        )}
      </div>
    </AppLayout>
  )
}
