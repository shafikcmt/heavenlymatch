import { Shield, Globe } from 'lucide-react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'

interface Props {
  /** Platform mode coming from the user/biodata record. */
  mode: 'general' | 'islamic' | string | null | undefined
  size?: 'sm' | 'md'
  className?: string
}

/**
 * Compact, dynamic platform-mode badge.
 * - Islamic / Halal Mode → shield icon, emerald palette.
 * - General Mode         → globe icon, sky palette.
 * Reusable anywhere the current mode needs to be surfaced.
 */
export default function ModeBadge({ mode, size = 'sm', className }: Props) {
  const { t } = useTranslation()
  const isIslamic = mode === 'islamic'
  const Icon = isIslamic ? Shield : Globe
  const label = isIslamic ? t('common', 'mode_islamic') : t('common', 'mode_general')

  return (
    <span
      className={cn(
        'inline-flex items-center gap-1 rounded-full font-semibold whitespace-nowrap',
        size === 'sm' ? 'px-2 py-0.5 text-[11px]' : 'px-2.5 py-1 text-xs',
        isIslamic
          ? 'bg-emerald-50 text-emerald-700'
          : 'bg-sky-50 text-sky-700',
        className,
      )}
    >
      <Icon size={size === 'sm' ? 12 : 14} />
      {label}
    </span>
  )
}
