import { useEffect, useRef, useState } from 'react'
import { Sparkles, Wand2, Copy, Check, X } from 'lucide-react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import { getSuggestions, rewriteText } from '@/lib/aiSuggestions'

interface Props {
  /** Logical field key (drives which template bank is used). */
  field: string
  /** Current textarea value. */
  value: string
  /** Platform mode — 'islamic' yields deen-friendly wording. */
  mode?: string | null
  /** Optional gender (reserved for future tone tuning). */
  gender?: string | null
  /** Field max length, so rewritten text never exceeds the limit. */
  maxLength?: number
  /** Called with the final text the user chose to insert. */
  onApply: (text: string) => void
  /**
   * 'suggest' → fresh template ideas (shown beside the label).
   * 'rewrite' → improve the user's own current text (shown below the textarea).
   */
  variant?: 'suggest' | 'rewrite'
}

/**
 * Optional AI writing helper shown around long biodata text fields.
 *
 * Two modes share one popover shell:
 *  • suggest — 2–3 language/mode-aware template ideas. Use this (empty field) or
 *    Replace / Append (field already has text).
 *  • rewrite — improved, matrimonial-friendly versions of the user's OWN text,
 *    with Replace / Append / Copy. Appears only when the field has content.
 *
 * The user is always in control: nothing is inserted without an explicit click,
 * nothing auto-saves, and inserted text stays fully editable. Works offline via
 * @/lib/aiSuggestions (no API key) and never throws.
 */
export default function WritingAssistant({
  field, value, mode, gender, maxLength, onApply, variant = 'suggest',
}: Props) {
  const { t, locale } = useTranslation()
  const [open, setOpen] = useState(false)
  const [items, setItems] = useState<string[]>([])
  const [copied, setCopied] = useState<number | null>(null)
  const ref = useRef<HTMLDivElement>(null)
  const isRewrite = variant === 'rewrite'

  useEffect(() => {
    if (!open) return
    // Both helpers are offline + safe (always return ≥1 item, never throw).
    setItems(
      isRewrite
        ? rewriteText({ field, mode, gender, locale, text: value, maxLength })
        : getSuggestions({ field, mode, gender, locale }),
    )
    setCopied(null)
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
  }, [open, field, mode, gender, locale, value, maxLength, isRewrite])

  const hasContent = (value ?? '').trim().length > 0
  const apply = (text: string, how: 'replace' | 'append') => {
    onApply(how === 'append' && hasContent ? `${value.trimEnd()}\n${text}` : text)
    setOpen(false)
  }
  const copy = async (text: string, i: number) => {
    try { await navigator.clipboard?.writeText(text) } catch { /* clipboard blocked — ignore */ }
    setCopied(i)
    setTimeout(() => setCopied(c => (c === i ? null : c)), 1500)
  }

  const actionBtn = 'rounded-lg px-2 py-1 text-[11px] font-semibold transition-colors'
  const Icon = isRewrite ? Wand2 : Sparkles

  return (
    <div className="relative" ref={ref}>
      <button
        type="button"
        onClick={() => setOpen(o => !o)}
        aria-haspopup="dialog"
        aria-expanded={open}
        className="flex items-center gap-1 rounded-lg border border-primary-200 bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 hover:bg-primary-100 transition-colors"
      >
        <Icon size={12} /> {t('common', isRewrite ? 'ai_rewrite' : 'ai_suggest')}
      </button>

      {open && (
        <div
          role="dialog"
          className="absolute right-0 z-50 mt-1.5 w-72 sm:w-80 rounded-2xl border border-slate-200 bg-white shadow-xl ring-1 ring-black/5 p-3"
        >
          <div className="flex items-center justify-between mb-1">
            <p className="text-xs font-semibold text-slate-700 flex items-center gap-1">
              <Icon size={12} className="text-primary-600" />
              {t('common', isRewrite ? 'ai_rewrite_title' : 'ai_panel_title')}
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
          <p className="text-[11px] text-slate-400 mb-2">
            {t('common', isRewrite ? 'ai_rewrite_panel_hint' : 'ai_panel_hint')}
          </p>

          {items.length === 0 ? (
            <p className="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-500">
              {t('common', 'ai_unavailable')}
            </p>
          ) : (
            <div className="space-y-2 max-h-72 overflow-y-auto">
              {items.map((s, i) => (
                <div key={i} className="rounded-xl border border-slate-200 bg-slate-50 p-2.5">
                  <p className="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{s}</p>
                  <div className="flex flex-wrap items-center gap-1.5 mt-2">
                    {isRewrite || hasContent ? (
                      <>
                        <button type="button" onClick={() => apply(s, 'replace')}
                          className={cn(actionBtn, 'bg-primary-600 text-white hover:bg-primary-700')}>
                          {t('common', 'ai_replace')}
                        </button>
                        <button type="button" onClick={() => apply(s, 'append')}
                          className={cn(actionBtn, 'border border-primary-300 text-primary-700 hover:bg-primary-50')}>
                          {t('common', 'ai_append')}
                        </button>
                        {isRewrite && (
                          <button type="button" onClick={() => copy(s, i)}
                            className={cn(actionBtn, 'border border-slate-300 text-slate-600 hover:bg-slate-100 inline-flex items-center gap-1')}>
                            {copied === i ? <Check size={11} /> : <Copy size={11} />}
                            {t('common', copied === i ? 'ai_copied' : 'ai_copy')}
                          </button>
                        )}
                      </>
                    ) : (
                      <button type="button" onClick={() => apply(s, 'replace')}
                        className={cn(actionBtn, 'bg-primary-600 text-white hover:bg-primary-700')}>
                        {t('common', 'ai_use')}
                      </button>
                    )}
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      )}
    </div>
  )
}
