import { useEffect, useRef, useState } from 'react'
import { router } from '@inertiajs/react'
import { Globe, Check, ChevronDown } from 'lucide-react'
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
  const [open, setOpen] = useState(false)
  const ref = useRef<HTMLDivElement>(null)

  const onDark = dark || inverted
  const currentLabel = LOCALES.find(l => l.code === locale)?.label ?? 'English'

  // Close on outside click / Escape
  useEffect(() => {
    if (!open) return
    function onDown(e: MouseEvent) {
      if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false)
    }
    function onKey(e: KeyboardEvent) {
      if (e.key === 'Escape') setOpen(false)
    }
    document.addEventListener('mousedown', onDown)
    document.addEventListener('keydown', onKey)
    return () => {
      document.removeEventListener('mousedown', onDown)
      document.removeEventListener('keydown', onKey)
    }
  }, [open])

  function switchLocale(code: string) {
    setOpen(false)
    if (code === locale) return
    router.post(
      route('language.switch', { locale: code }),
      {},
      { preserveScroll: true, preserveState: true },
    )
  }

  const triggerTone = onDark
    ? 'border-white/20 text-white hover:bg-white/10'
    : 'border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300'

  return (
    <div ref={ref} className={`relative ${className}`}>
      <button
        type="button"
        onClick={() => setOpen(o => !o)}
        aria-haspopup="listbox"
        aria-expanded={open}
        aria-label="Change language"
        className={`inline-flex items-center gap-1.5 rounded-xl border px-3 h-9 text-sm font-semibold transition-colors ${triggerTone}`}
      >
        <Globe size={15} className="shrink-0 opacity-80" />
        <span>{currentLabel}</span>
        <ChevronDown size={14} className={`shrink-0 opacity-70 transition-transform ${open ? 'rotate-180' : ''}`} />
      </button>

      {open && (
        <ul
          role="listbox"
          className="absolute right-0 mt-2 w-40 z-50 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg shadow-slate-900/10 py-1"
        >
          {LOCALES.map(({ code, label }) => (
            <li key={code}>
              <button
                type="button"
                role="option"
                aria-selected={locale === code}
                onClick={() => switchLocale(code)}
                className={`flex w-full items-center justify-between px-3 py-2 text-sm transition-colors ${
                  locale === code
                    ? 'font-semibold text-primary-700 bg-primary-50'
                    : 'text-slate-700 hover:bg-slate-50'
                }`}
              >
                {label}
                {locale === code && <Check size={15} className="text-primary-600" />}
              </button>
            </li>
          ))}
        </ul>
      )}
    </div>
  )
}
