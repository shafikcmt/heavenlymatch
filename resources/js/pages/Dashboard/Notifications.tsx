/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { relativeTime, cn } from '@/lib/utils'
import { type PaginatedResponse } from '@/types'
import { Bell } from 'lucide-react'
import { useTranslation } from '@/lib/i18n'

interface Notification {
  id: number
  type: string
  title: string
  body: string
  data: Record<string, string>
  read_at: string | null
  created_at: string
}

interface Props {
  notifications: PaginatedResponse<Notification>
  unreadCount: number
}

const TYPE_ICON: Record<string, string> = {
  interest: '💌',
  photo:    '📷',
  match:    '💑',
  message:  '💬',
  payment:  '⭐',
  system:   '🔔',
}

function getIcon(type: string): string {
  const key = Object.keys(TYPE_ICON).find(k => type.toLowerCase().includes(k))
  return key ? TYPE_ICON[key]! : '🔔'
}

function getActionUrl(n: Notification): string | null {
  if (n.data?.action_url) return n.data.action_url
  if (n.data?.from) return `/profile/${n.data.from}`
  if (n.type.includes('interest')) return '/interests/received'
  if (n.type.includes('message')) return '/inbox'
  return null
}

export default function Notifications({ notifications, unreadCount }: Props) {
  const { t } = useTranslation()

  const markAllRead = () => router.post(route('notifications.read-all'), {}, { preserveScroll: true })
  const markRead    = (id: number) => router.post(route('notifications.read', { id }), {}, { preserveScroll: true })

  return (
    <AppLayout>
      <Head title={t('dashboard', 'notifications_title')} />

      <div className="max-w-3xl mx-auto px-4 py-8">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-3">
            <h1 className="text-2xl font-bold text-slate-900">{t('dashboard', 'notifications_title')}</h1>
            {unreadCount > 0 && (
              <span className="rounded-full bg-red-500 text-white text-xs font-bold px-2.5 py-0.5">
                {unreadCount}
              </span>
            )}
          </div>
          {unreadCount > 0 && (
            <Button variant="outline" size="sm" onClick={markAllRead}>
              {t('dashboard', 'mark_all_read')}
            </Button>
          )}
        </div>

        {notifications.data.length === 0 ? (
          <div className="text-center py-20">
            <Bell size={40} className="mx-auto text-slate-300 mb-4" />
            <p className="text-slate-500">{t('dashboard', 'no_notifications')}</p>
          </div>
        ) : (
          <>
            <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden divide-y divide-slate-100">
              {notifications.data.map(n => {
                const actionUrl = getActionUrl(n)

                const content = (
                  <div
                    className={cn(
                      'flex gap-4 items-start px-5 py-4 transition-colors',
                      !n.read_at ? 'bg-primary-50 cursor-pointer hover:bg-primary-100' : 'hover:bg-slate-50',
                    )}
                    onClick={() => !n.read_at && markRead(n.id)}
                  >
                    <span className="text-2xl shrink-0">{getIcon(n.type)}</span>
                    <div className="flex-1 min-w-0">
                      {n.title && (
                        <p className={cn('text-sm font-semibold', !n.read_at ? 'text-slate-900' : 'text-slate-700')}>
                          {n.title}
                        </p>
                      )}
                      {n.body && (
                        <p className={cn('text-sm', n.title ? 'text-slate-500 mt-0.5' : (!n.read_at ? 'font-semibold text-slate-900' : 'text-slate-600'))}>
                          {n.body}
                        </p>
                      )}
                      <p className="text-xs text-slate-400 mt-1">{relativeTime(n.created_at)}</p>
                    </div>
                    {!n.read_at && (
                      <div className="h-2 w-2 rounded-full bg-primary-600 shrink-0 mt-1.5" />
                    )}
                  </div>
                )

                return actionUrl ? (
                  <Link key={n.id} href={actionUrl} className="block">
                    {content}
                  </Link>
                ) : (
                  <div key={n.id}>{content}</div>
                )
              })}
            </div>

            {notifications.last_page > 1 && (
              <div className="flex items-center justify-center gap-2 mt-8">
                {notifications.current_page > 1 && (
                  <Button variant="outline" size="sm"
                    onClick={() => router.get(notifications.prev_page_url ?? '', {}, { preserveScroll: true })}
                  >
                    ← {t('common', 'previous')}
                  </Button>
                )}
                <span className="text-sm text-slate-500">
                  {notifications.current_page} / {notifications.last_page}
                </span>
                {notifications.current_page < notifications.last_page && (
                  <Button variant="outline" size="sm"
                    onClick={() => router.get(notifications.next_page_url ?? '', {}, { preserveScroll: true })}
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
