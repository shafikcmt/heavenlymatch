import { Link } from '@inertiajs/react'
import type { LucideIcon } from 'lucide-react'
import { cn } from '@/lib/utils'

interface Props {
  icon: LucideIcon
  title: string
  description?: string
  ctaLabel?: string
  ctaHref?: string
  secondaryLabel?: string
  secondaryHref?: string
  className?: string
}

export function EmptyState({
  icon: Icon,
  title,
  description,
  ctaLabel,
  ctaHref,
  secondaryLabel,
  secondaryHref,
  className,
}: Props) {
  return (
    <div className={cn('flex flex-col items-center justify-center py-16 px-6 text-center', className)}>
      <div className="mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100">
        <Icon size={36} className="text-slate-300" strokeWidth={1.5} />
      </div>
      <h3 className="text-base font-semibold text-slate-700 mb-2">{title}</h3>
      {description && (
        <p className="text-sm text-slate-500 max-w-xs leading-relaxed mb-6">{description}</p>
      )}
      {!description && (ctaLabel || secondaryLabel) && <div className="mb-6" />}
      <div className="flex flex-col gap-2 w-full max-w-xs">
        {ctaLabel && ctaHref && (
          <Link
            href={ctaHref}
            className="w-full rounded-xl bg-primary-700 py-3 text-sm font-semibold text-white text-center hover:bg-primary-800 transition-colors active:scale-95"
          >
            {ctaLabel}
          </Link>
        )}
        {secondaryLabel && secondaryHref && (
          <Link
            href={secondaryHref}
            className="w-full rounded-xl border border-slate-200 py-3 text-sm font-medium text-slate-600 text-center hover:bg-slate-50 transition-colors"
          >
            {secondaryLabel}
          </Link>
        )}
      </div>
    </div>
  )
}
