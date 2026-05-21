/// <reference path="../../types/ziggy.d.ts" />
import { Head, router } from '@inertiajs/react'
import AdminLayout from '@/layouts/AdminLayout'
import { useTranslation } from '@/lib/i18n'
import { Users, FileText, CreditCard, AlertCircle, TrendingUp, Star, ArrowRight } from 'lucide-react'
import { cn } from '@/lib/utils'

interface RecentUser {
  registration_id: string
  name: string
  email: string
  gender: 'male' | 'female'
  account_status: string
  membership_status: string
  created_at: string
}

interface PendingBiodata {
  id: number
  registration_id: string
  status: string
  updated_at: string
  registration: { registration_id: string; name: string; gender: string } | null
}

interface PendingPayment {
  id: number
  registration_id: string
  transaction_no: string
  plan_name: string
  amount: number
  updated_at: string
  registration: { registration_id: string; name: string } | null
}

interface Stats {
  total_users: number
  active_users: number
  new_today: number
  pending_biodatas: number
  total_premium: number
  pending_payments: number
  open_reports: number
  total_revenue: number
}

interface Props {
  stats: Stats
  recentUsers: RecentUser[]
  pendingBiodatas: PendingBiodata[]
  pendingPayments: PendingPayment[]
}

export default function Dashboard({ stats, recentUsers, pendingBiodatas, pendingPayments }: Props) {
  const { t } = useTranslation()

  return (
    <AdminLayout>
      <Head title={t('admin', 'dashboard_title')} />

      <div className="space-y-6">
        <h1 className="text-xl font-bold text-slate-900">{t('admin', 'dashboard_title')}</h1>

        {/* Stat cards */}
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
          <StatCard
            icon={<Users size={18} />}
            label={t('admin', 'total_users')}
            value={stats.total_users}
            color="blue"
          />
          <StatCard
            icon={<TrendingUp size={18} />}
            label={t('admin', 'new_today')}
            value={stats.new_today}
            color="green"
          />
          <StatCard
            icon={<FileText size={18} />}
            label={t('admin', 'pending_review')}
            value={stats.pending_biodatas}
            color="amber"
            onClick={() => router.get(route('admin.biodatas.index'))}
          />
          <StatCard
            icon={<CreditCard size={18} />}
            label={t('admin', 'pending_payments')}
            value={stats.pending_payments}
            color="purple"
            onClick={() => router.get(route('admin.payments.index'))}
          />
          <StatCard
            icon={<Star size={18} />}
            label={t('admin', 'total_premium')}
            value={stats.total_premium}
            color="gold"
          />
          <StatCard
            icon={<AlertCircle size={18} />}
            label={t('admin', 'open_reports')}
            value={stats.open_reports}
            color="red"
            onClick={() => router.get(route('admin.reports.index'))}
          />
          <StatCard
            icon={<Users size={18} />}
            label={t('admin', 'active_users')}
            value={stats.active_users}
            color="green"
          />
          <StatCard
            icon={<CreditCard size={18} />}
            label="Revenue (BDT)"
            value={`৳${stats.total_revenue.toLocaleString('en-BD')}`}
            color="green"
            wide
          />
        </div>

        {/* Pending queues */}
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          {/* Pending biodatas */}
          <section className="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <div className="flex items-center justify-between px-4 py-3 border-b border-slate-100">
              <h2 className="text-sm font-semibold text-slate-900">{t('admin', 'pending_review')}</h2>
              <button
                onClick={() => router.get(route('admin.biodatas.index'))}
                className="text-xs text-primary-600 hover:underline flex items-center gap-1"
              >
                View all <ArrowRight size={12} />
              </button>
            </div>
            {pendingBiodatas.length === 0 ? (
              <p className="px-4 py-6 text-center text-sm text-slate-400">{t('admin', 'no_pending')}</p>
            ) : (
              <ul className="divide-y divide-slate-50">
                {pendingBiodatas.map(b => (
                  <li
                    key={b.id}
                    className="flex items-center justify-between px-4 py-3 hover:bg-slate-50 cursor-pointer"
                    onClick={() => router.get(route('admin.biodatas.index'))}
                  >
                    <div>
                      <p className="text-sm font-medium text-slate-800">
                        {b.registration?.name ?? b.registration_id}
                      </p>
                      <p className="text-xs text-slate-400">{b.registration?.gender}</p>
                    </div>
                    <span className="text-xs text-amber-600 font-medium bg-amber-50 px-2 py-0.5 rounded-full">
                      Pending
                    </span>
                  </li>
                ))}
              </ul>
            )}
          </section>

          {/* Pending payments */}
          <section className="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <div className="flex items-center justify-between px-4 py-3 border-b border-slate-100">
              <h2 className="text-sm font-semibold text-slate-900">{t('admin', 'pending_payments')}</h2>
              <button
                onClick={() => router.get(route('admin.payments.index'))}
                className="text-xs text-primary-600 hover:underline flex items-center gap-1"
              >
                View all <ArrowRight size={12} />
              </button>
            </div>
            {pendingPayments.length === 0 ? (
              <p className="px-4 py-6 text-center text-sm text-slate-400">{t('admin', 'no_pending_payments')}</p>
            ) : (
              <ul className="divide-y divide-slate-50">
                {pendingPayments.map(p => (
                  <li
                    key={p.id}
                    className="flex items-center justify-between px-4 py-3 hover:bg-slate-50 cursor-pointer"
                    onClick={() => router.get(route('admin.payments.index'))}
                  >
                    <div>
                      <p className="text-sm font-medium text-slate-800">
                        {p.registration?.name ?? p.registration_id}
                      </p>
                      <p className="text-xs text-slate-400">{p.plan_name}</p>
                    </div>
                    <p className="text-sm font-bold text-slate-800">৳{p.amount.toLocaleString('en-BD')}</p>
                  </li>
                ))}
              </ul>
            )}
          </section>
        </div>

        {/* Recent registrations */}
        <section>
          <div className="flex items-center justify-between mb-3">
            <h2 className="text-base font-semibold text-slate-900">{t('admin', 'recent_users')}</h2>
            <button
              onClick={() => router.get(route('admin.users.index'))}
              className="text-xs text-primary-600 hover:underline flex items-center gap-1"
            >
              View all <ArrowRight size={12} />
            </button>
          </div>
          <div className="rounded-2xl border border-slate-200 bg-white overflow-x-auto">
            <table className="w-full text-sm min-w-[540px]">
              <thead>
                <tr className="border-b border-slate-100 bg-slate-50">
                  <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">Name</th>
                  <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">ID</th>
                  <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">Status</th>
                  <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">Plan</th>
                  <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">Joined</th>
                </tr>
              </thead>
              <tbody>
                {recentUsers.map(u => (
                  <tr
                    key={u.registration_id}
                    className="border-b border-slate-50 last:border-0 hover:bg-slate-50 cursor-pointer"
                    onClick={() => router.get(route('admin.users.show', u.registration_id))}
                  >
                    <td className="px-4 py-3 font-medium text-slate-800">{u.name}</td>
                    <td className="px-4 py-3 text-slate-500 font-mono text-xs">{u.registration_id}</td>
                    <td className="px-4 py-3">
                      <StatusBadge status={u.account_status} />
                    </td>
                    <td className="px-4 py-3">
                      <MembershipBadge status={u.membership_status} />
                    </td>
                    <td className="px-4 py-3 text-slate-400 text-xs">
                      {new Date(u.created_at).toLocaleDateString('en-BD')}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </AdminLayout>
  )
}

function StatCard({
  icon, label, value, color, onClick, wide = false,
}: {
  icon: React.ReactNode
  label: string
  value: number | string
  color: 'blue' | 'green' | 'amber' | 'purple' | 'red' | 'gold'
  onClick?: () => void
  wide?: boolean
}) {
  const colors = {
    blue:   { bg: 'bg-blue-50',   icon: 'text-blue-600',   val: 'text-blue-800' },
    green:  { bg: 'bg-emerald-50',icon: 'text-emerald-600',val: 'text-emerald-800' },
    amber:  { bg: 'bg-amber-50',  icon: 'text-amber-600',  val: 'text-amber-800' },
    purple: { bg: 'bg-violet-50', icon: 'text-violet-600', val: 'text-violet-800' },
    red:    { bg: 'bg-red-50',    icon: 'text-red-600',    val: 'text-red-800' },
    gold:   { bg: 'bg-yellow-50', icon: 'text-yellow-600', val: 'text-yellow-800' },
  }
  const c = colors[color]
  return (
    <div
      onClick={onClick}
      className={cn(
        'rounded-2xl border p-4',
        c.bg,
        onClick && 'cursor-pointer hover:opacity-90 transition-opacity',
        wide && 'sm:col-span-1',
      )}
    >
      <div className={cn('mb-2', c.icon)}>{icon}</div>
      <p className={cn('text-xl font-extrabold', c.val)}>{value}</p>
      <p className="text-xs text-slate-500 mt-0.5">{label}</p>
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
    <span className={cn('inline-block rounded-full px-2 py-0.5 text-xs font-medium', map[status] ?? 'bg-slate-100 text-slate-600')}>
      {status}
    </span>
  )
}

function MembershipBadge({ status }: { status: string }) {
  if (status === 'active') {
    return <span className="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-violet-100 text-violet-700">Premium</span>
  }
  return <span className="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-slate-100 text-slate-500">Free</span>
}
