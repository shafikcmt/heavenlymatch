/// <reference path="../types/ziggy.d.ts" />
import { Link, usePage } from '@inertiajs/react'
import { useEffect, useRef, useState } from 'react'
import { User, Settings, LogOut, ChevronDown, FileEdit, IdCard } from 'lucide-react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import type { PageProps } from '@/types'

/**
 * Top-right account dropdown for the authenticated dashboard header.
 * Always-accessible quick links + a clearly visible red Logout.
 */
export default function UserMenu() {
  const { auth } = usePage<PageProps>().props
  const { t } = useTranslation()
  const user = auth.user!
  const [open, setOpen] = useState(false)
  const ref = useRef<HTMLDivElement>(null)

  // Close on outside click / Escape
  useEffect(() => {
    if (!open) return
    const onClick = (e: MouseEvent) => {
      if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false)
    }
    const onKey = (e: KeyboardEvent) => e.key === 'Escape' && setOpen(false)
    document.addEventListener('mousedown', onClick)
    document.addEventListener('keydown', onKey)
    return () => {
      document.removeEventListener('mousedown', onClick)
      document.removeEventListener('keydown', onKey)
    }
  }, [open])

  const items = [
    { label: t('common', 'my_profile'),   href: route('dashboard.profile'), icon: User },
    { label: t('common', 'edit_biodata'), href: route('biodata.wizard'),    icon: FileEdit },
    { label: t('common', 'settings'),     href: route('settings.index'),    icon: Settings },
  ]

  return (
    <div className="relative" ref={ref}>
      <button
        type="button"
        onClick={() => setOpen(o => !o)}
        aria-haspopup="menu"
        aria-expanded={open}
        className="flex items-center gap-2 rounded-full py-1 pl-1 pr-2 hover:bg-slate-100 transition-colors"
      >
        <span className="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center shrink-0">
          <User size={18} className="text-primary-700" />
        </span>
        <span className="hidden sm:block text-left leading-tight max-w-[140px]">
          <span className="block text-sm font-semibold text-slate-900 truncate">{user.name}</span>
          <span className="block text-xs text-slate-400 truncate">{user.registration_id}</span>
        </span>
        <ChevronDown size={16} className={cn('text-slate-400 transition-transform', open && 'rotate-180')} />
      </button>

      {open && (
        <div
          role="menu"
          className="absolute right-0 mt-2 w-60 origin-top-right rounded-2xl border border-slate-200 bg-white shadow-xl ring-1 ring-black/5 overflow-hidden z-50"
        >
          {/* Header */}
          <div className="flex items-center gap-3 px-4 py-3 border-b border-slate-100 bg-slate-50">
            <span className="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center shrink-0">
              <User size={20} className="text-primary-700" />
            </span>
            <div className="min-w-0">
              <p className="text-sm font-semibold text-slate-900 truncate">{user.name}</p>
              <p className="text-xs text-slate-400 flex items-center gap-1 truncate">
                <IdCard size={12} /> {user.registration_id}
              </p>
            </div>
          </div>

          {/* Links */}
          <div className="py-1">
            {items.map(({ label, href, icon: Icon }) => (
              <Link
                key={href}
                href={href}
                onClick={() => setOpen(false)}
                role="menuitem"
                className="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors"
              >
                <Icon size={16} className="text-slate-400" />
                {label}
              </Link>
            ))}
          </div>

          {/* Logout — always visible, clearly destructive */}
          <div className="border-t border-slate-100 p-1.5">
            <Link
              href={route('logout')}
              method="post"
              as="button"
              role="menuitem"
              className="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors"
            >
              <LogOut size={16} />
              {t('common', 'logout')}
            </Link>
          </div>
        </div>
      )}
    </div>
  )
}
