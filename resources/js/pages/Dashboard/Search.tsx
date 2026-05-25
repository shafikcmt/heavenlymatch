/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { ProfileCard } from '@/components/profile/ProfileCard'
import { Button } from '@/components/ui/Button'
import { type PaginatedResponse, type ProfileCard as ProfileCardType } from '@/types'
import { Search as SearchIcon, SlidersHorizontal, X } from 'lucide-react'
import { useState } from 'react'
import { useTranslation } from '@/lib/i18n'

interface Filters {
  age_min?: string; age_max?: string
  height_cm_min?: string; height_cm_max?: string
  division?: string; district?: string; residing_country?: string
  marital_status?: string; occupation_category?: string; education?: string
  religion?: string; sect?: string
  income_min?: string; income_max?: string
  keyword?: string; sort?: string
}

interface Props {
  results: PaginatedResponse<ProfileCardType>
  filters: Filters
  membershipTier: string
  platformMode: string
}

function FilterSelect({ name, label, value, onChange, options }: {
  name: string; label: string; value: string
  onChange: (v: string) => void
  options: { value: string; label: string }[]
}) {
  const { t } = useTranslation()
  return (
    <div>
      <label className="block text-xs font-medium text-slate-600 mb-1">{label}</label>
      <select
        value={value}
        onChange={e => onChange(e.target.value)}
        className="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
      >
        <option value="">{t('common', 'any')}</option>
        {options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
      </select>
    </div>
  )
}

function FilterInput({ label, value, onChange, placeholder, type = 'text' }: {
  label: string; value: string; onChange: (v: string) => void
  placeholder?: string; type?: string
}) {
  return (
    <div>
      <label className="block text-xs font-medium text-slate-600 mb-1">{label}</label>
      <input
        type={type}
        value={value}
        onChange={e => onChange(e.target.value)}
        placeholder={placeholder}
        className="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
      />
    </div>
  )
}

export default function Search({ results, filters, membershipTier, platformMode }: Props) {
  const { t } = useTranslation()
  const [showFilters, setShowFilters] = useState(
    Object.values(filters).some(v => v != null && v !== '' && v !== 'newest'),
  )

  const { data, setData, get, processing } = useForm<Filters>({
    age_min: filters.age_min ?? '',
    age_max: filters.age_max ?? '',
    height_cm_min: filters.height_cm_min ?? '',
    height_cm_max: filters.height_cm_max ?? '',
    division: filters.division ?? '',
    district: filters.district ?? '',
    residing_country: filters.residing_country ?? '',
    marital_status: filters.marital_status ?? '',
    occupation_category: filters.occupation_category ?? '',
    education: filters.education ?? '',
    religion: filters.religion ?? '',
    sect: filters.sect ?? '',
    income_min: filters.income_min ?? '',
    income_max: filters.income_max ?? '',
    keyword: filters.keyword ?? '',
    sort: filters.sort ?? 'newest',
  })

  const search = (e: React.FormEvent) => {
    e.preventDefault()
    get(route('search.index'), { preserveState: true, preserveScroll: true })
  }

  const clearFilters = () => {
    router.get(route('search.index'), {}, { preserveState: false })
  }

  const hasActiveFilters = Object.entries(data)
    .filter(([k]) => k !== 'sort' && k !== 'keyword')
    .some(([, v]) => v != null && v !== '')

  const sortOptions = [
    { value: 'newest',      label: t('dashboard', 'sort_newest') },
    { value: 'last_active', label: t('dashboard', 'sort_last_active') },
    { value: 'featured',    label: t('dashboard', 'sort_featured') },
    { value: 'score',       label: t('dashboard', 'sort_score') },
  ]

  return (
    <AppLayout>
      <Head title={t('dashboard', 'search_title')} />

      <div className="max-w-6xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-slate-900 mb-6">{t('dashboard', 'search_title')}</h1>

        <form onSubmit={search}>
          {/* Top bar: keyword + sort + filters toggle */}
          <div className="flex gap-3 mb-4 flex-wrap sm:flex-nowrap">
            <div className="relative flex-1 min-w-0">
              <SearchIcon className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" size={16} />
              <input
                type="text"
                value={data.keyword ?? ''}
                onChange={e => setData('keyword', e.target.value)}
                placeholder={t('dashboard', 'search_placeholder')}
                className="w-full rounded-xl border border-slate-300 bg-white pl-9 pr-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
              />
            </div>

            {/* Sort */}
            <div className="flex items-center gap-2 shrink-0">
              <span className="text-xs font-medium text-slate-500 hidden sm:block">{t('dashboard', 'sort_label')}</span>
              <select
                value={data.sort ?? 'newest'}
                onChange={e => setData('sort', e.target.value)}
                className="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none"
              >
                {sortOptions.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
              </select>
            </div>

            <Button
              type="button"
              variant="outline"
              onClick={() => setShowFilters(v => !v)}
              className="gap-2 shrink-0"
            >
              <SlidersHorizontal size={16} />
              {t('common', 'filter')}
              {hasActiveFilters && <span className="h-2 w-2 rounded-full bg-primary-500" />}
            </Button>

            <Button type="submit" isLoading={processing} className="shrink-0">
              {t('common', 'search_action')}
            </Button>
          </div>

          {/* Advanced filters panel */}
          {showFilters && (
            <div className="rounded-2xl border border-slate-200 bg-white p-6 mb-6">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-sm font-semibold text-slate-700">{t('common', 'filter')}</h2>
                {hasActiveFilters && (
                  <button
                    type="button"
                    onClick={clearFilters}
                    className="flex items-center gap-1 text-xs text-red-500 hover:text-red-600 font-medium"
                  >
                    <X size={12} />
                    {t('dashboard', 'clear_filters')}
                  </button>
                )}
              </div>

              <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                {/* Age range */}
                <div>
                  <label className="block text-xs font-medium text-slate-600 mb-1">{t('dashboard', 'filter_age_range')}</label>
                  <div className="flex gap-2">
                    <input
                      type="number" placeholder={t('common', 'filter_min')} min={18} max={80}
                      value={data.age_min ?? ''}
                      onChange={e => setData('age_min', e.target.value)}
                      className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
                    />
                    <input
                      type="number" placeholder={t('common', 'filter_max')} min={18} max={80}
                      value={data.age_max ?? ''}
                      onChange={e => setData('age_max', e.target.value)}
                      className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
                    />
                  </div>
                </div>

                {/* Height range */}
                <div>
                  <label className="block text-xs font-medium text-slate-600 mb-1">{t('dashboard', 'height_range')}</label>
                  <div className="flex gap-2">
                    <input
                      type="number" placeholder={t('common', 'filter_min')} min={140} max={220}
                      value={data.height_cm_min ?? ''}
                      onChange={e => setData('height_cm_min', e.target.value)}
                      className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
                    />
                    <input
                      type="number" placeholder={t('common', 'filter_max')} min={140} max={220}
                      value={data.height_cm_max ?? ''}
                      onChange={e => setData('height_cm_max', e.target.value)}
                      className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
                    />
                  </div>
                </div>

                <FilterSelect
                  name="marital_status" label={t('dashboard', 'filter_marital_status')}
                  value={data.marital_status ?? ''} onChange={v => setData('marital_status', v)}
                  options={[
                    { value: 'never_married', label: 'Never Married' },
                    { value: 'divorced',      label: 'Divorced' },
                    { value: 'widowed',       label: 'Widowed' },
                  ]}
                />

                <FilterSelect
                  name="education" label={t('dashboard', 'filter_education')}
                  value={data.education ?? ''} onChange={v => setData('education', v)}
                  options={[
                    { value: 'hsc',             label: 'HSC / A-Level' },
                    { value: 'graduation',      label: "Bachelor's" },
                    { value: 'post_graduation', label: "Master's" },
                    { value: 'phd',             label: 'PhD' },
                  ]}
                />

                <FilterSelect
                  name="occupation_category" label={t('dashboard', 'filter_profession')}
                  value={data.occupation_category ?? ''} onChange={v => setData('occupation_category', v)}
                  options={[
                    { value: 'business',       label: 'Business' },
                    { value: 'service_govt',   label: 'Govt Job' },
                    { value: 'service_private',label: 'Private Job' },
                    { value: 'medical',        label: 'Medical' },
                    { value: 'engineering',    label: 'Engineering' },
                    { value: 'it',             label: 'IT / Tech' },
                    { value: 'abroad_job',     label: 'Abroad Job' },
                  ]}
                />

                <FilterSelect
                  name="religion" label={t('dashboard', 'filter_religion')}
                  value={data.religion ?? ''} onChange={v => setData('religion', v)}
                  options={[
                    { value: 'Islam',   label: 'Islam' },
                    { value: 'Hindu',   label: 'Hindu' },
                    { value: 'Other',   label: 'Other' },
                  ]}
                />

                <FilterSelect
                  name="sect" label={t('dashboard', 'filter_sect')}
                  value={data.sect ?? ''} onChange={v => setData('sect', v)}
                  options={[
                    { value: 'sunni',      label: 'Sunni' },
                    { value: 'hanafi',     label: 'Hanafi' },
                    { value: 'shafi',      label: 'Shafi\'i' },
                    { value: 'maliki',     label: 'Maliki' },
                    { value: 'hanbali',    label: 'Hanbali' },
                    { value: 'ahle_hadis', label: 'Ahle Hadis' },
                  ]}
                />

                <FilterInput
                  label={t('dashboard', 'filter_division')}
                  value={data.division ?? ''}
                  onChange={v => setData('division', v)}
                  placeholder="e.g. Dhaka"
                />

                <FilterInput
                  label={t('dashboard', 'filter_district')}
                  value={data.district ?? ''}
                  onChange={v => setData('district', v)}
                  placeholder="e.g. Mirpur"
                />

                <FilterSelect
                  name="residing_country" label={t('dashboard', 'filter_country')}
                  value={data.residing_country ?? ''} onChange={v => setData('residing_country', v)}
                  options={[
                    { value: 'Bangladesh',    label: 'Bangladesh' },
                    { value: 'UK',            label: 'UK' },
                    { value: 'USA',           label: 'USA' },
                    { value: 'Canada',        label: 'Canada' },
                    { value: 'Australia',     label: 'Australia' },
                    { value: 'UAE',           label: 'UAE' },
                    { value: 'Qatar',         label: 'Qatar' },
                    { value: 'Saudi Arabia',  label: 'Saudi Arabia' },
                  ]}
                />
              </div>

              <div className="flex justify-end mt-4 gap-3">
                {hasActiveFilters && (
                  <button
                    type="button"
                    onClick={clearFilters}
                    className="text-sm text-slate-500 hover:text-slate-700"
                  >
                    {t('dashboard', 'filter_clear_all')}
                  </button>
                )}
                <Button type="submit" size="sm" isLoading={processing}>
                  {t('common', 'search_action')}
                </Button>
              </div>
            </div>
          )}
        </form>

        {/* Results header */}
        <p className="text-sm text-slate-500 mb-4">
          {t('dashboard', 'profiles_found', { count: results.total })}
        </p>

        {results.data.length === 0 ? (
          <div className="text-center py-20">
            <div className="text-5xl mb-4">🔍</div>
            <p className="text-slate-500 mb-4">{t('dashboard', 'no_profiles_found')}</p>
            {hasActiveFilters && (
              <button onClick={clearFilters} className="text-sm text-primary-600 hover:underline font-medium">
                {t('dashboard', 'clear_filters')}
              </button>
            )}
          </div>
        ) : (
          <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {results.data.map(profile => (
                <ProfileCard key={profile.registration_id} profile={profile} />
              ))}
            </div>

            {/* Pagination */}
            {results.last_page > 1 && (
              <div className="flex items-center justify-center gap-2 mt-10">
                {results.current_page > 1 && (
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => router.get(results.prev_page_url ?? '', {}, { preserveState: true })}
                  >
                    ← {t('common', 'previous')}
                  </Button>
                )}
                <span className="text-sm text-slate-500">
                  {results.current_page} / {results.last_page}
                </span>
                {results.current_page < results.last_page && (
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => router.get(results.next_page_url ?? '', {}, { preserveState: true })}
                  >
                    {t('common', 'next')} →
                  </Button>
                )}
              </div>
            )}

            {/* Free tier limit message */}
            {membershipTier !== 'premium' && results.total > results.per_page && (
              <div className="mt-8 rounded-2xl bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 p-6 text-center">
                <p className="text-sm font-semibold text-amber-900 mb-1">{t('dashboard', 'search_limited')}</p>
                <a href={route('upgrade.plans')} className="text-sm text-amber-700 hover:underline">
                  {t('dashboard', 'upgrade_now')} →
                </a>
              </div>
            )}
          </>
        )}
      </div>
    </AppLayout>
  )
}
