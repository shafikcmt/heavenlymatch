/// <reference path="../../../types/ziggy.d.ts" />
import { Head, router } from '@inertiajs/react'
import { useState } from 'react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { Search } from 'lucide-react'
import { cn } from '@/lib/utils'

interface User {
  registration_id: string
  name: string
  email: string
  gender: 'male' | 'female'
  account_status: string
  membership_status: string
  is_email_verified: boolean
  is_mobile_verified: boolean
  created_at: string
  biodata: { status: string } | null
}

interface Paginated {
  data: User[]
  current_page: number
  last_page: number
  total: number
}

interface Filters {
  search?: string
  status?: string
  gender?: string
  membership?: string
}

interface Props {
  users: Paginated
  filters: Filters
}

export default function UsersIndex({ users, filters }: Props) {
  const { t } = useTranslation()
  const [search, setSearch] = useState(filters.search ?? '')

  function applyFilter(key: string, value: string) {
    router.get(route('admin.users.index'), { ...filters, [key]: value || undefined, page: 1 }, {
      preserveState: true,
      replace: true,
    })
  }

  function submitSearch(e: React.FormEvent) {
    e.preventDefault()
    applyFilter('search', search)
  }

  return (
    <AdminLayout>
      <Head title={t('admin', 'users_title')} />

      <div className="space-y-5">
        <h1 className="text-xl font-bold text-slate-900">{t('admin', 'users_title')}</h1>

        {/* Search + filters */}
        <div className="flex flex-wrap gap-3">
          <form onSubmit={submitSearch} className="flex gap-2 flex-1 min-w-[200px]">
            <div className="relative flex-1">
              <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
              <input
                type="text"
                value={search}
                onChange={e => setSearch(e.target.value)}
                placeholder={t('admin', 'search_placeholder')}
                className="w-full rounded-xl border border-slate-200 pl-8 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
              />
            </div>
            <Button type="submit" size="sm" variant="default">
              {t('common', 'search') || 'Search'}
            </Button>
          </form>

          <select
            value={filters.status ?? ''}
            onChange={e => applyFilter('status', e.target.value)}
            className="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
            <option value="">{t('admin', 'all_statuses')}</option>
            <option value="active">{t('admin', 'status_active')}</option>
            <option value="inactive">{t('admin', 'status_inactive')}</option>
            <option value="suspended">{t('admin', 'status_suspended')}</option>
            <option value="banned">{t('admin', 'status_banned')}</option>
          </select>

          <select
            value={filters.gender ?? ''}
            onChange={e => applyFilter('gender', e.target.value)}
            className="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
            <option value="">{t('admin', 'all_genders')}</option>
            <option value="male">{t('admin', 'user_gender_male')}</option>
            <option value="female">{t('admin', 'user_gender_female')}</option>
          </select>

          <select
            value={filters.membership ?? ''}
            onChange={e => applyFilter('membership', e.target.value)}
            className="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
            <option value="">{t('admin', 'all_memberships')}</option>
            <option value="free">{t('admin', 'membership_free')}</option>
            <option value="active">{t('admin', 'membership_premium')}</option>
            <option value="expired">{t('admin', 'membership_expired')}</option>
          </select>
        </div>

        {/* Count */}
        <p className="text-sm text-slate-500">{t('admin', 'users_count').replace(':count', String(users.total))}</p>

        {/* Table */}
        <div className="rounded-2xl border border-slate-200 bg-white overflow-x-auto">
          <table className="w-full text-sm min-w-[640px]">
            <thead>
              <tr className="border-b border-slate-100 bg-slate-50">
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_name')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_id')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_gender')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_status')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_membership')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_verified')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_biodata')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_joined')}</th>
              </tr>
            </thead>
            <tbody>
              {users.data.length === 0 ? (
                <tr>
                  <td colSpan={8} className="px-4 py-8 text-center text-slate-400 text-sm">
                    {t('admin', 'no_users_found')}
                  </td>
                </tr>
              ) : (
                users.data.map(u => (
                  <tr
                    key={u.registration_id}
                    onClick={() => router.get(route('admin.users.show', u.registration_id))}
                    className="border-b border-slate-50 last:border-0 hover:bg-slate-50 cursor-pointer"
                  >
                    <td className="px-4 py-3">
                      <p className="font-medium text-slate-800">{u.name}</p>
                      <p className="text-xs text-slate-400">{u.email}</p>
                    </td>
                    <td className="px-4 py-3 font-mono text-xs text-slate-500">{u.registration_id}</td>
                    <td className="px-4 py-3 text-slate-600 capitalize">{u.gender}</td>
                    <td className="px-4 py-3">
                      <StatusBadge status={u.account_status} />
                    </td>
                    <td className="px-4 py-3">
                      <MembershipBadge status={u.membership_status} labelPremium={t('admin', 'membership_premium')} labelFree={t('admin', 'membership_free')} />
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex flex-wrap gap-1">
                        <VerifyPill
                          label={t('admin', 'badge_email')}
                          on={u.is_email_verified}
                          title={u.is_email_verified ? t('admin', 'email_verified') : t('admin', 'email_not_verified')}
                        />
                        <VerifyPill
                          label={t('admin', 'badge_phone')}
                          on={u.is_mobile_verified}
                          title={u.is_mobile_verified ? t('admin', 'phone_verified') : t('admin', 'phone_not_verified')}
                        />
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      {u.biodata ? (
                        <span className={cn(
                          'inline-block rounded-full px-2 py-0.5 text-xs font-medium',
                          u.biodata.status === 'approved' ? 'bg-emerald-100 text-emerald-700' :
                          u.biodata.status === 'pending'  ? 'bg-amber-100 text-amber-700' :
                          u.biodata.status === 'rejected' ? 'bg-red-100 text-red-700' :
                          'bg-slate-100 text-slate-500',
                        )}>
                          {u.biodata.status}
                        </span>
                      ) : (
                        <span className="text-xs text-slate-400">—</span>
                      )}
                    </td>
                    <td className="px-4 py-3 text-xs text-slate-400">
                      {new Date(u.created_at).toLocaleDateString('en-BD')}
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        {users.last_page > 1 && (
          <div className="flex justify-center gap-2">
            {users.current_page > 1 && (
              <Button
                size="sm"
                variant="outline"
                onClick={() => router.get(route('admin.users.index'), { ...filters, page: users.current_page - 1 })}
              >
                {t('common', 'previous')}
              </Button>
            )}
            <span className="text-sm text-slate-500 flex items-center px-3">
              {users.current_page} / {users.last_page}
            </span>
            {users.current_page < users.last_page && (
              <Button
                size="sm"
                variant="outline"
                onClick={() => router.get(route('admin.users.index'), { ...filters, page: users.current_page + 1 })}
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

function StatusBadge({ status }: { status: string }) {
  const map: Record<string, string> = {
    active:    'bg-emerald-100 text-emerald-700',
    inactive:  'bg-slate-100 text-slate-600',
    suspended: 'bg-amber-100 text-amber-700',
    banned:    'bg-red-100 text-red-700',
  }
  return (
    <span className={cn('inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize', map[status] ?? 'bg-slate-100 text-slate-600')}>
      {status}
    </span>
  )
}

function VerifyPill({ label, on, title }: { label: string; on: boolean; title: string }) {
  return (
    <span
      title={title}
      className={cn(
        'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium',
        on ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400',
      )}
    >
      <span className={cn('h-1.5 w-1.5 rounded-full', on ? 'bg-emerald-500' : 'bg-slate-300')} />
      {label}
    </span>
  )
}

function MembershipBadge({ status, labelPremium, labelFree }: { status: string; labelPremium: string; labelFree: string }) {
  if (status === 'active') {
    return <span className="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-violet-100 text-violet-700">{labelPremium}</span>
  }
  return <span className="inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-slate-100 text-slate-500">{labelFree}</span>
}
