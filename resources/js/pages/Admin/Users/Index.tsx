/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link, router } from '@inertiajs/react'
import { useEffect, useRef, useState } from 'react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { cn } from '@/lib/utils'
import {
  Search, Plus, Upload, Download, MoreVertical, Eye, Pencil,
  Pause, Play, KeyRound, Trash2, RotateCcw, Trash,
} from 'lucide-react'
import ConfirmDialog from '@/components/admin/ConfirmDialog'
import ResetPasswordModal from '@/components/admin/ResetPasswordModal'

interface User {
  id: number
  registration_id: string
  name: string
  email: string
  mobile_number: string | null
  gender: 'male' | 'female'
  account_status: string
  membership_status: string
  membership_plan_name: string | null
  is_email_verified: boolean
  is_mobile_verified: boolean
  is_admin: boolean
  role: string | null
  last_login_at: string | null
  created_at: string
  deleted_at: string | null
  biodata: { status: string; is_completed: boolean } | null
}

interface Paginated { data: User[]; current_page: number; last_page: number; total: number }
interface Plan { id: number; name: string }
interface Filters {
  search?: string; status?: string; gender?: string; membership?: string
  email_verified?: string; phone_verified?: string; biodata_status?: string
  joined_from?: string; joined_to?: string; trashed?: string
}
interface Props {
  users: Paginated
  filters: Filters
  plans: Plan[]
  authRegistrationId: string
  trashedCount: number
}

export default function UsersIndex({ users, filters, plans, authRegistrationId, trashedCount }: Props) {
  const { t } = useTranslation()
  const [search, setSearch] = useState(filters.search ?? '')
  const [selected, setSelected] = useState<Set<string>>(new Set())
  const [bulkAction, setBulkAction] = useState('')
  const [bulkPlan, setBulkPlan] = useState('free')
  const [confirm, setConfirm] = useState<null | { kind: 'delete' | 'force' | 'bulkDelete'; id?: string }>(null)
  const [resetFor, setResetFor] = useState<User | null>(null)
  const [busy, setBusy] = useState(false)

  const trashed = filters.trashed === '1'

  function applyFilter(key: string, value: string) {
    router.get(route('admin.users.index'), { ...filters, [key]: value || undefined, page: 1 }, {
      preserveState: true, replace: true,
    })
  }
  function submitSearch(e: React.FormEvent) { e.preventDefault(); applyFilter('search', search) }

  // ── Selection ──
  const selectableIds = users.data.filter(u => !u.is_admin).map(u => u.registration_id)
  const allSelected = selectableIds.length > 0 && selectableIds.every(id => selected.has(id))
  function toggleAll() {
    setSelected(allSelected ? new Set() : new Set(selectableIds))
  }
  function toggleOne(id: string) {
    setSelected(prev => {
      const next = new Set(prev)
      next.has(id) ? next.delete(id) : next.add(id)
      return next
    })
  }
  function clearSelection() { setSelected(new Set()); setBulkAction('') }

  // ── Single-row actions ──
  function post(name: string, id: string) {
    router.post(route(name, id), {}, { preserveScroll: true })
  }
  function doDelete(id: string) {
    router.delete(route('admin.users.destroy', id), { preserveScroll: true, onFinish: () => setConfirm(null) })
  }
  function doForceDelete(id: string) {
    router.delete(route('admin.users.force-delete', id), { preserveScroll: true, onFinish: () => setConfirm(null) })
  }

  // ── Bulk ──
  function runBulk() {
    if (!bulkAction || selected.size === 0) return
    if (bulkAction === 'delete') { setConfirm({ kind: 'bulkDelete' }); return }
    submitBulk(bulkAction)
  }
  function submitBulk(action: string) {
    setBusy(true)
    router.post(route('admin.users.bulk-action'), {
      action, ids: Array.from(selected), plan: bulkPlan,
    }, {
      preserveScroll: true,
      onSuccess: () => { clearSelection(); setConfirm(null) },
      onFinish: () => setBusy(false),
    })
  }

  return (
    <AdminLayout>
      <Head title={t('admin', 'users_title')} />

      <div className="space-y-5">
        {/* Header */}
        <div className="flex flex-wrap items-center justify-between gap-3">
          <h1 className="text-xl font-bold text-slate-900">
            {trashed ? t('admin', 'trashed_view') : t('admin', 'users_title')}
          </h1>
          <div className="flex flex-wrap items-center gap-2">
            <a href={route('admin.users.export', filters as Record<string, string>)}>
              <Button variant="outline" size="sm"><Download size={15} /> {t('admin', 'export_csv')}</Button>
            </a>
            <Link href={route('admin.users.import')}>
              <Button variant="outline" size="sm"><Upload size={15} /> {t('admin', 'bulk_import')}</Button>
            </Link>
            <Link href={route('admin.users.create')}>
              <Button size="sm"><Plus size={15} /> {t('admin', 'add_user')}</Button>
            </Link>
          </div>
        </div>

        {/* View toggle */}
        <div className="flex gap-2 text-sm">
          <Link
            href={route('admin.users.index')}
            className={cn('rounded-lg px-3 py-1.5 font-medium', !trashed ? 'bg-primary-50 text-primary-700' : 'text-slate-500 hover:bg-slate-100')}
          >
            {t('admin', 'active_view')}
          </Link>
          <Link
            href={route('admin.users.index', { trashed: 1 })}
            className={cn('rounded-lg px-3 py-1.5 font-medium', trashed ? 'bg-primary-50 text-primary-700' : 'text-slate-500 hover:bg-slate-100')}
          >
            {t('admin', 'trashed_view')} ({trashedCount})
          </Link>
        </div>

        {/* Filters */}
        <div className="flex flex-wrap gap-2">
          <form onSubmit={submitSearch} className="flex gap-2 flex-1 min-w-[220px]">
            <div className="relative flex-1">
              <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
              <input
                type="text" value={search} onChange={e => setSearch(e.target.value)}
                placeholder={t('admin', 'search_placeholder')}
                className="w-full rounded-xl border border-slate-200 pl-8 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
              />
            </div>
            <Button type="submit" size="sm">{t('common', 'search') || 'Search'}</Button>
          </form>

          <FilterSelect value={filters.gender ?? ''} onChange={v => applyFilter('gender', v)} options={[
            ['', t('admin', 'all_genders')], ['male', t('admin', 'user_gender_male')], ['female', t('admin', 'user_gender_female')],
          ]} />
          <FilterSelect value={filters.status ?? ''} onChange={v => applyFilter('status', v)} options={[
            ['', t('admin', 'all_statuses')], ['active', t('admin', 'status_active')], ['inactive', t('admin', 'status_inactive')],
            ['suspended', t('admin', 'status_suspended')], ['banned', t('admin', 'status_banned')],
          ]} />
          <FilterSelect value={filters.membership ?? ''} onChange={v => applyFilter('membership', v)} options={[
            ['', t('admin', 'all_memberships')], ['free', t('admin', 'membership_free')], ['active', t('admin', 'membership_premium')], ['expired', t('admin', 'membership_expired')],
          ]} />
          <FilterSelect value={filters.email_verified ?? ''} onChange={v => applyFilter('email_verified', v)} options={[
            ['', t('admin', 'filter_email_verified')], ['1', t('admin', 'verified_yes')], ['0', t('admin', 'verified_no')],
          ]} />
          <FilterSelect value={filters.phone_verified ?? ''} onChange={v => applyFilter('phone_verified', v)} options={[
            ['', t('admin', 'filter_phone_verified')], ['1', t('admin', 'verified_yes')], ['0', t('admin', 'verified_no')],
          ]} />
          <FilterSelect value={filters.biodata_status ?? ''} onChange={v => applyFilter('biodata_status', v)} options={[
            ['', t('admin', 'all_biodata')], ['approved', t('admin', 'bio_approved')], ['pending', t('admin', 'bio_pending')],
            ['rejected', t('admin', 'bio_rejected')], ['draft', t('admin', 'bio_draft')], ['hidden', t('admin', 'bio_hidden')], ['none', t('admin', 'bio_none')],
          ]} />
          <label className="flex items-center gap-1 rounded-xl border border-slate-200 px-2 text-xs text-slate-500">
            {t('admin', 'filter_joined_from')}
            <input type="date" value={filters.joined_from ?? ''} onChange={e => applyFilter('joined_from', e.target.value)}
              className="bg-transparent py-2 text-sm focus:outline-none" />
          </label>
          <label className="flex items-center gap-1 rounded-xl border border-slate-200 px-2 text-xs text-slate-500">
            {t('admin', 'filter_joined_to')}
            <input type="date" value={filters.joined_to ?? ''} onChange={e => applyFilter('joined_to', e.target.value)}
              className="bg-transparent py-2 text-sm focus:outline-none" />
          </label>
        </div>

        {/* Bulk toolbar */}
        {selected.size > 0 && (
          <div className="flex flex-wrap items-center gap-2 rounded-xl border border-primary-200 bg-primary-50 px-3 py-2">
            <span className="text-sm font-medium text-primary-800">
              {t('admin', 'bulk_selected', { count: selected.size }).replace(':count', String(selected.size))}
            </span>
            <select value={bulkAction} onChange={e => setBulkAction(e.target.value)}
              className="rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
              <option value="">{t('admin', 'bulk_action')}</option>
              {!trashed && <option value="delete">{t('admin', 'bulk_delete')}</option>}
              {!trashed && <option value="activate">{t('admin', 'bulk_activate')}</option>}
              {!trashed && <option value="suspend">{t('admin', 'bulk_suspend')}</option>}
              {!trashed && <option value="verify_email">{t('admin', 'bulk_verify_email')}</option>}
              {!trashed && <option value="verify_phone">{t('admin', 'bulk_verify_phone')}</option>}
              {!trashed && <option value="change_plan">{t('admin', 'bulk_change_plan')}</option>}
              {trashed && <option value="restore">{t('admin', 'action_restore')}</option>}
              {trashed && <option value="force_delete">{t('admin', 'action_force_delete')}</option>}
            </select>
            {bulkAction === 'change_plan' && (
              <select value={bulkPlan} onChange={e => setBulkPlan(e.target.value)}
                className="rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="free">{t('admin', 'plan_free')}</option>
                {plans.map(p => <option key={p.id} value={p.id}>{p.name}</option>)}
              </select>
            )}
            <Button size="sm" onClick={runBulk} disabled={!bulkAction || busy} isLoading={busy}>{t('admin', 'bulk_apply')}</Button>
            <button onClick={clearSelection} className="text-sm text-slate-500 hover:text-slate-700">{t('admin', 'clear_selection')}</button>
          </div>
        )}

        {/* Count */}
        <p className="text-sm text-slate-500">{t('admin', 'users_count').replace(':count', String(users.total))}</p>

        {/* Table */}
        <div className="rounded-2xl border border-slate-200 bg-white overflow-x-auto">
          <table className="w-full text-sm min-w-[820px]">
            <thead>
              <tr className="border-b border-slate-100 bg-slate-50">
                <th className="w-10 px-4 py-3">
                  <input type="checkbox" checked={allSelected} onChange={toggleAll}
                    className="rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                </th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_name')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_id')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_status')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_membership')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_verified')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_biodata')}</th>
                <th className="text-left px-4 py-3 text-xs font-semibold text-slate-500">{t('admin', 'col_joined')}</th>
                <th className="px-4 py-3 text-xs font-semibold text-slate-500 text-right">{t('admin', 'col_actions')}</th>
              </tr>
            </thead>
            <tbody>
              {users.data.length === 0 ? (
                <tr><td colSpan={9} className="px-4 py-8 text-center text-slate-400 text-sm">{t('admin', 'no_users_found')}</td></tr>
              ) : users.data.map(u => {
                const isSelf = u.registration_id === authRegistrationId
                const locked = u.is_admin || isSelf
                return (
                  <tr key={u.registration_id} className="border-b border-slate-50 last:border-0 hover:bg-slate-50">
                    <td className="px-4 py-3">
                      <input type="checkbox" disabled={u.is_admin}
                        checked={selected.has(u.registration_id)} onChange={() => toggleOne(u.registration_id)}
                        className="rounded border-slate-300 text-primary-600 focus:ring-primary-500 disabled:opacity-30" />
                    </td>
                    <td className="px-4 py-3">
                      <p className="font-medium text-slate-800">{u.name}{u.is_admin && <span className="ml-1.5 rounded bg-violet-100 px-1.5 py-0.5 text-[10px] font-semibold text-violet-700">ADMIN</span>}</p>
                      <p className="text-xs text-slate-400">{u.email}</p>
                    </td>
                    <td className="px-4 py-3 font-mono text-xs text-slate-500">{u.registration_id}</td>
                    <td className="px-4 py-3"><StatusBadge status={u.account_status} /></td>
                    <td className="px-4 py-3"><PlanBadge status={u.membership_status} name={u.membership_plan_name} freeLabel={t('admin', 'membership_free')} /></td>
                    <td className="px-4 py-3">
                      <div className="flex flex-wrap gap-1">
                        <VerifyPill label={t('admin', 'badge_email')} on={u.is_email_verified} />
                        <VerifyPill label={t('admin', 'badge_phone')} on={u.is_mobile_verified} />
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      {u.biodata
                        ? <BioBadge status={u.biodata.status} />
                        : <span className="text-xs text-slate-400">—</span>}
                    </td>
                    <td className="px-4 py-3 text-xs text-slate-400">{new Date(u.created_at).toLocaleDateString('en-BD')}</td>
                    <td className="px-4 py-3 text-right">
                      <RowMenu
                        user={u} trashed={trashed} locked={locked} t={t}
                        onView={() => router.get(route('admin.users.show', u.registration_id))}
                        onEdit={() => router.get(route('admin.users.edit', u.registration_id))}
                        onSuspend={() => post('admin.users.suspend', u.registration_id)}
                        onActivate={() => post('admin.users.activate', u.registration_id)}
                        onReset={() => setResetFor(u)}
                        onDelete={() => setConfirm({ kind: 'delete', id: u.registration_id })}
                        onRestore={() => post('admin.users.restore', u.registration_id)}
                        onForce={() => setConfirm({ kind: 'force', id: u.registration_id })}
                      />
                    </td>
                  </tr>
                )
              })}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        {users.last_page > 1 && (
          <div className="flex justify-center gap-2">
            {users.current_page > 1 && (
              <Button size="sm" variant="outline" onClick={() => router.get(route('admin.users.index'), { ...filters, page: users.current_page - 1 })}>
                {t('common', 'previous')}
              </Button>
            )}
            <span className="text-sm text-slate-500 flex items-center px-3">{users.current_page} / {users.last_page}</span>
            {users.current_page < users.last_page && (
              <Button size="sm" variant="outline" onClick={() => router.get(route('admin.users.index'), { ...filters, page: users.current_page + 1 })}>
                {t('common', 'next')}
              </Button>
            )}
          </div>
        )}
      </div>

      {/* Confirm dialogs */}
      <ConfirmDialog
        open={confirm?.kind === 'delete'} title={t('admin', 'confirm_delete_title')} body={t('admin', 'confirm_delete_body')}
        confirmLabel={t('admin', 'action_delete')} cancelLabel={t('common', 'cancel') || 'Cancel'}
        onConfirm={() => confirm?.id && doDelete(confirm.id)} onClose={() => setConfirm(null)}
      />
      <ConfirmDialog
        open={confirm?.kind === 'force'} title={t('admin', 'confirm_force_title')} body={t('admin', 'confirm_force_body')}
        confirmLabel={t('admin', 'action_force_delete')} cancelLabel={t('common', 'cancel') || 'Cancel'}
        onConfirm={() => confirm?.id && doForceDelete(confirm.id)} onClose={() => setConfirm(null)}
      />
      <ConfirmDialog
        open={confirm?.kind === 'bulkDelete'} title={t('admin', 'confirm_bulk_delete_title')} body={t('admin', 'confirm_bulk_delete_body')}
        confirmLabel={t('admin', 'bulk_delete')} cancelLabel={t('common', 'cancel') || 'Cancel'} loading={busy}
        onConfirm={() => submitBulk('delete')} onClose={() => setConfirm(null)}
      />

      <ResetPasswordModal
        open={!!resetFor} registrationId={resetFor?.registration_id ?? ''} userName={resetFor?.name ?? ''}
        onClose={() => setResetFor(null)}
      />
    </AdminLayout>
  )
}

// ── Row actions dropdown ──
function RowMenu({ user, trashed, locked, t, onView, onEdit, onSuspend, onActivate, onReset, onDelete, onRestore, onForce }: any) {
  const [open, setOpen] = useState(false)
  const ref = useRef<HTMLDivElement>(null)
  useEffect(() => {
    if (!open) return
    const h = (e: MouseEvent) => { if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false) }
    document.addEventListener('mousedown', h)
    return () => document.removeEventListener('mousedown', h)
  }, [open])

  const item = 'flex w-full items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50'
  const run = (fn: () => void) => { setOpen(false); fn() }

  return (
    <div className="relative inline-block text-left" ref={ref}>
      <button onClick={() => setOpen(o => !o)} className="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100" aria-label="Actions">
        <MoreVertical size={16} />
      </button>
      {open && (
        <div className="absolute right-0 z-50 mt-1 w-48 origin-top-right rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
          {!trashed && <>
            <button className={item} onClick={() => run(onView)}><Eye size={14} /> {t('admin', 'action_view')}</button>
            <button className={item} onClick={() => run(onEdit)}><Pencil size={14} /> {t('admin', 'action_edit')}</button>
            {user.account_status === 'suspended'
              ? <button className={item} onClick={() => run(onActivate)}><Play size={14} /> {t('admin', 'action_activate')}</button>
              : !locked && <button className={item} onClick={() => run(onSuspend)}><Pause size={14} /> {t('admin', 'action_suspend')}</button>}
            <button className={item} onClick={() => run(onReset)}><KeyRound size={14} /> {t('admin', 'action_reset_password')}</button>
            {!locked && <button className={cn(item, 'text-red-600 hover:bg-red-50')} onClick={() => run(onDelete)}><Trash2 size={14} /> {t('admin', 'action_delete')}</button>}
          </>}
          {trashed && <>
            <button className={item} onClick={() => run(onRestore)}><RotateCcw size={14} /> {t('admin', 'action_restore')}</button>
            {!locked && <button className={cn(item, 'text-red-600 hover:bg-red-50')} onClick={() => run(onForce)}><Trash size={14} /> {t('admin', 'action_force_delete')}</button>}
          </>}
        </div>
      )}
    </div>
  )
}

function FilterSelect({ value, onChange, options }: { value: string; onChange: (v: string) => void; options: [string, string][] }) {
  return (
    <select value={value} onChange={e => onChange(e.target.value)}
      className="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-primary-500">
      {options.map(([v, label]) => <option key={v} value={v}>{label}</option>)}
    </select>
  )
}

function StatusBadge({ status }: { status: string }) {
  const map: Record<string, string> = {
    active: 'bg-emerald-100 text-emerald-700', inactive: 'bg-slate-100 text-slate-600',
    suspended: 'bg-amber-100 text-amber-700', banned: 'bg-red-100 text-red-700',
  }
  return <span className={cn('inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize', map[status] ?? 'bg-slate-100 text-slate-600')}>{status}</span>
}

function PlanBadge({ status, name, freeLabel }: { status: string; name: string | null; freeLabel: string }) {
  if (status === 'active') return <span className="inline-block rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-700">{name || 'Premium'}</span>
  return <span className="inline-block rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">{freeLabel}</span>
}

function VerifyPill({ label, on }: { label: string; on: boolean }) {
  return (
    <span className={cn('inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium', on ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400')}>
      <span className={cn('h-1.5 w-1.5 rounded-full', on ? 'bg-emerald-500' : 'bg-slate-300')} />{label}
    </span>
  )
}

function BioBadge({ status }: { status: string }) {
  const map: Record<string, string> = {
    approved: 'bg-emerald-100 text-emerald-700', pending: 'bg-amber-100 text-amber-700',
    rejected: 'bg-red-100 text-red-700', draft: 'bg-slate-100 text-slate-500', hidden: 'bg-slate-100 text-slate-500',
  }
  return <span className={cn('inline-block rounded-full px-2 py-0.5 text-xs font-medium capitalize', map[status] ?? 'bg-slate-100 text-slate-500')}>{status}</span>
}
