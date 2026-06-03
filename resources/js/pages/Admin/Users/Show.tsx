/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link, router } from '@inertiajs/react'
import { useState } from 'react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { cn } from '@/lib/utils'
import {
  ArrowLeft, Pencil, Pause, Play, KeyRound, Trash2, ShieldCheck, Mail, Phone,
  CheckCircle2, XCircle, ExternalLink,
} from 'lucide-react'
import ConfirmDialog from '@/components/admin/ConfirmDialog'
import ResetPasswordModal from '@/components/admin/ResetPasswordModal'

interface Biodata { id: number; status: string; is_completed: boolean }
interface User {
  id: number; registration_id: string; name: string; email: string; mobile_number: string | null
  gender: string; profile_created_for: string; account_status: string
  membership_status: string; membership_plan_name: string | null
  is_email_verified: boolean; is_mobile_verified: boolean; is_admin: boolean; role: string | null
  identity_verification_status: string | null
  last_login_at: string | null; created_at: string; deleted_at: string | null
  biodata: Biodata | null
}
interface Payment { id: number; transaction_no: string; plan_name: string; amount: number; status: string; created_at: string }
interface Props {
  user: User
  payments: Payment[]
  stats: { interests_sent: number; interests_received: number }
  authRegistrationId: string
}

export default function UserShow({ user, payments, stats, authRegistrationId }: Props) {
  const { t } = useTranslation()
  const [confirmDelete, setConfirmDelete] = useState(false)
  const [resetOpen, setResetOpen] = useState(false)

  const isSelf = user.registration_id === authRegistrationId
  const locked = user.is_admin || isSelf
  const suspended = user.account_status === 'suspended'

  const post = (name: string) => router.post(route(name, user.registration_id), {}, { preserveScroll: true })

  return (
    <AdminLayout>
      <Head title={`${user.name} — ${t('admin', 'details_title')}`} />

      <div className="max-w-4xl space-y-5">
        <Link href={route('admin.users.index')} className="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">
          <ArrowLeft size={15} /> {t('admin', 'back_to_users')}
        </Link>

        {/* Header card */}
        <div className="rounded-2xl border border-slate-200 bg-white p-6">
          <div className="flex flex-wrap items-start justify-between gap-4">
            <div className="flex items-center gap-4">
              <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-100 text-primary-700 text-xl font-bold">
                {user.name.charAt(0).toUpperCase()}
              </div>
              <div>
                <div className="flex items-center gap-2">
                  <h1 className="text-xl font-bold text-slate-900">{user.name}</h1>
                  {user.is_admin && <span className="rounded bg-violet-100 px-2 py-0.5 text-[10px] font-semibold text-violet-700">ADMIN</span>}
                </div>
                <p className="text-sm text-slate-500">{user.email}</p>
                <p className="font-mono text-xs text-slate-400">{user.registration_id}</p>
              </div>
            </div>
            <StatusBadge status={user.account_status} />
          </div>

          {/* Action bar */}
          <div className="mt-5 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
            <Link href={route('admin.users.edit', user.registration_id)}>
              <Button size="sm" variant="outline"><Pencil size={14} /> {t('admin', 'action_edit')}</Button>
            </Link>
            {suspended
              ? <Button size="sm" variant="outline" onClick={() => post('admin.users.activate')}><Play size={14} /> {t('admin', 'action_activate')}</Button>
              : !locked && <Button size="sm" variant="outline" onClick={() => post('admin.users.suspend')}><Pause size={14} /> {t('admin', 'action_suspend')}</Button>}
            <Button size="sm" variant="outline" onClick={() => setResetOpen(true)}><KeyRound size={14} /> {t('admin', 'action_reset_password')}</Button>
            {!locked && <Button size="sm" variant="destructive" onClick={() => setConfirmDelete(true)}><Trash2 size={14} /> {t('admin', 'action_delete')}</Button>}
          </div>
        </div>

        <div className="grid gap-5 md:grid-cols-3">
          {/* Basic info */}
          <div className="md:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 className="mb-4 text-sm font-semibold text-slate-900">{t('admin', 'basic_info')}</h2>
            <dl className="grid gap-y-3 gap-x-6 sm:grid-cols-2 text-sm">
              <Row label={t('admin', 'field_email')}>
                <span className="flex items-center gap-2"><Mail size={14} className="text-slate-400" />{user.email}
                  <VerifyDot on={user.is_email_verified} /></span>
              </Row>
              <Row label={t('admin', 'field_phone')}>
                <span className="flex items-center gap-2"><Phone size={14} className="text-slate-400" />{user.mobile_number || '—'}
                  {user.mobile_number && <VerifyDot on={user.is_mobile_verified} />}</span>
              </Row>
              <Row label={t('admin', 'field_gender')}><span className="capitalize">{user.gender}</span></Row>
              <Row label={t('admin', 'field_profile_for')}><span className="capitalize">{user.profile_created_for}</span></Row>
              <Row label={t('admin', 'col_membership')}>
                {user.membership_status === 'active'
                  ? <span className="font-medium text-violet-700">{user.membership_plan_name || 'Premium'}</span>
                  : <span className="text-slate-500">{t('admin', 'membership_free')}</span>}
              </Row>
              <Row label="Identity">
                <span className="flex items-center gap-1 capitalize">
                  <ShieldCheck size={14} className={user.identity_verification_status === 'verified' ? 'text-emerald-500' : 'text-slate-300'} />
                  {user.identity_verification_status ?? 'unverified'}
                </span>
              </Row>
              <Row label={t('admin', 'joined')}>{new Date(user.created_at).toLocaleString('en-BD')}</Row>
              <Row label={t('admin', 'last_login')}>{user.last_login_at ? new Date(user.last_login_at).toLocaleString('en-BD') : t('admin', 'never')}</Row>
            </dl>
          </div>

          {/* Side: stats + biodata */}
          <div className="space-y-5">
            <div className="rounded-2xl border border-slate-200 bg-white p-6">
              <h2 className="mb-3 text-sm font-semibold text-slate-900">Activity</h2>
              <div className="space-y-2 text-sm">
                <div className="flex justify-between"><span className="text-slate-500">{t('admin', 'interests_sent')}</span><b>{stats.interests_sent}</b></div>
                <div className="flex justify-between"><span className="text-slate-500">{t('admin', 'interests_received')}</span><b>{stats.interests_received}</b></div>
              </div>
            </div>

            <div className="rounded-2xl border border-slate-200 bg-white p-6">
              <h2 className="mb-3 text-sm font-semibold text-slate-900">{t('admin', 'col_biodata')}</h2>
              {user.biodata ? (
                <>
                  <BioBadge status={user.biodata.status} />
                  <div className="mt-3 flex flex-col gap-2">
                    <Link href={route('admin.biodatas.show', user.biodata.id)} className="inline-flex items-center gap-1 text-sm text-primary-600 hover:underline">
                      <ExternalLink size={13} /> {t('admin', 'view_biodata')}
                    </Link>
                    {user.biodata.status === 'pending' && (
                      <div className="flex gap-2">
                        <Button size="sm" variant="islamic" onClick={() => router.post(route('admin.biodatas.approve', user.biodata!.id), {}, { preserveScroll: true })}>
                          <CheckCircle2 size={14} /> {t('common', 'approve') || 'Approve'}
                        </Button>
                        <Button size="sm" variant="destructive" onClick={() => router.post(route('admin.biodatas.reject', user.biodata!.id), {}, { preserveScroll: true })}>
                          <XCircle size={14} /> {t('common', 'reject') || 'Reject'}
                        </Button>
                      </div>
                    )}
                  </div>
                </>
              ) : (
                <p className="text-sm text-slate-400">{t('admin', 'no_biodata')}</p>
              )}
            </div>
          </div>
        </div>

        {/* Payments */}
        <div className="rounded-2xl border border-slate-200 bg-white p-6">
          <h2 className="mb-3 text-sm font-semibold text-slate-900">{t('admin', 'payment_history')}</h2>
          {payments.length === 0 ? (
            <p className="text-sm text-slate-400">{t('admin', 'no_payments')}</p>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-sm min-w-[520px]">
                <thead>
                  <tr className="border-b border-slate-100 text-left text-xs font-semibold text-slate-500">
                    <th className="py-2 pr-4">#</th><th className="py-2 pr-4">{t('admin', 'field_plan')}</th>
                    <th className="py-2 pr-4">Amount</th><th className="py-2 pr-4">{t('admin', 'col_status')}</th><th className="py-2">{t('admin', 'joined')}</th>
                  </tr>
                </thead>
                <tbody>
                  {payments.map(p => (
                    <tr key={p.id} className="border-b border-slate-50 last:border-0">
                      <td className="py-2 pr-4 font-mono text-xs text-slate-500">{p.transaction_no}</td>
                      <td className="py-2 pr-4">{p.plan_name}</td>
                      <td className="py-2 pr-4">৳{p.amount}</td>
                      <td className="py-2 pr-4 capitalize">{p.status}</td>
                      <td className="py-2 text-xs text-slate-400">{new Date(p.created_at).toLocaleDateString('en-BD')}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>

      <ConfirmDialog
        open={confirmDelete} title={t('admin', 'confirm_delete_title')} body={t('admin', 'confirm_delete_body')}
        confirmLabel={t('admin', 'action_delete')} cancelLabel={t('common', 'cancel') || 'Cancel'}
        onConfirm={() => router.delete(route('admin.users.destroy', user.registration_id))}
        onClose={() => setConfirmDelete(false)}
      />
      <ResetPasswordModal open={resetOpen} registrationId={user.registration_id} userName={user.name} onClose={() => setResetOpen(false)} />
    </AdminLayout>
  )
}

function Row({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <div>
      <dt className="text-xs text-slate-400">{label}</dt>
      <dd className="mt-0.5 text-slate-800">{children}</dd>
    </div>
  )
}

function VerifyDot({ on }: { on: boolean }) {
  return (
    <span className={cn('inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[10px] font-medium', on ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400')}>
      {on ? <CheckCircle2 size={10} /> : <XCircle size={10} />}
    </span>
  )
}

function StatusBadge({ status }: { status: string }) {
  const map: Record<string, string> = {
    active: 'bg-emerald-100 text-emerald-700', inactive: 'bg-slate-100 text-slate-600',
    suspended: 'bg-amber-100 text-amber-700', banned: 'bg-red-100 text-red-700',
  }
  return <span className={cn('inline-block rounded-full px-3 py-1 text-xs font-semibold capitalize', map[status] ?? 'bg-slate-100 text-slate-600')}>{status}</span>
}

function BioBadge({ status }: { status: string }) {
  const map: Record<string, string> = {
    approved: 'bg-emerald-100 text-emerald-700', pending: 'bg-amber-100 text-amber-700',
    rejected: 'bg-red-100 text-red-700', draft: 'bg-slate-100 text-slate-500', hidden: 'bg-slate-100 text-slate-500',
  }
  return <span className={cn('inline-block rounded-full px-2.5 py-1 text-xs font-medium capitalize', map[status] ?? 'bg-slate-100 text-slate-500')}>{status}</span>
}
