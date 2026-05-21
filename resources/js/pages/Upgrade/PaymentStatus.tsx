/// <reference path="../../types/ziggy.d.ts" />
import { Head, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { CheckCircle, Clock, XCircle, Star } from 'lucide-react'
import { cn } from '@/lib/utils'

interface Transaction {
  transaction_no: string
  plan_name: string
  amount: number
  status: 'pending' | 'paid' | 'failed' | 'cancelled' | 'refunded'
  is_submitted: boolean
  admin_note: string | null
  created_at: string
}

interface Props {
  membershipStatus: string
  membershipPlan: string | null
  membershipExpires: string | null
  latestTransaction: Transaction | null
}

export default function PaymentStatus({
  membershipStatus, membershipPlan, membershipExpires, latestTransaction,
}: Props) {
  const { t } = useTranslation()

  const isActive   = membershipStatus === 'active'
  const isPending  = latestTransaction?.status === 'pending'
  const isRejected = latestTransaction?.status === 'failed'

  return (
    <AppLayout>
      <Head title="Payment Status" />

      <div className="max-w-md mx-auto pt-6">
        {/* Active membership */}
        {isActive && (
          <div className="text-center mb-8">
            <div className="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-emerald-100 mb-4">
              <CheckCircle size={32} className="text-emerald-600" />
            </div>
            <h1 className="text-xl font-bold text-slate-900 mb-1">
              {t('notifications', 'membership_activated_title', { plan: membershipPlan ?? '' })}
            </h1>
            <p className="text-sm text-slate-500">
              {membershipExpires && t('dashboard', 'plan_expires', { date: membershipExpires })}
            </p>
          </div>
        )}

        {/* Pending review */}
        {!isActive && isPending && latestTransaction?.is_submitted && (
          <div className="text-center mb-8">
            <div className="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-amber-100 mb-4">
              <Clock size={32} className="text-amber-600" />
            </div>
            <h1 className="text-xl font-bold text-slate-900 mb-1">
              {t('pricing', 'payment_pending')}
            </h1>
            <p className="text-sm text-slate-500 max-w-xs mx-auto">
              {t('notifications', 'payment_received_body', { plan: latestTransaction.plan_name })}
            </p>
          </div>
        )}

        {/* Not yet submitted */}
        {!isActive && isPending && !latestTransaction?.is_submitted && (
          <div className="text-center mb-8">
            <div className="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-slate-100 mb-4">
              <Clock size={32} className="text-slate-400" />
            </div>
            <h1 className="text-xl font-bold text-slate-900 mb-2">Payment Incomplete</h1>
            <p className="text-sm text-slate-500 mb-5">
              You haven't submitted your payment details yet.
            </p>
            <Button onClick={() => router.get(route('upgrade.manual', latestTransaction!.transaction_no))}>
              Complete Payment
            </Button>
          </div>
        )}

        {/* Rejected */}
        {!isActive && isRejected && (
          <div className="text-center mb-8">
            <div className="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-red-100 mb-4">
              <XCircle size={32} className="text-red-500" />
            </div>
            <h1 className="text-xl font-bold text-slate-900 mb-1">
              {t('notifications', 'membership_rejected_title')}
            </h1>
            {latestTransaction?.admin_note && (
              <p className="text-sm text-red-600 bg-red-50 rounded-xl px-4 py-3 mt-3 text-left">
                Reason: {latestTransaction.admin_note}
              </p>
            )}
            <p className="text-sm text-slate-500 mt-3">
              {t('pricing', 'payment_rejected')}
            </p>
          </div>
        )}

        {/* Free / no transaction */}
        {!isActive && !latestTransaction && (
          <div className="text-center mb-8">
            <div className="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-amber-100 mb-4">
              <Star size={32} className="text-amber-500" />
            </div>
            <h1 className="text-xl font-bold text-slate-900 mb-2">
              {t('dashboard', 'upgrade_title')}
            </h1>
            <p className="text-sm text-slate-500 mb-5">
              {t('dashboard', 'upgrade_subtitle')}
            </p>
          </div>
        )}

        {/* Transaction details card */}
        {latestTransaction && (
          <div className={cn(
            'rounded-2xl border p-5 mb-6',
            latestTransaction.status === 'paid'   ? 'border-emerald-200 bg-emerald-50' :
            latestTransaction.status === 'pending' ? 'border-amber-200 bg-amber-50' :
            latestTransaction.status === 'failed'  ? 'border-red-200 bg-red-50' :
            'border-slate-200 bg-slate-50',
          )}>
            <div className="space-y-2 text-sm">
              <Row label="Plan" value={latestTransaction.plan_name} />
              <Row label="Amount" value={`৳${latestTransaction.amount.toLocaleString('en-BD')}`} />
              <Row label="Ref" value={latestTransaction.transaction_no} mono />
              <Row
                label="Status"
                value={
                  latestTransaction.status === 'paid'    ? 'Approved' :
                  latestTransaction.status === 'pending' ? 'Under Review' :
                  latestTransaction.status === 'failed'  ? 'Rejected' : latestTransaction.status
                }
              />
              <Row label="Submitted" value={new Date(latestTransaction.created_at).toLocaleDateString('en-BD')} />
            </div>
          </div>
        )}

        {/* Actions */}
        <div className="flex flex-col gap-3">
          {(isRejected || !latestTransaction) && (
            <Button variant="premium" onClick={() => router.get(route('upgrade.plans'))}>
              {t('pricing', 'upgrade')}
            </Button>
          )}
          <Button variant="outline" onClick={() => router.get(route('dashboard'))}>
            {t('common', 'dashboard')}
          </Button>
        </div>
      </div>
    </AppLayout>
  )
}

function Row({ label, value, mono = false }: { label: string; value: string; mono?: boolean }) {
  return (
    <div className="flex justify-between gap-4">
      <span className="text-slate-500 shrink-0">{label}</span>
      <span className={cn('font-medium text-slate-800 text-right', mono && 'font-mono text-xs')}>{value}</span>
    </div>
  )
}

function cn(...c: (string | boolean | undefined)[]) { return c.filter(Boolean).join(' ') }
