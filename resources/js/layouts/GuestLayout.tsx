/// <reference path="../types/ziggy.d.ts" />
import { Link } from '@inertiajs/react'
import { Crown } from 'lucide-react'
import { useTranslation } from '@/lib/i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher'

interface Props {
  children: React.ReactNode
  title?: string
  panel?: React.ReactNode
}

export default function GuestLayout({ children, title, panel }: Props) {
  const { t } = useTranslation()

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-emerald-50 flex flex-col">
      {/* Header */}
      <header className="flex h-16 items-center px-6 border-b border-white/60 bg-white/70 backdrop-blur-sm">
        <Link href="/" className="flex items-center gap-2.5">
          <div className="h-8 w-8 rounded-xl bg-gradient-to-br from-primary-600 to-violet-600 flex items-center justify-center">
            <Crown size={16} className="text-white" />
          </div>
          <span className="font-bold text-slate-900 tracking-tight text-lg">HeavenlyMatch</span>
        </Link>

        <div className="ml-auto flex items-center gap-3">
          <LanguageSwitcher className="hidden sm:flex" />
          <Link href={route('login')} className="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
            {t('marketing', 'nav_sign_in')}
          </Link>
          <Link
            href={route('register')}
            className="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition-colors shadow-sm"
          >
            {t('marketing', 'nav_join_free')}
          </Link>
        </div>
      </header>

      {/* Content */}
      <div className="flex-1 flex items-center justify-center p-4 py-10">
        {panel ? (
          <div className="w-full max-w-5xl grid lg:grid-cols-[55fr_45fr] gap-14 items-center">
            {/* Trust panel — hidden on mobile */}
            <div className="hidden lg:block">{panel}</div>

            {/* Form column */}
            <div className="w-full">
              {title && (
                <h1 className="text-2xl font-bold text-slate-900 text-center mb-6">{title}</h1>
              )}
              {children}
            </div>
          </div>
        ) : (
          <div className="w-full max-w-md">
            {title && (
              <h1 className="text-2xl font-bold text-slate-900 text-center mb-6">{title}</h1>
            )}
            {children}
          </div>
        )}
      </div>

      {/* Footer */}
      <footer className="text-center py-5 text-xs text-slate-400 border-t border-slate-100/60">
        © {new Date().getFullYear()} HeavenlyMatch · Privacy-first matrimony for Muslims
      </footer>
    </div>
  )
}
