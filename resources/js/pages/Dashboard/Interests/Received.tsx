/// <reference path="../../../types/ziggy.d.ts" />
import { Head, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { calcAge, cmToFeetInches } from '@/lib/utils'
import { type PaginatedResponse } from '@/types'
import { Link } from '@inertiajs/react'

interface Interest {
  id: number
  status: string
  note?: string
  created_at: string
  sender: {
    registration_id: string
    name: string
    gender: string
    biodata?: {
      district?: string
      occupation?: string
      height_cm?: number
      birth_date?: string
    }
  }
}

interface Props {
  interests: PaginatedResponse<Interest>
}

export default function Received({ interests }: Props) {
  const accept = (id: number) =>
    router.post(route('interests.respond', { id }), { action: 'accept' }, { preserveScroll: true })

  const decline = (id: number) =>
    router.post(route('interests.respond', { id }), { action: 'reject' }, { preserveScroll: true })

  return (
    <AppLayout>
      <Head title="Received Interests" />

      <div className="max-w-3xl mx-auto px-4 py-8">
        <div className="flex items-center justify-between mb-6">
          <h1 className="text-2xl font-bold text-slate-900">Received Interests</h1>
          <Link href={route('interests.sent')}>
            <Button variant="outline" size="sm">Sent Interests →</Button>
          </Link>
        </div>

        {interests.data.length === 0 ? (
          <div className="text-center py-20">
            <div className="text-5xl mb-4">💌</div>
            <p className="text-slate-500">No interests received yet.</p>
          </div>
        ) : (
          <div className="space-y-4">
            {interests.data.map(interest => {
              const biodata = interest.sender.biodata
              const age = biodata?.birth_date ? calcAge(biodata.birth_date) : null

              return (
                <div key={interest.id} className="rounded-2xl border border-slate-200 bg-white p-5 flex gap-4 items-start">
                  <div className="h-14 w-14 rounded-full bg-primary-100 flex items-center justify-center text-2xl flex-shrink-0">
                    {interest.sender.gender === 'male' ? '👨' : '👩'}
                  </div>

                  <div className="flex-1 min-w-0">
                    <Link href={route('profile.show', { registrationId: interest.sender.registration_id })}>
                      <p className="font-semibold text-slate-900 hover:text-primary-600 transition-colors">
                        {interest.sender.name}
                      </p>
                    </Link>
                    <p className="text-sm text-slate-500">
                      {[
                        age ? `${age} yrs` : null,
                        biodata?.district,
                        biodata?.occupation,
                        biodata?.height_cm ? cmToFeetInches(biodata.height_cm) : null,
                      ].filter(Boolean).join(' · ')}
                    </p>
                    {interest.note && (
                      <p className="mt-2 text-sm text-slate-600 bg-slate-50 rounded-xl px-3 py-2">
                        "{interest.note}"
                      </p>
                    )}
                  </div>

                  <div className="flex gap-2 flex-shrink-0">
                    {interest.status === 'pending' ? (
                      <>
                        <Button size="sm" onClick={() => accept(interest.id)}>Accept</Button>
                        <Button size="sm" variant="outline" onClick={() => decline(interest.id)}>Decline</Button>
                      </>
                    ) : (
                      <Badge variant={interest.status === 'accepted' ? 'success' : 'danger'}>
                        {interest.status === 'accepted' ? 'Accepted' : 'Declined'}
                      </Badge>
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
