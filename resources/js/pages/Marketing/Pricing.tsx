/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import { CheckCircle, CreditCard } from 'lucide-react'
import { useState } from 'react'
import MarketingLayout from '@/layouts/MarketingLayout'
import { useTranslation } from '@/lib/i18n'
import { cn } from '@/lib/utils'

// Shape coming from MarketingController::pricing()
interface PlanRow {
  id: number
  name: string
  slug: string
  price: string
  duration_months: number
  is_popular: boolean
  badge: string | null
}

interface Props {
  plans: Record<string, PlanRow[]>
}

// Static feature lists per plan tier (from marketing translations)
const PLAN_FEATURES: Record<string, string[]> = {
  Free:     ['pricing_free_f1', 'pricing_free_f2', 'pricing_free_f3', 'pricing_free_f4', 'pricing_free_f5'],
  Gold:     ['pricing_gold_f1', 'pricing_gold_f2', 'pricing_gold_f3', 'pricing_gold_f4', 'pricing_gold_f5', 'pricing_gold_f6'],
  Diamond:  ['pricing_diamond_f1', 'pricing_diamond_f2', 'pricing_diamond_f3', 'pricing_diamond_f4', 'pricing_diamond_f5', 'pricing_diamond_f6'],
  Platinum: ['pricing_platinum_f1', 'pricing_platinum_f2', 'pricing_platinum_f3', 'pricing_platinum_f4', 'pricing_platinum_f5', 'pricing_platinum_f6'],
}

const PLAN_COLORS: Record<string, string> = {
  Free:     'border-slate-200',
  Gold:     'border-amber-300',
  Diamond:  'border-blue-400',
  Platinum: 'border-violet-400',
}

const PLAN_BADGE_COLORS: Record<string, string> = {
  Gold:     'bg-amber-500',
  Diamond:  'bg-blue-600',
  Platinum: 'bg-violet-600',
}

const DURATION_MONTHS = [1, 3, 6, 12] as const
type Duration = typeof DURATION_MONTHS[number]

const DURATION_KEYS: Record<Duration, string> = {
  1: 'pricing_month_1',
  3: 'pricing_month_3',
  6: 'pricing_month_6',
  12: 'pricing_month_12',
}

const FAQS = [
  { q: 'pricing_faq_q1', a: 'pricing_faq_a1' },
  { q: 'pricing_faq_q2', a: 'pricing_faq_a2' },
  { q: 'pricing_faq_q3', a: 'pricing_faq_a3' },
] as const

export default function Pricing({ plans }: Props) {
  const { t } = useTranslation()

  // Available durations based on what's in the DB
  const dbDurations = Array.from(
    new Set(
      Object.values(plans)
        .flat()
        .map(p => p.duration_months as Duration)
    )
  ).sort((a, b) => a - b)

  const availableDurations: Duration[] = dbDurations.length > 0
    ? dbDurations
    : [3]

  const [duration, setDuration] = useState<Duration>(
    availableDurations.includes(3) ? 3 : availableDurations[0]!
  )

  // Plan names in display order
  const tierOrder = ['Free', 'Gold', 'Diamond', 'Platinum']
  const activeTierNames = tierOrder.filter(name => name === 'Free' || plans[name])

  return (
    <MarketingLayout>
      <Head title={t('marketing', 'pricing_meta_title')} />

      {/* Hero */}
      <section className="bg-gradient-to-br from-primary-50 via-white to-violet-50 py-20 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <h1 className="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-5">
            {t('marketing', 'pricing_hero_title')}
          </h1>
          <p className="text-lg text-slate-600 max-w-xl mx-auto">
            {t('marketing', 'pricing_hero_subtitle')}
          </p>
        </div>
      </section>

      {/* Duration tabs */}
      {availableDurations.length > 1 && (
        <section className="py-8 px-4 bg-white border-b border-slate-100">
          <div className="max-w-xl mx-auto">
            <p className="text-sm font-medium text-slate-500 text-center mb-4">
              {t('marketing', 'pricing_duration_label')}
            </p>
            <div className="flex items-center justify-center gap-2 flex-wrap">
              {availableDurations.map(d => (
                <button
                  key={d}
                  onClick={() => setDuration(d)}
                  className={cn(
                    'px-4 py-2 rounded-xl text-sm font-semibold transition-colors border',
                    duration === d
                      ? 'bg-primary-600 text-white border-primary-600 shadow-sm'
                      : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'
                  )}
                >
                  {t('marketing', DURATION_KEYS[d])}
                </button>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Plan cards */}
      <section className="py-16 px-4 bg-white">
        <div className="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 items-stretch">
          {activeTierNames.map(tierName => {
            const isFreeTier = tierName === 'Free'
            const tierRows = plans[tierName] ?? []
            const selectedRow = tierRows.find(r => r.duration_months === duration) ?? tierRows[0]
            const isPopular = selectedRow?.is_popular ?? false
            const badge = selectedRow?.badge ?? null

            const priceDisplay = isFreeTier
              ? t('marketing', 'pricing_free_price')
              : selectedRow
                ? `৳${Number(selectedRow.price).toLocaleString('en-IN')}`
                : '—'

            const perMonthDisplay = isFreeTier
              ? t('marketing', 'pricing_free_tagline')
              : selectedRow
                ? `৳${Math.round(Number(selectedRow.price) / duration).toLocaleString('en-IN')}${t('marketing', 'pricing_per_month')}`
                : ''

            const features = PLAN_FEATURES[tierName] ?? []

            return (
              <div
                key={tierName}
                className={cn(
                  'relative rounded-3xl border-2 p-7 flex flex-col shadow-sm transition-shadow hover:shadow-md',
                  isPopular ? 'border-blue-400 bg-blue-50' : PLAN_COLORS[tierName] ?? 'border-slate-200',
                )}
              >
                {/* Badge */}
                {(badge || isPopular) && (
                  <div className={cn(
                    'absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full text-xs font-bold text-white whitespace-nowrap',
                    PLAN_BADGE_COLORS[tierName] ?? 'bg-primary-600'
                  )}>
                    {badge ?? t('marketing', 'pricing_popular')}
                  </div>
                )}

                <div className="mb-6">
                  <h3 className="text-xl font-bold text-slate-900 mb-1">{tierName}</h3>
                  <p className="text-3xl font-extrabold text-slate-900 mt-3">{priceDisplay}</p>
                  <p className="text-sm text-slate-500 mt-1">{perMonthDisplay}</p>
                  {!isFreeTier && selectedRow && duration > 1 && (
                    <p className="text-xs text-slate-400 mt-1">
                      {t('marketing', 'pricing_billed', {
                        amount: Number(selectedRow.price).toLocaleString('en-IN'),
                        n: duration,
                      })}
                    </p>
                  )}
                </div>

                <ul className="space-y-2.5 flex-1 mb-8">
                  {features.map(fKey => (
                    <li key={fKey} className="flex items-start gap-2 text-sm text-slate-700">
                      <CheckCircle size={15} className="text-emerald-500 flex-shrink-0 mt-0.5" />
                      {t('marketing', fKey)}
                    </li>
                  ))}
                </ul>

                {isFreeTier ? (
                  <Link
                    href={route('register')}
                    className="block text-center h-11 leading-[2.75rem] rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition-colors text-sm"
                  >
                    {t('marketing', 'pricing_cta_free')}
                  </Link>
                ) : (
                  <Link
                    href={route('login')}
                    className={cn(
                      'block text-center h-11 leading-[2.75rem] rounded-xl font-semibold transition-colors text-sm',
                      isPopular
                        ? 'bg-blue-600 text-white hover:bg-blue-700'
                        : 'bg-primary-600 text-white hover:bg-primary-700'
                    )}
                  >
                    {t('marketing', 'pricing_cta_upgrade', { plan: tierName })}
                  </Link>
                )}
              </div>
            )
          })}
        </div>
      </section>

      {/* Payment methods */}
      <section className="py-16 px-4 bg-slate-50">
        <div className="max-w-3xl mx-auto text-center">
          <div className="flex items-center justify-center gap-2 mb-4">
            <CreditCard size={20} className="text-primary-600" />
            <h2 className="text-xl font-bold text-slate-900">{t('marketing', 'pricing_payment_title')}</h2>
          </div>
          <p className="text-slate-600 mb-8">{t('marketing', 'pricing_payment_desc')}</p>
          <div className="flex flex-wrap items-center justify-center gap-4">
            {[
              { key: 'pricing_payment_bkash', color: 'bg-pink-50 border-pink-200 text-pink-700', emoji: '📱' },
              { key: 'pricing_payment_nagad',  color: 'bg-orange-50 border-orange-200 text-orange-700', emoji: '📲' },
              { key: 'pricing_payment_bank',   color: 'bg-blue-50 border-blue-200 text-blue-700', emoji: '🏦' },
            ].map(({ key, color, emoji }) => (
              <div key={key} className={cn('rounded-xl border px-6 py-3 flex items-center gap-2 font-semibold text-sm', color)}>
                <span>{emoji}</span>
                {t('marketing', key)}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* FAQ */}
      <section className="py-16 px-4 bg-white">
        <div className="max-w-3xl mx-auto">
          <h2 className="text-xl font-bold text-slate-900 mb-8 text-center">{t('marketing', 'pricing_faq_title')}</h2>
          <div className="space-y-4">
            {FAQS.map(({ q, a }) => (
              <div key={q} className="bg-slate-50 rounded-2xl border border-slate-200 p-6">
                <h3 className="font-semibold text-slate-900 mb-2">{t('marketing', q)}</h3>
                <p className="text-sm text-slate-600 leading-relaxed">{t('marketing', a)}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 px-4 bg-primary-600 text-white text-center">
        <div className="max-w-xl mx-auto">
          <h2 className="text-2xl font-bold mb-4">{t('marketing', 'home_final_title')}</h2>
          <p className="text-primary-100 mb-8">{t('marketing', 'home_hero_subtitle')}</p>
          <Link
            href={route('register')}
            className="inline-flex items-center justify-center h-12 px-8 font-semibold rounded-xl bg-white text-primary-600 hover:bg-slate-50 shadow-xl transition-colors"
          >
            {t('marketing', 'pricing_cta_free')} →
          </Link>
        </div>
      </section>
    </MarketingLayout>
  )
}
