/// <reference path="../../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import { useState } from 'react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { ArrowLeft, ShieldCheck, ShieldX, UserX, UserCheck, AlertTriangle } from 'lucide-react'
import { cn } from '@/lib/utils'

interface Biodata {
  id: number
  status: string
  updated_at: string
}

interface Payment {
  id: number
  transaction_no: string
  plan_name: string
  amount: number
  status: string
  created_at: string
  external_transaction_id: string | null
}

interface User {
  registration_id: string
  name: string
  email: string
  mobile_number: string | null
  gender: 'male' | 'female'
  account_status: string
  membership_status: string
  membership_plan_name: string | null
  membership_expires_at: string | null
  identity_verification_status: string | null
  role: string
  is_admin: boolean
  created_at: string
  last_login_at: string | null
  blocked_at: string | null
  blocked_reason: string | null
  biodata: Biodata | null
}

interface Props {
  user: User
  payments: Payment[]
}

function ActionModal({
  title,
  description,
  placeholder,
  confirmLabel,
  confirmVariant,
  onConfirm,
  onClose,
}: {
  title: string
  description: string
  placeholder?: string
  confirmLabel: string
  confirmVariant?: 'destructive' | 'default'
  onConfirm: (reason: string) => void
  onClose: () => void
}) {
  const [reason, setReason] = useState('')
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
      <div className="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 className="font-bold text-slate-900 mb-1">{title}</h3>
        <p className="text-sm text-slate-500 mb-4">{description}</p>
        {placeholder !== undefined && (
          <textarea
            value={reason}
            onChange={e => setReason(e.target.value)}
            rows={3}
            placeholder={placeholder}
            className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none mb-4"
          />
        )}
        <div className="flex gap-3">
          <Button type="button" variant="outline" size="sm" onClick={onClose} className="flex-1">
            Cancel
          </Button>
          <Button
            type="button"
            variant={confirmVariant ?? 'default'}
            size="sm"
            onClick={() => onConfirm(reason)}
            className="flex-1"
          >
            {confirmLabel}
          </Button>
        </div>
      </div>
    </div>
  )
}

export default function UserShow({ user, payments }: Props) {
  const { t } = useTranslation()
  const [modal, setModal] = useState<'ban' | 'suspend' | null>(null)

  function post(routeName: string, params: Record<string, string> = {}) {
    router.post(routeName, params)
  }

  return (
    <AdminLayout>
      <Head title={user.name} />

      {modal === 'ban' && (
        <ActionModal
          title={t('admin', 'user_ban')}
          description={t('admin', 'user_ban_reason')}
          placeholder="e.g. Spam / fake profile / abuse"
          confirmLabel={t('admin', 'user_ban')}
          confirmVariant="destructive"
          onConfirm={reason => { post(route('admin.users.ban', user.registration_id), { reason }); setModal(null) }}
          onClose={() => setModal(null)}
        />
      )}

      {modal === 'suspend' && (
        <ActionModal
          title={t('admin', 'user_suspend')}
          description="Provide a reason for suspension."
          placeholder="e.g. Policy violation"
          confirmLabel={t('admin', 'user_suspend')}
          confirmVariant="destructive"
          onConfirm={reason => { post(route('admin.users.suspend', user.registration_id), { reason }); setModal(null) }}
          onClose={() => setModal(null)}
        />
      )}

      <div className="max-w-2xl space-y-5">
        {/* Back */}
        <button
          onClick={() => router.get(route('admin.users.index'))}
          className="flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800"
        >
          <ArrowLeft size={14} />
          {t('admin', 'users_title')}
        </button>

        {/* Profile card */}
        <div className="rounded-2xl border border-slate-200 bg-white p-5">
          <div className="flex items-start justify-between gap-4 mb-4">
            <div>
              <h1 className="text-xl font-bold text-slate-900">{user.name}</h1>
              <p className="text-sm text-slate-500">{user.email}</p>
              {user.mobile_number && <p className="text-sm text-slate-500">{user.mobile_number}</p>}
            </div>
            <div className="text-right shrink-0">
              <StatusBadge status={user.account_status} />
              <p className="text-xs text-slate-400 mt-1 font-mono">{user.registration_id}</p>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-3 text-sm mb-4">
            <InfoRow label="Gender" value={user.gender} />
            <InfoRow label="Role" value={user.role ?? 'user'} />
            <InfoRow label="Membership" value={user.membership_status} />
            {user.membership_plan_name && <InfoRow label="Plan" value={user.membership_plan_name} />}
            {user.membership_expires_at && (
              <InfoRow label="Expires" value={new Date(user.membership_expires_at).toLocaleDateString('en-BD')} />
            )}
            <InfoRow label="Identity" value={user.identity_verification_status ?? 'unverified'} />
            <InfoRow label="Joined" value={new Date(user.created_at).toLocaleDateString('en-BD')} />
            {user.last_login_at && (
              <InfoRow label="Last Login" value={new Date(user.last_login_at).toLocaleDateString('en-BD')} />
            )}
            {user.blocked_reason && <InfoRow label="Ban Reason" value={user.blocked_reason} />}
          </div>

          {/* Biodata status */}
          {user.biodata && (
            <div className="mb-4 p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
              <span className="text-sm text-slate-600">Biodata status:</span>
              <span className={cn(
                'text-xs font-medium rounded-full px-2.5 py-0.5',
                user.biodata.status === 'approved' ? 'bg-emerald-100 text-emerald-700' :
                user.biodata.status === 'pending'  ? 'bg-amber-100 text-amber-700' :
                'bg-red-100 text-red-700',
              )}>
                {user.biodata.status}
              </span>
            </div>
          )}

          {/* Action buttons */}
          <div className="flex flex-wrap gap-2">
            {user.account_status !== 'active' && (
              <Button
                size="sm"
                variant="default"
                onClick={() => post(route('admin.users.activate', user.registration_id))}
                className="bg-emerald-600 hover:bg-emerald-700"
              >
                <UserCheck size={14} />
                {t('admin', 'user_activate')}
              </Button>
            )}
            {user.account_status === 'active' && (
              <Button
                size="sm"
                variant="outline"
                onClick={() => setModal('suspend')}
              >
                <AlertTriangle size={14} />
                {t('admin', 'user_suspend')}
              </Button>
            )}
            {user.account_status !== 'banned' ? (
              <Button
                size="sm"
                variant="destructive"
                onClick={() => setModal('ban')}
              >
                <UserX size={14} />
                {t('admin', 'user_ban')}
              </Button>
            ) : (
              <Button
                size="sm"
                variant="outline"
                onClick={() => post(route('admin.users.unban', user.registration_id))}
              >
                <UserCheck size={14} />
                {t('admin', 'user_unban')}
              </Button>
            )}
            {user.identity_verification_status !== 'verified' && (
              <Button
                size="sm"
                variant="outline"
                onClick={() => post(route('admin.users.verify', user.registration_id))}
              >
                <ShieldCheck size={14} />
                {t('admin', 'user_verify_identity')}
              </Button>
            )}
          </div>
        </div>

        {/* Payment history */}
        <section>
          <h2 className="text-base font-semibold text-slate-900 mb-3">{t('admin', 'payment_history')}</h2>
          {payments.length === 0 ? (
            <p className="text-sm text-slate-400 text-center py-6 rounded-2xl border border-slate-200 bg-white">
              {t('admin', 'no_payments')}
            </p>
          ) : (
            <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-slate-100 bg-slate-50">
                    <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">Ref</th>
                    <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">Plan</th>
                    <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">Amount</th>
                    <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">Status</th>
                    <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">Date</th>
                  </tr>
                </thead>
                <tbody>
                  {payments.map(p => (
                    <tr key={p.id} className="border-b border-slate-50 last:border-0">
                      <td className="px-4 py-3 font-mono text-xs text-slate-500">{p.transaction_no}</td>
                      <td className="px-4 py-3 text-slate-700">{p.plan_name}</td>
                      <td className="px-4 py-3 font-medium">৳{p.amount.toLocaleString('en-BD')}</td>
                      <td className="px-4 py-3">
                        <PaymentStatusBadge status={p.status} />
                      </td>
                      <td className="px-4 py-3 text-xs text-slate-400">
                        {new Date(p.created_at).toLocaleDateString('en-BD')}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </section>
      </div>
    </AdminLayout>
  )
}

function InfoRow({ label, value }: { label: string; value: string }) {
  return (
    <div className="rounded-lg bg-slate-50 px-3 py-2">
      <p className="text-xs text-slate-400 mb-0.5">{label}</p>
      <p className="text-sm font-medium text-slate-800 capitalize">{value}</p>
    </div>
  )
}

function StatusBadge({ status }: { status: string }) {
  const map: Record<string, string> = {
    active:    'bg-emerald-100 text-emerald-700',
    inactive:  'bg-slate-100 text-slate-600',
    suspended: 'bg-amber-100 text-amber-700',
    banned:    'bg-red-100 text-red-700',
  }
  return (
    <span className={cn('inline-block rounded-full px-2.5 py-0.5 text-xs font-medium capitalize', map[status] ?? 'bg-slate-100 text-slate-600')}>
      {status}
    </span>
  )
}

function PaymentStatusBadge({ status }: { status: string }) {
  const map: Record<string, string> = {
    paid:      'bg-emerald-100 text-emerald-700',
    pending:   'bg-amber-100 text-amber-700',
    failed:    'bg-red-100 text-red-700',
    cancelled: 'bg-slate-100 text-slate-500',
  }
  return (
    <span className={cn('inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize', map[status] ?? 'bg-slate-100 text-slate-500')}>
      {status === 'paid' ? 'Approved' : status}
    </span>
  )
}
