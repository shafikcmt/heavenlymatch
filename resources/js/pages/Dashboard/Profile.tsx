/// <reference path="../../types/ziggy.d.ts" />
import React from 'react'
import { Head, Link } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { cn } from '@/lib/utils'
import { Edit2, Camera, CheckCircle, Shield, Star, BadgeCheck } from 'lucide-react'

// ── Types ─────────────────────────────────────────────────────────────────────

interface BiodataData {
  id?: number
  status?: 'draft' | 'pending' | 'approved' | 'rejected' | 'hidden'
  completeness_score?: number
  // General
  marital_status?: string
  birth_date?: string
  height_cm?: number | null
  weight_kg?: number | null
  complexion?: string
  blood_group?: string
  about_me?: string
  profile_headline?: string
  mother_tongue?: string
  // Location
  nationality?: string
  division?: string
  district?: string
  upazila?: string
  permanent_address?: string
  grew_up_in?: string
  residing_country?: string
  residing_city?: string
  is_nrb?: boolean | null
  visa_status?: string
  // Religion
  religion?: string
  sect?: string
  is_practicing?: boolean | null
  prayers_info?: string
  quran_recitation?: string
  fiqh?: string
  clothing_style?: string
  beard_info?: string
  hijab_info?: string
  is_islamically_educated?: boolean | null
  beliefs_on_mazar?: string
  favorite_scholars?: string
  wali_approval?: boolean | null
  sunni_scale?: number | null
  // Education
  education_method?: string
  highest_qualification?: string
  occupation?: string
  occupation_category?: string
  profession_details?: string
  monthly_income?: number | null
  // Family
  father_name?: string
  father_alive?: boolean | null
  father_profession?: string
  mother_name?: string
  mother_alive?: boolean | null
  mother_profession?: string
  brothers?: number | null
  sisters?: number | null
  family_type?: string
  family_financial_status?: string
  home_ownership?: string
  family_details?: string
  family_religious_condition?: string
  // Lifestyle
  health_status?: string
  health_details?: string
  diet?: string
  smoking?: string
  hobbies?: string
  watch_entertainment?: string
  special_category?: string
  // Marriage
  guardian_agree?: boolean | null
  wife_in_veil?: boolean | null
  wife_study_allowed?: boolean | null
  wife_job_allowed?: boolean | null
  residence_after_marriage?: string
  post_marriage_plan?: string
  polygamy_open?: boolean | null
  children_count?: number | null
  guardian_mobile?: string
  guardian_relationship?: string
  guardian_email?: string
  // Partner
  partner_age_min?: number | null
  partner_age_max?: number | null
  partner_height_cm_min?: number | null
  partner_height_cm_max?: number | null
  partner_complexion?: string
  partner_marital_status?: string
  partner_education?: string
  partner_occupation_pref?: string
  partner_income_min?: number | null
  partner_income_max?: number | null
  partner_division?: string
  partner_district?: string
  partner_family_type?: string
  partner_expectations?: string
}

interface UserData {
  name: string
  gender: string
  registration_id: string
  account_status: string
  is_email_verified: boolean
  identity_verification_status: string
}

interface TrustData {
  isEmailVerified: boolean
  isIdentityVerified: boolean
  biodataApproved: boolean
  isPremium: boolean
}

interface PhotoItem {
  url?: string
  blurred: boolean
  is_primary?: boolean
}

interface Props {
  biodata: BiodataData | null
  photos: PhotoItem[]
  user: UserData
  trust: TrustData
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function computeAge(birthDate?: string): number | null {
  if (!birthDate) return null
  const dob = new Date(birthDate)
  if (isNaN(dob.getTime())) return null
  const today = new Date()
  let age = today.getFullYear() - dob.getFullYear()
  const m = today.getMonth() - dob.getMonth()
  if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--
  return age
}

function isEmpty(v: unknown): boolean {
  return v === null || v === undefined || v === ''
}

// ── Sub-components ────────────────────────────────────────────────────────────

function StatusBadge({ status }: { status?: string }) {
  const { t } = useTranslation()
  const key = (status ?? 'draft') as string
  const keyMap: Record<string, string> = {
    draft:    'biodata_status_draft',
    pending:  'biodata_status_pending',
    approved: 'biodata_status_approved',
    rejected: 'biodata_status_rejected',
  }
  const colorMap: Record<string, string> = {
    draft:    'bg-slate-100 text-slate-600',
    pending:  'bg-amber-100 text-amber-700',
    approved: 'bg-emerald-100 text-emerald-700',
    rejected: 'bg-red-100 text-red-700',
  }
  return (
    <span className={cn(
      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold',
      colorMap[key] ?? colorMap.draft,
    )}>
      {t('dashboard', keyMap[key] ?? 'biodata_status_draft')}
    </span>
  )
}

function CompletionBar({ score }: { score: number }) {
  const { t } = useTranslation()
  const color = score >= 80 ? 'bg-emerald-500' : score >= 50 ? 'bg-amber-500' : 'bg-primary-500'
  return (
    <div className="space-y-1">
      <div className="flex justify-between text-xs">
        <span className="text-slate-500">{t('biodata', 'profile_completion').replace(':percent', String(score))}</span>
        <span className="font-semibold text-slate-700">{score}%</span>
      </div>
      <div className="h-2 rounded-full bg-slate-100 overflow-hidden">
        <div className={cn('h-full rounded-full transition-all duration-500', color)} style={{ width: `${score}%` }} />
      </div>
    </div>
  )
}

function Section({
  title,
  step,
  children,
}: {
  title: string
  step?: number
  children: React.ReactNode
}) {
  const { t } = useTranslation()
  return (
    <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div className="flex items-center justify-between px-5 py-3 bg-slate-50 border-b border-slate-100">
        <h3 className="text-sm font-semibold text-slate-800">{title}</h3>
        {step != null && (
          <Link
            href={route('biodata.wizard', { step })}
            className="flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 hover:underline"
          >
            {t('dashboard', 'edit_section')}
            <Edit2 size={11} />
          </Link>
        )}
      </div>
      <div className="px-5 py-4 space-y-0.5">{children}</div>
    </div>
  )
}

function Row({ label, value }: { label: string; value?: string | number | null }) {
  if (isEmpty(value)) return null
  return (
    <div className="grid grid-cols-[10rem_1fr] gap-2 py-1 text-sm">
      <span className="text-slate-500 truncate">{label}</span>
      <span className="text-slate-800 font-medium">{value}</span>
    </div>
  )
}

function BoolRow({ label, value }: { label: string; value?: boolean | null }) {
  const { t } = useTranslation()
  if (value === null || value === undefined) return null
  return (
    <div className="grid grid-cols-[10rem_1fr] gap-2 py-1 text-sm">
      <span className="text-slate-500 truncate">{label}</span>
      <span className="text-slate-800 font-medium">{value ? t('common', 'yes') : t('common', 'no')}</span>
    </div>
  )
}

function TextBlock({ label, value }: { label: string; value?: string | null }) {
  if (isEmpty(value)) return null
  return (
    <div className="py-1 text-sm space-y-0.5">
      <p className="text-slate-500">{label}</p>
      <p className="text-slate-800 font-medium whitespace-pre-line">{value}</p>
    </div>
  )
}

// ── Main page ─────────────────────────────────────────────────────────────────

export default function MyProfile({ biodata, photos, user, trust }: Props) {
  const { t } = useTranslation()

  const primaryPhoto = photos.find(p => p.is_primary) ?? photos[0]
  const score = biodata?.completeness_score ?? 0
  const age = computeAge(biodata?.birth_date)

  return (
    <AppLayout>
      <Head title={t('dashboard', 'my_profile_title')} />

      <div className="max-w-5xl mx-auto px-4 py-8">

        {/* Page title */}
        <h1 className="text-2xl font-bold text-slate-900 mb-6">
          {t('dashboard', 'my_profile_title')}
        </h1>

        <div className="flex flex-col lg:flex-row gap-6">

          {/* ── Sidebar ─────────────────────────────────────────────── */}
          <aside className="lg:w-64 shrink-0 space-y-4">

            {/* Photo card */}
            <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden">
              {primaryPhoto?.url ? (
                <img
                  src={primaryPhoto.url}
                  alt={user.name}
                  className="w-full aspect-[3/4] object-cover"
                />
              ) : (
                <div className="w-full aspect-[3/4] bg-slate-100 flex flex-col items-center justify-center gap-2 text-slate-400">
                  <Camera size={36} />
                  <span className="text-xs">{t('biodata', 'photo_no_photos')}</span>
                </div>
              )}

              <div className="px-4 py-3 space-y-3">
                <div>
                  <p className="font-semibold text-slate-900 truncate">{user.name}</p>
                  <p className="text-xs text-slate-400">{user.registration_id}</p>
                </div>

                {biodata && <StatusBadge status={biodata.status} />}

                {biodata && <CompletionBar score={score} />}

                <div className="space-y-2 pt-1">
                  <Link href={route('biodata.wizard', { step: 1 })} className="block">
                    <Button variant="outline" size="sm" className="w-full">
                      {t('dashboard', 'edit_biodata')}
                    </Button>
                  </Link>
                  <Link href={route('profile.photos.index')} className="block">
                    <Button variant="ghost" size="sm" className="w-full">
                      {t('dashboard', 'manage_photos')}
                    </Button>
                  </Link>
                </div>
              </div>
            </div>

            {/* Trust badges */}
            <div className="bg-white rounded-2xl border border-slate-200 px-4 py-3 space-y-2">
              <p className="text-xs font-semibold text-slate-600 uppercase tracking-wide">
                {t('dashboard', 'dash_trust_title')}
              </p>
              <TrustBadgeRow
                icon={<CheckCircle size={14} />}
                label={t('dashboard', 'trust_email_verified')}
                active={trust.isEmailVerified}
              />
              <TrustBadgeRow
                icon={<BadgeCheck size={14} />}
                label={t('dashboard', 'trust_id_verified')}
                active={trust.isIdentityVerified}
              />
              <TrustBadgeRow
                icon={<Shield size={14} />}
                label={t('dashboard', 'trust_profile_approved')}
                active={trust.biodataApproved}
              />
              <TrustBadgeRow
                icon={<Star size={14} />}
                label={t('dashboard', 'trust_premium')}
                active={trust.isPremium}
              />
            </div>
          </aside>

          {/* ── Main content ─────────────────────────────────────────── */}
          <main className="flex-1 space-y-4">

            {/* Empty state */}
            {!biodata && (
              <div className="bg-white rounded-2xl border border-slate-200 px-6 py-14 text-center">
                <p className="text-slate-500 mb-2">{t('dashboard', 'no_biodata_desc')}</p>
                <Link href={route('biodata.wizard', { step: 1 })}>
                  <Button size="sm" className="mt-3">{t('dashboard', 'start_biodata_cta')}</Button>
                </Link>
              </div>
            )}

            {biodata && (
              <>
                {/* About me */}
                {biodata.about_me && (
                  <Section title={t('dashboard', 'profile_section_about')} step={1}>
                    <p className="text-sm text-slate-800 whitespace-pre-line leading-relaxed">
                      {biodata.about_me}
                    </p>
                  </Section>
                )}

                {/* General */}
                <Section title={t('dashboard', 'profile_section_general')} step={1}>
                  <Row label={t('biodata', 'profile_headline')} value={biodata.profile_headline} />
                  <Row label={t('biodata', 'marital_status')} value={biodata.marital_status} />
                  {age != null && <Row label={t('biodata', 'age')} value={`${age} ${t('dashboard', 'years_old').replace(':age', '')}`} />}
                  <Row label={t('biodata', 'birth_date')} value={biodata.birth_date} />
                  {!isEmpty(biodata.height_cm) && (
                    <Row label={t('biodata', 'height')} value={`${biodata.height_cm} cm`} />
                  )}
                  {!isEmpty(biodata.weight_kg) && (
                    <Row label={t('biodata', 'weight')} value={`${biodata.weight_kg} kg`} />
                  )}
                  <Row label={t('biodata', 'complexion')} value={biodata.complexion} />
                  <Row label={t('biodata', 'blood_group')} value={biodata.blood_group} />
                  <Row label={t('biodata', 'mother_tongue')} value={biodata.mother_tongue} />
                </Section>

                {/* Location */}
                <Section title={t('dashboard', 'profile_section_location')} step={2}>
                  <Row label={t('biodata', 'nationality')} value={biodata.nationality} />
                  <Row label={t('biodata', 'division')} value={biodata.division} />
                  <Row label={t('biodata', 'district')} value={biodata.district} />
                  <Row label={t('biodata', 'upazila')} value={biodata.upazila} />
                  <Row label={t('biodata', 'grew_up_in')} value={biodata.grew_up_in} />
                  <Row label={t('biodata', 'residing_country')} value={biodata.residing_country} />
                  <Row label={t('biodata', 'residing_city')} value={biodata.residing_city} />
                  <BoolRow label={t('biodata', 'is_nrb')} value={biodata.is_nrb} />
                  <Row label={t('biodata', 'visa_status')} value={biodata.visa_status} />
                  <TextBlock label={t('biodata', 'permanent_address')} value={biodata.permanent_address} />
                </Section>

                {/* Religion */}
                <Section title={t('dashboard', 'profile_section_religion')} step={3}>
                  <Row label={t('biodata', 'religion')} value={biodata.religion} />
                  <Row label={t('biodata', 'sect')} value={biodata.sect} />
                  <Row label={t('biodata', 'fiqh')} value={biodata.fiqh} />
                  <BoolRow label={t('biodata', 'is_practicing')} value={biodata.is_practicing} />
                  <Row label={t('biodata', 'prayers_info')} value={biodata.prayers_info} />
                  <Row label={t('biodata', 'quran_recitation')} value={biodata.quran_recitation} />
                  <Row label={t('biodata', 'clothing_style')} value={biodata.clothing_style} />
                  {user.gender === 'male'
                    ? <Row label={t('biodata', 'beard_info')} value={biodata.beard_info} />
                    : <Row label={t('biodata', 'hijab_info')} value={biodata.hijab_info} />
                  }
                  <BoolRow label={t('biodata', 'is_islamically_educated')} value={biodata.is_islamically_educated} />
                  {!isEmpty(biodata.sunni_scale) && (
                    <Row label={t('biodata', 'sunni_scale')} value={`${biodata.sunni_scale} / 10`} />
                  )}
                  <BoolRow label={t('biodata', 'wali_approval')} value={biodata.wali_approval} />
                  <TextBlock label={t('biodata', 'beliefs_on_mazar')} value={biodata.beliefs_on_mazar} />
                  <TextBlock label={t('biodata', 'favorite_scholars')} value={biodata.favorite_scholars} />
                </Section>

                {/* Education & Profession */}
                <Section title={t('dashboard', 'profile_section_education')} step={4}>
                  <Row label={t('biodata', 'education_method')} value={biodata.education_method} />
                  <Row label={t('biodata', 'highest_qualification')} value={biodata.highest_qualification} />
                  <Row label={t('biodata', 'occupation')} value={biodata.occupation} />
                  <Row label={t('biodata', 'occupation_category')} value={biodata.occupation_category} />
                  {!isEmpty(biodata.monthly_income) && (
                    <Row label={t('biodata', 'monthly_income')} value={`৳ ${biodata.monthly_income?.toLocaleString()}`} />
                  )}
                  <TextBlock label={t('biodata', 'profession_details')} value={biodata.profession_details} />
                </Section>

                {/* Family */}
                <Section title={t('dashboard', 'profile_section_family')} step={5}>
                  <Row label={t('biodata', 'father_name')} value={biodata.father_name} />
                  <BoolRow label={t('biodata', 'father_alive')} value={biodata.father_alive} />
                  <Row label={t('biodata', 'father_profession')} value={biodata.father_profession} />
                  <Row label={t('biodata', 'mother_name')} value={biodata.mother_name} />
                  <BoolRow label={t('biodata', 'mother_alive')} value={biodata.mother_alive} />
                  <Row label={t('biodata', 'mother_profession')} value={biodata.mother_profession} />
                  {!isEmpty(biodata.brothers) && (
                    <Row label={t('biodata', 'brothers')} value={biodata.brothers} />
                  )}
                  {!isEmpty(biodata.sisters) && (
                    <Row label={t('biodata', 'sisters')} value={biodata.sisters} />
                  )}
                  <Row label={t('biodata', 'family_type')} value={biodata.family_type} />
                  <Row label={t('biodata', 'family_financial_status')} value={biodata.family_financial_status} />
                  <Row label={t('biodata', 'home_ownership')} value={biodata.home_ownership} />
                  <Row label={t('biodata', 'family_religious_condition')} value={biodata.family_religious_condition} />
                  <TextBlock label={t('biodata', 'family_details')} value={biodata.family_details} />
                </Section>

                {/* Lifestyle */}
                <Section title={t('dashboard', 'profile_section_lifestyle')} step={6}>
                  <Row label={t('biodata', 'health_status')} value={biodata.health_status} />
                  <Row label={t('biodata', 'diet')} value={biodata.diet} />
                  <Row label={t('biodata', 'smoking')} value={biodata.smoking} />
                  <Row label={t('biodata', 'watch_entertainment')} value={biodata.watch_entertainment} />
                  <Row label={t('biodata', 'special_category')} value={biodata.special_category} />
                  <TextBlock label={t('biodata', 'health_details')} value={biodata.health_details} />
                  <TextBlock label={t('biodata', 'hobbies')} value={biodata.hobbies} />
                </Section>

                {/* Marriage */}
                <Section title={t('dashboard', 'profile_section_marriage')} step={7}>
                  <BoolRow label={t('biodata', 'guardian_agree')} value={biodata.guardian_agree} />
                  {user.gender === 'male' && (
                    <>
                      <BoolRow label={t('biodata', 'wife_in_veil')} value={biodata.wife_in_veil} />
                      <BoolRow label={t('biodata', 'wife_study_allowed')} value={biodata.wife_study_allowed} />
                      <BoolRow label={t('biodata', 'wife_job_allowed')} value={biodata.wife_job_allowed} />
                      <BoolRow label={t('biodata', 'polygamy_open')} value={biodata.polygamy_open} />
                    </>
                  )}
                  <Row label={t('biodata', 'residence_after_marriage')} value={biodata.residence_after_marriage} />
                  <Row label={t('biodata', 'post_marriage_plan')} value={biodata.post_marriage_plan} />
                  {!isEmpty(biodata.children_count) && (
                    <Row label={t('biodata', 'children_count')} value={biodata.children_count} />
                  )}
                </Section>

                {/* Contact & Guardian */}
                <Section title={t('dashboard', 'profile_section_contact')} step={7}>
                  <Row label={t('biodata', 'guardian_mobile')} value={biodata.guardian_mobile} />
                  <Row label={t('biodata', 'guardian_relationship')} value={biodata.guardian_relationship} />
                  <Row label={t('biodata', 'guardian_email')} value={biodata.guardian_email} />
                </Section>

                {/* Partner Preferences */}
                <Section title={t('dashboard', 'profile_section_partner')} step={8}>
                  {(!isEmpty(biodata.partner_age_min) || !isEmpty(biodata.partner_age_max)) && (
                    <Row
                      label={t('biodata', 'partner_age_range')}
                      value={`${biodata.partner_age_min ?? '?'} – ${biodata.partner_age_max ?? '?'}`}
                    />
                  )}
                  {(!isEmpty(biodata.partner_height_cm_min) || !isEmpty(biodata.partner_height_cm_max)) && (
                    <Row
                      label={t('biodata', 'partner_height_range')}
                      value={`${biodata.partner_height_cm_min ?? '?'} – ${biodata.partner_height_cm_max ?? '?'} cm`}
                    />
                  )}
                  <Row label={t('biodata', 'partner_complexion')} value={biodata.partner_complexion} />
                  <Row label={t('biodata', 'partner_marital_status')} value={biodata.partner_marital_status} />
                  <Row label={t('biodata', 'partner_education')} value={biodata.partner_education} />
                  <Row label={t('biodata', 'partner_occupation_pref')} value={biodata.partner_occupation_pref} />
                  {(!isEmpty(biodata.partner_income_min) || !isEmpty(biodata.partner_income_max)) && (
                    <Row
                      label={t('biodata', 'partner_income_range')}
                      value={`৳ ${biodata.partner_income_min ?? 0} – ৳ ${biodata.partner_income_max ?? '?'}`}
                    />
                  )}
                  <Row label={t('biodata', 'partner_division')} value={biodata.partner_division} />
                  <Row label={t('biodata', 'partner_district')} value={biodata.partner_district} />
                  <Row label={t('biodata', 'partner_family_type')} value={biodata.partner_family_type} />
                  <TextBlock label={t('biodata', 'partner_expectations')} value={biodata.partner_expectations} />
                </Section>
              </>
            )}
          </main>
        </div>
      </div>
    </AppLayout>
  )
}

// ── Trust badge row ───────────────────────────────────────────────────────────

function TrustBadgeRow({
  icon,
  label,
  active,
}: {
  icon: React.ReactNode
  label: string
  active: boolean
}) {
  return (
    <div className={cn('flex items-center gap-2 text-xs', active ? 'text-emerald-700' : 'text-slate-300')}>
      <span className="shrink-0">{icon}</span>
      <span className={active ? 'font-medium' : ''}>{label}</span>
    </div>
  )
}
