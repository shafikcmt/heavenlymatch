/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { ProfileCard } from '@/components/profile/ProfileCard'
import { Badge } from '@/components/ui/Badge'
import { Button } from '@/components/ui/Button'
import { type ProfileCard as ProfileCardType } from '@/types'

interface Props {
  matches: ProfileCardType[]
  hasBiodata: boolean
  membershipTier: string
}

export default function Matches({ matches, hasBiodata, membershipTier }: Props) {
  const isPremium = membershipTier === 'premium'

  if (!hasBiodata) {
    return (
      <AppLayout>
        <Head title="My Matches" />
        <div className="max-w-lg mx-auto px-4 py-16 text-center">
          <div className="text-6xl mb-4">💑</div>
          <h2 className="text-xl font-bold text-slate-900 mb-2">Complete your biodata first</h2>
          <p className="text-sm text-slate-500 mb-6">
            Our matching engine needs your biodata to find compatible profiles for you.
          </p>
          <Link href={route('biodata.wizard', { step: 1 })}>
            <Button size="lg">Create Biodata →</Button>
          </Link>
        </div>
      </AppLayout>
    )
  }

  return (
    <AppLayout>
      <Head title="My Matches" />

      <div className="max-w-6xl mx-auto px-4 py-8">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-bold text-slate-900">Your Matches</h1>
            <p className="text-sm text-slate-500 mt-1">
              Ranked by compatibility score
            </p>
          </div>
          {!isPremium && (
            <Link href={route('upgrade.plans')}>
              <Button variant="premium" size="sm">
                Unlock All Matches
              </Button>
            </Link>
          )}
        </div>

        {matches.length === 0 ? (
          <div className="text-center py-20">
            <div className="text-5xl mb-4">🔍</div>
            <p className="text-slate-500">No matches found yet. Check back soon as more profiles join.</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {matches.map(profile => (
              <ProfileCard key={profile.registration_id} profile={profile} />
            ))}
          </div>
        )}

        {!isPremium && matches.length >= 10 && (
          <div className="mt-10 rounded-2xl bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 p-8 text-center">
            <p className="text-lg font-bold text-amber-900 mb-2">
              You're seeing 10 of your top matches
            </p>
            <p className="text-sm text-amber-700 mb-4">
              Upgrade to Premium to see all your matches and message them directly.
            </p>
            <Link href={route('upgrade.plans')}>
              <Button variant="premium">Upgrade to Premium →</Button>
            </Link>
          </div>
        )}
      </div>
    </AppLayout>
  )
}
