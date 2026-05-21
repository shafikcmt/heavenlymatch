/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { calcAge } from '@/lib/utils'
import { type PaginatedResponse } from '@/types'

interface Interest {
  id: number
  status: string
  note?: string
  created_at: string
  receiver: {
    registration_id: string
    name: string
    gender: string
    biodata?: {
      district?: string
      occupation?: string
      birth_date?: string
    }
  }
}

interface Props {
  interests: PaginatedResponse<Interest>
}

const STATUS_BADGE: Record<string, 'default' | 'success' | 'warning' | 'danger'> = {
  pending:  'warning',
  accepted: 'success',
  rejected: 'danger',
}

export default function Sent({ interests }: Props) {
  const withdraw = (id: number) =>
    router.delete(route('interests.withdraw', { id }), { preserveScroll: true })

  return (
    <AppLayout>
      <Head title="Sent Interests" />

      <div className="max-w-3xl mx-auto px-4 py-8">
        <div className="flex items-center justify-between mb-6">
          <h1 className="text-2xl font-bold text-slate-900">Sent Interests</h1>
          <Link href={route('interests.received')}>
            <Button variant="outline" size="sm">← Received</Button>
          </Link>
        </div>

        {interests.data.length === 0 ? (
          <div className="text-center py-20">
            <div className="text-5xl mb-4">💌</div>
            <p className="text-slate-500">You haven't sent any interests yet. Browse profiles to start.</p>
            <Link href={route('search.index')} className="mt-4 inline-block">
              <Button>Browse Profiles</Button>
            </Link>
          </div>
        ) : (
          <div className="space-y-4">
            {interests.data.map(interest => {
              const biodata = interest.receiver.biodata
              const age = biodata?.birth_date ? calcAge(biodata.birth_date) : null

              return (
                <div key={interest.id} className="rounded-2xl border border-slate-200 bg-white p-5 flex gap-4 items-start">
                  <div className="h-14 w-14 rounded-full bg-primary-100 flex items-center justify-center text-2xl flex-shrink-0">
                    {interest.receiver.gender === 'male' ? '👨' : '👩'}
                  </div>

                  <div className="flex-1 min-w-0">
                    <Link href={route('profile.show', { registrationId: interest.receiver.registration_id })}>
                      <p className="font-semibold text-slate-900 hover:text-primary-600 transition-colors">
                        {interest.receiver.name}
                      </p>
                    </Link>
                    <p className="text-sm text-slate-500">
                      {[age ? `${age} yrs` : null, biodata?.district, biodata?.occupation].filter(Boolean).join(' · ')}
                    </p>
                  </div>

                  <div className="flex items-center gap-3 flex-shrink-0">
                    <Badge variant={STATUS_BADGE[interest.status] ?? 'default'}>
                      {interest.status.charAt(0).toUpperCase() + interest.status.slice(1)}
                    </Badge>
                    {interest.status === 'pending' && (
                      <Button size="sm" variant="ghost" onClick={() => withdraw(interest.id)}>
                        Withdraw
                      </Button>
                    )}
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
