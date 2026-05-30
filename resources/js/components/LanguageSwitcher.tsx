import { router } from '@inertiajs/react'
import { useTranslation } from '@/lib/i18n'

interface Props {
  className?: string
  dark?: boolean
  inverted?: boolean  // for use on dark/green backgrounds
}

const LOCALES = [
  { code: 'bn', label: 'বাংলা' },
  { code: 'en', label: 'English' },
] as const

export default function LanguageSwitcher({ className = '', dark = false, inverted = false }: Props) {
  const { locale } = useTranslation()

  function switchLocale(code: string) {
    if (code === locale) return
    router.post(
      route('language.switch', { locale: code }),
      {},
      { preserveScroll: true, preserveState: true },
    )
  }

  return (
    <div className={`flex items-center gap-1 ${className}`} role="group" aria-label="Language">
      {LOCALES.map(({ code, label }) => (
        <button
          key={code}
          type="button"
          onClick={() => switchLocale(code)}
          aria-pressed={locale === code}
          className={[
            'rounded px-2 py-0.5 text-xs font-medium transition-colors',
            inverted
              ? locale === code
                ? 'bg-white/20 text-white'
                : 'text-white/60 hover:text-white hover:bg-white/10'
              : locale === code
                ? 'bg-primary-600 text-white'
                : dark
                  ? 'text-slate-400 hover:text-white hover:bg-slate-800'
                  : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100',
          ].join(' ')}
        >
          {label}
        </button>
      ))}
    </div>
  )
}
