import { Link } from '@inertiajs/react'
import { cn } from '@/lib/utils'
import { useRef, useEffect } from 'react'

export interface MobileTab {
  key: string
  label: string
  href: string
}

interface Props {
  tabs: MobileTab[]
  activeKey: string
}

export function MobileTabs({ tabs, activeKey }: Props) {
  const activeRef = useRef<HTMLAnchorElement | null>(null)
  const containerRef = useRef<HTMLDivElement | null>(null)

  useEffect(() => {
    if (activeRef.current && containerRef.current) {
      const el = activeRef.current
      const container = containerRef.current
      const elLeft = el.offsetLeft
      const elWidth = el.offsetWidth
      const containerWidth = container.offsetWidth
      container.scrollLeft = elLeft - containerWidth / 2 + elWidth / 2
    }
  }, [activeKey])

  return (
    <div
      ref={containerRef}
      className="flex overflow-x-auto scrollbar-none bg-primary-700"
      style={{ WebkitOverflowScrolling: 'touch' }}
      role="tablist"
    >
      {tabs.map(tab => {
        const active = tab.key === activeKey
        return (
          <Link
            key={tab.key}
            href={tab.href}
            role="tab"
            aria-selected={active}
            ref={active ? (el: HTMLAnchorElement | null) => { activeRef.current = el } : undefined}
            className={cn(
              'flex-none whitespace-nowrap px-4 py-3 text-[11px] font-bold uppercase tracking-wider transition-colors',
              active
                ? 'border-b-2 border-white text-white'
                : 'text-white/55 hover:text-white/80',
            )}
          >
            {tab.label}
          </Link>
        )
      })}
    </div>
  )
}
