import { SearchableSelect } from './SearchableSelect'

function generateWeightOptions(): { value: string; label: string }[] {
  const options: { value: string; label: string }[] = [
    { value: '29', label: '< 30 KG' },
  ]
  for (let kg = 30; kg <= 150; kg++) {
    options.push({ value: String(kg), label: `${kg} KG` })
  }
  options.push({ value: '151', label: '150+ KG' })
  return options
}

const WEIGHT_OPTIONS = generateWeightOptions()

interface Props {
  label?: string
  value: number | ''
  onChange: (kg: number | '') => void
  error?: string
  placeholder?: string
  required?: boolean
}

export function WeightSelect({ label, value, onChange, error, placeholder, required }: Props) {
  const strValue = value !== '' && value !== undefined && value !== null
    ? String(value)
    : ''

  return (
    <SearchableSelect
      label={label}
      value={strValue}
      onChange={val => onChange(val ? parseInt(val, 10) : '')}
      options={WEIGHT_OPTIONS}
      error={error}
      placeholder={placeholder ?? '— Select weight —'}
      required={required}
    />
  )
}
