import { SearchableSelect } from './SearchableSelect'

function generateHeightOptions(): { value: string; label: string }[] {
  const options: { value: string; label: string }[] = []
  // 4'0" (48 in) to 7'0" (84 in)
  for (let totalInches = 48; totalInches <= 84; totalInches++) {
    const feet = Math.floor(totalInches / 12)
    const inches = totalInches % 12
    const cm = Math.round(totalInches * 2.54)
    options.push({ value: String(cm), label: `${feet}' ${inches}" (${cm} cm)` })
  }
  return options
}

const HEIGHT_OPTIONS = generateHeightOptions()

function findNearestOption(cm: number): string {
  let best = HEIGHT_OPTIONS[0]!
  let minDiff = Math.abs(parseInt(best.value, 10) - cm)
  for (const opt of HEIGHT_OPTIONS) {
    const diff = Math.abs(parseInt(opt.value, 10) - cm)
    if (diff < minDiff) { minDiff = diff; best = opt }
  }
  return best.value
}

interface Props {
  label?: string
  value: number | ''
  onChange: (cm: number | '') => void
  error?: string
  placeholder?: string
  required?: boolean
}

export function HeightSelect({ label, value, onChange, error, placeholder, required }: Props) {
  const strValue = value !== '' && value !== undefined && value !== null
    ? findNearestOption(Number(value))
    : ''

  return (
    <SearchableSelect
      label={label}
      value={strValue}
      onChange={val => onChange(val ? parseInt(val, 10) : '')}
      options={HEIGHT_OPTIONS}
      error={error}
      placeholder={placeholder ?? '— Select height —'}
      required={required}
    />
  )
}
