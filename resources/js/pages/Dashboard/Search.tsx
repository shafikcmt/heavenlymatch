/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { ProfileCard } from '@/components/profile/ProfileCard'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { type PaginatedResponse, type ProfileCard as ProfileCardType } from '@/types'
import { Search as SearchIcon, SlidersHorizontal } from 'lucide-react'
import { useState } from 'react'
import { cn } from '@/lib/utils'

interface Filters {
  age_min?: string; age_max?: string
  height_cm_min?: string; height_cm_max?: string
  division?: string; district?: string; residing_country?: string
  marital_status?: string; occupation_category?: string; education?: string
  income_min?: string; income_max?: string
  keyword?: string
}

interface Props {
  results: PaginatedResponse<ProfileCardType>
  filters: Filters
  membershipTier: string
  platformMode: string
}

export default function Search({ results, filters, membershipTier, platformMode }: Props) {
  const [showFilters, setShowFilters] = useState(false)
  const { data, setData, get, processing } = useForm<Filters>(filters)

  const search = (e: React.FormEvent) => {
    e.preventDefault()
    get(route('search.index'), { preserveState: true, preserveScroll: true })
  }

  const Select = ({ name, label, options }: {
    name: keyof Filters; label: string
    options: { value: string; label: string }[]
  }) => (
    <div>
      <label className="block text-xs font-medium text-slate-600 mb-1">{label}</label>
      <select
        value={data[name] ?? ''}
        onChange={e => setData(name, e.target.value)}
        className="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
      >
        <option value="">Any</option>
        {options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
      </select>
    </div>
  )

  return (
    <AppLayout>
      <Head title="Search Profiles" />

      <div className="max-w-6xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-slate-900 mb-6">Search Profiles</h1>

        <form onSubmit={search}>
          {/* Keyword search */}
          <div className="flex gap-3 mb-4">
            <div className="relative flex-1">
              <SearchIcon className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={16} />
              <input
                type="text"
                value={data.keyword ?? ''}
                onChange={e => setData('keyword', e.target.value)}
                placeholder="Search by location, occupation..."
                className="w-full rounded-xl border border-slate-300 bg-white pl-9 pr-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
              />
            </div>
            <Button
              type="button"
              variant="outline"
              onClick={() => setShowFilters(v => !v)}
              className="gap-2"
            >
              <SlidersHorizontal size={16} />
              Filters
            </Button>
            <Button type="submit" isLoading={processing}>Search</Button>
          </div>

          {/* Advanced filters */}
          {showFilters && (
            <div className="rounded-2xl border border-slate-200 bg-white p-6 mb-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
              <div>
                <label className="block text-xs font-medium text-slate-600 mb-1">Age Range</label>
                <div className="flex gap-2">
                  <input
                    type="number" placeholder="Min" min={18} max={80}
                    value={data.age_min ?? ''}
                    onChange={e => setData('age_min', e.target.value)}
                    className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
                  />
                  <input
                    type="number" placeholder="Max" min={18} max={80}
                    value={data.age_max ?? ''}
                    onChange={e => setData('age_max', e.target.value)}
                    className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none"
                  />
                </div>
              </div>

              <Select name="marital_status" label="Marital Status" options={[
                { value: 'never_married', label: 'Never Married' },
                { value: 'divorced', label: 'Divorced' },
                { value: 'widowed', label: 'Widowed' },
              ]} />

              <Select name="education" label="Education" options={[
                { value: 'hsc', label: 'HSC / A-Level' },
                { value: 'graduation', label: "Bachelor's" },
                { value: 'post_graduation', label: "Master's" },
                { value: 'phd', label: 'PhD' },
              ]} />

              <Select name="occupation_category" label="Occupation" options={[
                { value: 'business', label: 'Business' },
                { value: 'service_govt', label: 'Govt Job' },
                { value: 'service_private', label: 'Private Job' },
                { value: 'medical', label: 'Medical' },
                { value: 'engineering', label: 'Engineering' },
                { value: 'it', label: 'IT / Tech' },
                { value: 'abroad_job', label: 'Abroad Job' },
              ]} />

              <Input
                label="Division"
                value={data.division ?? ''}
                onChange={e => setData('division', e.target.value)}
                placeholder="e.g. Dhaka"
              />

              <Input
                label="District"
                value={data.district ?? ''}
                onChange={e => setData('district', e.target.value)}
                placeholder="e.g. Mirpur"
              />

              <Select name="residing_country" label="Country" options={[
                { value: 'Bangladesh', label: 'Bangladesh' },
                { value: 'UK', label: 'UK' },
                { value: 'USA', label: 'USA' },
                { value: 'Canada', label: 'Canada' },
                { value: 'Australia', label: 'Australia' },
                { value: 'UAE', label: 'UAE' },
                { value: 'Qatar', label: 'Qatar' },
                { value: 'Saudi Arabia', label: 'Saudi Arabia' },
              ]} />
            </div>
          )}
        </form>

        {/* Results */}
        <p className="text-sm text-slate-500 mb-4">
          {results.total} profile{results.total !== 1 ? 's' : ''} found
        </p>

        {results.data.length === 0 ? (
          <div className="text-center py-20">
            <div className="text-5xl mb-4">🔍</div>
            <p className="text-slate-500">No profiles match your filters. Try broadening your search.</p>
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
                    ← Previous
                  </Button>
                )}
                <span className="text-sm text-slate-500">
                  Page {results.current_page} of {results.last_page}
                </span>
                {results.current_page < results.last_page && (
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => router.get(results.next_page_url ?? '', {}, { preserveState: true })}
                  >
                    Next →
                  </Button>
                )}
              </div>
            )}
          </>
        )}
      </div>
    </AppLayout>
  )
}
