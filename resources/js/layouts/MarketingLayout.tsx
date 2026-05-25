/// <reference path="../types/ziggy.d.ts" />
import { useState } from 'react'
import { Link } from '@inertiajs/react'
import { Crown, Heart, Menu, X } from 'lucide-react'
import { useTranslation } from '@/lib/i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher'

interface Props {
  children: React.ReactNode
}

export default function MarketingLayout({ children }: Props) {
  const { t } = useTranslation()
  const year = new Date().getFullYear()
  const [mobileOpen, setMobileOpen] = useState(false)

  const navLinks = [
    { href: route('how-it-works'), label: t('marketing', 'nav_how_it_works') },
    { href: route('pricing'),      label: t('marketing', 'nav_pricing') },
    { href: route('about'),        label: t('marketing', 'nav_about') },
    { href: route('contact'),      label: t('marketing', 'nav_contact') },
  ]

  return (
    <div className="min-h-screen bg-white flex flex-col">
      {/* ── Navbar ── */}
      <nav className="sticky top-0 z-40 border-b border-slate-100 bg-white/95 backdrop-blur">
        <div className="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
          <Link href={route('home')} className="flex items-center gap-2 flex-shrink-0">
            <div className="h-8 w-8 rounded-xl bg-gradient-to-br from-primary-600 to-violet-600 flex items-center justify-center">
              <Crown size={16} className="text-white" />
            </div>
            <span className="font-bold text-slate-900 text-lg tracking-tight">HeavenlyMatch</span>
          </Link>

          {/* Desktop nav links */}
          <div className="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
            {navLinks.map(({ href, label }) => (
              <Link key={href} href={href} className="hover:text-slate-900 transition-colors">
                {label}
              </Link>
            ))}
          </div>

          <div className="flex items-center gap-2">
            <LanguageSwitcher className="hidden sm:flex" />
            <Link
              href={route('login')}
              className="hidden sm:inline-flex text-sm font-medium text-slate-700 hover:text-slate-900 px-3 py-1.5 rounded-lg hover:bg-slate-100 transition-colors"
            >
              {t('marketing', 'nav_sign_in')}
            </Link>
            <Link
              href={route('register')}
              className="text-sm font-semibold bg-primary-600 text-white px-4 py-2 rounded-xl hover:bg-primary-700 transition-colors shadow-sm"
            >
              {t('marketing', 'nav_join_free')}
            </Link>
            {/* Hamburger — visible on < md */}
            <button
              type="button"
              onClick={() => setMobileOpen(o => !o)}
              className="md:hidden ml-1 p-2 rounded-lg hover:bg-slate-100 transition-colors"
              aria-label="Toggle menu"
              aria-expanded={mobileOpen}
            >
              {mobileOpen
                ? <X size={20} className="text-slate-700" />
                : <Menu size={20} className="text-slate-700" />
              }
            </button>
          </div>
        </div>

        {/* Mobile dropdown */}
        {mobileOpen && (
          <div className="md:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1">
            {navLinks.map(({ href, label }) => (
              <Link
                key={href}
                href={href}
                onClick={() => setMobileOpen(false)}
                className="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors"
              >
                {label}
              </Link>
            ))}
            <div className="flex items-center gap-3 px-3 pt-3 mt-1 border-t border-slate-100">
              <Link
                href={route('login')}
                onClick={() => setMobileOpen(false)}
                className="text-sm font-medium text-slate-700 hover:text-primary-600 transition-colors"
              >
                {t('marketing', 'nav_sign_in')}
              </Link>
              <span className="text-slate-300">·</span>
              <LanguageSwitcher />
            </div>
          </div>
        )}
      </nav>

      {/* ── Page content ── */}
      <main className="flex-1">
        {children}
      </main>

      {/* ── Footer ── */}
      <footer className="border-t border-slate-100 py-10 px-4 bg-slate-50">
        <div className="max-w-6xl mx-auto">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-6">
            <div className="flex items-center gap-2">
              <Heart size={16} className="text-primary-600 fill-primary-600" />
              <span className="text-sm font-semibold text-slate-700">HeavenlyMatch</span>
              <span className="text-sm text-slate-400">·</span>
              <span className="text-sm text-slate-400">{t('marketing', 'footer_tagline')}</span>
            </div>
            <nav className="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-slate-400">
              <Link href={route('how-it-works')} className="hover:text-slate-600 transition-colors">{t('marketing', 'nav_how_it_works')}</Link>
              <Link href={route('pricing')} className="hover:text-slate-600 transition-colors">{t('marketing', 'nav_pricing')}</Link>
              <Link href={route('terms')} className="hover:text-slate-600 transition-colors">{t('marketing', 'footer_terms')}</Link>
              <Link href={route('privacy')} className="hover:text-slate-600 transition-colors">{t('marketing', 'footer_privacy')}</Link>
              <Link href={route('contact')} className="hover:text-slate-600 transition-colors">{t('marketing', 'footer_contact')}</Link>
            </nav>
          </div>
          <p className="mt-6 text-center text-xs text-slate-400">
            {t('marketing', 'footer_copyright', { year })}
          </p>
        </div>
      </footer>
    </div>
  )
}
