/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import { Shield, Eye, Users, CheckCircle } from 'lucide-react'
import MarketingLayout from '@/layouts/MarketingLayout'
import { useTranslation } from '@/lib/i18n'

const GENERAL_STEPS = [
  { n: '1', icon: '📝', titleKey: 'hiw_general_s1_title', descKey: 'hiw_general_s1_desc' },
  { n: '2', icon: '🔍', titleKey: 'hiw_general_s2_title', descKey: 'hiw_general_s2_desc' },
  { n: '3', icon: '💌', titleKey: 'hiw_general_s3_title', descKey: 'hiw_general_s3_desc' },
  { n: '4', icon: '💬', titleKey: 'hiw_general_s4_title', descKey: 'hiw_general_s4_desc' },
] as const

const ISLAMIC_STEPS = [
  { n: '1', icon: '📄', titleKey: 'hiw_islamic_s1_title', descKey: 'hiw_islamic_s1_desc' },
  { n: '2', icon: '🖼️', titleKey: 'hiw_islamic_s2_title', descKey: 'hiw_islamic_s2_desc' },
  { n: '3', icon: '👨‍👩‍👧', titleKey: 'hiw_islamic_s3_title', descKey: 'hiw_islamic_s3_desc' },
  { n: '4', icon: '🤝', titleKey: 'hiw_islamic_s4_title', descKey: 'hiw_islamic_s4_desc' },
] as const

const PRIVACY_POINTS = ['hiw_privacy_p1', 'hiw_privacy_p2', 'hiw_privacy_p3'] as const

const FAQS = [
  { q: 'hiw_faq_q1', a: 'hiw_faq_a1' },
  { q: 'hiw_faq_q2', a: 'hiw_faq_a2' },
  { q: 'hiw_faq_q3', a: 'hiw_faq_a3' },
  { q: 'hiw_faq_q4', a: 'hiw_faq_a4' },
] as const

export default function HowItWorks() {
  const { t } = useTranslation()

  return (
    <MarketingLayout>
      <Head title={t('marketing', 'hiw_meta_title')} />

      {/* Hero */}
      <section className="bg-gradient-to-br from-emerald-50 via-white to-primary-50 py-20 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <div className="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 rounded-full px-4 py-1.5 text-sm font-medium mb-6">
            <Shield size={14} />
            {t('marketing', 'home_trust_badge')}
          </div>
          <h1 className="text-4xl sm:text-5xl font-extrabold text-slate-900 leading-tight mb-6">
            {t('marketing', 'hiw_hero_title')}
          </h1>
          <p className="text-lg text-slate-600 mb-10 max-w-xl mx-auto">
            {t('marketing', 'hiw_hero_subtitle')}
          </p>
          <Link
            href={route('register')}
            className="inline-flex items-center justify-center h-14 px-8 text-lg font-semibold rounded-xl bg-primary-600 text-white hover:bg-primary-700 shadow-lg shadow-primary-200 transition-colors"
          >
            {t('marketing', 'hiw_cta_register')} →
          </Link>
        </div>
      </section>

      {/* General Mode */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-5xl mx-auto">
          <div className="flex items-center gap-3 mb-3">
            <span className="text-3xl">🌐</span>
            <h2 className="text-2xl font-bold text-slate-900">{t('marketing', 'hiw_general_title')}</h2>
          </div>
          <p className="text-slate-500 mb-10 max-w-2xl">{t('marketing', 'hiw_general_desc')}</p>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {GENERAL_STEPS.map(({ n, icon, titleKey, descKey }) => (
              <div key={n} className="relative">
                <div className="bg-blue-50 border border-blue-100 rounded-2xl p-6 h-full">
                  <div className="flex items-center gap-3 mb-3">
                    <span className="h-8 w-8 rounded-full bg-blue-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">
                      {n}
                    </span>
                    <span className="text-2xl">{icon}</span>
                  </div>
                  <h3 className="font-semibold text-slate-900 mb-2">{t('marketing', titleKey)}</h3>
                  <p className="text-sm text-slate-600 leading-relaxed">{t('marketing', descKey)}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Islamic Mode */}
      <section className="py-20 px-4 bg-emerald-50">
        <div className="max-w-5xl mx-auto">
          <div className="flex items-center gap-3 mb-3">
            <span className="text-3xl">☪️</span>
            <h2 className="text-2xl font-bold text-slate-900">{t('marketing', 'hiw_islamic_title')}</h2>
          </div>
          <p className="text-slate-500 mb-10 max-w-2xl">{t('marketing', 'hiw_islamic_desc')}</p>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {ISLAMIC_STEPS.map(({ n, icon, titleKey, descKey }) => (
              <div key={n} className="bg-white border border-emerald-200 rounded-2xl p-6 h-full">
                <div className="flex items-center gap-3 mb-3">
                  <span className="h-8 w-8 rounded-full bg-emerald-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">
                    {n}
                  </span>
                  <span className="text-2xl">{icon}</span>
                </div>
                <h3 className="font-semibold text-slate-900 mb-2">{t('marketing', titleKey)}</h3>
                <p className="text-sm text-slate-600 leading-relaxed">{t('marketing', descKey)}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Privacy */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-3xl mx-auto">
          <div className="flex items-center gap-3 mb-8">
            <div className="h-10 w-10 rounded-xl bg-primary-50 flex items-center justify-center">
              <Eye size={20} className="text-primary-600" />
            </div>
            <h2 className="text-2xl font-bold text-slate-900">{t('marketing', 'hiw_privacy_title')}</h2>
          </div>
          <div className="space-y-4">
            {PRIVACY_POINTS.map(key => (
              <div key={key} className="flex items-start gap-3">
                <CheckCircle size={18} className="text-emerald-500 flex-shrink-0 mt-0.5" />
                <p className="text-slate-600 leading-relaxed">{t('marketing', key)}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* FAQ */}
      <section className="py-20 px-4 bg-slate-50">
        <div className="max-w-3xl mx-auto">
          <h2 className="text-2xl font-bold text-slate-900 mb-10">{t('marketing', 'hiw_faq_title')}</h2>
          <div className="space-y-4">
            {FAQS.map(({ q, a }) => (
              <div key={q} className="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
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
          <p className="text-primary-100 mb-8">{t('marketing', 'home_final_subtitle')}</p>
          <Link
            href={route('register')}
            className="inline-flex items-center justify-center h-12 px-8 font-semibold rounded-xl bg-white text-primary-600 hover:bg-slate-50 shadow-xl transition-colors"
          >
            {t('marketing', 'hiw_cta_register')} →
          </Link>
        </div>
      </section>
    </MarketingLayout>
  )
}
