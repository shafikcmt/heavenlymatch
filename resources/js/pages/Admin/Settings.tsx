/// <reference path="../../types/ziggy.d.ts" />
import { useRef, useState } from 'react'
import { Head, router } from '@inertiajs/react'
import { useForm } from '@inertiajs/react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { Upload, Trash2 } from 'lucide-react'

interface Gateway {
  id: number
  name: string
  slug: string
  merchant_id: string | null
  type: string
}

interface Props {
  settings: Record<string, string>
  gateways: Gateway[]
  mediaUrls: Record<string, string | null>
}

export default function Settings({ settings, gateways, mediaUrls }: Props) {
  const { t } = useTranslation()

  const [uploadBusy, setUploadBusy] = useState<string | null>(null)
  const [flash, setFlash] = useState<string | null>(null)
  const heroRef = useRef<HTMLInputElement>(null)
  const successRef = useRef<HTMLInputElement>(null)
  const testimonialRef = useRef<HTMLInputElement>(null)

  const inputRefs: Record<string, React.RefObject<HTMLInputElement | null>> = {
    hero: heroRef,
    success: successRef,
    testimonial: testimonialRef,
  }

  const uploadImage = (key: string) => {
    const ref = inputRefs[key]
    const file = ref?.current?.files?.[0]
    if (!file) return
    setUploadBusy(key)
    const fd = new FormData()
    fd.append('image', file)
    router.post(route('admin.settings.media.upload', { key }), fd, {
      onFinish: () => {
        setUploadBusy(null)
        if (ref?.current) ref.current.value = ''
      },
    })
  }

  const removeImage = (key: string) => {
    if (!confirm(t('admin', 'image_remove_btn') + '?')) return
    setUploadBusy(key)
    router.delete(route('admin.settings.media.remove', { key }), {
      onFinish: () => setUploadBusy(null),
    })
  }

  const { data, setData, put, processing, errors, recentlySuccessful } = useForm({
    settings: {
      general: {
        site_name:        settings['general.site_name'] ?? '',
        maintenance_mode: settings['general.maintenance_mode'] ?? '0',
        support_email:    settings['general.support_email'] ?? '',
      },
      notification: {
        mail_from_name:    settings['notification.mail_from_name'] ?? '',
        mail_from_address: settings['notification.mail_from_address'] ?? '',
      },
    },
    gateways: gateways.map(gw => ({
      id:          gw.id,
      merchant_id: gw.merchant_id ?? '',
    })),
  })

  function submit(e: React.FormEvent) {
    e.preventDefault()
    put(route('admin.settings.update'))
  }

  function setGatewayMerchantId(index: number, value: string) {
    const updated = data.gateways.map((gw, i) =>
      i === index ? { id: gw.id, merchant_id: value } : gw
    )
    setData('gateways', updated)
  }

  return (
    <AdminLayout>
      <Head title={t('admin', 'settings_title')} />

      <div className="max-w-xl space-y-6">
        <h1 className="text-xl font-bold text-slate-900">{t('admin', 'settings_title')}</h1>

        {recentlySuccessful && (
          <div className="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
            {t('admin', 'settings_saved')}
          </div>
        )}

        <form onSubmit={submit} className="space-y-6">
          {/* General settings */}
          <section className="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 className="text-sm font-semibold text-slate-900 mb-4">
              {t('admin', 'settings_group_general')}
            </h2>
            <div className="space-y-4">
              <FormField
                label={t('admin', 'settings_site_name')}
                error={errors['settings.general.site_name']}
              >
                <input
                  type="text"
                  value={data.settings.general.site_name}
                  onChange={e => setData('settings', { ...data.settings, general: { ...data.settings.general, site_name: e.target.value } })}
                  className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
              </FormField>

              <FormField
                label={t('admin', 'settings_support_email')}
                error={errors['settings.general.support_email']}
              >
                <input
                  type="email"
                  value={data.settings.general.support_email}
                  onChange={e => setData('settings', { ...data.settings, general: { ...data.settings.general, support_email: e.target.value } })}
                  className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
              </FormField>

              <FormField label={t('admin', 'settings_maintenance')}>
                <label className="flex items-center gap-3 cursor-pointer">
                  <div
                    className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                      data.settings.general.maintenance_mode === '1' ? 'bg-red-500' : 'bg-slate-300'
                    }`}
                    onClick={() => setData('settings', {
                      ...data.settings,
                      general: {
                        ...data.settings.general,
                        maintenance_mode: data.settings.general.maintenance_mode === '1' ? '0' : '1',
                      },
                    })}
                  >
                    <span className={`inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform ${
                      data.settings.general.maintenance_mode === '1' ? 'translate-x-6' : 'translate-x-1'
                    }`} />
                  </div>
                  <span className="text-sm text-slate-600">
                    {data.settings.general.maintenance_mode === '1' ? 'On' : 'Off'}
                  </span>
                </label>
              </FormField>
            </div>
          </section>

          {/* Email notification settings */}
          <section className="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 className="text-sm font-semibold text-slate-900 mb-4">
              {t('admin', 'settings_group_notification')}
            </h2>
            <div className="space-y-4">
              <FormField
                label={t('admin', 'settings_mail_from_name')}
                error={errors['settings.notification.mail_from_name']}
              >
                <input
                  type="text"
                  value={data.settings.notification.mail_from_name}
                  onChange={e => setData('settings', { ...data.settings, notification: { ...data.settings.notification, mail_from_name: e.target.value } })}
                  className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
              </FormField>

              <FormField
                label={t('admin', 'settings_mail_from_address')}
                error={errors['settings.notification.mail_from_address']}
              >
                <input
                  type="email"
                  value={data.settings.notification.mail_from_address}
                  onChange={e => setData('settings', { ...data.settings, notification: { ...data.settings.notification, mail_from_address: e.target.value } })}
                  className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
              </FormField>
            </div>
          </section>

          {/* Payment gateways */}
          {gateways.length > 0 && (
            <section className="rounded-2xl border border-slate-200 bg-white p-5">
              <h2 className="text-sm font-semibold text-slate-900 mb-4">
                {t('admin', 'settings_group_payment')}
              </h2>
              <div className="space-y-4">
                {gateways.map((gw, idx) => (
                  <FormField
                    key={gw.id}
                    label={`${gw.name} — ${t('admin', 'settings_merchant_number')}`}
                  >
                    <input
                      type="text"
                      value={data.gateways[idx]?.merchant_id ?? ''}
                      onChange={e => setGatewayMerchantId(idx, e.target.value)}
                      placeholder="01XXXXXXXXX"
                      className="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"
                    />
                  </FormField>
                ))}
              </div>
            </section>
          )}

          <Button type="submit" isLoading={processing} className="w-full">
            {t('common', 'save') || 'Save Settings'}
          </Button>
        </form>

        {/* Homepage images — separate from PUT form */}
        <section className="rounded-2xl border border-slate-200 bg-white p-5 mt-6">
          <h2 className="text-sm font-semibold text-slate-900 mb-4">
            {t('admin', 'settings_group_homepage')}
          </h2>
          <div className="space-y-6">
            {([
              { key: 'hero',        labelKey: 'settings_hero_image' },
              { key: 'success',     labelKey: 'settings_success_image' },
              { key: 'testimonial', labelKey: 'settings_testimonial_image' },
            ] as const).map(({ key, labelKey }) => (
              <div key={key}>
                <p className="text-sm font-medium text-slate-700 mb-2">{t('admin', labelKey)}</p>
                {mediaUrls[key] ? (
                  <div className="flex items-start gap-3">
                    <img
                      src={mediaUrls[key]!}
                      alt=""
                      className="h-16 w-24 object-cover rounded-lg border border-slate-200"
                      onError={e => { (e.target as HTMLImageElement).style.display = 'none' }}
                    />
                    <button
                      type="button"
                      disabled={uploadBusy === key}
                      onClick={() => removeImage(key)}
                      className="inline-flex items-center gap-1.5 text-xs text-red-600 hover:text-red-700 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50 transition-colors disabled:opacity-50"
                    >
                      <Trash2 size={13} />
                      {t('admin', 'image_remove_btn')}
                    </button>
                  </div>
                ) : (
                  <p className="text-xs text-slate-400 italic mb-2">{t('admin', 'no_image_uploaded')}</p>
                )}
                <div className="flex items-center gap-2 mt-2">
                  <input
                    ref={inputRefs[key] as React.RefObject<HTMLInputElement>}
                    type="file"
                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                    className="block text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-slate-200 file:text-xs file:font-medium file:bg-slate-50 hover:file:bg-slate-100 file:cursor-pointer"
                  />
                  <button
                    type="button"
                    disabled={uploadBusy === key}
                    onClick={() => uploadImage(key)}
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg px-3 py-1.5 transition-colors disabled:opacity-50"
                  >
                    <Upload size={13} />
                    {uploadBusy === key ? '…' : t('admin', 'image_upload_btn')}
                  </button>
                </div>
              </div>
            ))}
          </div>
        </section>
      </div>
    </AdminLayout>
  )
}

function FormField({
  label,
  error,
  children,
}: {
  label: string
  error?: string
  children: React.ReactNode
}) {
  return (
    <div>
      <label className="block text-sm font-medium text-slate-700 mb-1">{label}</label>
      {children}
      {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
    </div>
  )
}
