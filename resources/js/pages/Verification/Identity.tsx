/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import {
  Shield, CheckCircle2, XCircle, Clock, Mail,
  User, Camera, Star, Phone, AlertTriangle, ChevronRight,
} from 'lucide-react'
import { cn } from '@/lib/utils'

interface Props {
  isEmailVerified: boolean
  email: string
  identityStatus: string
  biodataStatus: string | null
  biodataComplete: boolean
  hasPhotos: boolean
  isPremium: boolean
  membershipStatus: string
}

type ItemStatus = 'done' | 'pending' | 'rejected' | 'missing' | 'soon'

function CheckItem({
  status, label, hint, cta, ctaHref, ctaAction,
}: {
  status: ItemStatus
  label: string
  hint?: string
  cta?: string
  ctaHref?: string
  ctaAction?: () => void
}) {
  const icon = {
    done:     <CheckCircle2 size={18} className="text-emerald-500" />,
    pending:  <Clock size={18} className="text-amber-500" />,
    rejected: <XCircle size={18} className="text-red-500" />,
    missing:  <XCircle size={18} className="text-slate-400" />,
    soon:     <Clock size={18} className="text-slate-400" />,
  }[status]

  const bar = {
    done:     'border-l-emerald-400',
    pending:  'border-l-amber-400',
    rejected: 'border-l-red-400',
    missing:  'border-l-slate-300',
    soon:     'border-l-slate-300',
  }[status]

  return (
    <div className={cn('flex items-start gap-4 p-4 rounded-xl border border-slate-100 bg-white border-l-4', bar)}>
      <div className="shrink-0 mt-0.5">{icon}</div>
      <div className="flex-1 min-w-0">
        <p className={cn(
          'text-sm font-semibold',
          status === 'done' ? 'text-slate-900' : status === 'rejected' ? 'text-red-700' : 'text-slate-700',
        )}>
          {label}
        </p>
        {hint && <p className="text-xs text-slate-500 mt-0.5 leading-relaxed">{hint}</p>}
      </div>
      {cta && (ctaHref || ctaAction) && (
        <div className="shrink-0">
          {ctaHref ? (
            <Link href={ctaHref}>
              <Button size="sm" variant="outline" className="text-xs gap-1">
                {cta} <ChevronRight size={12} />
              </Button>
            </Link>
          ) : (
            <Button size="sm" variant="outline" className="text-xs gap-1" onClick={ctaAction}>
              {cta} <ChevronRight size={12} />
            </Button>
          )}
        </div>
      )}
    </div>
  )
}

function ComingSoonBadge({ label }: { label: string }) {
  return (
    <span className="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-500 text-xs px-2.5 py-0.5 font-medium">
      <Clock size={10} />
      {label}
    </span>
  )
}

export default function IdentityVerification({
  isEmailVerified, email, identityStatus, biodataStatus,
  biodataComplete, hasPhotos, isPremium, membershipStatus,
}: Props) {
  const { t } = useTranslation()

  const biodataItemStatus = (): ItemStatus => {
    if (biodataStatus === 'approved') return 'done'
    if (biodataStatus === 'pending') return 'pending'
    if (biodataStatus === 'rejected') return 'rejected'
    if (biodataComplete) return 'pending'
    return 'missing'
  }

  const biodataItemLabel = () => {
    if (biodataStatus === 'approved') return t('verification', 'biodata_approved')
    if (biodataStatus === 'pending') return t('verification', 'biodata_pending')
    if (biodataStatus === 'rejected') return t('verification', 'biodata_rejected')
    if (biodataComplete) return t('verification', 'biodata_pending')
    return t('verification', 'biodata_incomplete')
  }

  const identityItemStatus = (): ItemStatus => {
    if (identityStatus === 'verified') return 'done'
    if (identityStatus === 'pending_review') return 'pending'
    if (identityStatus === 'rejected') return 'rejected'
    return 'missing'
  }

  const identityItemLabel = () => {
    if (identityStatus === 'verified') return t('verification', 'identity_verified')
    if (identityStatus === 'pending_review') return t('verification', 'identity_pending')
    if (identityStatus === 'rejected') return t('verification', 'identity_rejected')
    return t('verification', 'identity_unverified')
  }

  const completedCount = [
    isEmailVerified,
    biodataStatus === 'approved',
    hasPhotos,
    isPremium,
  ].filter(Boolean).length

  return (
    <AppLayout>
      <Head title={t('verification', 'title')} />

      <div className="max-w-2xl mx-auto px-4 py-8 space-y-5">

        {/* Header */}
        <div className="rounded-2xl border border-primary-200 bg-gradient-to-br from-primary-50 to-white p-6">
          <div className="flex items-start gap-4">
            <div className="h-14 w-14 rounded-2xl bg-primary-100 flex items-center justify-center shrink-0">
              <Shield size={28} className="text-primary-600" />
            </div>
            <div className="flex-1">
              <h1 className="text-xl font-bold text-slate-900">{t('verification', 'title')}</h1>
              <p className="text-sm text-slate-500 mt-1">{t('verification', 'subtitle')}</p>
              {/* Progress bar */}
              <div className="mt-3">
                <div className="flex justify-between text-xs text-slate-500 mb-1">
                  <span>{t('verification', 'checklist_title')}</span>
                  <span>{completedCount} / 4</span>
                </div>
                <div className="h-2 bg-slate-200 rounded-full overflow-hidden">
                  <div
                    className="h-full bg-primary-500 rounded-full transition-all"
                    style={{ width: `${(completedCount / 4) * 100}%` }}
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Checklist */}
        <div className="space-y-3">

          {/* Email */}
          <CheckItem
            status={isEmailVerified ? 'done' : 'missing'}
            label={isEmailVerified ? t('verification', 'email_verified') : t('verification', 'email_not_verified')}
            hint={isEmailVerified ? undefined : t('verification', 'email_hint')}
            cta={isEmailVerified ? undefined : t('verification', 'email_verify_cta')}
            ctaHref={isEmailVerified ? undefined : route('verification.notice')}
          />

          {/* Biodata */}
          <CheckItem
            status={biodataItemStatus()}
            label={biodataItemLabel()}
            cta={(!biodataComplete || biodataStatus === 'rejected') ? t('verification', 'biodata_cta') : undefined}
            ctaHref={(!biodataComplete || biodataStatus === 'rejected') ? route('biodata.wizard') : undefined}
            hint={biodataStatus === 'approved' ? t('verification', 'biodata_approved_hint') : undefined}
          />

          {/* Photo */}
          <CheckItem
            status={hasPhotos ? 'done' : 'missing'}
            label={hasPhotos ? t('verification', 'photo_uploaded') : t('verification', 'photo_missing')}
            hint={hasPhotos ? undefined : t('verification', 'photo_hint')}
            cta={hasPhotos ? undefined : t('verification', 'photo_cta')}
            ctaHref={hasPhotos ? undefined : route('profile.photos.index')}
          />

          {/* Membership */}
          <CheckItem
            status={isPremium ? 'done' : 'missing'}
            label={isPremium ? t('verification', 'premium_active') : t('verification', 'premium_inactive')}
            hint={isPremium ? undefined : t('verification', 'premium_hint')}
            cta={isPremium ? undefined : t('verification', 'premium_cta')}
            ctaHref={isPremium ? undefined : route('upgrade.plans')}
          />
        </div>

        {/* Phone — coming soon */}
        <div className="rounded-2xl border border-slate-200 bg-white p-5">
          <div className="flex items-center gap-3 mb-2">
            <Phone size={18} className="text-slate-400" />
            <h3 className="text-sm font-semibold text-slate-700">{t('verification', 'phone_title')}</h3>
            <ComingSoonBadge label={t('verification', 'identity_coming_soon')} />
          </div>
          <p className="text-xs text-slate-400 ml-7">{t('verification', 'phone_coming_soon')}</p>
        </div>

        {/* Identity verification */}
        <div className="rounded-2xl border border-slate-200 bg-white p-5">
          <div className="flex items-center gap-3 mb-3">
            <User size={18} className={identityStatus === 'verified' ? 'text-emerald-500' : identityStatus === 'pending_review' ? 'text-amber-500' : 'text-slate-400'} />
            <h3 className="text-sm font-semibold text-slate-700">{t('verification', 'identity_title')}</h3>
            {identityStatus !== 'verified' && identityStatus !== 'pending_review' && (
              <ComingSoonBadge label={t('verification', 'identity_coming_soon')} />
            )}
            {identityStatus === 'pending_review' && (
              <span className="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-700 text-xs px-2.5 py-0.5 font-medium">
                <Clock size={10} />
                {t('verification', 'identity_pending')}
              </span>
            )}
            {identityStatus === 'verified' && (
              <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-700 text-xs px-2.5 py-0.5 font-medium">
                <CheckCircle2 size={10} />
                {t('verification', 'identity_verified')}
              </span>
            )}
          </div>

          <p className="text-xs text-slate-500 mb-3 leading-relaxed">{t('verification', 'identity_badge_desc')}</p>

          {identityStatus !== 'verified' && (
            <div className="rounded-xl bg-amber-50 border border-amber-200 p-4">
              <p className="text-xs font-semibold text-amber-900 mb-1">{t('verification', 'identity_coming_soon')}</p>
              <p className="text-xs text-amber-800 leading-relaxed">{t('verification', 'identity_manual_desc')}</p>
              <p className="text-xs text-amber-700 mt-2">
                <span className="font-medium">{t('verification', 'identity_email_label')}</span>{' '}
                <a href="mailto:verify@heavenlymatch.com" className="font-semibold underline">
                  verify@heavenlymatch.com
                </a>
              </p>
            </div>
          )}
        </div>

        {/* Why verify */}
        <div className="rounded-2xl border border-blue-100 bg-blue-50 p-5">
          <h3 className="text-sm font-semibold text-blue-900 mb-3">{t('verification', 'why_title')}</h3>
          <ul className="space-y-2">
            {([
              ['why_trust', 'Shield'],
              ['why_matches', 'Star'],
              ['why_interest', 'Heart'],
              ['why_safety', 'Check'],
            ] as const).map(([key]) => (
              <li key={key} className="flex items-start gap-2 text-xs text-blue-800">
                <CheckCircle2 size={13} className="text-blue-500 shrink-0 mt-0.5" />
                {t('verification', key)}
              </li>
            ))}
          </ul>
        </div>

        {/* Safety notice */}
        <div className="rounded-2xl border border-slate-200 bg-slate-50 p-5">
          <div className="flex items-center gap-2 mb-3">
            <AlertTriangle size={16} className="text-amber-500" />
            <h3 className="text-sm font-semibold text-slate-800">{t('verification', 'safety_title')}</h3>
          </div>
          <ul className="space-y-1.5">
            <li className="text-xs text-slate-600">{t('verification', 'safety_privacy')}</li>
            <li className="text-xs text-slate-600">{t('verification', 'safety_photo')}</li>
            <li className="text-xs text-slate-600">{t('verification', 'safety_report')}</li>
          </ul>
        </div>

        {/* Back */}
        <Link href={route('dashboard')}>
          <Button variant="outline" className="w-full">
            {t('verification', 'cta_back')}
          </Button>
        </Link>

      </div>
    </AppLayout>
  )
}
