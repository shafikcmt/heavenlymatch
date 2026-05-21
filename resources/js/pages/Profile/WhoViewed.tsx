/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { calcAge, relativeTime } from '@/lib/utils'
import { type PaginatedResponse } from '@/types'
import { Eye, Lock } from 'lucide-react'

interface ViewerEntry {
  viewer: {
    registration_id: string
    name: string
    gender: string
    biodata?: { district?: string; occupation?: string; birth_date?: string }
  }
  created_at: string
}

interface Props {
  viewers: PaginatedResponse<ViewerEntry>
  isPremium: boolean
  totalViews: number
}

export default function WhoViewed({ viewers, isPremium, totalViews }: Props) {
  return (
    <AppLayout>
      <Head title="Who Viewed Me" />

      <div className="max-w-3xl mx-auto px-4 py-8">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-bold text-slate-900">Who Viewed My Profile</h1>
            <p className="text-sm text-slate-500 mt-1">{totalViews} total profile views</p>
          </div>
          <Eye size={24} className="text-slate-300" />
        </div>

        {!isPremium && (
          <div className="rounded-2xl bg-amber-50 border border-amber-200 p-5 mb-6 flex gap-4 items-center">
            <Lock size={24} className="text-amber-500 flex-shrink-0" />
            <div>
              <p className="font-semibold text-amber-900">Upgrade to see all viewers</p>
              <p className="text-sm text-amber-700">Free accounts see the last 5 viewers. Premium unlocks full history.</p>
            </div>
            <Link href={route('upgrade.plans')} className="ml-auto flex-shrink-0">
              <Button variant="premium" size="sm">Upgrade</Button>
            </Link>
          </div>
        )}

        {viewers.data.length === 0 ? (
          <div className="text-center py-20">
            <Eye size={40} className="mx-auto text-slate-300 mb-4" />
            <p className="text-slate-500">No profile views yet.</p>
          </div>
        ) : (
          <div className="space-y-3">
            {viewers.data.map((entry, i) => {
              const { viewer } = entry
              const age = viewer.biodata?.birth_date ? calcAge(viewer.biodata.birth_date) : null

              return (
                <div key={i} className="rounded-2xl border border-slate-200 bg-white p-4 flex gap-4 items-center">
                  <div className="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center text-xl flex-shrink-0">
                    {viewer.gender === 'male' ? '👨' : '👩'}
                  </div>
                  <div className="flex-1 min-w-0">
                    <Link href={route('profile.show', { registrationId: viewer.registration_id })}>
                      <p className="font-semibold text-slate-900 hover:text-primary-600 transition-colors text-sm">
                        {viewer.name}
                      </p>
                    </Link>
                    <p className="text-xs text-slate-400">
                      {[age ? `${age} yrs` : null, viewer.biodata?.district, viewer.biodata?.occupation]
                        .filter(Boolean).join(' · ')}
                    </p>
                  </div>
                  <p className="text-xs text-slate-400 flex-shrink-0">{relativeTime(entry.created_at)}</p>
                </div>
              )
            })}
          </div>
        )}
      </div>
    </AppLayout>
  )
}
