import { Moon, Users } from 'lucide-react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'

export type PlatformMode = 'islamic' | 'general' | string | null | undefined

/** Treat islamic/halal as Islamic mode; everything else (incl. missing) → General. */
export function isIslamicMode(mode: PlatformMode): boolean {
  return mode === 'islamic' || mode === 'halal'
}

/**
 * Single source of truth for how a platform mode looks:
 *  - Islamic / Halal → crescent Moon, soft emerald.
 *  - General         → Users group, soft sky-blue.
 * Used by both the chip badge and the avatar-corner dot so they always agree.
 */
export function modeVisual(mode: PlatformMode) {
  const islamic = isIslamicMode(mode)
  return {
    islamic,
    Icon: islamic ? Moon : Users,
    chip: islamic
      ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
      : 'bg-sky-50 text-sky-700 border-sky-200',
    solid: islamic ? 'bg-emerald-500' : 'bg-sky-500',
  }
}

interface Props {
  /** Platform mode coming from the user/biodata record. */
  mode: PlatformMode
  size?: 'sm' | 'md'
  /** Short label ("Islamic" / "General") instead of the full mode name. */
  compact?: boolean
  className?: string
}

/**
 * Icon-first platform-mode badge. The icon is the primary identity; the text is
 * supporting. Part of the same badge system as the Premium (crown) and Verified
 * (check) chips — distinct icon + palette, consistent rounded shape.
 */
export default function ModeBadge({ mode, size = 'sm', compact = false, className }: Props) {
  const { t } = useTranslation()
  const { Icon, chip, islamic } = modeVisual(mode)
  const label = compact
    ? t('common', islamic ? 'mode_islamic_short' : 'mode_general_short')
    : t('common', islamic ? 'mode_islamic' : 'mode_general')

  return (
    <span
      className={cn(
        'inline-flex items-center gap-1 rounded-full border font-semibold whitespace-nowrap',
        size === 'sm' ? 'px-2 py-0.5 text-[11px]' : 'px-2.5 py-1 text-xs',
        chip,
        className,
      )}
    >
      <Icon size={size === 'sm' ? 12 : 14} className="shrink-0" />
      {label}
    </span>
  )
}

/**
 * Small mode icon rendered as a status dot on the corner of an avatar. Place
 * inside a `relative` wrapper that is a SIBLING of the (overflow-hidden) avatar,
 * so the dot is never clipped. White ring keeps it legible on any background.
 */
export function ModeAvatarDot({ mode, size = 'sm', className }: {
  mode: PlatformMode; size?: 'sm' | 'md'; className?: string
}) {
  const { Icon, solid } = modeVisual(mode)
  const isSm = size === 'sm'
  return (
    <span
      aria-hidden
      className={cn(
        'absolute -bottom-0.5 -right-0.5 rounded-full ring-2 ring-white flex items-center justify-center shadow-sm',
        isSm ? 'h-3.5 w-3.5' : 'h-4 w-4',
        solid,
        className,
      )}
    >
      <Icon size={isSm ? 8 : 9} className="text-white" />
    </span>
  )
}
