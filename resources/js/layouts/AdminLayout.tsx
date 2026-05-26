/// <reference path="../types/ziggy.d.ts" />
import { Link, usePage } from '@inertiajs/react'
import { useState } from 'react'
import type { PageProps } from '@/types'
import {
  LayoutDashboard, Users, FileText, CreditCard,
  Flag, Settings, Menu, X, Crown, LogOut, ExternalLink,
} from 'lucide-react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher'

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  const { auth, flash } = usePage<PageProps>().props
  const { t } = useTranslation()
  const user = auth.user!
  const [open, setOpen] = useState(false)
  const path = typeof window !== 'undefined' ? window.location.pathname : ''

  const NAV = [
    { label: t('admin', 'nav_dashboard'), href: route('admin.dashboard'), match: '/admin',         icon: LayoutDashboard },
    { label: t('admin', 'nav_users'),     href: route('admin.users.index'),    match: '/admin/users',    icon: Users },
    { label: t('admin', 'nav_biodatas'),  href: route('admin.biodatas.index'), match: '/admin/biodatas', icon: FileText },
    { label: t('admin', 'nav_payments'),  href: route('admin.payments.index'), match: '/admin/payments', icon: CreditCard },
    { label: t('admin', 'nav_reports'),   href: route('admin.reports.index'),  match: '/admin/reports',  icon: Flag },
    { label: t('admin', 'nav_settings'),  href: route('admin.settings.index'), match: '/admin/settings', icon: Settings },
  ]

  return (
    <div className="min-h-screen bg-slate-100 flex">
      {open && (
        <div className="fixed inset-0 z-40 bg-black/40 lg:hidden" onClick={() => setOpen(false)} />
      )}

      {/* Sidebar */}
      <aside className={cn(
        'fixed inset-y-0 left-0 z-50 flex w-60 flex-col bg-slate-900 transition-transform duration-300',
        'lg:translate-x-0 lg:static lg:z-auto',
        open ? 'translate-x-0' : '-translate-x-full',
      )}>
        <div className="flex h-14 items-center gap-3 px-4 border-b border-slate-800">
          <div className="h-7 w-7 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
            <Crown size={14} className="text-white" />
          </div>
          <span className="font-bold text-white text-sm">HM Admin</span>
          <button className="ml-auto lg:hidden text-slate-400" onClick={() => setOpen(false)}>
            <X size={16} />
          </button>
        </div>

        <nav className="flex-1 py-4 px-2 space-y-0.5 overflow-y-auto">
          {NAV.map(({ label, href, match, icon: Icon }) => {
            const active = path === match || (match !== '/admin' && path.startsWith(match))
            return (
              <Link
                key={href}
                href={href}
                className={cn(
                  'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                  active
                    ? 'bg-amber-500 text-white'
                    : 'text-slate-300 hover:bg-slate-800 hover:text-white',
                )}
              >
                <Icon size={16} />
                {label}
              </Link>
            )
          })}
        </nav>

        <div className="border-t border-slate-800 p-3">
          <p className="text-xs text-slate-400 px-2 mb-2 truncate">{user.name}</p>
          <div className="mb-2 px-1">
            <LanguageSwitcher className="w-full justify-center" dark />
          </div>
          <div className="flex gap-1">
            <Link
              href="/"
              className="flex-1 flex items-center justify-center gap-1.5 rounded-lg px-2 py-2 text-xs text-slate-400 hover:bg-slate-800 hover:text-white transition-colors"
            >
              <ExternalLink size={12} />
              {t('admin', 'back_to_site')}
            </Link>
            <Link
              href={route('admin.logout')}
              method="post"
              as="button"
              className="flex items-center gap-1.5 rounded-lg px-2 py-2 text-xs text-red-400 hover:bg-red-950 transition-colors"
            >
              <LogOut size={13} />
              {t('admin', 'logout')}
            </Link>
          </div>
        </div>
      </aside>

      {/* Main */}
      <div className="flex-1 flex flex-col min-w-0">
        {/* Mobile topbar */}
        <header className="sticky top-0 z-30 flex h-14 items-center gap-3 bg-white border-b border-slate-200 px-4 lg:hidden">
          <button onClick={() => setOpen(true)} className="text-slate-500">
            <Menu size={20} />
          </button>
          <span className="font-bold text-slate-900 text-sm">HM Admin</span>
          <div className="ml-auto">
            <LanguageSwitcher />
          </div>
        </header>

        {/* Flash */}
        {(flash.success || flash.error || flash.info) && (
          <div className="px-6 pt-4">
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

        <main className="flex-1 p-4 lg:p-6">{children}</main>
      </div>
    </div>
  )
}
