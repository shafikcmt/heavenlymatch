/// <reference path="../../types/ziggy.d.ts" />
import { useState, useRef } from 'react'
import { Link } from '@inertiajs/react'
import {
  Shield, CheckCircle, Lock, Users, CreditCard, MapPin,
  UserCheck, ChevronLeft, ChevronRight, Star, Check, X,
} from 'lucide-react'
import MarketingLayout from '@/layouts/MarketingLayout'
import { useTranslation } from '@/lib/i18n'
import { SeoHead } from '@/components/SeoHead'

// ── Types ─────────────────────────────────────────────────────────────────────

interface FeaturedProfile {
  id: string
  first_name: string
  gender: 'male' | 'female'
  age: number | null
  district: string | null
  occupation: string | null
  avatar_num: number
}

interface Props {
  heroImageUrl?: string | null
  successImageUrl?: string | null
  featuredProfiles?: FeaturedProfile[]
}

// ── Static data ───────────────────────────────────────────────────────────────

const STATS = [
  { key: 'home_stat_members',   value: '50,000+' },
  { key: 'home_stat_marriages', value: '3,200+' },
  { key: 'home_stat_daily',     value: '200+' },
  { key: 'home_stat_rating',    value: '94%' },
] as const

const GENERAL_FEATURES = [
  'home_mode_general_f1', 'home_mode_general_f2',
  'home_mode_general_f3', 'home_mode_general_f4',
] as const

const ISLAMIC_FEATURES = [
  'home_mode_islamic_f1', 'home_mode_islamic_f2',
  'home_mode_islamic_f3', 'home_mode_islamic_f4',
] as const

const TRUST_FEATURES = [
  { icon: Lock,      titleKey: 'home_trust_privacy_title',  descKey: 'home_trust_privacy_desc' },
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

const TESTIMONIAL_KEYS = [
  { initials: 'F.K.', locationKey: 'testimonial_1_location', textKey: 'testimonial_1_text' },
  { initials: 'A.R.', locationKey: 'testimonial_2_location', textKey: 'testimonial_2_text' },
  { initials: 'S.B.', locationKey: 'testimonial_3_location', textKey: 'testimonial_3_text' },
  { initials: 'M.H.', locationKey: 'testimonial_4_location', textKey: 'testimonial_4_text' },
  { initials: 'T.A.', locationKey: 'testimonial_5_location', textKey: 'testimonial_5_text' },
] as const

const COMPARISON_ROWS = [
  { leftKey: 'comparison_row_1_left', rightKey: 'comparison_row_1_right' },
  { leftKey: 'comparison_row_2_left', rightKey: 'comparison_row_2_right' },
  { leftKey: 'comparison_row_3_left', rightKey: 'comparison_row_3_right' },
  { leftKey: 'comparison_row_4_left', rightKey: 'comparison_row_4_right' },
  { leftKey: 'comparison_row_5_left', rightKey: 'comparison_row_5_right' },
] as const

const SUCCESS_FEATURES = [
  'success_feat_1', 'success_feat_2', 'success_feat_3', 'success_feat_4',
] as const

// ── Component ─────────────────────────────────────────────────────────────────

export default function Home({ heroImageUrl, successImageUrl, featuredProfiles = [] }: Props) {
  const { t } = useTranslation()
  const [testimonialIdx, setTestimonialIdx] = useState(0)
  const sliderRef = useRef<HTMLDivElement>(null)

  const prevTestimonial = () => setTestimonialIdx(i => (i === 0 ? TESTIMONIAL_KEYS.length - 1 : i - 1))
  const nextTestimonial = () => setTestimonialIdx(i => (i === TESTIMONIAL_KEYS.length - 1 ? 0 : i + 1))

  const scrollSlider = (dir: 'left' | 'right') => {
    if (!sliderRef.current) return
    sliderRef.current.scrollBy({ left: dir === 'right' ? 220 : -220, behavior: 'smooth' })
  }

  const current = TESTIMONIAL_KEYS[testimonialIdx] ?? TESTIMONIAL_KEYS[0]!

  return (
    <MarketingLayout>
      <SeoHead pageKey="home" />

      {/* ── Hero ── */}
      <section className="relative overflow-hidden py-28 px-4 bg-gradient-to-b from-rose-50 via-white to-primary-50/40">
        {/* Soft ambient blobs */}
        <div className="absolute -top-32 left-1/2 -translate-x-1/2 w-[700px] h-[500px] bg-gradient-to-br from-rose-100/60 via-primary-100/40 to-violet-100/30 rounded-full filter blur-3xl pointer-events-none" />
        <div className="absolute bottom-0 right-0 w-80 h-80 bg-emerald-100/30 rounded-full filter blur-3xl pointer-events-none" />

        <div className="max-w-4xl mx-auto text-center relative z-10">
          {/* Trust badge */}
          <div className="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 rounded-full px-4 py-1.5 text-sm font-semibold mb-8 shadow-sm">
            <Shield size={14} />
            {t('marketing', 'home_trust_badge')}
          </div>

          <h1 className="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-slate-900 leading-[1.1] tracking-tight mb-6">
            {t('marketing', 'home_hero_title')}{' '}
            <span className="bg-gradient-to-r from-primary-600 to-violet-600 bg-clip-text text-transparent">
              {t('marketing', 'home_hero_highlight')}
            </span>
          </h1>

          <p className="text-xl text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed">
            {t('marketing', 'home_hero_subtitle')}
          </p>

          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Link
              href={route('register')}
              className="inline-flex items-center justify-center gap-2 h-14 px-10 text-lg font-semibold rounded-2xl bg-primary-600 text-white hover:bg-primary-700 shadow-xl shadow-primary-200/60 transition-all hover:-translate-y-0.5"
            >
              {t('marketing', 'home_cta_register')} →
            </Link>
            <Link
              href={route('how-it-works')}
              className="inline-flex items-center justify-center gap-2 h-14 px-10 text-lg font-semibold rounded-2xl border-2 border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors shadow-sm"
            >
              {t('marketing', 'home_cta_how')}
            </Link>
          </div>

          <p className="mt-5 text-sm text-slate-400">{t('marketing', 'home_cta_subtitle')}</p>
        </div>

        {/* Stats */}
        <div className="max-w-3xl mx-auto mt-20 grid grid-cols-2 sm:grid-cols-4 gap-4 relative z-10">
          {STATS.map(({ key, value }) => (
            <div key={key} className="rounded-2xl border border-white bg-white/80 backdrop-blur-sm p-5 text-center shadow-md shadow-slate-100">
              <p className="text-2xl font-extrabold text-primary-600">{value}</p>
              <p className="text-xs text-slate-500 mt-1">{t('marketing', key)}</p>
            </div>
          ))}
        </div>
      </section>

      {/* ── Featured Profile Slider ── */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-5xl mx-auto">
          <div className="text-center mb-10">
            <h2 className="text-3xl font-bold text-slate-900 mb-2">{t('marketing', 'featured_profiles_title')}</h2>
            <p className="text-slate-500 text-sm">{t('marketing', 'featured_profiles_subtitle')}</p>
          </div>

          {featuredProfiles.length > 0 ? (
            <div className="relative">
              {/* Left/Right nav arrows */}
              <button
                onClick={() => scrollSlider('left')}
                aria-label="Scroll left"
                className="hidden sm:flex absolute -left-5 top-1/2 -translate-y-1/2 z-10 h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white shadow-md hover:bg-slate-50 transition-colors"
              >
                <ChevronLeft size={18} className="text-slate-600" />
              </button>
              <button
                onClick={() => scrollSlider('right')}
                aria-label="Scroll right"
                className="hidden sm:flex absolute -right-5 top-1/2 -translate-y-1/2 z-10 h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white shadow-md hover:bg-slate-50 transition-colors"
              >
                <ChevronRight size={18} className="text-slate-600" />
              </button>

              {/* Scrollable track */}
              <div
                ref={sliderRef}
                className="flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-3 -mx-4 px-4 sm:mx-0 sm:px-0"
                style={{ scrollbarWidth: 'none' }}
              >
                {featuredProfiles.map(profile => (
                  <div
                    key={profile.id}
                    className="shrink-0 snap-center w-[180px] sm:w-[200px] rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                  >
                    {/* Avatar area */}
                    <div className="h-[160px] bg-slate-50 flex items-center justify-center overflow-hidden">
                      <img
                        src={`/images/marketing/profile-placeholder-${profile.gender}.svg`}
                        alt=""
                        className="h-full w-full object-cover"
                        aria-hidden="true"
                        onError={e => {
                          (e.target as HTMLImageElement).src = `/images/avatar-${profile.gender}.svg`
                        }}
                      />
                    </div>
                    {/* Info */}
                    <div className="p-3">
                      <p className="font-semibold text-slate-900 text-sm truncate">{profile.first_name}</p>
                      <p className="text-xs text-slate-500 mt-0.5">
                        {profile.age ? `${profile.age} ${t('common', 'yrs')}` : '—'}
                        {profile.district ? ` · ${profile.district}` : ''}
                      </p>
                      {profile.occupation && (
                        <p className="text-xs text-slate-400 mt-0.5 truncate">{profile.occupation}</p>
                      )}
                      <Link
                        href={route('register')}
                        className="mt-3 block text-center text-xs font-semibold text-primary-600 hover:text-primary-700 border border-primary-200 rounded-lg py-1.5 hover:bg-primary-50 transition-colors"
                      >
                        {t('marketing', 'view_profile_btn')}
                      </Link>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          ) : (
            /* Empty state */
            <div className="text-center py-10 rounded-2xl border-2 border-dashed border-slate-200">
              <img
                src="/images/marketing/profile-placeholder-male.svg"
                alt=""
                className="h-24 w-24 mx-auto mb-4 opacity-40"
                aria-hidden="true"
              />
              <p className="text-slate-500 text-sm">{t('marketing', 'create_biodata_cta')}</p>
              <Link href={route('register')} className="mt-4 inline-block text-sm font-semibold text-primary-600 hover:underline">
                {t('marketing', 'home_cta_register')} →
              </Link>
            </div>
          )}
        </div>
      </section>

      {/* ── Success / Stats section ── */}
      <section className="py-20 px-4 bg-gradient-to-br from-emerald-900 to-slate-900 text-white">
        <div className="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          {/* Image */}
          <div className="order-2 lg:order-1 rounded-3xl overflow-hidden shadow-2xl aspect-[4/3]">
            {successImageUrl ? (
              <img
                src={successImageUrl}
                alt=""
                className="h-full w-full object-cover"
                aria-hidden="true"
                onError={e => {
                  (e.target as HTMLImageElement).src = '/images/marketing/success-couple-placeholder.svg'
                }}
              />
            ) : (
              <img
                src="/images/marketing/success-couple-placeholder.svg"
                alt=""
                className="h-full w-full object-cover"
                aria-hidden="true"
              />
            )}
          </div>

          {/* Text */}
          <div className="order-1 lg:order-2">
            <h2 className="text-3xl font-bold mb-4">{t('marketing', 'success_section_title')}</h2>
            <p className="text-emerald-100 text-sm leading-relaxed mb-6">{t('marketing', 'success_section_desc')}</p>

            <ul className="space-y-3 mb-8">
              {SUCCESS_FEATURES.map(key => (
                <li key={key} className="flex items-center gap-3 text-sm">
                  <span className="flex-shrink-0 h-6 w-6 rounded-full bg-emerald-500 flex items-center justify-center">
                    <Check size={13} className="text-white" strokeWidth={3} />
                  </span>
                  <span className="text-emerald-50">{t('marketing', key)}</span>
                </li>
              ))}
            </ul>

            <Link
              href={route('register')}
              className="inline-flex items-center justify-center h-12 px-8 font-semibold rounded-xl bg-amber-400 text-slate-900 hover:bg-amber-300 shadow-lg transition-colors"
            >
              {t('marketing', 'success_cta')} →
            </Link>
          </div>
        </div>
      </section>

      {/* ── Testimonials ── */}
      <section className="py-20 px-4 bg-slate-50">
        <div className="max-w-3xl mx-auto text-center">
          <h2 className="text-3xl font-bold text-slate-900 mb-2">{t('marketing', 'testimonials_title')}</h2>
          <p className="text-slate-500 text-sm mb-12">{t('marketing', 'testimonials_subtitle')}</p>

          <div className="relative bg-white rounded-3xl border border-slate-200 shadow-sm p-8 sm:p-10">
            {/* Sample story label */}
            <span className="absolute -top-3 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-900 text-xs font-bold px-3 py-1 rounded-full">
              {t('marketing', 'member_story_label')}
            </span>

            {/* Avatar */}
            <div className="h-16 w-16 rounded-full bg-gradient-to-br from-emerald-400 to-primary-600 flex items-center justify-center mx-auto mb-5 shadow-md">
              <span className="text-white font-bold text-lg">{current.initials}</span>
            </div>

            <blockquote className="text-slate-700 text-base leading-relaxed italic mb-6">
              &ldquo;{t('marketing', current.textKey)}&rdquo;
            </blockquote>

            <div className="flex items-center justify-center gap-1.5 text-sm text-slate-500">
              <MapPin size={13} />
              {t('marketing', current.locationKey)}
            </div>

            {/* Stars */}
            <div className="flex justify-center gap-1 mt-3">
              {[...Array(5)].map((_, i) => (
                <Star key={i} size={14} className="text-amber-400 fill-amber-400" />
              ))}
            </div>
          </div>

          {/* Navigation */}
          <div className="flex items-center justify-center gap-4 mt-8">
            <button
              onClick={prevTestimonial}
              aria-label="Previous"
              className="h-10 w-10 rounded-full border border-slate-200 bg-white shadow-sm flex items-center justify-center hover:bg-slate-50 transition-colors"
            >
              <ChevronLeft size={18} className="text-slate-600" />
            </button>

            {/* Dots */}
            <div className="flex gap-2">
              {TESTIMONIAL_KEYS.map((_, i) => (
                <button
                  key={i}
                  onClick={() => setTestimonialIdx(i)}
                  aria-label={`Go to testimonial ${i + 1}`}
                  className={`h-2 rounded-full transition-all ${i === testimonialIdx ? 'w-6 bg-primary-600' : 'w-2 bg-slate-300'}`}
                />
              ))}
            </div>

            <button
              onClick={nextTestimonial}
              aria-label="Next"
              className="h-10 w-10 rounded-full border border-slate-200 bg-white shadow-sm flex items-center justify-center hover:bg-slate-50 transition-colors"
            >
              <ChevronRight size={18} className="text-slate-600" />
            </button>
          </div>
        </div>
      </section>

      {/* ── Comparison ── */}
      <section className="py-20 px-4 bg-white">
        <div className="max-w-4xl mx-auto">
          <h2 className="text-3xl font-bold text-slate-900 text-center mb-12">{t('marketing', 'comparison_title')}</h2>

          {/* Header row */}
          <div className="grid grid-cols-2 gap-4 mb-4">
            <div className="rounded-xl bg-red-50 border border-red-100 p-4 text-center">
              <p className="text-sm font-semibold text-red-700">{t('marketing', 'comparison_you_expect')}</p>
            </div>
            <div className="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-center">
              <p className="text-sm font-semibold text-emerald-700">{t('marketing', 'comparison_we_offer')}</p>
            </div>
          </div>

          {/* Comparison rows */}
          <div className="space-y-3">
            {COMPARISON_ROWS.map(({ leftKey, rightKey }) => (
              <div key={leftKey} className="grid grid-cols-2 gap-4">
                <div className="flex items-start gap-2.5 rounded-xl border border-red-100 bg-red-50/60 p-3.5">
                  <X size={15} className="text-red-400 shrink-0 mt-0.5" />
                  <p className="text-sm text-red-800">{t('marketing', leftKey)}</p>
                </div>
                <div className="flex items-start gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50/60 p-3.5">
                  <Check size={15} className="text-emerald-600 shrink-0 mt-0.5" strokeWidth={3} />
                  <p className="text-sm text-emerald-900 font-medium">{t('marketing', rightKey)}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── Two modes ── */}
      <section className="py-20 px-4 bg-slate-50">
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
      <section className="py-20 px-4 bg-white">
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
      <section className="py-20 px-4 bg-slate-50">
        <div className="max-w-5xl mx-auto">
          <h2 className="text-3xl font-bold text-slate-900 text-center mb-14">
            {t('marketing', 'home_steps_title')}
          </h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {STEPS.map(({ n, icon, titleKey, descKey }) => (
              <div key={n} className="text-center">
                <div className="relative inline-block mb-4">
                  <div className="h-16 w-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-3xl mx-auto shadow-sm">
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
