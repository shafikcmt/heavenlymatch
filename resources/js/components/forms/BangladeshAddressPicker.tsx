import { useState, useEffect, useRef } from 'react'
import { ChevronDown, X } from 'lucide-react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import {
  loadDivisions, loadDistricts, loadUpazilas,
  type Division, type District, type Upazila,
} from '@/lib/address-data'

// ── Public interface ──────────────────────────────────────────────────────────

export interface AddressValue {
  division?: string   // stored as English name (e.g. "Dhaka")
  district?: string   // stored as English name (e.g. "Gazipur")
  upazila?: string    // stored as English name (e.g. "Sreepur")
}

interface Props {
  value: AddressValue
  onChange: (val: AddressValue) => void
  /** 'form' = full labels, required feel. 'filter' = compact, Any placeholders. */
  mode?: 'form' | 'filter'
  showUpazila?: boolean
  showDistrict?: boolean
  errors?: {
    division?: string
    district?: string
    upazila?: string
  }
}

// ── Internal single-level searchable dropdown ─────────────────────────────────

type AnyItem = { id: string; name: string; bn_name: string }

function LevelSelect({
  label, value, options, onChange, placeholder,
  disabled = false, error, locale, loading,
}: {
  label: string
  value: string
  options: AnyItem[]
  onChange: (name: string) => void
  placeholder: string
  disabled?: boolean
  error?: string
  locale: string
  loading?: boolean
}) {
  const [open, setOpen] = useState(false)
  const [query, setQuery] = useState('')
  const containerRef = useRef<HTMLDivElement>(null)
  const inputRef = useRef<HTMLInputElement>(null)

  const displayName = (o: AnyItem) => locale === 'bn' ? o.bn_name : o.name

  const selected = options.find(o => o.name === value)
  const displayValue = selected ? displayName(selected) : value || ''

  const filtered = query.trim()
    ? options.filter(o =>
        o.name.toLowerCase().includes(query.toLowerCase()) ||
        o.bn_name.includes(query)
      )
    : options

  useEffect(() => {
    const handler = (e: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setOpen(false)
        setQuery('')
      }
    }
    document.addEventListener('mousedown', handler)
    return () => document.removeEventListener('mousedown', handler)
  }, [])

  const handleOpen = () => {
    if (disabled || loading) return
    setOpen(true)
    setQuery('')
    setTimeout(() => inputRef.current?.focus(), 50)
  }

  const handleSelect = (item: AnyItem) => {
    onChange(item.name)
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
    if (e.key === 'Enter' && filtered.length > 0) handleSelect(filtered[0]!)
  }

  return (
    <div className="relative" ref={containerRef}>
      {label && (
        <label className="block text-xs font-medium text-slate-600 mb-1">{label}</label>
      )}

      <button
        type="button"
        onClick={handleOpen}
        disabled={disabled || loading}
        className={cn(
          'w-full flex items-center gap-2 rounded-xl border bg-white px-3 py-2 text-sm text-left transition-colors',
          open ? 'border-primary-500 ring-1 ring-primary-500' : 'border-slate-300',
          error && 'border-red-400',
          (disabled || loading)
            ? 'cursor-not-allowed bg-slate-50 text-slate-400'
            : 'cursor-pointer hover:border-slate-400',
        )}
      >
        <span className={cn('flex-1 truncate', !displayValue && 'text-slate-400')}>
          {loading ? (locale === 'bn' ? 'লোড হচ্ছে…' : 'Loading…') : (displayValue || placeholder)}
        </span>
        {value && !disabled && !loading && (
          <X
            size={13}
            className="text-slate-400 hover:text-slate-600 shrink-0"
            onClick={handleClear}
          />
        )}
        <ChevronDown
          size={15}
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
              placeholder={locale === 'bn' ? 'খুঁজুন…' : 'Type to search…'}
              className="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            />
          </div>
          <ul className="max-h-48 overflow-y-auto py-1">
            {filtered.length > 0 ? (
              filtered.map(item => (
                <li
                  key={item.id}
                  onClick={() => handleSelect(item)}
                  className={cn(
                    'px-3 py-2 text-sm cursor-pointer transition-colors flex items-center justify-between',
                    item.name === value
                      ? 'bg-primary-50 text-primary-700 font-medium'
                      : 'text-slate-700 hover:bg-slate-50',
                  )}
                >
                  <span>{locale === 'bn' ? item.bn_name : item.name}</span>
                  {locale !== 'bn' && item.bn_name && (
                    <span className="text-xs text-slate-400 ml-2 shrink-0">{item.bn_name}</span>
                  )}
                </li>
              ))
            ) : (
              <li className="px-3 py-3 text-sm text-slate-400 text-center">
                {locale === 'bn' ? 'কোনো ফলাফল নেই' : 'No results found'}
              </li>
            )}
          </ul>
        </div>
      )}

      {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
    </div>
  )
}

// ── Main component ────────────────────────────────────────────────────────────

export function BangladeshAddressPicker({
  value,
  onChange,
  mode = 'form',
  showUpazila = true,
  showDistrict = true,
  errors,
}: Props) {
  const { locale } = useTranslation()

  const [divisions, setDivisions] = useState<Division[]>([])
  const [allDistricts, setAllDistricts] = useState<District[]>([])
  const [allUpazilas, setAllUpazilas] = useState<Upazila[]>([])
  const [loadingDivisions, setLoadingDivisions] = useState(true)
  const [loadingDistricts, setLoadingDistricts] = useState(false)
  const [loadingUpazilas, setLoadingUpazilas] = useState(false)

  // Load divisions on mount
  useEffect(() => {
    loadDivisions()
      .then(setDivisions)
      .finally(() => setLoadingDivisions(false))
  }, [])

  // Load all districts once (when division exists or first open)
  useEffect(() => {
    if (!showDistrict) return
    setLoadingDistricts(true)
    loadDistricts()
      .then(setAllDistricts)
      .finally(() => setLoadingDistricts(false))
  }, [showDistrict])

  // Load all upazilas once
  useEffect(() => {
    if (!showUpazila) return
    setLoadingUpazilas(true)
    loadUpazilas()
      .then(setAllUpazilas)
      .finally(() => setLoadingUpazilas(false))
  }, [showUpazila])

  // Derive currently available districts from selected division
  const currentDivision = divisions.find(d => d.name === value.division)
  const districts: District[] = currentDivision
    ? allDistricts.filter(d => d.division_id === currentDivision.id)
    : []

  // Derive currently available upazilas from selected district
  const currentDistrict = allDistricts.find(d => d.name === value.district)
  const upazilas: Upazila[] = currentDistrict
    ? allUpazilas.filter(u => u.district_id === currentDistrict.id)
    : []

  const handleDivision = (name: string) => {
    onChange({ division: name || undefined, district: undefined, upazila: undefined })
  }

  const handleDistrict = (name: string) => {
    onChange({ ...value, district: name || undefined, upazila: undefined })
  }

  const handleUpazila = (name: string) => {
    onChange({ ...value, upazila: name || undefined })
  }

  // Label strings
  const isFilter = mode === 'filter'
  const divLabel  = locale === 'bn' ? 'বিভাগ' : 'Division'
  const distLabel = locale === 'bn' ? 'জেলা' : 'District'
  const upaLabel  = locale === 'bn' ? 'উপজেলা / থানা' : 'Upazila / Thana'

  const divPlaceholder  = isFilter
    ? (locale === 'bn' ? 'যেকোনো বিভাগ' : 'Any division')
    : (locale === 'bn' ? 'বিভাগ বাছুন' : 'Select division')

  const distPlaceholder = value.division
    ? (isFilter ? (locale === 'bn' ? 'যেকোনো জেলা' : 'Any district') : (locale === 'bn' ? 'জেলা বাছুন' : 'Select district'))
    : (locale === 'bn' ? 'আগে বিভাগ বাছুন' : 'Select division first')

  const upaPlaceholder  = value.district
    ? (isFilter ? (locale === 'bn' ? 'যেকোনো উপজেলা' : 'Any upazila') : (locale === 'bn' ? 'উপজেলা বাছুন' : 'Select upazila'))
    : (locale === 'bn' ? 'আগে জেলা বাছুন' : 'Select district first')

  return (
    <div className={cn('space-y-3', isFilter && 'space-y-2')}>
      <LevelSelect
        label={divLabel}
        value={value.division ?? ''}
        options={divisions}
        onChange={handleDivision}
        placeholder={divPlaceholder}
        loading={loadingDivisions}
        error={errors?.division}
        locale={locale}
      />

      {showDistrict && (
        <LevelSelect
          label={distLabel}
          value={value.district ?? ''}
          options={districts}
          onChange={handleDistrict}
          placeholder={distPlaceholder}
          disabled={!value.division}
          loading={!!value.division && loadingDistricts}
          error={errors?.district}
          locale={locale}
        />
      )}

      {showUpazila && showDistrict && (
        <LevelSelect
          label={upaLabel}
          value={value.upazila ?? ''}
          options={upazilas}
          onChange={handleUpazila}
          placeholder={upaPlaceholder}
          disabled={!value.district}
          loading={!!value.district && loadingUpazilas}
          error={errors?.upazila}
          locale={locale}
        />
      )}
    </div>
  )
}
