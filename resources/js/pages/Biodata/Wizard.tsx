/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import { useState } from 'react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { SearchableSelect } from '@/components/ui/SearchableSelect'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import { CheckCircle, Save } from 'lucide-react'
import { BD_DIVISIONS, BD_DISTRICTS } from '@/data/bangladesh'

interface BiodataData {
  // Step 1: General
  marital_status?: string
  birth_date?: string
  height_cm?: number | ''
  weight_kg?: number | ''
  complexion?: string
  blood_group?: string
  about_me?: string
  profile_headline?: string
  mother_tongue?: string
  // Step 2: Location
  division?: string
  district?: string
  upazila?: string
  residing_country?: string
  residing_city?: string
  is_nrb?: boolean
  visa_status?: string
  // Step 3: Religion
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
  // Step 4: Education & Career
  education_method?: string
  highest_qualification?: string
  occupation?: string
  occupation_category?: string
  profession_details?: string
  monthly_income?: number | ''
  // Step 5: Family
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
  // Step 6: Lifestyle
  health_status?: string
  diet?: string
  smoking?: string
  hobbies?: string
  // Step 7: Marriage
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
  // Step 8: Partner
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

export default function BiodataWizard({ step, steps, biodata, user }: Props) {
  const totalSteps = Object.keys(steps).length
  const { t } = useTranslation()
  const completenessScore = biodata.completeness_score ?? 0

  const { data, setData, post, processing, errors } = useForm<BiodataData>({
    ...biodata,
  })

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

  // ── Reusable sub-components ──────────────────────────────────────────────
  type FieldName = keyof BiodataData

  const Field = ({
    name, label, type = 'text', placeholder = '', required = false,
  }: {
    name: FieldName; label: string; type?: string
    placeholder?: string; required?: boolean
  }) => (
    <Input
      label={label}
      type={type}
      value={(data[name] as string | number | undefined) ?? ''}
      onChange={e => setData(name, e.target.value as never)}
      error={errors[name]}
      placeholder={placeholder}
      required={required}
    />
  )

  const Sel = ({
    name, label, options, allowFreeText = false,
  }: {
    name: FieldName; label: string
    options: { value: string; label: string }[]
    allowFreeText?: boolean
  }) => (
    <SearchableSelect
      label={label}
      value={(data[name] as string) ?? ''}
      onChange={v => setData(name, v as never)}
      options={options}
      error={errors[name]}
      allowFreeText={allowFreeText}
    />
  )

  const Toggle = ({ name, label }: { name: FieldName; label: string }) => (
    <label className="flex items-center gap-3 cursor-pointer select-none">
      <div
        className={cn(
          'relative w-11 h-6 rounded-full transition-colors shrink-0',
          data[name] ? 'bg-primary-600' : 'bg-slate-300',
        )}
        onClick={() => setData(name, !data[name] as never)}
      >
        <div className={cn(
          'absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-transform',
          data[name] ? 'translate-x-6' : 'translate-x-1',
        )} />
      </div>
      <span className="text-sm text-slate-700">{label}</span>
    </label>
  )

  const Textarea = ({
    name, label, placeholder = '', rows = 4, maxLength,
  }: {
    name: FieldName; label: string; placeholder?: string
    rows?: number; maxLength?: number
  }) => {
    const val = (data[name] as string) ?? ''
    return (
      <div>
        <div className="flex items-center justify-between mb-1">
          <label className="block text-sm font-medium text-slate-700">{label}</label>
          {maxLength && (
            <span className={cn(
              'text-xs',
              val.length > maxLength * 0.9 ? 'text-amber-600' : 'text-slate-400',
            )}>
              {val.length}/{maxLength}
            </span>
          )}
        </div>
        <textarea
          value={val}
          onChange={e => setData(name, e.target.value as never)}
          rows={rows}
          maxLength={maxLength}
          placeholder={placeholder}
          className="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 resize-none"
        />
        {errors[name] && <p className="mt-1 text-xs text-red-600">{errors[name]}</p>}
      </div>
    )
  }

  // ── Step label / helper ───────────────────────────────────────────────────
  const currentLabel = t('biodata', `step_labels.${step}`)
  const currentHelper = t('biodata', `step_helper.${step}`)

  // ── Cascading location (BD) ───────────────────────────────────────────────
  const divisionOptions = BD_DIVISIONS.map(d => ({ value: d, label: d }))
  const districtOptions = (data.division && BD_DISTRICTS[data.division as string])
    ? BD_DISTRICTS[data.division as string]!.map(d => ({ value: d, label: d }))
    : []

  return (
    <AppLayout>
      <Head title={t('biodata', 'wizard_title')} />

      <div className="max-w-2xl mx-auto px-4 py-6">

        {/* ── Overall completion bar ── */}
        {completenessScore > 0 && (
          <div className="mb-5">
            <div className="flex items-center justify-between text-xs text-slate-500 mb-1.5">
              <span>{t('biodata', 'profile_completion')}</span>
              <span className="font-semibold text-slate-700">{completenessScore}%</span>
            </div>
            <div className="h-2 rounded-full bg-slate-200 overflow-hidden">
              <div
                className={cn(
                  'h-full rounded-full transition-all duration-500',
                  completenessScore >= 80 ? 'bg-emerald-500' :
                  completenessScore >= 50 ? 'bg-primary-500' : 'bg-amber-400',
                )}
                style={{ width: `${completenessScore}%` }}
              />
            </div>
          </div>
        )}

        {/* ── Step progress tabs ── */}
        <div className="flex items-center gap-0.5 mb-6 overflow-x-auto pb-1">
          {Array.from({ length: totalSteps }, (_, i) => i + 1).map(num => (
            <div key={num} className="flex items-center gap-0.5 flex-shrink-0">
              <button
                type="button"
                onClick={() => num < step && router.get(route('biodata.wizard', { step: num }))}
                className={cn(
                  'h-7 w-7 rounded-full flex items-center justify-center text-xs font-bold transition-all',
                  step > num ? 'bg-emerald-500 text-white cursor-pointer hover:bg-emerald-600' :
                  step === num ? 'bg-primary-600 text-white cursor-default' :
                  'bg-slate-200 text-slate-400 cursor-default',
                )}
              >
                {step > num ? <CheckCircle size={14} /> : num}
              </button>
              {num < totalSteps && (
                <div className={cn('w-3 h-0.5', step > num ? 'bg-emerald-300' : 'bg-slate-200')} />
              )}
            </div>
          ))}
        </div>

        {/* ── Form card ── */}
        <div className="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

          {/* Card header */}
          <div className="px-6 py-5 border-b border-slate-100 bg-slate-50">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-xs font-medium text-slate-400 uppercase tracking-wide mb-0.5">
                  {t('biodata', 'wizard_subtitle', { step: String(step), total: String(totalSteps) })}
                </p>
                <h2 className="text-lg font-bold text-slate-900">{currentLabel}</h2>
                {currentHelper && (
                  <p className="text-sm text-slate-500 mt-0.5">{currentHelper}</p>
                )}
              </div>
              <button
                type="button"
                onClick={saveDraft}
                disabled={savingDraft || processing}
                className="flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:border-slate-400 transition-colors disabled:opacity-50"
              >
                <Save size={13} />
                {savingDraft ? t('common', 'saving') : t('biodata', 'wizard_save_draft')}
              </button>
            </div>
          </div>

          {/* Form body */}
          <form onSubmit={submit} className="p-6 space-y-5">

            {/* ── Step 1: General ── */}
            {step === 1 && (
              <>
                <Sel name="marital_status" label={t('biodata', 'marital_status')} options={[
                  { value: 'never_married', label: t('biodata', 'never_married') },
                  { value: 'married',       label: t('biodata', 'married') },
                  { value: 'divorced',      label: t('biodata', 'divorced') },
                  { value: 'widowed',       label: t('biodata', 'widowed') },
                ]} />
                <Field name="birth_date" label={t('biodata', 'birth_date')} type="date" />
                <div className="grid grid-cols-2 gap-4">
                  <Field name="height_cm" label={t('biodata', 'height')} type="number" placeholder="e.g. 165" />
                  <Field name="weight_kg" label={t('biodata', 'weight')} type="number" placeholder="e.g. 60" />
                </div>
                <Sel name="complexion" label={t('biodata', 'complexion')} options={[
                  { value: 'very_fair', label: t('biodata', 'very_fair') },
                  { value: 'fair',      label: t('biodata', 'fair') },
                  { value: 'wheatish',  label: t('biodata', 'wheatish') },
                  { value: 'medium',    label: t('biodata', 'medium') },
                  { value: 'dark',      label: t('biodata', 'dark') },
                ]} />
                <Sel name="blood_group" label={t('biodata', 'blood_group')} options={
                  ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'].map(b => ({ value: b, label: b }))
                } />
                <Field name="mother_tongue" label={t('biodata', 'mother_tongue')} placeholder="e.g. Bangla, Sylheti" />
                <Field name="profile_headline" label={t('biodata', 'profile_headline')} placeholder="e.g. Practicing Muslim, Engineer in Dhaka" />
                <Textarea
                  name="about_me"
                  label={t('biodata', 'about_me')}
                  placeholder="Write a brief introduction — your personality, what you value, what you're looking for..."
                  rows={5}
                  maxLength={1000}
                />
                <p className="text-xs text-slate-400">
                  {t('biodata', 'about_me_tip')}
                </p>
              </>
            )}

            {/* ── Step 2: Location ── */}
            {step === 2 && (
              <>
                <Field name="residing_country" label={t('biodata', 'residing_country')} placeholder="Bangladesh" />
                <Field name="residing_city" label={t('biodata', 'residing_city')} placeholder="Dhaka" />
                <Sel name="division" label={t('biodata', 'division')} options={divisionOptions} />
                <SearchableSelect
                  label={t('biodata', 'district')}
                  value={(data.district as string) ?? ''}
                  onChange={v => setData('district', v)}
                  options={districtOptions}
                  placeholder={data.division ? '— Select district —' : '— Select division first —'}
                  disabled={!data.division}
                  error={errors.district as string | undefined}
                />
                <Field name="upazila" label={t('biodata', 'upazila')} placeholder="e.g. Mirpur" />
                <Toggle name="is_nrb" label={t('biodata', 'is_nrb')} />
                <Sel name="visa_status" label={t('biodata', 'visa_status')} options={[
                  { value: 'citizen',            label: 'Citizen' },
                  { value: 'permanent_resident', label: 'Permanent Resident' },
                  { value: 'work_visa',           label: 'Work Visa' },
                  { value: 'student_visa',        label: 'Student Visa' },
                ]} />
              </>
            )}

            {/* ── Step 3: Religion ── */}
            {step === 3 && (
              <>
                <Field name="religion" label={t('biodata', 'religion')} placeholder="Islam" />
                <Field name="sect" label={t('biodata', 'sect')} placeholder="e.g. Hanafi, Ahle Hadith" />
                <Toggle name="is_practicing" label={t('biodata', 'is_practicing')} />
                <Sel name="prayers_info" label={t('biodata', 'prayers_info')} options={[
                  { value: '5_times',  label: t('biodata', 'prayers_5_times') },
                  { value: '4_times',  label: t('biodata', 'prayers_4_times') },
                  { value: 'sometimes',label: t('biodata', 'prayers_sometimes') },
                  { value: 'rarely',   label: t('biodata', 'prayers_rarely') },
                  { value: 'never',    label: t('biodata', 'prayers_never') },
                ]} />
                <Sel name="quran_recitation" label={t('biodata', 'quran_recitation')} options={[
                  { value: 'fluent',   label: t('biodata', 'quran_fluent') },
                  { value: 'basic',    label: t('biodata', 'quran_basic') },
                  { value: 'learning', label: t('biodata', 'quran_learning') },
                  { value: 'no',       label: t('biodata', 'quran_no') },
                ]} />
                {user.gender === 'female'
                  ? <Sel name="hijab_info" label={t('biodata', 'hijab_info')} options={[
                      { value: 'wears_niqab', label: 'Wears Niqab' },
                      { value: 'wears_hijab', label: 'Wears Hijab' },
                      { value: 'trying',      label: 'Trying to wear' },
                      { value: 'no_hijab',    label: 'Does not wear' },
                    ]} />
                  : <Field name="beard_info" label={t('biodata', 'beard_info')} placeholder="e.g. Full beard, Trimmed" />
                }
                <Toggle name="is_islamically_educated" label={t('biodata', 'is_islamically_educated')} />
                {user.mode === 'islamic' && (
                  <>
                    <Toggle name="wali_approval" label={t('biodata', 'wali_approval')} />
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-1">
                        {t('biodata', 'sunni_scale')}
                      </label>
                      <input
                        type="range"
                        min={1}
                        max={10}
                        value={(data.sunni_scale as number) ?? 5}
                        onChange={e => setData('sunni_scale', parseInt(e.target.value))}
                        className="w-full accent-primary-600"
                      />
                      <div className="flex justify-between text-xs text-slate-400 mt-1">
                        <span>{t('biodata', 'practicing_scale_min')}</span>
                        <span className="font-semibold text-primary-600">{data.sunni_scale ?? 5}</span>
                        <span>{t('biodata', 'practicing_scale_max')}</span>
                      </div>
                    </div>
                  </>
                )}
              </>
            )}

            {/* ── Step 4: Education & Career ── */}
            {step === 4 && (
              <>
                <Sel name="education_method" label={t('biodata', 'education_method')} options={[
                  { value: 'general', label: 'General' },
                  { value: 'islamic', label: 'Islamic (Madrasa)' },
                  { value: 'both',    label: 'Both General & Islamic' },
                ]} />
                <Sel name="highest_qualification" label={t('biodata', 'highest_qualification')} options={[
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
                ]} />
                <Field name="occupation" label={t('biodata', 'occupation')} placeholder="e.g. Software Engineer" />
                <Sel name="occupation_category" label={t('biodata', 'occupation_category')} options={[
                  { value: 'business',       label: 'Business / Entrepreneur' },
                  { value: 'service_govt',   label: 'Government Job' },
                  { value: 'service_private',label: 'Private Job' },
                  { value: 'education',      label: 'Education / Teacher' },
                  { value: 'medical',        label: 'Medical / Healthcare' },
                  { value: 'engineering',    label: 'Engineering' },
                  { value: 'it',             label: 'IT / Tech' },
                  { value: 'abroad_job',     label: 'Working Abroad' },
                  { value: 'student',        label: 'Student' },
                  { value: 'housewife',      label: 'Housewife' },
                  { value: 'agriculture',    label: 'Agriculture' },
                  { value: 'other',          label: 'Other' },
                ]} />
                <Field name="monthly_income" label={t('biodata', 'monthly_income')} type="number" placeholder="e.g. 50000" />
                <Textarea
                  name="profession_details"
                  label={t('biodata', 'profession_details')}
                  placeholder="Brief description of your work..."
                  rows={3}
                />
              </>
            )}

            {/* ── Step 5: Family ── */}
            {step === 5 && (
              <>
                <div className="grid grid-cols-2 gap-4">
                  <Field name="father_name" label={t('biodata', 'father_name')} placeholder="Abdul Karim" />
                  <Field name="father_profession" label={t('biodata', 'father_profession')} placeholder="Retired" />
                </div>
                <Toggle name="father_alive" label={t('biodata', 'father_alive')} />
                <div className="grid grid-cols-2 gap-4">
                  <Field name="mother_name" label={t('biodata', 'mother_name')} placeholder="Fatema Begum" />
                  <Field name="mother_profession" label={t('biodata', 'mother_profession')} placeholder="Housewife" />
                </div>
                <Toggle name="mother_alive" label={t('biodata', 'mother_alive')} />
                <div className="grid grid-cols-2 gap-4">
                  <Field name="brothers" label={t('biodata', 'brothers')} type="number" placeholder="0" />
                  <Field name="sisters" label={t('biodata', 'sisters')} type="number" placeholder="0" />
                </div>
                <Sel name="family_type" label={t('biodata', 'family_type')} options={[
                  { value: 'joint',    label: t('biodata', 'family_joint') },
                  { value: 'nuclear',  label: t('biodata', 'family_nuclear') },
                  { value: 'flexible', label: t('biodata', 'family_flexible') },
                ]} />
                <Sel name="family_financial_status" label={t('biodata', 'family_financial_status')} options={[
                  { value: 'lower',        label: t('biodata', 'finance_lower') },
                  { value: 'lower_middle', label: t('biodata', 'finance_lower_middle') },
                  { value: 'middle',       label: t('biodata', 'finance_middle') },
                  { value: 'upper_middle', label: t('biodata', 'finance_upper_middle') },
                  { value: 'upper',        label: t('biodata', 'finance_upper') },
                ]} />
                <Sel name="home_ownership" label={t('biodata', 'home_ownership')} options={[
                  { value: 'own_house',    label: 'Own House' },
                  { value: 'family_house', label: 'Family House' },
                  { value: 'rented',       label: 'Rented' },
                ]} />
                <Textarea
                  name="family_details"
                  label={t('biodata', 'family_details')}
                  placeholder="Brief description of your family..."
                  rows={3}
                />
              </>
            )}

            {/* ── Step 6: Lifestyle & Health ── */}
            {step === 6 && (
              <>
                <Sel name="health_status" label={t('biodata', 'health_status')} options={[
                  { value: 'healthy',          label: 'Healthy' },
                  { value: 'minor_condition',  label: 'Minor Condition' },
                  { value: 'disability',       label: 'Disability' },
                  { value: 'prefer_not_say',   label: 'Prefer not to say' },
                ]} />
                <Sel name="diet" label={t('biodata', 'diet')} options={[
                  { value: 'halal_only',     label: 'Halal Only' },
                  { value: 'vegetarian',     label: 'Vegetarian' },
                  { value: 'no_restriction', label: 'No Restriction' },
                ]} />
                <Sel name="smoking" label={t('biodata', 'smoking')} options={[
                  { value: 'never',        label: 'Never' },
                  { value: 'occasionally', label: 'Occasionally' },
                  { value: 'regularly',    label: 'Regularly' },
                ]} />
                <Textarea
                  name="hobbies"
                  label={t('biodata', 'hobbies')}
                  placeholder="Reading, cooking, traveling, gardening..."
                  rows={3}
                />
              </>
            )}

            {/* ── Step 7: Marriage & Guardian ── */}
            {step === 7 && (
              <>
                {user.gender === 'male' && (
                  <div className="rounded-xl bg-slate-50 p-4 space-y-3">
                    <p className="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                      {t('biodata', 'after_marriage_section')}
                    </p>
                    <Toggle name="wife_in_veil" label={t('biodata', 'wife_in_veil')} />
                    <Toggle name="wife_study_allowed" label={t('biodata', 'wife_study_allowed')} />
                    <Toggle name="wife_job_allowed" label={t('biodata', 'wife_job_allowed')} />
                    <Toggle name="polygamy_open" label={t('biodata', 'polygamy_open')} />
                  </div>
                )}
                <Toggle name="guardian_agree" label={t('biodata', 'guardian_agree')} />
                <Field name="residence_after_marriage" label={t('biodata', 'residence_after_marriage')} placeholder="Dhaka / Abroad / Flexible" />
                <Field name="post_marriage_plan" label={t('biodata', 'post_marriage_plan')} placeholder="Stay in BD / Move abroad / Flexible" />
                <Field name="children_count" label={t('biodata', 'children_count')} type="number" placeholder="0" />
                <div className="border-t border-slate-200 pt-5">
                  <p className="text-sm font-semibold text-slate-700 mb-3">
                    {t('biodata', 'guardian_contact_section')}
                  </p>
                  <div className="space-y-4">
                    <Field name="guardian_mobile" label={t('biodata', 'guardian_mobile')} placeholder="+88017XXXXXXXX" />
                    <Field name="guardian_relationship" label={t('biodata', 'guardian_relationship')} placeholder="Father / Brother / Uncle" />
                    <Field name="guardian_email" label={t('biodata', 'guardian_email')} type="email" placeholder="guardian@example.com" />
                  </div>
                </div>
              </>
            )}

            {/* ── Step 8: Partner Preferences ── */}
            {step === 8 && (
              <>
                <div>
                  <p className="text-sm font-medium text-slate-700 mb-2">{t('biodata', 'partner_age_range')}</p>
                  <div className="grid grid-cols-2 gap-4">
                    <Field name="partner_age_min" label={t('biodata', 'partner_age_min')} type="number" placeholder="22" />
                    <Field name="partner_age_max" label={t('biodata', 'partner_age_max')} type="number" placeholder="35" />
                  </div>
                </div>
                <div>
                  <p className="text-sm font-medium text-slate-700 mb-2">{t('biodata', 'partner_height_range')}</p>
                  <div className="grid grid-cols-2 gap-4">
                    <Field name="partner_height_cm_min" label={t('biodata', 'partner_height_cm_min')} type="number" placeholder="155" />
                    <Field name="partner_height_cm_max" label={t('biodata', 'partner_height_cm_max')} type="number" placeholder="185" />
                  </div>
                </div>
                <div>
                  <p className="text-sm font-medium text-slate-700 mb-2">{t('biodata', 'partner_income_range')}</p>
                  <div className="grid grid-cols-2 gap-4">
                    <Field name="partner_income_min" label={t('biodata', 'partner_income_min')} type="number" placeholder="0" />
                    <Field name="partner_income_max" label={t('biodata', 'partner_income_max')} type="number" placeholder="0" />
                  </div>
                </div>
                <Sel name="partner_marital_status" label={t('biodata', 'partner_marital_status')} options={[
                  { value: 'never_married', label: t('biodata', 'never_married') },
                  { value: 'divorced',      label: t('biodata', 'divorced') },
                  { value: 'widowed',       label: t('biodata', 'widowed') },
                  { value: 'any',           label: 'Any' },
                ]} />
                <Sel name="partner_education" label={t('biodata', 'partner_education')} options={[
                  { value: 'ssc',             label: t('biodata', 'qual_ssc') },
                  { value: 'hsc',             label: t('biodata', 'qual_hsc') },
                  { value: 'graduation',      label: t('biodata', 'qual_graduation') },
                  { value: 'post_graduation', label: t('biodata', 'qual_post_graduation') },
                  { value: 'any',             label: 'No preference' },
                ]} />
                <Sel name="partner_division" label={t('biodata', 'partner_division')} options={[
                  { value: 'any', label: 'Any Division' },
                  ...BD_DIVISIONS.map(d => ({ value: d, label: d })),
                ]} />
                <Sel name="partner_family_type" label={t('biodata', 'partner_family_type')} options={[
                  { value: 'joint',    label: t('biodata', 'family_joint') },
                  { value: 'nuclear',  label: t('biodata', 'family_nuclear') },
                  { value: 'any',      label: 'No preference' },
                ]} />
                <Textarea
                  name="partner_expectations"
                  label={t('biodata', 'partner_expectations')}
                  placeholder="Describe the qualities you're looking for in a life partner..."
                  rows={5}
                  maxLength={1000}
                />
              </>
            )}

            {/* ── Step 9: Photos ── */}
            {step === 9 && (
              <div className="text-center py-8">
                <div className="mx-auto w-20 h-20 rounded-full bg-amber-50 flex items-center justify-center text-4xl mb-4">
                  📷
                </div>
                <h3 className="font-bold text-slate-900 mb-2">{t('biodata', 'step9_title')}</h3>
                <p className="text-sm text-slate-500 mb-1 max-w-sm mx-auto">
                  {t('biodata', 'step9_desc')}
                </p>
                <p className="text-xs text-amber-600 mb-6">
                  {t('biodata', 'step9_note')}
                </p>
                <p className="text-xs text-slate-400">
                  {t('biodata', 'step9_submit_hint')}
                </p>
              </div>
            )}

            {/* ── Navigation buttons ── */}
            <div className="flex gap-3 pt-2 border-t border-slate-100">
              {step > 1 && (
                <Button
                  type="button"
                  variant="outline"
                  className="flex-1 sm:flex-none sm:px-6"
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
                {step === totalSteps ? t('biodata', 'wizard_complete') : `${t('biodata', 'wizard_next')} →`}
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
