/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link, router, usePage } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { calcAge, cmToFeetInches, scoreColor } from '@/lib/utils'
import {
  Heart, Send, Eye, EyeOff, Flag, Edit,
  ChevronLeft, ChevronRight, X, Shield,
  CheckCircle2, Star, Mail, ShieldCheck, AlertTriangle,
  MessageCircle, Phone, MoreHorizontal, User, BookOpen, Users, Camera,
} from 'lucide-react'
import { useState, useEffect, useRef } from 'react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import { levelLabelKey } from '@/lib/education'
import type { PageProps } from '@/types'

interface EduRecordView {
  level?: string
  subject?: string
  institute?: string
  board_university?: string
  passing_year?: string
  result_type?: string
  result_value?: string
}

interface BiodataDetail {
  marital_status?: string
  marital_substatus?: string
  birth_date?: string
  height_cm?: number
  weight_kg?: number
  complexion?: string
  blood_group?: string
  about_me?: string
  profile_headline?: string
  nationality?: string
  division?: string
  district?: string
  upazila?: string
  village_area?: string
  permanent_country?: string
  current_division?: string
  current_district?: string
  current_upazila?: string
  current_area?: string
  residing_country?: string
  residing_city?: string
  grew_up_in?: string
  is_nrb?: boolean
  visa_status?: string
  religion?: string
  sect?: string
  fiqh?: string
  is_practicing?: boolean
  prayers_info?: string
  quran_recitation?: string
  clothing_style?: string
  beard_info?: string
  beard_since?: string
  pants_above_ankle?: boolean
  hijab_info?: string
  niqab_since?: string
  purdah_details?: string
  prayer_start_age?: string
  weekly_missed_prayers?: string
  mahram_practice?: string
  islamic_books_read?: string
  deen_work_details?: string
  social_media_usage?: string
  is_islamically_educated?: boolean
  wali_approval?: boolean
  sunni_scale?: number
  beliefs_on_mazar?: string
  favorite_scholars?: string
  education_method?: string
  education_medium?: string
  highest_qualification?: string
  education_details?: EduRecordView[]
  occupation?: string
  occupation_category?: string
  monthly_income?: number
  income_type?: string
  income_privacy?: string
  workplace_type?: string
  future_career_plan?: string
  profession_halal_status?: string
  profession_details?: string
  father_name?: string
  father_alive?: boolean
  father_profession?: string
  mother_name?: string
  mother_alive?: boolean
  mother_profession?: string
  uncle_profession?: string
  family_type?: string
  brothers?: number
  sisters?: number
  family_financial_status?: string
  family_religious_condition?: string
  family_assets_details?: string
  family_details?: string
  health_status?: string
  health_details?: string
  diet?: string
  smoking?: string
  hobbies?: string
  watch_entertainment?: string
  guardian_agree?: boolean
  why_getting_married?: string
  marriage_thoughts?: string
  marriage_timeline?: string
  residence_after_marriage?: string
  post_marriage_plan?: string
  wife_in_veil?: boolean
  wife_study_allowed?: boolean
  wife_job_allowed?: boolean
  polygamy_open?: boolean
  expect_gift_from_bride?: string
  gift_expectation_details?: string
  wants_to_work?: boolean
  continue_study?: boolean
  continue_job?: boolean
  preferred_living?: string
  has_children?: boolean
  children_count?: number
  children_live_with?: string
  children_notes?: string
  previous_marriage_date?: string
  divorce_date?: string
  divorce_reason?: string
  spouse_death_date?: string
  spouse_death_reason?: string
  child_acceptance_expectation?: string
  reason_for_second_marriage?: string
  current_wife_count?: number
  current_family_consent?: boolean
  first_wife_knows?: boolean
  second_marriage_living?: string
  partner_age_min?: number
  partner_age_max?: number
  partner_height_cm_min?: number
  partner_height_cm_max?: number
  partner_income_min?: number
  partner_income_max?: number
  partner_complexion?: string
  partner_marital_status?: string
  partner_education?: string
  partner_economic_status?: string
  partner_deen_practice?: string
  partner_special_qualities?: string
  partner_deal_breakers?: string
  partner_expectations?: string
  partner_division?: string
  partner_district?: string
  partner_districts?: string[]
  partner_family_type?: string
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
  customFields?: CustomFieldValue[]
}

// Admin-defined custom field value shown on a profile (Phase E3).
interface CustomFieldValue {
  key: string
  label: string
  value: unknown
  input_type: string
}

// ── Desktop section card + row ────────────────────────────────────────────────
const SECTION = ({ title, children }: { title: string; children: React.ReactNode }) => (
  <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
    <div className="px-5 py-3 bg-slate-50/70 border-b border-slate-100">
      <h3 className="text-sm font-semibold text-slate-800 tracking-tight">{title}</h3>
    </div>
    <div className="px-5 py-4">{children}</div>
  </div>
)

const ROW = ({ label, value }: { label: string; value?: string | number | null }) =>
  value != null && value !== '' ? (
    <div className="flex items-baseline gap-3 py-2 border-b border-slate-50 last:border-0">
      <span className="text-[11px] text-slate-400 font-medium w-32 flex-shrink-0 uppercase tracking-wide">{label}</span>
      <span className="text-sm text-slate-800 font-medium flex-1">{value}</span>
    </div>
  ) : null

// Long-text block (multi-line, preserves line breaks).
const PARA = ({ label, value }: { label: string; value?: string | null }) =>
  value ? (
    <div className="py-2 border-b border-slate-50 last:border-0">
      <p className="text-[11px] text-slate-400 font-medium uppercase tracking-wide mb-1">{label}</p>
      <p className="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{value}</p>
    </div>
  ) : null

// ── Mobile circle tab step ────────────────────────────────────────────────────
const MOBILE_TABS = [
  { key: 'basics',    label: 'Basics',   icon: User },
  { key: 'religious', label: 'Religious', icon: BookOpen },
  { key: 'contact',   label: 'Contact',  icon: Phone },
  { key: 'family',    label: 'Family',   icon: Users },
  { key: 'partner',   label: 'Partner',  icon: Heart },
]

function MobileSectionTabs({ active, onChange }: { active: string; onChange: (k: string) => void }) {
  return (
    <div className="flex items-center justify-between px-4 py-4 bg-white border-b border-slate-100">
      {MOBILE_TABS.map((tab, i) => {
        const Icon = tab.icon
        const isActive = tab.key === active
        return (
          <div key={tab.key} className="flex items-center">
            <button
              onClick={() => onChange(tab.key)}
              className="flex flex-col items-center gap-1"
            >
              <div
                className={cn(
                  'h-12 w-12 rounded-full border-2 flex items-center justify-center transition-colors',
                  isActive
                    ? 'border-rose-500 bg-rose-50'
                    : 'border-slate-200 bg-white',
                )}
              >
                <Icon
                  size={20}
                  className={isActive ? 'text-rose-500' : 'text-slate-400'}
                  strokeWidth={1.5}
                />
              </div>
              <span
                className={cn(
                  'text-[10px] font-medium',
                  isActive ? 'text-rose-500' : 'text-slate-400',
                )}
              >
                {tab.label}
              </span>
            </button>
            {i < MOBILE_TABS.length - 1 && (
              <div className="h-px w-4 bg-slate-200 mx-0.5 mb-4" />
            )}
          </div>
        )
      })}
    </div>
  )
}

// ── Match score circle ────────────────────────────────────────────────────────
function MatchScoreCircle({ score }: { score: number }) {
  const r = 28
  const circ = 2 * Math.PI * r
  const offset = circ - (score / 100) * circ
  const color = score >= 80 ? '#059669' : score >= 60 ? '#0aad75' : score >= 40 ? '#f59e0b' : '#ef4444'

  return (
    <div className="relative h-16 w-16">
      <svg viewBox="0 0 64 64" className="-rotate-90 h-full w-full">
        <circle cx="32" cy="32" r={r} fill="none" stroke="#e2e8f0" strokeWidth="4" />
        <circle
          cx="32" cy="32" r={r} fill="none"
          stroke={color}
          strokeWidth="4"
          strokeLinecap="round"
          strokeDasharray={circ}
          strokeDashoffset={offset}
        />
      </svg>
      <div className="absolute inset-0 flex flex-col items-center justify-center">
        <span className="text-sm font-bold text-slate-800 leading-none">{score}%</span>
        <span className="text-[8px] text-slate-500 leading-none mt-0.5">Match</span>
        <span className="text-[8px] text-slate-500 leading-none">score</span>
      </div>
    </div>
  )
}

export default function ProfileShow({
  profile, biodata, photos,
  interestSent, interestReceived, isConnected, conversationId, isShortlisted, isOwnProfile,
  isAlreadyReported, photoAccessStatus, profileTrust, customFields = [],
}: Props) {
  const { completion } = usePage<PageProps>().props
  const { t } = useTranslation()

  // Format an admin-defined custom field value for display (Phase E3).
  const fmtCustomValue = (cf: CustomFieldValue): string => {
    const v = cf.value
    if (Array.isArray(v)) return v.join(', ')
    if (typeof v === 'boolean') return v ? t('common', 'yes') : t('common', 'no')
    return v == null ? '' : String(v)
  }

  // Education label helpers (Phase: education workflow fix). The education_medium
  // column is the system; level values localise via lvl_* keys, with a graceful
  // fallback to the raw text for legacy/free-text values.
  const eduSystem = biodata?.education_medium ?? ''
  const eduSystemLabel = eduSystem
    ? t('biodata', `edu_medium_${eduSystem === 'english_medium' ? 'english' : eduSystem}`)
    : null
  const eduLevelLabel = (value?: string | null): string | null => {
    if (!value) return null
    const key = levelLabelKey(eduSystem, value)
    return key ? t('biodata', key) : value.replace(/_/g, ' ')
  }
  const eduRecords: EduRecordView[] = Array.isArray(biodata?.education_details) ? biodata.education_details : []

  // Present / Permanent address lines (empty parts dropped; null → row hidden).
  const presentAddr = [
    biodata?.current_area, biodata?.current_upazila, biodata?.current_district,
    biodata?.current_division, biodata?.residing_city, biodata?.residing_country,
  ].filter(Boolean).join(', ')
  const permanentAddr = [
    biodata?.village_area, biodata?.upazila, biodata?.district,
    biodata?.division, biodata?.permanent_country,
  ].filter(Boolean).join(', ')
  const eduRecordLine = (r: EduRecordView): string => {
    const parts = [
      eduLevelLabel(r.level),
      r.subject,
      r.institute,
      r.board_university,
      r.passing_year,
      r.result_value ? `${r.result_type ?? ''} ${r.result_value}`.trim() : null,
    ].filter(Boolean)
    return parts.join(' · ')
  }

  const [shortlisted, setShortlisted]           = useState(isShortlisted)
  const [sent, setSent]                         = useState(interestSent)
  const [reportOpen, setReportOpen]             = useState(false)
  const [reportSubmitting, setReportSubmitting] = useState(false)
  const [reportSuccess, setReportSuccess]       = useState(false)
  const [reportError, setReportError]           = useState<string | null>(null)
  const [showGuard, setShowGuard]               = useState(false)
  const [photoIdx, setPhotoIdx]                 = useState(0)
  const [accessStatus, setAccessStatus]         = useState(photoAccessStatus)
  const [activeMobileTab, setActiveMobileTab]   = useState('basics')
  const [stickyVisible, setStickyVisible]       = useState(false)

  const heroRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    const handleScroll = () => {
      if (heroRef.current) {
        setStickyVisible(heroRef.current.getBoundingClientRect().bottom < 0)
      }
    }
    window.addEventListener('scroll', handleScroll, { passive: true })
    return () => window.removeEventListener('scroll', handleScroll)
  }, [])

  const requestPhotoAccess = () => {
    router.post(
      route('photo.request-access', { registrationId: profile.registration_id }),
      {},
      { preserveScroll: true, onSuccess: () => setAccessStatus('pending') },
    )
  }

  const age = biodata?.birth_date ? calcAge(biodata.birth_date) : null
  const displayPhotos = photos.length > 0 ? photos : []
  const activePhoto = displayPhotos[photoIdx] ?? null
  const prevPhoto = () => setPhotoIdx(i => Math.max(0, i - 1))
  const nextPhoto = () => setPhotoIdx(i => Math.min(displayPhotos.length - 1, i + 1))

  const sendInterest = () => {
    if (sent) return
    if (!completion?.can_send_interest) { setShowGuard(true); return }
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

  const genderLabel = profile.gender === 'female' ? 'Her' : 'Him'

  return (
    <AppLayout>
      <Head title={`${profile.name} — Profile`} />

      {/* Completion guard modal */}
      {showGuard && completion && (
        <div className="fixed inset-0 z-50 flex items-end justify-center bg-black/50 sm:items-center" onClick={() => setShowGuard(false)}>
          <div className="w-full max-w-sm rounded-t-3xl bg-white p-6 shadow-xl sm:rounded-2xl" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between mb-3">
              <h3 className="font-bold text-slate-900">{t('dashboard', 'interest_block_title')}</h3>
              <button onClick={() => setShowGuard(false)} className="text-slate-400"><X size={18} /></button>
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

      {/* ═══════════════════════════════════════════════════════════════════════
          MOBILE LAYOUT
      ═══════════════════════════════════════════════════════════════════════ */}
      <div className="lg:hidden -mx-4 -mt-4">

        {/* ── Sticky compact header (appears after hero scrolls off) ── */}
        {stickyVisible && (
          <div className="fixed top-[112px] left-0 right-0 z-40 flex items-center gap-3 bg-primary-700 px-4 py-2.5 shadow-md">
            <button
              onClick={() => window.history.back()}
              aria-label="Back"
              className="text-white/80 hover:text-white"
            >
              <ChevronLeft size={22} />
            </button>
            <div className="h-9 w-9 rounded-full overflow-hidden bg-white/20 shrink-0">
              {activePhoto && !activePhoto.blurred ? (
                <img src={activePhoto.url} alt={profile.name} className="h-full w-full object-cover" />
              ) : (
                <User size={20} className="text-white m-auto mt-1" />
              )}
            </div>
            <div className="min-w-0">
              <p className="text-sm font-bold text-white truncate leading-tight">{profile.name}</p>
              <p className="text-[10px] text-white/70 font-mono">{profile.registration_id}</p>
            </div>
          </div>
        )}

        {/* ── Hero photo ── */}
        <div ref={heroRef} className="relative w-full bg-slate-200" style={{ height: '62vw', maxHeight: 340 }}>
          {activePhoto ? (
            activePhoto.blurred ? (
              <div className="h-full w-full flex flex-col items-center justify-center bg-slate-200 gap-3">
                <EyeOff size={36} className="text-slate-400" />
                <p className="text-xs text-slate-500 text-center px-6">{t('dashboard', 'photo_hidden_msg')}</p>
                {!isOwnProfile && profile.platform_mode === 'islamic' && (
                  accessStatus === 'pending' ? (
                    <span className="text-xs text-amber-700 bg-amber-100 rounded-full px-3 py-1.5 font-medium">
                      {t('dashboard', 'photo_request_pending_label')}
                    </span>
                  ) : accessStatus === 'granted' ? (
                    <span className="text-xs text-emerald-700 bg-emerald-100 rounded-full px-3 py-1.5 font-medium">
                      {t('dashboard', 'photo_request_granted_label')}
                    </span>
                  ) : accessStatus === 'denied' ? (
                    <span className="text-xs text-red-700 bg-red-100 rounded-full px-3 py-1.5 font-medium">
                      {t('dashboard', 'photo_request_denied_label')}
                    </span>
                  ) : (
                    <button
                      onClick={requestPhotoAccess}
                      className="text-xs font-semibold text-primary-700 bg-primary-100 hover:bg-primary-200 rounded-full px-4 py-1.5 transition-colors"
                    >
                      {t('dashboard', 'photo_request_access')}
                    </button>
                  )
                )}
              </div>
            ) : (
              <img src={activePhoto.url} alt={profile.name} className="h-full w-full object-cover" />
            )
          ) : (
            <div
              className={cn(
                'h-full w-full flex flex-col items-center justify-center gap-2',
                profile.gender === 'female' ? 'bg-gradient-to-b from-rose-100 to-pink-50' : 'bg-gradient-to-b from-sky-100 to-blue-50',
              )}
            >
              <img
                src={`/images/avatar-${profile.gender}.svg`}
                alt={profile.name}
                className="h-28 w-28 opacity-30"
                onError={e => { (e.target as HTMLImageElement).style.display = 'none' }}
              />
              <p className="text-slate-400 text-xs font-semibold tracking-widest uppercase">Image Not Available</p>
            </div>
          )}

          {/* Back button overlay */}
          <button
            onClick={() => window.history.back()}
            aria-label="Back"
            className="absolute top-4 left-4 flex h-9 w-9 items-center justify-center rounded-full bg-black/30 text-white backdrop-blur-sm hover:bg-black/50 transition-colors"
          >
            <ChevronLeft size={20} />
          </button>

          {/* Photo navigation */}
          {displayPhotos.length > 1 && (
            <>
              {photoIdx > 0 && (
                <button
                  onClick={prevPhoto}
                  aria-label="Previous photo"
                  className="absolute left-3 top-1/2 -translate-y-1/2 flex h-9 w-9 items-center justify-center rounded-full bg-black/30 text-white backdrop-blur-sm"
                >
                  <ChevronLeft size={18} />
                </button>
              )}
              {photoIdx < displayPhotos.length - 1 && (
                <button
                  onClick={nextPhoto}
                  aria-label="Next photo"
                  className="absolute right-3 top-1/2 -translate-y-1/2 flex h-9 w-9 items-center justify-center rounded-full bg-black/30 text-white backdrop-blur-sm"
                >
                  <ChevronRight size={18} />
                </button>
              )}
            </>
          )}

          {/* Islamic badge */}
          {profile.platform_mode === 'islamic' && (
            <div className="absolute top-4 right-4">
              <Badge variant="islamic">Islamic</Badge>
            </div>
          )}
        </div>

        {/* ── Profile info below hero ── */}
        <div className="bg-white px-4 pt-3 pb-4">
          {/* ID + photo count row */}
          <div className="flex items-center justify-between mb-1">
            <div className="flex items-center gap-2">
              <span className="text-sm font-mono text-slate-500">{profile.registration_id}</span>
              {displayPhotos.length > 0 && (
                <span className="flex items-center gap-1 text-xs text-slate-400">
                  <Camera size={12} />
                  {displayPhotos.length}
                </span>
              )}
            </div>
            {profileTrust.isPremium && (
              <span className="text-[11px] font-bold text-teal-600 uppercase tracking-wide">Platinum ⚡</span>
            )}
          </div>

          {/* Name + match score */}
          <div className="flex items-start justify-between gap-3">
            <div className="flex-1 min-w-0">
              <h1 className="text-xl font-bold text-slate-900 leading-tight">{profile.name}</h1>
              <p className="text-sm text-slate-500 mt-0.5">
                {[
                  age ? `${age} ${t('common', 'yrs')}` : null,
                  biodata?.height_cm ? cmToFeetInches(biodata.height_cm) : null,
                  biodata?.district,
                  biodata?.residing_country,
                ].filter(Boolean).join(', ')}
              </p>
              {/* Chat Now link */}
              {!isOwnProfile && isConnected && conversationId && (
                <Link
                  href={route('inbox.show', { conversationId })}
                  className="mt-2 inline-flex items-center gap-1.5 text-sm font-semibold text-rose-600"
                >
                  <MessageCircle size={15} className="fill-rose-600" strokeWidth={0} />
                  Chat Now
                </Link>
              )}
            </div>
            {/* Match score circle — show if available from biodata completeness */}
            {biodata?.completeness_score != null && biodata.completeness_score > 0 && (
              <MatchScoreCircle score={biodata.completeness_score} />
            )}
          </div>
        </div>

        {/* ── Quick action row ── */}
        {!isOwnProfile && (
          <div className="flex bg-white border-t border-b border-slate-100">
            <button
              onClick={toggleShortlist}
              aria-label={shortlisted ? t('dashboard', 'shortlist_remove') : t('dashboard', 'shortlist_add')}
              className="flex flex-1 flex-col items-center justify-center gap-1 py-3 transition-colors active:bg-slate-50"
            >
              <div className="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100">
                <Star
                  size={20}
                  className={shortlisted ? 'fill-amber-500 text-amber-500' : 'text-rose-600 fill-rose-600'}
                  strokeWidth={0}
                />
              </div>
              <span className="text-[11px] font-medium text-slate-600">
                {t('dashboard', 'shortlist_add') || 'Shortlist'}
              </span>
            </button>

            <button
              aria-label="Call"
              className="flex flex-1 flex-col items-center justify-center gap-1 py-3 transition-colors active:bg-slate-50"
            >
              <div className="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100">
                <Phone size={20} className="text-rose-600 fill-rose-600" strokeWidth={0} />
              </div>
              <span className="text-[11px] font-medium text-slate-600">Call</span>
            </button>

            <button
              onClick={() => setReportOpen(true)}
              aria-label="More options"
              className="flex flex-1 flex-col items-center justify-center gap-1 py-3 transition-colors active:bg-slate-50"
            >
              <div className="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100">
                <MoreHorizontal size={20} className="text-rose-600" />
              </div>
              <span className="text-[11px] font-medium text-slate-600">More</span>
            </button>
          </div>
        )}

        {/* Own profile edit button */}
        {isOwnProfile && (
          <div className="px-4 py-3 bg-white border-b border-slate-100">
            <Link href={route('biodata.wizard')}>
              <Button className="w-full gap-2">
                <Edit size={16} />
                {t('dashboard', 'edit_biodata')}
              </Button>
            </Link>
          </div>
        )}

        {/* ── Section tabs ── */}
        <MobileSectionTabs active={activeMobileTab} onChange={setActiveMobileTab} />

        {/* ── Section content ── */}
        <div className="bg-slate-50 px-4 py-4 space-y-4">
          {biodata ? (
            <>
              {/* BASICS */}
              {activeMobileTab === 'basics' && (
                <div className="space-y-0 bg-white rounded-2xl overflow-hidden shadow-sm">
                  <MobileRow label={t('common', 'gender')} value={profile.gender ? t('common', profile.gender) : null} />
                  <MobileRow label={t('dashboard', 'profile_label_marital')} value={biodata.marital_status?.replace(/_/g, ' ')} />
                  <MobileRow label={t('dashboard', 'profile_label_age')} value={age ? `${age} years` : null} />
                  <MobileRow label={t('dashboard', 'profile_label_height')} value={biodata.height_cm ? cmToFeetInches(biodata.height_cm) : null} />
                  <MobileRow label={t('dashboard', 'profile_label_weight')} value={biodata.weight_kg ? `${biodata.weight_kg} kg` : null} />
                  <MobileRow label={t('dashboard', 'profile_label_complexion')} value={biodata.complexion} />
                  <MobileRow label={t('dashboard', 'profile_label_blood')} value={biodata.blood_group} />
                  <MobileRow label={t('biodata', 'physical_status')} value={biodata.health_status ? t('biodata', `health_${biodata.health_status}`) : null} />
                  {(biodata.health_status === 'minor_condition' || biodata.health_status === 'disability') && (
                    <MobileRow label={t('biodata', 'health_issue_details')} value={biodata.health_details} />
                  )}
                  <MobileRow label={t('biodata', 'nationality')} value={biodata.nationality} />
                  <MobileRow label={t('biodata', 'present_address')} value={presentAddr || null} />
                  <MobileRow label={t('biodata', 'permanent_address')} value={permanentAddr || null} />
                </div>
              )}

              {/* RELIGIOUS */}
              {activeMobileTab === 'religious' && (
                <div className="space-y-0 bg-white rounded-2xl overflow-hidden shadow-sm">
                  <MobileRow label={t('dashboard', 'profile_label_religion')} value={biodata.religion} />
                  <MobileRow label={t('dashboard', 'profile_label_sect')} value={biodata.sect} />
                  <MobileRow label={t('biodata', 'fiqh')} value={biodata.fiqh} />
                  <MobileRow label={t('dashboard', 'profile_label_prayers')} value={biodata.prayers_info?.replace(/_times/, ' times daily')} />
                  <MobileRow label={t('biodata', 'quran_recitation')} value={biodata.quran_recitation} />
                  <MobileRow label={t('biodata', 'clothing_style')} value={biodata.clothing_style} />
                  <MobileRow label={t('dashboard', 'profile_label_islam_edu')} value={biodata.is_islamically_educated ? t('common', 'yes') : null} />
                  <MobileRow label={t('dashboard', 'profile_label_wali')} value={biodata.wali_approval ? t('common', 'yes') : null} />
                  {biodata.sunni_scale != null && (
                    <MobileRow label={t('biodata', 'sunni_scale')} value={`${biodata.sunni_scale} / 10`} />
                  )}
                  {profile.gender === 'male' ? (
                    <>
                      <MobileRow label={t('biodata', 'beard_info')} value={biodata.beard_info} />
                      <MobileRow label={t('biodata', 'beard_since')} value={biodata.beard_since} />
                      {biodata.pants_above_ankle && <MobileRow label={t('biodata', 'pants_above_ankle')} value={t('common', 'yes')} />}
                    </>
                  ) : (
                    <>
                      <MobileRow label={t('biodata', 'hijab_info')} value={biodata.hijab_info} />
                      <MobileRow label={t('biodata', 'niqab_since')} value={biodata.niqab_since} />
                    </>
                  )}
                  <MobileRow label={t('biodata', 'prayer_start_age')} value={biodata.prayer_start_age} />
                  <MobileRow label={t('biodata', 'weekly_missed_prayers')} value={biodata.weekly_missed_prayers} />
                  <MobileRow label={t('biodata', 'mahram_practice')} value={biodata.mahram_practice} />
                  <MobileRow label={t('biodata', 'islamic_books_read')} value={biodata.islamic_books_read} />
                  <MobileRow label={t('biodata', 'deen_work_details')} value={biodata.deen_work_details} />
                  <MobileRow label={t('biodata', 'social_media_usage')} value={biodata.social_media_usage} />
                  {biodata.purdah_details && (
                    <div className="px-4 py-3 border-b border-slate-50">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1">{t('biodata', 'purdah_details')}</p>
                      <p className="text-sm text-slate-800 leading-relaxed whitespace-pre-wrap">{biodata.purdah_details}</p>
                    </div>
                  )}
                  {biodata.beliefs_on_mazar && (
                    <div className="px-4 py-3 border-b border-slate-50">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1">{t('biodata', 'beliefs_on_mazar')}</p>
                      <p className="text-sm text-slate-800">{biodata.beliefs_on_mazar}</p>
                    </div>
                  )}
                </div>
              )}

              {/* CONTACT / EDUCATION */}
              {activeMobileTab === 'contact' && (
                <div className="space-y-0 bg-white rounded-2xl overflow-hidden shadow-sm">
                  <MobileRow label={t('biodata', 'education_system')} value={eduSystemLabel} />
                  <MobileRow label={t('dashboard', 'profile_label_qual')} value={eduLevelLabel(biodata.highest_qualification)} />
                  <MobileRow label={t('dashboard', 'profile_label_occupation')} value={biodata.occupation} />
                  <MobileRow label={t('biodata', 'occupation_category')} value={biodata.occupation_category?.replace(/_/g, ' ')} />
                  <MobileRow label={t('biodata', 'workplace_type')} value={biodata.workplace_type} />
                  <MobileRow label={t('dashboard', 'profile_label_income')} value={biodata.monthly_income != null ? `৳${biodata.monthly_income.toLocaleString()}` : null} />
                  <MobileRow label={t('biodata', 'profession_halal_status')} value={biodata.profession_halal_status ? t('biodata', `halal_status_${biodata.profession_halal_status === 'halal_alternative' ? 'alternative' : biodata.profession_halal_status}`) : null} />
                  {eduRecords.length > 0 && (
                    <div className="px-4 py-3 border-b border-slate-50">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1.5">{t('biodata', 'education_records')}</p>
                      <ul className="space-y-1">
                        {eduRecords.map((r, i) => (
                          <li key={i} className="text-sm text-slate-800">{eduRecordLine(r)}</li>
                        ))}
                      </ul>
                    </div>
                  )}
                  {biodata.profession_details && (
                    <div className="px-4 py-3 border-b border-slate-50">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1">{t('biodata', 'profession_details')}</p>
                      <p className="text-sm text-slate-800">{biodata.profession_details}</p>
                    </div>
                  )}
                  {biodata.future_career_plan && (
                    <div className="px-4 py-3 border-b border-slate-50">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1">{t('biodata', 'future_career_plan')}</p>
                      <p className="text-sm text-slate-800 leading-relaxed whitespace-pre-wrap">{biodata.future_career_plan}</p>
                    </div>
                  )}
                  {/* Safety notice */}
                  <div className="mx-4 my-3 rounded-xl border border-amber-200 bg-amber-50 p-3">
                    <div className="flex items-center gap-1.5 mb-1">
                      <Shield size={13} className="text-amber-600 shrink-0" />
                      <p className="text-xs font-semibold text-amber-800">{t('dashboard', 'safety_notice_title')}</p>
                    </div>
                    <ul className="space-y-0.5 text-xs text-amber-700 list-disc list-inside">
                      <li>{t('dashboard', 'safety_tip_financial')}</li>
                      <li>{t('dashboard', 'safety_tip_respectful')}</li>
                    </ul>
                  </div>
                </div>
              )}

              {/* FAMILY */}
              {activeMobileTab === 'family' && (
                <div className="space-y-0 bg-white rounded-2xl overflow-hidden shadow-sm">
                  <MobileRow label={t('biodata', 'father_name')} value={biodata.father_name} />
                  <MobileRow label={t('biodata', 'father_profession')} value={biodata.father_profession} />
                  <MobileRow label={t('biodata', 'mother_name')} value={biodata.mother_name} />
                  <MobileRow label={t('biodata', 'mother_profession')} value={biodata.mother_profession} />
                  <MobileRow label={t('biodata', 'uncle_profession')} value={biodata.uncle_profession} />
                  <MobileRow label={t('dashboard', 'profile_label_family_type')} value={biodata.family_type} />
                  <MobileRow label={t('dashboard', 'profile_label_brothers')} value={biodata.brothers ?? null} />
                  <MobileRow label={t('dashboard', 'profile_label_sisters')} value={biodata.sisters ?? null} />
                  <MobileRow label={t('biodata', 'family_financial_status')} value={biodata.family_financial_status} />
                  <MobileRow label={t('biodata', 'family_religious_condition')} value={biodata.family_religious_condition} />
                  {biodata.family_assets_details && (
                    <div className="px-4 py-3 border-b border-slate-50">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1">{t('biodata', 'family_assets_details')}</p>
                      <p className="text-sm text-slate-800 leading-relaxed whitespace-pre-wrap">{biodata.family_assets_details}</p>
                    </div>
                  )}
                  <MobileRow label={t('dashboard', 'profile_label_health')} value={biodata.health_status?.replace(/_/g, ' ')} />
                  <MobileRow label={t('biodata', 'diet')} value={biodata.diet?.replace(/_/g, ' ')} />
                  <MobileRow label={t('biodata', 'smoking')} value={biodata.smoking} />
                  {biodata.hobbies && (
                    <div className="px-4 py-3 border-b border-slate-50">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1">{t('biodata', 'hobbies')}</p>
                      <p className="text-sm text-slate-800">{biodata.hobbies}</p>
                    </div>
                  )}

                  {/* Marriage info (no dedicated mobile tab — shown here) */}
                  {(biodata.why_getting_married || biodata.residence_after_marriage || biodata.has_children
                    || biodata.divorce_date || biodata.spouse_death_date || biodata.reason_for_second_marriage) && (
                    <>
                      <div className="px-4 pt-3 pb-1">
                        <p className="text-[11px] font-semibold text-rose-500 uppercase tracking-wider">{t('biodata', 'step_labels.7')}</p>
                      </div>
                      <MobileRow label={t('biodata', 'guardian_agree')} value={biodata.guardian_agree ? t('common', 'yes') : null} />
                      <MobileRow label={t('biodata', 'residence_after_marriage')} value={biodata.residence_after_marriage} />
                      <MobileRow label={t('biodata', 'marriage_timeline')} value={biodata.marriage_timeline} />
                      {profile.gender === 'male' && (
                        <>
                          <MobileRow label={t('biodata', 'wife_in_veil')} value={biodata.wife_in_veil ? t('common', 'yes') : null} />
                          <MobileRow label={t('biodata', 'wife_job_allowed')} value={biodata.wife_job_allowed ? t('common', 'yes') : null} />
                          <MobileRow label={t('biodata', 'polygamy_open')} value={biodata.polygamy_open ? t('common', 'yes') : null} />
                        </>
                      )}
                      {profile.gender === 'female' && (
                        <>
                          <MobileRow label={t('biodata', 'wants_to_work')} value={biodata.wants_to_work ? t('common', 'yes') : null} />
                          <MobileRow label={t('biodata', 'continue_job')} value={biodata.continue_job ? t('common', 'yes') : null} />
                          <MobileRow label={t('biodata', 'preferred_living')} value={biodata.preferred_living} />
                        </>
                      )}
                      {biodata.has_children && (
                        <>
                          <MobileRow label={t('biodata', 'children_count')} value={biodata.children_count ?? null} />
                          <MobileRow label={t('biodata', 'children_live_with')} value={biodata.children_live_with} />
                        </>
                      )}
                      {biodata.marital_status === 'divorced' && (
                        <MobileRow label={t('biodata', 'divorce_date')} value={biodata.divorce_date?.slice(0, 10)} />
                      )}
                      {biodata.marital_status === 'widowed' && (
                        <MobileRow label={t('biodata', 'spouse_death_date')} value={biodata.spouse_death_date?.slice(0, 10)} />
                      )}
                      {biodata.marital_status === 'married' && (
                        <MobileRow label={t('biodata', 'current_wife_count')} value={biodata.current_wife_count ?? null} />
                      )}
                    </>
                  )}
                </div>
              )}

              {/* PARTNER */}
              {activeMobileTab === 'partner' && (
                <div className="space-y-0 bg-white rounded-2xl overflow-hidden shadow-sm">
                  {(biodata.partner_age_min != null || biodata.partner_age_max != null) && (
                    <MobileRow
                      label={t('dashboard', 'profile_label_age_range')}
                      value={`${biodata.partner_age_min ?? '?'}–${biodata.partner_age_max ?? '?'} yrs`}
                    />
                  )}
                  {(biodata.partner_height_cm_min != null || biodata.partner_height_cm_max != null) && (
                    <MobileRow
                      label={t('biodata', 'partner_height_range')}
                      value={`${biodata.partner_height_cm_min ?? '?'}–${biodata.partner_height_cm_max ?? '?'} cm`}
                    />
                  )}
                  <MobileRow label={t('biodata', 'partner_complexion')} value={biodata.partner_complexion} />
                  <MobileRow label={t('biodata', 'partner_marital_status')} value={biodata.partner_marital_status} />
                  <MobileRow label={t('biodata', 'partner_education')} value={biodata.partner_education} />
                  <MobileRow label={t('biodata', 'partner_economic_status')} value={biodata.partner_economic_status} />
                  <MobileRow label={t('biodata', 'partner_deen_practice')} value={biodata.partner_deen_practice} />
                  <MobileRow label={t('biodata', 'partner_family_type')} value={biodata.partner_family_type} />
                  <MobileRow label={t('biodata', 'partner_division')} value={biodata.partner_division} />
                  <MobileRow label={t('biodata', 'partner_district')} value={biodata.partner_district} />
                  {Array.isArray(biodata.partner_districts) && biodata.partner_districts.length > 0 && (
                    <MobileRow label={t('biodata', 'partner_districts')} value={biodata.partner_districts.join(', ')} />
                  )}
                  {biodata.partner_special_qualities && (
                    <div className="px-4 py-3 border-b border-slate-50">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1">{t('biodata', 'partner_special_qualities')}</p>
                      <p className="text-sm text-slate-800 leading-relaxed whitespace-pre-wrap">{biodata.partner_special_qualities}</p>
                    </div>
                  )}
                  {biodata.partner_deal_breakers && (
                    <div className="px-4 py-3 border-b border-slate-50">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1">{t('biodata', 'partner_deal_breakers')}</p>
                      <p className="text-sm text-slate-800 leading-relaxed whitespace-pre-wrap">{biodata.partner_deal_breakers}</p>
                    </div>
                  )}
                  {biodata.partner_expectations && (
                    <div className="px-4 py-3">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1">{t('biodata', 'partner_expectations')}</p>
                      <p className="text-sm text-slate-800 leading-relaxed">{biodata.partner_expectations}</p>
                    </div>
                  )}
                </div>
              )}
            </>
          ) : (
            <div className="rounded-2xl bg-white p-6 text-center">
              <p className="text-sm text-slate-400">{t('dashboard', 'no_biodata_yet')}</p>
            </div>
          )}

          {/* Admin-defined custom fields (Phase E3) — shown across mobile tabs */}
          {customFields.length > 0 && (
            <div className="bg-white rounded-2xl overflow-hidden shadow-sm">
              <div className="px-4 py-2.5 bg-slate-50/70 border-b border-slate-100">
                <h3 className="text-sm font-semibold text-slate-800">{t('biodata', 'additional_info')}</h3>
              </div>
              {customFields.map(cf => (
                <MobileRow key={cf.key} label={cf.label} value={fmtCustomValue(cf)} />
              ))}
            </div>
          )}

          {/* Report link */}
          {!isOwnProfile && !isAlreadyReported && (
            <button
              onClick={() => setReportOpen(true)}
              className="w-full text-xs text-slate-400 hover:text-red-500 flex items-center justify-center gap-1 transition-colors py-2"
            >
              <Flag size={12} /> {t('dashboard', 'report_profile')}
            </button>
          )}
        </div>

        {/* ── Like/Skip bottom bar (non-own profile, not connected) ── */}
        {!isOwnProfile && !isConnected && (
          <div className="sticky bottom-16 z-30 flex items-center bg-white border-t border-slate-200 px-4 py-3 gap-3 shadow-lg">
            <span className="text-base font-bold text-slate-800 flex-1">
              Like {genderLabel}?
            </span>
            <button
              onClick={() => window.history.back()}
              className="flex items-center gap-1.5 rounded-full border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
            >
              <X size={15} /> Skip
            </button>
            {sent ? (
              <button disabled className="flex items-center gap-1.5 rounded-full bg-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-500">
                <CheckCircle2 size={15} /> Sent
              </button>
            ) : (
              <button
                onClick={sendInterest}
                className="flex items-center gap-1.5 rounded-full bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-rose-700 transition-colors active:scale-95"
              >
                <CheckCircle2 size={15} /> Yes
              </button>
            )}
          </div>
        )}
      </div>

      {/* ═══════════════════════════════════════════════════════════════════════
          DESKTOP LAYOUT (unchanged, grid sidebar)
      ═══════════════════════════════════════════════════════════════════════ */}
      <div className="hidden lg:block max-w-4xl mx-auto px-4 py-6">
        <button
          onClick={() => window.history.back()}
          className="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition-colors mb-6"
        >
          <ChevronLeft size={16} />
          {t('common', 'back')}
        </button>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

          {/* ── Desktop sidebar ── */}
          <div className="space-y-4">
            {/* Photo card */}
            <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
              <div className="h-56 bg-slate-100 relative flex items-center justify-center">
                {activePhoto ? (
                  activePhoto.blurred ? (
                    <div className="absolute inset-0 flex flex-col items-center justify-center bg-slate-200 px-4">
                      <EyeOff size={32} className="text-slate-400 mb-2" />
                      <p className="text-xs text-slate-500 text-center mb-3">{t('dashboard', 'photo_hidden_msg')}</p>
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

                {displayPhotos.length > 1 && (
                  <>
                    {photoIdx > 0 && (
                      <button onClick={prevPhoto} aria-label="Previous photo" className="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-1 shadow hover:bg-white">
                        <ChevronLeft size={16} />
                      </button>
                    )}
                    {photoIdx < displayPhotos.length - 1 && (
                      <button onClick={nextPhoto} aria-label="Next photo" className="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 rounded-full p-1 shadow hover:bg-white">
                        <ChevronRight size={16} />
                      </button>
                    )}
                    <div className="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1">
                      {displayPhotos.map((_, i) => (
                        <button key={i} onClick={() => setPhotoIdx(i)} aria-label={`Photo ${i + 1}`} className={cn('h-1.5 rounded-full transition-all', i === photoIdx ? 'w-4 bg-white' : 'w-1.5 bg-white/50')} />
                      ))}
                    </div>
                  </>
                )}

                {profile.platform_mode === 'islamic' && (
                  <div className="absolute top-3 right-3"><Badge variant="islamic">Islamic</Badge></div>
                )}
              </div>

              <div className="p-4">
                <div className="text-center mb-3">
                  <h1 className="text-lg font-bold text-slate-900 leading-tight">{profile.name}</h1>
                  {biodata?.profile_headline && (
                    <p className="text-xs text-slate-500 mt-1 italic leading-snug">&ldquo;{biodata.profile_headline}&rdquo;</p>
                  )}
                  <p className="text-xs text-slate-400 mt-1.5">
                    {[age ? `${age} ${t('common', 'yrs')}` : null, biodata?.district, biodata?.residing_country].filter(Boolean).join(' · ')}
                  </p>
                </div>

                <div className="flex flex-wrap justify-center gap-1 mb-3">
                  {profileTrust.isEmailVerified && (
                    <span className="inline-flex items-center gap-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] px-2 py-0.5 border border-emerald-200 font-medium">
                      <Mail size={9} /> {t('dashboard', 'trust_email_verified')}
                    </span>
                  )}
                  {profileTrust.isIdentityVerified && (
                    <span className="inline-flex items-center gap-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] px-2 py-0.5 border border-blue-200 font-medium">
                      <ShieldCheck size={9} /> {t('dashboard', 'trust_id_verified')}
                    </span>
                  )}
                  {profileTrust.biodataApproved && (
                    <span className="inline-flex items-center gap-0.5 rounded-full bg-violet-50 text-violet-700 text-[10px] px-2 py-0.5 border border-violet-200 font-medium">
                      <CheckCircle2 size={9} /> {t('dashboard', 'trust_profile_approved')}
                    </span>
                  )}
                  {profileTrust.isPremium && (
                    <span className="inline-flex items-center gap-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] px-2 py-0.5 border border-amber-200 font-medium">
                      <Star size={9} /> {t('dashboard', 'trust_premium')}
                    </span>
                  )}
                </div>

                {biodata?.completeness_score != null && (
                  <div className="px-1">
                    <div className="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                      <div className={cn('h-full rounded-full transition-all', scoreColor(biodata.completeness_score).replace('text-', 'bg-'))} style={{ width: `${biodata.completeness_score}%` }} />
                    </div>
                    <p className="text-[10px] text-slate-400 mt-1 text-center">
                      {t('dashboard', 'profile_completion_pct', { percent: biodata.completeness_score })}
                    </p>
                  </div>
                )}
              </div>
            </div>

            {/* Desktop actions */}
            {isOwnProfile ? (
              <Link href={route('biodata.wizard')}>
                <Button className="w-full gap-2"><Edit size={16} />{t('dashboard', 'edit_biodata')}</Button>
              </Link>
            ) : (
              <div className="space-y-2">
                {isConnected ? (
                  <>
                    <Button className="w-full" variant="outline" disabled>✓ {t('dashboard', 'connected_label')}</Button>
                    {conversationId && (
                      <Link href={route('inbox.show', { conversationId })}>
                        <Button className="w-full gap-2"><MessageCircle size={16} />{t('interests', 'message')}</Button>
                      </Link>
                    )}
                  </>
                ) : sent ? (
                  <Button className="w-full" variant="outline" disabled>{t('dashboard', 'interest_sent_label')}</Button>
                ) : interestReceived ? (
                  <div className="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center">
                    <p className="text-sm text-emerald-700 font-medium mb-2">{t('dashboard', 'this_person_sent_interest')}</p>
                    <Link href={route('interests.received')}>
                      <Button size="sm" className="w-full">{t('dashboard', 'accept_interest')}</Button>
                    </Link>
                  </div>
                ) : (
                  <Button className="w-full gap-2" onClick={sendInterest}>
                    <Send size={16} />{t('dashboard', 'interest_send')}
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
                    <Flag size={12} className="text-red-400" />{t('dashboard', 'report_already_done')}
                  </p>
                ) : (
                  <button
                    onClick={() => setReportOpen(true)}
                    className="w-full text-xs text-slate-400 hover:text-red-500 flex items-center justify-center gap-1 mt-1 transition-colors"
                  >
                    <Flag size={12} /> {t('dashboard', 'report_profile')}
                  </button>
                )}

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

          {/* ── Desktop main content ── */}
          <div className="lg:col-span-2 space-y-4">
            {biodata?.about_me && (
              <SECTION title={t('dashboard', 'profile_section_about')}>
                <p className="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{biodata.about_me}</p>
              </SECTION>
            )}

            {!biodata ? (
              <SECTION title={t('dashboard', 'profile_section_general')}>
                <p className="text-sm text-slate-400">{t('dashboard', 'no_biodata_yet')}</p>
              </SECTION>
            ) : (
              <>
                <SECTION title={t('dashboard', 'profile_section_general')}>
                  <ROW label={t('common', 'gender')} value={profile.gender ? t('common', profile.gender) : null} />
                  <ROW label={t('dashboard', 'profile_label_marital')} value={biodata.marital_status?.replace(/_/g, ' ')} />
                  <ROW label={t('dashboard', 'profile_label_age')} value={age ? `${age} years` : null} />
                  <ROW label={t('dashboard', 'profile_label_height')} value={biodata.height_cm ? cmToFeetInches(biodata.height_cm) : null} />
                  <ROW label={t('dashboard', 'profile_label_weight')} value={biodata.weight_kg ? `${biodata.weight_kg} kg` : null} />
                  <ROW label={t('dashboard', 'profile_label_complexion')} value={biodata.complexion} />
                  <ROW label={t('dashboard', 'profile_label_blood')} value={biodata.blood_group} />
                  <ROW label={t('biodata', 'physical_status')} value={biodata.health_status ? t('biodata', `health_${biodata.health_status}`) : null} />
                  {(biodata.health_status === 'minor_condition' || biodata.health_status === 'disability') && (
                    <ROW label={t('biodata', 'health_issue_details')} value={biodata.health_details} />
                  )}
                </SECTION>

                {(presentAddr || permanentAddr || biodata.nationality) && (
                  <SECTION title={t('dashboard', 'profile_section_location')}>
                    <ROW label={t('biodata', 'nationality')} value={biodata.nationality} />
                    <ROW label={t('biodata', 'present_address')} value={presentAddr || null} />
                    <ROW label={t('biodata', 'permanent_address')} value={permanentAddr || null} />
                    {biodata.is_nrb && <ROW label={t('biodata', 'is_nrb')} value={t('common', 'yes')} />}
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
                  {biodata.sunni_scale != null && <ROW label={t('biodata', 'sunni_scale')} value={`${biodata.sunni_scale} / 10`} />}
                  {profile.gender === 'male' ? (
                    <>
                      <ROW label={t('biodata', 'beard_info')} value={biodata.beard_info} />
                      <ROW label={t('biodata', 'beard_since')} value={biodata.beard_since} />
                      {biodata.pants_above_ankle && <ROW label={t('biodata', 'pants_above_ankle')} value={t('common', 'yes')} />}
                    </>
                  ) : (
                    <>
                      <ROW label={t('biodata', 'hijab_info')} value={biodata.hijab_info} />
                      <ROW label={t('biodata', 'niqab_since')} value={biodata.niqab_since} />
                    </>
                  )}
                  <ROW label={t('biodata', 'prayer_start_age')} value={biodata.prayer_start_age} />
                  <ROW label={t('biodata', 'weekly_missed_prayers')} value={biodata.weekly_missed_prayers} />
                  <ROW label={t('biodata', 'mahram_practice')} value={biodata.mahram_practice} />
                  <ROW label={t('biodata', 'islamic_books_read')} value={biodata.islamic_books_read} />
                  <ROW label={t('biodata', 'deen_work_details')} value={biodata.deen_work_details} />
                  <ROW label={t('biodata', 'social_media_usage')} value={biodata.social_media_usage} />
                  <PARA label={t('biodata', 'purdah_details')} value={biodata.purdah_details} />
                </SECTION>

                <SECTION title={t('dashboard', 'profile_section_education')}>
                  <ROW label={t('biodata', 'education_system')} value={eduSystemLabel} />
                  <ROW label={t('dashboard', 'profile_label_qual')} value={eduLevelLabel(biodata.highest_qualification)} />
                  {eduRecords.length > 0 && (
                    <div className="py-2">
                      <p className="text-xs text-slate-400 uppercase tracking-wide mb-1.5">{t('biodata', 'education_records')}</p>
                      <ul className="space-y-1">
                        {eduRecords.map((r, i) => (
                          <li key={i} className="text-sm text-slate-800">{eduRecordLine(r)}</li>
                        ))}
                      </ul>
                    </div>
                  )}
                  <ROW label={t('dashboard', 'profile_label_occupation')} value={biodata.occupation} />
                  <ROW label={t('biodata', 'occupation_category')} value={biodata.occupation_category?.replace(/_/g, ' ')} />
                  <ROW label={t('biodata', 'workplace_type')} value={biodata.workplace_type} />
                  {biodata.monthly_income != null && (
                    <ROW
                      label={t('dashboard', 'profile_label_income')}
                      value={`৳${biodata.monthly_income.toLocaleString()}${biodata.income_type && biodata.income_type !== 'private' ? ` / ${t('biodata', `income_type_${biodata.income_type}`)}` : ''}`}
                    />
                  )}
                  <ROW label={t('biodata', 'profession_halal_status')} value={biodata.profession_halal_status ? t('biodata', `halal_status_${biodata.profession_halal_status === 'halal_alternative' ? 'alternative' : biodata.profession_halal_status}`) : null} />
                  <PARA label={t('biodata', 'future_career_plan')} value={biodata.future_career_plan} />
                </SECTION>

                <SECTION title={t('dashboard', 'profile_section_family')}>
                  <ROW label={t('biodata', 'father_name')} value={biodata.father_name} />
                  <ROW label={t('biodata', 'father_profession')} value={biodata.father_profession} />
                  <ROW label={t('biodata', 'mother_name')} value={biodata.mother_name} />
                  <ROW label={t('biodata', 'mother_profession')} value={biodata.mother_profession} />
                  <ROW label={t('dashboard', 'profile_label_family_type')} value={biodata.family_type} />
                  <ROW label={t('biodata', 'uncle_profession')} value={biodata.uncle_profession} />
                  <ROW label={t('dashboard', 'profile_label_brothers')} value={biodata.brothers ?? null} />
                  <ROW label={t('dashboard', 'profile_label_sisters')} value={biodata.sisters ?? null} />
                  <ROW label={t('biodata', 'family_financial_status')} value={biodata.family_financial_status} />
                  <ROW label={t('biodata', 'family_religious_condition')} value={biodata.family_religious_condition} />
                  <PARA label={t('biodata', 'family_assets_details')} value={biodata.family_assets_details} />
                  <PARA label={t('dashboard', 'profile_section_family')} value={biodata.family_details} />
                </SECTION>

                {(biodata.health_status || biodata.diet || biodata.smoking || biodata.hobbies) && (
                  <SECTION title={t('dashboard', 'profile_section_lifestyle')}>
                    <ROW label={t('dashboard', 'profile_label_health')} value={biodata.health_status?.replace(/_/g, ' ')} />
                    <ROW label={t('biodata', 'diet')} value={biodata.diet?.replace(/_/g, ' ')} />
                    <ROW label={t('biodata', 'smoking')} value={biodata.smoking} />
                    {biodata.watch_entertainment && <ROW label={t('biodata', 'watch_entertainment')} value={biodata.watch_entertainment} />}
                  </SECTION>
                )}

                {(biodata.why_getting_married || biodata.marriage_thoughts || biodata.residence_after_marriage
                  || biodata.guardian_agree != null || biodata.has_children
                  || biodata.divorce_date || biodata.spouse_death_date || biodata.reason_for_second_marriage) && (
                  <SECTION title={t('biodata', 'step_labels.7')}>
                    <PARA label={t('biodata', 'why_getting_married')} value={biodata.why_getting_married} />
                    <PARA label={t('biodata', 'marriage_thoughts')} value={biodata.marriage_thoughts} />
                    <ROW label={t('biodata', 'marriage_timeline')} value={biodata.marriage_timeline} />
                    <ROW label={t('biodata', 'guardian_agree')} value={biodata.guardian_agree ? t('common', 'yes') : null} />
                    <ROW label={t('biodata', 'residence_after_marriage')} value={biodata.residence_after_marriage} />
                    <ROW label={t('biodata', 'post_marriage_plan')} value={biodata.post_marriage_plan} />

                    {profile.gender === 'male' && (
                      <>
                        <ROW label={t('biodata', 'wife_in_veil')} value={biodata.wife_in_veil ? t('common', 'yes') : null} />
                        <ROW label={t('biodata', 'wife_study_allowed')} value={biodata.wife_study_allowed ? t('common', 'yes') : null} />
                        <ROW label={t('biodata', 'wife_job_allowed')} value={biodata.wife_job_allowed ? t('common', 'yes') : null} />
                        <ROW label={t('biodata', 'polygamy_open')} value={biodata.polygamy_open ? t('common', 'yes') : null} />
                        <ROW label={t('biodata', 'expect_gift_from_bride')} value={biodata.expect_gift_from_bride} />
                        <PARA label={t('biodata', 'gift_expectation_details')} value={biodata.gift_expectation_details} />
                      </>
                    )}

                    {profile.gender === 'female' && (
                      <>
                        <ROW label={t('biodata', 'wants_to_work')} value={biodata.wants_to_work ? t('common', 'yes') : null} />
                        <ROW label={t('biodata', 'continue_study')} value={biodata.continue_study ? t('common', 'yes') : null} />
                        <ROW label={t('biodata', 'continue_job')} value={biodata.continue_job ? t('common', 'yes') : null} />
                        <ROW label={t('biodata', 'preferred_living')} value={biodata.preferred_living} />
                      </>
                    )}

                    {biodata.has_children && (
                      <>
                        <ROW label={t('biodata', 'children_count')} value={biodata.children_count ?? null} />
                        <ROW label={t('biodata', 'children_live_with')} value={biodata.children_live_with} />
                        <PARA label="" value={biodata.children_notes} />
                      </>
                    )}

                    {/* Marital-status conditionals */}
                    {biodata.marital_status === 'divorced' && (
                      <>
                        <ROW label={t('biodata', 'previous_marriage_date')} value={biodata.previous_marriage_date?.slice(0, 10)} />
                        <ROW label={t('biodata', 'divorce_date')} value={biodata.divorce_date?.slice(0, 10)} />
                        <PARA label={t('biodata', 'divorce_reason')} value={biodata.divorce_reason} />
                      </>
                    )}
                    {biodata.marital_status === 'widowed' && (
                      <>
                        <ROW label={t('biodata', 'spouse_death_date')} value={biodata.spouse_death_date?.slice(0, 10)} />
                        <PARA label={t('biodata', 'spouse_death_reason')} value={biodata.spouse_death_reason} />
                        <PARA label={t('biodata', 'child_acceptance_expectation')} value={biodata.child_acceptance_expectation} />
                      </>
                    )}
                    {biodata.marital_status === 'married' && (
                      <>
                        <PARA label={t('biodata', 'reason_for_second_marriage')} value={biodata.reason_for_second_marriage} />
                        <ROW label={t('biodata', 'current_wife_count')} value={biodata.current_wife_count ?? null} />
                        <ROW label={t('biodata', 'second_marriage_living')} value={biodata.second_marriage_living} />
                        <ROW label={t('biodata', 'current_family_consent')} value={biodata.current_family_consent ? t('common', 'yes') : null} />
                        <ROW label={t('biodata', 'first_wife_knows')} value={biodata.first_wife_knows ? t('common', 'yes') : null} />
                      </>
                    )}
                  </SECTION>
                )}

                {(biodata.partner_expectations || biodata.partner_age_min != null || biodata.partner_age_max != null
                  || biodata.partner_special_qualities || biodata.partner_deal_breakers) && (
                  <SECTION title={t('dashboard', 'profile_section_partner')}>
                    {(biodata.partner_age_min != null || biodata.partner_age_max != null) && (
                      <ROW label={t('dashboard', 'profile_label_age_range')} value={`${biodata.partner_age_min ?? '?'}–${biodata.partner_age_max ?? '?'} yrs`} />
                    )}
                    {(biodata.partner_height_cm_min != null || biodata.partner_height_cm_max != null) && (
                      <ROW
                        label={t('biodata', 'partner_height_range')}
                        value={`${biodata.partner_height_cm_min ? cmToFeetInches(biodata.partner_height_cm_min) : '?'} – ${biodata.partner_height_cm_max ? cmToFeetInches(biodata.partner_height_cm_max) : '?'}`}
                      />
                    )}
                    <ROW label={t('biodata', 'partner_complexion')} value={biodata.partner_complexion} />
                    <ROW label={t('biodata', 'partner_marital_status')} value={biodata.partner_marital_status} />
                    <ROW label={t('biodata', 'partner_education')} value={biodata.partner_education} />
                    <ROW label={t('biodata', 'partner_economic_status')} value={biodata.partner_economic_status} />
                    <ROW label={t('biodata', 'partner_deen_practice')} value={biodata.partner_deen_practice} />
                    <ROW label={t('biodata', 'partner_family_type')} value={biodata.partner_family_type} />
                    <ROW label={t('biodata', 'partner_division')} value={biodata.partner_division} />
                    <ROW label={t('biodata', 'partner_district')} value={biodata.partner_district} />
                    {Array.isArray(biodata.partner_districts) && biodata.partner_districts.length > 0 && (
                      <ROW label={t('biodata', 'partner_districts')} value={biodata.partner_districts.join(', ')} />
                    )}
                    <PARA label={t('biodata', 'partner_special_qualities')} value={biodata.partner_special_qualities} />
                    <PARA label={t('biodata', 'partner_deal_breakers')} value={biodata.partner_deal_breakers} />
                    <PARA label={t('biodata', 'partner_expectations')} value={biodata.partner_expectations} />
                  </SECTION>
                )}
              </>
            )}

            {/* Admin-defined custom fields (Phase E3) */}
            {customFields.length > 0 && (
              <SECTION title={t('biodata', 'additional_info')}>
                {customFields.map(cf => (
                  <ROW key={cf.key} label={cf.label} value={fmtCustomValue(cf)} />
                ))}
              </SECTION>
            )}
          </div>
        </div>
      </div>

      {/* ── Report modal ── */}
      {reportOpen && (
        <div className="fixed inset-0 bg-black/50 flex items-end justify-center z-50 p-4 sm:items-center">
          <div className="bg-white rounded-t-3xl sm:rounded-2xl p-6 max-w-sm w-full shadow-xl">
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
                    <AlertTriangle size={12} />{reportError}
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
    </AppLayout>
  )
}

// ── Mobile detail row helper ──────────────────────────────────────────────────
function MobileRow({ label, value }: { label: string; value?: string | number | null }) {
  if (value == null) return null
  return (
    <div className="flex items-start gap-2 px-4 py-3 border-b border-slate-50 last:border-0">
      <span className="text-xs text-slate-400 w-28 shrink-0 pt-0.5">{label}</span>
      <span className="text-sm text-slate-800 font-medium flex-1">: {value}</span>
    </div>
  )
}
