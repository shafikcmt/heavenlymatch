/// <reference path="../../types/ziggy.d.ts" />
import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import { Search, SlidersHorizontal, X, CheckCircle2, LogIn, MapPin, Briefcase, GraduationCap, ChevronLeft, ChevronRight } from 'lucide-react'
import MarketingLayout from '@/layouts/MarketingLayout'
import { useTranslation } from '@/lib/i18n'
import { BangladeshAddressPicker, type AddressValue } from '@/components/forms/BangladeshAddressPicker'
import { SeoHead } from '@/components/SeoHead'

// ── Types ─────────────────────────────────────────────────────────────────────

interface PublicProfile {
  id: string
  gender: 'male' | 'female'
  age: number | null
  height_cm: number | null
  marital_status: string | null
  district: string | null
  division: string | null
  occupation: string | null
  highest_qualification: string | null
  about_me: string | null
  religion: string | null
  sect: string | null
  is_verified: boolean
  avatar_num: number
}

interface PaginationLink {
  url: string | null
  label: string
  active: boolean
}

interface PaginatedProfiles {
  data: PublicProfile[]
  current_page: number
  last_page: number
  total: number
  per_page: number
  links: PaginationLink[]
}

interface Filters {
  looking_for?: string
  age_min?: string
  age_max?: string
  division?: string
  district?: string
  upazila?: string
  marital_status?: string
  sect?: string
}

interface Props {
  results: PaginatedProfiles
  filters: Filters
}

// ── Helpers ───────────────────────────────────────────────────────────────────

const SECTS = ['Sunni', 'Hanafi', "Shafi'i", 'Maliki', 'Hanbali']
const MARITAL_STATUSES = ['never_married', 'divorced', 'widowed']

function AvatarPlaceholder({ gender }: { gender: 'male' | 'female' }) {
  return (
    <div className={`h-full w-full flex items-center justify-center ${gender === 'female' ? 'bg-gradient-to-br from-rose-50 to-pink-100' : 'bg-gradient-to-br from-blue-50 to-sky-100'}`}>
      <img
        src={`/images/marketing/profile-placeholder-${gender}.svg`}
        alt=""
        className="h-14 w-14 object-contain opacity-50"
        aria-hidden="true"
        onError={e => { (e.target as HTMLImageElement).style.display = 'none' }}
      />
    </div>
  )
}

// ── Component ─────────────────────────────────────────────────────────────────

export default function Profiles({ results, filters }: Props) {
  const { t } = useTranslation()
  const [showFilters, setShowFilters] = useState(false)

  const [localFilters, setLocalFilters] = useState<Filters>({
    looking_for:    filters.looking_for    ?? 'bride',
    age_min:        filters.age_min        ?? '',
    age_max:        filters.age_max        ?? '',
    division:       filters.division       ?? '',
    district:       filters.district       ?? '',
    upazila:        filters.upazila        ?? '',
    marital_status: filters.marital_status ?? '',
    sect:           filters.sect           ?? '',
  })

  const address: AddressValue = {
    division: localFilters.division || undefined,
    district: localFilters.district || undefined,
    upazila:  localFilters.upazila  || undefined,
  }

  const handleAddress = (val: AddressValue) => setLocalFilters(s => ({
    ...s,
    division: val.division ?? '',
    district: val.district ?? '',
    upazila:  val.upazila  ?? '',
  }))

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault()
    const params: Record<string, string> = {}
    if (localFilters.looking_for)    params.looking_for    = localFilters.looking_for
    if (localFilters.age_min)        params.age_min        = localFilters.age_min
    if (localFilters.age_max)        params.age_max        = localFilters.age_max
    if (localFilters.division)       params.division       = localFilters.division
    if (localFilters.district)       params.district       = localFilters.district
    if (localFilters.upazila)        params.upazila        = localFilters.upazila
    if (localFilters.marital_status) params.marital_status = localFilters.marital_status
    if (localFilters.sect)           params.sect           = localFilters.sect
    router.get(route('profiles.index'), params, { preserveScroll: false })
    setShowFilters(false)
  }

  const clearFilters = () => {
    setLocalFilters({ looking_for: 'bride', age_min: '', age_max: '', division: '', district: '', upazila: '', marital_status: '', sect: '' })
    router.get(route('profiles.index'), {}, { preserveScroll: false })
  }

  const ageMinNum = localFilters.age_min ? parseInt(localFilters.age_min, 10) : null

  const FilterForm = () => (
    <form onSubmit={handleSearch} className="space-y-4">
      {/* Looking for */}
      <div>
        <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
          {t('marketing', 'hero_looking_for')}
        </label>
        <div className="grid grid-cols-2 gap-2">
          {(['bride', 'groom'] as const).map(v => (
            <button
              key={v}
              type="button"
              onClick={() => setLocalFilters(s => ({ ...s, looking_for: v }))}
              className={`py-2 rounded-xl text-sm font-semibold border-2 transition-all ${
                localFilters.looking_for === v
                  ? 'border-primary-600 bg-primary-600 text-white'
                  : 'border-slate-200 text-slate-600 hover:border-primary-200'
              }`}
            >
              {v === 'bride' ? t('marketing', 'hero_bride_label') : t('marketing', 'hero_groom_label')}
            </button>
          ))}
        </div>
      </div>

      {/* Age */}
      <div>
        <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
          {t('common', 'age')}
        </label>
        <div className="grid grid-cols-2 gap-2">
          <select
            value={localFilters.age_min}
            onChange={e => setLocalFilters(s => ({
              ...s,
              age_min: e.target.value,
              age_max: s.age_max && parseInt(s.age_max) < parseInt(e.target.value || '0') ? e.target.value : s.age_max,
            }))}
            className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
          >
            <option value="">{t('marketing', 'hero_age_from')}</option>
            {Array.from({ length: 43 }, (_, i) => i + 18).map(age => (
              <option key={age} value={age}>{age}</option>
            ))}
          </select>
          <select
            value={localFilters.age_max}
            onChange={e => setLocalFilters(s => ({ ...s, age_max: e.target.value }))}
            className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
          >
            <option value="">{t('marketing', 'hero_age_to')}</option>
            {Array.from({ length: 43 }, (_, i) => i + 18)
              .filter(age => !ageMinNum || age >= ageMinNum)
              .map(age => (
                <option key={age} value={age}>{age}</option>
              ))}
          </select>
        </div>
      </div>

      {/* Location */}
      <BangladeshAddressPicker value={address} onChange={handleAddress} mode="filter" />

      {/* Sect */}
      <div>
        <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
          {t('marketing', 'hero_sect')}
        </label>
        <select
          value={localFilters.sect}
          onChange={e => setLocalFilters(s => ({ ...s, sect: e.target.value }))}
          className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
        >
          <option value="">{t('marketing', 'hero_sect_any')}</option>
          {SECTS.map(s => <option key={s} value={s}>{s}</option>)}
        </select>
      </div>

      {/* Marital status */}
      <div>
        <label className="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">
          {t('biodata', 'marital_status')}
        </label>
        <select
          value={localFilters.marital_status}
          onChange={e => setLocalFilters(s => ({ ...s, marital_status: e.target.value }))}
          className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
        >
          <option value="">{t('common', 'any')}</option>
          {MARITAL_STATUSES.map(s => (
            <option key={s} value={s}>{t('biodata', s) || s.replace('_', ' ')}</option>
          ))}
        </select>
      </div>

      <button
        type="submit"
        className="w-full flex items-center justify-center gap-2 h-11 rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 transition-colors"
      >
        <Search size={15} /> {t('common', 'search_action')}
      </button>

      <button
        type="button"
        onClick={clearFilters}
        className="w-full flex items-center justify-center gap-1.5 h-9 rounded-xl border border-slate-200 text-slate-500 text-xs hover:bg-slate-50 transition-colors"
      >
        <X size={13} /> {t('common', 'clear')}
      </button>
    </form>
  )

  return (
    <MarketingLayout>
      <SeoHead pageKey="home" />

      {/* ── Header ── */}
      <div className="bg-slate-900 text-white py-10 px-4">
        <div className="max-w-6xl mx-auto">
          <h1 className="text-2xl sm:text-3xl font-bold mb-1">{t('marketing', 'public_profiles_heading')}</h1>
          <p className="text-slate-400 text-sm">
            {results.total > 0
              ? t('marketing', 'public_profiles_count', { n: results.total })
              : t('common', 'no_results')}
          </p>
        </div>
      </div>

      <div className="max-w-6xl mx-auto px-4 py-8">
        <div className="flex gap-8">

          {/* ── Sidebar filters (desktop) ── */}
          <aside className="hidden lg:block w-64 flex-shrink-0">
            <div className="bg-white rounded-2xl border border-slate-200 p-5 sticky top-20">
              <h2 className="font-semibold text-slate-900 text-sm mb-4">{t('common', 'filter')}</h2>
              <FilterForm />
            </div>
          </aside>

          {/* ── Main content ── */}
          <div className="flex-1 min-w-0">

            {/* Mobile filter toggle */}
            <div className="flex items-center justify-between mb-4 lg:hidden">
              <p className="text-sm text-slate-500">{results.total} {t('common', 'search')}</p>
              <button
                onClick={() => setShowFilters(!showFilters)}
                className="flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
              >
                <SlidersHorizontal size={15} />
                {t('common', 'filter')}
              </button>
            </div>

            {/* Mobile filter panel */}
            {showFilters && (
              <div className="lg:hidden mb-6 bg-white rounded-2xl border border-slate-200 p-5">
                <FilterForm />
              </div>
            )}

            {/* Login CTA banner */}
            <div className="mb-6 flex items-center gap-3 rounded-2xl bg-primary-50 border border-primary-200 px-5 py-4">
              <LogIn size={18} className="text-primary-600 flex-shrink-0" />
              <div className="flex-1 min-w-0">
                <p className="text-sm font-semibold text-primary-900">{t('marketing', 'public_login_cta_title')}</p>
                <p className="text-xs text-primary-700 mt-0.5">{t('marketing', 'public_login_cta_sub')}</p>
              </div>
              <div className="flex gap-2 flex-shrink-0">
                <Link
                  href={route('login')}
                  className="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-700 border border-primary-300 rounded-lg px-3 py-1.5 hover:bg-primary-100 transition-colors"
                >
                  {t('marketing', 'nav_sign_in')}
                </Link>
                <Link
                  href={route('register')}
                  className="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-primary-600 rounded-lg px-3 py-1.5 hover:bg-primary-700 transition-colors"
                >
                  {t('marketing', 'nav_join_free')}
                </Link>
              </div>
            </div>

            {/* Results grid */}
            {results.data.length > 0 ? (
              <>
                <div className="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                  {results.data.map(profile => (
                    <div
                      key={profile.id}
                      className="group bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
                    >
                      {/* Avatar */}
                      <div className="relative h-32 overflow-hidden">
                        <AvatarPlaceholder gender={profile.gender} />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent" />
                        {profile.is_verified && (
                          <div className="absolute top-2.5 right-2.5 bg-white/90 backdrop-blur-sm rounded-full p-0.5">
                            <CheckCircle2 size={14} className="text-emerald-500" />
                          </div>
                        )}
                        <div className="absolute bottom-2 left-3 flex items-center gap-1.5">
                          <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${profile.gender === 'female' ? 'bg-rose-500/90 text-white' : 'bg-blue-500/90 text-white'}`}>
                            {profile.gender === 'female' ? t('common', 'female') : t('common', 'male')}
                          </span>
                          {profile.sect && (
                            <span className="text-[10px] font-medium bg-white/80 text-slate-700 px-2 py-0.5 rounded-full">{profile.sect}</span>
                          )}
                        </div>
                      </div>

                      {/* Info */}
                      <div className="p-3.5">
                        {/* Age + height */}
                        <div className="mb-1.5">
                          <p className="font-semibold text-slate-900 text-sm leading-tight">
                            {profile.age ? t('marketing', 'public_age_years', { n: profile.age }) : '—'}
                            {profile.height_cm ? `, ${Math.floor(profile.height_cm / 30.48)}′${Math.round((profile.height_cm / 30.48 % 1) * 12)}″` : ''}
                          </p>
                          {profile.marital_status && (
                            <p className="text-xs text-slate-400 mt-0.5">
                              {t('biodata', profile.marital_status) || profile.marital_status.replace('_', ' ')}
                            </p>
                          )}
                        </div>

                        {/* Details */}
                        <div className="space-y-0.5 mb-3">
                          {(profile.district || profile.division) && (
                            <div className="flex items-center gap-1.5 text-xs text-slate-500">
                              <MapPin size={10} className="text-slate-300 flex-shrink-0" />
                              <span className="truncate">{[profile.district, profile.division].filter(Boolean).join(', ')}</span>
                            </div>
                          )}
                          {profile.highest_qualification && (
                            <div className="flex items-center gap-1.5 text-xs text-slate-500">
                              <GraduationCap size={10} className="text-slate-300 flex-shrink-0" />
                              <span className="truncate">{profile.highest_qualification}</span>
                            </div>
                          )}
                          {profile.occupation && (
                            <div className="flex items-center gap-1.5 text-xs text-slate-500">
                              <Briefcase size={10} className="text-slate-300 flex-shrink-0" />
                              <span className="truncate">{profile.occupation}</span>
                            </div>
                          )}
                        </div>

                        <Link
                          href={route('profiles.show', { registrationId: profile.id })}
                          className="block text-center text-xs font-semibold text-primary-600 border border-primary-200 rounded-xl py-2 hover:bg-primary-50 hover:border-primary-400 transition-colors"
                        >
                          {t('marketing', 'public_view_biodata')} →
                        </Link>
                      </div>
                    </div>
                  ))}
                </div>

                {/* Pagination */}
                {results.last_page > 1 && (
                  <div className="flex items-center justify-center gap-2 mt-8">
                    {results.current_page > 1 && (
                      <Link
                        href={results.links.find(l => l.label === '&laquo; Previous')?.url ?? '#'}
                        className="flex items-center gap-1 px-4 py-2 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition-colors"
                      >
                        <ChevronLeft size={15} /> {t('common', 'previous')}
                      </Link>
                    )}
                    <span className="text-sm text-slate-500">
                      {t('common', 'page', { current: results.current_page, total: results.last_page })}
                    </span>
                    {results.current_page < results.last_page && (
                      <Link
                        href={results.links.find(l => l.label === 'Next &raquo;')?.url ?? '#'}
                        className="flex items-center gap-1 px-4 py-2 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition-colors"
                      >
                        {t('common', 'next')} <ChevronRight size={15} />
                      </Link>
                    )}
                  </div>
                )}
              </>
            ) : (
              <div className="text-center py-20 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50">
                <Search size={32} className="text-slate-300 mx-auto mb-3" />
                <p className="text-slate-500 font-medium mb-1">{t('common', 'no_results')}</p>
                <p className="text-sm text-slate-400 mb-4">{t('marketing', 'public_no_results_sub')}</p>
                <button
                  onClick={clearFilters}
                  className="text-sm font-semibold text-primary-600 hover:underline"
                >
                  {t('common', 'clear')}
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </MarketingLayout>
  )
}
