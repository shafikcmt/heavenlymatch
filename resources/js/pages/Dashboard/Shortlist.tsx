/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { calcAge, cmToFeetInches } from '@/lib/utils'
import { type PaginatedResponse } from '@/types'
import { Heart, EyeOff } from 'lucide-react'
import { useTranslation } from '@/lib/i18n'

interface ShortlistedProfile {
  registration_id: string
  name: string
  gender: string
  platform_mode: string
  photo_visibility?: string
  district?: string
  division?: string
  occupation?: string
  height_cm?: number
  birth_date?: string
  photo_url?: string | null
  has_photo?: boolean
  shortlisted_at: string
}

interface Props {
  shortlisted: PaginatedResponse<ShortlistedProfile>
}

export default function Shortlist({ shortlisted }: Props) {
  const { t } = useTranslation()

  const removeFromShortlist = (id: string) =>
    router.post(route('shortlist.toggle'), { target_id: id }, { preserveScroll: true })

  return (
    <AppLayout>
      <Head title={t('dashboard', 'shortlist_title')} />

      <div className="max-w-5xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-slate-900 mb-6">
          {t('dashboard', 'shortlist_title')}
          <span className="ml-2 text-base font-normal text-slate-400">
            ({shortlisted.total})
          </span>
        </h1>

        {shortlisted.data.length === 0 ? (
          <div className="text-center py-20">
            <Heart size={40} className="mx-auto text-slate-300 mb-4" />
            <p className="text-slate-500 mb-4">{t('dashboard', 'no_shortlist')}</p>
            <Link href={route('search.index')}>
              <Button>{t('dashboard', 'browse_profiles')}</Button>
            </Link>
          </div>
        ) : (
          <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {shortlisted.data.map(profile => {
                const age     = profile.birth_date ? calcAge(profile.birth_date) : null
                const blurred = profile.photo_visibility === 'blurred'

                return (
                  <div
                    key={profile.registration_id}
                    className="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow"
                  >
                    <Link href={route('profile.show', { registrationId: profile.registration_id })}>
                      {/* Photo area */}
                      <div className="aspect-[3/4] bg-slate-100 relative flex items-center justify-center overflow-hidden">
                        {profile.has_photo && profile.photo_url && !blurred ? (
                          <img
                            src={profile.photo_url}
                            alt={profile.name}
                            className="w-full h-full object-cover transition-transform hover:scale-105 duration-300"
                            onError={e => { (e.target as HTMLImageElement).style.display = 'none' }}
                          />
                        ) : blurred ? (
                          <div className="absolute inset-0 flex flex-col items-center justify-center bg-slate-200">
                            <EyeOff size={24} className="text-slate-400 mb-1" />
                            <p className="text-xs text-slate-500 text-center px-3">
                              {t('dashboard', 'photo_hidden_msg')}
                            </p>
                          </div>
                        ) : (
                          <span className="text-6xl">
                            {profile.gender === 'male' ? '👨' : '👩'}
                          </span>
                        )}

                        {profile.platform_mode === 'islamic' && (
                          <span className="absolute top-2 right-2 text-xs bg-emerald-500 text-white px-2 py-0.5 rounded-full">
                            {t('dashboard', 'halal_badge')}
                          </span>
                        )}
                      </div>

                      {/* Info */}
                      <div className="p-4">
                        <p className="font-semibold text-slate-900 truncate">{profile.name}</p>
                        <p className="text-sm text-slate-500 mt-0.5">
                          {[
                            age ? `${age} yrs` : null,
                            profile.district,
                            profile.occupation,
                          ].filter(Boolean).join(' · ')}
                        </p>
                        {profile.height_cm && (
                          <p className="text-xs text-slate-400 mt-1">{cmToFeetInches(profile.height_cm)}</p>
                        )}
                      </div>
                    </Link>

                    {/* Actions */}
                    <div className="px-4 pb-4 flex gap-2">
                      <Link
                        href={route('profile.show', { registrationId: profile.registration_id })}
                        className="flex-1"
                      >
                        <Button variant="outline" size="sm" className="w-full">
                          {t('dashboard', 'view_profile')}
                        </Button>
                      </Link>
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => removeFromShortlist(profile.registration_id)}
                        className="text-red-500 hover:text-red-600 hover:bg-red-50 shrink-0"
                      >
                        {t('dashboard', 'shortlist_remove')}
                      </Button>
                    </div>
                  </div>
                )
              })}
            </div>

            {/* Pagination */}
            {shortlisted.last_page > 1 && (
              <div className="flex items-center justify-center gap-2 mt-10">
                {shortlisted.current_page > 1 && (
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => router.get(shortlisted.prev_page_url ?? '', {}, { preserveScroll: true })}
                  >
                    ← {t('common', 'previous')}
                  </Button>
                )}
                <span className="text-sm text-slate-500">
                  {shortlisted.current_page} / {shortlisted.last_page}
                </span>
                {shortlisted.current_page < shortlisted.last_page && (
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => router.get(shortlisted.next_page_url ?? '', {}, { preserveScroll: true })}
                  >
                    {t('common', 'next')} →
                  </Button>
                )}
              </div>
            )}
          </>
        )}
      </div>
    </AppLayout>
  )
}
