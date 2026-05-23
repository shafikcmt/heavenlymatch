import { useState, useRef, useEffect } from 'react'
import { ChevronDown, X } from 'lucide-react'
import { cn } from '@/lib/utils'

interface Option {
  value: string
  label: string
}

interface Props {
  label?: string
  value: string
  onChange: (value: string) => void
  options: Option[]
  placeholder?: string
  error?: string
  disabled?: boolean
  allowFreeText?: boolean
}

export function SearchableSelect({
  label,
  value,
  onChange,
  options,
  placeholder = '— Select —',
  error,
  disabled = false,
  allowFreeText = false,
}: Props) {
  const [open, setOpen] = useState(false)
  const [query, setQuery] = useState('')
  const containerRef = useRef<HTMLDivElement>(null)
  const inputRef = useRef<HTMLInputElement>(null)

  const selected = options.find(o => o.value === value)
  const displayLabel = selected?.label ?? (allowFreeText ? value : '')

  const filtered = query.trim()
    ? options.filter(o => o.label.toLowerCase().includes(query.toLowerCase()))
    : options

  useEffect(() => {
    const onClickOutside = (e: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setOpen(false)
        setQuery('')
      }
    }
    document.addEventListener('mousedown', onClickOutside)
    return () => document.removeEventListener('mousedown', onClickOutside)
  }, [])

  const handleOpen = () => {
    if (disabled) return
    setOpen(true)
    setQuery('')
    setTimeout(() => inputRef.current?.focus(), 50)
  }

  const handleSelect = (opt: Option) => {
    onChange(opt.value)
    setOpen(false)
    setQuery('')
  }

  const handleClear = (e: React.MouseEvent) => {
    e.stopPropagation()
    onChange('')
    setOpen(false)
    setQuery('')
  }

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Escape') { setOpen(false); setQuery('') }
    if (e.key === 'Enter' && filtered.length > 0) {
      handleSelect(filtered[0]!)
    }
    if (e.key === 'Enter' && allowFreeText && filtered.length === 0 && query.trim()) {
      onChange(query.trim())
      setOpen(false)
      setQuery('')
    }
  }

  return (
    <div className="relative" ref={containerRef}>
      {label && (
        <label className="block text-sm font-medium text-slate-700 mb-1">{label}</label>
      )}

      <button
        type="button"
        onClick={handleOpen}
        disabled={disabled}
        className={cn(
          'w-full flex items-center gap-2 rounded-xl border bg-white px-4 py-2.5 text-sm text-left transition-colors',
          open ? 'border-primary-500 ring-1 ring-primary-500' : 'border-slate-300',
          error ? 'border-red-400' : '',
          disabled ? 'cursor-not-allowed bg-slate-50 text-slate-400' : 'cursor-pointer hover:border-slate-400',
        )}
      >
        <span className={cn('flex-1 truncate', !displayLabel && 'text-slate-400')}>
          {displayLabel || placeholder}
        </span>
        {value && !disabled && (
          <X
            size={14}
            className="text-slate-400 hover:text-slate-600 shrink-0"
            onClick={handleClear}
          />
        )}
        <ChevronDown
          size={16}
          className={cn('text-slate-400 shrink-0 transition-transform', open && 'rotate-180')}
        />
      </button>

      {open && (
        <div className="absolute z-50 mt-1 w-full rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden">
          <div className="p-2 border-b border-slate-100">
            <input
              ref={inputRef}
              type="text"
              value={query}
              onChange={e => setQuery(e.target.value)}
              onKeyDown={handleKeyDown}
              placeholder="Type to search..."
              className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            />
          </div>
          <ul className="max-h-48 overflow-y-auto py-1">
            {filtered.length > 0 ? (
              filtered.map(opt => (
                <li
                  key={opt.value}
                  onClick={() => handleSelect(opt)}
                  className={cn(
                    'px-4 py-2 text-sm cursor-pointer transition-colors',
                    opt.value === value
                      ? 'bg-primary-50 text-primary-700 font-medium'
                      : 'text-slate-700 hover:bg-slate-50',
                  )}
                >
                  {opt.label}
                </li>
              ))
            ) : (
              <li className="px-4 py-3 text-sm text-slate-400 text-center">
                {allowFreeText && query.trim()
                  ? <span>Press Enter to use "<strong>{query}</strong>"</span>
                  : 'No options found'}
              </li>
            )}
          </ul>
        </div>
      )}

      {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
    </div>
  )
}
