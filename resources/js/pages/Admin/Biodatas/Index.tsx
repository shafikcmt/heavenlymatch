/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link, router, useForm } from '@inertiajs/react'
import { useEffect, useRef, useState } from 'react'
import axios from 'axios'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import ConfirmDialog from '@/components/admin/ConfirmDialog'
import { useTranslation } from '@/lib/i18n'
import { cn } from '@/lib/utils'
import {
  Search, CheckCircle, XCircle, Eye, EyeOff, RotateCcw, FileSearch,
  Clock, ShieldCheck, Ban, Layers, Inbox, Mail, Phone, Crown, MapPin,
  Image as ImageIcon, ChevronRight, X, ExternalLink,
} from 'lucide-react'

interface BiodataRegistration {
  registration_id: string
  name: string
  email: string
  gender: string
  platform_mode: string
  is_email_verified: boolean
  is_mobile_verified: boolean
  membership_status: string
  membership_plan_name: string | null
}

interface Biodata {
  id: number
  registration_id: string
  status: string
  admin_note: string | null
  completeness_score: number | null
  district: string | null
  division: string | null
  sect: string | null
  photos_count: number
  updated_at: string
  registration: BiodataRegistration | null
}

interface Paginated {
  data: Biodata[]
  current_page: number
  last_page: number
  total: number
}

interface Counts { all: number; pending: number; approved: number; rejected: number; hidden: number }
interface Filters {
  search?: string; gender?: string; sect?: string; status?: string
  min_completeness?: string; updated_from?: string; updated_to?: string; sort?: string
}
interface Props { biodatas: Paginated; counts: Counts; tab: string; filters: Filters; sects: string[] }

// ── Helpers ──────────────────────────────────────────────────────────────────
const STATUS_STYLES: Record<string, string> = {
  pending:  'bg-amber-100 text-amber-700',
  approved: 'bg-emerald-100 text-emerald-700',
  rejected: 'bg-red-100 text-red-700',
  hidden:   'bg-slate-200 text-slate-600',
  draft:    'bg-slate-100 text-slate-400',
}

function completionColor(pct: number) {
  if (pct >= 80) return 'bg-emerald-500'
  if (pct >= 50) return 'bg-amber-500'
  return 'bg-red-500'
}

export default function BiodatasIndex({ biodatas, counts, tab, filters, sects }: Props) {
  const { t } = useTranslation()
  const [search, setSearch] = useState(filters.search ?? '')
  const [selected, setSelected] = useState<Set<number>>(new Set())
  const [bulkAction, setBulkAction] = useState('')
  const [navigating, setNavigating] = useState(false)

  // Modals
  const [rejectFor, setRejectFor] = useState<number | null>(null)
  const [hideFor, setHideFor] = useState<number | null>(null)
  const [previewId, setPreviewId] = useState<number | null>(null)
  const [confirm, setConfirm] = useState<null | {
    kind: 'approve' | 'unhide' | 'bulk'
    id?: number
    action?: string
  }>(null)
  const [bulkReject, setBulkReject] = useState(false)
  const [busy, setBusy] = useState(false)

  const filterActive = Object.entries(filters).some(([k, v]) => k !== 'sort' && v)

  // ── Navigation / filters ──
  function go(params: Record<string, any>, opts: Record<string, any> = {}) {
    router.get(route('admin.biodatas.index'), params, {
      preserveState: true, replace: true,
      onStart: () => setNavigating(true),
      onFinish: () => setNavigating(false),
      ...opts,
    })
  }
  function applyFilter(key: string, value: string) {
    go({ ...filters, tab, [key]: value || undefined, page: 1 })
  }
  function submitSearch(e: React.FormEvent) { e.preventDefault(); applyFilter('search', search) }
  function switchTab(newTab: string) {
    setSelected(new Set())
    go({ tab: newTab, sort: filters.sort }, { preserveState: false })
  }
  function resetFilters() {
    setSearch('')
    router.get(route('admin.biodatas.index'), { tab }, { replace: true })
  }

  // ── Selection ──
  const pageIds = biodatas.data.map(b => b.id)
  const allSelected = pageIds.length > 0 && pageIds.every(id => selected.has(id))
  function toggleAll() { setSelected(allSelected ? new Set() : new Set(pageIds)) }
  function toggleOne(id: number) {
    setSelected(prev => {
      const next = new Set(prev)
      next.has(id) ? next.delete(id) : next.add(id)
      return next
    })
  }
  function clearSelection() { setSelected(new Set()); setBulkAction('') }

  // ── Single actions ──
  function doApprove(id: number) {
    router.post(route('admin.biodatas.approve', id), {}, { preserveScroll: true, onFinish: () => setConfirm(null) })
  }
  function doUnhide(id: number) {
    router.post(route('admin.biodatas.unhide', id), {}, { preserveScroll: true, onFinish: () => setConfirm(null) })
  }

  // ── Bulk ──
  function runBulk() {
    if (!bulkAction || selected.size === 0) return
    if (bulkAction === 'reject') { setBulkReject(true); return }
    setConfirm({ kind: 'bulk', action: bulkAction })
  }
  function submitBulk(action: string, note?: string) {
    setBusy(true)
    router.post(route('admin.biodatas.bulk-action'), {
      action, ids: Array.from(selected), note,
    }, {
      preserveScroll: true,
      onSuccess: () => { clearSelection(); setConfirm(null); setBulkReject(false) },
      onFinish: () => setBusy(false),
    })
  }

  const tabs = [
    { key: 'pending',  label: t('admin', 'biodata_tab_pending'),  count: counts.pending,  icon: Clock },
    { key: 'approved', label: t('admin', 'biodata_tab_approved'), count: counts.approved, icon: ShieldCheck },
    { key: 'rejected', label: t('admin', 'biodata_tab_rejected'), count: counts.rejected, icon: XCircle },
    { key: 'hidden',   label: t('admin', 'biodata_tab_hidden'),   count: counts.hidden,   icon: EyeOff },
    { key: 'all',      label: t('admin', 'biodata_tab_all'),      count: counts.all,      icon: Layers },
  ]

  return (
    <AdminLayout>
      <Head title={t('admin', 'biodatas_title')} />

      <div className="space-y-5">
        {/* Header */}
        <div>
          <h1 className="text-xl font-bold text-slate-900">{t('admin', 'biodatas_title')}</h1>
          <p className="text-sm text-slate-500">{t('admin', 'biodata_subtitle')}</p>
        </div>

        {/* Summary cards */}
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
          <SummaryCard label={t('admin', 'biodata_summary_pending')}  value={counts.pending}  icon={Clock}       tone="amber" />
          <SummaryCard label={t('admin', 'biodata_summary_approved')} value={counts.approved} icon={ShieldCheck} tone="emerald" />
          <SummaryCard label={t('admin', 'biodata_summary_rejected')} value={counts.rejected} icon={Ban}         tone="red" />
          <SummaryCard label={t('admin', 'biodata_summary_hidden')}   value={counts.hidden}   icon={EyeOff}      tone="slate" />
          <SummaryCard label={t('admin', 'biodata_summary_total')}    value={counts.all}      icon={Layers}      tone="primary" />
        </div>

        {/* Tabs */}
        <div className="-mx-1 flex gap-1.5 overflow-x-auto px-1 pb-1">
          {tabs.map(({ key, label, count, icon: Icon }) => (
            <button
              key={key}
              onClick={() => switchTab(key)}
              className={cn(
                'inline-flex shrink-0 items-center gap-1.5 rounded-full px-3.5 py-2 text-sm font-medium transition-colors',
                tab === key ? 'bg-primary-600 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50',
              )}
            >
              <Icon size={14} />
              {label}
              <span className={cn(
                'rounded-full px-1.5 py-0.5 text-[11px] font-semibold',
                tab === key ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-500',
              )}>
                {count}
              </span>
            </button>
          ))}
        </div>

        {/* Filter bar */}
        <div className="rounded-2xl border border-slate-200 bg-white p-3">
          <div className="flex flex-wrap items-center gap-2">
            <form onSubmit={submitSearch} className="relative min-w-[200px] flex-1">
              <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
              <input
                type="text" value={search} onChange={e => setSearch(e.target.value)}
                placeholder={t('admin', 'biodata_search_ph')}
                className="w-full rounded-xl border border-slate-200 py-2 pl-8 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
              />
            </form>
            <FilterSelect value={filters.gender ?? ''} onChange={v => applyFilter('gender', v)} options={[
              ['', t('admin', 'biodata_filter_gender')], ['male', t('admin', 'biodata_gender_male')], ['female', t('admin', 'biodata_gender_female')],
            ]} />
            {sects.length > 0 && (
              <FilterSelect value={filters.sect ?? ''} onChange={v => applyFilter('sect', v)} options={[
                ['', t('admin', 'biodata_filter_sect')], ...sects.map(s => [s, s] as [string, string]),
              ]} />
            )}
            {tab === 'all' && (
              <FilterSelect value={filters.status ?? ''} onChange={v => applyFilter('status', v)} options={[
                ['', t('admin', 'biodata_status_filter')],
                ['pending', t('admin', 'biodata_tab_pending')], ['approved', t('admin', 'biodata_tab_approved')],
                ['rejected', t('admin', 'biodata_tab_rejected')], ['hidden', t('admin', 'biodata_tab_hidden')],
                ['draft', t('admin', 'biodata_status_draft')],
              ]} />
            )}
            <FilterSelect value={filters.min_completeness ?? ''} onChange={v => applyFilter('min_completeness', v)} options={[
              ['', t('admin', 'biodata_filter_completeness')], ['50', t('admin', 'biodata_completeness_50')], ['80', t('admin', 'biodata_completeness_80')],
            ]} />
            <FilterSelect value={filters.sort ?? ''} onChange={v => applyFilter('sort', v)} options={[
              ['', t('admin', 'biodata_sort_updated')], ['completeness', t('admin', 'biodata_sort_completeness')], ['newest', t('admin', 'biodata_sort_newest')],
            ]} />
            <label className="flex items-center gap-1 rounded-xl border border-slate-200 px-2 text-[11px] text-slate-500">
              {t('admin', 'biodata_filter_from')}
              <input type="date" value={filters.updated_from ?? ''} onChange={e => applyFilter('updated_from', e.target.value)}
                className="bg-transparent py-2 text-sm focus:outline-none" />
            </label>
            <label className="flex items-center gap-1 rounded-xl border border-slate-200 px-2 text-[11px] text-slate-500">
              {t('admin', 'biodata_filter_to')}
              <input type="date" value={filters.updated_to ?? ''} onChange={e => applyFilter('updated_to', e.target.value)}
                className="bg-transparent py-2 text-sm focus:outline-none" />
            </label>
            {filterActive && (
              <button onClick={resetFilters} className="inline-flex items-center gap-1 rounded-xl px-2.5 py-2 text-sm text-slate-500 hover:bg-slate-100">
                <RotateCcw size={13} /> {t('admin', 'biodata_reset_filters')}
              </button>
            )}
          </div>
        </div>

        {/* Bulk toolbar */}
        {selected.size > 0 && (
          <div className="flex flex-wrap items-center gap-2 rounded-xl border border-primary-200 bg-primary-50 px-3 py-2">
            <span className="text-sm font-medium text-primary-800">
              {t('admin', 'biodata_selected', { count: selected.size })}
            </span>
            <select value={bulkAction} onChange={e => setBulkAction(e.target.value)}
              className="rounded-lg border border-slate-200 px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
              <option value="">{t('admin', 'biodata_bulk_action')}</option>
              <option value="approve">{t('admin', 'biodata_bulk_approve')}</option>
              <option value="reject">{t('admin', 'biodata_bulk_reject')}</option>
              <option value="hide">{t('admin', 'biodata_bulk_hide')}</option>
              <option value="unhide">{t('admin', 'biodata_bulk_unhide')}</option>
            </select>
            <Button size="sm" onClick={runBulk} disabled={!bulkAction || busy} isLoading={busy}>{t('admin', 'biodata_bulk_apply')}</Button>
            <button onClick={clearSelection} className="text-sm text-slate-500 hover:text-slate-700">{t('admin', 'biodata_clear')}</button>
          </div>
        )}

        {/* Select-all + count */}
        {biodatas.data.length > 0 && (
          <div className="flex items-center justify-between px-1">
            <label className="flex items-center gap-2 text-xs text-slate-500">
              <input type="checkbox" checked={allSelected} onChange={toggleAll}
                className="rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
              {t('admin', 'biodata_select_all')}
            </label>
            <span className="text-xs text-slate-400">{biodatas.total}</span>
          </div>
        )}

        {/* List */}
        {navigating ? (
          <SkeletonList />
        ) : biodatas.data.length === 0 ? (
          <EmptyState filterActive={filterActive} onReset={resetFilters} />
        ) : (
          <div className="space-y-3">
            {biodatas.data.map(b => (
              <BiodataCard
                key={b.id} b={b} t={t}
                selected={selected.has(b.id)} onSelect={() => toggleOne(b.id)}
                onPreview={() => setPreviewId(b.id)}
                onApprove={() => setConfirm({ kind: 'approve', id: b.id })}
                onReject={() => setRejectFor(b.id)}
                onHide={() => setHideFor(b.id)}
                onUnhide={() => setConfirm({ kind: 'unhide', id: b.id })}
              />
            ))}
          </div>
        )}

        {/* Pagination */}
        {biodatas.last_page > 1 && (
          <div className="flex justify-center gap-2">
            {biodatas.current_page > 1 && (
              <Button size="sm" variant="outline" onClick={() => go({ ...filters, tab, page: biodatas.current_page - 1 })}>
                {t('common', 'previous')}
              </Button>
            )}
            <span className="flex items-center px-3 text-sm text-slate-500">{biodatas.current_page} / {biodatas.last_page}</span>
            {biodatas.current_page < biodatas.last_page && (
              <Button size="sm" variant="outline" onClick={() => go({ ...filters, tab, page: biodatas.current_page + 1 })}>
                {t('common', 'next')}
              </Button>
            )}
          </div>
        )}
      </div>

      {/* ── Modals ── */}
      {rejectFor !== null && (
        <ReasonModal
          title={t('admin', 'biodata_reject')} note={t('admin', 'rejection_note')}
          placeholder={t('admin', 'rejection_placeholder')} required
          confirmLabel={t('admin', 'biodata_reject')} variant="destructive"
          routeName="admin.biodatas.reject" id={rejectFor}
          onClose={() => setRejectFor(null)}
        />
      )}
      {hideFor !== null && (
        <ReasonModal
          title={t('admin', 'biodata_hide')} note={t('admin', 'biodata_hide_reason')}
          placeholder={t('admin', 'biodata_hide_reason_ph')}
          confirmLabel={t('admin', 'biodata_hide')} variant="default"
          routeName="admin.biodatas.hide" id={hideFor}
          onClose={() => setHideFor(null)}
        />
      )}
      {bulkReject && (
        <ReasonModal
          title={t('admin', 'biodata_bulk_reject')} note={t('admin', 'rejection_note')}
          placeholder={t('admin', 'rejection_placeholder')} required
          confirmLabel={t('admin', 'biodata_bulk_apply')} variant="destructive"
          busyExternal={busy}
          onSubmitNote={(note) => submitBulk('reject', note)}
          onClose={() => setBulkReject(false)}
        />
      )}

      <ConfirmDialog
        open={confirm?.kind === 'approve'} danger={false}
        title={t('admin', 'biodata_confirm_approve_title')} body={t('admin', 'biodata_confirm_approve_body')}
        confirmLabel={t('admin', 'biodata_approve')} cancelLabel={t('common', 'cancel')}
        onConfirm={() => confirm?.id && doApprove(confirm.id)} onClose={() => setConfirm(null)}
      />
      <ConfirmDialog
        open={confirm?.kind === 'unhide'} danger={false}
        title={t('admin', 'biodata_confirm_unhide_title')} body={t('admin', 'biodata_confirm_unhide_body')}
        confirmLabel={t('admin', 'biodata_unhide')} cancelLabel={t('common', 'cancel')}
        onConfirm={() => confirm?.id && doUnhide(confirm.id)} onClose={() => setConfirm(null)}
      />
      <ConfirmDialog
        open={confirm?.kind === 'bulk'}
        danger={confirm?.action === 'hide'}
        title={t('admin', 'biodata_confirm_bulk_title')} body={t('admin', 'biodata_confirm_bulk_body')}
        confirmLabel={t('admin', 'biodata_bulk_apply')} cancelLabel={t('common', 'cancel')} loading={busy}
        onConfirm={() => confirm?.action && submitBulk(confirm.action)} onClose={() => setConfirm(null)}
      />

      {previewId !== null && (
        <PreviewModal id={previewId} t={t}
          onClose={() => setPreviewId(null)}
          onApprove={() => { setPreviewId(null); setConfirm({ kind: 'approve', id: previewId }) }}
          onReject={() => { setPreviewId(null); setRejectFor(previewId) }}
        />
      )}
    </AdminLayout>
  )
}

// ── Summary card ───────────────────────────────────────────────────────────
const TONES: Record<string, { dot: string; icon: string; bg: string }> = {
  amber:   { dot: 'bg-amber-500',   icon: 'text-amber-600 bg-amber-50',   bg: 'border-amber-100' },
  emerald: { dot: 'bg-emerald-500', icon: 'text-emerald-600 bg-emerald-50', bg: 'border-emerald-100' },
  red:     { dot: 'bg-red-500',     icon: 'text-red-600 bg-red-50',       bg: 'border-red-100' },
  slate:   { dot: 'bg-slate-400',   icon: 'text-slate-600 bg-slate-100',  bg: 'border-slate-200' },
  primary: { dot: 'bg-primary-500', icon: 'text-primary-600 bg-primary-50', bg: 'border-primary-100' },
}
function SummaryCard({ label, value, icon: Icon, tone }: { label: string; value: number; icon: any; tone: string }) {
  const s = TONES[tone] ?? { dot: 'bg-slate-400', icon: 'text-slate-600 bg-slate-100', bg: 'border-slate-200' }
  return (
    <div className={cn('flex items-center gap-3 rounded-2xl border bg-white p-3.5', s.bg)}>
      <div className={cn('flex h-9 w-9 shrink-0 items-center justify-center rounded-xl', s.icon)}>
        <Icon size={17} />
      </div>
      <div className="min-w-0">
        <div className="flex items-center gap-1.5">
          <span className={cn('h-1.5 w-1.5 rounded-full', s.dot)} />
          <p className="truncate text-xs text-slate-500">{label}</p>
        </div>
        <p className="text-lg font-bold leading-tight text-slate-900">{value}</p>
      </div>
    </div>
  )
}

// ── Biodata review card ──────────────────────────────────────────────────────
function BiodataCard({ b, t, selected, onSelect, onPreview, onApprove, onReject, onHide, onUnhide }: {
  b: Biodata; t: any; selected: boolean; onSelect: () => void
  onPreview: () => void; onApprove: () => void; onReject: () => void; onHide: () => void; onUnhide: () => void
}) {
  const reg = b.registration
  const pct = b.completeness_score ?? 0
  const premium = reg?.membership_status === 'active'
  const location = [b.district, b.division].filter(Boolean).join(', ')

  return (
    <div className={cn(
      'rounded-2xl border bg-white p-4 transition-shadow hover:shadow-sm',
      selected ? 'border-primary-300 ring-1 ring-primary-200' : 'border-slate-200',
    )}>
      <div className="flex flex-col gap-4 md:flex-row md:items-center">
        {/* Left: select + avatar + identity */}
        <div className="flex min-w-0 flex-1 items-start gap-3">
          <input type="checkbox" checked={selected} onChange={onSelect}
            className="mt-1 rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
          <Avatar name={reg?.name ?? b.registration_id} gender={reg?.gender} />
          <div className="min-w-0">
            <div className="flex flex-wrap items-center gap-1.5">
              <p className="truncate font-semibold text-slate-900">{reg?.name ?? b.registration_id}</p>
              <GenderBadge gender={reg?.gender} mode={reg?.platform_mode} />
            </div>
            <p className="truncate text-xs text-slate-400">{reg?.email}</p>
            <div className="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[11px] text-slate-400">
              <span className="font-mono">{b.registration_id}</span>
              {location && <span className="inline-flex items-center gap-0.5"><MapPin size={11} />{location}</span>}
              {b.photos_count > 0 && <span className="inline-flex items-center gap-0.5"><ImageIcon size={11} />{b.photos_count} {t('admin', 'biodata_photos_label')}</span>}
              <span>{t('admin', 'biodata_updated_label')} {new Date(b.updated_at).toLocaleDateString('en-BD')}</span>
            </div>
          </div>
        </div>

        {/* Middle: completion + badges */}
        <div className="w-full shrink-0 space-y-2 md:w-56">
          <div className="flex items-center justify-between text-xs">
            <span className="text-slate-500">{t('admin', 'biodata_completeness')}</span>
            <span className="font-semibold text-slate-700">{pct}%</span>
          </div>
          <div className="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
            <div className={cn('h-full rounded-full transition-all', completionColor(pct))} style={{ width: `${Math.min(100, Math.max(0, pct))}%` }} />
          </div>
          <div className="flex flex-wrap items-center gap-1.5 pt-1">
            <StatusBadge status={b.status} t={t} />
            {premium
              ? <span className="inline-flex items-center gap-1 rounded-full bg-violet-100 px-2 py-0.5 text-[11px] font-medium text-violet-700"><Crown size={10} />{reg?.membership_plan_name || t('admin', 'biodata_plan_premium')}</span>
              : <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-500">{t('admin', 'biodata_plan_free')}</span>}
          </div>
          <div className="flex flex-wrap gap-1.5">
            <VerifyPill on={!!reg?.is_email_verified} icon={Mail} okLabel={t('admin', 'biodata_email_ok')} noLabel={t('admin', 'biodata_email_no')} />
            <VerifyPill on={!!reg?.is_mobile_verified} icon={Phone} okLabel={t('admin', 'biodata_phone_ok')} noLabel={t('admin', 'biodata_phone_no')} />
          </div>
        </div>

        {/* Right: actions */}
        <div className="flex shrink-0 flex-wrap gap-2 md:flex-col md:items-stretch md:justify-center">
          <Button size="sm" variant="outline" className="gap-1" onClick={onPreview}>
            <FileSearch size={13} /> {t('admin', 'biodata_view')}
          </Button>
          {(b.status === 'pending' || b.status === 'rejected') && (
            <Button size="sm" className="gap-1 bg-emerald-600 hover:bg-emerald-700" onClick={onApprove}>
              <CheckCircle size={13} /> {t('admin', 'biodata_approve')}
            </Button>
          )}
          {(b.status === 'pending' || b.status === 'approved') && (
            <Button size="sm" variant="destructive" className="gap-1" onClick={onReject}>
              <XCircle size={13} /> {t('admin', 'biodata_reject')}
            </Button>
          )}
          {b.status === 'approved' && (
            <Button size="sm" variant="outline" className="gap-1" onClick={onHide}>
              <EyeOff size={13} /> {t('admin', 'biodata_hide')}
            </Button>
          )}
          {b.status === 'hidden' && (
            <Button size="sm" variant="outline" className="gap-1" onClick={onUnhide}>
              <Eye size={13} /> {t('admin', 'biodata_unhide')}
            </Button>
          )}
          <Link href={route('admin.biodatas.show', b.id)}>
            <Button size="sm" variant="ghost" className="w-full gap-1 text-slate-500">
              {t('admin', 'biodata_manage')} <ChevronRight size={13} />
            </Button>
          </Link>
        </div>
      </div>

      {b.admin_note && (
        <p className="mt-3 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-600">
          <strong>{t('admin', 'biodata_note_label')}</strong> {b.admin_note}
        </p>
      )}
    </div>
  )
}

// ── Small presentational bits ─────────────────────────────────────────────────
function Avatar({ name, gender }: { name: string; gender?: string }) {
  const initial = (name?.trim()?.charAt(0) || '?').toUpperCase()
  const tone = gender === 'female' ? 'bg-rose-100 text-rose-600' : 'bg-sky-100 text-sky-600'
  return (
    <div className={cn('flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-base font-bold', tone)}>
      {initial}
    </div>
  )
}

function GenderBadge({ gender, mode }: { gender?: string; mode?: string }) {
  if (!gender) return null
  return (
    <span className="inline-flex items-center gap-1 rounded-full bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium capitalize text-slate-500">
      {gender}{mode ? ` · ${mode}` : ''}
    </span>
  )
}

function StatusBadge({ status, t }: { status: string; t: any }) {
  const label = t('admin', `biodata_tab_${status}`) !== `biodata_tab_${status}`
    ? t('admin', `biodata_tab_${status}`)
    : (status === 'draft' ? t('admin', 'biodata_status_draft') : status)
  return (
    <span className={cn('inline-block rounded-full px-2 py-0.5 text-[11px] font-semibold capitalize', STATUS_STYLES[status] ?? STATUS_STYLES.draft)}>
      {label}
    </span>
  )
}

function VerifyPill({ on, icon: Icon, okLabel, noLabel }: { on: boolean; icon: any; okLabel: string; noLabel: string }) {
  return (
    <span title={on ? okLabel : noLabel}
      className={cn('inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[10px] font-medium',
        on ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400')}>
      <Icon size={10} />{on ? <CheckCircle size={9} /> : <XCircle size={9} />}
    </span>
  )
}

function FilterSelect({ value, onChange, options }: { value: string; onChange: (v: string) => void; options: [string, string][] }) {
  return (
    <select value={value} onChange={e => onChange(e.target.value)}
      className="rounded-xl border border-slate-200 px-2.5 py-2 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-primary-500">
      {options.map(([v, label]) => <option key={v} value={v}>{label}</option>)}
    </select>
  )
}

function EmptyState({ filterActive, onReset }: { filterActive: boolean; onReset: () => void }) {
  const { t } = useTranslation()
  return (
    <div className="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center">
      <div className="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
        <Inbox size={26} />
      </div>
      <p className="font-medium text-slate-600">{t('admin', 'biodata_empty_title')}</p>
      <p className="mt-1 text-sm text-slate-400">{t('admin', 'biodata_empty_hint')}</p>
      {filterActive && (
        <Button size="sm" variant="outline" className="mt-4 gap-1" onClick={onReset}>
          <RotateCcw size={13} /> {t('admin', 'biodata_reset_filters')}
        </Button>
      )}
    </div>
  )
}

function SkeletonList() {
  return (
    <div className="space-y-3">
      {[0, 1, 2, 3].map(i => (
        <div key={i} className="rounded-2xl border border-slate-200 bg-white p-4">
          <div className="flex items-center gap-3">
            <div className="h-11 w-11 shrink-0 animate-pulse rounded-xl bg-slate-100" />
            <div className="flex-1 space-y-2">
              <div className="h-3.5 w-40 animate-pulse rounded bg-slate-100" />
              <div className="h-3 w-56 animate-pulse rounded bg-slate-100" />
            </div>
            <div className="hidden h-8 w-48 animate-pulse rounded bg-slate-100 md:block" />
          </div>
        </div>
      ))}
    </div>
  )
}

// ── Reason modal (reject + hide + bulk reject) ────────────────────────────────
function ReasonModal({ title, note, placeholder, required = false, confirmLabel, variant, routeName, id, onSubmitNote, busyExternal, onClose }: {
  title: string; note: string; placeholder: string; required?: boolean
  confirmLabel: string; variant: 'destructive' | 'default'
  routeName?: string; id?: number
  onSubmitNote?: (note: string) => void; busyExternal?: boolean
  onClose: () => void
}) {
  const { t } = useTranslation()
  const { data, setData, post, processing, errors, reset } = useForm({ note: '' })

  function submit(e: React.FormEvent) {
    e.preventDefault()
    if (onSubmitNote) { onSubmitNote(data.note); return }
    if (routeName && id != null) {
      post(route(routeName, id), { preserveScroll: true, onSuccess: () => { reset(); onClose() } })
    }
  }

  const tooShort = required && data.note.trim().length < 5
  const loading = processing || !!busyExternal

  return (
    <div className="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div className="absolute inset-0 bg-black/40" onClick={onClose} />
      <div className="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <button onClick={onClose} className="absolute right-3 top-3 text-slate-400 hover:text-slate-600" aria-label="Close"><X size={18} /></button>
        <h3 className="mb-1 font-bold text-slate-900">{title}</h3>
        <p className="mb-3 text-sm text-slate-500">{note}</p>
        <form onSubmit={submit}>
          <textarea
            value={data.note} onChange={e => setData('note', e.target.value)} rows={3} placeholder={placeholder}
            className={cn('w-full resize-none rounded-xl border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500',
              errors.note ? 'border-red-400' : 'border-slate-200')}
          />
          {errors.note && <p className="mt-1 text-xs text-red-600">{errors.note}</p>}
          <div className="mt-4 flex gap-3">
            <Button type="button" variant="outline" size="sm" onClick={onClose} className="flex-1" disabled={loading}>
              {t('common', 'cancel')}
            </Button>
            <Button type="submit" variant={variant} size="sm" isLoading={loading} disabled={tooShort} className="flex-1">
              {confirmLabel}
            </Button>
          </div>
        </form>
      </div>
    </div>
  )
}

// ── Quick preview modal ───────────────────────────────────────────────────────
function PreviewModal({ id, t, onClose, onApprove, onReject }: {
  id: number; t: any; onClose: () => void; onApprove: () => void; onReject: () => void
}) {
  const [data, setData] = useState<any>(null)
  const [loading, setLoading] = useState(true)
  const closeRef = useRef(onClose)
  closeRef.current = onClose

  useEffect(() => {
    let active = true
    setLoading(true)
    axios.get(route('admin.biodatas.preview', id))
      .then(res => { if (active) setData(res.data) })
      .catch(() => { if (active) setData(null) })
      .finally(() => { if (active) setLoading(false) })
    return () => { active = false }
  }, [id])

  useEffect(() => {
    const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') closeRef.current() }
    document.addEventListener('keydown', onKey)
    return () => document.removeEventListener('keydown', onKey)
  }, [])

  const m = data?.member
  const yesNo = (v: boolean | null | undefined) => v === true ? t('common', 'yes') : v === false ? t('common', 'no') : null
  const status = data?.status

  return (
    <div className="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4">
      <div className="absolute inset-0 bg-black/40" onClick={onClose} />
      <div className="relative flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-2xl bg-white shadow-xl sm:rounded-2xl">
        {/* Header */}
        <div className="flex items-start justify-between gap-3 border-b border-slate-100 px-5 py-4">
          <div className="min-w-0">
            <h3 className="font-bold text-slate-900">{t('admin', 'biodata_preview_title')}</h3>
            {m && <p className="truncate text-sm text-slate-500">{m.name} · <span className="font-mono text-xs">{data.registration_id}</span></p>}
          </div>
          <button onClick={onClose} className="text-slate-400 hover:text-slate-600" aria-label="Close"><X size={20} /></button>
        </div>

        {/* Body */}
        <div className="flex-1 overflow-y-auto px-5 py-4">
          {loading ? (
            <p className="py-10 text-center text-sm text-slate-400">{t('admin', 'biodata_preview_loading')}</p>
          ) : !data ? (
            <p className="py-10 text-center text-sm text-slate-400">—</p>
          ) : (
            <div className="space-y-5">
              {/* Member chips */}
              {m && (
                <div className="flex flex-wrap items-center gap-2">
                  {status && <span className={cn('rounded-full px-2 py-0.5 text-[11px] font-semibold capitalize', STATUS_STYLES[status] ?? STATUS_STYLES.draft)}>{status}</span>}
                  {data.completeness_score != null && <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">{data.completeness_score}%</span>}
                  <span className={cn('rounded-full px-2 py-0.5 text-[11px]', m.is_email_verified ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400')}>{m.is_email_verified ? t('admin', 'biodata_email_ok') : t('admin', 'biodata_email_no')}</span>
                  <span className={cn('rounded-full px-2 py-0.5 text-[11px]', m.is_mobile_verified ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400')}>{m.is_mobile_verified ? t('admin', 'biodata_phone_ok') : t('admin', 'biodata_phone_no')}</span>
                  {data.photos_count > 0 && <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">{data.photos_count} {t('admin', 'biodata_photos_label')}</span>}
                </div>
              )}

              <PreviewSection title={t('admin', 'biodata_sec_basic')} rows={[
                [t('dashboard', 'profile_label_age'), data.basic.age],
                [t('dashboard', 'profile_label_marital'), data.basic.marital_status],
                [t('dashboard', 'profile_label_height'), data.basic.height_cm ? `${data.basic.height_cm} cm` : null],
                [t('dashboard', 'profile_label_complexion'), data.basic.complexion],
                [t('dashboard', 'profile_label_blood'), data.basic.blood_group],
                [t('admin', 'biodata_label_district'), [data.location.district, data.location.division].filter(Boolean).join(', ') || null],
              ]} />
              {data.basic.about_me && (
                <div>
                  <p className="text-xs font-medium text-slate-500">{t('admin', 'biodata_label_about')}</p>
                  <p className="mt-0.5 whitespace-pre-line text-sm text-slate-800">{data.basic.about_me}</p>
                </div>
              )}

              <PreviewSection title={t('admin', 'biodata_sec_religion')} rows={[
                [t('dashboard', 'profile_label_religion'), data.religion.religion],
                [t('dashboard', 'profile_label_sect'), data.religion.sect],
                [t('dashboard', 'profile_label_prayers'), data.religion.prayers_info],
                [t('dashboard', 'profile_label_islam_edu'), yesNo(data.religion.is_islamically_educated)],
              ]} />

              <PreviewSection title={t('admin', 'biodata_sec_education')} rows={[
                [t('dashboard', 'profile_label_qual'), data.education.highest_qualification],
                [t('dashboard', 'profile_label_occupation'), data.education.occupation],
                [t('dashboard', 'profile_label_income'), data.education.monthly_income ? `${Number(data.education.monthly_income).toLocaleString()} BDT` : null],
              ]} />

              <PreviewSection title={t('admin', 'biodata_sec_family')} rows={[
                [t('admin', 'biodata_label_father'), yesNo(data.family.father_alive)],
                [t('admin', 'biodata_label_mother'), yesNo(data.family.mother_alive)],
                [t('dashboard', 'profile_label_brothers'), data.family.brothers],
                [t('dashboard', 'profile_label_sisters'), data.family.sisters],
                [t('dashboard', 'profile_label_family_type'), data.family.family_type],
                [t('admin', 'biodata_label_family_status'), data.family.family_financial_status],
              ]} />

              <PreviewSection title={t('admin', 'biodata_sec_partner')} rows={[
                [t('dashboard', 'profile_label_age_range'), (data.partner.age_min || data.partner.age_max) ? `${data.partner.age_min ?? '?'} – ${data.partner.age_max ?? '?'}` : null],
                [t('dashboard', 'profile_label_sect'), data.partner.sect],
                [t('dashboard', 'profile_label_qual'), data.partner.education],
              ]} />
              {data.partner.expectations && (
                <div>
                  <p className="text-xs font-medium text-slate-500">{t('admin', 'biodata_label_expectations')}</p>
                  <p className="mt-0.5 whitespace-pre-line text-sm text-slate-800">{data.partner.expectations}</p>
                </div>
              )}

              <PreviewSection title={t('admin', 'biodata_sec_contact')} rows={[
                [t('admin', 'biodata_label_guardian_rel'), data.contact.guardian_relationship],
                [t('admin', 'biodata_label_guardian_mobile'), data.contact.guardian_mobile],
                [t('biodata', 'whatsapp_number'), data.contact.whatsapp_number],
                [t('biodata', 'contact_privacy'), data.contact.contact_privacy],
              ]} />
            </div>
          )}
        </div>

        {/* Footer actions */}
        <div className="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 px-5 py-3">
          <Link href={route('admin.biodatas.show', id)} className="inline-flex items-center gap-1 text-sm text-primary-600 hover:underline">
            <ExternalLink size={14} /> {t('admin', 'biodata_preview_full')}
          </Link>
          <div className="flex gap-2">
            {(status === 'pending' || status === 'rejected') && (
              <Button size="sm" className="gap-1 bg-emerald-600 hover:bg-emerald-700" onClick={onApprove}>
                <CheckCircle size={14} /> {t('admin', 'biodata_approve')}
              </Button>
            )}
            {(status === 'pending' || status === 'approved') && (
              <Button size="sm" variant="destructive" className="gap-1" onClick={onReject}>
                <XCircle size={14} /> {t('admin', 'biodata_reject')}
              </Button>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}

function PreviewSection({ title, rows }: { title: string; rows: [string, React.ReactNode][] }) {
  const visible = rows.filter(([, v]) => v !== null && v !== undefined && v !== '')
  if (visible.length === 0) return null
  return (
    <div>
      <h4 className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">{title}</h4>
      <dl className="grid grid-cols-2 gap-3 sm:grid-cols-3">
        {visible.map(([label, value], i) => (
          <div key={i}>
            <dt className="text-[11px] text-slate-400">{label}</dt>
            <dd className="text-sm capitalize text-slate-800">{value}</dd>
          </div>
        ))}
      </dl>
    </div>
  )
}
