import { useState, useEffect, useRef } from 'react'
import { cn } from '@/lib/utils'

const MONTHS = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
]

const BASE_SELECT = [
  'block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm',
  'focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 cursor-pointer',
  'appearance-none transition-colors',
].join(' ')

interface Props {
  label?: string
  value: string // YYYY-MM-DD or ''
  onChange: (date: string) => void
  error?: string
  required?: boolean
}

export function DateOfBirthSelect({ label, value, onChange, error, required }: Props) {
  const currentYear = new Date().getFullYear()
  const [day, setDay] = useState('')
  const [month, setMonth] = useState('')
  const [year, setYear] = useState('')
  const initialized = useRef(false)

  // Parse server value once on mount (or when value first becomes non-empty)
  useEffect(() => {
    if (!initialized.current && value && /^\d{4}-\d{2}-\d{2}$/.test(value)) {
      const parts = value.split('-')
      setYear(parts[0] ?? '')
      setMonth(String(parseInt(parts[1] ?? '0', 10)))
      setDay(String(parseInt(parts[2] ?? '0', 10)))
      initialized.current = true
    }
  }, [value])

  const combine = (d: string, m: string, y: string) => {
    if (d && m && y) {
      onChange(`${y}-${m.padStart(2, '0')}-${d.padStart(2, '0')}`)
    } else {
      onChange('')
    }
  }

  // 18 to 70 years ago
  const years = Array.from({ length: 53 }, (_, i) => currentYear - 18 - i)
  const days = Array.from({ length: 31 }, (_, i) => i + 1)

  const errClass = error ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : ''

  return (
    <div>
      {label && (
        <label className="block text-sm font-medium text-slate-700 mb-1.5">
          {label}
          {required && <span className="ml-0.5 text-red-500">*</span>}
        </label>
      )}
      <div className="grid grid-cols-3 gap-2">
        <select
          value={day}
          onChange={e => { setDay(e.target.value); combine(e.target.value, month, year) }}
          className={cn(BASE_SELECT, errClass, !day && 'text-slate-400')}
        >
          <option value="">Day</option>
          {days.map(d => (
            <option key={d} value={String(d)} className="text-slate-900">{d}</option>
          ))}
        </select>

        <select
          value={month}
          onChange={e => { setMonth(e.target.value); combine(day, e.target.value, year) }}
          className={cn(BASE_SELECT, errClass, !month && 'text-slate-400')}
        >
          <option value="">Month</option>
          {MONTHS.map((m, i) => (
            <option key={i + 1} value={String(i + 1)} className="text-slate-900">{m}</option>
          ))}
        </select>

        <select
          value={year}
          onChange={e => { setYear(e.target.value); combine(day, month, e.target.value) }}
          className={cn(BASE_SELECT, errClass, !year && 'text-slate-400')}
        >
          <option value="">Year</option>
          {years.map(y => (
            <option key={y} value={String(y)} className="text-slate-900">{y}</option>
          ))}
        </select>
      </div>
      {error && <p className="mt-1.5 text-xs text-red-600">{error}</p>}
    </div>
  )
}
