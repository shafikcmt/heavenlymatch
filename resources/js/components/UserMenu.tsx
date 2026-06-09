/// <reference path="../types/ziggy.d.ts" />
import { Link, usePage } from '@inertiajs/react'
import { useEffect, useRef, useState } from 'react'
import {
  User, Settings, LogOut, ChevronDown, FileEdit, IdCard,
  Globe, Crown, Star, BadgeCheck, ShieldCheck, ArrowRight,
} from 'lucide-react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import type { PageProps } from '@/types'
import ModeBadge from '@/components/ModeBadge'
import LanguageSwitcher from '@/components/LanguageSwitcher'

interface Props {
  /** Render the trigger for a dark/green header (mobile brand bar). */
  inverted?: boolean
}

/**
 * Top-right account control — a modern SaaS-style profile menu.
 *
 * Trigger  : gender avatar + name + biodata ID + current mode badge.
 * Dropdown : branded identity header with trust chips, a membership card
 *            (upgrade CTA for free users), a preferences block (mode +
 *            language), quick links, and a clearly destructive Logout.
 */
export default function UserMenu({ inverted = false }: Props) {
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

  const avatarSrc = `/images/avatar-${user.gender}.svg`
  const isPremium = user.membership_status === 'active'
  const isExpired = user.membership_status === 'expired'

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
        className={cn(
          'flex items-center gap-2 rounded-full py-1 pl-1 pr-2 transition-colors',
          inverted ? 'hover:bg-white/10' : 'hover:bg-slate-100',
          open && (inverted ? 'bg-white/10' : 'bg-slate-100'),
        )}
      >
        <span
          className={cn(
            'h-8 w-8 rounded-full overflow-hidden flex items-center justify-center shrink-0 ring-2',
            inverted ? 'ring-white/30 bg-white/20' : 'ring-primary-100 bg-primary-50',
          )}
        >
          <img src={avatarSrc} alt={user.name} className="h-full w-full object-cover" />
        </span>
        {/* Name + ID + mode (desktop / wider trigger only) */}
        <span className="hidden sm:flex items-center gap-2">
          <span className="text-left leading-tight max-w-[140px]">
            <span className={cn('block text-sm font-semibold truncate', inverted ? 'text-white' : 'text-slate-900')}>
              {user.name}
            </span>
            <span className={cn('block text-xs truncate', inverted ? 'text-white/70' : 'text-slate-400')}>
              {user.registration_id}
            </span>
          </span>
          {!inverted && <ModeBadge mode={user.platform_mode} />}
        </span>
        <ChevronDown
          size={16}
          className={cn('transition-transform', inverted ? 'text-white/80' : 'text-slate-400', open && 'rotate-180')}
        />
      </button>

      {open && (
        <div
          role="menu"
          className="absolute right-0 mt-2 w-72 origin-top-right rounded-2xl border border-slate-200 bg-white shadow-xl ring-1 ring-black/5 overflow-hidden z-50"
        >
          {/* ── Identity header (branded gradient) ── */}
          <div className="relative px-4 pt-4 pb-3.5 bg-gradient-to-br from-primary-600 to-primary-700 text-white">
            <div className="flex items-center gap-3">
              <span className="h-11 w-11 rounded-full overflow-hidden ring-2 ring-white/40 bg-white/15 shrink-0">
                <img src={avatarSrc} alt={user.name} className="h-full w-full object-cover" />
              </span>
              <div className="min-w-0">
                <p className="text-sm font-bold truncate">{user.name}</p>
                <p className="text-xs text-white/75 flex items-center gap-1 truncate">
                  <IdCard size={12} /> {user.registration_id}
                </p>
              </div>
            </div>

            {/* Trust chips — only render what's true */}
            {(user.is_email_verified || user.biodata_status === 'approved') && (
              <div className="flex flex-wrap gap-1.5 mt-3">
                {user.is_email_verified && (
                  <span className="inline-flex items-center gap-1 rounded-full bg-white/15 px-2 py-0.5 text-[11px] font-medium">
                    <BadgeCheck size={12} /> {t('common', 'verified')}
                  </span>
                )}
                {user.biodata_status === 'approved' && (
                  <span className="inline-flex items-center gap-1 rounded-full bg-white/15 px-2 py-0.5 text-[11px] font-medium">
                    <ShieldCheck size={12} /> {t('common', 'approved')}
                  </span>
                )}
              </div>
            )}
          </div>

          {/* ── Membership card ── */}
          <div className="p-3 border-b border-slate-100">
            {isPremium ? (
              <div className="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5">
                <span className="h-8 w-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                  <Star size={16} className="text-amber-600 fill-amber-500" />
                </span>
                <div className="min-w-0 flex-1">
                  <p className="text-sm font-semibold text-amber-900 truncate">{t('common', 'menu_plan_premium')}</p>
                  {user.membership_expires && (
                    <p className="text-[11px] text-amber-700">
                      {t('common', 'menu_expires_on', { date: formatDate(user.membership_expires) })}
                    </p>
                  )}
                </div>
              </div>
            ) : (
              <Link
                href={route('upgrade.plans')}
                onClick={() => setOpen(false)}
                role="menuitem"
                className="group flex items-center gap-3 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 px-3 py-2.5 text-white shadow-sm hover:shadow-md transition-shadow"
              >
                <span className="h-8 w-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
                  <Crown size={16} />
                </span>
                <div className="min-w-0 flex-1">
                  <p className="text-sm font-bold leading-tight truncate">
                    {isExpired ? t('common', 'menu_renews') : t('common', 'menu_upgrade')}
                  </p>
                  <p className="text-[11px] text-white/85 truncate">{t('common', 'menu_upgrade_desc')}</p>
                </div>
                <ArrowRight size={15} className="shrink-0 transition-transform group-hover:translate-x-0.5" />
              </Link>
            )}
          </div>

          {/* ── Preferences: mode + language ── */}
          <div className="px-4 py-2.5 border-b border-slate-100 space-y-2.5">
            <p className="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">
              {t('common', 'menu_preferences')}
            </p>
            <div className="flex items-center justify-between">
              <span className="text-xs font-medium text-slate-500">{t('common', 'mode')}</span>
              <ModeBadge mode={user.platform_mode} size="md" />
            </div>
            <div className="flex items-center justify-between">
              <span className="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                <Globe size={14} className="text-slate-400" />
                {t('common', 'language')}
              </span>
              <LanguageSwitcher />
            </div>
          </div>

          {/* ── Quick links ── */}
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

          {/* ── Logout — always visible, clearly destructive ── */}
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

/** Short, locale-neutral date for the membership renewal line. */
function formatDate(iso: string): string {
  const d = new Date(iso)
  if (isNaN(d.getTime())) return iso
  return d.toLocaleDateString('en-BD', { day: 'numeric', month: 'short', year: 'numeric' })
}
