/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { type PaginatedResponse } from '@/types'
import { relativeTime, cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import { MessageCircle } from 'lucide-react'

interface ConversationItem {
  id: number
  user_a_id: string
  user_b_id: string
  latest_message?: { body: string; sender_id: string; created_at: string }
  updated_at: string
  unread_count: number
  other_participant?: { name: string; gender: string; registration_id: string }
}

interface Props {
  conversations: PaginatedResponse<ConversationItem>
  myId: string
}

export default function InboxIndex({ conversations, myId }: Props) {
  const { t } = useTranslation()

  return (
    <AppLayout>
      <Head title={t('inbox', 'title')} />

      <div className="max-w-3xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-slate-900 mb-6">{t('inbox', 'title')}</h1>

        {conversations.data.length === 0 ? (
          <div className="text-center py-20">
            <MessageCircle size={40} className="mx-auto text-slate-300 mb-4" />
            <p className="text-slate-500">{t('inbox', 'no_conversations')}</p>
            <p className="text-sm text-slate-400 mt-1">{t('inbox', 'start_hint')}</p>
            <Link href={route('interests.received')} className="mt-4 inline-block">
              <Button variant="outline" size="sm">{t('interests', 'received_title')}</Button>
            </Link>
          </div>
        ) : (
          <>
            <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden divide-y divide-slate-100">
              {conversations.data.map(convo => {
                const other = convo.other_participant
                const preview = convo.latest_message
                const hasUnread = convo.unread_count > 0

                return (
                  <Link
                    key={convo.id}
                    href={route('inbox.show', { conversationId: convo.id })}
                    className="flex gap-4 items-center px-5 py-4 hover:bg-slate-50 transition-colors"
                  >
                    <div className="relative h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center text-xl shrink-0">
                      <span>{other?.gender === 'male' ? '👨' : '👩'}</span>
                      {hasUnread && (
                        <span className="absolute -top-0.5 -right-0.5 h-5 w-5 rounded-full bg-red-500 text-[10px] font-bold text-white flex items-center justify-center border-2 border-white">
                          {convo.unread_count > 9 ? '9+' : convo.unread_count}
                        </span>
                      )}
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className={cn('font-semibold text-slate-900 truncate', hasUnread && 'text-primary-700')}>
                        {other?.name ?? (convo.user_a_id === myId ? convo.user_b_id : convo.user_a_id)}
                      </p>
                      <p className={cn(
                        'text-sm truncate',
                        hasUnread ? 'text-slate-700 font-medium' : 'text-slate-400',
                      )}>
                        {preview
                          ? `${preview.sender_id === myId ? t('inbox', 'you_prefix') : ''}${preview.body}`
                          : t('inbox', 'no_messages')}
                      </p>
                    </div>
                    <div className="text-xs text-slate-400 shrink-0">
                      {preview ? relativeTime(preview.created_at) : ''}
                    </div>
                  </Link>
                )
              })}
            </div>

            {conversations.last_page > 1 && (
              <div className="flex items-center justify-center gap-2 mt-8">
                {conversations.current_page > 1 && (
                  <Button variant="outline" size="sm"
                    onClick={() => router.get(conversations.prev_page_url ?? '', {}, { preserveScroll: true })}
                  >
                    ← {t('common', 'previous')}
                  </Button>
                )}
                <span className="text-sm text-slate-500">
                  {conversations.current_page} / {conversations.last_page}
                </span>
                {conversations.current_page < conversations.last_page && (
                  <Button variant="outline" size="sm"
                    onClick={() => router.get(conversations.next_page_url ?? '', {}, { preserveScroll: true })}
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
