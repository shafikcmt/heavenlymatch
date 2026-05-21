/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { type PaginatedResponse } from '@/types'
import { relativeTime } from '@/lib/utils'
import { cn } from '@/lib/utils'

interface ConversationItem {
  id: number
  participant_a: string
  participant_b: string
  last_message?: { body: string; sender_id: string; created_at: string }
  updated_at: string
  other_participant?: { name: string; gender: string; registration_id: string }
}

interface Props {
  conversations: PaginatedResponse<ConversationItem>
  myId: string
}

export default function InboxIndex({ conversations, myId }: Props) {
  return (
    <AppLayout>
      <Head title="Inbox" />

      <div className="max-w-3xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-slate-900 mb-6">Inbox</h1>

        {conversations.data.length === 0 ? (
          <div className="text-center py-20">
            <div className="text-5xl mb-4">💬</div>
            <p className="text-slate-500">No conversations yet.</p>
            <p className="text-sm text-slate-400 mt-1">
              Accept an interest to start messaging.
            </p>
          </div>
        ) : (
          <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden divide-y divide-slate-100">
            {conversations.data.map(convo => {
              const otherId = convo.participant_a === myId ? convo.participant_b : convo.participant_a
              const other = convo.other_participant
              const preview = convo.last_message

              return (
                <Link
                  key={convo.id}
                  href={route('inbox.show', { conversationId: convo.id })}
                  className="flex gap-4 items-center px-5 py-4 hover:bg-slate-50 transition-colors"
                >
                  <div className="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center text-xl flex-shrink-0">
                    {other?.gender === 'male' ? '👨' : '👩'}
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="font-semibold text-slate-900 truncate">
                      {other?.name ?? otherId}
                    </p>
                    <p className={cn(
                      'text-sm truncate',
                      preview?.sender_id !== myId ? 'text-slate-700 font-medium' : 'text-slate-400',
                    )}>
                      {preview
                        ? `${preview.sender_id === myId ? 'You: ' : ''}${preview.body}`
                        : 'No messages yet'}
                    </p>
                  </div>
                  <div className="text-xs text-slate-400 flex-shrink-0">
                    {preview ? relativeTime(preview.created_at) : ''}
                  </div>
                </Link>
              )
            })}
          </div>
        )}
      </div>
    </AppLayout>
  )
}
