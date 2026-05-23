/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import { useState } from 'react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { CheckCircle } from 'lucide-react'
import { cn } from '@/lib/utils'

interface ReportUser {
  registration_id: string
  name: string
  email: string
}

interface Report {
  id: number
  reporter_id: string
  reported_id: string
  reason: string
  details: string | null
  status: string
  resolution_note: string | null
  resolved_at: string | null
  created_at: string
  reporter: ReportUser | null
  reported: ReportUser | null
}

interface Paginated {
  data: Report[]
  current_page: number
  last_page: number
  total: number
}

interface Counts {
  open: number
  resolved: number
  dismissed: number
}

interface Props {
  reports: Paginated
  counts: Counts
  tab: string
}

function ActionModal({
  title,
  actionLabel,
  actionVariant,
  routeName,
  onClose,
}: {
  title: string
  actionLabel: string
  actionVariant: 'default' | 'destructive'
  routeName: string
  onClose: () => void
}) {
  const { t } = useTranslation()
  const { data, setData, post, processing } = useForm({ note: '' })

  function submit(e: React.FormEvent) {
    e.preventDefault()
    post(routeName, { onSuccess: onClose })
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
      <div className="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 className="font-bold text-slate-900 mb-4">{title}</h3>
        <form onSubmit={submit}>
          <textarea
            value={data.note}
            onChange={e => setData('note', e.target.value)}
            rows={3}
            placeholder={t('admin', 'resolution_note')}
            className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none mb-4"
          />
          <div className="flex gap-3">
            <Button type="button" variant="outline" size="sm" onClick={onClose} className="flex-1">
              {t('common', 'cancel')}
            </Button>
            <Button type="submit" variant={actionVariant} size="sm" isLoading={processing} className="flex-1">
              {actionLabel}
            </Button>
          </div>
        </form>
      </div>
    </div>
  )
}

export default function Reports({ reports, counts, tab }: Props) {
  const { t } = useTranslation()
  const [actionModal, setActionModal] = useState<{ id: number; type: 'resolve' | 'dismiss' } | null>(null)

  function switchTab(newTab: string) {
    router.get(route('admin.reports.index'), { tab: newTab }, { preserveState: false })
  }

  const tabs: Array<{ key: string; labelKey: string; count: number }> = [
    { key: 'open',      labelKey: 'report_tab_open',      count: counts.open },
    { key: 'resolved',  labelKey: 'report_tab_resolved',  count: counts.resolved },
    { key: 'dismissed', labelKey: 'report_tab_dismissed', count: counts.dismissed },
  ]

  const REASON_LABELS: Record<string, string> = {
    fake_profile:          t('admin', 'reason_fake_profile'),
    harassment:            t('admin', 'reason_harassment'),
    inappropriate_photos:  t('admin', 'reason_inappropriate'),
    spam:                  t('admin', 'reason_spam'),
    scam:                  t('admin', 'reason_scam'),
    underage:              t('admin', 'reason_underage'),
    other:                 t('admin', 'reason_other'),
  }

  return (
    <AdminLayout>
      <Head title={t('admin', 'reports_title')} />

      {actionModal && (
        <ActionModal
          key={actionModal.id}
          title={actionModal.type === 'resolve' ? t('admin', 'report_resolve') : t('admin', 'report_dismiss')}
          actionLabel={actionModal.type === 'resolve' ? t('admin', 'report_resolve') : t('admin', 'report_dismiss')}
          actionVariant={actionModal.type === 'resolve' ? 'default' : 'destructive'}
          routeName={
            actionModal.type === 'resolve'
              ? route('admin.reports.resolve', actionModal.id)
              : route('admin.reports.dismiss', actionModal.id)
          }
          onClose={() => setActionModal(null)}
        />
      )}

      <div className="space-y-5">
        <h1 className="text-xl font-bold text-slate-900">{t('admin', 'reports_title')}</h1>

        {/* Tabs */}
        <div className="flex gap-1 flex-wrap">
          {tabs.map(({ key, labelKey, count }) => (
            <button
              key={key}
              onClick={() => switchTab(key)}
              className={cn(
                'px-3 py-1.5 rounded-xl text-sm font-medium transition-colors',
                tab === key
                  ? 'bg-primary-600 text-white'
                  : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
              )}
            >
              {t('admin', labelKey)}
              <span className={cn(
                'ml-1.5 text-xs rounded-full px-1.5 py-0.5',
                tab === key ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600',
              )}>
                {count}
              </span>
            </button>
          ))}
        </div>

        {/* Reports list */}
        {reports.data.length === 0 ? (
          <div className="rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center text-slate-400">
            <CheckCircle size={28} className="mx-auto mb-2 text-emerald-400" />
            {t('admin', 'no_open_reports')}
          </div>
        ) : (
          <div className="space-y-3">
            {reports.data.map(r => (
              <div key={r.id} className="rounded-2xl border border-slate-200 bg-white p-5">
                <div className="flex flex-wrap items-start gap-4 mb-3">
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2 mb-1">
                      <span className="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        {REASON_LABELS[r.reason] ?? r.reason}
                      </span>
                      <ReportStatusBadge status={r.status} />
                    </div>
                    <div className="grid grid-cols-2 gap-2 text-xs text-slate-500">
                      <span>
                        <span className="text-slate-400">{t('admin', 'reporter')}: </span>
                        <button
                          onClick={() => router.get(route('admin.users.show', r.reporter_id))}
                          className="font-medium text-primary-600 hover:underline"
                        >
                          {r.reporter?.name ?? r.reporter_id}
                        </button>
                      </span>
                      <span>
                        <span className="text-slate-400">{t('admin', 'reported_user')}: </span>
                        <button
                          onClick={() => router.get(route('admin.users.show', r.reported_id))}
                          className="font-medium text-primary-600 hover:underline"
                        >
                          {r.reported?.name ?? r.reported_id}
                        </button>
                      </span>
                    </div>
                  </div>
                  <span className="text-xs text-slate-400 shrink-0">
                    {new Date(r.created_at).toLocaleDateString('en-BD')}
                  </span>
                </div>

                {r.details && (
                  <p className="text-sm text-slate-600 bg-slate-50 rounded-xl px-3 py-2 mb-3 line-clamp-3">
                    {r.details}
                  </p>
                )}

                {r.resolution_note && (
                  <p className="text-xs text-slate-500 italic mb-3">
                    {t('admin', 'resolution_label')} {r.resolution_note}
                  </p>
                )}

                {r.status === 'open' && (
                  <div className="flex gap-3">
                    <Button
                      size="sm"
                      variant="default"
                      onClick={() => setActionModal({ id: r.id, type: 'resolve' })}
                      className="flex-1 bg-emerald-600 hover:bg-emerald-700"
                    >
                      {t('admin', 'report_resolve')}
                    </Button>
                    <Button
                      size="sm"
                      variant="outline"
                      onClick={() => setActionModal({ id: r.id, type: 'dismiss' })}
                      className="flex-1"
                    >
                      {t('admin', 'report_dismiss')}
                    </Button>
                  </div>
                )}
              </div>
            ))}
          </div>
        )}

        {/* Pagination */}
        {reports.last_page > 1 && (
          <div className="flex justify-center gap-2">
            {reports.current_page > 1 && (
              <Button
                size="sm"
                variant="outline"
                onClick={() => router.get(route('admin.reports.index'), { tab, page: reports.current_page - 1 })}
              >
                {t('common', 'previous')}
              </Button>
            )}
            <span className="text-sm text-slate-500 flex items-center px-3">
              {reports.current_page} / {reports.last_page}
            </span>
            {reports.current_page < reports.last_page && (
              <Button
                size="sm"
                variant="outline"
                onClick={() => router.get(route('admin.reports.index'), { tab, page: reports.current_page + 1 })}
              >
                {t('common', 'next')}
              </Button>
            )}
          </div>
        )}
      </div>
    </AdminLayout>
  )
}

function ReportStatusBadge({ status }: { status: string }) {
  const { t } = useTranslation()
  const colorMap: Record<string, string> = {
    open:       'bg-amber-100 text-amber-700',
    reviewing:  'bg-amber-100 text-amber-700',
    resolved:   'bg-emerald-100 text-emerald-700',
    dismissed:  'bg-slate-100 text-slate-500',
  }
  const labelKey = `report_status_${status}` as const
  return (
    <span className={cn('inline-block rounded-full px-2 py-0.5 text-xs font-medium', colorMap[status] ?? 'bg-slate-100 text-slate-500')}>
      {t('admin', labelKey) || status}
    </span>
  )
}
