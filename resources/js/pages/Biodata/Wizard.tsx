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
  residence_after_marriage?: string
  post_marriage_plan?: string
  guardian_mobile?: string
  guardian_email?: string
  // Step 8: Partner
  partner_age_min?: number | ''
  partner_age_max?: number | ''
  partner_height_cm_min?: number | ''
  partner_height_cm_max?: number | ''
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

  // ── Step labels / helpers (from translations via dot-notation) ────────────
  const STEP_LABELS = ['General', 'Location', 'Religion', 'Education', 'Family', 'Lifestyle', 'Marriage', 'Partner', 'Photos']
  const currentLabel = t('biodata', `step_labels.${step}`) || STEP_LABELS[step - 1] || `Step ${step}`
  const currentHelper = t('biodata', `step_helper.${step}`)

  // ── Cascading location (BD) ───────────────────────────────────────────────
  const divisionOptions = BD_DIVISIONS.map(d => ({ value: d, label: d }))
  const districtOptions = (data.division && BD_DISTRICTS[data.division as string])
    ? BD_DISTRICTS[data.division as string]!.map(d => ({ value: d, label: d }))
    : []

  return (
    <AppLayout>
      <Head title={`Biodata — Step ${step} of ${totalSteps}`} />

      <div className="max-w-2xl mx-auto px-4 py-6">

        {/* ── Overall completion bar ── */}
        {completenessScore > 0 && (
          <div className="mb-5">
            <div className="flex items-center justify-between text-xs text-slate-500 mb-1.5">
              <span>Profile Completion</span>
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
                  Step {step} of {totalSteps}
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
                {savingDraft ? 'Saving…' : 'Save Draft'}
              </button>
            </div>
          </div>

          {/* Form body */}
          <form onSubmit={submit} className="p-6 space-y-5">

            {/* ── Step 1: General ── */}
            {step === 1 && (
              <>
                <Sel name="marital_status" label="Marital Status" options={[
                  { value: 'never_married', label: 'Never Married' },
                  { value: 'married', label: 'Married' },
                  { value: 'divorced', label: 'Divorced' },
                  { value: 'widowed', label: 'Widowed' },
                ]} />
                <Field name="birth_date" label="Date of Birth" type="date" />
                <div className="grid grid-cols-2 gap-4">
                  <Field name="height_cm" label="Height (cm)" type="number" placeholder="e.g. 165" />
                  <Field name="weight_kg" label="Weight (kg)" type="number" placeholder="e.g. 60" />
                </div>
                <Sel name="complexion" label="Complexion" options={[
                  { value: 'very_fair', label: 'Very Fair' },
                  { value: 'fair', label: 'Fair' },
                  { value: 'wheatish', label: 'Wheatish' },
                  { value: 'medium', label: 'Medium' },
                  { value: 'dark', label: 'Dark' },
                ]} />
                <Sel name="blood_group" label="Blood Group" options={
                  ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'].map(b => ({ value: b, label: b }))
                } />
                <Field name="mother_tongue" label="Mother Tongue" placeholder="e.g. Bangla, Sylheti" />
                <Field name="profile_headline" label="Profile Headline" placeholder="e.g. Practicing Muslim, Engineer in Dhaka" />
                <Textarea
                  name="about_me"
                  label="About Me"
                  placeholder="Write a brief introduction — your personality, what you value, what you're looking for..."
                  rows={5}
                  maxLength={1000}
                />
                <p className="text-xs text-slate-400">
                  Tip: profiles with 100+ characters in About Me get a 10% completeness bonus.
                </p>
              </>
            )}

            {/* ── Step 2: Location ── */}
            {step === 2 && (
              <>
                <Field name="residing_country" label="Currently Residing In" placeholder="Bangladesh" />
                <Field name="residing_city" label="City (current)" placeholder="Dhaka" />
                <Sel name="division" label="Division (permanent home)" options={divisionOptions} />
                <SearchableSelect
                  label="District (permanent home)"
                  value={(data.district as string) ?? ''}
                  onChange={v => setData('district', v)}
                  options={districtOptions}
                  placeholder={data.division ? '— Select district —' : '— Select division first —'}
                  disabled={!data.division}
                  error={errors.district as string | undefined}
                />
                <Field name="upazila" label="Upazila / Thana" placeholder="e.g. Mirpur" />
                <Toggle name="is_nrb" label="Non-Resident Bangladeshi (NRB)" />
                <Sel name="visa_status" label="Visa / Residency Status" options={[
                  { value: 'citizen', label: 'Citizen' },
                  { value: 'permanent_resident', label: 'Permanent Resident' },
                  { value: 'work_visa', label: 'Work Visa' },
                  { value: 'student_visa', label: 'Student Visa' },
                ]} />
              </>
            )}

            {/* ── Step 3: Religion ── */}
            {step === 3 && (
              <>
                <Field name="religion" label="Religion" placeholder="Islam" />
                <Field name="sect" label="Sect / Madhab" placeholder="e.g. Hanafi, Ahle Hadith" />
                <Toggle name="is_practicing" label="Practicing Muslim" />
                <Sel name="prayers_info" label="Daily Prayers" options={[
                  { value: '5_times', label: '5 times daily (Alhamdulillah)' },
                  { value: '4_times', label: 'Mostly 5 times' },
                  { value: 'sometimes', label: 'Sometimes' },
                  { value: 'rarely', label: 'Rarely' },
                  { value: 'never', label: 'Not yet' },
                ]} />
                <Sel name="quran_recitation" label="Quran Recitation" options={[
                  { value: 'fluent', label: 'Fluent' },
                  { value: 'basic', label: 'Basic' },
                  { value: 'learning', label: 'Currently Learning' },
                  { value: 'no', label: 'No' },
                ]} />
                {user.gender === 'female'
                  ? <Sel name="hijab_info" label="Hijab / Niqab" options={[
                      { value: 'wears_niqab', label: 'Wears Niqab' },
                      { value: 'wears_hijab', label: 'Wears Hijab' },
                      { value: 'trying', label: 'Trying to wear' },
                      { value: 'no_hijab', label: 'Does not wear' },
                    ]} />
                  : <Field name="beard_info" label="Beard" placeholder="e.g. Full beard, Trimmed" />
                }
                <Toggle name="is_islamically_educated" label="Islamically Educated (Alim / Hafez / Islamic Course)" />
                {user.mode === 'islamic' && (
                  <>
                    <Toggle name="wali_approval" label="Wali / Guardian Approves This Profile" />
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-1">
                        Practicing Scale (1–10)
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
                        <span>1 – Minimal</span>
                        <span className="font-semibold text-primary-600">{data.sunni_scale ?? 5}</span>
                        <span>10 – Devout</span>
                      </div>
                    </div>
                  </>
                )}
              </>
            )}

            {/* ── Step 4: Education & Career ── */}
            {step === 4 && (
              <>
                <Sel name="education_method" label="Education System" options={[
                  { value: 'general', label: 'General' },
                  { value: 'islamic', label: 'Islamic (Madrasa)' },
                  { value: 'both', label: 'Both General & Islamic' },
                ]} />
                <Sel name="highest_qualification" label="Highest Qualification" options={[
                  { value: 'below_ssc', label: 'Below SSC' },
                  { value: 'ssc', label: 'SSC / O-Level' },
                  { value: 'hsc', label: 'HSC / A-Level' },
                  { value: 'diploma', label: 'Diploma' },
                  { value: 'graduation', label: "Bachelor's Degree" },
                  { value: 'post_graduation', label: "Master's Degree" },
                  { value: 'phd', label: 'PhD / Doctorate' },
                  { value: 'hafez', label: 'Hafez' },
                  { value: 'alim', label: 'Alim' },
                  { value: 'fazil', label: 'Fazil' },
                  { value: 'kamil', label: 'Kamil' },
                ]} />
                <Field name="occupation" label="Current Occupation" placeholder="e.g. Software Engineer" />
                <Sel name="occupation_category" label="Occupation Category" options={[
                  { value: 'business', label: 'Business / Entrepreneur' },
                  { value: 'service_govt', label: 'Government Job' },
                  { value: 'service_private', label: 'Private Job' },
                  { value: 'education', label: 'Education / Teacher' },
                  { value: 'medical', label: 'Medical / Healthcare' },
                  { value: 'engineering', label: 'Engineering' },
                  { value: 'it', label: 'IT / Tech' },
                  { value: 'abroad_job', label: 'Working Abroad' },
                  { value: 'student', label: 'Student' },
                  { value: 'housewife', label: 'Housewife' },
                  { value: 'agriculture', label: 'Agriculture' },
                  { value: 'other', label: 'Other' },
                ]} />
                <Field name="monthly_income" label="Monthly Income (BDT)" type="number" placeholder="e.g. 50000" />
                <Textarea
                  name="profession_details"
                  label="Profession Details (optional)"
                  placeholder="Brief description of your work..."
                  rows={3}
                />
              </>
            )}

            {/* ── Step 5: Family ── */}
            {step === 5 && (
              <>
                <div className="grid grid-cols-2 gap-4">
                  <Field name="father_name" label="Father's Name" placeholder="Abdul Karim" />
                  <Field name="father_profession" label="Father's Profession" placeholder="Retired" />
                </div>
                <Toggle name="father_alive" label="Father is alive" />
                <div className="grid grid-cols-2 gap-4">
                  <Field name="mother_name" label="Mother's Name" placeholder="Fatema Begum" />
                  <Field name="mother_profession" label="Mother's Profession" placeholder="Housewife" />
                </div>
                <Toggle name="mother_alive" label="Mother is alive" />
                <div className="grid grid-cols-2 gap-4">
                  <Field name="brothers" label="No. of Brothers" type="number" placeholder="0" />
                  <Field name="sisters" label="No. of Sisters" type="number" placeholder="0" />
                </div>
                <Sel name="family_type" label="Family Type" options={[
                  { value: 'joint', label: 'Joint Family' },
                  { value: 'nuclear', label: 'Nuclear Family' },
                  { value: 'flexible', label: 'Flexible' },
                ]} />
                <Sel name="family_financial_status" label="Family Financial Status" options={[
                  { value: 'lower', label: 'Lower Class' },
                  { value: 'lower_middle', label: 'Lower Middle Class' },
                  { value: 'middle', label: 'Middle Class' },
                  { value: 'upper_middle', label: 'Upper Middle Class' },
                  { value: 'upper', label: 'Upper Class' },
                ]} />
                <Sel name="home_ownership" label="Home Ownership" options={[
                  { value: 'own_house', label: 'Own House' },
                  { value: 'family_house', label: 'Family House' },
                  { value: 'rented', label: 'Rented' },
                ]} />
                <Textarea
                  name="family_details"
                  label="Family Details (optional)"
                  placeholder="Brief description of your family..."
                  rows={3}
                />
              </>
            )}

            {/* ── Step 6: Lifestyle & Health ── */}
            {step === 6 && (
              <>
                <Sel name="health_status" label="Health Status" options={[
                  { value: 'healthy', label: 'Healthy' },
                  { value: 'minor_condition', label: 'Minor Condition' },
                  { value: 'disability', label: 'Disability' },
                  { value: 'prefer_not_say', label: 'Prefer not to say' },
                ]} />
                <Sel name="diet" label="Diet Preference" options={[
                  { value: 'halal_only', label: 'Halal Only' },
                  { value: 'vegetarian', label: 'Vegetarian' },
                  { value: 'no_restriction', label: 'No Restriction' },
                ]} />
                <Sel name="smoking" label="Smoking" options={[
                  { value: 'never', label: 'Never' },
                  { value: 'occasionally', label: 'Occasionally' },
                  { value: 'regularly', label: 'Regularly' },
                ]} />
                <Textarea
                  name="hobbies"
                  label="Hobbies & Interests"
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
                    <p className="text-xs font-semibold text-slate-500 uppercase tracking-wide">After Marriage</p>
                    <Toggle name="wife_in_veil" label="Expect wife to observe purdah / veil" />
                    <Toggle name="wife_study_allowed" label="Wife can continue studying" />
                    <Toggle name="wife_job_allowed" label="Wife can do job after marriage" />
                  </div>
                )}
                <Toggle name="guardian_agree" label="Guardian / family is aware and approves this profile" />
                <Field name="residence_after_marriage" label="Residence After Marriage" placeholder="Dhaka / Abroad / Flexible" />
                <Field name="post_marriage_plan" label="Post-Marriage Plan" placeholder="Stay in BD / Move abroad / Flexible" />
                <div className="border-t border-slate-200 pt-5">
                  <p className="text-sm font-semibold text-slate-700 mb-3">Guardian Contact</p>
                  <div className="space-y-4">
                    <Field name="guardian_mobile" label="Guardian Mobile" placeholder="+88017XXXXXXXX" />
                    <Field name="guardian_email" label="Guardian Email (optional)" type="email" placeholder="guardian@example.com" />
                  </div>
                </div>
              </>
            )}

            {/* ── Step 8: Partner Preferences ── */}
            {step === 8 && (
              <>
                <div>
                  <p className="text-sm font-medium text-slate-700 mb-2">Preferred Age Range</p>
                  <div className="grid grid-cols-2 gap-4">
                    <Field name="partner_age_min" label="Min Age" type="number" placeholder="22" />
                    <Field name="partner_age_max" label="Max Age" type="number" placeholder="35" />
                  </div>
                </div>
                <div>
                  <p className="text-sm font-medium text-slate-700 mb-2">Preferred Height Range (cm)</p>
                  <div className="grid grid-cols-2 gap-4">
                    <Field name="partner_height_cm_min" label="Min Height" type="number" placeholder="155" />
                    <Field name="partner_height_cm_max" label="Max Height" type="number" placeholder="185" />
                  </div>
                </div>
                <Sel name="partner_marital_status" label="Preferred Marital Status" options={[
                  { value: 'never_married', label: 'Never Married' },
                  { value: 'divorced', label: 'Divorced' },
                  { value: 'widowed', label: 'Widowed' },
                  { value: 'any', label: 'Any' },
                ]} />
                <Sel name="partner_education" label="Minimum Education" options={[
                  { value: 'ssc', label: 'SSC / O-Level' },
                  { value: 'hsc', label: 'HSC / A-Level' },
                  { value: 'graduation', label: "Bachelor's" },
                  { value: 'post_graduation', label: "Master's or above" },
                  { value: 'any', label: 'No preference' },
                ]} />
                <Sel name="partner_division" label="Preferred Division (optional)" options={[
                  { value: 'any', label: 'Any Division' },
                  ...BD_DIVISIONS.map(d => ({ value: d, label: d })),
                ]} />
                <Sel name="partner_family_type" label="Preferred Family Type" options={[
                  { value: 'joint', label: 'Joint Family' },
                  { value: 'nuclear', label: 'Nuclear Family' },
                  { value: 'any', label: 'No preference' },
                ]} />
                <Textarea
                  name="partner_expectations"
                  label="Partner Expectations"
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
                <h3 className="font-bold text-slate-900 mb-2">Upload Your Profile Photo</h3>
                <p className="text-sm text-slate-500 mb-1 max-w-sm mx-auto">
                  Profiles with a photo get <strong>3× more match requests</strong>. Your privacy settings control who can see it.
                </p>
                <p className="text-xs text-amber-600 mb-6">
                  You can upload photos from your Profile page after completing this step.
                </p>
                <p className="text-xs text-slate-400">
                  Click "Submit Biodata" below to finish — then visit your profile to upload photos.
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
                  ← Back
                </Button>
              )}
              <Button
                type="submit"
                className="flex-1"
                size="lg"
                isLoading={processing}
              >
                {step === totalSteps ? 'Submit Biodata ✓' : 'Save & Continue →'}
              </Button>
            </div>
          </form>
        </div>

        <p className="text-center text-xs text-slate-400 mt-4">
          Your biodata will be reviewed before going live. Usually takes 24 hours.
        </p>
      </div>
    </AppLayout>
  )
}
