import { cn } from '@/lib/utils'

interface BadgeProps {
  children: React.ReactNode
  variant?: 'default' | 'success' | 'warning' | 'danger' | 'premium' | 'islamic' | 'outline'
  size?: 'sm' | 'default'
  className?: string
}

export function Badge({ children, variant = 'default', size = 'default', className }: BadgeProps) {
  return (
    <span
      className={cn(
        'inline-flex items-center gap-1 rounded-full font-medium',
        size === 'sm' ? 'px-2 py-0.5 text-xs' : 'px-2.5 py-1 text-xs',
        {
          default:  'bg-slate-100 text-slate-700',
          success:  'bg-emerald-50 text-emerald-700 border border-emerald-200',
          warning:  'bg-amber-50 text-amber-700 border border-amber-200',
          danger:   'bg-red-50 text-red-700 border border-red-200',
          premium:  'bg-amber-100 text-amber-800',
          islamic:  'bg-emerald-100 text-emerald-800',
          outline:  'border border-slate-300 text-slate-600',
        }[variant],
        className,
      )}
    >
      {children}
    </span>
  )
}
