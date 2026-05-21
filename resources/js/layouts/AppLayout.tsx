import { usePage, Link } from '@inertiajs/react'
import { useState } from 'react'
import type { PageProps } from '@/types'
import {
  Home, Search, Heart, MessageCircle, Star, Bell,
  User, Settings, LogOut, Menu, X, ChevronDown,
  Shield, Sparkles, Crown,
} from 'lucide-react'
import { cn } from '@/lib/utils'

const NAV_ITEMS = [
  { label: 'Dashboard',    href: '/dashboard',    icon: Home },
  { label: 'Matches',      href: '/matches',       icon: Sparkles },
  { label: 'Search',       href: '/search',        icon: Search },
  { label: 'Interests',    href: '/interests',     icon: Heart },
  { label: 'Messages',     href: '/inbox',         icon: MessageCircle },
  { label: 'Shortlist',    href: '/shortlist',     icon: Star },
  { label: 'Notifications',href: '/notifications', icon: Bell },
]

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
  const { auth, flash } = usePage<PageProps>().props
  const user = auth.user!
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const currentPath = (typeof window !== 'undefined') ? window.location.pathname : ''

  return (
    <div className="min-h-screen bg-slate-50 flex">
      {/* ── Mobile sidebar backdrop ── */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 z-40 bg-black/40 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* ── Sidebar ── */}
      <aside
        className={cn(
          'fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out',
          'lg:translate-x-0 lg:static lg:z-auto',
          sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        )}
      >
        {/* Logo */}
        <div className="flex h-16 items-center gap-3 px-5 border-b border-slate-100">
          <div className="h-8 w-8 rounded-xl bg-gradient-to-br from-primary-600 to-violet-600 flex items-center justify-center">
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
          {NAV_ITEMS.map(({ label, href, icon: Icon }) => {
            const active = currentPath === href || currentPath.startsWith(href + '/')
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
                <Icon size={18} className={active ? 'text-primary-600' : 'text-slate-400'} />
                {label}
              </Link>
            )
          })}
        </nav>

        {/* User section */}
        <div className="border-t border-slate-100 p-3">
          {user.platform_mode === 'islamic' && (
            <div className="mb-2 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-700 flex items-center gap-2">
              <Shield size={13} />
              Islamic / Halal Mode
            </div>
          )}
          <div className="flex items-center gap-3 rounded-xl px-3 py-2">
            <div className="h-9 w-9 rounded-full bg-slate-200 flex items-center justify-center shrink-0">
              <User size={18} className="text-slate-500" />
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-sm font-semibold text-slate-900 truncate">{user.name}</p>
              <div className="flex items-center gap-1">
                <p className="text-xs text-slate-400 truncate">{user.registration_id}</p>
                <MembershipBadge tier={user.membership_plan} />
              </div>
            </div>
          </div>
          <div className="mt-1 grid grid-cols-2 gap-1">
            <Link
              href="/settings"
              className="flex items-center gap-2 rounded-lg px-3 py-2 text-xs text-slate-600 hover:bg-slate-100"
            >
              <Settings size={14} />
              Settings
            </Link>
            <Link
              href="/logout"
              method="post"
              as="button"
              className="flex items-center gap-2 rounded-lg px-3 py-2 text-xs text-red-500 hover:bg-red-50"
            >
              <LogOut size={14} />
              Logout
            </Link>
          </div>
        </div>
      </aside>

      {/* ── Main content ── */}
      <div className="flex-1 flex flex-col min-w-0">
        {/* Top bar (mobile) */}
        <header className="sticky top-0 z-30 flex h-14 items-center gap-3 border-b border-slate-200 bg-white px-4 lg:hidden">
          <button
            onClick={() => setSidebarOpen(true)}
            className="text-slate-500 hover:text-slate-700"
          >
            <Menu size={20} />
          </button>
          <span className="font-bold text-slate-900">HeavenlyMatch</span>
          <div className="ml-auto flex items-center gap-2">
            <Link href="/notifications" className="text-slate-500 hover:text-slate-700">
              <Bell size={20} />
            </Link>
          </div>
        </header>

        {/* Flash messages */}
        {(flash.success || flash.error || flash.info) && (
          <div className="px-4 pt-4">
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
            {flash.info && (
              <div className="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-800">
                ℹ {flash.info}
              </div>
            )}
          </div>
        )}

        <main className="flex-1 p-4 lg:p-6">
          {children}
        </main>
      </div>
    </div>
  )
}
