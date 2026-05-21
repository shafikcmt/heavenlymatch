/// <reference path="../../types/ziggy.d.ts" />
import { Head, useForm } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { Input } from '@/components/ui/Input'
import { Copy, CheckCircle, Upload } from 'lucide-react'
import { useState, useRef } from 'react'

interface Transaction {
  transaction_no: string
  plan_name: string
  amount: number
  gateway_name: string
  gateway_type: string
  merchant_number: string
  instructions: string
}

interface Props {
  transaction: Transaction
}

export default function ManualPayment({ transaction }: Props) {
  const { t } = useTranslation()
  const [copied, setCopied] = useState(false)
  const fileRef = useRef<HTMLInputElement>(null)
  const [preview, setPreview] = useState<string | null>(null)

  const { data, setData, post, processing, errors } = useForm({
    sender_number:           '',
    external_transaction_id: '',
    screenshot:              null as File | null,
  })

  function copyNumber() {
    navigator.clipboard.writeText(transaction.merchant_number)
    setCopied(true)
    setTimeout(() => setCopied(false), 2000)
  }

  function handleFileChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0] ?? null
    setData('screenshot', file)
    if (file) {
      const reader = new FileReader()
      reader.onload = ev => setPreview(ev.target?.result as string)
      reader.readAsDataURL(file)
    } else {
      setPreview(null)
    }
  }

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    post(route('upgrade.manual.submit', transaction.transaction_no), {
      forceFormData: true,
    })
  }

  const isManual = ['manual', 'bkash', 'nagad'].includes(transaction.gateway_type)

  return (
    <AppLayout>
      <Head title={t('pricing', 'payment_title')} />

      <div className="max-w-lg mx-auto">
        <h1 className="text-xl font-bold text-slate-900 mb-6">
          {t('pricing', 'payment_title')}
        </h1>

        {/* Order summary */}
        <div className="rounded-2xl border border-slate-200 bg-white p-5 mb-6">
          <div className="flex justify-between items-center mb-3 pb-3 border-b border-slate-100">
            <span className="text-sm text-slate-500">{t('admin', 'plan_name')}</span>
            <span className="text-sm font-semibold text-slate-900">{transaction.plan_name}</span>
          </div>
          <div className="flex justify-between items-center mb-3 pb-3 border-b border-slate-100">
            <span className="text-sm text-slate-500">{t('pricing', 'payment_method')}</span>
            <span className="text-sm font-semibold text-slate-900">{transaction.gateway_name}</span>
          </div>
          <div className="flex justify-between items-center">
            <span className="text-sm font-bold text-slate-700">{t('admin', 'amount')}</span>
            <span className="text-xl font-extrabold text-primary-600">
              ৳{transaction.amount.toLocaleString('en-BD')}
            </span>
          </div>
        </div>

        {/* Payment instructions */}
        {isManual && transaction.merchant_number && (
          <div className="rounded-2xl bg-amber-50 border border-amber-200 p-5 mb-6">
            <h2 className="text-sm font-bold text-amber-900 mb-3">
              {t('pricing', 'payment_instructions')}
            </h2>
            <p className="text-sm text-amber-800 mb-4">
              {transaction.gateway_type === 'bkash'
                ? t('pricing', 'bkash_instructions', {
                    amount: transaction.amount.toLocaleString('en-BD'),
                    number: transaction.merchant_number,
                  })
                : t('pricing', 'nagad_instructions', {
                    amount: transaction.amount.toLocaleString('en-BD'),
                    number: transaction.merchant_number,
                  })}
            </p>

            {/* Merchant number copy */}
            <div className="flex items-center gap-3 rounded-xl bg-white border border-amber-200 px-4 py-3">
              <div>
                <p className="text-xs text-slate-400 mb-0.5">{transaction.gateway_name} Number</p>
                <p className="text-lg font-bold text-slate-900 tracking-wide">
                  {transaction.merchant_number}
                </p>
              </div>
              <button
                type="button"
                onClick={copyNumber}
                className="ml-auto flex items-center gap-1.5 rounded-lg bg-amber-100 hover:bg-amber-200 px-3 py-1.5 text-xs font-medium text-amber-800 transition-colors"
              >
                {copied ? <CheckCircle size={13} /> : <Copy size={13} />}
                {copied ? 'Copied!' : 'Copy'}
              </button>
            </div>
          </div>
        )}

        {/* Submission form */}
        <form onSubmit={handleSubmit} className="rounded-2xl border border-slate-200 bg-white p-5 space-y-5">
          <h2 className="text-sm font-bold text-slate-900">
            Submit Payment Details
          </h2>

          {/* Sender number */}
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">
              {t('pricing', 'sender_number')}
              <span className="text-red-500 ml-0.5">*</span>
            </label>
            <Input
              type="tel"
              placeholder="01XXXXXXXXX"
              value={data.sender_number}
              onChange={e => setData('sender_number', e.target.value)}
              className={errors.sender_number ? 'border-red-400' : ''}
            />
            {errors.sender_number && (
              <p className="mt-1 text-xs text-red-600">{errors.sender_number}</p>
            )}
          </div>

          {/* Transaction ID */}
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">
              {t('pricing', 'transaction_id')}
              <span className="text-red-500 ml-0.5">*</span>
            </label>
            <Input
              type="text"
              placeholder="e.g. 8A2B3C4D5E"
              value={data.external_transaction_id}
              onChange={e => setData('external_transaction_id', e.target.value.toUpperCase())}
              className={cn('font-mono', errors.external_transaction_id ? 'border-red-400' : '')}
            />
            <p className="mt-1 text-xs text-slate-400">
              {t('pricing', 'transaction_id_hint')}
            </p>
            {errors.external_transaction_id && (
              <p className="mt-1 text-xs text-red-600">{errors.external_transaction_id}</p>
            )}
          </div>

          {/* Screenshot upload */}
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">
              {t('pricing', 'screenshot')}
            </label>
            <input
              ref={fileRef}
              type="file"
              accept="image/jpeg,image/png,image/webp"
              onChange={handleFileChange}
              className="hidden"
            />

            {preview ? (
              <div className="relative">
                <img
                  src={preview}
                  alt="Payment screenshot"
                  className="w-full max-h-48 object-contain rounded-xl border border-slate-200"
                />
                <button
                  type="button"
                  onClick={() => { setPreview(null); setData('screenshot', null); if (fileRef.current) fileRef.current.value = '' }}
                  className="absolute top-2 right-2 rounded-full bg-white/90 px-2 py-1 text-xs text-red-600 border border-red-200 hover:bg-red-50"
                >
                  Remove
                </button>
              </div>
            ) : (
              <button
                type="button"
                onClick={() => fileRef.current?.click()}
                className="w-full flex flex-col items-center gap-2 rounded-xl border-2 border-dashed border-slate-200 hover:border-slate-300 py-6 transition-colors"
              >
                <Upload size={20} className="text-slate-400" />
                <span className="text-sm text-slate-400">Click to upload screenshot</span>
                <span className="text-xs text-slate-300">JPG, PNG, WebP · max 2MB</span>
              </button>
            )}
            {errors.screenshot && (
              <p className="mt-1 text-xs text-red-600">{errors.screenshot}</p>
            )}
          </div>

          <Button
            type="submit"
            className="w-full"
            size="lg"
            isLoading={processing}
            disabled={!data.sender_number || !data.external_transaction_id}
          >
            {t('pricing', 'submit_payment')}
          </Button>
        </form>

        <p className="text-center text-xs text-slate-400 mt-4">
          Transaction ref: <span className="font-mono">{transaction.transaction_no}</span>
        </p>
      </div>
    </AppLayout>
  )
}

function cn(...classes: (string | false | undefined | null)[]) {
  return classes.filter(Boolean).join(' ')
}
