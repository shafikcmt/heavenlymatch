/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import { Shield, CheckCircle, Lock, Users, CreditCard, MapPin, Eye, UserCheck } from 'lucide-react'
import MarketingLayout from '@/layouts/MarketingLayout'
import { useTranslation } from '@/lib/i18n'

const STATS = [
  { key: 'home_stat_members',   value: '50,000+' },
  { key: 'home_stat_marriages', value: '3,200+' },
  { key: 'home_stat_daily',     value: '200+' },
  { key: 'home_stat_rating',    value: '94%' },
] as const

const GENERAL_FEATURES = [
  'home_mode_general_f1',
  'home_mode_general_f2',
  'home_mode_general_f3',
  'home_mode_general_f4',
] as const

const ISLAMIC_FEATURES = [
  'home_mode_islamic_f1',
  'home_mode_islamic_f2',
  'home_mode_islamic_f3',
  'home_mode_islamic_f4',
] as const

const TRUST_FEATURES = [
  { icon: Lock,      titleKey: 'home_trust_privacy_title', descKey: 'home_trust_privacy_desc' },
  { icon: UserCheck, titleKey: 'home_trust_verified_title', descKey: 'home_trust_verified_desc' },
  { icon: Users,     titleKey: 'home_trust_guardian_title', descKey: 'home_trust_guardian_desc' },
  { icon: Shield,    titleKey: 'home_trust_halal_title',    descKey: 'home_trust_halal_desc' },
  { icon: CreditCard,titleKey: 'home_trust_payment_title',  descKey: 'home_trust_payment_desc' },
  { icon: MapPin,    titleKey: 'home_trust_bd_title',       descKey: 'home_trust_bd_desc' },
] as const

const STEPS = [
  { n: '1', icon: '📝', titleKey: 'home_step1_title', descKey: 'home_step1_desc' },
  { n: '2', icon: '✅', titleKey: 'home_step2_title', descKey: 'home_step2_desc' },
  { n: '3', icon: '🔍', titleKey: 'home_step3_title', descKey: 'home_step3_desc' },
  { n: '4', icon: '💌', titleKey: 'home_step4_title', descKey: 'home_step4_desc' },
] as const

export default function Home() {
  const { t } = useTranslation()

  return (
    <MarketingLayout>
      <Head title="HeavenlyMatch — Halal Matrimony for Bangladeshi Muslims" />

      {/* ── Hero ── */}
      <section className="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-emerald-50 py-20 px-4">
        <div className="max-w-4xl mx-auto text-center">
          <div className="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 rounded-full px-4 py-1.5 text-sm font-medium mb-6">
            <Shield size={14} />
            {t('marketing', 'home_trust_badge')}
          </div>

          <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-tight mb-6">
            {t('marketing', 'home_hero_title')}{' '}
            <span className="bg-gradient-to-r from-primary-600 to-violet-600 bg-clip-text text-transparent">
              {t('marketing', 'home_hero_highlight')}
            </span>
          </h1>

          <p className="text-lg text-slate-600 max-w-2xl mx-auto mb-10">
            {t('marketing', 'home_hero_subtitle')}
          </p>

          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Link
              href={route('register')}
              className="inline-flex items-center justify-center gap-2 h-14 px-8 text-lg font-semibold rounded-xl bg-primary-600 text-white hover:bg-primary-700 shadow-lg shadow-primary-200 transition-colors"
            >
              {t('marketing', 'home_cta_register')} →
            </Link>
            <Link
              href={route('how-it-works')}
              className="inline-flex items-center justify-center gap-2 h-14 px-8 text-lg font-semibold rounded-xl border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 transition-colors"
            >
              {t('marketing', 'home_cta_how')}
            </Link>
          </div>

          <p className="mt-4 text-xs text-slate-400">{t('marketing', 'home_cta_subtitle')}</p>
        </div>

        {/* Stats */}
        <div className="max-w-4xl mx-auto mt-16 grid grid-cols-2 sm:grid-cols-4 gap-4">
          {STATS.map(({ key, value }) => (
            <div key={key} className="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
              <p className="text-2xl font-extrabold text-primary-600">{value}</p>
              <p className="text-xs text-slate-500 mt-1">{t('marketing', key)}</p>
            </div>
          ))}
        </div>
      </section>

      {/* ── Two modes ── */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-5xl mx-auto">
          <h2 className="text-3xl font-bold text-slate-900 text-center mb-3">
            {t('marketing', 'home_modes_title')}
          </h2>
          <p className="text-slate-500 text-center mb-12 max-w-xl mx-auto">
            {t('marketing', 'home_modes_subtitle')}
          </p>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* General */}
            <div className="rounded-3xl border-2 border-blue-200 bg-blue-50 p-8">
              <div className="text-4xl mb-4">🌐</div>
              <h3 className="text-xl font-bold text-slate-900 mb-2">{t('marketing', 'home_mode_general_title')}</h3>
              <p className="text-slate-600 text-sm mb-5">{t('marketing', 'home_mode_general_desc')}</p>
              <ul className="space-y-2.5">
                {GENERAL_FEATURES.map(key => (
                  <li key={key} className="flex items-start gap-2 text-sm text-slate-700">
                    <CheckCircle size={16} className="text-blue-500 flex-shrink-0 mt-0.5" />
                    {t('marketing', key)}
                  </li>
                ))}
              </ul>
            </div>

            {/* Islamic */}
            <div className="rounded-3xl border-2 border-emerald-400 bg-emerald-50 p-8 relative">
              <div className="absolute -top-3 right-6 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                {t('marketing', 'home_mode_islamic_badge')}
              </div>
              <div className="text-4xl mb-4">☪️</div>
              <h3 className="text-xl font-bold text-slate-900 mb-2">{t('marketing', 'home_mode_islamic_title')}</h3>
              <p className="text-slate-600 text-sm mb-5">{t('marketing', 'home_mode_islamic_desc')}</p>
              <ul className="space-y-2.5">
                {ISLAMIC_FEATURES.map(key => (
                  <li key={key} className="flex items-start gap-2 text-sm text-slate-700">
                    <CheckCircle size={16} className="text-emerald-500 flex-shrink-0 mt-0.5" />
                    {t('marketing', key)}
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>
      </section>

      {/* ── Trust features ── */}
      <section className="py-20 px-4 bg-slate-50">
        <div className="max-w-5xl mx-auto">
          <h2 className="text-3xl font-bold text-slate-900 text-center mb-3">
            {t('marketing', 'home_trust_title')}
          </h2>
          <p className="text-slate-500 text-center mb-12 max-w-xl mx-auto">
            {t('marketing', 'home_trust_subtitle')}
          </p>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {TRUST_FEATURES.map(({ icon: Icon, titleKey, descKey }) => (
              <div key={titleKey} className="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div className="h-10 w-10 rounded-xl bg-primary-50 flex items-center justify-center mb-4">
                  <Icon size={20} className="text-primary-600" />
                </div>
                <h3 className="font-semibold text-slate-900 mb-2">{t('marketing', titleKey)}</h3>
                <p className="text-sm text-slate-500 leading-relaxed">{t('marketing', descKey)}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── Steps ── */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-5xl mx-auto">
          <h2 className="text-3xl font-bold text-slate-900 text-center mb-14">
            {t('marketing', 'home_steps_title')}
          </h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {STEPS.map(({ n, icon, titleKey, descKey }) => (
              <div key={n} className="text-center">
                <div className="relative inline-block mb-4">
                  <div className="h-16 w-16 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-3xl mx-auto shadow-sm">
                    {icon}
                  </div>
                  <span className="absolute -top-2 -right-2 h-6 w-6 rounded-full bg-primary-600 text-white text-xs font-bold flex items-center justify-center shadow">
                    {n}
                  </span>
                </div>
                <h3 className="font-bold text-slate-900 mb-2">{t('marketing', titleKey)}</h3>
                <p className="text-sm text-slate-500 leading-relaxed">{t('marketing', descKey)}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── Final CTA ── */}
      <section className="py-20 px-4 bg-gradient-to-r from-primary-600 to-violet-600 text-white">
        <div className="max-w-2xl mx-auto text-center">
          <h2 className="text-3xl font-bold mb-4">{t('marketing', 'home_final_title')}</h2>
          <p className="text-primary-100 mb-8 text-lg">{t('marketing', 'home_final_subtitle')}</p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Link
              href={route('register')}
              className="inline-flex items-center justify-center h-14 px-8 text-lg font-semibold rounded-xl bg-white text-primary-600 hover:bg-slate-50 shadow-xl transition-colors"
            >
              {t('marketing', 'home_final_cta')} →
            </Link>
            <Link
              href={route('pricing')}
              className="inline-flex items-center justify-center h-14 px-8 text-lg font-semibold rounded-xl border border-white/40 text-white hover:bg-white/10 transition-colors"
            >
              {t('marketing', 'home_view_pricing')}
            </Link>
          </div>
        </div>
      </section>
    </MarketingLayout>
  )
}
