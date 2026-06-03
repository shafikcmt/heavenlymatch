/// <reference path="../../types/ziggy.d.ts" />
import { Link, usePage } from '@inertiajs/react'
import { Home, Search, Mail, Crown, User, FileEdit, Settings } from 'lucide-react'
import { cn } from '@/lib/utils'
import type { PageProps } from '@/types'
import { useTranslation } from '@/lib/i18n'

const FULL_NAV = [
  { key: 'home',    label: 'nav_home',    href: '/dashboard',        icon: Home,   exact: true },
  { key: 'search',  label: 'nav_search',  href: '/search',           icon: Search, exact: false },
  { key: 'mailbox', label: 'nav_mailbox', href: '/inbox',            icon: Mail,   exact: false },
  { key: 'upgrade', label: 'nav_upgrade', href: '/upgrade/plans',    icon: Crown,  exact: false },
  { key: 'profile', label: 'nav_profile', href: '/dashboard/profile',icon: User,   exact: false },
]

// Before biodata approval: focus on completing the biodata.
const ONBOARD_NAV = [
  { key: 'home',     label: 'nav_home',     href: '/dashboard',        icon: Home,     exact: true },
  { key: 'biodata',  label: 'nav_biodata',  href: '/biodata/wizard',   icon: FileEdit, exact: false },
  { key: 'profile',  label: 'nav_profile',  href: '/dashboard/profile',icon: User,     exact: false },
  { key: 'settings', label: 'nav_settings', href: '/settings',         icon: Settings, exact: false },
]

export function MobileBottomNav() {
  const { unread_notifications, access } = usePage<PageProps>().props
  const { t } = useTranslation()
  const currentPath = typeof window !== 'undefined' ? window.location.pathname : ''
  const fullAccess = access ? access.can_access_matches : true
  const navItems = fullAccess ? FULL_NAV : ONBOARD_NAV

  const isActive = (href: string, exact: boolean) =>
    exact ? currentPath === href : currentPath.startsWith(href)

  return (
    <nav
      aria-label="Mobile navigation"
      className="fixed bottom-0 left-0 right-0 z-50 flex h-16 border-t border-slate-200 bg-white lg:hidden safe-bottom"
    >
      {navItems.map(({ key, label, href, icon: Icon, exact }) => {
        const active = isActive(href, exact)
        const isMailbox = key === 'mailbox'
        return (
          <Link
            key={key}
            href={href}
            aria-label={t('common', label) || label}
            className="relative flex flex-1 flex-col items-center justify-center gap-0.5 py-1 transition-colors active:bg-slate-50"
          >
            <span className="relative">
              <Icon
                size={22}
                className={cn(
                  'transition-colors',
                  active ? 'text-primary-700 fill-primary-700' : 'text-slate-400',
                )}
                strokeWidth={active ? 2.5 : 1.8}
              />
              {isMailbox && unread_notifications > 0 && (
                <span className="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white">
                  {unread_notifications > 9 ? '9+' : unread_notifications}
                </span>
              )}
            </span>
            <span
              className={cn(
                'text-[10px] font-medium leading-none',
                active ? 'text-primary-700' : 'text-slate-400',
              )}
            >
              {t('common', label) || key.charAt(0).toUpperCase() + key.slice(1)}
            </span>
          </Link>
        )
      })}
    </nav>
  )
}
