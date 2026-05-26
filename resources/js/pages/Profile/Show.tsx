/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link, router, usePage } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { calcAge, cmToFeetInches, scoreColor } from '@/lib/utils'
import { Heart, Send, Eye, EyeOff, Flag, Edit, ChevronLeft, ChevronRight, X, Shield, CheckCircle2, Star, Mail, ShieldCheck, AlertTriangle, MessageCircle } from 'lucide-react'
import { useState } from 'react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import type { PageProps } from '@/types'

interface BiodataDetail {
  marital_status?: string
  birth_date?: string
  height_cm?: number
  weight_kg?: number
  complexion?: string
  blood_group?: string
  about_me?: string
  profile_headline?: string
  // Location (public fields only — address/guardian stripped server-side for non-connected)
  nationality?: string
  division?: string
  district?: string
  residing_country?: string
  residing_city?: string
  grew_up_in?: string
  is_nrb?: boolean
  visa_status?: string
  // Religion
  religion?: string
  sect?: string
  fiqh?: string
  is_practicing?: boolean
  prayers_info?: string
  quran_recitation?: string
  clothing_style?: string
  beard_info?: string
  hijab_info?: string
  is_islamically_educated?: boolean
  wali_approval?: boolean
  sunni_scale?: number
  beliefs_on_mazar?: string
  favorite_scholars?: string
  // Education
  education_method?: string
  highest_qualification?: string
  occupation?: string
  occupation_category?: string
  monthly_income?: number
  profession_details?: string
  // Family
  father_name?: string
  father_alive?: boolean
  father_profession?: string
  mother_name?: string
  mother_alive?: boolean
  mother_profession?: string
  family_type?: string
  brothers?: number
  sisters?: number
  family_financial_status?: string
  family_religious_condition?: string
  family_details?: string
  // Lifestyle
  health_status?: string
  diet?: string
  smoking?: string
  hobbies?: string
  watch_entertainment?: string
  // Partner
  partner_age_min?: number
  partner_age_max?: number
  partner_height_cm_min?: number
  partner_height_cm_max?: number
  partner_complexion?: string
  partner_marital_status?: string
  partner_education?: string
  partner_expectations?: string
  partner_division?: string
  partner_district?: string
  // Score
  completeness_score?: number
}

interface ProfileData {
  registration_id: string
  name: string
  gender: string
  platform_mode: string
  email_verified_at?: string
}

interface ProfileTrust {
  isEmailVerified: boolean
  isIdentityVerified: boolean
  biodataApproved: boolean
  isPremium: boolean
}

interface Props {
  profile: ProfileData
  biodata?: BiodataDetail
  photos: Array<{ url: string; is_primary: boolean; blurred: boolean }>
  interestSent: boolean
  interestReceived: boolean
  isConnected: boolean
  conversationId?: number | null
  isShortlisted: boolean
  isOwnProfile: boolean
  isAlreadyReported: boolean
  photoAccessStatus?: 'pending' | 'granted' | 'denied' | null
  profileTrust: ProfileTrust
}

const SECTION = ({ title, children }: { title: string; children: React.ReactNode }) => (
  <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
    <div className="px-5 py-3 bg-slate-50/70 border-b border-slate-100">
      <h3 className="text-sm font-semibold text-slate-800 tracking-tight">{title}</h3>
    </div>
    <div className="px-5 py-4">
      {children}
    </div>
  </div>
)

const ROW = ({ label, value }: { label: string; value?: string | number | null }) =>
  value != null ? (
    <div className="flex items-baseline gap-3 py-2 border-b border-slate-50 last:border-0">
      <span className="text-[11px] text-slate-400 font-medium w-32 flex-shrink-0 uppercase tracking-wide">{label}</span>
      <span className="text-sm text-slate-800 font-medium flex-1">{value}</span>
    </div>
  ) : null

export default function ProfileShow({
  profile, biodata, photos,
  interestSent, interestReceived, isConnected, conversationId, isShortlisted, isOwnProfile,
  isAlreadyReported, photoAccessStatus, profileTrust,
}: Props) {
  const { completion } = usePage<PageProps>().props
  const { t } = useTranslation()

  const [shortlisted, setShortlisted]     = useState(isShortlisted)
  const [sent, setSent]                   = useState(interestSent)
  const [reportOpen, setReportOpen]       = useState(false)
  const [reportSubmitting, setReportSubmitting] = useState(false)
  const [reportSuccess, setReportSuccess] = useState(false)
  const [reportError, setReportError]     = useState<string | null>(null)
  const [showGuard, setShowGuard]         = useState(false)
  const [photoIdx, setPhotoIdx]           = useState(0)
  const [accessStatus, setAccessStatus]   = useState<'pending' | 'granted' | 'denied' | null | undefined>(photoAccessStatus)

  const requestPhotoAccess = () => {
    router.post(
      route('photo.request-access', { registrationId: profile.registration_id }),
      {},
      { preserveScroll: true, onSuccess: () => setAccessStatus('pending') },
    )
  }

  const age = biodata?.birth_date ? calcAge(biodata.birth_date) : null

  // Photo gallery
  const visiblePhotos = photos.filter(p => !p.blurred)
  const displayPhotos = photos.length > 0 ? photos : []
  const activePhoto   = displayPhotos[photoIdx] ?? null

  const prevPhoto = () => setPhotoIdx(i => Math.max(0, i - 1))
  const nextPhoto = () => setPhotoIdx(i => Math.min(displayPhotos.length - 1, i + 1))

  const sendInterest = () => {
    if (sent) return
    if (!completion?.can_send_interest) {
      setShowGuard(true)
      return
    }
    setSent(true)
    router.post(route('interests.store'), { receiver_id: profile.registration_id }, {
      preserveScroll: true,
      onError: () => setSent(false),
    })
  }

  const toggleShortlist = () => {
    router.post(route('shortlist.toggle'), { target_id: profile.registration_id }, {
      preserveScroll: true,
      onSuccess: () => setShortlisted(v => !v),
    })
  }

  return (
    <AppLayout>
      <Head title={`${profile.name} — Profile`} />

      {/* Completion guard modal */}
      {showGuard && completion && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" onClick={() => setShowGuard(false)}>
          <div className="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between mb-3">
              <h3 className="font-bold text-slate-900">{t('dashboard', 'interest_block_title')}</h3>
              <button onClick={() => setShowGuard(false)} className="text-slate-400 hover:text-slate-600"><X size={18} /></button>
            </div>
            <p className="text-sm text-slate-600 mb-5">{t('dashboard', 'interest_block_body')}</p>
            <div className="flex gap-3">
              <Button variant="outline" size="sm" className="flex-1" onClick={() => setShowGuard(false)}>
                {t('dashboard', 'interest_block_dismiss')}
              </Button>
              <Link href={completion.next_step_url} className="flex-1">
                <Button size="sm" className="w-full">{t('dashboard', 'interest_block_cta')}</Button>
              </Link>
            </div>
          </div>
        </div>
      )}

      {/* Back button */}
      <div className="max-w-4xl mx-auto px-4 pt-6">
        <button
          onClick={() => window.history.back()}
          className="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition-colors"
        >
          <ChevronLeft size={16} />
          {t('common', 'back')}
        </button>
      </div>

      <div className="max-w-4xl mx-auto px-4 py-6">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

          {/* ── Sidebar ─────────────────────────────────────────────────── */}
          <div className="space-y-4">

            {/* Photo card */}
            <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
              <div className="h-56 bg-slate-100 relative flex items-center justify-center">
                {activePhoto ? (
                  activePhoto.blurred ? (
                    <div className="absolute inset-0 flex flex-col items-center justify-center bg-slate-200 px-4">
                      <EyeOff size={32} className="text-slate-400 mb-2" />
                      <p className="text-xs text-slate-500 text-center mb-3">
                        {t('dashboard', 'photo_hidden_msg')}
                      </p>
                      {!isOwnProfile && profile.platform_mode === 'islamic' && (
                        accessStatus === 'pending' ? (
                          <span className="text-xs text-amber-700 bg-amber-100 rounded-full px-3 py-1 font-medium">
                            {t('dashboard', 'photo_request_pending_label')}
                          </span>
                        ) : accessStatus === 'granted' ? (
                          <span className="text-xs text-emerald-700 bg-emerald-100 rounded-full px-3 py-1 font-medium">
                            {t('dashboard', 'photo_request_granted_label')}
                          </span>
                        ) : accessStatus === 'denied' ? (
                          <span className="text-xs text-red-700 bg-red-100 rounded-full px-3 py-1 font-medium">
                            {t('dashboard', 'photo_request_denied_label')}
                          </span>
                        ) : (
                          <button
                            onClick={requestPhotoAccess}
                            className="text-xs font-medium text-primary-700 bg-primary-100 hover:bg-primary-200 rounded-full px-3 py-1 transition-colors"
                          >
                            {t('dashboard', 'photo_request_access')}
                          </button>
                        )
                      )}
                    </div>
                  ) : (
                    <img src={activePhoto.url} alt={profile.name} className="w-full h-full object-cover" />
                  )
                ) : (
                  <div className={`h-full w-full flex items-center justify-center ${profile.gender === 'male' ? 'bg-gradient-to-br from-blue-50 to-sky-100' : 'bg-gradient-to-br from-rose-50 to-pink-100'}`}>
                    <span className="text-6xl opacity-70">{profile.gender === 'male' ? '👨' : '👩'}</span>
                  </div>
                )}

                {/* Gallery nav */}
                {displayPhotos.length > 1 && (
                  <>
                    {photoIdx > 0 && (
                      <button
                        onClick={prevPhoto}
                        aria-label="Previous photo"
                        className="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-1 shadow hover:bg-white transition-colors"
                      >
                        <ChevronLeft size={16} />
                      </button>
                    )}
                    {photoIdx < displayPhotos.length - 1 && (
                      <button
                        onClick={nextPhoto}
                        aria-label="Next photo"
                        className="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-1 shadow hover:bg-white transition-colors"
                      >
                        <ChevronRight size={16} />
                      </button>
                    )}
                    <div className="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1">
                      {displayPhotos.map((_, i) => (
                        <button
                          key={i}
                          onClick={() => setPhotoIdx(i)}
                          aria-label={`Photo ${i + 1} of ${displayPhotos.length}`}
                          aria-current={i === photoIdx ? 'true' : undefined}
                          className={cn('h-1.5 rounded-full transition-all', i === photoIdx ? 'w-4 bg-white' : 'w-1.5 bg-white/50')}
                        />
                      ))}
                    </div>
                  </>
                )}

                {profile.platform_mode === 'islamic' && (
                  <div className="absolute top-3 right-3">
                    <Badge variant="islamic">Islamic</Badge>
                  </div>
                )}
              </div>

              <div className="p-4">
                <div className="text-center mb-3">
                  <h1 className="text-lg font-bold text-slate-900 leading-tight">{profile.name}</h1>
                  {biodata?.profile_headline && (
                    <p className="text-xs text-slate-500 mt-1 italic leading-snug">&ldquo;{biodata.profile_headline}&rdquo;</p>
                  )}
                  <p className="text-xs text-slate-400 mt-1.5">
                    {[age ? `${age} ${t('common', 'yrs')}` : null, biodata?.district, biodata?.residing_country]
                      .filter(Boolean).join(' · ')}
                  </p>
                </div>

                {/* Trust badges */}
                <div className="flex flex-wrap justify-center gap-1 mb-3">
                  {profileTrust.isEmailVerified && (
                    <span className="inline-flex items-center gap-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] px-2 py-0.5 border border-emerald-200 font-medium">
                      <Mail size={9} />
                      {t('dashboard', 'trust_email_verified')}
                    </span>
                  )}
                  {profileTrust.isIdentityVerified && (
                    <span className="inline-flex items-center gap-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] px-2 py-0.5 border border-blue-200 font-medium">
                      <ShieldCheck size={9} />
                      {t('dashboard', 'trust_id_verified')}
                    </span>
                  )}
                  {profileTrust.biodataApproved && (
                    <span className="inline-flex items-center gap-0.5 rounded-full bg-violet-50 text-violet-700 text-[10px] px-2 py-0.5 border border-violet-200 font-medium">
                      <CheckCircle2 size={9} />
                      {t('dashboard', 'trust_profile_approved')}
                    </span>
                  )}
                  {profileTrust.isPremium && (
                    <span className="inline-flex items-center gap-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] px-2 py-0.5 border border-amber-200 font-medium">
                      <Star size={9} />
                      {t('dashboard', 'trust_premium')}
                    </span>
                  )}
                </div>

                {biodata?.completeness_score != null && (
                  <div className="px-1">
                    <div className="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                      <div
                        className={cn('h-full rounded-full transition-all', scoreColor(biodata.completeness_score).replace('text-', 'bg-'))}
                        style={{ width: `${biodata.completeness_score}%` }}
                      />
                    </div>
                    <p className="text-[10px] text-slate-400 mt-1 text-center">
                      {t('dashboard', 'profile_completion_pct', { percent: biodata.completeness_score })}
                    </p>
                  </div>
                )}
              </div>
            </div>

            {/* Actions */}
            {isOwnProfile ? (
              <div className="space-y-2">
                <Link href={route('biodata.wizard')}>
                  <Button className="w-full gap-2">
                    <Edit size={16} />
                    {t('dashboard', 'edit_biodata')}
                  </Button>
                </Link>
              </div>
            ) : (
              <div className="space-y-2">
                {isConnected ? (
                  <>
                    <Button className="w-full" variant="outline" disabled>
                      ✓ {t('dashboard', 'connected_label')}
                    </Button>
                    {conversationId && (
                      <Link href={route('inbox.show', { conversationId })}>
                        <Button className="w-full gap-2">
                          <MessageCircle size={16} />
                          {t('interests', 'message')}
                        </Button>
                      </Link>
                    )}
                  </>
                ) : sent ? (
                  <Button className="w-full" variant="outline" disabled>
                    {t('dashboard', 'interest_sent_label')}
                  </Button>
                ) : interestReceived ? (
                  <div className="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center">
                    <p className="text-sm text-emerald-700 font-medium mb-2">
                      {t('dashboard', 'this_person_sent_interest')}
                    </p>
                    <Link href={route('interests.received')}>
                      <Button size="sm" className="w-full">{t('dashboard', 'accept_interest')}</Button>
                    </Link>
                  </div>
                ) : (
                  <Button className="w-full gap-2" onClick={sendInterest}>
                    <Send size={16} />
                    {t('dashboard', 'interest_send')}
                  </Button>
                )}

                <Button
                  variant="outline"
                  className={cn('w-full gap-2', shortlisted && 'text-red-500 border-red-200 bg-red-50')}
                  onClick={toggleShortlist}
                >
                  <Heart size={16} className={shortlisted ? 'fill-red-500 text-red-500' : ''} />
                  {shortlisted ? t('dashboard', 'shortlist_remove') : t('dashboard', 'shortlist_add')}
                </Button>

                {isAlreadyReported ? (
                  <p className="text-xs text-center text-slate-400 mt-1 flex items-center justify-center gap-1">
                    <Flag size={12} className="text-red-400" />
                    {t('dashboard', 'report_already_done')}
                  </p>
                ) : (
                  <button
                    onClick={() => setReportOpen(true)}
                    className="w-full text-xs text-slate-400 hover:text-red-500 flex items-center justify-center gap-1 mt-1 transition-colors"
                  >
                    <Flag size={12} /> {t('dashboard', 'report_profile')}
                  </button>
                )}

                {/* Safety notice */}
                <div className="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3">
                  <div className="flex items-center gap-1.5 mb-2">
                    <Shield size={13} className="text-amber-600 shrink-0" />
                    <p className="text-xs font-semibold text-amber-800">{t('dashboard', 'safety_notice_title')}</p>
                  </div>
                  <ul className="space-y-1 text-xs text-amber-700 list-disc list-inside">
                    <li>{t('dashboard', 'safety_tip_financial')}</li>
                    <li>{t('dashboard', 'safety_tip_respectful')}</li>
                    <li>{t('dashboard', 'safety_tip_guardian')}</li>
                    <li>{t('dashboard', 'safety_tip_report')}</li>
                  </ul>
                </div>
              </div>
            )}
          </div>

          {/* ── Main content ─────────────────────────────────────────────── */}
          <div className="lg:col-span-2 space-y-4">

            {biodata?.about_me ? (
              <SECTION title={t('dashboard', 'profile_section_about')}>
                <p className="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{biodata.about_me}</p>
              </SECTION>
            ) : null}

            {!biodata ? (
              <SECTION title={t('dashboard', 'profile_section_general')}>
                <p className="text-sm text-slate-400">{t('dashboard', 'no_biodata_yet')}</p>
              </SECTION>
            ) : (
              <>
                <SECTION title={t('dashboard', 'profile_section_general')}>
                  <ROW label={t('dashboard', 'profile_label_marital')} value={biodata.marital_status?.replace(/_/g, ' ')} />
                  <ROW label={t('dashboard', 'profile_label_age')} value={age ? `${age} years` : null} />
                  <ROW label={t('dashboard', 'profile_label_height')} value={biodata.height_cm ? cmToFeetInches(biodata.height_cm) : null} />
                  <ROW label={t('dashboard', 'profile_label_weight')} value={biodata.weight_kg ? `${biodata.weight_kg} kg` : null} />
                  <ROW label={t('dashboard', 'profile_label_complexion')} value={biodata.complexion} />
                  <ROW label={t('dashboard', 'profile_label_blood')} value={biodata.blood_group} />
                </SECTION>

                {(biodata.division || biodata.residing_country || biodata.nationality || biodata.grew_up_in) && (
                  <SECTION title={t('dashboard', 'profile_section_location')}>
                    <ROW label={t('biodata', 'nationality')} value={biodata.nationality} />
                    <ROW label={t('biodata', 'division')} value={biodata.division} />
                    <ROW label={t('biodata', 'district')} value={biodata.district} />
                    <ROW label={t('biodata', 'residing_country')} value={biodata.residing_country} />
                    <ROW label={t('biodata', 'residing_city')} value={biodata.residing_city} />
                    <ROW label={t('biodata', 'grew_up_in')} value={biodata.grew_up_in} />
                    {biodata.is_nrb && <ROW label={t('biodata', 'is_nrb')} value={t('common', 'yes')} />}
                    <ROW label={t('biodata', 'visa_status')} value={biodata.visa_status} />
                  </SECTION>
                )}

                <SECTION title={t('dashboard', 'profile_section_religion')}>
                  <ROW label={t('dashboard', 'profile_label_religion')} value={biodata.religion} />
                  <ROW label={t('dashboard', 'profile_label_sect')} value={biodata.sect} />
                  <ROW label={t('biodata', 'fiqh')} value={biodata.fiqh} />
                  <ROW label={t('dashboard', 'profile_label_prayers')} value={biodata.prayers_info?.replace(/_times/, ' times daily')} />
                  <ROW label={t('biodata', 'quran_recitation')} value={biodata.quran_recitation} />
                  <ROW label={t('biodata', 'clothing_style')} value={biodata.clothing_style} />
                  <ROW label={t('dashboard', 'profile_label_islam_edu')} value={biodata.is_islamically_educated ? t('common', 'yes') : null} />
                  <ROW label={t('dashboard', 'profile_label_wali')} value={biodata.wali_approval ? t('common', 'yes') : null} />
                  {biodata.sunni_scale != null && (
                    <ROW label={t('biodata', 'sunni_scale')} value={`${biodata.sunni_scale} / 10`} />
                  )}
                  {biodata.beliefs_on_mazar && (
                    <div className="pt-1.5 text-sm">
                      <p className="text-slate-500">{t('biodata', 'beliefs_on_mazar')}</p>
                      <p className="text-slate-800 mt-0.5">{biodata.beliefs_on_mazar}</p>
                    </div>
                  )}
                </SECTION>

                <SECTION title={t('dashboard', 'profile_section_education')}>
                  <ROW label={t('biodata', 'education_method')} value={biodata.education_method} />
                  <ROW label={t('dashboard', 'profile_label_qual')} value={biodata.highest_qualification?.replace(/_/g, ' ')} />
                  <ROW label={t('dashboard', 'profile_label_occupation')} value={biodata.occupation} />
                  <ROW label={t('biodata', 'occupation_category')} value={biodata.occupation_category?.replace(/_/g, ' ')} />
                  <ROW label={t('dashboard', 'profile_label_income')} value={biodata.monthly_income ? `৳${biodata.monthly_income.toLocaleString()}` : null} />
                  {biodata.profession_details && (
                    <div className="pt-1.5 text-sm">
                      <p className="text-slate-500">{t('biodata', 'profession_details')}</p>
                      <p className="text-slate-800 mt-0.5">{biodata.profession_details}</p>
                    </div>
                  )}
                </SECTION>

                <SECTION title={t('dashboard', 'profile_section_family')}>
                  <ROW label={t('biodata', 'father_name')} value={biodata.father_name} />
                  <ROW label={t('biodata', 'father_profession')} value={biodata.father_profession} />
                  <ROW label={t('biodata', 'mother_name')} value={biodata.mother_name} />
                  <ROW label={t('biodata', 'mother_profession')} value={biodata.mother_profession} />
                  <ROW label={t('dashboard', 'profile_label_family_type')} value={biodata.family_type} />
                  <ROW label={t('dashboard', 'profile_label_brothers')} value={biodata.brothers ?? null} />
                  <ROW label={t('dashboard', 'profile_label_sisters')} value={biodata.sisters ?? null} />
                  <ROW label={t('biodata', 'family_financial_status')} value={biodata.family_financial_status} />
                  <ROW label={t('biodata', 'family_religious_condition')} value={biodata.family_religious_condition} />
                </SECTION>

                {(biodata.health_status || biodata.diet || biodata.smoking || biodata.hobbies) && (
                  <SECTION title={t('dashboard', 'profile_section_lifestyle')}>
                    <ROW label={t('dashboard', 'profile_label_health')} value={biodata.health_status?.replace(/_/g, ' ')} />
                    <ROW label={t('biodata', 'diet')} value={biodata.diet?.replace(/_/g, ' ')} />
                    <ROW label={t('biodata', 'smoking')} value={biodata.smoking} />
                    {biodata.watch_entertainment && (
                      <ROW label={t('biodata', 'watch_entertainment')} value={biodata.watch_entertainment} />
                    )}
                    {biodata.hobbies && (
                      <div className="pt-1.5 text-sm">
                        <p className="text-slate-500">{t('biodata', 'hobbies')}</p>
                        <p className="text-slate-800 mt-0.5">{biodata.hobbies}</p>
                      </div>
                    )}
                  </SECTION>
                )}

                {(biodata.partner_expectations || biodata.partner_age_min != null || biodata.partner_age_max != null) && (
                  <SECTION title={t('dashboard', 'profile_section_partner')}>
                    {(biodata.partner_age_min != null || biodata.partner_age_max != null) && (
                      <ROW
                        label={t('dashboard', 'profile_label_age_range')}
                        value={`${biodata.partner_age_min ?? '?'}–${biodata.partner_age_max ?? '?'} yrs`}
                      />
                    )}
                    {(biodata.partner_height_cm_min != null || biodata.partner_height_cm_max != null) && (
                      <ROW
                        label={t('biodata', 'partner_height_range')}
                        value={`${biodata.partner_height_cm_min ?? '?'}–${biodata.partner_height_cm_max ?? '?'} cm`}
                      />
                    )}
                    <ROW label={t('biodata', 'partner_complexion')} value={biodata.partner_complexion} />
                    <ROW label={t('biodata', 'partner_marital_status')} value={biodata.partner_marital_status} />
                    <ROW label={t('biodata', 'partner_education')} value={biodata.partner_education} />
                    <ROW label={t('biodata', 'partner_division')} value={biodata.partner_division} />
                    <ROW label={t('biodata', 'partner_district')} value={biodata.partner_district} />
                    {biodata.partner_expectations && (
                      <div className="pt-1.5 text-sm">
                        <p className="text-slate-500">{t('biodata', 'partner_expectations')}</p>
                        <p className="text-slate-800 mt-0.5 leading-relaxed">{biodata.partner_expectations}</p>
                      </div>
                    )}
                  </SECTION>
                )}
              </>
            )}
          </div>
        </div>

        {/* ── Report modal ─────────────────────────────────────────────────── */}
        {reportOpen && (
          <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div className="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-bold text-slate-900 flex items-center gap-2">
                  <Flag size={16} className="text-red-500" />
                  {t('dashboard', 'report_profile')}
                </h3>
                <button onClick={() => { setReportOpen(false); setReportSuccess(false); setReportError(null) }} className="text-slate-400 hover:text-slate-600">
                  <X size={18} />
                </button>
              </div>

              {reportSuccess ? (
                <div className="text-center py-4">
                  <CheckCircle2 size={36} className="text-emerald-500 mx-auto mb-3" />
                  <p className="text-sm font-semibold text-slate-900 mb-1">{t('dashboard', 'report_submitted')}</p>
                  <Button size="sm" variant="outline" className="mt-3" onClick={() => { setReportOpen(false); setReportSuccess(false) }}>
                    {t('common', 'close')}
                  </Button>
                </div>
              ) : (
                <form
                  onSubmit={e => {
                    e.preventDefault()
                    if (reportSubmitting) return
                    const fd = new FormData(e.currentTarget)
                    const reason = fd.get('reason') as string
                    if (!reason) { setReportError(t('common', 'required')); return }
                    setReportSubmitting(true)
                    setReportError(null)
                    router.post(
                      route('report.store', { registrationId: profile.registration_id }),
                      { reason, description: fd.get('description') as string },
                      {
                        onSuccess: () => { setReportSuccess(true); setReportSubmitting(false) },
                        onError: () => { setReportError(t('common', 'error')); setReportSubmitting(false) },
                        preserveScroll: true,
                      },
                    )
                  }}
                  className="space-y-4"
                >
                  <select name="reason" required className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">{t('dashboard', 'report_reason_select')}</option>
                    <option value="fake_profile">{t('dashboard', 'report_reason_fake')}</option>
                    <option value="inappropriate_photos">{t('dashboard', 'report_reason_photo')}</option>
                    <option value="harassment">{t('dashboard', 'report_reason_harassment')}</option>
                    <option value="scam">{t('dashboard', 'report_reason_scam')}</option>
                    <option value="spam">{t('dashboard', 'report_reason_spam')}</option>
                    <option value="underage">{t('dashboard', 'report_reason_underage')}</option>
                    <option value="other">{t('dashboard', 'report_reason_other')}</option>
                  </select>
                  {reportError && (
                    <p className="text-xs text-red-600 flex items-center gap-1">
                      <AlertTriangle size={12} />
                      {reportError}
                    </p>
                  )}
                  <textarea
                    name="description" rows={3}
                    placeholder={t('dashboard', 'report_detail_ph')}
                    className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm resize-none focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                  />
                  <div className="flex gap-3">
                    <Button type="button" variant="outline" className="flex-1" onClick={() => { setReportOpen(false); setReportError(null) }}>
                      {t('common', 'cancel')}
                    </Button>
                    <Button type="submit" variant="destructive" className="flex-1" isLoading={reportSubmitting}>
                      {t('dashboard', 'report_submit')}
                    </Button>
                  </div>
                </form>
              )}
            </div>
          </div>
        )}
      </div>
    </AppLayout>
  )
}
