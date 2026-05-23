/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { calcAge, cmToFeetInches, relativeTime } from '@/lib/utils'
import { cn } from '@/lib/utils'
import { type PaginatedResponse } from '@/types'
import { useState } from 'react'
import { useTranslation } from '@/lib/i18n'
import { MessageCircle, User } from 'lucide-react'

interface Interest {
  id: number
  status: string
  initial_message?: string
  created_at: string
  conversation?: { id: number } | null
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
  counts: { pending: number; accepted: number; declined: number }
  currentStatus: string
}

const STATUS_VARIANT: Record<string, 'default' | 'success' | 'warning' | 'danger'> = {
  pending:  'warning',
  accepted: 'success',
  declined: 'danger',
}

export default function Received({ interests, counts, currentStatus }: Props) {
  const { t } = useTranslation()
  const [loadingId, setLoadingId] = useState<{ id: number; action: string } | null>(null)

  const respond = (id: number, action: 'accept' | 'reject') => {
    setLoadingId({ id, action })
    router.post(
      route('interests.respond', { id }),
      { action },
      { preserveScroll: true, onFinish: () => setLoadingId(null) },
    )
  }

  const tabs = [
    { key: 'pending',  label: t('interests', 'tab_pending'),  count: counts.pending },
    { key: 'accepted', label: t('interests', 'tab_accepted'), count: counts.accepted },
    { key: 'declined', label: t('interests', 'tab_declined'), count: counts.declined },
  ]

  const emptyMsg = currentStatus === 'pending'
    ? t('interests', 'no_received')
    : t('interests', `empty_${currentStatus}_received`)

  return (
    <AppLayout>
      <Head title={t('interests', 'received_title')} />

      <div className="max-w-3xl mx-auto px-4 py-8">
        <div className="flex items-center justify-between mb-6">
          <h1 className="text-2xl font-bold text-slate-900">{t('interests', 'received_title')}</h1>
          <Link href={route('interests.sent')}>
            <Button variant="outline" size="sm">{t('interests', 'go_sent')}</Button>
          </Link>
        </div>

        {/* Status tabs */}
        <div className="flex gap-1 mb-6 rounded-2xl bg-slate-100 p-1">
          {tabs.map(tab => (
            <button
              key={tab.key}
              onClick={() => router.get(route('interests.received'), { status: tab.key }, { preserveScroll: false })}
              className={cn(
                'flex-1 flex items-center justify-center gap-1.5 rounded-xl py-2 text-sm font-medium transition-colors',
                currentStatus === tab.key
                  ? 'bg-white text-slate-900 shadow-sm'
                  : 'text-slate-500 hover:text-slate-700',
              )}
            >
              {tab.label}
              {tab.count > 0 && (
                <span className={cn(
                  'rounded-full px-1.5 py-0.5 text-xs font-bold min-w-[1.25rem] text-center',
                  currentStatus === tab.key
                    ? 'bg-primary-100 text-primary-700'
                    : 'bg-slate-200 text-slate-600',
                )}>
                  {tab.count}
                </span>
              )}
            </button>
          ))}
        </div>

        {interests.data.length === 0 ? (
          <div className="text-center py-20">
            <div className="text-5xl mb-4">💌</div>
            <p className="text-slate-500">{emptyMsg}</p>
            {currentStatus === 'pending' && (
              <Link href={route('search.index')} className="mt-4 inline-block">
                <Button variant="outline" size="sm">{t('dashboard', 'browse_profiles')}</Button>
              </Link>
            )}
          </div>
        ) : (
          <>
            <div className="space-y-4">
              {interests.data.map(interest => {
                const biodata = interest.sender.biodata
                const age = biodata?.birth_date ? calcAge(biodata.birth_date) : null
                const busy = loadingId?.id === interest.id
                const isAccepting = busy && loadingId?.action === 'accept'
                const isDeclining = busy && loadingId?.action === 'reject'

                return (
                  <div key={interest.id} className="rounded-2xl border border-slate-200 bg-white p-5">
                    <div className="flex gap-4 items-start">
                      <div className="h-14 w-14 rounded-full bg-primary-100 flex items-center justify-center shrink-0">
                        <User size={24} className="text-primary-500" />
                      </div>

                      <div className="flex-1 min-w-0">
                        <div className="flex items-start justify-between gap-2 flex-wrap">
                          <div>
                            <Link href={route('profile.show', { registrationId: interest.sender.registration_id })}>
                              <p className="font-semibold text-slate-900 hover:text-primary-600 transition-colors">
                                {interest.sender.name}
                              </p>
                            </Link>
                            <p className="text-sm text-slate-500 mt-0.5">
                              {[
                                age ? `${age} yrs` : null,
                                biodata?.district,
                                biodata?.occupation,
                                biodata?.height_cm ? cmToFeetInches(biodata.height_cm) : null,
                              ].filter(Boolean).join(' · ')}
                            </p>
                            <p className="text-xs text-slate-400 mt-1">{relativeTime(interest.created_at)}</p>
                          </div>
                          <Badge variant={STATUS_VARIANT[interest.status] ?? 'default'}>
                            {t('interests', `status_${interest.status}`)}
                          </Badge>
                        </div>

                        {interest.initial_message && (
                          <p className="mt-2 text-sm text-slate-600 bg-slate-50 rounded-xl px-3 py-2 italic">
                            "{interest.initial_message}"
                          </p>
                        )}
                      </div>
                    </div>

                    <div className="mt-4 flex gap-2 flex-wrap">
                      <Link href={route('profile.show', { registrationId: interest.sender.registration_id })} className="shrink-0">
                        <Button variant="outline" size="sm">{t('interests', 'view_profile')}</Button>
                      </Link>

                      {interest.status === 'pending' && (
                        <>
                          <Button
                            size="sm"
                            isLoading={isAccepting}
                            disabled={busy}
                            onClick={() => respond(interest.id, 'accept')}
                          >
                            {isAccepting ? t('interests', 'accepting') : t('interests', 'accept')}
                          </Button>
                          <Button
                            size="sm"
                            variant="outline"
                            isLoading={isDeclining}
                            disabled={busy}
                            onClick={() => respond(interest.id, 'reject')}
                          >
                            {isDeclining ? t('interests', 'declining') : t('interests', 'decline')}
                          </Button>
                        </>
                      )}

                      {interest.status === 'accepted' && interest.conversation?.id && (
                        <Link href={route('inbox.show', { conversationId: interest.conversation.id })}>
                          <Button size="sm" className="gap-1.5">
                            <MessageCircle size={14} />
                            {t('interests', 'message')}
                          </Button>
                        </Link>
                      )}
                    </div>
                  </div>
                )
              })}
            </div>

            {interests.last_page > 1 && (
              <div className="flex items-center justify-center gap-2 mt-8">
                {interests.current_page > 1 && (
                  <Button variant="outline" size="sm"
                    onClick={() => router.get(interests.prev_page_url ?? '', {}, { preserveScroll: true })}
                  >
                    ← {t('common', 'previous')}
                  </Button>
                )}
                <span className="text-sm text-slate-500">{interests.current_page} / {interests.last_page}</span>
                {interests.current_page < interests.last_page && (
                  <Button variant="outline" size="sm"
                    onClick={() => router.get(interests.next_page_url ?? '', {}, { preserveScroll: true })}
                  >
                    {t('common', 'next')} →
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
