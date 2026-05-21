/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import { useRef, useState } from 'react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { Star, Trash2, Upload, ImageOff, AlertCircle } from 'lucide-react'
import { cn } from '@/lib/utils'

interface Photo {
  path: string
  is_primary: boolean
  uploaded_at: string
}

interface Props {
  photos: Photo[]
  photoUrls: string[]
  photoVisibility: 'public' | 'members_only' | 'blurred'
  maxPhotos: number
  hasBiodata: boolean
}

export default function Photos({ photos, photoUrls, photoVisibility, maxPhotos, hasBiodata }: Props) {
  const { t } = useTranslation()
  const fileInputRef = useRef<HTMLInputElement>(null)
  const [deletingIndex, setDeletingIndex] = useState<number | null>(null)

  const canUpload = photos.length < maxPhotos

  // Upload form
  const { data, setData, post, processing, errors, reset } = useForm<{ photo: File | null }>({
    photo: null,
  })

  function handleFileChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0]
    if (!file) return
    setData('photo', file)
    post(route('profile.photos.store'), {
      forceFormData: true,
      onSuccess: () => {
        reset()
        if (fileInputRef.current) fileInputRef.current.value = ''
      },
    })
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

        {/* No biodata warning */}
        {!hasBiodata && (
          <div className="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
            <AlertCircle size={18} className="text-amber-600 mt-0.5 shrink-0" />
            <p className="text-sm text-amber-800">{t('biodata', 'photo_no_biodata')}</p>
          </div>
        )}

        {/* Upload area */}
        {hasBiodata && (
          <div className={cn(
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

            {processing ? (
              <div className="flex flex-col items-center gap-2">
                <div className="h-8 w-8 rounded-full border-2 border-primary-600 border-t-transparent animate-spin" />
                <p className="text-sm text-slate-500">Uploading…</p>
              </div>
            ) : (
              <>
                <Upload size={28} className={cn('mx-auto mb-3', canUpload ? 'text-primary-500' : 'text-slate-400')} />
                <p className="text-sm font-medium text-slate-700 mb-1">
                  {canUpload
                    ? t('biodata', 'photo_upload_btn')
                    : t('biodata', 'photo_limit_reached', { max: String(maxPhotos) })}
                </p>
                <p className="text-xs text-slate-400">
                  {t('biodata', 'photo_count', {
                    count: String(photos.length),
                    max: String(maxPhotos),
                  })}
                  {' · '}JPG, PNG, WebP · max 4 MB
                </p>
              </>
            )}

            {errors.photo && (
              <p className="mt-2 text-xs text-red-600">{errors.photo}</p>
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
            {(['public', 'members_only', 'blurred'] as const).map(val => (
              <label
                key={val}
                className={cn(
                  'flex items-center gap-3 rounded-xl border px-4 py-3 cursor-pointer transition-colors',
                  photoVisibility === val
                    ? 'border-primary-500 bg-primary-50'
                    : 'border-slate-200 hover:bg-slate-50',
                )}
              >
                <input
                  type="radio"
                  name="photo_visibility"
                  value={val}
                  checked={photoVisibility === val}
                  onChange={() => handleVisibilityChange(val)}
                  className="accent-primary-600"
                />
                <div>
                  <p className="text-sm font-medium text-slate-800">
                    {t('biodata', val === 'public' ? 'photo_public' : val === 'members_only' ? 'photo_members' : 'photo_blurred')}
                  </p>
                  <p className="text-xs text-slate-400">
                    {val === 'public' && 'Anyone can see your photos'}
                    {val === 'members_only' && 'Only accepted connections can see clearly'}
                    {val === 'blurred' && 'Photos are blurred until you approve requests'}
                  </p>
                </div>
              </label>
            ))}
          </div>
        </section>
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

      {/* Primary badge */}
      {isPrimary && (
        <div className="absolute top-2 left-2 flex items-center gap-1 rounded-full bg-primary-600 px-2 py-0.5 text-xs font-semibold text-white shadow">
          <Star size={10} fill="currentColor" />
          {primaryLabel}
        </div>
      )}

      {/* Action overlay */}
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
