/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { cn, relativeTime } from '@/lib/utils'
import { useEffect, useRef, useState } from 'react'
import axios from 'axios'
import { Send, AlertTriangle, Flag } from 'lucide-react'
import { useTranslation } from '@/lib/i18n'

interface Message {
  id: number
  sender_id: string
  body: string
  created_at: string
}

interface Conversation { id: number }
interface OtherUser {
  registration_id: string
  name: string
  gender: string
  biodata?: { occupation?: string; district?: string }
}

interface Props {
  conversation: Conversation
  messages: Message[]
  other: OtherUser
  myId: string
}

export default function Thread({ conversation, messages: initialMessages, other, myId }: Props) {
  const { t } = useTranslation()
  const [messages, setMessages]   = useState<Message[]>(initialMessages)
  const [body, setBody]           = useState('')
  const [sending, setSending]     = useState(false)
  const [sendError, setSendError] = useState<string | null>(null)
  const bottomRef  = useRef<HTMLDivElement>(null)
  const lastIdRef  = useRef(initialMessages[initialMessages.length - 1]?.id ?? 0)

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: 'smooth' })
  }, [messages])

  // Long-poll every 4 seconds for new messages
  useEffect(() => {
    const poll = async () => {
      try {
        const { data } = await axios.get<Message[]>(
          route('inbox.poll', { conversationId: conversation.id, afterId: lastIdRef.current })
        )
        if (data.length) {
          setMessages(prev => [...prev, ...data])
          lastIdRef.current = data[data.length - 1]!.id
        }
      } catch {}
    }
    const timer = setInterval(poll, 4000)
    return () => clearInterval(timer)
  }, [conversation.id])

  const sendMessage = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!body.trim() || sending) return
    setSending(true)
    setSendError(null)

    try {
      const { data } = await axios.post<Message>(
        route('inbox.send', { conversationId: conversation.id }),
        { body: body.trim() }
      )
      setMessages(prev => [...prev, data])
      lastIdRef.current = data.id
      setBody('')
    } catch {
      setSendError(t('inbox', 'error_send'))
    } finally {
      setSending(false)
    }
  }

  return (
    <AppLayout>
      <Head title={t('inbox', 'chat_with').replace(':name', other.name)} />

      <div className="max-w-3xl mx-auto px-4 py-4 flex flex-col h-[calc(100vh-8rem)]">
        {/* Header */}
        <div className="flex items-center gap-3 pb-4 border-b border-slate-200">
          <Link href={route('inbox.index')} className="text-slate-400 hover:text-slate-600 text-sm">
            ← {t('inbox', 'back_to_inbox')}
          </Link>
          <div className="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-lg">
            {other.gender === 'male' ? '👨' : '👩'}
          </div>
          <div className="flex-1 min-w-0">
            <Link href={route('profile.show', { registrationId: other.registration_id })}>
              <p className="font-semibold text-slate-900 hover:text-primary-600 transition-colors text-sm truncate">
                {other.name}
              </p>
            </Link>
            <p className="text-xs text-slate-400">
              {[other.biodata?.occupation, other.biodata?.district].filter(Boolean).join(', ')}
            </p>
          </div>
          <Link
            href={route('profile.show', { registrationId: other.registration_id })}
            className="text-xs text-slate-400 hover:text-red-500 flex items-center gap-1 shrink-0"
          >
            <Flag size={12} />
            {t('inbox', 'report_link')}
          </Link>
        </div>

        {/* Safety notice */}
        <div className="mt-3 mb-1 flex items-start gap-2 rounded-xl bg-amber-50 border border-amber-200 px-3 py-2">
          <AlertTriangle size={14} className="text-amber-500 shrink-0 mt-0.5" />
          <p className="text-xs text-amber-700">{t('inbox', 'safety_notice')}</p>
        </div>

        {/* Messages */}
        <div className="flex-1 overflow-y-auto py-4 space-y-3">
          {messages.length === 0 && (
            <p className="text-center text-sm text-slate-400 py-8">{t('inbox', 'no_messages')}</p>
          )}
          {messages.map(msg => {
            const isMine = msg.sender_id === myId
            return (
              <div key={msg.id} className={cn('flex', isMine ? 'justify-end' : 'justify-start')}>
                <div className={cn(
                  'max-w-xs lg:max-w-md px-4 py-2.5 rounded-2xl text-sm',
                  isMine
                    ? 'bg-primary-600 text-white rounded-br-sm'
                    : 'bg-slate-100 text-slate-900 rounded-bl-sm',
                )}>
                  <p>{msg.body}</p>
                  <p className={cn('text-xs mt-1', isMine ? 'text-primary-200' : 'text-slate-400')}>
                    {relativeTime(msg.created_at)}
                  </p>
                </div>
              </div>
            )
          })}
          <div ref={bottomRef} />
        </div>

        {/* Send error */}
        {sendError && (
          <p className="text-xs text-red-600 mb-1 px-1">{sendError}</p>
        )}

        {/* Input */}
        <form onSubmit={sendMessage} className="flex gap-3 pt-4 border-t border-slate-200">
          <input
            type="text"
            value={body}
            onChange={e => setBody(e.target.value)}
            placeholder={t('inbox', 'type_placeholder')}
            className="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
          />
          <Button type="submit" isLoading={sending} className="gap-2 px-4" aria-label={t('inbox', 'send')}>
            <Send size={16} />
          </Button>
        </form>
      </div>
    </AppLayout>
  )
}
