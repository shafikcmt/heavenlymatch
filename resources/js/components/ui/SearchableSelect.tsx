import { useState, useRef, useEffect, useCallback } from 'react'
import { createPortal } from 'react-dom'
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
  required?: boolean
  helperText?: string
  emptyText?: string
}

interface MenuPos {
  left: number
  width: number
  top?: number
  bottom?: number
  maxHeight: number
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
  required = false,
  helperText,
  emptyText,
}: Props) {
  const [open, setOpen] = useState(false)
  const [query, setQuery] = useState('')
  const [highlighted, setHighlighted] = useState(0)
  const [pos, setPos] = useState<MenuPos | null>(null)
  const containerRef = useRef<HTMLDivElement>(null)
  const triggerRef = useRef<HTMLButtonElement>(null)
  const menuRef = useRef<HTMLDivElement>(null)
  const inputRef = useRef<HTMLInputElement>(null)

  const selected = options.find(o => o.value === value)
  const displayLabel = selected?.label ?? (allowFreeText ? value : '')

  const filtered = query.trim()
    ? options.filter(o => o.label.toLowerCase().includes(query.toLowerCase()))
    : options

  useEffect(() => { setHighlighted(0) }, [query])

  // Position the portalled menu under (or above) the trigger using fixed coords,
  // so the card's overflow-hidden / stacking context can never clip it.
  const updatePos = useCallback(() => {
    const el = triggerRef.current
    if (!el) return
    const r = el.getBoundingClientRect()
    const spaceBelow = window.innerHeight - r.bottom
    const spaceAbove = r.top
    const openUp = spaceBelow < 260 && spaceAbove > spaceBelow
    const maxHeight = Math.min(288, Math.max(140, (openUp ? spaceAbove : spaceBelow) - 16))
    setPos({
      left: r.left,
      width: r.width,
      top: openUp ? undefined : r.bottom + 4,
      bottom: openUp ? window.innerHeight - r.top + 4 : undefined,
      maxHeight,
    })
  }, [])

  useEffect(() => {
    if (!open) return
    updatePos()
    const handler = () => updatePos()
    // capture:true so we react to scrolls on any ancestor container too.
    window.addEventListener('scroll', handler, true)
    window.addEventListener('resize', handler)
    return () => {
      window.removeEventListener('scroll', handler, true)
      window.removeEventListener('resize', handler)
    }
  }, [open, updatePos])

  useEffect(() => {
    const onClickOutside = (e: MouseEvent) => {
      const t = e.target as Node
      if (containerRef.current?.contains(t) || menuRef.current?.contains(t)) return
      setOpen(false)
      setQuery('')
    }
    document.addEventListener('mousedown', onClickOutside)
    return () => document.removeEventListener('mousedown', onClickOutside)
  }, [])

  const handleOpen = () => {
    if (disabled) return
    updatePos()
    setOpen(true)
    setQuery('')
    setTimeout(() => inputRef.current?.focus(), 10)
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

  // e.preventDefault() stops form submit on Enter; also handles arrow navigation
  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter') {
      e.preventDefault()
      if (filtered.length > 0) {
        handleSelect(filtered[highlighted] ?? filtered[0]!)
      } else if (allowFreeText && query.trim()) {
        onChange(query.trim())
        setOpen(false)
        setQuery('')
      }
    } else if (e.key === 'Escape') {
      setOpen(false)
      setQuery('')
    } else if (e.key === 'ArrowDown') {
      e.preventDefault()
      setHighlighted(h => Math.min(h + 1, filtered.length - 1))
    } else if (e.key === 'ArrowUp') {
      e.preventDefault()
      setHighlighted(h => Math.max(h - 1, 0))
    }
  }

  return (
    <div className="flex flex-col gap-1.5" ref={containerRef}>
      {label && (
        <label className="text-sm font-medium text-slate-700">
          {label}
          {required && <span className="ml-0.5 text-red-500">*</span>}
        </label>
      )}

      <div className="relative">
        <button
          ref={triggerRef}
          type="button"
          onClick={() => (open ? setOpen(false) : handleOpen())}
          disabled={disabled}
          className={cn(
            'w-full flex items-center gap-2 rounded-xl border bg-white px-4 py-2.5 text-sm text-left shadow-sm transition-colors',
            open
              ? 'border-primary-500 ring-1 ring-primary-500'
              : 'border-slate-300 hover:border-slate-400',
            error && !open && 'border-red-400',
            disabled && 'cursor-not-allowed bg-slate-50 text-slate-400',
            !disabled && 'cursor-pointer',
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

        {open && pos && createPortal(
          <div
            ref={menuRef}
            style={{
              position: 'fixed',
              left: pos.left,
              width: pos.width,
              top: pos.top,
              bottom: pos.bottom,
              zIndex: 9999,
            }}
            className="rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden"
          >
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
            <ul className="overflow-y-auto py-1" style={{ maxHeight: pos.maxHeight }}>
              {filtered.length > 0 ? (
                filtered.map((opt, idx) => (
                  <li
                    key={opt.value}
                    // onMouseDown + preventDefault keeps focus on search input while selecting
                    onMouseDown={e => { e.preventDefault(); handleSelect(opt) }}
                    className={cn(
                      'px-4 py-2 text-sm cursor-pointer transition-colors',
                      idx === highlighted
                        ? 'bg-primary-100 text-primary-700 font-medium'
                        : opt.value === value
                          ? 'bg-primary-50 text-primary-700'
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
                    : (emptyText ?? 'No options found')}
                </li>
              )}
            </ul>
          </div>,
          document.body,
        )}
      </div>

      {error && <p className="text-xs text-red-600">{error}</p>}
      {!error && helperText && <p className="text-xs text-slate-500">{helperText}</p>}
    </div>
  )
}
