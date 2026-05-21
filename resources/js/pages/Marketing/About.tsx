/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import { Heart, Shield, Users, MapPin } from 'lucide-react'
import MarketingLayout from '@/layouts/MarketingLayout'
import { useTranslation } from '@/lib/i18n'

const VALUES = [
  { icon: Shield, titleKey: 'about_value_halal_title',   descKey: 'about_value_halal_desc' },
  { icon: Heart,  titleKey: 'about_value_privacy_title', descKey: 'about_value_privacy_desc' },
  { icon: Users,  titleKey: 'about_value_family_title',  descKey: 'about_value_family_desc' },
  { icon: MapPin, titleKey: 'about_value_bd_title',      descKey: 'about_value_bd_desc' },
] as const

const STATS = [
  { value: '3,200+', key: 'about_trust_stat1' },
  { value: '50,000+', key: 'about_trust_stat2' },
  { value: '15+',  key: 'about_trust_stat3' },
  { value: '94%',  key: 'about_trust_stat4' },
] as const

export default function About() {
  const { t } = useTranslation()

  return (
    <MarketingLayout>
      <Head title={t('marketing', 'about_meta_title')} />

      {/* Hero */}
      <section className="bg-gradient-to-br from-primary-50 via-white to-emerald-50 py-20 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <div className="inline-flex items-center gap-2 bg-primary-100 text-primary-700 rounded-full px-4 py-1.5 text-sm font-medium mb-6">
            <Heart size={14} className="fill-primary-500" />
            HeavenlyMatch
          </div>
          <h1 className="text-4xl sm:text-5xl font-extrabold text-slate-900 leading-tight mb-2">
            {t('marketing', 'about_hero_title')}
          </h1>
          <h1 className="text-4xl sm:text-5xl font-extrabold bg-gradient-to-r from-primary-600 to-violet-600 bg-clip-text text-transparent mb-6">
            {t('marketing', 'about_hero_title2')}
          </h1>
          <p className="text-lg text-slate-600 max-w-2xl mx-auto">
            {t('marketing', 'about_hero_subtitle')}
          </p>
        </div>
      </section>

      {/* Mission */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-3xl mx-auto">
          <h2 className="text-2xl font-bold text-slate-900 mb-6">{t('marketing', 'about_mission_title')}</h2>
          <p className="text-slate-600 leading-relaxed mb-5">{t('marketing', 'about_mission_p1')}</p>
          <p className="text-slate-600 leading-relaxed">{t('marketing', 'about_mission_p2')}</p>
        </div>
      </section>

      {/* Stats */}
      <section className="py-16 px-4 bg-slate-50">
        <div className="max-w-4xl mx-auto">
          <h2 className="text-2xl font-bold text-slate-900 text-center mb-10">{t('marketing', 'about_trust_title')}</h2>
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-5">
            {STATS.map(({ value, key }) => (
              <div key={key} className="bg-white rounded-2xl border border-slate-200 p-6 text-center shadow-sm">
                <p className="text-3xl font-extrabold text-primary-600 mb-1">{value}</p>
                <p className="text-sm text-slate-500">{t('marketing', key)}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Values */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-5xl mx-auto">
          <h2 className="text-2xl font-bold text-slate-900 text-center mb-12">{t('marketing', 'about_values_title')}</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
            {VALUES.map(({ icon: Icon, titleKey, descKey }) => (
              <div key={titleKey} className="bg-slate-50 rounded-2xl border border-slate-200 p-7">
                <div className="h-10 w-10 rounded-xl bg-primary-100 flex items-center justify-center mb-4">
                  <Icon size={20} className="text-primary-600" />
                </div>
                <h3 className="font-bold text-slate-900 mb-2">{t('marketing', titleKey)}</h3>
                <p className="text-sm text-slate-600 leading-relaxed">{t('marketing', descKey)}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Story */}
      <section className="py-20 px-4 bg-gradient-to-br from-emerald-50 to-primary-50">
        <div className="max-w-3xl mx-auto">
          <h2 className="text-2xl font-bold text-slate-900 mb-6">{t('marketing', 'about_story_title')}</h2>
          <p className="text-slate-600 leading-relaxed mb-5">{t('marketing', 'about_story_p1')}</p>
          <p className="text-slate-600 leading-relaxed mb-10">{t('marketing', 'about_story_p2')}</p>
          <Link
            href={route('register')}
            className="inline-flex items-center justify-center h-12 px-8 font-semibold rounded-xl bg-primary-600 text-white hover:bg-primary-700 shadow-lg transition-colors"
          >
            {t('marketing', 'home_cta_register')} →
          </Link>
        </div>
      </section>
    </MarketingLayout>
  )
}
