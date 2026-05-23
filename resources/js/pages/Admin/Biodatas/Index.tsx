/// <reference path="../../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import { useState } from 'react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { CheckCircle, XCircle } from 'lucide-react'
import { cn } from '@/lib/utils'

interface BiodataRegistration {
  registration_id: string
  name: string
  email: string
  gender: string
  platform_mode: string
}

interface Biodata {
  id: number
  registration_id: string
  status: string
  admin_note: string | null
  updated_at: string
  registration: BiodataRegistration | null
}

interface Paginated {
  data: Biodata[]
  current_page: number
  last_page: number
  total: number
}

interface Counts {
  all: number
  pending: number
  approved: number
  rejected: number
  hidden: number
}

interface Props {
  biodatas: Paginated
  counts: Counts
  tab: string
}

function RejectModal({ biodataId, onClose }: { biodataId: number; onClose: () => void }) {
  const { t } = useTranslation()
  const { data, setData, post, processing, errors } = useForm({ note: '' })

  function submit(e: React.FormEvent) {
    e.preventDefault()
    post(route('admin.biodatas.reject', biodataId), { onSuccess: onClose })
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
      <div className="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 className="font-bold text-slate-900 mb-1">{t('admin', 'biodata_reject')}</h3>
        <p className="text-sm text-slate-500 mb-4">{t('admin', 'rejection_note')}</p>
        <form onSubmit={submit}>
          <textarea
            value={data.note}
            onChange={e => setData('note', e.target.value)}
            rows={3}
            placeholder="e.g. Photo is missing. Please add a recent photo."
            className={cn(
              'w-full rounded-xl border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none',
              errors.note ? 'border-red-400' : 'border-slate-200',
            )}
          />
          {errors.note && <p className="mt-1 text-xs text-red-600">{errors.note}</p>}
          <div className="flex gap-3 mt-4">
            <Button type="button" variant="outline" size="sm" onClick={onClose} className="flex-1">
              {t('common', 'cancel')}
            </Button>
            <Button
              type="submit"
              variant="destructive"
              size="sm"
              isLoading={processing}
              disabled={data.note.trim().length < 5}
              className="flex-1"
            >
              {t('admin', 'biodata_reject')}
            </Button>
          </div>
        </form>
      </div>
    </div>
  )
}

export default function BiodatasIndex({ biodatas, counts, tab }: Props) {
  const { t } = useTranslation()
  const [rejectId, setRejectId] = useState<number | null>(null)

  function switchTab(newTab: string) {
    router.get(route('admin.biodatas.index'), { tab: newTab }, { preserveState: false })
  }

  function approve(id: number) {
    if (!confirm(t('admin', 'biodata_approve') + '?')) return
    router.post(route('admin.biodatas.approve', id))
  }

  const tabs: Array<{ key: string; labelKey: string; count: number }> = [
    { key: 'pending',  labelKey: 'biodata_tab_pending',  count: counts.pending },
    { key: 'approved', labelKey: 'biodata_tab_approved', count: counts.approved },
    { key: 'rejected', labelKey: 'biodata_tab_rejected', count: counts.rejected },
    { key: 'hidden',   labelKey: 'biodata_tab_hidden',   count: counts.hidden },
    { key: 'all',      labelKey: 'biodata_tab_all',      count: counts.all },
  ]

  return (
    <AdminLayout>
      <Head title={t('admin', 'biodatas_title')} />

      {rejectId && (
        <RejectModal biodataId={rejectId} onClose={() => setRejectId(null)} />
      )}

      <div className="space-y-5">
        <h1 className="text-xl font-bold text-slate-900">{t('admin', 'biodatas_title')}</h1>

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

        {/* List */}
        {biodatas.data.length === 0 ? (
          <div className="rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center text-slate-400">
            <CheckCircle size={28} className="mx-auto mb-2 text-emerald-400" />
            {t('admin', 'no_pending')}
          </div>
        ) : (
          <div className="space-y-3">
            {biodatas.data.map(b => (
              <div key={b.id} className="rounded-2xl border border-slate-200 bg-white p-5">
                <div className="flex flex-wrap items-start gap-4 mb-4">
                  <div className="flex-1 min-w-0">
                    <p className="font-semibold text-slate-900">
                      {b.registration?.name ?? b.registration_id}
                    </p>
                    <p className="text-xs text-slate-400">{b.registration?.email}</p>
                    <p className="text-xs text-slate-400">{b.registration?.registration_id}</p>
                  </div>
                  <div className="shrink-0 text-right">
                    <span className={cn(
                      'inline-block rounded-full px-2.5 py-0.5 text-xs font-medium capitalize',
                      b.status === 'approved' ? 'bg-emerald-100 text-emerald-700' :
                      b.status === 'pending'  ? 'bg-amber-100 text-amber-700' :
                      b.status === 'rejected' ? 'bg-red-100 text-red-700' :
                      'bg-slate-100 text-slate-500',
                    )}>
                      {b.status}
                    </span>
                    <p className="text-xs text-slate-400 mt-1 capitalize">
                      {b.registration?.gender} · {b.registration?.platform_mode}
                    </p>
                  </div>
                </div>

                {b.admin_note && (
                  <p className="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2 mb-4">
                    {t('admin', 'biodata_note_label')} {b.admin_note}
                  </p>
                )}

                <p className="text-xs text-slate-400 mb-3">
                  {t('admin', 'biodata_updated_label')} {new Date(b.updated_at).toLocaleDateString('en-BD')}
                </p>

                {(b.status === 'pending' || b.status === 'rejected') && (
                  <div className="flex gap-3">
                    <Button
                      size="sm"
                      variant="default"
                      onClick={() => approve(b.id)}
                      className="flex-1 bg-emerald-600 hover:bg-emerald-700"
                    >
                      <CheckCircle size={14} />
                      {t('admin', 'biodata_approve')}
                    </Button>
                    <Button
                      size="sm"
                      variant="destructive"
                      onClick={() => setRejectId(b.id)}
                      className="flex-1"
                    >
                      <XCircle size={14} />
                      {t('admin', 'biodata_reject')}
                    </Button>
                  </div>
                )}
              </div>
            ))}
          </div>
        )}

        {/* Pagination */}
        {biodatas.last_page > 1 && (
          <div className="flex justify-center gap-2">
            {biodatas.current_page > 1 && (
              <Button
                size="sm"
                variant="outline"
                onClick={() => router.get(route('admin.biodatas.index'), { tab, page: biodatas.current_page - 1 })}
              >
                {t('common', 'previous')}
              </Button>
            )}
            <span className="text-sm text-slate-500 flex items-center px-3">
              {biodatas.current_page} / {biodatas.last_page}
            </span>
            {biodatas.current_page < biodatas.last_page && (
              <Button
                size="sm"
                variant="outline"
                onClick={() => router.get(route('admin.biodatas.index'), { tab, page: biodatas.current_page + 1 })}
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
