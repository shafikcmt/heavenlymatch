/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import { useState } from 'react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { CheckCircle, XCircle, ExternalLink, AlertCircle } from 'lucide-react'
import { cn } from '@/lib/utils'

interface UserInfo {
  registration_id: string
  name: string
  email: string
  phone: string | null
}

interface PendingTxn {
  id: number
  transaction_no: string
  plan_name: string
  amount: number
  gateway_name: string | null
  external_transaction_id: string
  sender_number: string | null
  screenshot_path: string | null
  submitted_at: string
  user: UserInfo | null
}

interface RecentTxn {
  id: number
  transaction_no: string
  plan_name: string
  amount: number
  status: string
  admin_note: string | null
  reviewed_at: string | null
  user_name: string | null
}

interface Stats {
  pending_count: number
  approved_today: number
  rejected_today: number
}

interface PaginatedPending {
  data: PendingTxn[]
  current_page: number
  last_page: number
  total: number
}

interface Props {
  pending: PaginatedPending
  recent: RecentTxn[]
  stats: Stats
}

function RejectModal({
  txnId,
  onClose,
}: {
  txnId: number
  onClose: () => void
}) {
  const { t } = useTranslation()
  const { data, setData, post, processing, errors } = useForm({ admin_note: '' })

  function submit(e: React.FormEvent) {
    e.preventDefault()
    post(route('admin.payments.reject', txnId), {
      onSuccess: onClose,
    })
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
      <div className="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 className="font-bold text-slate-900 mb-1">{t('admin', 'payment_reject')}</h3>
        <p className="text-sm text-slate-500 mb-4">{t('admin', 'rejection_note')}</p>
        <form onSubmit={submit}>
          <textarea
            value={data.admin_note}
            onChange={e => setData('admin_note', e.target.value)}
            rows={3}
            placeholder="e.g. Transaction ID not found in our records."
            className={cn(
              'w-full rounded-xl border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none',
              errors.admin_note ? 'border-red-400' : 'border-slate-200',
            )}
          />
          {errors.admin_note && (
            <p className="mt-1 text-xs text-red-600">{errors.admin_note}</p>
          )}
          <div className="flex gap-3 mt-4">
            <Button
              type="button"
              variant="outline"
              size="sm"
              onClick={onClose}
              className="flex-1"
            >
              {t('common', 'cancel')}
            </Button>
            <Button
              type="submit"
              variant="destructive"
              size="sm"
              isLoading={processing}
              disabled={data.admin_note.trim().length < 5}
              className="flex-1"
            >
              {t('admin', 'payment_reject')}
            </Button>
          </div>
        </form>
      </div>
    </div>
  )
}

export default function Payments({ pending, recent, stats }: Props) {
  const { t } = useTranslation()
  const [rejectModalId, setRejectModalId] = useState<number | null>(null)

  function approve(id: number) {
    if (!confirm(t('admin', 'approve_confirm'))) return
    router.post(route('admin.payments.approve', id))
  }

  return (
    <AdminLayout>
      <Head title={t('admin', 'payments_title')} />

      {rejectModalId && (
        <RejectModal txnId={rejectModalId} onClose={() => setRejectModalId(null)} />
      )}

      <div className="space-y-6">
        {/* Page header */}
        <div>
          <h1 className="text-xl font-bold text-slate-900">{t('admin', 'payments_title')}</h1>
        </div>

        {/* Stats */}
        <div className="grid grid-cols-3 gap-4">
          <StatCard
            label={t('admin', 'pending_review')}
            value={stats.pending_count}
            color="amber"
          />
          <StatCard
            label={t('admin', 'approved_today')}
            value={stats.approved_today}
            color="green"
          />
          <StatCard
            label={t('admin', 'rejected_today')}
            value={stats.rejected_today}
            color="red"
          />
        </div>

        {/* Pending queue */}
        <section>
          <h2 className="text-base font-semibold text-slate-900 mb-3">
            {t('admin', 'awaiting_review')}
            {stats.pending_count > 0 && (
              <span className="ml-2 inline-flex items-center justify-center h-5 w-5 rounded-full bg-amber-500 text-white text-xs font-bold">
                {stats.pending_count}
              </span>
            )}
          </h2>

          {pending.data.length === 0 ? (
            <div className="rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center text-slate-400">
              <CheckCircle size={28} className="mx-auto mb-2 text-emerald-400" />
              {t('admin', 'no_pending_payments')}
            </div>
          ) : (
            <div className="space-y-3">
              {pending.data.map(txn => (
                <div
                  key={txn.id}
                  className="rounded-2xl border border-slate-200 bg-white p-5"
                >
                  <div className="flex flex-wrap items-start gap-4 mb-4">
                    {/* User info */}
                    <div className="flex-1 min-w-0">
                      <p className="font-semibold text-slate-900 truncate">
                        {txn.user?.name ?? 'Unknown User'}
                      </p>
                      <p className="text-xs text-slate-400">{txn.user?.email}</p>
                      <p className="text-xs text-slate-400">{txn.user?.registration_id}</p>
                    </div>

                    {/* Amount badge */}
                    <div className="text-right shrink-0">
                      <p className="text-xl font-extrabold text-slate-900">
                        ৳{txn.amount.toLocaleString('en-BD')}
                      </p>
                      <p className="text-xs text-slate-500">{txn.plan_name}</p>
                    </div>
                  </div>

                  {/* Payment details grid */}
                  <div className="grid grid-cols-2 gap-3 mb-4 text-sm">
                    <Detail label={t('admin', 'via_method')} value={txn.gateway_name ?? '—'} />
                    <Detail label={t('admin', 'sender_no_label')} value={txn.sender_number ?? '—'} />
                    <Detail
                      label={t('admin', 'transaction_id')}
                      value={txn.external_transaction_id}
                      mono
                    />
                    <Detail
                      label={t('admin', 'submitted_at')}
                      value={new Date(txn.submitted_at).toLocaleDateString('bn-BD')}
                    />
                  </div>

                  {/* Screenshot */}
                  {txn.screenshot_path && (
                    <a
                      href={txn.screenshot_path}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="inline-flex items-center gap-1.5 text-xs text-primary-600 hover:underline mb-4"
                    >
                      <ExternalLink size={12} />
                      {t('admin', 'view_screenshot')}
                    </a>
                  )}

                  {/* Action buttons */}
                  <div className="flex gap-3">
                    <Button
                      size="sm"
                      variant="default"
                      onClick={() => approve(txn.id)}
                      className="flex-1 bg-emerald-600 hover:bg-emerald-700"
                    >
                      <CheckCircle size={14} />
                      {t('admin', 'payment_approve')}
                    </Button>
                    <Button
                      size="sm"
                      variant="destructive"
                      onClick={() => setRejectModalId(txn.id)}
                      className="flex-1"
                    >
                      <XCircle size={14} />
                      {t('admin', 'payment_reject')}
                    </Button>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* Pagination */}
          {pending.last_page > 1 && (
            <div className="flex justify-center gap-2 mt-4">
              {pending.current_page > 1 && (
                <Button
                  size="sm"
                  variant="outline"
                  onClick={() => router.get(route('admin.payments.index'), { page: pending.current_page - 1 })}
                >
                  {t('common', 'previous')}
                </Button>
              )}
              <span className="text-sm text-slate-500 flex items-center px-3">
                {pending.current_page} / {pending.last_page}
              </span>
              {pending.current_page < pending.last_page && (
                <Button
                  size="sm"
                  variant="outline"
                  onClick={() => router.get(route('admin.payments.index'), { page: pending.current_page + 1 })}
                >
                  {t('common', 'next')}
                </Button>
              )}
            </div>
          )}
        </section>

        {/* Recent decisions */}
        {recent.length > 0 && (
          <section>
            <h2 className="text-base font-semibold text-slate-900 mb-3">{t('admin', 'recent_decisions')}</h2>
            <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-slate-100 bg-slate-50">
                    <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_user')}</th>
                    <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_plan')}</th>
                    <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_amount')}</th>
                    <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_decision')}</th>
                    <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_date')}</th>
                  </tr>
                </thead>
                <tbody>
                  {recent.map(txn => (
                    <tr key={txn.id} className="border-b border-slate-50 last:border-0 hover:bg-slate-50">
                      <td className="px-4 py-3 font-medium text-slate-800">{txn.user_name ?? '—'}</td>
                      <td className="px-4 py-3 text-slate-600">{txn.plan_name}</td>
                      <td className="px-4 py-3 font-medium">৳{txn.amount.toLocaleString('en-BD')}</td>
                      <td className="px-4 py-3">
                        <span className={cn(
                          'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium',
                          txn.status === 'paid'
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-red-100 text-red-700',
                        )}>
                          {txn.status === 'paid'
                            ? <CheckCircle size={10} />
                            : <XCircle size={10} />}
                          {txn.status === 'paid' ? t('admin', 'decision_approved') : t('admin', 'decision_rejected')}
                        </span>
                      </td>
                      <td className="px-4 py-3 text-slate-400 text-xs">
                        {txn.reviewed_at
                          ? new Date(txn.reviewed_at).toLocaleDateString('en-BD')
                          : '—'}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </section>
        )}
      </div>
    </AdminLayout>
  )
}

function StatCard({ label, value, color }: { label: string; value: number; color: 'amber' | 'green' | 'red' }) {
  const bg = { amber: 'bg-amber-50', green: 'bg-emerald-50', red: 'bg-red-50' }
  const text = { amber: 'text-amber-700', green: 'text-emerald-700', red: 'text-red-700' }
  return (
    <div className={cn('rounded-2xl border p-4', bg[color])}>
      <p className={cn('text-2xl font-extrabold', text[color])}>{value}</p>
      <p className="text-xs text-slate-500 mt-1">{label}</p>
    </div>
  )
}

function Detail({ label, value, mono = false }: { label: string; value: string; mono?: boolean }) {
  return (
    <div className="rounded-lg bg-slate-50 px-3 py-2">
      <p className="text-xs text-slate-400 mb-0.5">{label}</p>
      <p className={cn('text-sm font-medium text-slate-800 truncate', mono && 'font-mono')}>{value}</p>
    </div>
  )
}
