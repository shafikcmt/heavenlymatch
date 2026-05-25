/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm, usePage } from '@inertiajs/react'
import { useRef, useState } from 'react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { Star, Trash2, Upload, ImageOff, AlertCircle, Shield, CheckCircle, X, UserCheck } from 'lucide-react'
import { cn } from '@/lib/utils'
import type { PageProps } from '@/types'

interface Photo {
  path: string
  is_primary: boolean
  uploaded_at: string
}

interface IncomingRequest {
  id: number
  requester_id: string
  requester_name: string
  created_at: string
}

interface Props {
  photos: Photo[]
  photoUrls: string[]
  photoVisibility: 'public' | 'members_only' | 'blurred'
  maxPhotos: number
  hasBiodata: boolean
  incomingRequests: IncomingRequest[]
}

export default function Photos({ photos, photoUrls, photoVisibility, maxPhotos, hasBiodata, incomingRequests }: Props) {
  const { t } = useTranslation()
  const { flash, auth } = usePage<PageProps>().props
  const fileInputRef = useRef<HTMLInputElement>(null)
  const [deletingIndex, setDeletingIndex] = useState<number | null>(null)
  const [previewUrl, setPreviewUrl] = useState<string | null>(null)
  const [previewError, setPreviewError] = useState<string | null>(null)

  const canUpload = photos.length < maxPhotos
  const isIslamic = auth.user?.platform_mode === 'islamic'

  const { data, setData, post, processing, errors, reset } = useForm<{ photo: File | null }>({
    photo: null,
  })

  function handleFileChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0]
    if (!file) return

    setPreviewError(null)

    if (file.size > 4 * 1024 * 1024) {
      setPreviewError(t('biodata', 'photo_too_large'))
      if (fileInputRef.current) fileInputRef.current.value = ''
      return
    }

    setData('photo', file)
    setPreviewUrl(URL.createObjectURL(file))
  }

  function handleUploadConfirm() {
    post(route('profile.photos.store'), {
      forceFormData: true,
      onSuccess: () => {
        if (previewUrl) URL.revokeObjectURL(previewUrl)
        setPreviewUrl(null)
        setPreviewError(null)
        reset()
        if (fileInputRef.current) fileInputRef.current.value = ''
      },
    })
  }

  function handlePreviewCancel() {
    if (previewUrl) URL.revokeObjectURL(previewUrl)
    setPreviewUrl(null)
    setPreviewError(null)
    reset()
    if (fileInputRef.current) fileInputRef.current.value = ''
  }

  function handleDelete(index: number) {
    if (!confirm(t('biodata', 'photo_delete_confirm'))) return
    setDeletingIndex(index)
    router.delete(route('profile.photos.destroy', index), {
      onFinish: () => setDeletingIndex(null),
    })
  }

  function handleSetPrimary(index: number) {
    router.put(route('profile.photos.primary', index))
  }

  function handleVisibilityChange(value: string) {
    router.put(route('profile.photos.visibility'), { photo_visibility: value }, {
      preserveScroll: true,
    })
  }

  return (
    <AppLayout>
      <Head title={t('biodata', 'photos_title')} />

      <div className="max-w-2xl mx-auto pt-4 pb-10 space-y-6">
        <h1 className="text-xl font-bold text-slate-900">{t('biodata', 'photos_title')}</h1>

        {/* Flash messages */}
        {flash.success && (
          <div className="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <CheckCircle size={18} className="text-emerald-600 shrink-0" />
            <p className="text-sm text-emerald-800">{flash.success}</p>
          </div>
        )}
        {flash.error && (
          <div className="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
            <AlertCircle size={18} className="text-red-600 shrink-0" />
            <p className="text-sm text-red-800">{flash.error}</p>
          </div>
        )}

        {/* Islamic privacy notice */}
        {isIslamic && (
          <div className="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <Shield size={18} className="text-emerald-600 mt-0.5 shrink-0" />
            <p className="text-sm text-emerald-800">{t('biodata', 'photo_privacy_islamic_notice')}</p>
          </div>
        )}

        {/* No biodata warning */}
        {!hasBiodata && (
          <div className="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
            <AlertCircle size={18} className="text-amber-600 mt-0.5 shrink-0" />
            <p className="text-sm text-amber-800">{t('biodata', 'photo_no_biodata')}</p>
          </div>
        )}

        {/* Preview panel (shown after file is selected) */}
        {previewUrl && (
          <div className="rounded-2xl border-2 border-primary-300 bg-primary-50 p-5 space-y-4">
            <p className="text-sm font-semibold text-slate-700">{t('biodata', 'photo_preview_label')}</p>
            <img
              src={previewUrl}
              alt="preview"
              className="w-40 h-40 object-cover rounded-2xl border border-slate-200 mx-auto shadow"
            />
            {errors.photo && (
              <p className="text-xs text-red-600 text-center">{errors.photo}</p>
            )}
            <div className="flex gap-3">
              <Button
                variant="outline"
                size="sm"
                className="flex-1"
                onClick={handlePreviewCancel}
                disabled={processing}
              >
                <X size={14} />
                {t('biodata', 'photo_cancel_preview')}
              </Button>
              <Button
                size="sm"
                className="flex-1"
                onClick={handleUploadConfirm}
                isLoading={processing}
                disabled={processing}
              >
                <Upload size={14} />
                {t('biodata', 'photo_confirm_upload')}
              </Button>
            </div>
          </div>
        )}

        {/* Upload drop zone (hidden when preview is showing) */}
        {hasBiodata && !previewUrl && (
          <div
            className={cn(
              'rounded-2xl border-2 border-dashed p-6 text-center transition-colors',
              canUpload
                ? 'border-slate-200 hover:border-primary-400 cursor-pointer'
                : 'border-slate-100 bg-slate-50 opacity-60',
            )}
            onClick={() => canUpload && fileInputRef.current?.click()}
          >
            <input
              ref={fileInputRef}
              type="file"
              accept="image/jpeg,image/png,image/webp"
              className="hidden"
              onChange={handleFileChange}
              disabled={!canUpload || processing}
            />

            <Upload size={28} className={cn('mx-auto mb-3', canUpload ? 'text-primary-500' : 'text-slate-400')} />
            <p className="text-sm font-medium text-slate-700 mb-1">
              {canUpload
                ? t('biodata', 'photo_upload_btn')
                : t('biodata', 'photo_limit_reached', { max: String(maxPhotos) })}
            </p>
            <p className="text-xs text-slate-400">
              {t('biodata', 'photo_count', { count: String(photos.length), max: String(maxPhotos) })}
              {' · '}
              {t('biodata', 'photo_file_hint')}
            </p>

            {previewError && (
              <p className="mt-2 text-xs text-red-600">{previewError}</p>
            )}
          </div>
        )}

        {/* Photo grid */}
        {photos.length === 0 ? (
          <div className="flex flex-col items-center gap-3 py-12 text-slate-400">
            <ImageOff size={36} />
            <p className="text-sm">{t('biodata', 'photo_no_photos')}</p>
          </div>
        ) : (
          <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
            {photos.map((photo, index) => (
              <PhotoCard
                key={index}
                src={photoUrls[index] ?? ''}
                isPrimary={photo.is_primary}
                isDeleting={deletingIndex === index}
                onSetPrimary={() => handleSetPrimary(index)}
                onDelete={() => handleDelete(index)}
                primaryLabel={t('biodata', 'photo_primary_badge')}
                setPrimaryLabel={t('biodata', 'photo_set_primary')}
                deleteLabel={t('biodata', 'photo_delete')}
              />
            ))}
          </div>
        )}

        {/* Visibility setting */}
        <section className="rounded-2xl border border-slate-200 bg-white p-5">
          <h2 className="text-sm font-semibold text-slate-900 mb-1">
            {t('biodata', 'photo_visibility')}
          </h2>
          <p className="text-xs text-slate-500 mb-4">{t('biodata', 'photo_visibility_hint')}</p>

          <div className="flex flex-col gap-2">
            {([
              { value: 'public',       label: t('biodata', 'photo_public'),   desc: t('biodata', 'photo_vis_public_desc') },
              { value: 'members_only', label: t('biodata', 'photo_members'),  desc: t('biodata', 'photo_vis_members_desc') },
              { value: 'blurred',      label: t('biodata', 'photo_blurred'),  desc: t('biodata', 'photo_vis_blurred_desc') },
            ] as const).map(({ value, label, desc }) => (
              <label
                key={value}
                className={cn(
                  'flex items-start gap-3 rounded-xl border px-4 py-3 cursor-pointer transition-colors',
                  photoVisibility === value
                    ? 'border-primary-500 bg-primary-50'
                    : 'border-slate-200 hover:bg-slate-50',
                )}
              >
                <input
                  type="radio"
                  name="photo_visibility"
                  value={value}
                  checked={photoVisibility === value}
                  onChange={() => handleVisibilityChange(value)}
                  className="accent-primary-600 mt-0.5"
                />
                <div>
                  <p className="text-sm font-medium text-slate-800">{label}</p>
                  <p className="text-xs text-slate-400">{desc}</p>
                </div>
              </label>
            ))}
          </div>

          {isIslamic && photoVisibility !== 'blurred' && (
            <div className="flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 mt-3">
              <Shield size={14} className="text-emerald-600 mt-0.5 shrink-0" />
              <p className="text-xs text-emerald-800">{t('biodata', 'photo_privacy_islamic_notice')}</p>
            </div>
          )}
        </section>

        {/* Incoming photo access requests */}
        {incomingRequests.length > 0 && (
          <section className="rounded-2xl border border-slate-200 bg-white p-5">
            <div className="flex items-center gap-2 mb-4">
              <UserCheck size={18} className="text-primary-600" />
              <h2 className="text-sm font-semibold text-slate-900">
                {t('dashboard', 'photo_access_requests_title')}
              </h2>
              <span className="ml-auto text-xs font-bold text-white bg-primary-500 rounded-full px-2 py-0.5">
                {incomingRequests.length}
              </span>
            </div>
            <div className="space-y-3">
              {incomingRequests.map(req => (
                <div
                  key={req.id}
                  className="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3"
                >
                  <div className="min-w-0">
                    <p className="text-sm font-medium text-slate-800 truncate">
                      {t('dashboard', 'photo_access_requester', { name: req.requester_name })}
                    </p>
                    <p className="text-xs text-slate-400">{req.created_at}</p>
                  </div>
                  <div className="flex gap-2 shrink-0">
                    <button
                      onClick={() => router.post(
                        route('profile.photos.requests.respond', req.id),
                        { action: 'granted' },
                        { preserveScroll: true },
                      )}
                      className="rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 transition-colors"
                    >
                      {t('dashboard', 'photo_access_approve')}
                    </button>
                    <button
                      onClick={() => router.post(
                        route('profile.photos.requests.respond', req.id),
                        { action: 'denied' },
                        { preserveScroll: true },
                      )}
                      className="rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold px-3 py-1.5 transition-colors"
                    >
                      {t('dashboard', 'photo_access_deny')}
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </section>
        )}
      </div>
    </AppLayout>
  )
}

function PhotoCard({
  src,
  isPrimary,
  isDeleting,
  onSetPrimary,
  onDelete,
  primaryLabel,
  setPrimaryLabel,
  deleteLabel,
}: {
  src: string
  isPrimary: boolean
  isDeleting: boolean
  onSetPrimary: () => void
  onDelete: () => void
  primaryLabel: string
  setPrimaryLabel: string
  deleteLabel: string
}) {
  return (
    <div className={cn(
      'relative rounded-2xl overflow-hidden border-2 bg-slate-100 aspect-square group',
      isPrimary ? 'border-primary-500 shadow-md' : 'border-transparent',
    )}>
      <img
        src={src}
        alt=""
        className="w-full h-full object-cover"
        loading="lazy"
      />

      {isPrimary && (
        <div className="absolute top-2 left-2 flex items-center gap-1 rounded-full bg-primary-600 px-2 py-0.5 text-xs font-semibold text-white shadow">
          <Star size={10} fill="currentColor" />
          {primaryLabel}
        </div>
      )}

      <div className="absolute inset-0 flex flex-col items-center justify-end gap-2 p-3 bg-black/0 group-hover:bg-black/40 transition-all opacity-0 group-hover:opacity-100">
        {!isPrimary && (
          <button
            onClick={onSetPrimary}
            className="w-full rounded-xl bg-white/90 py-1.5 text-xs font-semibold text-slate-800 hover:bg-white transition-colors"
          >
            {setPrimaryLabel}
          </button>
        )}
        <button
          onClick={onDelete}
          disabled={isDeleting}
          className="w-full rounded-xl bg-red-500/90 py-1.5 text-xs font-semibold text-white hover:bg-red-600 transition-colors flex items-center justify-center gap-1 disabled:opacity-60"
        >
          <Trash2 size={12} />
          {isDeleting ? '…' : deleteLabel}
        </button>
      </div>
    </div>
  )
}
