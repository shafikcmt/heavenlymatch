/// <reference path="../../types/ziggy.d.ts" />
import { useState } from 'react'
import axios from 'axios'
import { X, KeyRound, Copy, Check } from 'lucide-react'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { useTranslation } from '@/lib/i18n'

interface Props {
  open: boolean
  registrationId: string
  userName: string
  onClose: () => void
}

/** Admin reset-password modal — manual or random, optional email notice. */
export default function ResetPasswordModal({ open, registrationId, userName, onClose }: Props) {
  const { t } = useTranslation()
  const [mode, setMode] = useState<'manual' | 'generate'>('manual')
  const [password, setPassword] = useState('')
  const [confirm, setConfirm] = useState('')
  const [notify, setNotify] = useState(false)
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState('')
  const [generated, setGenerated] = useState<string | null>(null)
  const [copied, setCopied] = useState(false)

  if (!open) return null

  const submit = async () => {
    setError(''); setBusy(true)
    try {
      const { data } = await axios.post(route('admin.users.reset-password', registrationId), {
        mode,
        password: mode === 'manual' ? password : undefined,
        password_confirmation: mode === 'manual' ? confirm : undefined,
        notify,
      })
      if (data?.password) {
        setGenerated(data.password)
      } else {
        onClose()
      }
    } catch (e: any) {
      const errs = e?.response?.data?.errors
      setError(errs ? Object.values(errs).flat().join(' ') : (e?.response?.data?.message ?? 'Error'))
    } finally {
      setBusy(false)
    }
  }

  const copy = () => {
    if (generated) {
      navigator.clipboard?.writeText(generated)
      setCopied(true)
      setTimeout(() => setCopied(false), 1500)
    }
  }

  const close = () => {
    setMode('manual'); setPassword(''); setConfirm(''); setNotify(false)
    setError(''); setGenerated(null); setCopied(false)
    onClose()
  }

  return (
    <div className="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div className="absolute inset-0 bg-black/40" onClick={close} />
      <div className="relative w-full max-w-md rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
        <button onClick={close} className="absolute right-3 top-3 text-slate-400 hover:text-slate-600" aria-label="Close">
          <X size={18} />
        </button>
        <div className="p-6">
          <div className="flex items-center gap-2 mb-1">
            <KeyRound size={18} className="text-primary-600" />
            <h3 className="text-base font-bold text-slate-900">{t('admin', 'reset_pw_title')}</h3>
          </div>
          <p className="text-xs text-slate-500 mb-4">{userName}</p>

          {generated ? (
            <div className="space-y-3">
              <p className="text-sm text-slate-600">{t('admin', 'generated_password')}</p>
              <div className="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                <code className="flex-1 font-mono text-sm text-slate-900">{generated}</code>
                <button onClick={copy} className="text-slate-500 hover:text-primary-600" aria-label="Copy">
                  {copied ? <Check size={16} className="text-emerald-600" /> : <Copy size={16} />}
                </button>
              </div>
              <div className="flex justify-end">
                <Button size="sm" onClick={close}>{t('common', 'close') || 'Close'}</Button>
              </div>
            </div>
          ) : (
            <div className="space-y-4">
              <div className="flex gap-2">
                {(['manual', 'generate'] as const).map(m => (
                  <button
                    key={m}
                    type="button"
                    onClick={() => setMode(m)}
                    className={`flex-1 rounded-xl border-2 px-3 py-2 text-xs font-semibold transition-all ${
                      mode === m ? 'border-primary-600 bg-primary-50 text-primary-700' : 'border-slate-200 text-slate-600'
                    }`}
                  >
                    {m === 'manual' ? t('admin', 'reset_pw_manual') : t('admin', 'reset_pw_generate')}
                  </button>
                ))}
              </div>

              {mode === 'manual' && (
                <>
                  <Input type="password" label={t('admin', 'field_new_password')} value={password} onChange={e => setPassword(e.target.value)} required />
                  <Input type="password" label={t('admin', 'field_confirm_password')} value={confirm} onChange={e => setConfirm(e.target.value)} required />
                </>
              )}

              <label className="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" checked={notify} onChange={e => setNotify(e.target.checked)} className="rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                {t('admin', 'reset_pw_send_email')}
              </label>

              {error && <p className="text-xs text-red-600">{error}</p>}

              <div className="flex justify-end gap-2">
                <Button variant="outline" size="sm" onClick={close} disabled={busy}>{t('common', 'cancel') || 'Cancel'}</Button>
                <Button size="sm" onClick={submit} isLoading={busy}>{t('admin', 'reset_pw_title')}</Button>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
