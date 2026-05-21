/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { cn } from '@/lib/utils'
import { CheckCircle } from 'lucide-react'

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
  // Step 3: Religion
  religion?: string
  sect?: string
  is_practicing?: boolean
  prayers_info?: string
  quran_recitation?: string
  clothing_style?: string
  beard_info?: string
  hijab_info?: string
  wali_approval?: boolean
  sunni_scale?: number | ''
  // Step 4: Education
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
  biodata: BiodataData
  user: { name: string; gender: string; mode: string }
}

const STEP_LABELS = [
  'General', 'Location', 'Religion', 'Education',
  'Family', 'Lifestyle', 'Marriage', 'Partner', 'Photos',
]

export default function BiodataWizard({ step, steps, biodata, user }: Props) {
  const totalSteps = Object.keys(steps).length

  const { data, setData, post, processing, errors } = useForm<BiodataData>({
    ...biodata,
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('biodata.save', { step }))
  }

  const Field = ({ name, label, type = 'text', placeholder = '', required = false }: {
    name: string; label: string; type?: string; placeholder?: string; required?: boolean
  }) => (
    <Input
      label={label}
      type={type}
      value={(data[name as keyof BiodataData] as string | number | undefined) ?? ''}
      onChange={e => setData(name as keyof BiodataData, e.target.value)}
      error={errors[name as keyof BiodataData] as string | undefined}
      placeholder={placeholder}
      required={required}
    />
  )

  const Select = ({ name, label, options }: {
    name: string; label: string
    options: { value: string; label: string }[]
  }) => (
    <div>
      <label className="block text-sm font-medium text-slate-700 mb-1">{label}</label>
      <select
        value={(data[name as keyof BiodataData] as string) ?? ''}
        onChange={e => setData(name as keyof BiodataData, e.target.value)}
        className="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
      >
        <option value="">— Select —</option>
        {options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
      </select>
      {errors[name as keyof BiodataData] && <p className="mt-1 text-xs text-red-600">{errors[name as keyof BiodataData] as string}</p>}
    </div>
  )

  const Toggle = ({ name, label }: { name: string; label: string }) => (
    <label className="flex items-center gap-3 cursor-pointer">
      <div
        className={cn(
          'relative w-11 h-6 rounded-full transition-colors',
          data[name as keyof BiodataData] ? 'bg-primary-600' : 'bg-slate-300',
        )}
        onClick={() => setData(name as keyof BiodataData, !data[name as keyof BiodataData])}
      >
        <div className={cn(
          'absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-transform',
          data[name as keyof BiodataData] ? 'translate-x-6' : 'translate-x-1',
        )} />
      </div>
      <span className="text-sm text-slate-700">{label}</span>
    </label>
  )

  return (
    <AppLayout>
      <Head title={`Biodata — Step ${step} of ${totalSteps}`} />

      <div className="max-w-2xl mx-auto px-4 py-8">
        {/* Step progress */}
        <div className="flex items-center gap-1 mb-8 overflow-x-auto pb-2">
          {STEP_LABELS.map((label, i) => {
            const num = i + 1
            return (
              <div key={num} className="flex items-center gap-1 flex-shrink-0">
                <div className={cn(
                  'h-7 w-7 rounded-full flex items-center justify-center text-xs font-bold transition-all',
                  step > num ? 'bg-emerald-500 text-white' :
                  step === num ? 'bg-primary-600 text-white' :
                  'bg-slate-200 text-slate-400',
                )}>
                  {step > num ? <CheckCircle size={14} /> : num}
                </div>
                <span className={cn(
                  'text-xs font-medium hidden sm:block',
                  step === num ? 'text-slate-900' : 'text-slate-400',
                )}>{label}</span>
                {num < totalSteps && (
                  <div className={cn('w-4 h-0.5 mx-1', step > num ? 'bg-emerald-400' : 'bg-slate-200')} />
                )}
              </div>
            )
          })}
        </div>

        <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
          <h2 className="text-lg font-bold text-slate-900 mb-6">
            Step {step}: {STEP_LABELS[(step - 1)] ?? ''}
          </h2>

          <form onSubmit={submit} className="space-y-5">
            {/* ── Step 1: General ── */}
            {step === 1 && (
              <>
                <Select name="marital_status" label="Marital Status" options={[
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
                <Select name="complexion" label="Complexion" options={[
                  { value: 'very_fair', label: 'Very Fair' },
                  { value: 'fair', label: 'Fair' },
                  { value: 'wheatish', label: 'Wheatish' },
                  { value: 'medium', label: 'Medium' },
                  { value: 'dark', label: 'Dark' },
                ]} />
                <Select name="blood_group" label="Blood Group" options={
                  ['A+','A-','B+','B-','AB+','AB-','O+','O-'].map(b => ({ value: b, label: b }))
                } />
                <Field name="mother_tongue" label="Mother Tongue" placeholder="e.g. Bangla, Sylheti" />
                <Field name="profile_headline" label="Profile Headline" placeholder="e.g. Practicing Muslim, Engineer in Dhaka" />
                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1">About Me</label>
                  <textarea
                    value={(data.about_me as string) ?? ''}
                    onChange={e => setData('about_me', e.target.value)}
                    rows={4}
                    placeholder="Write a brief introduction about yourself..."
                    className="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 resize-none"
                  />
                </div>
              </>
            )}

            {/* ── Step 2: Location ── */}
            {step === 2 && (
              <>
                <Field name="residing_country" label="Currently Residing In" placeholder="Bangladesh" />
                <Field name="residing_city" label="City / District (current)" placeholder="Dhaka" />
                <Field name="division" label="Division (permanent)" placeholder="Dhaka" />
                <Field name="district" label="District (permanent)" placeholder="Dhaka" />
                <Field name="upazila" label="Upazila / Thana" placeholder="Mirpur" />
                <Toggle name="is_nrb" label="Non-Resident Bangladeshi (NRB)" />
                <Select name="visa_status" label="Visa / Residency Status" options={[
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
                <Select name="prayers_info" label="Daily Prayers" options={[
                  { value: '5_times', label: '5 times daily (Alhamdulillah)' },
                  { value: '4_times', label: 'Mostly 5 times' },
                  { value: 'sometimes', label: 'Sometimes' },
                  { value: 'rarely', label: 'Rarely' },
                  { value: 'never', label: 'Not yet' },
                ]} />
                <Select name="quran_recitation" label="Quran Recitation" options={[
                  { value: 'fluent', label: 'Fluent' },
                  { value: 'basic', label: 'Basic' },
                  { value: 'learning', label: 'Learning' },
                  { value: 'no', label: 'No' },
                ]} />
                {user.gender === 'female'
                  ? <Select name="hijab_info" label="Hijab / Niqab" options={[
                      { value: 'wears_niqab', label: 'Wears Niqab' },
                      { value: 'wears_hijab', label: 'Wears Hijab' },
                      { value: 'trying', label: 'Trying to wear' },
                      { value: 'no_hijab', label: 'Does not wear' },
                    ]} />
                  : <Field name="beard_info" label="Beard" placeholder="e.g. Full beard, Trimmed" />
                }
                <Toggle name="is_islamically_educated" label="Islamically Educated (Alim/Hafez/Islamic Course)" />
                {user.mode === 'islamic' && (
                  <>
                    <Toggle name="wali_approval" label="Wali/Guardian Approves This Profile" />
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

            {/* ── Step 4: Education & Profession ── */}
            {step === 4 && (
              <>
                <Select name="education_method" label="Education Method" options={[
                  { value: 'general', label: 'General' },
                  { value: 'islamic', label: 'Islamic (Madrasa)' },
                  { value: 'both', label: 'Both' },
                ]} />
                <Select name="highest_qualification" label="Highest Qualification" options={[
                  { value: 'below_ssc', label: 'Below SSC' },
                  { value: 'ssc', label: 'SSC / O-Level' },
                  { value: 'hsc', label: 'HSC / A-Level' },
                  { value: 'diploma', label: 'Diploma' },
                  { value: 'graduation', label: 'Graduation / Bachelor\'s' },
                  { value: 'post_graduation', label: 'Post Graduation / Master\'s' },
                  { value: 'phd', label: 'PhD' },
                  { value: 'hafez', label: 'Hafez' },
                  { value: 'alim', label: 'Alim' },
                  { value: 'fazil', label: 'Fazil' },
                  { value: 'kamil', label: 'Kamil' },
                ]} />
                <Field name="occupation" label="Current Occupation" placeholder="e.g. Software Engineer" />
                <Select name="occupation_category" label="Occupation Category" options={[
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
                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1">Profession Details</label>
                  <textarea
                    value={(data.profession_details as string) ?? ''}
                    onChange={e => setData('profession_details', e.target.value)}
                    rows={3}
                    placeholder="Brief description of your work..."
                    className="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 resize-none"
                  />
                </div>
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
                <Select name="family_type" label="Family Type" options={[
                  { value: 'joint', label: 'Joint Family' },
                  { value: 'nuclear', label: 'Nuclear Family' },
                  { value: 'flexible', label: 'Flexible' },
                ]} />
                <Select name="family_financial_status" label="Family Financial Status" options={[
                  { value: 'lower', label: 'Lower Class' },
                  { value: 'lower_middle', label: 'Lower Middle Class' },
                  { value: 'middle', label: 'Middle Class' },
                  { value: 'upper_middle', label: 'Upper Middle Class' },
                  { value: 'upper', label: 'Upper Class' },
                ]} />
                <Select name="home_ownership" label="Home Ownership" options={[
                  { value: 'own_house', label: 'Own House' },
                  { value: 'family_house', label: 'Family House' },
                  { value: 'rented', label: 'Rented' },
                ]} />
                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1">Family Details (optional)</label>
                  <textarea
                    value={(data.family_details as string) ?? ''}
                    onChange={e => setData('family_details', e.target.value)}
                    rows={3}
                    placeholder="Brief description of your family..."
                    className="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 resize-none"
                  />
                </div>
              </>
            )}

            {/* ── Step 6: Lifestyle & Health ── */}
            {step === 6 && (
              <>
                <Select name="health_status" label="Health Status" options={[
                  { value: 'healthy', label: 'Healthy' },
                  { value: 'minor_condition', label: 'Minor Condition' },
                  { value: 'disability', label: 'Disability' },
                  { value: 'prefer_not_say', label: 'Prefer not to say' },
                ]} />
                <Select name="diet" label="Diet Preference" options={[
                  { value: 'halal_only', label: 'Halal Only' },
                  { value: 'vegetarian', label: 'Vegetarian' },
                  { value: 'no_restriction', label: 'No Restriction' },
                ]} />
                <Select name="smoking" label="Smoking" options={[
                  { value: 'never', label: 'Never' },
                  { value: 'occasionally', label: 'Occasionally' },
                  { value: 'regularly', label: 'Regularly' },
                ]} />
                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1">Hobbies & Interests</label>
                  <textarea
                    value={(data.hobbies as string) ?? ''}
                    onChange={e => setData('hobbies', e.target.value)}
                    rows={3}
                    placeholder="Reading, cooking, traveling..."
                    className="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 resize-none"
                  />
                </div>
              </>
            )}

            {/* ── Step 7: Marriage & Guardian ── */}
            {step === 7 && (
              <>
                {user.gender === 'male' && (
                  <>
                    <Toggle name="wife_in_veil" label="Expect wife to observe purdah/veil" />
                    <Toggle name="wife_study_allowed" label="Wife can continue studying" />
                    <Toggle name="wife_job_allowed" label="Wife can do job after marriage" />
                  </>
                )}
                <Toggle name="guardian_agree" label="Guardian / family is aware and approves" />
                <Field name="residence_after_marriage" label="Residence After Marriage" placeholder="Dhaka / Abroad / Flexible" />
                <Field name="post_marriage_plan" label="Post-Marriage Plan" placeholder="Stay in BD / Move abroad / Flexible" />
                <div className="border-t border-slate-200 pt-5">
                  <p className="text-sm font-semibold text-slate-700 mb-3">Guardian Contact (for Islamic Mode)</p>
                  <Field name="guardian_mobile" label="Guardian Mobile" placeholder="+88017XXXXXXXX" />
                  <div className="mt-4">
                    <Field name="guardian_email" label="Guardian Email (optional)" type="email" placeholder="guardian@example.com" />
                  </div>
                </div>
              </>
            )}

            {/* ── Step 8: Partner Preferences ── */}
            {step === 8 && (
              <>
                <div className="grid grid-cols-2 gap-4">
                  <Field name="partner_age_min" label="Age Min" type="number" placeholder="22" />
                  <Field name="partner_age_max" label="Age Max" type="number" placeholder="30" />
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <Field name="partner_height_cm_min" label="Height Min (cm)" type="number" placeholder="155" />
                  <Field name="partner_height_cm_max" label="Height Max (cm)" type="number" placeholder="180" />
                </div>
                <Select name="partner_marital_status" label="Preferred Marital Status" options={[
                  { value: 'never_married', label: 'Never Married' },
                  { value: 'divorced', label: 'Divorced' },
                  { value: 'widowed', label: 'Widowed' },
                  { value: 'any', label: 'Any' },
                ]} />
                <Select name="partner_education" label="Minimum Education" options={[
                  { value: 'ssc', label: 'SSC / O-Level' },
                  { value: 'hsc', label: 'HSC / A-Level' },
                  { value: 'graduation', label: "Bachelor's" },
                  { value: 'post_graduation', label: "Master's or above" },
                  { value: 'any', label: 'No preference' },
                ]} />
                <Field name="partner_division" label="Preferred Division" placeholder="Any / Dhaka / Chittagong" />
                <Select name="partner_family_type" label="Preferred Family Type" options={[
                  { value: 'joint', label: 'Joint' },
                  { value: 'nuclear', label: 'Nuclear' },
                  { value: 'any', label: 'No preference' },
                ]} />
                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1">Partner Expectations</label>
                  <textarea
                    value={(data.partner_expectations as string) ?? ''}
                    onChange={e => setData('partner_expectations', e.target.value)}
                    rows={4}
                    placeholder="Describe qualities you're looking for..."
                    className="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 resize-none"
                  />
                </div>
              </>
            )}

            {/* ── Step 9: Photos ── */}
            {step === 9 && (
              <div className="text-center py-8">
                <div className="mx-auto w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-3xl mb-4">
                  📷
                </div>
                <h3 className="font-bold text-slate-900 mb-2">Upload Your Photo</h3>
                <p className="text-sm text-slate-500 mb-6">
                  A photo increases your profile visibility by 3×. Your privacy settings control who can see it.
                </p>
                <label className="cursor-pointer inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-primary-300 px-8 py-6 text-primary-600 hover:bg-primary-50 transition-colors">
                  <span className="text-sm font-medium">Choose photo (JPG, PNG — max 5 MB)</span>
                  <input type="file" accept="image/jpeg,image/png" className="hidden"
                    onChange={e => {
                      const file = e.target.files?.[0]
                      if (file) setData('photo_file' as unknown as keyof BiodataData, file as unknown as never)
                    }}
                  />
                </label>
                <p className="mt-4 text-xs text-slate-400">
                  You can skip this step and add photos later from your profile settings.
                </p>
              </div>
            )}

            {/* Navigation */}
            <div className="flex gap-3 pt-2">
              {step > 1 && (
                <Button
                  type="button"
                  variant="outline"
                  className="flex-1"
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
