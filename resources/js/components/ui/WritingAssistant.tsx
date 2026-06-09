import { useEffect, useRef, useState } from 'react'
import { Sparkles, X } from 'lucide-react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import { getSuggestions } from '@/lib/aiSuggestions'

interface Props {
  /** Logical field key (drives which template bank is used). */
  field: string
  /** Current textarea value — decides whether to offer Replace/Append vs Use. */
  value: string
  /** Platform mode — 'islamic' yields deen-friendly wording. */
  mode?: string | null
  /** Optional gender (reserved for future tone tuning). */
  gender?: string | null
  /** Called with the final text the user chose to insert. */
  onApply: (text: string) => void
}

/**
 * Small, optional "AI Suggestion" helper shown beside long biodata text fields.
 * Opens a dropdown of 2–3 language/mode-aware template suggestions. The user
 * stays fully in control — suggestions are inserted only on explicit click, and
 * when the field already has text they choose Replace or Append (or Cancel by
 * closing). Never auto-saves; the inserted text remains freely editable.
 *
 * Works offline via @/lib/aiSuggestions — no API key required, never crashes.
 */
export default function WritingAssistant({ field, value, mode, gender, onApply }: Props) {
  const { t, locale } = useTranslation()
  const [open, setOpen] = useState(false)
  const [suggestions, setSuggestions] = useState<string[]>([])
  const ref = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (!open) return
    // Safe: getSuggestions always returns at least one item and never throws.
    setSuggestions(getSuggestions({ field, mode, gender, locale }))
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
  }, [open, field, mode, gender, locale])

  const hasContent = (value ?? '').trim().length > 0
  const apply = (text: string, how: 'replace' | 'append') => {
    onApply(how === 'append' && hasContent ? `${value.trimEnd()}\n${text}` : text)
    setOpen(false)
  }

  const actionBtn = 'rounded-lg px-2 py-1 text-[11px] font-semibold transition-colors'

  return (
    <div className="relative" ref={ref}>
      <button
        type="button"
        onClick={() => setOpen(o => !o)}
        aria-haspopup="dialog"
        aria-expanded={open}
        className="flex items-center gap-1 rounded-lg border border-primary-200 bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 hover:bg-primary-100 transition-colors"
      >
        <Sparkles size={12} /> {t('common', 'ai_suggest')}
      </button>

      {open && (
        <div
          role="dialog"
          className="absolute right-0 z-50 mt-1.5 w-72 sm:w-80 rounded-2xl border border-slate-200 bg-white shadow-xl ring-1 ring-black/5 p-3"
        >
          <div className="flex items-center justify-between mb-1">
            <p className="text-xs font-semibold text-slate-700 flex items-center gap-1">
              <Sparkles size={12} className="text-primary-600" /> {t('common', 'ai_panel_title')}
            </p>
            <button
              type="button"
              onClick={() => setOpen(false)}
              aria-label={t('common', 'ai_cancel')}
              className="text-slate-400 hover:text-slate-600 transition-colors"
            >
              <X size={14} />
            </button>
          </div>
          <p className="text-[11px] text-slate-400 mb-2">{t('common', 'ai_panel_hint')}</p>

          <div className="space-y-2 max-h-72 overflow-y-auto">
            {suggestions.map((s, i) => (
              <div key={i} className="rounded-xl border border-slate-200 bg-slate-50 p-2.5">
                <p className="text-xs text-slate-700 leading-relaxed">{s}</p>
                <div className="flex items-center gap-1.5 mt-2">
                  {hasContent ? (
                    <>
                      <button
                        type="button"
                        onClick={() => apply(s, 'replace')}
                        className={cn(actionBtn, 'bg-primary-600 text-white hover:bg-primary-700')}
                      >
                        {t('common', 'ai_replace')}
                      </button>
                      <button
                        type="button"
                        onClick={() => apply(s, 'append')}
                        className={cn(actionBtn, 'border border-primary-300 text-primary-700 hover:bg-primary-50')}
                      >
                        {t('common', 'ai_append')}
                      </button>
                    </>
                  ) : (
                    <button
                      type="button"
                      onClick={() => apply(s, 'replace')}
                      className={cn(actionBtn, 'bg-primary-600 text-white hover:bg-primary-700')}
                    >
                      {t('common', 'ai_use')}
                    </button>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}
