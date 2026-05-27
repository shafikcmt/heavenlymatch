/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import { useState } from 'react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { SearchableSelect } from '@/components/ui/SearchableSelect'
import { HeightSelect } from '@/components/ui/HeightSelect'
import { WeightSelect } from '@/components/ui/WeightSelect'
import { DateOfBirthSelect } from '@/components/ui/DateOfBirthSelect'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import { CheckCircle, Save } from 'lucide-react'
import { BangladeshAddressPicker } from '@/components/forms/BangladeshAddressPicker'

// ─── Types ────────────────────────────────────────────────────────────────────

interface BiodataData {
  marital_status?: string
  birth_date?: string
  height_cm?: number | ''
  weight_kg?: number | ''
  complexion?: string
  blood_group?: string
  about_me?: string
  profile_headline?: string
  mother_tongue?: string
  division?: string
  district?: string
  upazila?: string
  residing_country?: string
  residing_city?: string
  is_nrb?: boolean
  visa_status?: string
  religion?: string
  sect?: string
  is_practicing?: boolean
  prayers_info?: string
  quran_recitation?: string
  clothing_style?: string
  beard_info?: string
  hijab_info?: string
  is_islamically_educated?: boolean
  wali_approval?: boolean
  sunni_scale?: number | ''
  education_method?: string
  highest_qualification?: string
  occupation?: string
  occupation_category?: string
  profession_details?: string
  monthly_income?: number | ''
  father_name?: string
  father_alive?: boolean
  father_profession?: string
  mother_name?: string
  mother_alive?: boolean
  mother_profession?: string
  brothers?: number | ''
  sisters?: number | ''
  family_type?: string
  family_financial_status?: string
  home_ownership?: string
  family_details?: string
  health_status?: string
  diet?: string
  smoking?: string
  hobbies?: string
  guardian_agree?: boolean
  wife_in_veil?: boolean
  wife_study_allowed?: boolean
  wife_job_allowed?: boolean
  polygamy_open?: boolean
  children_count?: number | ''
  residence_after_marriage?: string
  post_marriage_plan?: string
  guardian_mobile?: string
  guardian_relationship?: string
  guardian_email?: string
  partner_age_min?: number | ''
  partner_age_max?: number | ''
  partner_height_cm_min?: number | ''
  partner_height_cm_max?: number | ''
  partner_income_min?: number | ''
  partner_income_max?: number | ''
  partner_complexion?: string
  partner_marital_status?: string
  partner_education?: string
  partner_division?: string
  partner_district?: string
  partner_family_type?: string
  partner_expectations?: string
}

interface Props {
  step: number
  steps: Record<number, string>
  biodata: BiodataData & { completeness_score?: number }
  user: { name: string; gender: string; mode: string }
}

// ─── Module-level stable sub-components (prevents focus loss) ─────────────────

interface ToggleProps {
  value: boolean | undefined
  label: string
  onChange: (v: boolean) => void
}
function WizardToggle({ value, label, onChange }: ToggleProps) {
  return (
    <label className="flex items-center gap-3 cursor-pointer select-none group">
      <div
        className={cn(
          'relative w-11 h-6 rounded-full transition-colors shrink-0',
          value ? 'bg-primary-600' : 'bg-slate-200',
        )}
        onClick={() => onChange(!value)}
      >
        <div className={cn(
          'absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-transform duration-200',
          value ? 'translate-x-6' : 'translate-x-1',
        )} />
      </div>
      <span className="text-sm text-slate-700 group-hover:text-slate-900 transition-colors">{label}</span>
    </label>
  )
}

interface TextareaProps {
  value: string
  label: string
  placeholder?: string
  rows?: number
  maxLength?: number
  onChange: (v: string) => void
  error?: string
}
function WizardTextarea({ value, label, placeholder = '', rows = 4, maxLength, onChange, error }: TextareaProps) {
  return (
    <div>
      <div className="flex items-center justify-between mb-1.5">
        <label className="block text-sm font-medium text-slate-700">{label}</label>
        {maxLength && (
          <span className={cn('text-xs tabular-nums', value.length > maxLength * 0.9 ? 'text-amber-600' : 'text-slate-400')}>
            {value.length}/{maxLength}
          </span>
        )}
      </div>
      <textarea
        value={value}
        onChange={e => onChange(e.target.value)}
        rows={rows}
        maxLength={maxLength}
        placeholder={placeholder}
        className="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 resize-none transition-colors"
      />
      {error && <p className="mt-1 text-xs text-red-600">{error}</p>}
    </div>
  )
}

function SectionLabel({ children }: { children: React.ReactNode }) {
  return (
    <div className="pt-1">
      <p className="text-xs font-semibold text-slate-400 uppercase tracking-wider pb-2 border-b border-slate-100">
        {children}
      </p>
    </div>
  )
}

// ─── Static option lists (no translation needed) ──────────────────────────────

const COUNTRY_OPTIONS = [
  'Bangladesh','United Kingdom','United States','Canada','Australia',
  'Saudi Arabia','United Arab Emirates','Qatar','Kuwait','Bahrain',
  'Oman','Malaysia','Singapore','Italy','France','Germany',
  'Sweden','Norway','Denmark','Netherlands','Japan','South Korea','New Zealand',
].map(c => ({ value: c, label: c }))

const MOTHER_TONGUE_OPTIONS = [
  { value: 'Bangla', label: 'Bangla / Bengali' },
  { value: 'Sylheti', label: 'Sylheti' },
  { value: 'Chittagonian', label: 'Chittagonian (Chatgaiya)' },
  { value: 'English', label: 'English' },
  { value: 'Hindi', label: 'Hindi' },
  { value: 'Urdu', label: 'Urdu' },
  { value: 'Arabic', label: 'Arabic' },
]

const BEARD_OPTIONS = [
  { value: 'Full beard (sunnah)', label: 'Full beard (sunnah)' },
  { value: 'Trimmed beard', label: 'Trimmed beard' },
  { value: 'Short beard', label: 'Short beard' },
  { value: 'No beard', label: 'No beard' },
  { value: 'Currently growing', label: 'Currently growing' },
]

const CLOTHING_OPTIONS = [
  { value: 'Sunnah dress (full)', label: 'Sunnah dress (full)' },
  { value: 'Traditional / Kurta-Pyjama', label: 'Traditional / Kurta-Pyjama' },
  { value: 'Smart casual', label: 'Smart casual' },
  { value: 'Formal', label: 'Formal' },
  { value: 'Mixed / No preference', label: 'Mixed / No preference' },
]

const SECT_OPTIONS = [
  { value: 'Hanafi', label: 'Hanafi' },
  { value: "Shafi'i", label: "Shafi'i" },
  { value: 'Maliki', label: 'Maliki' },
  { value: 'Hanbali', label: 'Hanbali' },
  { value: 'Ahle Hadith', label: 'Ahle Hadith' },
  { value: 'Deobandi', label: 'Deobandi' },
  { value: 'Barelvi', label: 'Barelvi' },
  { value: 'Salafi', label: 'Salafi' },
]

const GUARDIAN_REL_OPTIONS = [
  { value: 'Father', label: 'Father' },
  { value: 'Brother', label: 'Brother' },
  { value: 'Uncle', label: 'Uncle' },
  { value: 'Grandfather', label: 'Grandfather' },
  { value: 'Mother', label: 'Mother' },
  { value: 'Other', label: 'Other' },
]

const RESIDENCE_OPTIONS = [
  { value: 'Bangladesh', label: 'Bangladesh' },
  { value: 'Abroad', label: 'Abroad' },
  { value: 'Flexible', label: 'Flexible' },
  { value: "Husband's family home", label: "Husband's family home" },
  { value: 'Own flat / rented', label: 'Own flat / rented' },
]

const POST_MARRIAGE_OPTIONS = [
  { value: 'Stay in Bangladesh', label: 'Stay in Bangladesh' },
  { value: 'Move abroad', label: 'Move abroad' },
  { value: 'Flexible', label: 'Flexible' },
  { value: 'Continue current job', label: 'Continue current job' },
  { value: 'Focus on family', label: 'Focus on family' },
]

const OCCUPATION_OPTIONS = [
  { value: 'Engineer', label: 'Engineer' },
  { value: 'Software Engineer', label: 'Software Engineer' },
  { value: 'Doctor', label: 'Doctor' },
  { value: 'Teacher / Lecturer', label: 'Teacher / Lecturer' },
  { value: 'Business Owner', label: 'Business Owner' },
  { value: 'Government Employee', label: 'Government Employee' },
  { value: 'Army / Police / Defense', label: 'Army / Police / Defense' },
  { value: 'Student', label: 'Student' },
  { value: 'Housewife', label: 'Housewife' },
  { value: 'Islamic Scholar / Imam', label: 'Islamic Scholar / Imam' },
  { value: 'Nurse', label: 'Nurse' },
  { value: 'Accountant', label: 'Accountant' },
  { value: 'Lawyer', label: 'Lawyer' },
  { value: 'Banker', label: 'Banker' },
]

const PROFESSION_OPTIONS = [
  { value: 'Retired', label: 'Retired' },
  { value: 'Deceased', label: 'Deceased' },
  { value: 'Farmer', label: 'Farmer' },
  { value: 'Business', label: 'Business' },
  { value: 'Government Service', label: 'Government Service' },
  { value: 'Private Service', label: 'Private Service' },
  { value: 'Teacher', label: 'Teacher' },
  { value: 'Housewife', label: 'Housewife' },
  { value: 'Doctor', label: 'Doctor' },
  { value: 'Engineer', label: 'Engineer' },
  { value: 'Islamic Scholar', label: 'Islamic Scholar' },
]

const COUNT_OPTIONS = Array.from({ length: 21 }, (_, i) => ({ value: String(i), label: String(i) }))

// ─── Main Component ───────────────────────────────────────────────────────────

export default function BiodataWizard({ step, steps, biodata, user }: Props) {
  const totalSteps = Object.keys(steps).length
  const { t } = useTranslation()
  const completenessScore = biodata.completeness_score ?? 0

  const { data, setData, post, processing, errors } = useForm<BiodataData>({ ...biodata })
  const [savingDraft, setSavingDraft] = useState(false)

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('biodata.save', { step }))
  }

  const saveDraft = (e: React.MouseEvent) => {
    e.preventDefault()
    setSavingDraft(true)
    router.post(
      route('biodata.save', { step }),
      { ...(data as Record<string, unknown>), save_draft: true },
      { onFinish: () => setSavingDraft(false) },
    )
  }

  const currentLabel = t('biodata', `step_labels.${step}`)
  const currentHelper = t('biodata', `step_helper.${step}`)

  // Inline option builders that require translation
  const maritalStatusOpts = [
    { value: 'never_married', label: t('biodata', 'never_married') },
    { value: 'married',       label: t('biodata', 'married') },
    { value: 'divorced',      label: t('biodata', 'divorced') },
    { value: 'widowed',       label: t('biodata', 'widowed') },
  ]
  const complexionOpts = [
    { value: 'very_fair', label: t('biodata', 'very_fair') },
    { value: 'fair',      label: t('biodata', 'fair') },
    { value: 'wheatish',  label: t('biodata', 'wheatish') },
    { value: 'medium',    label: t('biodata', 'medium') },
    { value: 'dark',      label: t('biodata', 'dark') },
  ]
  const bloodGroupOpts = ['A+','A-','B+','B-','AB+','AB-','O+','O-'].map(b => ({ value: b, label: b }))
  const visaOpts = [
    { value: 'citizen',            label: t('biodata', 'visa_citizen') },
    { value: 'permanent_resident', label: t('biodata', 'visa_permanent_resident') },
    { value: 'work_visa',          label: t('biodata', 'visa_work_visa') },
    { value: 'student_visa',       label: t('biodata', 'visa_student_visa') },
  ]
  const prayersOpts = [
    { value: '5_times',   label: t('biodata', 'prayers_5_times') },
    { value: '4_times',   label: t('biodata', 'prayers_4_times') },
    { value: 'sometimes', label: t('biodata', 'prayers_sometimes') },
    { value: 'rarely',    label: t('biodata', 'prayers_rarely') },
    { value: 'never',     label: t('biodata', 'prayers_never') },
  ]
  const quranOpts = [
    { value: 'fluent',   label: t('biodata', 'quran_fluent') },
    { value: 'basic',    label: t('biodata', 'quran_basic') },
    { value: 'learning', label: t('biodata', 'quran_learning') },
    { value: 'no',       label: t('biodata', 'quran_no') },
  ]
  const hijabOpts = [
    { value: 'wears_niqab', label: t('biodata', 'hijab_wears_niqab') },
    { value: 'wears_hijab', label: t('biodata', 'hijab_wears_hijab') },
    { value: 'trying',      label: t('biodata', 'hijab_trying') },
    { value: 'no_hijab',    label: t('biodata', 'hijab_no_hijab') },
  ]
  const eduMethodOpts = [
    { value: 'general', label: t('biodata', 'edu_method_general') },
    { value: 'islamic', label: t('biodata', 'edu_method_islamic') },
    { value: 'both',    label: t('biodata', 'edu_method_both') },
  ]
  const qualOpts = [
    { value: 'below_ssc',       label: t('biodata', 'qual_below_ssc') },
    { value: 'ssc',             label: t('biodata', 'qual_ssc') },
    { value: 'hsc',             label: t('biodata', 'qual_hsc') },
    { value: 'diploma',         label: t('biodata', 'qual_diploma') },
    { value: 'graduation',      label: t('biodata', 'qual_graduation') },
    { value: 'post_graduation', label: t('biodata', 'qual_post_graduation') },
    { value: 'phd',             label: t('biodata', 'qual_phd') },
    { value: 'hafez',           label: t('biodata', 'qual_hafez') },
    { value: 'alim',            label: t('biodata', 'qual_alim') },
    { value: 'fazil',           label: t('biodata', 'qual_fazil') },
    { value: 'kamil',           label: t('biodata', 'qual_kamil') },
  ]
  const occCatOpts = [
    { value: 'business',        label: t('biodata', 'occ_business') },
    { value: 'service_govt',    label: t('biodata', 'occ_service_govt') },
    { value: 'service_private', label: t('biodata', 'occ_service_private') },
    { value: 'education',       label: t('biodata', 'occ_education') },
    { value: 'medical',         label: t('biodata', 'occ_medical') },
    { value: 'engineering',     label: t('biodata', 'occ_engineering') },
    { value: 'it',              label: t('biodata', 'occ_it') },
    { value: 'abroad_job',      label: t('biodata', 'occ_abroad_job') },
    { value: 'student',         label: t('biodata', 'occ_student') },
    { value: 'housewife',       label: t('biodata', 'occ_housewife') },
    { value: 'agriculture',     label: t('biodata', 'occ_agriculture') },
    { value: 'other',           label: t('biodata', 'occ_other') },
  ]
  const familyTypeOpts = [
    { value: 'joint',    label: t('biodata', 'family_joint') },
    { value: 'nuclear',  label: t('biodata', 'family_nuclear') },
    { value: 'flexible', label: t('biodata', 'family_flexible') },
  ]
  const financeOpts = [
    { value: 'lower',        label: t('biodata', 'finance_lower') },
    { value: 'lower_middle', label: t('biodata', 'finance_lower_middle') },
    { value: 'middle',       label: t('biodata', 'finance_middle') },
    { value: 'upper_middle', label: t('biodata', 'finance_upper_middle') },
    { value: 'upper',        label: t('biodata', 'finance_upper') },
  ]
  const homeOpts = [
    { value: 'own_house',    label: t('biodata', 'home_own_house') },
    { value: 'family_house', label: t('biodata', 'home_family_house') },
    { value: 'rented',       label: t('biodata', 'home_rented') },
  ]
  const healthOpts = [
    { value: 'healthy',         label: t('biodata', 'health_healthy') },
    { value: 'minor_condition', label: t('biodata', 'health_minor_condition') },
    { value: 'disability',      label: t('biodata', 'health_disability') },
    { value: 'prefer_not_say',  label: t('biodata', 'health_prefer_not_say') },
  ]
  const dietOpts = [
    { value: 'halal_only',     label: t('biodata', 'diet_halal_only') },
    { value: 'vegetarian',     label: t('biodata', 'diet_vegetarian') },
    { value: 'no_restriction', label: t('biodata', 'diet_no_restriction') },
  ]
  const smokingOpts = [
    { value: 'never',        label: t('biodata', 'smoking_never') },
    { value: 'occasionally', label: t('biodata', 'smoking_occasionally') },
    { value: 'regularly',    label: t('biodata', 'smoking_regularly') },
  ]
  const partnerMaritalOpts = [
    { value: 'never_married', label: t('biodata', 'never_married') },
    { value: 'divorced',      label: t('biodata', 'divorced') },
    { value: 'widowed',       label: t('biodata', 'widowed') },
    { value: 'any',           label: t('biodata', 'any') },
  ]
  const partnerEduOpts = [
    { value: 'ssc',             label: t('biodata', 'qual_ssc') },
    { value: 'hsc',             label: t('biodata', 'qual_hsc') },
    { value: 'graduation',      label: t('biodata', 'qual_graduation') },
    { value: 'post_graduation', label: t('biodata', 'qual_post_graduation') },
    { value: 'any',             label: t('biodata', 'no_preference') },
  ]
  const partnerFamilyOpts = [
    { value: 'joint',   label: t('biodata', 'family_joint') },
    { value: 'nuclear', label: t('biodata', 'family_nuclear') },
    { value: 'any',     label: t('biodata', 'no_preference') },
  ]
  const ageOpts = Array.from({ length: 63 }, (_, i) => ({ value: String(i + 18), label: `${i + 18}` }))

  // ─── Render ──────────────────────────────────────────────────────────────────

  return (
    <AppLayout>
      <Head title={t('biodata', 'wizard_title')} />

      <div className="max-w-2xl mx-auto px-4 py-6 sm:py-8">

        {/* Overall completion bar */}
        {completenessScore > 0 && (
          <div className="mb-5">
            <div className="flex items-center justify-between text-xs mb-1.5">
              <span className="text-slate-500">{t('biodata', 'profile_completion')}</span>
              <span className={cn(
                'font-bold',
                completenessScore >= 80 ? 'text-emerald-600' :
                completenessScore >= 50 ? 'text-primary-600' : 'text-amber-600',
              )}>{completenessScore}%</span>
            </div>
            <div className="h-2 rounded-full bg-slate-200 overflow-hidden">
              <div
                className={cn(
                  'h-full rounded-full transition-all duration-700',
                  completenessScore >= 80 ? 'bg-emerald-500' :
                  completenessScore >= 50 ? 'bg-primary-500' : 'bg-amber-400',
                )}
                style={{ width: `${completenessScore}%` }}
              />
            </div>
          </div>
        )}

        {/* Step progress */}
        <div className="flex items-center gap-0 mb-6 overflow-x-auto pb-1 scrollbar-none">
          {Array.from({ length: totalSteps }, (_, i) => i + 1).map(num => (
            <div key={num} className="flex items-center flex-shrink-0">
              <button
                type="button"
                title={steps[num]}
                onClick={() => num < step ? router.get(route('biodata.wizard', { step: num })) : undefined}
                className={cn(
                  'h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-200',
                  step > num
                    ? 'bg-emerald-500 text-white cursor-pointer hover:bg-emerald-600 hover:scale-105'
                    : step === num
                      ? 'bg-primary-600 text-white shadow-md shadow-primary-200 scale-110'
                      : 'bg-slate-100 text-slate-400 cursor-default border-2 border-slate-200',
                )}
              >
                {step > num ? <CheckCircle size={14} /> : num}
              </button>
              {num < totalSteps && (
                <div className={cn(
                  'h-0.5 transition-colors duration-300',
                  num < 5 ? 'w-5 sm:w-7' : 'w-4 sm:w-6',
                  step > num ? 'bg-emerald-400' : 'bg-slate-200',
                )} />
              )}
            </div>
          ))}
        </div>

        {/* Form card */}
        <div className="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

          {/* Card header */}
          <div className={cn(
            'px-6 py-5 border-b border-slate-100',
            step === 1
              ? 'bg-gradient-to-r from-primary-50 via-white to-emerald-50'
              : 'bg-slate-50',
          )}>
            <div className="flex items-start justify-between gap-4">
              <div>
                <p className="text-xs font-semibold text-primary-500 uppercase tracking-widest mb-0.5">
                  {t('biodata', 'wizard_subtitle', { step: String(step), total: String(totalSteps) })}
                </p>
                <h2 className="text-lg font-bold text-slate-900 leading-tight">{currentLabel}</h2>
                {currentHelper && (
                  <p className="text-sm text-slate-500 mt-0.5 leading-snug">{currentHelper}</p>
                )}
              </div>
              <button
                type="button"
                onClick={saveDraft}
                disabled={savingDraft || processing}
                className="flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:border-slate-400 transition-colors disabled:opacity-50 shrink-0 shadow-sm"
              >
                <Save size={13} />
                {savingDraft ? t('common', 'saving') : t('biodata', 'wizard_save_draft')}
              </button>
            </div>
          </div>

          {/* Form body */}
          <form onSubmit={submit} className="p-6 space-y-5">

            {/* ── Step 1: General Info ── */}
            {step === 1 && (
              <>
                {step === 1 && (
                  <div className="rounded-xl bg-primary-50 border border-primary-100 px-4 py-3 text-sm text-primary-700">
                    Welcome! Let's start with your basic information. Fill as much as you can — a complete profile gets more attention.
                  </div>
                )}

                <Input
                  label={t('biodata', 'profile_headline')}
                  value={data.profile_headline ?? ''}
                  onChange={e => setData('profile_headline', e.target.value as never)}
                  error={errors.profile_headline}
                  placeholder="e.g. Practicing Muslim, Software Engineer in Dhaka"
                />

                <SearchableSelect
                  label={t('biodata', 'marital_status')}
                  value={data.marital_status ?? ''}
                  onChange={v => setData('marital_status', v as never)}
                  options={maritalStatusOpts}
                  error={errors.marital_status}
                />

                <DateOfBirthSelect
                  label={t('biodata', 'birth_date')}
                  value={(data.birth_date as string) ?? ''}
                  onChange={v => setData('birth_date', v as never)}
                  error={errors.birth_date}
                />

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <HeightSelect
                    label={t('biodata', 'height')}
                    value={data.height_cm ?? ''}
                    onChange={v => setData('height_cm', v as never)}
                    error={errors.height_cm}
                  />
                  <WeightSelect
                    label={t('biodata', 'weight')}
                    value={data.weight_kg ?? ''}
                    onChange={v => setData('weight_kg', v as never)}
                    error={errors.weight_kg}
                  />
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'complexion')}
                    value={data.complexion ?? ''}
                    onChange={v => setData('complexion', v as never)}
                    options={complexionOpts}
                    error={errors.complexion}
                  />
                  <SearchableSelect
                    label={t('biodata', 'blood_group')}
                    value={data.blood_group ?? ''}
                    onChange={v => setData('blood_group', v as never)}
                    options={bloodGroupOpts}
                    error={errors.blood_group}
                  />
                </div>

                <SearchableSelect
                  label={t('biodata', 'mother_tongue')}
                  value={data.mother_tongue ?? ''}
                  onChange={v => setData('mother_tongue', v as never)}
                  options={MOTHER_TONGUE_OPTIONS}
                  error={errors.mother_tongue}
                  allowFreeText
                  placeholder="e.g. Bangla, Sylheti..."
                />

                <WizardTextarea
                  label={t('biodata', 'about_me')}
                  value={(data.about_me as string) ?? ''}
                  onChange={v => setData('about_me', v as never)}
                  error={errors.about_me}
                  placeholder="Write a brief introduction — your personality, what you value, what you're looking for..."
                  rows={5}
                  maxLength={1000}
                />
                <p className="text-xs text-slate-400 -mt-1">{t('biodata', 'about_me_tip')}</p>
              </>
            )}

            {/* ── Step 2: Location ── */}
            {step === 2 && (
              <>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'residing_country')}
                    value={data.residing_country ?? ''}
                    onChange={v => setData('residing_country', v as never)}
                    options={COUNTRY_OPTIONS}
                    error={errors.residing_country}
                    allowFreeText
                    placeholder="e.g. Bangladesh, UK..."
                  />
                  <Input
                    label={t('biodata', 'residing_city')}
                    value={data.residing_city ?? ''}
                    onChange={e => setData('residing_city', e.target.value as never)}
                    error={errors.residing_city}
                    placeholder="e.g. Dhaka, London"
                  />
                </div>

                <SectionLabel>Hometown (Bangladesh)</SectionLabel>
                <BangladeshAddressPicker
                  value={{
                    division: (data.division as string) ?? undefined,
                    district: (data.district as string) ?? undefined,
                    upazila:  (data.upazila  as string) ?? undefined,
                  }}
                  onChange={val => setData({
                    ...data,
                    division: val.division ?? '',
                    district: val.district ?? '',
                    upazila:  val.upazila  ?? '',
                  })}
                  errors={{
                    division: errors.division as string | undefined,
                    district: errors.district as string | undefined,
                    upazila:  errors.upazila  as string | undefined,
                  }}
                />

                <WizardToggle
                  value={!!data.is_nrb}
                  label={t('biodata', 'is_nrb')}
                  onChange={v => setData('is_nrb', v as never)}
                />

                {data.is_nrb && (
                  <SearchableSelect
                    label={t('biodata', 'visa_status')}
                    value={data.visa_status ?? ''}
                    onChange={v => setData('visa_status', v as never)}
                    options={visaOpts}
                    error={errors.visa_status}
                  />
                )}
              </>
            )}

            {/* ── Step 3: Religion ── */}
            {step === 3 && (
              <>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <Input
                    label={t('biodata', 'religion')}
                    value={data.religion ?? ''}
                    onChange={e => setData('religion', e.target.value as never)}
                    error={errors.religion}
                    placeholder="Islam"
                  />
                  <SearchableSelect
                    label={t('biodata', 'sect')}
                    value={data.sect ?? ''}
                    onChange={v => setData('sect', v as never)}
                    options={SECT_OPTIONS}
                    error={errors.sect}
                    allowFreeText
                    placeholder="e.g. Hanafi, Ahle Hadith"
                  />
                </div>

                <div className="rounded-xl bg-slate-50 border border-slate-100 p-4 space-y-3">
                  <SectionLabel>Practice & Observance</SectionLabel>
                  <WizardToggle
                    value={!!data.is_practicing}
                    label={t('biodata', 'is_practicing')}
                    onChange={v => setData('is_practicing', v as never)}
                  />
                  <SearchableSelect
                    label={t('biodata', 'prayers_info')}
                    value={data.prayers_info ?? ''}
                    onChange={v => setData('prayers_info', v as never)}
                    options={prayersOpts}
                    error={errors.prayers_info}
                  />
                  <SearchableSelect
                    label={t('biodata', 'quran_recitation')}
                    value={data.quran_recitation ?? ''}
                    onChange={v => setData('quran_recitation', v as never)}
                    options={quranOpts}
                    error={errors.quran_recitation}
                  />
                </div>

                <div className="space-y-4">
                  <SectionLabel>Appearance & Dress</SectionLabel>
                  {user.gender === 'female' ? (
                    <SearchableSelect
                      label={t('biodata', 'hijab_info')}
                      value={data.hijab_info ?? ''}
                      onChange={v => setData('hijab_info', v as never)}
                      options={hijabOpts}
                      error={errors.hijab_info}
                    />
                  ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <SearchableSelect
                        label={t('biodata', 'beard_info')}
                        value={data.beard_info ?? ''}
                        onChange={v => setData('beard_info', v as never)}
                        options={BEARD_OPTIONS}
                        error={errors.beard_info}
                        allowFreeText
                      />
                      <SearchableSelect
                        label={t('biodata', 'clothing_style') || 'Clothing Style'}
                        value={data.clothing_style ?? ''}
                        onChange={v => setData('clothing_style', v as never)}
                        options={CLOTHING_OPTIONS}
                        error={errors.clothing_style}
                        allowFreeText
                      />
                    </div>
                  )}
                </div>

                <WizardToggle
                  value={!!data.is_islamically_educated}
                  label={t('biodata', 'is_islamically_educated')}
                  onChange={v => setData('is_islamically_educated', v as never)}
                />

                {user.mode === 'islamic' && (
                  <div className="rounded-xl bg-amber-50 border border-amber-100 p-4 space-y-3">
                    <SectionLabel>Islamic Mode</SectionLabel>
                    <WizardToggle
                      value={!!data.wali_approval}
                      label={t('biodata', 'wali_approval')}
                      onChange={v => setData('wali_approval', v as never)}
                    />
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-2">
                        {t('biodata', 'sunni_scale')}
                      </label>
                      <input
                        type="range"
                        min={1}
                        max={10}
                        value={(data.sunni_scale as number) ?? 5}
                        onChange={e => setData('sunni_scale', parseInt(e.target.value) as never)}
                        className="w-full accent-primary-600"
                      />
                      <div className="flex justify-between text-xs text-slate-400 mt-1">
                        <span>{t('biodata', 'practicing_scale_min')}</span>
                        <span className="font-bold text-primary-600 text-sm">{data.sunni_scale ?? 5}</span>
                        <span>{t('biodata', 'practicing_scale_max')}</span>
                      </div>
                    </div>
                  </div>
                )}
              </>
            )}

            {/* ── Step 4: Education & Career ── */}
            {step === 4 && (
              <>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'education_method')}
                    value={data.education_method ?? ''}
                    onChange={v => setData('education_method', v as never)}
                    options={eduMethodOpts}
                    error={errors.education_method}
                  />
                  <SearchableSelect
                    label={t('biodata', 'highest_qualification')}
                    value={data.highest_qualification ?? ''}
                    onChange={v => setData('highest_qualification', v as never)}
                    options={qualOpts}
                    error={errors.highest_qualification}
                  />
                </div>

                <SectionLabel>Career</SectionLabel>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'occupation_category')}
                    value={data.occupation_category ?? ''}
                    onChange={v => setData('occupation_category', v as never)}
                    options={occCatOpts}
                    error={errors.occupation_category}
                  />
                  <SearchableSelect
                    label={t('biodata', 'occupation')}
                    value={data.occupation ?? ''}
                    onChange={v => setData('occupation', v as never)}
                    options={OCCUPATION_OPTIONS}
                    error={errors.occupation}
                    allowFreeText
                    placeholder="e.g. Software Engineer"
                  />
                </div>

                <Input
                  label={t('biodata', 'monthly_income')}
                  type="number"
                  value={data.monthly_income !== '' && data.monthly_income !== undefined ? String(data.monthly_income) : ''}
                  onChange={e => setData('monthly_income', (e.target.value ? parseInt(e.target.value, 10) : '') as never)}
                  error={errors.monthly_income}
                  placeholder="Monthly income in BDT (e.g. 50000)"
                />

                <WizardTextarea
                  label={t('biodata', 'profession_details')}
                  value={(data.profession_details as string) ?? ''}
                  onChange={v => setData('profession_details', v as never)}
                  error={errors.profession_details}
                  placeholder="Brief description of your work, company, or studies..."
                  rows={3}
                />
              </>
            )}

            {/* ── Step 5: Family ── */}
            {step === 5 && (
              <>
                <SectionLabel>Father's Information</SectionLabel>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <Input
                    label={t('biodata', 'father_name')}
                    value={data.father_name ?? ''}
                    onChange={e => setData('father_name', e.target.value as never)}
                    error={errors.father_name}
                    placeholder="Abdul Karim"
                  />
                  <SearchableSelect
                    label={t('biodata', 'father_profession')}
                    value={data.father_profession ?? ''}
                    onChange={v => setData('father_profession', v as never)}
                    options={PROFESSION_OPTIONS}
                    error={errors.father_profession}
                    allowFreeText
                    placeholder="e.g. Retired, Business"
                  />
                </div>
                <WizardToggle
                  value={!!data.father_alive}
                  label={t('biodata', 'father_alive')}
                  onChange={v => setData('father_alive', v as never)}
                />

                <SectionLabel>Mother's Information</SectionLabel>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <Input
                    label={t('biodata', 'mother_name')}
                    value={data.mother_name ?? ''}
                    onChange={e => setData('mother_name', e.target.value as never)}
                    error={errors.mother_name}
                    placeholder="Fatema Begum"
                  />
                  <SearchableSelect
                    label={t('biodata', 'mother_profession')}
                    value={data.mother_profession ?? ''}
                    onChange={v => setData('mother_profession', v as never)}
                    options={PROFESSION_OPTIONS}
                    error={errors.mother_profession}
                    allowFreeText
                    placeholder="e.g. Housewife, Teacher"
                  />
                </div>
                <WizardToggle
                  value={!!data.mother_alive}
                  label={t('biodata', 'mother_alive')}
                  onChange={v => setData('mother_alive', v as never)}
                />

                <SectionLabel>Siblings</SectionLabel>
                <div className="grid grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'brothers')}
                    value={data.brothers !== '' && data.brothers !== undefined ? String(data.brothers) : ''}
                    onChange={v => setData('brothers', (v !== '' ? parseInt(v, 10) : '') as never)}
                    options={COUNT_OPTIONS}
                    error={errors.brothers}
                    placeholder="0"
                  />
                  <SearchableSelect
                    label={t('biodata', 'sisters')}
                    value={data.sisters !== '' && data.sisters !== undefined ? String(data.sisters) : ''}
                    onChange={v => setData('sisters', (v !== '' ? parseInt(v, 10) : '') as never)}
                    options={COUNT_OPTIONS}
                    error={errors.sisters}
                    placeholder="0"
                  />
                </div>

                <SectionLabel>Family Background</SectionLabel>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'family_type')}
                    value={data.family_type ?? ''}
                    onChange={v => setData('family_type', v as never)}
                    options={familyTypeOpts}
                    error={errors.family_type}
                  />
                  <SearchableSelect
                    label={t('biodata', 'family_financial_status')}
                    value={data.family_financial_status ?? ''}
                    onChange={v => setData('family_financial_status', v as never)}
                    options={financeOpts}
                    error={errors.family_financial_status}
                  />
                  <SearchableSelect
                    label={t('biodata', 'home_ownership')}
                    value={data.home_ownership ?? ''}
                    onChange={v => setData('home_ownership', v as never)}
                    options={homeOpts}
                    error={errors.home_ownership}
                  />
                </div>

                <WizardTextarea
                  label={t('biodata', 'family_details')}
                  value={(data.family_details as string) ?? ''}
                  onChange={v => setData('family_details', v as never)}
                  error={errors.family_details}
                  placeholder="Brief description of your family background..."
                  rows={3}
                />
              </>
            )}

            {/* ── Step 6: Lifestyle & Health ── */}
            {step === 6 && (
              <>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'health_status')}
                    value={data.health_status ?? ''}
                    onChange={v => setData('health_status', v as never)}
                    options={healthOpts}
                    error={errors.health_status}
                  />
                  <SearchableSelect
                    label={t('biodata', 'diet')}
                    value={data.diet ?? ''}
                    onChange={v => setData('diet', v as never)}
                    options={dietOpts}
                    error={errors.diet}
                  />
                  <SearchableSelect
                    label={t('biodata', 'smoking')}
                    value={data.smoking ?? ''}
                    onChange={v => setData('smoking', v as never)}
                    options={smokingOpts}
                    error={errors.smoking}
                  />
                </div>

                <WizardTextarea
                  label={t('biodata', 'hobbies')}
                  value={(data.hobbies as string) ?? ''}
                  onChange={v => setData('hobbies', v as never)}
                  error={errors.hobbies}
                  placeholder="Reading, cooking, traveling, gardening..."
                  rows={3}
                />
              </>
            )}

            {/* ── Step 7: Marriage & Guardian ── */}
            {step === 7 && (
              <>
                {user.gender === 'male' && (
                  <div className="rounded-xl bg-slate-50 border border-slate-100 p-4 space-y-3">
                    <SectionLabel>{t('biodata', 'after_marriage_section')}</SectionLabel>
                    <WizardToggle
                      value={!!data.wife_in_veil}
                      label={t('biodata', 'wife_in_veil')}
                      onChange={v => setData('wife_in_veil', v as never)}
                    />
                    <WizardToggle
                      value={!!data.wife_study_allowed}
                      label={t('biodata', 'wife_study_allowed')}
                      onChange={v => setData('wife_study_allowed', v as never)}
                    />
                    <WizardToggle
                      value={!!data.wife_job_allowed}
                      label={t('biodata', 'wife_job_allowed')}
                      onChange={v => setData('wife_job_allowed', v as never)}
                    />
                    <WizardToggle
                      value={!!data.polygamy_open}
                      label={t('biodata', 'polygamy_open')}
                      onChange={v => setData('polygamy_open', v as never)}
                    />
                  </div>
                )}

                <WizardToggle
                  value={!!data.guardian_agree}
                  label={t('biodata', 'guardian_agree')}
                  onChange={v => setData('guardian_agree', v as never)}
                />

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'residence_after_marriage')}
                    value={data.residence_after_marriage ?? ''}
                    onChange={v => setData('residence_after_marriage', v as never)}
                    options={RESIDENCE_OPTIONS}
                    error={errors.residence_after_marriage}
                    allowFreeText
                    placeholder="Dhaka / Abroad / Flexible"
                  />
                  <SearchableSelect
                    label={t('biodata', 'post_marriage_plan')}
                    value={data.post_marriage_plan ?? ''}
                    onChange={v => setData('post_marriage_plan', v as never)}
                    options={POST_MARRIAGE_OPTIONS}
                    error={errors.post_marriage_plan}
                    allowFreeText
                    placeholder="Stay in BD / Move abroad"
                  />
                </div>

                <Input
                  label={t('biodata', 'children_count')}
                  type="number"
                  value={data.children_count !== '' && data.children_count !== undefined ? String(data.children_count) : ''}
                  onChange={e => setData('children_count', (e.target.value ? parseInt(e.target.value, 10) : '') as never)}
                  error={errors.children_count}
                  placeholder="0"
                />

                <div className="border-t border-slate-100 pt-5">
                  <p className="text-sm font-semibold text-slate-700 mb-4">
                    {t('biodata', 'guardian_contact_section')}
                  </p>
                  <div className="space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <Input
                        label={t('biodata', 'guardian_mobile')}
                        value={data.guardian_mobile ?? ''}
                        onChange={e => setData('guardian_mobile', e.target.value as never)}
                        error={errors.guardian_mobile}
                        placeholder="+88017XXXXXXXX"
                      />
                      <SearchableSelect
                        label={t('biodata', 'guardian_relationship')}
                        value={data.guardian_relationship ?? ''}
                        onChange={v => setData('guardian_relationship', v as never)}
                        options={GUARDIAN_REL_OPTIONS}
                        error={errors.guardian_relationship}
                        allowFreeText
                        placeholder="Father / Brother / Uncle"
                      />
                    </div>
                    <Input
                      label={t('biodata', 'guardian_email')}
                      type="email"
                      value={data.guardian_email ?? ''}
                      onChange={e => setData('guardian_email', e.target.value as never)}
                      error={errors.guardian_email}
                      placeholder="guardian@example.com"
                    />
                  </div>
                </div>
              </>
            )}

            {/* ── Step 8: Partner Preferences ── */}
            {step === 8 && (
              <>
                <SectionLabel>Age & Height Range</SectionLabel>
                <div className="grid grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'partner_age_min')}
                    value={data.partner_age_min !== '' && data.partner_age_min !== undefined ? String(data.partner_age_min) : ''}
                    onChange={v => setData('partner_age_min', (v ? parseInt(v, 10) : '') as never)}
                    options={ageOpts}
                    error={errors.partner_age_min}
                    placeholder="Min age"
                  />
                  <SearchableSelect
                    label={t('biodata', 'partner_age_max')}
                    value={data.partner_age_max !== '' && data.partner_age_max !== undefined ? String(data.partner_age_max) : ''}
                    onChange={v => setData('partner_age_max', (v ? parseInt(v, 10) : '') as never)}
                    options={ageOpts}
                    error={errors.partner_age_max}
                    placeholder="Max age"
                  />
                </div>

                <div className="grid grid-cols-2 gap-4">
                  <HeightSelect
                    label={t('biodata', 'partner_height_cm_min')}
                    value={data.partner_height_cm_min ?? ''}
                    onChange={v => setData('partner_height_cm_min', v as never)}
                    error={errors.partner_height_cm_min}
                    placeholder="Min height"
                  />
                  <HeightSelect
                    label={t('biodata', 'partner_height_cm_max')}
                    value={data.partner_height_cm_max ?? ''}
                    onChange={v => setData('partner_height_cm_max', v as never)}
                    error={errors.partner_height_cm_max}
                    placeholder="Max height"
                  />
                </div>

                <SectionLabel>Income Range</SectionLabel>
                <div className="grid grid-cols-2 gap-4">
                  <Input
                    label={t('biodata', 'partner_income_min')}
                    type="number"
                    value={data.partner_income_min !== '' && data.partner_income_min !== undefined ? String(data.partner_income_min) : ''}
                    onChange={e => setData('partner_income_min', (e.target.value ? parseInt(e.target.value, 10) : '') as never)}
                    error={errors.partner_income_min}
                    placeholder="0"
                  />
                  <Input
                    label={t('biodata', 'partner_income_max')}
                    type="number"
                    value={data.partner_income_max !== '' && data.partner_income_max !== undefined ? String(data.partner_income_max) : ''}
                    onChange={e => setData('partner_income_max', (e.target.value ? parseInt(e.target.value, 10) : '') as never)}
                    error={errors.partner_income_max}
                    placeholder="0"
                  />
                </div>

                <SectionLabel>Partner Profile</SectionLabel>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'partner_marital_status')}
                    value={data.partner_marital_status ?? ''}
                    onChange={v => setData('partner_marital_status', v as never)}
                    options={partnerMaritalOpts}
                    error={errors.partner_marital_status}
                  />
                  <SearchableSelect
                    label={t('biodata', 'partner_complexion') || 'Partner Complexion'}
                    value={data.partner_complexion ?? ''}
                    onChange={v => setData('partner_complexion', v as never)}
                    options={complexionOpts}
                    error={errors.partner_complexion}
                  />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'partner_education')}
                    value={data.partner_education ?? ''}
                    onChange={v => setData('partner_education', v as never)}
                    options={partnerEduOpts}
                    error={errors.partner_education}
                  />
                  <SearchableSelect
                    label={t('biodata', 'partner_family_type')}
                    value={data.partner_family_type ?? ''}
                    onChange={v => setData('partner_family_type', v as never)}
                    options={partnerFamilyOpts}
                    error={errors.partner_family_type}
                  />
                </div>

                <SectionLabel>Preferred Location (Bangladesh)</SectionLabel>
                <BangladeshAddressPicker
                  value={{
                    division: (data.partner_division as string) ?? undefined,
                    district: (data.partner_district as string) ?? undefined,
                  }}
                  onChange={val => setData({
                    ...data,
                    partner_division: val.division ?? '',
                    partner_district: val.district ?? '',
                  })}
                  showUpazila={false}
                />

                <WizardTextarea
                  label={t('biodata', 'partner_expectations')}
                  value={(data.partner_expectations as string) ?? ''}
                  onChange={v => setData('partner_expectations', v as never)}
                  error={errors.partner_expectations}
                  placeholder="Describe the qualities you're looking for in a life partner..."
                  rows={5}
                  maxLength={1000}
                />
              </>
            )}

            {/* ── Step 9: Photos ── */}
            {step === 9 && (
              <div className="text-center py-10">
                <div className="mx-auto w-24 h-24 rounded-full bg-amber-50 border-2 border-amber-100 flex items-center justify-center text-5xl mb-5">
                  📷
                </div>
                <h3 className="text-lg font-bold text-slate-900 mb-2">{t('biodata', 'step9_title')}</h3>
                <p className="text-sm text-slate-500 mb-2 max-w-sm mx-auto leading-relaxed">
                  {t('biodata', 'step9_desc')}
                </p>
                <p className="text-xs text-amber-600 font-medium mb-6">
                  {t('biodata', 'step9_note')}
                </p>
                <p className="text-xs text-slate-400">{t('biodata', 'step9_submit_hint')}</p>
              </div>
            )}

            {/* ── Navigation ── */}
            <div className="flex flex-col-reverse sm:flex-row gap-3 pt-4 border-t border-slate-100">
              {step > 1 && (
                <Button
                  type="button"
                  variant="outline"
                  className="sm:w-auto sm:px-6"
                  onClick={() => router.get(route('biodata.wizard', { step: step - 1 }))}
                >
                  ← {t('common', 'back')}
                </Button>
              )}
              <Button
                type="submit"
                className="flex-1"
                size="lg"
                isLoading={processing}
              >
                {step === totalSteps
                  ? t('biodata', 'wizard_complete')
                  : `${t('biodata', 'wizard_next')} →`}
              </Button>
            </div>

          </form>
        </div>

        <p className="text-center text-xs text-slate-400 mt-4">
          {t('biodata', 'wizard_review_notice')}
        </p>
      </div>
    </AppLayout>
  )
}
