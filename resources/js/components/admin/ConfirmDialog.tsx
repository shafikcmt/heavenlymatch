import { AlertTriangle, X } from 'lucide-react'
import { Button } from '@/components/ui/Button'

interface Props {
  open: boolean
  title: string
  body: string
  confirmLabel: string
  cancelLabel?: string
  danger?: boolean
  loading?: boolean
  onConfirm: () => void
  onClose: () => void
}

/** Lightweight, dependency-free confirmation modal used across admin actions. */
export default function ConfirmDialog({
  open, title, body, confirmLabel, cancelLabel = 'Cancel',
  danger = true, loading = false, onConfirm, onClose,
}: Props) {
  if (!open) return null

  return (
    <div className="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div className="absolute inset-0 bg-black/40" onClick={onClose} />
      <div className="relative w-full max-w-md rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
        <button
          onClick={onClose}
          className="absolute right-3 top-3 text-slate-400 hover:text-slate-600"
          aria-label="Close"
        >
          <X size={18} />
        </button>
        <div className="p-6">
          <div className="flex items-start gap-3">
            <div className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-full ${danger ? 'bg-red-100' : 'bg-amber-100'}`}>
              <AlertTriangle size={20} className={danger ? 'text-red-600' : 'text-amber-600'} />
            </div>
            <div className="min-w-0">
              <h3 className="text-base font-bold text-slate-900">{title}</h3>
              <p className="mt-1 text-sm text-slate-600">{body}</p>
            </div>
          </div>
          <div className="mt-6 flex justify-end gap-2">
            <Button variant="outline" size="sm" onClick={onClose} disabled={loading}>
              {cancelLabel}
            </Button>
            <Button
              variant={danger ? 'destructive' : 'default'}
              size="sm"
              onClick={onConfirm}
              isLoading={loading}
            >
              {confirmLabel}
            </Button>
          </div>
        </div>
      </div>
    </div>
  )
}
