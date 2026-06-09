import { useMemo, useState } from 'react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'

interface Country { code: string; dial: string; flag: string; name: string }

// Bangladesh first (default). A small set of common NRB destinations follows so a
// guardian/contact abroad can still be entered. BD is strictly validated; other
// codes accept a generic 6–14 digit national number.
const COUNTRIES: Country[] = [
  { code: 'BD', dial: '+880', flag: '🇧🇩', name: 'Bangladesh' },
  { code: 'IN', dial: '+91',  flag: '🇮🇳', name: 'India' },
  { code: 'SA', dial: '+966', flag: '🇸🇦', name: 'Saudi Arabia' },
  { code: 'AE', dial: '+971', flag: '🇦🇪', name: 'UAE' },
  { code: 'QA', dial: '+974', flag: '🇶🇦', name: 'Qatar' },
  { code: 'KW', dial: '+965', flag: '🇰🇼', name: 'Kuwait' },
  { code: 'OM', dial: '+968', flag: '🇴🇲', name: 'Oman' },
  { code: 'MY', dial: '+60',  flag: '🇲🇾', name: 'Malaysia' },
  { code: 'GB', dial: '+44',  flag: '🇬🇧', name: 'UK' },
  { code: 'US', dial: '+1',   flag: '🇺🇸', name: 'USA' },
]
const DIALS = COUNTRIES.map(c => c.dial).sort((a, b) => b.length - a.length) // longest first

const BD_RE = /^\+8801[3-9]\d{8}$/

/** Strip a leading 0 (BD national numbers are entered as 01… or 1…). */
function bdNational(localDigits: string): string {
  return localDigits.replace(/^0+/, '')
}

/** Split a stored E.164-ish value into { dial, local-digits } for editing. */
function parseValue(value: string): { dial: string; local: string } {
  const v = (value ?? '').trim()
  if (!v) return { dial: '+880', local: '' }
  const digits = v.replace(/[^0-9]/g, '')
  // Match the longest known dial code (handles +880 before +88-style ambiguity).
  for (const dial of DIALS) {
    const d = dial.replace('+', '')
    if (digits.startsWith(d)) return { dial, local: digits.slice(d.length) }
  }
  // Legacy BD values like 018… / 01… with no country code.
  if (digits.startsWith('0') || (digits.length <= 11 && digits.startsWith('1'))) {
    return { dial: '+880', local: bdNational(digits) }
  }
  return { dial: '+880', local: digits }
}

/** Assemble the normalized value to store. '' when no local number typed. */
function assemble(dial: string, local: string): string {
  const digits = local.replace(/[^0-9]/g, '')
  if (!digits) return ''
  if (dial === '+880') return `+880${bdNational(digits)}`
  return `${dial}${digits}`
}

/** Is the assembled value valid? Empty is valid (optional). */
function isValid(dial: string, local: string): boolean {
  const digits = local.replace(/[^0-9]/g, '')
  if (!digits) return true
  if (dial === '+880') return BD_RE.test(assemble(dial, local))
  const intl = digits.length
  return intl >= 6 && intl <= 14
}

interface Props {
  label: string
  /** Stored value (e.g. "+88018XXXXXXXX" or legacy "018XXXXXXXX"). */
  value: string
  /** Emits the normalized value ("" when cleared). */
  onChange: (value: string) => void
  /** Server-side error for this field. */
  error?: string
  required?: boolean
  /** Append a localized "(Optional)" hint to the label. */
  optional?: boolean
}

/**
 * Country-code + local-number phone field. Bangladesh (+880) by default with
 * strict BD validation; other codes accept a generic national number. Emits a
 * normalized value the backend re-validates. Old saved numbers parse cleanly.
 */
export default function PhoneNumberInput({ label, value, onChange, error, required, optional }: Props) {
  const { t } = useTranslation()
  const initial = useMemo(() => parseValue(value), [value])
  const [dial, setDial] = useState(initial.dial)
  const [local, setLocal] = useState(initial.local)
  const [touched, setTouched] = useState(false)

  const country = COUNTRIES.find(c => c.dial === dial) ?? COUNTRIES[0]!
  const placeholder = dial === '+880' ? '18XXXXXXXX' : t('biodata', 'phone_local_ph')
  const localInvalid = touched && local.trim() !== '' && !isValid(dial, local)
  const inlineError = error || (localInvalid ? t('biodata', 'phone_invalid') : undefined)

  const emit = (d: string, l: string) => onChange(assemble(d, l))

  return (
    <div className="flex flex-col gap-1.5">
      <label className="text-sm font-medium text-slate-700">
        {label}
        {required && <span className="ml-0.5 text-red-500">*</span>}
        {optional && <span className="ml-1 font-normal text-slate-400">({t('common', 'optional')})</span>}
      </label>

      <div className="flex gap-2">
        <select
          aria-label="Country code"
          value={dial}
          onChange={e => { setDial(e.target.value); emit(e.target.value, local) }}
          className="shrink-0 rounded-xl border border-slate-300 bg-white px-2 py-2.5 text-sm text-slate-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
        >
          {COUNTRIES.map(c => (
            <option key={c.code} value={c.dial}>{c.flag} {c.dial}</option>
          ))}
        </select>
        <input
          type="tel"
          inputMode="numeric"
          value={local}
          onChange={e => { const v = e.target.value.replace(/[^0-9]/g, ''); setLocal(v); emit(dial, v) }}
          onBlur={() => setTouched(true)}
          placeholder={placeholder}
          aria-invalid={!!inlineError}
          className={cn(
            'flex-1 min-w-0 rounded-xl border bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 transition-colors',
            'focus:outline-none focus:ring-1',
            inlineError
              ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
              : 'border-slate-300 focus:border-primary-500 focus:ring-primary-500',
          )}
        />
      </div>

      {inlineError
        ? <p className="text-xs text-red-600">{inlineError}</p>
        : <p className="text-xs text-slate-400">{t('biodata', 'phone_code_hint')}</p>}
      <span className="sr-only">{country.name}</span>
    </div>
  )
}
