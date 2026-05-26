/// <reference path="../../types/ziggy.d.ts" />
import { useState, useRef } from 'react'
import { Link, usePage, router } from '@inertiajs/react'
import {
  Shield, CheckCircle, Lock, Users, CreditCard, MapPin,
  UserCheck, ChevronLeft, ChevronRight, Star, Check, X, Flag,
  Search, Heart, MessageCircle, ClipboardList, ShieldCheck,
} from 'lucide-react'
import MarketingLayout from '@/layouts/MarketingLayout'
import { useTranslation } from '@/lib/i18n'
import { SeoHead } from '@/components/SeoHead'
import { BangladeshAddressPicker, type AddressValue } from '@/components/forms/BangladeshAddressPicker'
import type { PageProps } from '@/types'

// ── Types ─────────────────────────────────────────────────────────────────────

interface FeaturedProfile {
  id: string
  first_name: string
  gender: 'male' | 'female'
  age: number | null
  district: string | null
  occupation: string | null
  avatar_num: number
  is_verified: boolean
}

interface Props {
  heroImageUrl?: string | null
  successImageUrl?: string | null
  featuredProfiles?: FeaturedProfile[]
  totalApproved?: number
  verifiedCount?: number
}

// ── Static data ───────────────────────────────────────────────────────────────

const STATS = [
  { key: 'home_stat_members',   value: '50,000+' },
  { key: 'home_stat_marriages', value: '3,200+' },
  { key: 'home_stat_daily',     value: '200+' },
  { key: 'home_stat_rating',    value: '94%' },
] as const

const SECTS = ['Sunni', 'Hanafi', "Shafi'i", 'Maliki', 'Hanbali'] as const

const STEPS = [
  { n: '1', icon: ClipboardList, titleKey: 'home_step1_title', descKey: 'home_step1_desc', colorClass: 'bg-primary-100 text-primary-600' },
  { n: '2', icon: ShieldCheck,   titleKey: 'home_step2_title', descKey: 'home_step2_desc', colorClass: 'bg-emerald-100 text-emerald-600' },
  { n: '3', icon: Search,        titleKey: 'home_step3_title', descKey: 'home_step3_desc', colorClass: 'bg-blue-100 text-blue-600' },
  { n: '4', icon: Heart,         titleKey: 'home_step4_title', descKey: 'home_step4_desc', colorClass: 'bg-rose-100 text-rose-600' },
  { n: '5', icon: MessageCircle, titleKey: 'home_step5_title', descKey: 'home_step5_desc', colorClass: 'bg-violet-100 text-violet-600' },
]

const TRUST_FEATURES = [
  { icon: UserCheck,  titleKey: 'home_trust_verified_title', descKey: 'home_trust_verified_desc' },
  { icon: Lock,       titleKey: 'home_trust_privacy_title',  descKey: 'home_trust_privacy_desc' },
  { icon: Users,      titleKey: 'home_trust_guardian_title', descKey: 'home_trust_guardian_desc' },
  { icon: Shield,     titleKey: 'home_trust_secure_title',   descKey: 'home_trust_secure_desc' },
  { icon: Flag,       titleKey: 'home_trust_report_title',   descKey: 'home_trust_report_desc' },
  { icon: CreditCard, titleKey: 'home_trust_payment_title',  descKey: 'home_trust_payment_desc' },
] as const

const GENERAL_FEATURES = [
  'home_mode_general_f1', 'home_mode_general_f2',
  'home_mode_general_f3', 'home_mode_general_f4',
] as const

const ISLAMIC_FEATURES = [
  'home_mode_islamic_f1', 'home_mode_islamic_f2',
  'home_mode_islamic_f3', 'home_mode_islamic_f4',
] as const

const TESTIMONIAL_KEYS = [
  { initials: 'F.K.', locationKey: 'testimonial_1_location', textKey: 'testimonial_1_text' },
  { initials: 'A.R.', locationKey: 'testimonial_2_location', textKey: 'testimonial_2_text' },
  { initials: 'S.B.', locationKey: 'testimonial_3_location', textKey: 'testimonial_3_text' },
  { initials: 'M.H.', locationKey: 'testimonial_4_location', textKey: 'testimonial_4_text' },
  { initials: 'T.A.', locationKey: 'testimonial_5_location', textKey: 'testimonial_5_text' },
] as const

const SUCCESS_STORIES = TESTIMONIAL_KEYS.slice(0, 3)

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

// ── Helpers ───────────────────────────────────────────────────────────────────

function SelectField({
  label, value, onChange, children,
}: {
  label: string
  value: string
  onChange: (v: string) => void
  children: React.ReactNode
}) {
  return (
    <div>
      <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">{label}</label>
      <select
        value={value}
        onChange={e => onChange(e.target.value)}
        className="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-200 transition-colors"
      >
        {children}
      </select>
    </div>
  )
}

// ── Component ─────────────────────────────────────────────────────────────────

export default function Home({ heroImageUrl, successImageUrl, featuredProfiles = [] }: Props) {
  const { t } = useTranslation()
  const { auth } = usePage<PageProps>().props
  const isLoggedIn = !!auth?.user

  // Hero search form
  const [lookingFor, setLookingFor] = useState<'bride' | 'groom'>('bride')
  const [heroSearch, setHeroSearch] = useState({ age_min: '', age_max: '', division: '', district: '', upazila: '', sect: '' })
  const heroAddress: AddressValue = {
    division: heroSearch.division || undefined,
    district: heroSearch.district || undefined,
    upazila:  heroSearch.upazila  || undefined,
  }
  const handleHeroAddress = (val: AddressValue) => setHeroSearch(s => ({
    ...s,
    division: val.division ?? '',
    district: val.district ?? '',
    upazila:  val.upazila  ?? '',
  }))

  // Quick registration form
  const [quickReg, setQuickReg] = useState({ name: '', mobile: '', profile_for: 'self' })

  // Testimonial carousel
  const [testimonialIdx, setTestimonialIdx] = useState(0)
  const sliderRef = useRef<HTMLDivElement>(null)

  const prevTestimonial = () => setTestimonialIdx(i => (i === 0 ? TESTIMONIAL_KEYS.length - 1 : i - 1))
  const nextTestimonial = () => setTestimonialIdx(i => (i === TESTIMONIAL_KEYS.length - 1 ? 0 : i + 1))
  const scrollSlider = (dir: 'left' | 'right') => {
    sliderRef.current?.scrollBy({ left: dir === 'right' ? 220 : -220, behavior: 'smooth' })
  }

  const current = TESTIMONIAL_KEYS[testimonialIdx] ?? TESTIMONIAL_KEYS[0]!

  const handleHeroSearch = (e: React.FormEvent) => {
    e.preventDefault()
    // Validate age range: age_max must be >= age_min
    const ageMin = heroSearch.age_min ? parseInt(heroSearch.age_min, 10) : null
    const ageMax = heroSearch.age_max ? parseInt(heroSearch.age_max, 10) : null
    if (ageMin !== null && ageMax !== null && ageMax < ageMin) {
      setHeroSearch(s => ({ ...s, age_max: heroSearch.age_min }))
      return
    }

    const params = new URLSearchParams()
    params.set('looking_for', lookingFor)
    if (heroSearch.age_min)  params.set('age_min',  heroSearch.age_min)
    if (heroSearch.age_max)  params.set('age_max',  heroSearch.age_max)
    if (heroSearch.division) params.set('division', heroSearch.division)
    if (heroSearch.district) params.set('district', heroSearch.district)
    if (heroSearch.upazila)  params.set('upazila',  heroSearch.upazila)
    if (heroSearch.sect) params.set('sect', heroSearch.sect)
    const qs = params.toString()

    if (isLoggedIn) {
      router.visit(route('search.index') + (qs ? `?${qs}` : ''))
    } else {
      router.visit(route('profiles.index') + (qs ? `?${qs}` : ''))
    }
  }

  const handleQuickReg = (e: React.FormEvent) => {
    e.preventDefault()
    const params = new URLSearchParams()
    if (quickReg.name) params.set('name', quickReg.name)
    if (quickReg.mobile) params.set('mobile_number', quickReg.mobile)
    if (quickReg.profile_for) params.set('profile_created_for', quickReg.profile_for)
    window.location.href = route('register') + '?' + params.toString()
  }

  return (
    <MarketingLayout>
      <SeoHead pageKey="home" />

      {/* ── Hero with search form ─────────────────────────────────────────── */}
      <section className="relative overflow-hidden bg-slate-900 flex items-center min-h-[580px]">
        {/* Background */}
        {heroImageUrl ? (
          <img
            src={heroImageUrl}
            alt=""
            className="absolute inset-0 w-full h-full object-cover opacity-35"
            aria-hidden="true"
          />
        ) : (
          <div className="absolute inset-0 bg-gradient-to-br from-slate-900 via-primary-950/90 to-violet-900" />
        )}
        {/* Gradient overlay for readability */}
        <div className="absolute inset-0 bg-gradient-to-r from-slate-900/95 via-slate-900/70 to-slate-900/40" />

        <div className="relative z-10 w-full max-w-6xl mx-auto px-4 py-12 lg:py-14">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-12 items-center">

            {/* Left: Heading + Stats */}
            <div className="text-white">
              <div className="inline-flex items-center gap-2 bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 rounded-full px-4 py-1.5 text-xs font-semibold mb-5">
                <Shield size={12} />
                {t('marketing', 'home_trust_badge')}
              </div>

              <h1 className="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight mb-4 tracking-tight">
                {t('marketing', 'hero_search_title')}
              </h1>

              <p className="text-slate-300 text-sm sm:text-base leading-relaxed mb-8 max-w-md">
                {t('marketing', 'hero_search_subtitle')}
              </p>

              {/* Stats grid */}
              <div className="grid grid-cols-2 gap-3 max-w-sm">
                {STATS.map(({ key, value }) => (
                  <div key={key} className="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                    <p className="text-xl font-extrabold text-white">{value}</p>
                    <p className="text-xs text-slate-400 mt-0.5">{t('marketing', key)}</p>
                  </div>
                ))}
              </div>
            </div>

            {/* Right: Search form card */}
            <div className="bg-white rounded-2xl shadow-2xl p-6">
              <p className="text-center text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">
                {t('marketing', 'home_hero_title')}{' '}
                <span className="text-primary-600">{t('marketing', 'home_hero_highlight')}</span>
              </p>

              <form onSubmit={handleHeroSearch} className="space-y-3.5">
                {/* Looking for toggle */}
                <div>
                  <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                    {t('marketing', 'hero_looking_for')}
                  </label>
                  <div className="grid grid-cols-2 gap-2">
                    {(['bride', 'groom'] as const).map(v => (
                      <button
                        key={v}
                        type="button"
                        onClick={() => setLookingFor(v)}
                        className={`py-2.5 rounded-xl text-sm font-semibold border-2 transition-all ${
                          lookingFor === v
                            ? 'border-primary-600 bg-primary-600 text-white shadow-sm'
                            : 'border-slate-200 text-slate-600 hover:border-primary-200 hover:bg-primary-50'
                        }`}
                      >
                        {v === 'bride' ? t('marketing', 'hero_bride_label') : t('marketing', 'hero_groom_label')}
                      </button>
                    ))}
                  </div>
                </div>

                {/* Age row */}
                <div className="grid grid-cols-2 gap-2">
                  <SelectField
                    label={t('marketing', 'hero_age_from')}
                    value={heroSearch.age_min}
                    onChange={v => setHeroSearch(s => ({ ...s, age_min: v }))}
                  >
                    <option value="">Any</option>
                    {Array.from({ length: 43 }, (_, i) => i + 18).map(age => (
                      <option key={age} value={age}>{age}</option>
                    ))}
                  </SelectField>
                  <SelectField
                    label={t('marketing', 'hero_age_to')}
                    value={heroSearch.age_max}
                    onChange={v => setHeroSearch(s => ({ ...s, age_max: v }))}
                  >
                    <option value="">Any</option>
                    {Array.from({ length: 43 }, (_, i) => i + 18)
                      .filter(age => !heroSearch.age_min || age >= parseInt(heroSearch.age_min, 10))
                      .map(age => (
                        <option key={age} value={age}>{age}</option>
                      ))}
                  </SelectField>
                </div>

                {/* Division → District → Upazila (cascading) */}
                <BangladeshAddressPicker
                  value={heroAddress}
                  onChange={handleHeroAddress}
                  mode="filter"
                />

                {/* Sect */}
                <SelectField
                  label={t('marketing', 'hero_sect')}
                  value={heroSearch.sect}
                  onChange={v => setHeroSearch(s => ({ ...s, sect: v }))}
                >
                  <option value="">{t('marketing', 'hero_sect_any')}</option>
                  {SECTS.map(s => <option key={s} value={s}>{s}</option>)}
                </SelectField>

                {/* Submit */}
                <button
                  type="submit"
                  className="w-full flex items-center justify-center gap-2 h-12 rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 active:scale-[0.98] transition-all shadow-md shadow-primary-200"
                >
                  <Search size={16} />
                  {t('marketing', 'hero_search_btn')}
                </button>

                {!isLoggedIn && (
                  <p className="text-center text-xs text-slate-400">
                    {t('marketing', 'hero_guest_search_note')}
                  </p>
                )}
              </form>
            </div>
          </div>
        </div>
      </section>

      {/* ── Quick Registration CTA ────────────────────────────────────────────── */}
      <section className="bg-gradient-to-r from-primary-700 via-primary-600 to-violet-600 py-10 px-4">
        <div className="w-full max-w-6xl mx-auto">
          {/* Heading */}
          <div className="text-center text-white mb-6">
            <h2 className="text-2xl sm:text-3xl font-bold mb-2">
              {t('marketing', 'quick_reg_title')}
            </h2>
            <p className="text-primary-100 text-sm">{t('marketing', 'quick_reg_subtitle')}</p>
          </div>

          {/* Full-width form card — matches hero card width */}
          <div className="bg-white rounded-2xl p-5 shadow-2xl">
            <form onSubmit={handleQuickReg}>
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                {/* Profile for */}
                <div>
                  <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                    {t('marketing', 'quick_reg_for_label')}
                  </label>
                  <select
                    value={quickReg.profile_for}
                    onChange={e => setQuickReg(s => ({ ...s, profile_for: e.target.value }))}
                    className="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-200 transition-colors"
                  >
                    <option value="self">{t('marketing', 'quick_reg_for_self')}</option>
                    <option value="son">{t('marketing', 'quick_reg_for_son')}</option>
                    <option value="daughter">{t('marketing', 'quick_reg_for_daughter')}</option>
                    <option value="brother">{t('marketing', 'quick_reg_for_brother')}</option>
                    <option value="sister">{t('marketing', 'quick_reg_for_sister')}</option>
                    <option value="relative">{t('marketing', 'quick_reg_for_relative')}</option>
                  </select>
                </div>

                {/* Name */}
                <div>
                  <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                    {t('marketing', 'quick_reg_name')}
                  </label>
                  <input
                    type="text"
                    value={quickReg.name}
                    onChange={e => setQuickReg(s => ({ ...s, name: e.target.value }))}
                    placeholder="Your full name"
                    className="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-200 transition-colors"
                  />
                </div>

                {/* Mobile */}
                <div>
                  <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                    {t('marketing', 'quick_reg_mobile')}
                  </label>
                  <input
                    type="tel"
                    value={quickReg.mobile}
                    onChange={e => setQuickReg(s => ({ ...s, mobile: e.target.value }))}
                    placeholder="+880 1XXX-XXXXXX"
                    className="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-200 transition-colors"
                  />
                </div>

                {/* Register button aligned to input height */}
                <div className="flex items-end">
                  <button
                    type="submit"
                    className="w-full h-[42px] rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 active:scale-[0.98] transition-all shadow-md"
                  >
                    {t('marketing', 'quick_reg_btn')}
                  </button>
                </div>
              </div>
            </form>
          </div>

          <p className="text-center text-primary-200 text-xs mt-3">{t('marketing', 'quick_reg_note')}</p>
        </div>
      </section>

      {/* ── Featured Profile Slider ───────────────────────────────────────────── */}
      <section className="py-16 px-4 bg-white">
        <div className="max-w-5xl mx-auto">
          <div className="text-center mb-8">
            <h2 className="text-2xl font-bold text-slate-900 mb-1.5">{t('marketing', 'featured_profiles_title')}</h2>
            <p className="text-slate-500 text-sm">{t('marketing', 'featured_profiles_subtitle')}</p>
          </div>

          {featuredProfiles.length > 0 ? (
            <div className="relative">
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

              <div
                ref={sliderRef}
                className="flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-3 -mx-4 px-4 sm:mx-0 sm:px-0"
                style={{ scrollbarWidth: 'none' }}
              >
                {featuredProfiles.map(profile => (
                  <div
                    key={profile.id}
                    className="shrink-0 snap-center w-[175px] sm:w-[195px] rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all"
                  >
                    {/* Avatar area */}
                    <div className="relative h-[150px] bg-gradient-to-br from-slate-100 to-slate-50 flex items-center justify-center overflow-hidden">
                      <img
                        src={`/images/marketing/profile-placeholder-${profile.gender}.svg`}
                        alt=""
                        className="h-full w-full object-cover"
                        aria-hidden="true"
                        onError={e => {
                          (e.target as HTMLImageElement).src = `/images/avatar-${profile.gender}.svg`
                        }}
                      />
                      {profile.is_verified && (
                        <div className="absolute top-2 right-2 bg-emerald-500 text-white rounded-full p-1" title="Verified">
                          <Check size={10} strokeWidth={3} />
                        </div>
                      )}
                      <div className="absolute bottom-0 inset-x-0 h-10 bg-gradient-to-t from-slate-900/40 to-transparent" />
                    </div>

                    {/* Info */}
                    <div className="p-3">
                      <div className="flex items-center justify-between mb-0.5">
                        <p className="font-semibold text-slate-900 text-sm truncate">{profile.first_name}</p>
                        <span className={`text-xs font-semibold px-1.5 py-0.5 rounded ${profile.gender === 'female' ? 'bg-rose-50 text-rose-600' : 'bg-blue-50 text-blue-600'}`}>
                          {profile.gender === 'female' ? '♀' : '♂'}
                        </span>
                      </div>
                      <p className="text-xs text-slate-500">
                        {profile.age ? `${profile.age} yrs` : '—'}
                        {profile.district ? ` · ${profile.district}` : ''}
                      </p>
                      {profile.occupation && (
                        <p className="text-xs text-slate-400 mt-0.5 truncate">{profile.occupation}</p>
                      )}
                      <Link
                        href={isLoggedIn ? route('profile.show', { registrationId: profile.id }) : route('profiles.show', { registrationId: profile.id })}
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
            <div className="text-center py-12 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50">
              <div className="h-16 w-16 rounded-2xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                <UserCheck size={28} className="text-primary-600" />
              </div>
              <p className="text-slate-500 text-sm mb-3">{t('marketing', 'create_biodata_cta')}</p>
              <Link href={route('register')} className="text-sm font-semibold text-primary-600 hover:underline">
                {t('marketing', 'home_cta_register')} →
              </Link>
            </div>
          )}
        </div>
      </section>

      {/* ── Success Stories ───────────────────────────────────────────────────── */}
      <section className="py-16 px-4 bg-slate-50">
        <div className="max-w-5xl mx-auto">
          <div className="text-center mb-10">
            <h2 className="text-2xl font-bold text-slate-900 mb-1.5">
              {t('marketing', 'success_stories_title')}
            </h2>
            <p className="text-slate-500 text-sm">{t('marketing', 'success_stories_subtitle')}</p>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            {/* Left: Success image */}
            <div className="rounded-3xl overflow-hidden shadow-xl aspect-[4/3]">
              {successImageUrl ? (
                <img
                  src={successImageUrl}
                  alt=""
                  className="h-full w-full object-cover"
                  aria-hidden="true"
                  onError={e => {
                    (e.target as HTMLImageElement).style.display = 'none'
                  }}
                />
              ) : (
                <div className="h-full w-full bg-gradient-to-br from-emerald-500 to-primary-700 flex items-center justify-center">
                  <div className="text-center text-white px-6">
                    <Heart size={48} className="mx-auto mb-3 opacity-80 fill-white/30" />
                    <p className="font-bold text-2xl mb-1">3,200+</p>
                    <p className="text-sm text-emerald-100">Blessed Unions</p>
                    <p className="text-xs text-emerald-200 mt-1">Alhamdulillah</p>
                  </div>
                </div>
              )}
            </div>

            {/* Right: Story cards */}
            <div className="space-y-4">
              {SUCCESS_STORIES.map(story => (
                <div key={story.initials} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                  <div className="flex items-start gap-3">
                    <div className="h-10 w-10 rounded-full bg-gradient-to-br from-primary-400 to-violet-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                      <span className="text-white font-bold text-xs">{story.initials}</span>
                    </div>
                    <div className="min-w-0">
                      <div className="flex gap-0.5 mb-1.5">
                        {[...Array(5)].map((_, i) => (
                          <Star key={i} size={11} className="text-amber-400 fill-amber-400" />
                        ))}
                      </div>
                      <blockquote className="text-slate-700 text-sm leading-relaxed italic line-clamp-3">
                        &ldquo;{t('marketing', story.textKey)}&rdquo;
                      </blockquote>
                      <p className="text-xs text-slate-400 mt-2 flex items-center gap-1">
                        <MapPin size={11} />
                        {t('marketing', story.locationKey)}
                      </p>
                    </div>
                  </div>
                </div>
              ))}

              <ul className="space-y-2 pt-2">
                {SUCCESS_FEATURES.map(key => (
                  <li key={key} className="flex items-center gap-2.5 text-sm text-slate-700">
                    <span className="flex-shrink-0 h-5 w-5 rounded-full bg-emerald-100 flex items-center justify-center">
                      <Check size={11} className="text-emerald-600" strokeWidth={3} />
                    </span>
                    {t('marketing', key)}
                  </li>
                ))}
              </ul>

              <Link
                href={route('register')}
                className="flex items-center justify-center h-11 rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 transition-colors shadow-md mt-4"
              >
                {t('marketing', 'success_cta')} →
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* ── How It Works ─────────────────────────────────────────────────────── */}
      <section className="py-16 px-4 bg-white">
        <div className="max-w-5xl mx-auto">
          <div className="text-center mb-10">
            <h2 className="text-2xl font-bold text-slate-900 mb-2">{t('marketing', 'home_steps_title')}</h2>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
            {STEPS.map(({ n, icon: Icon, titleKey, descKey, colorClass }) => (
              <div key={n} className="text-center">
                <div className="relative inline-block mb-4">
                  <div className={`h-14 w-14 rounded-2xl ${colorClass} flex items-center justify-center mx-auto shadow-sm`}>
                    <Icon size={24} />
                  </div>
                  <span className="absolute -top-2 -right-2 h-5 w-5 rounded-full bg-primary-600 text-white text-xs font-bold flex items-center justify-center shadow">
                    {n}
                  </span>
                </div>
                <h3 className="font-bold text-slate-900 text-xs sm:text-sm mb-1.5">{t('marketing', titleKey)}</h3>
                <p className="text-xs text-slate-500 leading-relaxed">{t('marketing', descKey)}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── Trust & Safety ────────────────────────────────────────────────────── */}
      <section className="py-16 px-4 bg-slate-50">
        <div className="max-w-5xl mx-auto">
          <div className="text-center mb-10">
            <h2 className="text-2xl font-bold text-slate-900 mb-2">{t('marketing', 'home_trust_title')}</h2>
            <p className="text-slate-500 text-sm max-w-xl mx-auto">{t('marketing', 'home_trust_subtitle')}</p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {TRUST_FEATURES.map(({ icon: Icon, titleKey, descKey }) => (
              <div key={titleKey} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                <div className="h-10 w-10 rounded-xl bg-primary-50 flex items-center justify-center mb-3">
                  <Icon size={20} className="text-primary-600" />
                </div>
                <h3 className="font-semibold text-slate-900 text-sm mb-1.5">{t('marketing', titleKey)}</h3>
                <p className="text-xs text-slate-500 leading-relaxed">{t('marketing', descKey)}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── Testimonials ─────────────────────────────────────────────────────── */}
      <section className="py-16 px-4 bg-white">
        <div className="max-w-3xl mx-auto text-center">
          <h2 className="text-2xl font-bold text-slate-900 mb-1.5">{t('marketing', 'testimonials_title')}</h2>
          <p className="text-slate-500 text-sm mb-10">{t('marketing', 'testimonials_subtitle')}</p>

          <div className="relative bg-white rounded-3xl border border-slate-200 shadow-sm p-8 sm:p-10">
            <span className="absolute -top-3 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-900 text-xs font-bold px-3 py-1 rounded-full">
              {t('marketing', 'member_story_label')}
            </span>

            <div className="h-14 w-14 rounded-full bg-gradient-to-br from-emerald-400 to-primary-600 flex items-center justify-center mx-auto mb-5 shadow-md">
              <span className="text-white font-bold text-base">{current.initials}</span>
            </div>

            <blockquote className="text-slate-700 text-base leading-relaxed italic mb-5">
              &ldquo;{t('marketing', current.textKey)}&rdquo;
            </blockquote>

            <div className="flex items-center justify-center gap-1.5 text-sm text-slate-500">
              <MapPin size={13} />
              {t('marketing', current.locationKey)}
            </div>

            <div className="flex justify-center gap-1 mt-3">
              {[...Array(5)].map((_, i) => (
                <Star key={i} size={14} className="text-amber-400 fill-amber-400" />
              ))}
            </div>
          </div>

          <div className="flex items-center justify-center gap-4 mt-8">
            <button
              onClick={prevTestimonial}
              aria-label="Previous"
              className="h-10 w-10 rounded-full border border-slate-200 bg-white shadow-sm flex items-center justify-center hover:bg-slate-50 transition-colors"
            >
              <ChevronLeft size={18} className="text-slate-600" />
            </button>

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

      {/* ── Comparison ───────────────────────────────────────────────────────── */}
      <section className="py-16 px-4 bg-slate-50">
        <div className="max-w-4xl mx-auto">
          <h2 className="text-2xl font-bold text-slate-900 text-center mb-10">{t('marketing', 'comparison_title')}</h2>

          <div className="grid grid-cols-2 gap-4 mb-4">
            <div className="rounded-xl bg-red-50 border border-red-100 p-3 text-center">
              <p className="text-xs font-semibold text-red-700">{t('marketing', 'comparison_you_expect')}</p>
            </div>
            <div className="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-center">
              <p className="text-xs font-semibold text-emerald-700">{t('marketing', 'comparison_we_offer')}</p>
            </div>
          </div>

          <div className="space-y-3">
            {COMPARISON_ROWS.map(({ leftKey, rightKey }) => (
              <div key={leftKey} className="grid grid-cols-2 gap-4">
                <div className="flex items-start gap-2.5 rounded-xl border border-red-100 bg-red-50/60 p-3.5">
                  <X size={14} className="text-red-400 shrink-0 mt-0.5" />
                  <p className="text-xs text-red-800">{t('marketing', leftKey)}</p>
                </div>
                <div className="flex items-start gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50/60 p-3.5">
                  <Check size={14} className="text-emerald-600 shrink-0 mt-0.5" strokeWidth={3} />
                  <p className="text-xs text-emerald-900 font-medium">{t('marketing', rightKey)}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── Two Modes ────────────────────────────────────────────────────────── */}
      <section className="py-16 px-4 bg-white">
        <div className="max-w-5xl mx-auto">
          <h2 className="text-2xl font-bold text-slate-900 text-center mb-2">
            {t('marketing', 'home_modes_title')}
          </h2>
          <p className="text-slate-500 text-center text-sm mb-10 max-w-xl mx-auto">
            {t('marketing', 'home_modes_subtitle')}
          </p>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="rounded-3xl border-2 border-blue-200 bg-blue-50 p-7">
              <div className="text-3xl mb-4">🌐</div>
              <h3 className="text-lg font-bold text-slate-900 mb-2">{t('marketing', 'home_mode_general_title')}</h3>
              <p className="text-slate-600 text-sm mb-5">{t('marketing', 'home_mode_general_desc')}</p>
              <ul className="space-y-2">
                {GENERAL_FEATURES.map(key => (
                  <li key={key} className="flex items-start gap-2 text-sm text-slate-700">
                    <CheckCircle size={15} className="text-blue-500 flex-shrink-0 mt-0.5" />
                    {t('marketing', key)}
                  </li>
                ))}
              </ul>
            </div>

            <div className="rounded-3xl border-2 border-emerald-400 bg-emerald-50 p-7 relative">
              <div className="absolute -top-3 right-6 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                {t('marketing', 'home_mode_islamic_badge')}
              </div>
              <div className="text-3xl mb-4">☪️</div>
              <h3 className="text-lg font-bold text-slate-900 mb-2">{t('marketing', 'home_mode_islamic_title')}</h3>
              <p className="text-slate-600 text-sm mb-5">{t('marketing', 'home_mode_islamic_desc')}</p>
              <ul className="space-y-2">
                {ISLAMIC_FEATURES.map(key => (
                  <li key={key} className="flex items-start gap-2 text-sm text-slate-700">
                    <CheckCircle size={15} className="text-emerald-500 flex-shrink-0 mt-0.5" />
                    {t('marketing', key)}
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>
      </section>

      {/* ── Final CTA ────────────────────────────────────────────────────────── */}
      <section className="py-16 px-4 bg-gradient-to-r from-primary-600 to-violet-600 text-white">
        <div className="max-w-2xl mx-auto text-center">
          <h2 className="text-2xl sm:text-3xl font-bold mb-4">{t('marketing', 'home_final_title')}</h2>
          <p className="text-primary-100 mb-8 text-base">{t('marketing', 'home_final_subtitle')}</p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Link
              href={route('register')}
              className="inline-flex items-center justify-center h-12 px-8 text-base font-semibold rounded-xl bg-white text-primary-600 hover:bg-slate-50 shadow-xl transition-colors"
            >
              {t('marketing', 'home_final_cta')} →
            </Link>
            <Link
              href={route('pricing')}
              className="inline-flex items-center justify-center h-12 px-8 text-base font-semibold rounded-xl border border-white/40 text-white hover:bg-white/10 transition-colors"
            >
              {t('marketing', 'home_view_pricing')}
            </Link>
          </div>
        </div>
      </section>
    </MarketingLayout>
  )
}
