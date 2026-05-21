/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { calcAge, cmToFeetInches, relativeTime } from '@/lib/utils'
import { type PaginatedResponse } from '@/types'
import { Heart } from 'lucide-react'

interface ShortlistedProfile {
  registration_id: string
  name: string
  gender: string
  platform_mode: string
  district?: string
  division?: string
  occupation?: string
  height_cm?: number
  birth_date?: string
  photos?: Array<{ path: string; is_primary: boolean; blurred: boolean }>
  shortlisted_at: string
}

interface Props {
  shortlisted: PaginatedResponse<ShortlistedProfile>
}

export default function Shortlist({ shortlisted }: Props) {
  const removeFromShortlist = (id: string) =>
    router.post(route('shortlist.toggle'), { target_id: id }, { preserveScroll: true })

  return (
    <AppLayout>
      <Head title="My Shortlist" />

      <div className="max-w-5xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-slate-900 mb-6">
          My Shortlist
          <span className="ml-2 text-base font-normal text-slate-400">
            ({shortlisted.total} profile{shortlisted.total !== 1 ? 's' : ''})
          </span>
        </h1>

        {shortlisted.data.length === 0 ? (
          <div className="text-center py-20">
            <Heart size={40} className="mx-auto text-slate-300 mb-4" />
            <p className="text-slate-500">No shortlisted profiles yet.</p>
            <Link href={route('search.index')} className="mt-4 inline-block">
              <Button>Browse Profiles</Button>
            </Link>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {shortlisted.data.map(profile => {
              const age = profile.birth_date ? calcAge(profile.birth_date) : null
              return (
                <div key={profile.registration_id} className="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                  <Link href={route('profile.show', { registrationId: profile.registration_id })}>
                    <div className="aspect-[3/4] bg-slate-100 flex items-center justify-center text-6xl relative">
                      {profile.gender === 'male' ? '👨' : '👩'}
                      {profile.platform_mode === 'islamic' && (
                        <span className="absolute top-2 right-2 text-xs bg-emerald-500 text-white px-2 py-0.5 rounded-full">
                          Islamic
                        </span>
                      )}
                    </div>
                    <div className="p-4">
                      <p className="font-semibold text-slate-900">{profile.name}</p>
                      <p className="text-sm text-slate-500">
                        {[age ? `${age} yrs` : null, profile.district, profile.occupation].filter(Boolean).join(' · ')}
                      </p>
                      {profile.height_cm && (
                        <p className="text-xs text-slate-400 mt-1">{cmToFeetInches(profile.height_cm)}</p>
                      )}
                    </div>
                  </Link>
                  <div className="px-4 pb-4 flex gap-2">
                    <Link href={route('profile.show', { registrationId: profile.registration_id })} className="flex-1">
                      <Button variant="outline" size="sm" className="w-full">View Profile</Button>
                    </Link>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => removeFromShortlist(profile.registration_id)}
                      className="text-red-500 hover:text-red-600 hover:bg-red-50"
                    >
                      Remove
                    </Button>
                  </div>
                </div>
              )
            })}
          </div>
        )}
      </div>
    </AppLayout>
  )
}
