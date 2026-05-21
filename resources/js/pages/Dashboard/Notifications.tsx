/// <reference path="../../types/ziggy.d.ts" />
import { Head, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { relativeTime } from '@/lib/utils'
import { cn } from '@/lib/utils'
import { type PaginatedResponse } from '@/types'
import { Bell } from 'lucide-react'

interface Notification {
  id: string
  type: string
  data: Record<string, string>
  read_at: string | null
  created_at: string
}

interface Props {
  notifications: PaginatedResponse<Notification>
  unreadCount: number
}

export default function Notifications({ notifications, unreadCount }: Props) {
  const markAllRead = () => router.post(route('notifications.read-all'), {}, { preserveScroll: true })
  const markRead = (id: string) => router.post(route('notifications.read', { id }), {}, { preserveScroll: true })

  const getIcon = (type: string) => {
    if (type.includes('Interest')) return '💌'
    if (type.includes('Photo')) return '📷'
    if (type.includes('Match')) return '💑'
    if (type.includes('Message')) return '💬'
    return '🔔'
  }

  const getMessage = (n: Notification) =>
    n.data.message ?? n.data.body ?? 'You have a new notification'

  return (
    <AppLayout>
      <Head title="Notifications" />

      <div className="max-w-3xl mx-auto px-4 py-8">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-3">
            <h1 className="text-2xl font-bold text-slate-900">Notifications</h1>
            {unreadCount > 0 && (
              <span className="rounded-full bg-red-500 text-white text-xs font-bold px-2.5 py-0.5">
                {unreadCount}
              </span>
            )}
          </div>
          {unreadCount > 0 && (
            <Button variant="outline" size="sm" onClick={markAllRead}>
              Mark all as read
            </Button>
          )}
        </div>

        {notifications.data.length === 0 ? (
          <div className="text-center py-20">
            <Bell size={40} className="mx-auto text-slate-300 mb-4" />
            <p className="text-slate-500">No notifications yet.</p>
          </div>
        ) : (
          <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden divide-y divide-slate-100">
            {notifications.data.map(n => (
              <div
                key={n.id}
                className={cn(
                  'flex gap-4 items-start px-5 py-4 cursor-pointer hover:bg-slate-50 transition-colors',
                  !n.read_at && 'bg-primary-50',
                )}
                onClick={() => !n.read_at && markRead(n.id)}
              >
                <span className="text-2xl flex-shrink-0">{getIcon(n.type)}</span>
                <div className="flex-1 min-w-0">
                  <p className={cn('text-sm', !n.read_at ? 'font-semibold text-slate-900' : 'text-slate-600')}>
                    {getMessage(n)}
                  </p>
                  <p className="text-xs text-slate-400 mt-0.5">{relativeTime(n.created_at)}</p>
                </div>
                {!n.read_at && (
                  <div className="h-2 w-2 rounded-full bg-primary-600 flex-shrink-0 mt-1.5" />
                )}
              </div>
            ))}
          </div>
        )}
      </div>
    </AppLayout>
  )
}
