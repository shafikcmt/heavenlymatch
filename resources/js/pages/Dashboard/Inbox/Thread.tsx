/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { cn, relativeTime } from '@/lib/utils'
import { useEffect, useRef, useState } from 'react'
import axios from 'axios'
import { Send } from 'lucide-react'

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
  const [messages, setMessages] = useState<Message[]>(initialMessages)
  const [body, setBody] = useState('')
  const [sending, setSending] = useState(false)
  const bottomRef = useRef<HTMLDivElement>(null)
  const lastIdRef = useRef(initialMessages[initialMessages.length - 1]?.id ?? 0)

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

    try {
      const { data } = await axios.post<Message>(
        route('inbox.send', { conversationId: conversation.id }),
        { body: body.trim() }
      )
      setMessages(prev => [...prev, data])
      lastIdRef.current = data.id
      setBody('')
    } catch {
      alert('Failed to send message. Please try again.')
    } finally {
      setSending(false)
    }
  }

  return (
    <AppLayout>
      <Head title={`Chat with ${other.name}`} />

      <div className="max-w-3xl mx-auto px-4 py-4 flex flex-col h-[calc(100vh-8rem)]">
        {/* Header */}
        <div className="flex items-center gap-3 pb-4 border-b border-slate-200">
          <Link href={route('inbox.index')} className="text-slate-400 hover:text-slate-600">←</Link>
          <div className="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-lg">
            {other.gender === 'male' ? '👨' : '👩'}
          </div>
          <div>
            <Link href={route('profile.show', { registrationId: other.registration_id })}>
              <p className="font-semibold text-slate-900 hover:text-primary-600 transition-colors text-sm">
                {other.name}
              </p>
            </Link>
            <p className="text-xs text-slate-400">
              {[other.biodata?.occupation, other.biodata?.district].filter(Boolean).join(', ')}
            </p>
          </div>
        </div>

        {/* Messages */}
        <div className="flex-1 overflow-y-auto py-4 space-y-3">
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

        {/* Input */}
        <form onSubmit={sendMessage} className="flex gap-3 pt-4 border-t border-slate-200">
          <input
            type="text"
            value={body}
            onChange={e => setBody(e.target.value)}
            placeholder="Type a message..."
            className="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
          />
          <Button type="submit" isLoading={sending} className="gap-2 px-4">
            <Send size={16} />
          </Button>
        </form>
      </div>
    </AppLayout>
  )
}
