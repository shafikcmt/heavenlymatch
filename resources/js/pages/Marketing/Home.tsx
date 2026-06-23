/// <reference path="../../types/ziggy.d.ts" />
import { useState } from 'react'
import { Link, usePage, router } from '@inertiajs/react'
import { Search, ShieldCheck, Lock, BadgeCheck } from 'lucide-react'
import MarketingLayout from '@/layouts/MarketingLayout'
import { useTranslation } from '@/lib/i18n'
import { SeoHead } from '@/components/SeoHead'
import type { PageProps } from '@/types'

// ── Types ─────────────────────────────────────────────────────────────────────

interface Props {
  heroImageUrl?: string | null
}

// ── Static data ───────────────────────────────────────────────────────────────

const TRUST_POINTS = [
  { icon: BadgeCheck, key: 'home_trust_verified_title' },
  { icon: Lock,       key: 'home_trust_privacy_title' },
  { icon: ShieldCheck, key: 'home_trust_secure_title' },
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
        className="w-full h-11 rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 transition-colors"
      >
        {children}
      </select>
    </div>
  )
}

// ── Component ─────────────────────────────────────────────────────────────────

export default function Home({ heroImageUrl }: Props) {
  const { t } = useTranslation()
  const { auth } = usePage<PageProps>().props
  const isLoggedIn = !!auth?.user

  // Hero search form
  const [lookingFor, setLookingFor] = useState<'bride' | 'groom'>('bride')
  const [heroSearch, setHeroSearch] = useState({ age_min: '', age_max: '' })

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
    if (heroSearch.age_min) params.set('age_min', heroSearch.age_min)
    if (heroSearch.age_max) params.set('age_max', heroSearch.age_max)
    const qs = params.toString()

    if (isLoggedIn) {
      router.visit(route('search.index') + (qs ? `?${qs}` : ''))
    } else {
      router.visit(route('profiles.index') + (qs ? `?${qs}` : ''))
    }
  }

  return (
    <MarketingLayout>
      <SeoHead pageKey="home" />

      {/* ── Hero: heading + search ─────────────────────────────────────────── */}
      <section className="relative overflow-hidden bg-slate-950 flex items-center min-h-[78vh]">
        {/* Background */}
        {heroImageUrl ? (
          <img
            src={heroImageUrl}
            alt=""
            className="absolute inset-0 w-full h-full object-cover opacity-30"
            aria-hidden="true"
          />
        ) : (
          <div className="absolute inset-0 bg-gradient-to-br from-slate-950 via-primary-950 to-violet-950" />
        )}
        {/* Soft radial accents */}
        <div className="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-primary-600/25 blur-3xl" aria-hidden="true" />
        <div className="absolute -bottom-32 -right-16 h-96 w-96 rounded-full bg-violet-600/20 blur-3xl" aria-hidden="true" />
        <div className="absolute inset-0 bg-gradient-to-b from-slate-950/40 via-transparent to-slate-950/70" aria-hidden="true" />

        <div className="relative z-10 w-full max-w-5xl mx-auto px-4 py-16 sm:py-20">
          {/* Heading */}
          <div className="text-center text-white mb-8 sm:mb-10">
            <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
              {t('marketing', 'quick_reg_title')}
            </h1>
          </div>

          {/* Centered horizontal search card */}
          <div className="bg-white/95 backdrop-blur rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 sm:p-7">
            <form
              onSubmit={handleHeroSearch}
              className="flex flex-col md:flex-row md:items-end gap-4 md:gap-5"
            >
              {/* Looking for */}
              <div className="md:flex-1">
                <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
                  {t('marketing', 'hero_looking_for')}
                </label>
                <div className="grid grid-cols-2 gap-2">
                  {(['bride', 'groom'] as const).map(v => (
                    <button
                      key={v}
                      type="button"
                      onClick={() => setLookingFor(v)}
                      className={`h-11 rounded-xl text-sm font-semibold border-2 transition-all ${
                        lookingFor === v
                          ? 'border-primary-600 bg-primary-600 text-white shadow-sm'
                          : 'border-slate-200 text-slate-600 hover:border-primary-300 hover:bg-primary-50'
                      }`}
                    >
                      {v === 'bride' ? t('marketing', 'hero_bride_label') : t('marketing', 'hero_groom_label')}
                    </button>
                  ))}
                </div>
              </div>

              {/* Age From */}
              <div className="md:w-32">
                <SelectField
                  label={t('marketing', 'hero_age_from')}
                  value={heroSearch.age_min}
                  onChange={v => setHeroSearch(s => ({ ...s, age_min: v }))}
                >
                  <option value="">{t('marketing', 'hero_age_any')}</option>
                  {Array.from({ length: 43 }, (_, i) => i + 18).map(age => (
                    <option key={age} value={age}>{age}</option>
                  ))}
                </SelectField>
              </div>

              {/* Age To */}
              <div className="md:w-32">
                <SelectField
                  label={t('marketing', 'hero_age_to')}
                  value={heroSearch.age_max}
                  onChange={v => setHeroSearch(s => ({ ...s, age_max: v }))}
                >
                  <option value="">{t('marketing', 'hero_age_any')}</option>
                  {Array.from({ length: 43 }, (_, i) => i + 18)
                    .filter(age => !heroSearch.age_min || age >= parseInt(heroSearch.age_min, 10))
                    .map(age => (
                      <option key={age} value={age}>{age}</option>
                    ))}
                </SelectField>
              </div>

              {/* Submit */}
              <button
                type="submit"
                className="w-full md:w-auto flex items-center justify-center gap-2 h-11 px-7 rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 active:scale-[0.98] transition-all shadow-lg shadow-primary-900/30 whitespace-nowrap"
              >
                <Search size={16} />
                {t('marketing', 'hero_search_btn')}
              </button>
            </form>
          </div>

          {/* Slim trust strip */}
          <div className="mt-7 flex flex-wrap items-center justify-center gap-x-7 gap-y-3 text-sm text-slate-300">
            {TRUST_POINTS.map(({ icon: Icon, key }) => (
              <span key={key} className="inline-flex items-center gap-2">
                <Icon size={16} className="text-emerald-400" />
                {t('marketing', key)}
              </span>
            ))}
          </div>
        </div>
      </section>

      {/* ── Minimal CTA ──────────────────────────────────────────────────────── */}
      <section className="bg-white py-14 px-4">
        <div className="max-w-2xl mx-auto text-center">
          <h2 className="text-2xl sm:text-3xl font-bold text-slate-900 mb-3">{t('marketing', 'home_final_title')}</h2>
          <p className="text-slate-500 text-base mb-7">{t('marketing', 'home_final_subtitle')}</p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Link
              href={route('register')}
              className="inline-flex items-center justify-center h-12 px-8 text-base font-semibold rounded-xl bg-primary-600 text-white hover:bg-primary-700 shadow-lg shadow-primary-200 transition-colors"
            >
              {t('marketing', 'home_final_cta')} →
            </Link>
            <Link
              href={route('pricing')}
              className="inline-flex items-center justify-center h-12 px-8 text-base font-semibold rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors"
            >
              {t('marketing', 'home_view_pricing')}
            </Link>
          </div>
        </div>
      </section>
    </MarketingLayout>
  )
}
