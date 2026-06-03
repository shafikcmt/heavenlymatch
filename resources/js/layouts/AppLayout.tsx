/// <reference path="../types/ziggy.d.ts" />
import { usePage, Link } from '@inertiajs/react'
import { useState } from 'react'
import type { PageProps } from '@/types'
import {
  Home, Search, Heart, MessageCircle, Star, Bell,
  User, Settings, LogOut, Menu, X,
  Shield, Sparkles, Crown, TrendingUp, Headphones, FileEdit,
} from 'lucide-react'
import { cn } from '@/lib/utils'
import LanguageSwitcher from '@/components/LanguageSwitcher'
import { useTranslation } from '@/lib/i18n'
import { MobileBottomNav } from '@/components/mobile/MobileBottomNav'
import { MobileTabs, type MobileTab } from '@/components/mobile/MobileTabs'
import UserMenu from '@/components/UserMenu'

// Determine which tab key is active from the current path
function getActiveTab(path: string): string {
  if (path.startsWith('/matches'))             return 'matches'
  if (path.startsWith('/search'))              return 'search'
  if (path.startsWith('/shortlist'))           return 'shortlist'
  if (path.startsWith('/interests/received'))  return 'mutual'
  if (path.startsWith('/profile/who-viewed'))  return 'viewed'
  if (path.startsWith('/dashboard'))           return 'dashboard'
  return 'dashboard'
}

const MATCH_TABS: MobileTab[] = [
  { key: 'dashboard', label: 'Dashboard',       href: '/dashboard' },
  { key: 'search',    label: 'Just Joined',     href: '/search?sort=newest' },
  { key: 'matches',   label: 'Matches',         href: '/matches' },
  { key: 'shortlist', label: 'Shortlisted',     href: '/shortlist' },
  { key: 'mutual',    label: 'Mutual',          href: '/interests/received' },
  { key: 'viewed',    label: 'Viewed My Profile', href: '/profile/who-viewed' },
]

// Pages that show the match tabs row
const TAB_PATHS = ['/dashboard', '/matches', '/search', '/shortlist', '/interests/received', '/profile/who-viewed']

// Map the current path to a `common` translation key for the desktop header title
function getPageTitleKey(path: string): string {
  if (path.startsWith('/matches'))       return 'matches'
  if (path.startsWith('/search'))        return 'search'
  if (path.startsWith('/interests'))     return 'interests'
  if (path.startsWith('/inbox'))         return 'inbox'
  if (path.startsWith('/shortlist'))     return 'shortlist'
  if (path.startsWith('/notifications')) return 'notifications'
  if (path.startsWith('/settings'))      return 'settings'
  if (path.startsWith('/profile') || path.startsWith('/dashboard/profile')) return 'profile'
  return 'dashboard'
}

function MembershipBadge({ tier }: { tier: string | null }) {
  if (!tier || tier === 'free') return null
  const map: Record<string, { label: string; className: string }> = {
    gold:    { label: '★ Gold',    className: 'bg-amber-100 text-amber-800' },
    diamond: { label: '💎 Diamond', className: 'bg-blue-100 text-blue-800' },
    silver:  { label: '◆ Silver',  className: 'bg-slate-100 text-slate-700' },
  }
  const badge = map[tier.toLowerCase()] ?? map['silver']!
  return (
    <span className={cn('rounded-full px-2 py-0.5 text-xs font-bold', badge.className)}>
      {badge.label}
    </span>
  )
}

export default function AppLayout({ children }: { children: React.ReactNode }) {
  const { auth, flash, completion, access, unread_notifications } = usePage<PageProps>().props
  const { t } = useTranslation()
  const user = auth.user!
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const currentPath = typeof window !== 'undefined' ? window.location.pathname : ''
  const activeTab = getActiveTab(currentPath)
  const pageTitle = t('common', getPageTitleKey(currentPath))

  // Matching features unlock only once the biodata is approved.
  const fullAccess = access ? access.can_access_matches : true
  // Match-list tabs + notification bell are only meaningful for approved users.
  const showTabs = fullAccess && TAB_PATHS.some(p => currentPath.startsWith(p))
  const showBell = fullAccess

  // Conditional sidebar: before approval show only Dashboard + Biodata
  // (Settings + Logout live in the footer and stay visible in every state).
  const biodataItem = {
    label: access && !access.has_biodata ? t('dashboard', 'complete_biodata') : t('common', 'edit_biodata'),
    href: '/biodata/wizard',
    icon: FileEdit,
  }
  const navItems = fullAccess
    ? [
        { label: t('common', 'dashboard'),     href: '/dashboard',          icon: Home },
        { label: t('common', 'matches'),       href: '/matches',            icon: Sparkles },
        { label: t('common', 'search'),        href: '/search',             icon: Search },
        { label: t('common', 'interests'),     href: '/interests/received', icon: Heart },
        { label: t('common', 'inbox'),         href: '/inbox',              icon: MessageCircle },
        { label: t('common', 'shortlist'),     href: '/shortlist',          icon: Star },
        { label: t('common', 'notifications'), href: '/notifications',      icon: Bell },
      ]
    : [
        { label: t('common', 'dashboard'), href: '/dashboard', icon: Home },
        biodataItem,
      ]

  return (
    <div className="min-h-screen bg-slate-50 flex">
      {/* ── Mobile sidebar backdrop ── */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 z-40 bg-black/40 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* ── Sidebar (desktop + mobile drawer) ── */}
      <aside
        className={cn(
          'fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out',
          'lg:translate-x-0 lg:static lg:z-auto',
          sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        )}
      >
        {/* Logo */}
        <div className="flex h-16 items-center gap-3 px-5 border-b border-slate-100">
          <div className="h-8 w-8 rounded-xl bg-primary-700 flex items-center justify-center">
            <Crown size={16} className="text-white" />
          </div>
          <span className="font-bold text-slate-900 tracking-tight">HeavenlyMatch</span>
          <button
            className="ml-auto lg:hidden text-slate-400 hover:text-slate-600"
            onClick={() => setSidebarOpen(false)}
          >
            <X size={18} />
          </button>
        </div>

        {/* Nav items */}
        <nav className="flex-1 overflow-y-auto py-4 px-3 space-y-1">
          {navItems.map(({ label, href, icon: Icon }) => {
            const active = currentPath === href || currentPath.startsWith(href + '/')
            const isBell = href === '/notifications'
            const badge = isBell && unread_notifications > 0 ? unread_notifications : 0
            return (
              <Link
                key={href}
                href={href}
                className={cn(
                  'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors',
                  active
                    ? 'bg-primary-50 text-primary-700'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                )}
              >
                <span className="relative">
                  <Icon size={18} className={active ? 'text-primary-700' : 'text-slate-400'} />
                  {badge > 0 && (
                    <span className="absolute -top-1.5 -right-1.5 h-4 w-4 rounded-full bg-red-500 text-[10px] font-bold text-white flex items-center justify-center">
                      {badge > 9 ? '9+' : badge}
                    </span>
                  )}
                </span>
                {label}
              </Link>
            )
          })}
        </nav>

        {/* User section — sticky footer; Logout always stays in view (no scroll) */}
        <div className="border-t border-slate-100 p-3 space-y-2">
          {user.platform_mode === 'islamic' && (
            <div className="rounded-xl bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-700 flex items-center gap-2">
              <Shield size={13} />
              {t('common', 'mode_islamic')}
            </div>
          )}

          {/* Profile completion mini-bar */}
          {completion && completion.percentage < 100 && (
            <Link href={completion.next_step_url} className="block rounded-xl bg-amber-50 border border-amber-200 px-3 py-2 hover:bg-amber-100 transition-colors">
              <div className="flex items-center justify-between mb-1">
                <span className="text-xs font-medium text-amber-800 flex items-center gap-1">
                  <TrendingUp size={11} />
                  {t('dashboard', 'profile_pct', { n: completion.percentage })}
                </span>
                <span className="text-xs text-amber-600">{t('dashboard', 'profile_complete_cta')}</span>
              </div>
              <div className="h-1.5 rounded-full bg-amber-200 overflow-hidden">
                <div
                  className="h-full rounded-full bg-amber-500 transition-all"
                  style={{ width: `${completion.percentage}%` }}
                />
              </div>
            </Link>
          )}

          {/* User identity */}
          <Link
            href={route('dashboard.profile')}
            className="flex items-center gap-3 rounded-xl px-2 py-2 hover:bg-slate-100 transition-colors"
          >
            <div className="h-9 w-9 rounded-full bg-primary-100 flex items-center justify-center shrink-0">
              <User size={18} className="text-primary-700" />
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-sm font-semibold text-slate-900 truncate">{user.name}</p>
              <div className="flex items-center gap-1">
                <p className="text-xs text-slate-400 truncate">{user.registration_id}</p>
                <MembershipBadge tier={user.membership_plan} />
              </div>
            </div>
          </Link>

          <div className="px-1">
            <LanguageSwitcher className="w-full justify-center" />
          </div>

          {/* Settings + Logout — Logout is the last, always-visible action */}
          <div className="grid grid-cols-2 gap-1.5 pt-1">
            <Link
              href={route('settings.index')}
              className="flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors"
            >
              <Settings size={14} />
              {t('common', 'settings')}
            </Link>
            <Link
              href={route('logout')}
              method="post"
              as="button"
              className="flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 transition-colors"
            >
              <LogOut size={14} />
              {t('common', 'logout')}
            </Link>
          </div>
        </div>
      </aside>

      {/* ── Main content ── */}
      <div className="flex-1 flex flex-col min-w-0">

        {/* ── Desktop header (sticky, with account dropdown) ── */}
        <header className="hidden lg:flex sticky top-0 z-30 h-16 items-center gap-4 border-b border-slate-200 bg-white/95 backdrop-blur px-6">
          <h1 className="text-lg font-bold text-slate-900 tracking-tight">{pageTitle}</h1>

          <div className="ml-auto flex items-center gap-2">
            {showBell && (
              <Link
                href="/notifications"
                aria-label={t('common', 'notifications')}
                className="relative rounded-full p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors"
              >
                <Bell size={20} />
                {unread_notifications > 0 && (
                  <span className="absolute top-0.5 right-0.5 h-4 w-4 rounded-full bg-red-500 text-[10px] font-bold text-white flex items-center justify-center">
                    {unread_notifications > 9 ? '9+' : unread_notifications}
                  </span>
                )}
              </Link>
            )}
            <LanguageSwitcher />
            <div className="h-6 w-px bg-slate-200" />
            <UserMenu />
          </div>
        </header>

        {/* ── Mobile header (green brand) ── */}
        <header className="sticky top-0 z-30 lg:hidden">
          {/* Top bar */}
          <div className="flex h-14 items-center gap-3 bg-primary-700 px-4">
            {/* Hamburger for sidebar (mobile) */}
            <button
              onClick={() => setSidebarOpen(true)}
              aria-label="Open menu"
              className="text-white/80 hover:text-white mr-1"
            >
              <Menu size={22} />
            </button>

            <span className="font-bold text-white text-[17px] tracking-widest uppercase flex-1">
              HOME
            </span>

            <div className="flex items-center gap-3">
              <LanguageSwitcher inverted />

              {showBell && (
                <Link
                  href="/notifications"
                  aria-label={t('common', 'notifications')}
                  className="relative text-white/80 hover:text-white"
                >
                  <Bell size={22} />
                  {unread_notifications > 0 && (
                    <span className="absolute -top-1.5 -right-1.5 h-4 w-4 rounded-full bg-red-500 text-[10px] font-bold text-white flex items-center justify-center">
                      {unread_notifications > 9 ? '9+' : unread_notifications}
                    </span>
                  )}
                </Link>
              )}

              <button
                aria-label="Support"
                className="text-white/80 hover:text-white"
              >
                <Headphones size={22} />
              </button>
            </div>
          </div>

          {/* Scrollable tabs — only on match-list pages */}
          {showTabs && (
            <MobileTabs tabs={MATCH_TABS} activeKey={activeTab} />
          )}
        </header>

        {/* Flash messages */}
        {(flash.success || flash.error || flash.warning || flash.info) && (
          <div className="px-4 pt-4 space-y-2">
            {flash.success && (
              <div className="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
                ✓ {flash.success}
              </div>
            )}
            {flash.error && (
              <div className="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                ✕ {flash.error}
              </div>
            )}
            {flash.warning && (
              <div className="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">
                ⚠ {flash.warning}
              </div>
            )}
            {flash.info && (
              <div className="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-800">
                ℹ {flash.info}
              </div>
            )}
          </div>
        )}

        {/* Page content — extra bottom padding on mobile for bottom nav */}
        <main className="flex-1 p-4 lg:p-6 pb-24 lg:pb-6">
          {children}
        </main>

        {/* Mobile bottom navigation */}
        <MobileBottomNav />
      </div>
    </div>
  )
}
