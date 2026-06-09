/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import { useState, useRef } from 'react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { SearchableSelect } from '@/components/ui/SearchableSelect'
import WritingAssistant from '@/components/ui/WritingAssistant'
import { HeightSelect } from '@/components/ui/HeightSelect'
import { WeightSelect } from '@/components/ui/WeightSelect'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import {
  CheckCircle, Save, Plus, Trash2, Camera, Upload, X, Star,
  User, MapPin, Moon, GraduationCap, HeartPulse, Users, HeartHandshake,
  Search, Phone, ClipboardCheck, AlertTriangle,
} from 'lucide-react'
import { BangladeshAddressPicker } from '@/components/forms/BangladeshAddressPicker'
import PhoneNumberInput from '@/components/forms/PhoneNumberInput'
import {
  levelsForSystem, recordLevelsFor, isRecordLevelValid,
  isHighestValidForSystem, nextDefaultLevel, rankOf, levelLabelKey, isEduSystem,
} from '@/lib/education'

// SSC / O-Level / Dakhil / Sanawiyah / SSC-Vocational all sit at this rank.
// Anything below it ("low qualification") needs no detailed education records.
const SSC_EQUIVALENT_RANK = 4

// ─── Sub-types ────────────────────────────────────────────────────────────────

interface EducationRecord {
  level?: string
  edu_type?: string
  subject?: string
  institute?: string
  board_university?: string
  passing_year?: string
  result_type?: string
  result_value?: string
  is_current?: boolean
  note?: string
}

interface SiblingDetail {
  position?: string
  marital_status?: string
  education?: string
  profession?: string
  location?: string
  note?: string
}

// ─── Types ────────────────────────────────────────────────────────────────────

interface BiodataData {
  marital_status?: string
  marital_substatus?: string
  birth_date?: string
  // Transient DOB parts (compose birth_date on the client; not DB columns).
  birth_day?: string
  birth_month?: string
  birth_year?: string
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
  village_area?: string
  permanent_address?: string
  grew_up_in?: string
  same_as_permanent?: boolean
  current_division?: string
  current_district?: string
  current_upazila?: string
  current_area?: string
  present_address?: string
  residing_country?: string
  permanent_country?: string
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
  sunni_scale?: number | ''
  education_method?: string
  education_medium?: string
  highest_qualification?: string
  education_details?: EducationRecord[]
  occupation?: string
  occupation_category?: string
  profession_details?: string
  monthly_income?: number | ''
  income_type?: string
  income_privacy?: string
  workplace_type?: string
  future_career_plan?: string
  profession_halal_status?: string
  father_name?: string
  father_alive?: boolean
  father_profession?: string
  mother_name?: string
  mother_alive?: boolean
  mother_profession?: string
  uncle_profession?: string
  brothers?: number | ''
  sisters?: number | ''
  brothers_details?: SiblingDetail[]
  sisters_details?: SiblingDetail[]
  family_type?: string
  family_financial_status?: string
  home_ownership?: string
  family_assets_details?: string
  family_details?: string
  health_status?: string
  health_details?: string
  diet?: string
  smoking?: string
  hobbies?: string
  guardian_agree?: boolean
  why_getting_married?: string
  marriage_thoughts?: string
  marriage_timeline?: string
  wife_in_veil?: boolean
  wife_study_allowed?: boolean
  wife_job_allowed?: boolean
  expect_gift_from_bride?: string
  gift_expectation_details?: string
  polygamy_open?: boolean
  wants_to_work?: boolean
  continue_study?: boolean
  continue_job?: boolean
  preferred_living?: string
  has_children?: boolean
  children_count?: number | ''
  children_live_with?: string
  children_notes?: string
  previous_marriage_date?: string
  divorce_date?: string
  divorce_reason?: string
  spouse_death_date?: string
  spouse_death_reason?: string
  child_acceptance_expectation?: string
  reason_for_second_marriage?: string
  current_wife_count?: number | ''
  current_family_consent?: boolean
  first_wife_knows?: boolean
  second_marriage_living?: string
  residence_after_marriage?: string
  post_marriage_plan?: string
  contact_person_name?: string
  guardian_name?: string
  guardian_mobile?: string
  guardian_relationship?: string
  guardian_email?: string
  guardian_whatsapp?: string
  whatsapp_number?: string
  contact_privacy?: string
  biodata_visibility?: string
  allow_shortlist?: boolean
  allow_contact_request?: boolean
  guardian_knows_biodata?: boolean
  info_truthful_confirmed?: boolean
  accept_liability_terms?: boolean
  confirm_correct?: boolean
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
  partner_districts?: string[]
  partner_family_type?: string
  partner_economic_status?: string
  partner_deen_practice?: string
  partner_special_qualities?: string
  partner_deal_breakers?: string
  partner_expectations?: string
  // Admin-defined custom fields (Phase E3) — stored as a JSON bag.
  custom_fields?: Record<string, string | number | boolean | string[] | null>
}

// Admin-defined custom field definition (from BiodataFieldService).
interface CustomFieldDef {
  key: string
  label: string
  placeholder: string | null
  helper: string | null
  input_type: string
  options: { value: string; label: string }[]
  default: string | null
  required: boolean
  step: number
}

interface PhotoItem {
  path: string
  is_primary: boolean
  uploaded_at: string
}

// Admin field-control overlay for built-in fields (keyed by model column).
interface FieldControl {
  active: boolean
  required: boolean
  visible: boolean
  label?: string | null
  placeholder?: string | null
  helper?: string | null
}

interface Props {
  step: number
  steps: Record<number, string>
  biodata: BiodataData & { completeness_score?: number }
  user: { name: string; gender: string; mode: string }
  customFields?: CustomFieldDef[]
  fieldControl?: Record<string, FieldControl>
  photos?: PhotoItem[]
  photoUrls?: string[]
  maxPhotos?: number
}

// ─── Module-level helpers to normalise server data ────────────────────────────

function normaliseEdu(raw: unknown): EducationRecord {
  const o = (raw as Record<string, unknown>) ?? {}
  return {
    level: (o.level as string) ?? '',
    edu_type: (o.edu_type as string) ?? '',
    subject: (o.subject as string) ?? '',
    institute: (o.institute as string) ?? '',
    board_university: (o.board_university as string) ?? '',
    passing_year: (o.passing_year as string) ?? '',
    result_type: (o.result_type as string) ?? '',
    result_value: (o.result_value as string) ?? '',
    is_current: Boolean(o.is_current),
    note: (o.note as string) ?? '',
  }
}

function normaliseSibling(raw: unknown): SiblingDetail {
  const o = (raw as Record<string, unknown>) ?? {}
  return {
    position: (o.position as string) ?? '',
    marital_status: (o.marital_status as string) ?? '',
    education: (o.education as string) ?? '',
    profession: (o.profession as string) ?? '',
    location: (o.location as string) ?? '',
    note: (o.note as string) ?? '',
  }
}

function toEduList(raw: unknown): EducationRecord[] {
  return Array.isArray(raw) ? raw.map(normaliseEdu) : []
}

function toSiblingList(raw: unknown): SiblingDetail[] {
  return Array.isArray(raw) ? raw.map(normaliseSibling) : []
}

// ─── Module-level stable sub-components ──────────────────────────────────────

function WizardToggle({ value, label, onChange }: {
  value: boolean | undefined; label: string; onChange: (v: boolean) => void
}) {
  return (
    <label className="flex items-center gap-3 cursor-pointer select-none group">
      <div
        className={cn('relative w-11 h-6 rounded-full transition-colors shrink-0',
          value ? 'bg-primary-600' : 'bg-slate-200')}
        onClick={() => onChange(!value)}
      >
        <div className={cn('absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-transform duration-200',
          value ? 'translate-x-6' : 'translate-x-1')} />
      </div>
      <span className="text-sm text-slate-700 group-hover:text-slate-900 transition-colors">{label}</span>
    </label>
  )
}

function WizardTextarea({ value, label, placeholder = '', rows = 4, maxLength, onChange, error, assist, required }: {
  value: string; label: string; placeholder?: string
  rows?: number; maxLength?: number; onChange: (v: string) => void; error?: string
  required?: boolean
  /** When set, shows a small AI writing-assistant button for this long-text field. */
  assist?: { field: string; mode?: string | null; gender?: string | null }
}) {
  const { t } = useTranslation()
  return (
    <div>
      <div className="flex items-center justify-between gap-2 mb-1.5">
        <label className="block text-sm font-medium text-slate-700">
          {label}{required && <span className="ml-0.5 text-red-500">*</span>}
        </label>
        <div className="flex items-center gap-2 shrink-0">
          {maxLength && (
            <span className={cn('text-xs tabular-nums', value.length > maxLength * 0.9 ? 'text-amber-600' : 'text-slate-400')}>
              {value.length}/{maxLength}
            </span>
          )}
          {assist && (
            <WritingAssistant
              field={assist.field}
              value={value}
              mode={assist.mode}
              gender={assist.gender}
              onApply={onChange}
            />
          )}
        </div>
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
      {assist && !error && (
        value.trim().length > 0 ? (
          // Has content → offer to improve the user's own text.
          <div className="mt-1.5 flex flex-wrap items-center justify-between gap-2">
            <p className="text-xs text-slate-400">{t('common', 'ai_rewrite_hint')}</p>
            <WritingAssistant
              variant="rewrite"
              field={assist.field}
              value={value}
              mode={assist.mode}
              gender={assist.gender}
              maxLength={maxLength}
              onApply={onChange}
            />
          </div>
        ) : (
          // Empty → gentle nudge toward the suggestion button beside the label.
          <p className="mt-1 text-xs text-slate-400">{t('common', 'ai_help_hint')}</p>
        )
      )}
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

// ─── Education record card (module-level = stable identity) ──────────────────

const RESULT_TYPE_OPTS = [
  { value: 'GPA', label: 'GPA' },
  { value: 'CGPA', label: 'CGPA' },
  { value: 'Division', label: 'Division' },
  { value: 'Class', label: 'Class' },
  { value: 'Pass', label: 'Pass' },
  { value: 'Other', label: 'Other' },
]

function EducationRecordCard({ record, index, levelOptions, levelLabel, allowFreeLevel, invalid, invalidMsg, onChange, onRemove }: {
  record: EducationRecord
  index: number
  levelOptions: { value: string; label: string }[]
  levelLabel: string
  allowFreeLevel: boolean
  invalid: boolean
  invalidMsg: string
  onChange: (r: EducationRecord) => void
  onRemove: () => void
}) {
  const upd = (k: keyof EducationRecord, v: string | boolean) => onChange({ ...record, [k]: v })
  // Display the localised label for a known level value, else the raw stored text.
  const headerLevel = levelOptions.find(o => o.value === record.level)?.label || record.level
  return (
    <div className={cn(
      'rounded-xl border bg-white p-4 space-y-3 shadow-sm',
      invalid ? 'border-red-300 ring-1 ring-red-200' : 'border-slate-200',
    )}>
      <div className="flex items-center justify-between">
        <p className="text-xs font-semibold text-primary-600 uppercase tracking-wide">
          {index + 1}. {headerLevel || 'New Education Record'}
        </p>
        <button type="button" onClick={onRemove}
          className="flex items-center gap-1 text-xs text-red-500 hover:text-red-700 font-medium transition-colors">
          <Trash2 size={12} /> Remove
        </button>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <SearchableSelect label={levelLabel} value={record.level ?? ''}
          onChange={v => upd('level', v)} options={levelOptions}
          allowFreeText={allowFreeLevel}
          error={invalid ? invalidMsg : undefined}
          emptyText={allowFreeLevel ? undefined : 'No levels available'}
          placeholder="— Select —" />
        <Input label="Subject / Group / Dept" value={record.subject ?? ''}
          onChange={e => upd('subject', e.target.value)} placeholder="e.g. Science, CSE, Arabic" />
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <Input label="Institute Name" value={record.institute ?? ''}
          onChange={e => upd('institute', e.target.value)} placeholder="e.g. Dhaka University" />
        <Input label="Board / University" value={record.board_university ?? ''}
          onChange={e => upd('board_university', e.target.value)} placeholder="e.g. Dhaka Board" />
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <Input label="Passing Year" value={record.passing_year ?? ''}
          onChange={e => upd('passing_year', e.target.value)} placeholder="e.g. 2018" />
        <div className="flex items-end pb-1">
          <WizardToggle value={!!record.is_current} label="Currently studying"
            onChange={v => upd('is_current', v)} />
        </div>
      </div>

      <div className="grid grid-cols-2 gap-3">
        <SearchableSelect label="Result Type" value={record.result_type ?? ''}
          onChange={v => upd('result_type', v)} options={RESULT_TYPE_OPTS} placeholder="GPA / Division" />
        <Input label="Result" value={record.result_value ?? ''}
          onChange={e => upd('result_value', e.target.value)} placeholder="e.g. 5.00, First Division" />
      </div>

      <Input label="Note (optional)" value={record.note ?? ''}
        onChange={e => upd('note', e.target.value)} placeholder="Any additional info..." />
    </div>
  )
}

// ─── Sibling detail card (module-level) ──────────────────────────────────────

const POSITION_OPTS = [
  { value: 'elder', label: 'Elder' },
  { value: 'younger', label: 'Younger' },
]
const SIBLING_MARITAL_OPTS = [
  { value: 'married', label: 'Married' },
  { value: 'unmarried', label: 'Unmarried' },
  { value: 'divorced', label: 'Divorced' },
  { value: 'widowed', label: 'Widowed' },
]

function SiblingDetailCard({ sibling, index, genderLabel, onChange, onRemove }: {
  sibling: SiblingDetail
  index: number
  genderLabel: string
  onChange: (s: SiblingDetail) => void
  onRemove: () => void
}) {
  const upd = (k: keyof SiblingDetail, v: string) => onChange({ ...sibling, [k]: v })
  return (
    <div className="rounded-xl border border-slate-200 bg-white p-4 space-y-3 shadow-sm">
      <div className="flex items-center justify-between">
        <p className="text-xs font-semibold text-slate-500 uppercase tracking-wide">
          {genderLabel} #{index + 1}
        </p>
        <button type="button" onClick={onRemove}
          className="flex items-center gap-1 text-xs text-red-500 hover:text-red-700 font-medium transition-colors">
          <Trash2 size={12} /> Remove
        </button>
      </div>

      <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <SearchableSelect label="Position" value={sibling.position ?? ''}
          onChange={v => upd('position', v)} options={POSITION_OPTS} placeholder="Elder / Younger" />
        <SearchableSelect label="Marital Status" value={sibling.marital_status ?? ''}
          onChange={v => upd('marital_status', v)} options={SIBLING_MARITAL_OPTS} placeholder="Select..." />
        <Input label="Education" value={sibling.education ?? ''}
          onChange={e => upd('education', e.target.value)} placeholder="e.g. Graduation" />
      </div>

      <div className="grid grid-cols-2 gap-3">
        <Input label="Profession" value={sibling.profession ?? ''}
          onChange={e => upd('profession', e.target.value)} placeholder="e.g. Engineer" />
        <Input label="Location" value={sibling.location ?? ''}
          onChange={e => upd('location', e.target.value)} placeholder="e.g. Dhaka, UK" />
      </div>

      <Input label="Note (optional)" value={sibling.note ?? ''}
        onChange={e => upd('note', e.target.value)} placeholder="Any short note..." />
    </div>
  )
}

// ─── Static option lists ──────────────────────────────────────────────────────

const RELIGION_OPTIONS = [
  { value: 'Islam', label: 'Islam' },
  { value: 'Hinduism', label: 'Hinduism' },
  { value: 'Christianity', label: 'Christianity' },
  { value: 'Buddhism', label: 'Buddhism' },
  { value: 'Other', label: 'Other' },
]

const BD_CITY_OPTIONS = [
  'Dhaka', 'Chittagong (Chattogram)', 'Sylhet', 'Rajshahi', 'Khulna',
  'Barisal (Barishal)', 'Rangpur', 'Mymensingh', 'Narayanganj', 'Gazipur',
  'Cumilla (Comilla)', "Cox's Bazar", 'Bogura', 'Narsingdi', 'Tangail',
  'Bagerhat', 'Bandarban', 'Barguna', 'Bhola', 'Brahmanbaria', 'Chandpur',
  'Chapainawabganj', 'Chuadanga', 'Dinajpur', 'Faridpur', 'Feni',
  'Gaibandha', 'Gopalganj', 'Habiganj', 'Jamalpur', 'Jashore (Jessore)',
  'Jhalakathi', 'Jhenaidah', 'Joypurhat', 'Khagrachhari', 'Kishoreganj',
  'Kurigram', 'Kushtia', 'Lakshmipur', 'Lalmonirhat', 'Madaripur',
  'Magura', 'Manikganj', 'Meherpur', 'Moulvibazar', 'Munshiganj',
  'Naogaon', 'Narail', 'Natore', 'Netrokona', 'Nilphamari',
  'Noakhali', 'Pabna', 'Panchagarh', 'Patuakhali', 'Pirojpur',
  'Rajbari', 'Rangamati', 'Satkhira', 'Sherpur', 'Sirajganj',
  'Sunamganj', 'Thakurgaon',
].map(c => ({ value: c, label: c }))

const COUNTRY_OPTIONS = [
  'Bangladesh', 'United Kingdom', 'United States', 'Canada', 'Australia',
  'Saudi Arabia', 'United Arab Emirates', 'Qatar', 'Kuwait', 'Bahrain',
  'Oman', 'Malaysia', 'Singapore', 'Italy', 'France', 'Germany',
  'Sweden', 'Norway', 'Denmark', 'Netherlands', 'Japan', 'South Korea', 'New Zealand',
].map(c => ({ value: c, label: c }))

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
  { value: 'Other', label: 'Other' },
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
  { value: 'Housewife / Homemaker', label: 'Housewife / Homemaker' },
  { value: 'Islamic Scholar / Imam', label: 'Islamic Scholar / Imam' },
  { value: 'Nurse', label: 'Nurse' },
  { value: 'Accountant', label: 'Accountant' },
  { value: 'Lawyer', label: 'Lawyer' },
  { value: 'Banker', label: 'Banker' },
  { value: 'Journalist', label: 'Journalist' },
  { value: 'Architect', label: 'Architect' },
  { value: 'Pharmacist', label: 'Pharmacist' },
]

const PROFESSION_OPTIONS = [
  { value: 'Retired', label: 'Retired' },
  { value: 'Deceased', label: 'Deceased' },
  { value: 'Farmer', label: 'Farmer' },
  { value: 'Business', label: 'Business' },
  { value: 'Government Service', label: 'Government Service' },
  { value: 'Private Service', label: 'Private Service' },
  { value: 'Teacher', label: 'Teacher' },
  { value: 'Homemaker / Housewife', label: 'Homemaker / Housewife' },
  { value: 'Doctor', label: 'Doctor' },
  { value: 'Engineer', label: 'Engineer' },
  { value: 'Islamic Scholar', label: 'Islamic Scholar' },
  { value: 'Army / Police', label: 'Army / Police' },
]

const COUNT_OPTIONS = Array.from({ length: 21 }, (_, i) => ({ value: String(i), label: String(i) }))

// Step number → related icon for the progress indicator.
const STEP_ICONS: Record<number, any> = {
  1: User, 2: MapPin, 3: Moon, 4: GraduationCap, 5: HeartPulse,
  6: Users, 7: HeartHandshake, 8: Search, 9: Phone, 10: ClipboardCheck,
}

const isBdCountry = (c?: string) => !c || c.trim().toLowerCase() === 'bangladesh'

/**
 * Present / Permanent address card. Country first; Bangladesh shows the
 * division→district→upazila cascade + area, otherwise state/city/area inputs.
 * Maps to existing biodata columns supplied by the parent (no new column except
 * permanent_country). Dropdowns are portalled, so the card never clips them.
 */
function AddressBlock({
  title, helper, country, onCountry, bd, onBd, area, onArea, areaLabel, areaPh,
  city, onCity, state, onState, disabled = false, errors = {},
}: {
  title: string
  helper: string
  country: string
  onCountry: (v: string) => void
  bd: { division?: string; district?: string; upazila?: string }
  onBd: (v: { division?: string; district?: string; upazila?: string }) => void
  area: string
  onArea: (v: string) => void
  areaLabel: string
  areaPh: string
  city: string
  onCity: (v: string) => void
  state: string
  onState: (v: string) => void
  disabled?: boolean
  errors?: { country?: string; division?: string; district?: string; city?: string }
}) {
  const { t } = useTranslation()
  const isBd = isBdCountry(country)
  return (
    <div className={cn('rounded-2xl border border-slate-200 p-4 sm:p-5 space-y-4', disabled && 'opacity-60')}>
      <div>
        <h3 className="text-sm font-bold text-slate-800">{title}</h3>
        <p className="text-xs text-slate-400 mt-0.5">{helper}</p>
      </div>

      <SearchableSelect
        label={t('biodata', 'addr_country')}
        value={country}
        onChange={onCountry}
        options={COUNTRY_OPTIONS}
        error={errors.country}
        allowFreeText
        required
        disabled={disabled}
        placeholder={t('biodata', 'addr_country_ph')}
      />

      {isBd ? (
        <>
          <BangladeshAddressPicker
            value={{ division: bd.division || undefined, district: bd.district || undefined, upazila: bd.upazila || undefined }}
            onChange={onBd}
            errors={{ division: errors.division, district: errors.district }}
          />
          <Input
            label={areaLabel}
            value={area}
            onChange={e => onArea(e.target.value)}
            placeholder={areaPh}
            disabled={disabled}
          />
        </>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <Input
            label={t('biodata', 'addr_state')}
            value={state}
            onChange={e => onState(e.target.value)}
            placeholder={t('biodata', 'addr_state_ph')}
            disabled={disabled}
          />
          <Input
            label={t('biodata', 'addr_city')}
            value={city}
            onChange={e => onCity(e.target.value)}
            placeholder={t('biodata', 'addr_city_ph')}
            error={errors.city}
            required
            disabled={disabled}
          />
          <div className="sm:col-span-2">
            <Input
              label={areaLabel}
              value={area}
              onChange={e => onArea(e.target.value)}
              placeholder={areaPh}
              disabled={disabled}
            />
          </div>
        </div>
      )}
    </div>
  )
}

// ─── Main Component ───────────────────────────────────────────────────────────

export default function BiodataWizard({ step, steps, biodata, user, customFields = [], fieldControl = {}, photos = [], photoUrls = [], maxPhotos = 6 }: Props) {
  const totalSteps = Object.keys(steps).length
  const { t } = useTranslation()
  const completenessScore = biodata.completeness_score ?? 0

  // ── Admin field-control overlay (built-in fields) ────────────────────────────
  // Safe fallbacks: a field absent from the map keeps its hardcoded behaviour.
  const fcVisible  = (col: string) => fieldControl[col]?.visible !== false
  const fcRequired = (col: string) => fieldControl[col]?.required === true
  const fcLabel    = (col: string, fallback: string) => fieldControl[col]?.label || fallback
  // Label with an "(Optional)" suffix unless the admin marked it required.
  const fcFieldLabel = (col: string, fallback: string) =>
    fcRequired(col) ? fcLabel(col, fallback) : `${fcLabel(col, fallback)} (${t('common', 'optional')})`

  // ── Photo upload state (Step 9) ──────────────────────────────────────────────
  const photoInputRef = useRef<HTMLInputElement>(null)
  const [photoFile, setPhotoFile] = useState<File | null>(null)
  const [photoPreview, setPhotoPreview] = useState<string | null>(null)
  const [photoError, setPhotoError] = useState<string | null>(null)
  const [photoUploading, setPhotoUploading] = useState(false)
  const [deletingPhotoIdx, setDeletingPhotoIdx] = useState<number | null>(null)

  function handlePhotoSelect(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0]
    if (!file) return
    setPhotoError(null)
    if (file.size > 4 * 1024 * 1024) {
      setPhotoError(t('biodata', 'photo_too_large'))
      if (photoInputRef.current) photoInputRef.current.value = ''
      return
    }
    setPhotoFile(file)
    setPhotoPreview(URL.createObjectURL(file))
  }

  function clearPhotoPreview() {
    if (photoPreview) URL.revokeObjectURL(photoPreview)
    setPhotoPreview(null)
    setPhotoFile(null)
    setPhotoError(null)
    if (photoInputRef.current) photoInputRef.current.value = ''
  }

  function handlePhotoUpload() {
    if (!photoFile) return
    const fd = new FormData()
    fd.append('photo', photoFile)
    setPhotoUploading(true)
    router.post(route('profile.photos.store'), fd as never, {
      forceFormData: true,
      onFinish: () => {
        setPhotoUploading(false)
        clearPhotoPreview()
      },
    })
  }

  function handlePhotoDelete(index: number) {
    if (!confirm(t('biodata', 'photo_delete_confirm'))) return
    setDeletingPhotoIdx(index)
    router.delete(route('profile.photos.destroy', index), {
      onFinish: () => setDeletingPhotoIdx(null),
    })
  }

  function handleSetPrimary(index: number) {
    router.put(route('profile.photos.primary', index), {}, { preserveScroll: true })
  }

  const { data, setData, post, processing, errors } = useForm<BiodataData>({
    ...biodata,
    education_details: toEduList(biodata.education_details),
    brothers_details: toSiblingList(biodata.brothers_details),
    sisters_details: toSiblingList(biodata.sisters_details),
    partner_districts: Array.isArray(biodata.partner_districts) ? biodata.partner_districts : [],
    custom_fields: (biodata.custom_fields && typeof biodata.custom_fields === 'object' ? biodata.custom_fields : {}) as Record<string, string | number | boolean | string[] | null>,
    // Split the stored birth_date into editable day/month/year parts.
    birth_day:   biodata.birth_date ? String(Number(String(biodata.birth_date).slice(8, 10))) : '',
    birth_month: biodata.birth_date ? String(Number(String(biodata.birth_date).slice(5, 7))) : '',
    birth_year:  biodata.birth_date ? String(biodata.birth_date).slice(0, 4) : '',
    contact_privacy: biodata.contact_privacy ?? 'private',
    // Privacy-first default: income hidden from the public profile until changed.
    income_privacy: biodata.income_privacy ?? 'private',
    allow_shortlist: biodata.allow_shortlist ?? true,
    allow_contact_request: biodata.allow_contact_request ?? true,
    // Declaration checkboxes always start unchecked — must be re-affirmed on submit.
    guardian_knows_biodata: false,
    info_truthful_confirmed: false,
    accept_liability_terms: false,
    confirm_correct: false,
  })

  const contactPrivacyOpts = [
    { value: 'private',       label: t('biodata', 'contact_privacy_private') },
    { value: 'request_only',  label: t('biodata', 'contact_privacy_request') },
    { value: 'matches_only',  label: t('biodata', 'contact_privacy_matches') },
  ]

  const [savingDraft, setSavingDraft] = useState(false)

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    // Block step 1 on an impossible birth date (e.g. Feb 30).
    if (step === 1 && dobInvalid) {
      return
    }
    // Block step 4 if any education record ranks above the highest qualification.
    if (step === 4 && eduInvalidIndexes.length > 0) {
      setEduNotice(t('biodata', 'edu_fix_records_notice'))
      return
    }
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

  // ── Custom field helpers (Phase E3) ──────────────────────────────────────────
  const customFieldsForStep = customFields.filter(f => f.step === step)
  const cfValue = (key: string): unknown => (data.custom_fields ?? {})[key]
  const setCustomField = (key: string, value: unknown) =>
    setData('custom_fields', { ...(data.custom_fields ?? {}), [key]: value } as never)

  // ── Education record helpers (system-driven, conditional) ────────────────────
  const eduRecords: EducationRecord[] = toEduList(data.education_details)
  const eduSystem = data.education_medium ?? ''        // education_medium IS the system
  const eduHighest = data.highest_qualification ?? ''
  const [eduNotice, setEduNotice] = useState<string | null>(null)

  // education_method (legacy general/islamic/both) is derived from the system so
  // existing search/profile code keeps working without a confusing extra field.
  const deriveEduMethod = (system: string): string =>
    system === 'qawmi' || system === 'alia' ? 'islamic'
      : system === 'general' || system === 'english_medium' || system === 'vocational' ? 'general'
      : (data.education_method ?? '')

  // Localised option lists for the current system.
  const highestOpts = levelsForSystem(eduSystem).map(l => ({ value: l.value, label: t('biodata', l.labelKey) }))
  // Detailed records are only kept for academic MILESTONES — SSC-equivalent and
  // above, capped at the highest qualification. Sub-SSC levels (Class 5/8/JSC) are
  // "low qualifications" and never appear as record options.
  const milestoneLevels = recordLevelsFor(eduSystem, eduHighest).filter(l => l.rank >= SSC_EQUIVALENT_RANK)
  const recordLevelOpts = (currentValue: string) => {
    const used = eduRecords.map(r => r.level ?? '').filter(v => v && v !== currentValue)
    return milestoneLevels
      .filter(l => !used.includes(l.value))
      .map(l => ({ value: l.value, label: t('biodata', l.labelKey) }))
  }
  const allLevelsAdded =
    eduSystem !== '' && eduSystem !== 'other' &&
    milestoneLevels.length > 0 &&
    milestoneLevels.every(l => eduRecords.some(r => r.level === l.value))

  // Records whose known level ranks above the chosen highest qualification.
  const eduInvalidIndexes = eduRecords
    .map((r, i) => (!isRecordLevelValid(eduSystem, eduHighest, r.level ?? '') ? i : -1))
    .filter(i => i >= 0)

  // ── Conditional gating (system → highest qualification → records) ────────────
  const hasSystem  = !!eduSystem
  const hasHighest = !!eduHighest
  // `other` system has no ladder → free-text records, no highest-qualification gate.
  const isOtherSystem = eduSystem === 'other'
  const highestRank = rankOf(eduSystem, eduHighest)
  // Low qualification: a KNOWN level below the SSC-equivalent rank.
  const isLowQualification =
    isEduSystem(eduSystem) && !isOtherSystem && hasHighest &&
    highestRank != null && highestRank < SSC_EQUIVALENT_RANK
  // Show the detailed records UI only once we know the ceiling (highest selected,
  // SSC+), or for the free-text `other` system.
  const showDetailedRecords =
    isOtherSystem || (isEduSystem(eduSystem) && hasHighest && !isLowQualification)
  // A record counts as "detailed" if it carries any real schooling data (not just
  // a free-text note) — used to honour data-safety when downgrading to low-qual.
  const hasDetailedRecords = eduRecords.some(r =>
    r.level || r.institute || r.subject || r.board_university || r.result_value || r.passing_year)

  // Smart "Add …" button label from the next level we'd add.
  const nextAddLevel = nextDefaultLevel(eduSystem, eduHighest, eduRecords.map(r => r.level ?? '').filter(Boolean))
  const nextAddLabelKey = nextAddLevel ? levelLabelKey(eduSystem, nextAddLevel) : null
  const addButtonLabel = nextAddLabelKey
    ? t('biodata', 'edu_add_specific', { level: t('biodata', nextAddLabelKey) })
    : t('biodata', 'edu_add_record')

  // Single free-text note for low qualifications, stored as one note-only record
  // (no level → never counted as a detailed record, never trips validation).
  const lowQualNote = eduRecords.find(r => !r.level && r.note)?.note ?? ''
  const setLowQualNote = (v: string) => {
    setData('education_details', (v ? [normaliseEdu({ note: v, edu_type: deriveEduMethod(eduSystem) })] : []) as never)
  }

  const handleSystemChange = (system: string) => {
    setData('education_method', deriveEduMethod(system) as never)
    setData('education_medium', system as never)
    // Clear a highest qualification that doesn't belong to the new system.
    if (!isHighestValidForSystem(system, eduHighest)) {
      setData('highest_qualification', '' as never)
    }
    // Flag records that no longer belong to the new system.
    const stale = eduRecords.some(r => r.level && !isRecordLevelValid(system, '', r.level))
    setEduNotice(stale || eduRecords.length > 0 ? t('biodata', 'edu_system_changed_notice') : null)
  }

  const handleHighestChange = (value: string) => {
    setData('highest_qualification', value as never)
    const nowInvalid = eduRecords.some(r => r.level && !isRecordLevelValid(eduSystem, value, r.level))
    setEduNotice(nowInvalid ? t('biodata', 'edu_highest_changed_notice') : null)
  }

  const addEdu = () => {
    const used = eduRecords.map(r => r.level ?? '').filter(Boolean)
    const level = nextDefaultLevel(eduSystem, eduHighest, used)
    setData('education_details', [...eduRecords, normaliseEdu({ level, edu_type: deriveEduMethod(eduSystem) })] as never)
  }
  const updateEdu = (idx: number, rec: EducationRecord) => {
    const next = [...eduRecords]
    next[idx] = rec
    setData('education_details', next as never)
  }
  const removeEdu = (idx: number) => {
    setData('education_details', eduRecords.filter((_, i) => i !== idx) as never)
  }

  // ── Date of birth: day + month + year compose birth_date (single DB column) ──
  // birth_date stays the source of truth (age, search, matching, completion).
  const birthDay   = (data.birth_day as string) ?? ''
  const birthMonth = (data.birth_month as string) ?? ''
  const birthYear  = (data.birth_year as string) ?? ''
  const composeDob = (d: string, m: string, y: string): string => {
    if (!d || !m || !y) return ''
    const dd = Number(d), mm = Number(m), yy = Number(y)
    const dt = new Date(yy, mm - 1, dd)
    // Reject impossible combinations (e.g. Feb 30 rolls over to March).
    if (dt.getFullYear() !== yy || dt.getMonth() !== mm - 1 || dt.getDate() !== dd) return ''
    return `${yy}-${String(mm).padStart(2, '0')}-${String(dd).padStart(2, '0')}`
  }
  const setBirthPart = (part: 'd' | 'm' | 'y', value: string) => {
    const d = part === 'd' ? value : birthDay
    const m = part === 'm' ? value : birthMonth
    const y = part === 'y' ? value : birthYear
    setData({ ...data, birth_day: d, birth_month: m, birth_year: y, birth_date: composeDob(d, m, y) } as never)
  }
  const currentYear  = new Date().getFullYear()
  const birthDayOpts  = Array.from({ length: 31 }, (_, i) => ({ value: String(i + 1), label: String(i + 1) }))
  const birthYearOpts = Array.from({ length: 63 }, (_, i) => {
    const y = currentYear - 18 - i
    return { value: String(y), label: String(y) }
  })
  const monthOpts = Array.from({ length: 12 }, (_, i) => ({
    value: String(i + 1), label: t('biodata', `month_${i + 1}`),
  }))
  // All three parts chosen but the combination is not a real calendar date.
  const dobInvalid = !!(birthDay && birthMonth && birthYear) && !composeDob(birthDay, birthMonth, birthYear)
  // Health details only matter when physical status is not "healthy"/"prefer not say".
  const showHealthDetails = data.health_status === 'minor_condition' || data.health_status === 'disability'

  // ── Sibling detail helpers ───────────────────────────────────────────────────
  const broDetails: SiblingDetail[] = toSiblingList(data.brothers_details)
  const sisDetails: SiblingDetail[] = toSiblingList(data.sisters_details)

  const handleBrotherCount = (v: string) => {
    const count = v !== '' ? parseInt(v, 10) : 0
    setData('brothers', (v !== '' ? count : '') as never)
    if (count > broDetails.length) {
      setData('brothers_details', [
        ...broDetails,
        ...Array.from({ length: count - broDetails.length }, () => normaliseSibling({})),
      ] as never)
    } else {
      setData('brothers_details', broDetails.slice(0, count) as never)
    }
  }

  const handleSisterCount = (v: string) => {
    const count = v !== '' ? parseInt(v, 10) : 0
    setData('sisters', (v !== '' ? count : '') as never)
    if (count > sisDetails.length) {
      setData('sisters_details', [
        ...sisDetails,
        ...Array.from({ length: count - sisDetails.length }, () => normaliseSibling({})),
      ] as never)
    } else {
      setData('sisters_details', sisDetails.slice(0, count) as never)
    }
  }

  const updateBro = (idx: number, s: SiblingDetail) => {
    const next = [...broDetails]; next[idx] = s
    setData('brothers_details', next as never)
  }
  const updateSis = (idx: number, s: SiblingDetail) => {
    const next = [...sisDetails]; next[idx] = s
    setData('sisters_details', next as never)
  }

  // ── Derived state ────────────────────────────────────────────────────────────
  const isIslam = !data.religion || data.religion === 'Islam'
  const isPreviouslyMarried = data.marital_status && data.marital_status !== 'never_married'

  // ── Location: permanent-same-as-present toggle copies present → permanent ────
  const sameAddr = !!data.same_as_permanent
  const toggleSamePermanent = (v: boolean) => {
    setData({
      ...data,
      same_as_permanent: v,
      ...(v ? {
        permanent_country: data.residing_country ?? '',
        division:  data.current_division ?? '',
        district:  data.current_district ?? '',
        upazila:   data.current_upazila ?? '',
        village_area: data.current_area ?? '',
      } : {}),
    } as never)
  }

  // ── Translated option builders ───────────────────────────────────────────────
  const currentLabel = t('biodata', `step_labels.${step}`)
  const currentHelper = t('biodata', `step_helper.${step}`)

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
  // Education System + Highest Qualification options now come from @/lib/education
  // (system-driven). The old flat eduMethodOpts/qualOpts lists were removed.
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
  // diet / smoking option lists removed — fields no longer shown in Step 5
  // (DB columns and any saved values are intentionally preserved).
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

  // ── Phase C option builders ──────────────────────────────────────────────────
  const eduMediumOpts = [
    { value: 'general',        label: t('biodata', 'edu_medium_general') },
    { value: 'qawmi',          label: t('biodata', 'edu_medium_qawmi') },
    { value: 'alia',           label: t('biodata', 'edu_medium_alia') },
    { value: 'english_medium', label: t('biodata', 'edu_medium_english') },
    { value: 'vocational',     label: t('biodata', 'edu_medium_vocational') },
    { value: 'other',          label: t('biodata', 'edu_medium_other') },
  ]
  const incomeTypeOpts = [
    { value: 'monthly',   label: t('biodata', 'income_type_monthly') },
    { value: 'business',  label: t('biodata', 'income_type_business') },
    { value: 'freelance', label: t('biodata', 'income_type_freelance') },
    { value: 'daily',     label: t('biodata', 'income_type_daily') },
    { value: 'variable',  label: t('biodata', 'income_type_variable') },
    { value: 'private',   label: t('biodata', 'income_type_private') },
  ]
  const incomePrivacyOpts = [
    { value: 'public',       label: t('biodata', 'income_privacy_public') },
    { value: 'range',        label: t('biodata', 'income_privacy_range') },
    { value: 'members_only', label: t('biodata', 'income_privacy_members') },
    { value: 'private',      label: t('biodata', 'income_privacy_private') },
  ]
  const halalStatusOpts = [
    { value: 'halal',             label: t('biodata', 'halal_status_halal') },
    { value: 'halal_alternative', label: t('biodata', 'halal_status_alternative') },
    { value: 'not_sure',          label: t('biodata', 'halal_status_not_sure') },
    { value: 'prefer_not_say',    label: t('biodata', 'halal_status_prefer_not_say') },
  ]
  const biodataVisibilityOpts = [
    { value: 'public',              label: t('biodata', 'visibility_public') },
    { value: 'admin_approved_only', label: t('biodata', 'visibility_approved_only') },
    { value: 'private',             label: t('biodata', 'visibility_private') },
  ]
  // marital_substatus options depend on the chosen 4-value enum + gender.
  const maritalSubstatusOpts = (() => {
    const m = data.marital_status
    if (m === 'divorced') {
      return [
        { value: 'divorced',  label: t('biodata', 'sub_divorced') },
        { value: 'separated', label: t('biodata', 'sub_separated') },
      ]
    }
    if (m === 'widowed') {
      return user.gender === 'female'
        ? [{ value: 'widow', label: t('biodata', 'sub_widow') }]
        : [{ value: 'widower', label: t('biodata', 'sub_widower') }]
    }
    if (m === 'married') {
      return [{ value: 'second_marriage', label: t('biodata', 'sub_second_marriage') }]
    }
    return []
  })()

  // ── Partner districts (multi-select chips) ───────────────────────────────────
  const partnerDistricts: string[] = Array.isArray(data.partner_districts) ? data.partner_districts : []
  const addPartnerDistrict = (d: string) => {
    const v = d.trim()
    if (!v || partnerDistricts.includes(v)) return
    setData('partner_districts', [...partnerDistricts, v] as never)
  }
  const removePartnerDistrict = (d: string) => {
    setData('partner_districts', partnerDistricts.filter(x => x !== d) as never)
  }

  // ─── Render ──────────────────────────────────────────────────────────────────

  return (
    <AppLayout>
      <Head title={t('biodata', 'wizard_title')} />

      <div className="max-w-2xl mx-auto px-4 py-6 sm:py-8">

        {/* Step-based progress (each step = 10%) */}
        <div className="mb-6">
          <div className="flex items-center justify-between text-xs mb-2">
            <span className="font-semibold text-primary-600 uppercase tracking-wide">
              {t('biodata', 'wizard_subtitle', { step: String(step), total: String(totalSteps) })}
            </span>
            <span className="font-bold text-slate-700 tabular-nums">{step * 10}%</span>
          </div>
          <div className="h-2 rounded-full bg-slate-200 overflow-hidden mb-5">
            <div
              className="h-full rounded-full bg-primary-600 transition-all duration-500"
              style={{ width: `${step * 10}%` }}
            />
          </div>

          {/* Step circles with centered connector line */}
          <div className="relative px-1">
            <div className="absolute top-4 left-3 right-3 h-0.5 bg-slate-200" />
            <div
              className="absolute top-4 left-3 h-0.5 bg-emerald-400 transition-all duration-500"
              style={{ width: `calc((100% - 1.5rem) * ${(step - 1) / (totalSteps - 1)})` }}
            />
            <div className="relative flex justify-between">
              {Array.from({ length: totalSteps }, (_, i) => i + 1).map(num => {
                const Icon = STEP_ICONS[num] ?? User
                const done = step > num
                const active = step === num
                const clickable = num < step
                return (
                  <button
                    key={num}
                    type="button"
                    title={steps[num]}
                    onClick={() => clickable ? router.get(route('biodata.wizard', { step: num })) : undefined}
                    className={cn('flex items-center justify-center', clickable ? 'cursor-pointer' : 'cursor-default')}
                  >
                    <span className={cn(
                      'h-8 w-8 rounded-full flex items-center justify-center transition-all duration-200',
                      done   ? 'bg-emerald-500 text-white hover:scale-105' :
                      active ? 'bg-primary-600 text-white ring-4 ring-primary-100 scale-110 shadow-sm' :
                               'bg-white text-slate-300 border-2 border-slate-200',
                    )}>
                      {done ? <CheckCircle size={15} /> : <Icon size={15} />}
                    </span>
                  </button>
                )
              })}
            </div>
          </div>
        </div>

        {/* Form card */}
        <div className="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

          {/* Card header */}
          <div className={cn('px-6 py-5 border-b border-slate-100',
            step === 1 ? 'bg-gradient-to-r from-primary-50 via-white to-emerald-50' : 'bg-slate-50',
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

          {/* Form */}
          <form onSubmit={submit} className="p-6 space-y-5">

            {/* ── Step 1: Basic Information ── */}
            {step === 1 && (
              <>
                <div className="rounded-xl bg-primary-50 border border-primary-100 px-4 py-3 text-sm text-primary-700">
                  {t('biodata', 'basic_info_subtitle')}
                </div>

                {fcVisible('marital_status') && (
                  <SearchableSelect
                    label={fcLabel('marital_status', t('biodata', 'marital_status'))}
                    value={data.marital_status ?? ''}
                    onChange={v => setData('marital_status', v as never)}
                    options={maritalStatusOpts}
                    error={errors.marital_status}
                    required={fcRequired('marital_status')}
                  />
                )}

                {/* Date of birth — day + month + year (composed into birth_date).
                    Admin can hide/require the whole DOB block via the registry. */}
                {fcVisible('birth_date') && (
                  <div>
                    <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                      <SearchableSelect
                        label={t('biodata', 'birth_day')}
                        value={birthDay}
                        onChange={v => setBirthPart('d', v)}
                        options={birthDayOpts}
                        placeholder={t('biodata', 'birth_day')}
                        required={fcRequired('birth_date')}
                      />
                      <SearchableSelect
                        label={t('biodata', 'birth_month')}
                        value={birthMonth}
                        onChange={v => setBirthPart('m', v)}
                        options={monthOpts}
                        placeholder={t('biodata', 'birth_month')}
                        required={fcRequired('birth_date')}
                      />
                      <SearchableSelect
                        label={t('biodata', 'birth_year')}
                        value={birthYear}
                        onChange={v => setBirthPart('y', v)}
                        options={birthYearOpts}
                        error={errors.birth_date}
                        required={fcRequired('birth_date')}
                      />
                    </div>
                    {dobInvalid && (
                      <p className="mt-1.5 text-xs text-red-600">{t('biodata', 'dob_invalid')}</p>
                    )}
                  </div>
                )}

                {/* Physical summary */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {fcVisible('height_cm') && (
                    <HeightSelect
                      label={fcLabel('height_cm', t('biodata', 'height'))}
                      value={data.height_cm ?? ''}
                      onChange={v => setData('height_cm', v as never)}
                      error={errors.height_cm}
                      required={fcRequired('height_cm')}
                    />
                  )}
                  {fcVisible('weight_kg') && (
                    <WeightSelect
                      label={fcLabel('weight_kg', t('biodata', 'weight'))}
                      value={data.weight_kg ?? ''}
                      onChange={v => setData('weight_kg', v as never)}
                      error={errors.weight_kg}
                      required={fcRequired('weight_kg')}
                    />
                  )}
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {fcVisible('complexion') && (
                    <SearchableSelect
                      label={fcLabel('complexion', t('biodata', 'complexion'))}
                      value={data.complexion ?? ''}
                      onChange={v => setData('complexion', v as never)}
                      options={complexionOpts}
                      error={errors.complexion}
                      required={fcRequired('complexion')}
                    />
                  )}
                  {fcVisible('blood_group') && (
                    <SearchableSelect
                      label={fcLabel('blood_group', t('biodata', 'blood_group'))}
                      value={data.blood_group ?? ''}
                      onChange={v => setData('blood_group', v as never)}
                      options={bloodGroupOpts}
                      error={errors.blood_group}
                      required={fcRequired('blood_group')}
                    />
                  )}
                </div>

                {/* Physical / health status */}
                {fcVisible('health_status') && (
                  <SearchableSelect
                    label={fcLabel('health_status', t('biodata', 'physical_status'))}
                    value={data.health_status ?? ''}
                    onChange={v => setData('health_status', v as never)}
                    options={healthOpts}
                    error={errors.health_status}
                    required={fcRequired('health_status')}
                  />
                )}
                {showHealthDetails && (
                  <WizardTextarea
                    label={t('biodata', 'health_issue_details')}
                    value={(data.health_details as string) ?? ''}
                    onChange={v => setData('health_details', v as never)}
                    error={errors.health_details}
                    placeholder={t('biodata', 'health_issue_details_ph')}
                    rows={3}
                    maxLength={500}
                  />
                )}
              </>
            )}

            {/* ── Step 2: Location ── */}
            {step === 2 && (
              <>
                {/* Section 1 — Present Address */}
                <AddressBlock
                  title={t('biodata', 'present_address')}
                  helper={t('biodata', 'present_address_help')}
                  country={data.residing_country ?? ''}
                  onCountry={v => setData('residing_country', v as never)}
                  bd={{ division: data.current_division, district: data.current_district, upazila: data.current_upazila }}
                  onBd={val => setData({ ...data, current_division: val.division ?? '', current_district: val.district ?? '', current_upazila: val.upazila ?? '' } as never)}
                  area={data.current_area ?? ''}
                  onArea={v => setData('current_area', v as never)}
                  areaLabel={t('biodata', 'addr_area')}
                  areaPh={t('biodata', 'addr_area_ph')}
                  city={data.residing_city ?? ''}
                  onCity={v => setData('residing_city', v as never)}
                  state={data.current_division ?? ''}
                  onState={v => setData('current_division', v as never)}
                  errors={{ country: errors.residing_country, division: errors.current_division, district: errors.current_district, city: errors.residing_city }}
                />

                {/* Permanent same as present */}
                <WizardToggle
                  value={sameAddr}
                  label={t('biodata', 'permanent_same_as_present')}
                  onChange={toggleSamePermanent}
                />

                {/* Section 2 — Permanent Address */}
                {sameAddr ? (
                  <div className="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3 text-sm text-slate-500">
                    {t('biodata', 'permanent_same_summary')}
                  </div>
                ) : (
                  <AddressBlock
                    title={t('biodata', 'permanent_address')}
                    helper={t('biodata', 'permanent_address_help')}
                    country={data.permanent_country ?? ''}
                    onCountry={v => setData('permanent_country', v as never)}
                    bd={{ division: data.division, district: data.district, upazila: data.upazila }}
                    onBd={val => setData({ ...data, division: val.division ?? '', district: val.district ?? '', upazila: val.upazila ?? '' } as never)}
                    area={data.village_area ?? ''}
                    onArea={v => setData('village_area', v as never)}
                    areaLabel={t('biodata', 'addr_area')}
                    areaPh={t('biodata', 'addr_area_ph')}
                    city={data.district ?? ''}
                    onCity={v => setData('district', v as never)}
                    state={data.division ?? ''}
                    onState={v => setData('division', v as never)}
                    errors={{ country: errors.permanent_country, division: errors.division, district: errors.district, city: errors.district }}
                  />
                )}
              </>
            )}

            {/* ── Step 3: Religion ── */}
            {step === 3 && (
              <>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {fcVisible('religion') && (
                    <SearchableSelect
                      label={fcLabel('religion', t('biodata', 'religion'))}
                      value={data.religion ?? ''}
                      onChange={v => setData('religion', v as never)}
                      options={RELIGION_OPTIONS}
                      error={errors.religion}
                      allowFreeText
                      required={fcRequired('religion')}
                      placeholder="Select religion..."
                    />
                  )}
                  {isIslam && fcVisible('sect') && (
                    <SearchableSelect
                      label={fcLabel('sect', t('biodata', 'sect'))}
                      value={data.sect ?? ''}
                      onChange={v => setData('sect', v as never)}
                      options={SECT_OPTIONS}
                      error={errors.sect}
                      allowFreeText
                      required={fcRequired('sect')}
                      placeholder="e.g. Hanafi, Ahle Hadith"
                    />
                  )}
                </div>

                {isIslam && (
                  <>
                    <div className="rounded-xl bg-slate-50 border border-slate-100 p-4 space-y-3">
                      <SectionLabel>Practice & Observance</SectionLabel>
                      {fcVisible('is_practicing') && (
                        <WizardToggle
                          value={!!data.is_practicing}
                          label={fcLabel('is_practicing', t('biodata', 'is_practicing'))}
                          onChange={v => setData('is_practicing', v as never)}
                        />
                      )}
                      {fcVisible('prayers_info') && (
                        <SearchableSelect
                          label={fcLabel('prayers_info', t('biodata', 'prayers_info'))}
                          value={data.prayers_info ?? ''}
                          onChange={v => setData('prayers_info', v as never)}
                          options={prayersOpts}
                          error={errors.prayers_info}
                          required={fcRequired('prayers_info')}
                        />
                      )}
                      {fcVisible('quran_recitation') && (
                        <SearchableSelect
                          label={fcLabel('quran_recitation', t('biodata', 'quran_recitation'))}
                          value={data.quran_recitation ?? ''}
                          onChange={v => setData('quran_recitation', v as never)}
                          options={quranOpts}
                          error={errors.quran_recitation}
                          required={fcRequired('quran_recitation')}
                        />
                      )}
                    </div>

                    <div className="space-y-4">
                      <SectionLabel>Appearance & Dress</SectionLabel>
                      {user.gender === 'female' ? (
                        <>
                          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <SearchableSelect
                              label={fcLabel('hijab_info', t('biodata', 'hijab_info'))}
                              value={data.hijab_info ?? ''}
                              onChange={v => setData('hijab_info', v as never)}
                              options={hijabOpts}
                              error={errors.hijab_info}
                              required={fcRequired('hijab_info')}
                            />
                            <Input
                              label={`${t('biodata', 'niqab_since')} (${t('common', 'optional')})`}
                              value={data.niqab_since ?? ''}
                              onChange={e => setData('niqab_since', e.target.value as never)}
                              error={errors.niqab_since}
                              placeholder={t('biodata', 'since_ph')}
                            />
                          </div>
                          <WizardTextarea
                            label={`${t('biodata', 'purdah_details')} (${t('common', 'optional')})`}
                            value={(data.purdah_details as string) ?? ''}
                            onChange={v => setData('purdah_details', v as never)}
                            error={errors.purdah_details}
                            placeholder={t('biodata', 'purdah_details_ph')}
                            assist={{ field: 'religious', mode: user.mode, gender: user.gender }}
                            rows={2}
                            maxLength={500}
                          />
                        </>
                      ) : (
                        <>
                          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <SearchableSelect
                              label={fcLabel('beard_info', t('biodata', 'beard_info'))}
                              value={data.beard_info ?? ''}
                              onChange={v => setData('beard_info', v as never)}
                              options={BEARD_OPTIONS}
                              error={errors.beard_info}
                              allowFreeText
                              required={fcRequired('beard_info')}
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
                          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                            <Input
                              label={`${t('biodata', 'beard_since')} (${t('common', 'optional')})`}
                              value={data.beard_since ?? ''}
                              onChange={e => setData('beard_since', e.target.value as never)}
                              error={errors.beard_since}
                              placeholder={t('biodata', 'since_ph')}
                            />
                            <div className="pb-1">
                              <WizardToggle
                                value={!!data.pants_above_ankle}
                                label={t('biodata', 'pants_above_ankle')}
                                onChange={v => setData('pants_above_ankle', v as never)}
                              />
                            </div>
                          </div>
                        </>
                      )}
                    </div>

                    {/* Deeper deen practice detail (all optional) */}
                    <div className="rounded-xl bg-slate-50 border border-slate-100 p-4 space-y-3">
                      <SectionLabel>{t('biodata', 'deen_detail_section')}</SectionLabel>
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <Input
                          label={`${t('biodata', 'prayer_start_age')} (${t('common', 'optional')})`}
                          value={data.prayer_start_age ?? ''}
                          onChange={e => setData('prayer_start_age', e.target.value as never)}
                          error={errors.prayer_start_age}
                          placeholder={t('biodata', 'prayer_start_age_ph')}
                        />
                        <Input
                          label={`${t('biodata', 'weekly_missed_prayers')} (${t('common', 'optional')})`}
                          value={data.weekly_missed_prayers ?? ''}
                          onChange={e => setData('weekly_missed_prayers', e.target.value as never)}
                          error={errors.weekly_missed_prayers}
                          placeholder={t('biodata', 'weekly_missed_prayers_ph')}
                        />
                      </div>
                      <Input
                        label={`${t('biodata', 'mahram_practice')} (${t('common', 'optional')})`}
                        value={data.mahram_practice ?? ''}
                        onChange={e => setData('mahram_practice', e.target.value as never)}
                        error={errors.mahram_practice}
                        placeholder={t('biodata', 'mahram_practice_ph')}
                      />
                      <Input
                        label={`${t('biodata', 'islamic_books_read')} (${t('common', 'optional')})`}
                        value={data.islamic_books_read ?? ''}
                        onChange={e => setData('islamic_books_read', e.target.value as never)}
                        error={errors.islamic_books_read}
                        placeholder={t('biodata', 'islamic_books_read_ph')}
                      />
                      <Input
                        label={`${t('biodata', 'deen_work_details')} (${t('common', 'optional')})`}
                        value={data.deen_work_details ?? ''}
                        onChange={e => setData('deen_work_details', e.target.value as never)}
                        error={errors.deen_work_details}
                        placeholder={t('biodata', 'deen_work_details_ph')}
                      />
                      <Input
                        label={`${t('biodata', 'social_media_usage')} (${t('common', 'optional')})`}
                        value={data.social_media_usage ?? ''}
                        onChange={e => setData('social_media_usage', e.target.value as never)}
                        error={errors.social_media_usage}
                        placeholder={t('biodata', 'social_media_usage_ph')}
                      />
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
                            type="range" min={1} max={10}
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
              </>
            )}

            {/* ── Step 4: Education & Career ── */}
            {step === 4 && (
              <>
                {/* ═══ EDUCATION ═══ — the whole education-system block (system,
                    highest qualification + detailed records) can be hidden/required
                    by the admin via the registry (education_medium). */}
                {fcVisible('education_medium') && (
                <>
                <SectionLabel>{t('biodata', 'section_education')}</SectionLabel>

                {/* Education System drives everything below. Highest Qualification
                    appears only for the laddered systems (not free-text `other`). */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={fcLabel('education_medium', t('biodata', 'education_system'))}
                    value={data.education_medium ?? ''}
                    onChange={handleSystemChange}
                    options={eduMediumOpts}
                    error={errors.education_medium}
                    helperText={t('biodata', 'education_system_help')}
                    required={fcRequired('education_medium')}
                  />
                  {isEduSystem(eduSystem) && !isOtherSystem && fcVisible('highest_qualification') && (
                    <SearchableSelect
                      label={fcLabel('highest_qualification', t('biodata', 'highest_qualification'))}
                      value={data.highest_qualification ?? ''}
                      onChange={handleHighestChange}
                      options={highestOpts}
                      error={errors.highest_qualification}
                      emptyText={t('biodata', 'edu_select_system_first')}
                      required={fcRequired('highest_qualification')}
                    />
                  )}
                </div>

                {eduNotice && (
                  <div className="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs text-amber-800">
                    <AlertTriangle size={15} className="mt-0.5 shrink-0" />
                    <span>{eduNotice}</span>
                  </div>
                )}

                {/* (a) No system chosen yet */}
                {!hasSystem && (
                  <div className="rounded-xl border border-dashed border-slate-300 bg-slate-50 py-6 text-center">
                    <p className="text-sm text-slate-400">{t('biodata', 'edu_select_system_first')}</p>
                  </div>
                )}

                {/* (b) Laddered system chosen but no highest qualification yet */}
                {isEduSystem(eduSystem) && !isOtherSystem && !hasHighest && (
                  <div className="flex items-start gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    <GraduationCap size={16} className="mt-0.5 shrink-0" />
                    <span>{t('biodata', 'edu_select_highest_first')}</span>
                  </div>
                )}

                {/* (c) Low qualification (below SSC-equivalent): no detailed records */}
                {isLowQualification && (
                  <div className="space-y-3">
                    <div className="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                      <CheckCircle size={18} className="mt-0.5 shrink-0 text-emerald-500" />
                      <div>
                        <p className="text-sm font-medium text-slate-700">{t('biodata', 'edu_low_qual_title')}</p>
                        <p className="text-xs text-slate-500 mt-0.5">{t('biodata', 'edu_low_qual_desc')}</p>
                      </div>
                    </div>

                    {hasDetailedRecords ? (
                      <>
                        <div className="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs text-amber-800">
                          <AlertTriangle size={15} className="mt-0.5 shrink-0" />
                          <span>{t('biodata', 'edu_low_qual_review')}</span>
                        </div>
                        <div className="space-y-3">
                          {eduRecords.map((rec, idx) => (
                            <EducationRecordCard
                              key={idx}
                              record={rec}
                              index={idx}
                              levelOptions={recordLevelOpts(rec.level ?? '')}
                              levelLabel={t('biodata', 'edu_record_level')}
                              allowFreeLevel={false}
                              invalid={eduInvalidIndexes.includes(idx)}
                              invalidMsg={t('biodata', 'edu_level_too_high')}
                              onChange={r => updateEdu(idx, r)}
                              onRemove={() => removeEdu(idx)}
                            />
                          ))}
                        </div>
                      </>
                    ) : (
                      <Input
                        label={t('biodata', 'edu_low_qual_note_label')}
                        value={lowQualNote}
                        onChange={e => setLowQualNote(e.target.value)}
                        placeholder={t('biodata', 'edu_low_qual_note_ph')}
                      />
                    )}
                  </div>
                )}

                {/* (d) Detailed education records (SSC+ or free-text `other`).
                    Admin can hide the whole records repeater via education_details. */}
                {showDetailedRecords && fcVisible('education_details') && (
                  <div className="space-y-3">
                    <p className="text-xs text-slate-400">{t('biodata', 'education_records_help')}</p>

                    {eduRecords.length === 0 && (
                      <div className="rounded-xl border border-dashed border-slate-300 bg-slate-50 py-8 text-center">
                        <p className="text-sm text-slate-400">{t('biodata', 'edu_no_records')}</p>
                      </div>
                    )}

                    <div className="space-y-3">
                      {eduRecords.map((rec, idx) => (
                        <EducationRecordCard
                          key={idx}
                          record={rec}
                          index={idx}
                          levelOptions={recordLevelOpts(rec.level ?? '')}
                          levelLabel={t('biodata', 'edu_record_level')}
                          allowFreeLevel={isOtherSystem}
                          invalid={eduInvalidIndexes.includes(idx)}
                          invalidMsg={t('biodata', 'edu_level_too_high')}
                          onChange={r => updateEdu(idx, r)}
                          onRemove={() => removeEdu(idx)}
                        />
                      ))}
                    </div>

                    {/* Add button only when an allowed level is still missing.
                        Free-text `other` always allows adding more records. */}
                    {!isOtherSystem && allLevelsAdded ? (
                      <p className="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-center text-xs text-slate-500">
                        {t('biodata', 'edu_all_levels_added')}
                      </p>
                    ) : (
                      <button
                        type="button"
                        onClick={addEdu}
                        className="flex items-center gap-2 w-full justify-center rounded-xl border-2 border-dashed border-primary-300 bg-primary-50 px-4 py-3 text-sm font-medium text-primary-600 hover:bg-primary-100 hover:border-primary-400 transition-colors"
                      >
                        <Plus size={16} /> {isOtherSystem ? t('biodata', 'edu_add_record') : addButtonLabel}
                      </button>
                    )}
                  </div>
                )}
                </>
                )}

                {/* ═══ CAREER ═══ */}
                <SectionLabel>{t('biodata', 'section_career')}</SectionLabel>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {fcVisible('occupation_category') && (
                    <SearchableSelect
                      label={fcLabel('occupation_category', t('biodata', 'occupation_category'))}
                      value={data.occupation_category ?? ''}
                      onChange={v => setData('occupation_category', v as never)}
                      options={occCatOpts}
                      error={errors.occupation_category}
                      required={fcRequired('occupation_category')}
                    />
                  )}
                  {fcVisible('occupation') && (
                    <SearchableSelect
                      label={fcLabel('occupation', t('biodata', 'occupation'))}
                      value={data.occupation ?? ''}
                      onChange={v => setData('occupation', v as never)}
                      options={OCCUPATION_OPTIONS}
                      error={errors.occupation}
                      allowFreeText
                      required={fcRequired('occupation')}
                      placeholder={t('biodata', 'occupation_ph')}
                    />
                  )}
                </div>

                <WizardTextarea
                  label={t('biodata', 'profession_details')}
                  value={(data.profession_details as string) ?? ''}
                  onChange={v => setData('profession_details', v as never)}
                  error={errors.profession_details}
                  placeholder={t('biodata', 'profession_details_ph')}
                  rows={3}
                  assist={{ field: 'profession_details', mode: user.mode, gender: user.gender }}
                />

                {/* Income — all optional, privacy-first defaults */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <Input
                    label={`${t('biodata', 'monthly_income')} (${t('common', 'optional')})`}
                    type="text"
                    inputMode="numeric"
                    value={data.monthly_income !== '' && data.monthly_income !== undefined ? String(data.monthly_income) : ''}
                    onChange={e => {
                      const digits = e.target.value.replace(/[^\d]/g, '')
                      setData('monthly_income', (digits ? parseInt(digits, 10) : '') as never)
                    }}
                    onWheel={e => (e.target as HTMLInputElement).blur()}
                    error={errors.monthly_income}
                    placeholder={t('biodata', 'monthly_income_ph')}
                    helperText={t('biodata', 'monthly_income_help')}
                  />
                  <SearchableSelect
                    label={`${t('biodata', 'income_type')} (${t('common', 'optional')})`}
                    value={data.income_type ?? ''}
                    onChange={v => setData('income_type', v as never)}
                    options={incomeTypeOpts}
                    error={errors.income_type}
                    placeholder="— Select —"
                  />
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'income_privacy')}
                    value={data.income_privacy ?? ''}
                    onChange={v => setData('income_privacy', v as never)}
                    options={incomePrivacyOpts}
                    error={errors.income_privacy}
                    helperText={t('biodata', 'income_privacy_help')}
                  />
                  {fcVisible('profession_halal_status') && (
                    <SearchableSelect
                      label={fcFieldLabel('profession_halal_status', t('biodata', 'profession_halal_status'))}
                      value={data.profession_halal_status ?? ''}
                      onChange={v => setData('profession_halal_status', v as never)}
                      options={halalStatusOpts}
                      error={errors.profession_halal_status}
                      helperText={t('biodata', 'profession_halal_help')}
                      placeholder="— Select —"
                      required={fcRequired('profession_halal_status')}
                    />
                  )}
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <Input
                    label={`${t('biodata', 'workplace_type')} (${t('common', 'optional')})`}
                    value={data.workplace_type ?? ''}
                    onChange={e => setData('workplace_type', e.target.value as never)}
                    error={errors.workplace_type}
                    placeholder={t('biodata', 'workplace_type_ph')}
                  />
                </div>

                <WizardTextarea
                  label={`${t('biodata', 'future_career_plan')} (${t('common', 'optional')})`}
                  value={(data.future_career_plan as string) ?? ''}
                  onChange={v => setData('future_career_plan', v as never)}
                  error={errors.future_career_plan}
                  placeholder={t('biodata', 'future_career_plan_ph')}
                  rows={2}
                  maxLength={500}
                  assist={{ field: 'future_career_plan', mode: user.mode, gender: user.gender }}
                />
              </>
            )}

            {/* ── Step 6: Family ── */}
            {step === 6 && (
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
                  {fcVisible('father_profession') && (
                    <SearchableSelect
                      label={fcLabel('father_profession', t('biodata', 'father_profession'))}
                      value={data.father_profession ?? ''}
                      onChange={v => setData('father_profession', v as never)}
                      options={PROFESSION_OPTIONS}
                      error={errors.father_profession}
                      allowFreeText
                      required={fcRequired('father_profession')}
                      placeholder="e.g. Retired, Business"
                    />
                  )}
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
                  {fcVisible('mother_profession') && (
                    <SearchableSelect
                      label={fcLabel('mother_profession', t('biodata', 'mother_profession'))}
                      value={data.mother_profession ?? ''}
                      onChange={v => setData('mother_profession', v as never)}
                      options={PROFESSION_OPTIONS}
                      error={errors.mother_profession}
                      allowFreeText
                      required={fcRequired('mother_profession')}
                      placeholder="e.g. Homemaker, Teacher"
                    />
                  )}
                </div>
                <WizardToggle
                  value={!!data.mother_alive}
                  label={t('biodata', 'mother_alive')}
                  onChange={v => setData('mother_alive', v as never)}
                />

                {/* Siblings */}
                <SectionLabel>Siblings</SectionLabel>
                <div className="grid grid-cols-2 gap-4">
                  {fcVisible('brothers') && (
                    <SearchableSelect
                      label={fcLabel('brothers', t('biodata', 'brothers'))}
                      value={data.brothers !== '' && data.brothers !== undefined ? String(data.brothers) : ''}
                      onChange={handleBrotherCount}
                      options={COUNT_OPTIONS}
                      error={errors.brothers}
                      placeholder="0"
                      required={fcRequired('brothers')}
                    />
                  )}
                  {fcVisible('sisters') && (
                    <SearchableSelect
                      label={fcLabel('sisters', t('biodata', 'sisters'))}
                      value={data.sisters !== '' && data.sisters !== undefined ? String(data.sisters) : ''}
                      onChange={handleSisterCount}
                      options={COUNT_OPTIONS}
                      error={errors.sisters}
                      placeholder="0"
                      required={fcRequired('sisters')}
                    />
                  )}
                </div>

                {/* Brother details — admin can hide the sibling cards via the registry. */}
                {broDetails.length > 0 && fcVisible('brothers_details') && (
                  <div className="space-y-3">
                    <p className="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                      Brother Details <span className="text-slate-300 font-normal">(optional)</span>
                    </p>
                    {broDetails.map((s, idx) => (
                      <SiblingDetailCard
                        key={idx}
                        sibling={s}
                        index={idx}
                        genderLabel="Brother"
                        onChange={s => updateBro(idx, s)}
                        onRemove={() => {
                          const next = broDetails.filter((_, i) => i !== idx)
                          setData('brothers_details', next as never)
                          setData('brothers', next.length as never)
                        }}
                      />
                    ))}
                  </div>
                )}

                {/* Sister details — admin can hide the sibling cards via the registry. */}
                {sisDetails.length > 0 && fcVisible('sisters_details') && (
                  <div className="space-y-3">
                    <p className="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                      Sister Details <span className="text-slate-300 font-normal">(optional)</span>
                    </p>
                    {sisDetails.map((s, idx) => (
                      <SiblingDetailCard
                        key={idx}
                        sibling={s}
                        index={idx}
                        genderLabel="Sister"
                        onChange={s => updateSis(idx, s)}
                        onRemove={() => {
                          const next = sisDetails.filter((_, i) => i !== idx)
                          setData('sisters_details', next as never)
                          setData('sisters', next.length as never)
                        }}
                      />
                    ))}
                  </div>
                )}

                <SectionLabel>Family Background</SectionLabel>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  {fcVisible('family_type') && (
                    <SearchableSelect
                      label={fcLabel('family_type', t('biodata', 'family_type'))}
                      value={data.family_type ?? ''}
                      onChange={v => setData('family_type', v as never)}
                      options={familyTypeOpts}
                      error={errors.family_type}
                      required={fcRequired('family_type')}
                    />
                  )}
                  {fcVisible('family_financial_status') && (
                    <SearchableSelect
                      label={fcLabel('family_financial_status', t('biodata', 'family_financial_status'))}
                      value={data.family_financial_status ?? ''}
                      onChange={v => setData('family_financial_status', v as never)}
                      options={financeOpts}
                      error={errors.family_financial_status}
                      required={fcRequired('family_financial_status')}
                    />
                  )}
                  <SearchableSelect
                    label={t('biodata', 'home_ownership')}
                    value={data.home_ownership ?? ''}
                    onChange={v => setData('home_ownership', v as never)}
                    options={homeOpts}
                    error={errors.home_ownership}
                  />
                </div>

                <Input
                  label={`${t('biodata', 'uncle_profession')} (${t('common', 'optional')})`}
                  value={data.uncle_profession ?? ''}
                  onChange={e => setData('uncle_profession', e.target.value as never)}
                  error={errors.uncle_profession}
                  placeholder={t('biodata', 'uncle_profession_ph')}
                />

                {fcVisible('family_details') && (
                  <WizardTextarea
                    label={fcLabel('family_details', t('biodata', 'family_details'))}
                    required={fcRequired('family_details')}
                    value={(data.family_details as string) ?? ''}
                    onChange={v => setData('family_details', v as never)}
                    error={errors.family_details}
                    placeholder="Brief description of your family background..."
                    rows={3}
                    assist={{ field: 'family_details', mode: user.mode, gender: user.gender }}
                  />
                )}

                <WizardTextarea
                  label={`${t('biodata', 'family_assets_details')} (${t('common', 'optional')})`}
                  value={(data.family_assets_details as string) ?? ''}
                  onChange={v => setData('family_assets_details', v as never)}
                  error={errors.family_assets_details}
                  placeholder={t('biodata', 'family_assets_details_ph')}
                  rows={2}
                  maxLength={1000}
                />
              </>
            )}

            {/* ── Step 5: Lifestyle ── */}
            {step === 5 && (
              <>
                <SectionLabel>{t('biodata', 'section_lifestyle')}</SectionLabel>
                {/* Diet & Smoking removed from the form — columns kept in the DB so
                    existing values are preserved and re-saved untouched. */}
                {fcVisible('hobbies') && (
                  <WizardTextarea
                    label={fcLabel('hobbies', t('biodata', 'hobbies'))}
                    required={fcRequired('hobbies')}
                    value={(data.hobbies as string) ?? ''}
                    onChange={v => setData('hobbies', v as never)}
                    error={errors.hobbies}
                    placeholder="Reading, cooking, traveling, gardening..."
                    rows={3}
                    assist={{ field: 'hobbies', mode: user.mode, gender: user.gender }}
                  />
                )}
              </>
            )}

            {/* ── Step 7: Marriage & Guardian ── */}
            {step === 7 && (
              <>
                {maritalSubstatusOpts.length > 0 && (
                  <SearchableSelect
                    label={t('biodata', 'marital_substatus')}
                    value={data.marital_substatus ?? ''}
                    onChange={v => setData('marital_substatus', v as never)}
                    options={maritalSubstatusOpts}
                    error={errors.marital_substatus}
                  />
                )}

                <SectionLabel>{t('biodata', 'marriage_thoughts_section')}</SectionLabel>
                {fcVisible('why_getting_married') && (
                  <WizardTextarea
                    label={fcFieldLabel('why_getting_married', t('biodata', 'why_getting_married'))}
                    required={fcRequired('why_getting_married')}
                    value={(data.why_getting_married as string) ?? ''}
                    onChange={v => setData('why_getting_married', v as never)}
                    error={errors.why_getting_married}
                    placeholder={t('biodata', 'why_getting_married_ph')}
                    rows={3}
                    maxLength={1000}
                    assist={{ field: 'why_getting_married', mode: user.mode, gender: user.gender }}
                  />
                )}
                {fcVisible('marriage_thoughts') && (
                  <WizardTextarea
                    label={fcFieldLabel('marriage_thoughts', t('biodata', 'marriage_thoughts'))}
                    required={fcRequired('marriage_thoughts')}
                    value={(data.marriage_thoughts as string) ?? ''}
                    onChange={v => setData('marriage_thoughts', v as never)}
                    error={errors.marriage_thoughts}
                    placeholder={t('biodata', 'marriage_thoughts_ph')}
                    rows={3}
                    maxLength={1000}
                    assist={{ field: 'marriage_thoughts', mode: user.mode, gender: user.gender }}
                  />
                )}
                <Input
                  label={`${t('biodata', 'marriage_timeline')} (${t('common', 'optional')})`}
                  value={data.marriage_timeline ?? ''}
                  onChange={e => setData('marriage_timeline', e.target.value as never)}
                  error={errors.marriage_timeline}
                  placeholder={t('biodata', 'marriage_timeline_ph')}
                />

                {user.gender === 'male' && (
                  <div className="rounded-xl bg-slate-50 border border-slate-100 p-4 space-y-3">
                    <SectionLabel>{t('biodata', 'after_marriage_section')}</SectionLabel>
                    {fcVisible('wife_in_veil') && (
                      <WizardToggle value={!!data.wife_in_veil} label={fcLabel('wife_in_veil', t('biodata', 'wife_in_veil'))}
                        onChange={v => setData('wife_in_veil', v as never)} />
                    )}
                    <WizardToggle value={!!data.wife_study_allowed} label={t('biodata', 'wife_study_allowed')}
                      onChange={v => setData('wife_study_allowed', v as never)} />
                    <WizardToggle value={!!data.wife_job_allowed} label={t('biodata', 'wife_job_allowed')}
                      onChange={v => setData('wife_job_allowed', v as never)} />
                    {fcVisible('polygamy_open') && (
                      <WizardToggle value={!!data.polygamy_open} label={fcLabel('polygamy_open', t('biodata', 'polygamy_open'))}
                        onChange={v => setData('polygamy_open', v as never)} />
                    )}
                    <Input
                      label={`${t('biodata', 'expect_gift_from_bride')} (${t('common', 'optional')})`}
                      value={data.expect_gift_from_bride ?? ''}
                      onChange={e => setData('expect_gift_from_bride', e.target.value as never)}
                      error={errors.expect_gift_from_bride}
                      placeholder={t('biodata', 'expect_gift_ph')}
                    />
                    {!!data.expect_gift_from_bride && (
                      <Input
                        label={t('biodata', 'gift_expectation_details')}
                        value={data.gift_expectation_details ?? ''}
                        onChange={e => setData('gift_expectation_details', e.target.value as never)}
                        error={errors.gift_expectation_details}
                        placeholder={t('biodata', 'gift_expectation_details_ph')}
                      />
                    )}
                  </div>
                )}

                {user.gender === 'female' && (
                  <div className="rounded-xl bg-rose-50 border border-rose-100 p-4 space-y-3">
                    <SectionLabel>{t('biodata', 'female_intentions_section')}</SectionLabel>
                    {fcVisible('wants_to_work') && (
                      <WizardToggle value={!!data.wants_to_work} label={fcLabel('wants_to_work', t('biodata', 'wants_to_work'))}
                        onChange={v => setData('wants_to_work', v as never)} />
                    )}
                    <WizardToggle value={!!data.continue_study} label={t('biodata', 'continue_study')}
                      onChange={v => setData('continue_study', v as never)} />
                    <WizardToggle value={!!data.continue_job} label={t('biodata', 'continue_job')}
                      onChange={v => setData('continue_job', v as never)} />
                    <Input
                      label={`${t('biodata', 'preferred_living')} (${t('common', 'optional')})`}
                      value={data.preferred_living ?? ''}
                      onChange={e => setData('preferred_living', e.target.value as never)}
                      error={errors.preferred_living}
                      placeholder={t('biodata', 'preferred_living_ph')}
                    />
                  </div>
                )}

                {fcVisible('guardian_agree') && (
                  <WizardToggle value={!!data.guardian_agree} label={fcLabel('guardian_agree', t('biodata', 'guardian_agree'))}
                    onChange={v => setData('guardian_agree', v as never)} />
                )}

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {fcVisible('residence_after_marriage') && (
                    <SearchableSelect
                      label={fcLabel('residence_after_marriage', t('biodata', 'residence_after_marriage'))}
                      value={data.residence_after_marriage ?? ''}
                      onChange={v => setData('residence_after_marriage', v as never)}
                      options={RESIDENCE_OPTIONS}
                      error={errors.residence_after_marriage}
                      allowFreeText
                      required={fcRequired('residence_after_marriage')}
                      placeholder="Dhaka / Abroad / Flexible"
                    />
                  )}
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

                {/* Children — only for previously married */}
                {isPreviouslyMarried && (
                  <div className="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-4">
                    <SectionLabel>Children from Previous Marriage</SectionLabel>
                    <WizardToggle
                      value={!!data.has_children}
                      label="Yes, I have children from a previous marriage"
                      onChange={v => setData('has_children', v as never)}
                    />
                    {data.has_children && (
                      <>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                          <Input
                            label="Number of children"
                            type="number"
                            value={data.children_count !== '' && data.children_count !== undefined ? String(data.children_count) : ''}
                            onChange={e => setData('children_count', (e.target.value ? parseInt(e.target.value, 10) : '') as never)}
                            error={errors.children_count}
                            placeholder="e.g. 2"
                          />
                          <Input
                            label="Children live with"
                            value={data.children_live_with ?? ''}
                            onChange={e => setData('children_live_with', e.target.value as never)}
                            error={errors.children_live_with}
                            placeholder="e.g. With me, With grandparents"
                          />
                        </div>
                        <WizardTextarea
                          label="Children details (optional)"
                          value={(data.children_notes as string) ?? ''}
                          onChange={v => setData('children_notes', v as never)}
                          error={errors.children_notes}
                          placeholder="Brief info about your children..."
                          rows={2}
                        />
                      </>
                    )}
                  </div>
                )}

                {/* Divorced-specific */}
                {data.marital_status === 'divorced' && (
                  <div className="rounded-xl border border-amber-200 bg-amber-50 p-4 space-y-3">
                    <SectionLabel>{t('biodata', 'divorce_section')}</SectionLabel>
                    <p className="text-xs text-amber-700">{t('biodata', 'sensitive_note')}</p>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <Input
                        label={`${t('biodata', 'previous_marriage_date')} (${t('common', 'optional')})`}
                        type="date"
                        value={data.previous_marriage_date ?? ''}
                        onChange={e => setData('previous_marriage_date', e.target.value as never)}
                        error={errors.previous_marriage_date}
                      />
                      <Input
                        label={`${t('biodata', 'divorce_date')} (${t('common', 'optional')})`}
                        type="date"
                        value={data.divorce_date ?? ''}
                        onChange={e => setData('divorce_date', e.target.value as never)}
                        error={errors.divorce_date}
                      />
                    </div>
                    <WizardTextarea
                      label={`${t('biodata', 'divorce_reason')} (${t('common', 'optional')})`}
                      value={(data.divorce_reason as string) ?? ''}
                      onChange={v => setData('divorce_reason', v as never)}
                      error={errors.divorce_reason}
                      placeholder={t('biodata', 'divorce_reason_ph')}
                      rows={2}
                      maxLength={1000}
                    />
                  </div>
                )}

                {/* Widowed-specific */}
                {data.marital_status === 'widowed' && (
                  <div className="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-3">
                    <SectionLabel>{t('biodata', 'widowed_section')}</SectionLabel>
                    <p className="text-xs text-slate-500">{t('biodata', 'sensitive_note')}</p>
                    <Input
                      label={`${t('biodata', 'spouse_death_date')} (${t('common', 'optional')})`}
                      type="date"
                      value={data.spouse_death_date ?? ''}
                      onChange={e => setData('spouse_death_date', e.target.value as never)}
                      error={errors.spouse_death_date}
                    />
                    <WizardTextarea
                      label={`${t('biodata', 'spouse_death_reason')} (${t('common', 'optional')})`}
                      value={(data.spouse_death_reason as string) ?? ''}
                      onChange={v => setData('spouse_death_reason', v as never)}
                      error={errors.spouse_death_reason}
                      placeholder={t('biodata', 'spouse_death_reason_ph')}
                      rows={2}
                      maxLength={1000}
                    />
                    <WizardTextarea
                      label={`${t('biodata', 'child_acceptance_expectation')} (${t('common', 'optional')})`}
                      value={(data.child_acceptance_expectation as string) ?? ''}
                      onChange={v => setData('child_acceptance_expectation', v as never)}
                      error={errors.child_acceptance_expectation}
                      placeholder={t('biodata', 'child_acceptance_expectation_ph')}
                      rows={2}
                      maxLength={1000}
                    />
                  </div>
                )}

                {/* Married / second-marriage-specific */}
                {data.marital_status === 'married' && (
                  <div className="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-3">
                    <SectionLabel>{t('biodata', 'second_marriage_section')}</SectionLabel>
                    <p className="text-xs text-slate-500">{t('biodata', 'sensitive_note')}</p>
                    <WizardTextarea
                      label={`${t('biodata', 'reason_for_second_marriage')} (${t('common', 'optional')})`}
                      value={(data.reason_for_second_marriage as string) ?? ''}
                      onChange={v => setData('reason_for_second_marriage', v as never)}
                      error={errors.reason_for_second_marriage}
                      placeholder={t('biodata', 'reason_for_second_marriage_ph')}
                      rows={2}
                      maxLength={1000}
                    />
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <Input
                        label={`${t('biodata', 'current_wife_count')} (${t('common', 'optional')})`}
                        type="number"
                        value={data.current_wife_count !== '' && data.current_wife_count !== undefined ? String(data.current_wife_count) : ''}
                        onChange={e => setData('current_wife_count', (e.target.value ? parseInt(e.target.value, 10) : '') as never)}
                        error={errors.current_wife_count}
                        placeholder="e.g. 1"
                      />
                      <Input
                        label={`${t('biodata', 'second_marriage_living')} (${t('common', 'optional')})`}
                        value={data.second_marriage_living ?? ''}
                        onChange={e => setData('second_marriage_living', e.target.value as never)}
                        error={errors.second_marriage_living}
                        placeholder={t('biodata', 'second_marriage_living_ph')}
                      />
                    </div>
                    <WizardToggle value={!!data.current_family_consent} label={t('biodata', 'current_family_consent')}
                      onChange={v => setData('current_family_consent', v as never)} />
                    <WizardToggle value={!!data.first_wife_knows} label={t('biodata', 'first_wife_knows')}
                      onChange={v => setData('first_wife_knows', v as never)} />
                  </div>
                )}

              </>
            )}

            {/* ── Step 9: Contact & Privacy ── */}
            {step === 9 && (
              <>
                <div className="rounded-xl bg-primary-50 border border-primary-100 px-4 py-3 text-sm text-primary-700">
                  {t('biodata', 'contact_intro')}
                </div>

                <SectionLabel>{t('biodata', 'guardian_contact_section')}</SectionLabel>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {fcVisible('contact_person_name') && (
                    <Input
                      label={fcFieldLabel('contact_person_name', t('biodata', 'contact_person_name'))}
                      required={fcRequired('contact_person_name')}
                      value={data.contact_person_name ?? ''}
                      onChange={e => setData('contact_person_name', e.target.value as never)}
                      error={errors.contact_person_name}
                      placeholder={fieldControl['contact_person_name']?.placeholder || t('biodata', 'contact_person_name_ph')}
                    />
                  )}
                  {fcVisible('guardian_name') && (
                    <Input
                      label={fcFieldLabel('guardian_name', t('biodata', 'guardian_name'))}
                      required={fcRequired('guardian_name')}
                      value={data.guardian_name ?? ''}
                      onChange={e => setData('guardian_name', e.target.value as never)}
                      error={errors.guardian_name}
                      placeholder={fieldControl['guardian_name']?.placeholder || t('biodata', 'guardian_name_ph')}
                    />
                  )}
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {fcVisible('guardian_mobile') && (
                    <PhoneNumberInput
                      label={fcLabel('guardian_mobile', t('biodata', 'guardian_mobile'))}
                      optional={!fcRequired('guardian_mobile')}
                      required={fcRequired('guardian_mobile')}
                      value={data.guardian_mobile ?? ''}
                      onChange={v => setData('guardian_mobile', v as never)}
                      error={errors.guardian_mobile}
                    />
                  )}
                  {fcVisible('guardian_relationship') && (
                    <SearchableSelect
                      label={fcFieldLabel('guardian_relationship', t('biodata', 'guardian_relationship'))}
                      required={fcRequired('guardian_relationship')}
                      value={data.guardian_relationship ?? ''}
                      onChange={v => setData('guardian_relationship', v as never)}
                      options={GUARDIAN_REL_OPTIONS}
                      error={errors.guardian_relationship}
                      allowFreeText
                      placeholder="Father / Brother / Uncle"
                    />
                  )}
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {fcVisible('guardian_email') && (
                    <Input
                      label={fcFieldLabel('guardian_email', t('biodata', 'guardian_email'))}
                      required={fcRequired('guardian_email')}
                      type="email"
                      value={data.guardian_email ?? ''}
                      onChange={e => setData('guardian_email', e.target.value as never)}
                      error={errors.guardian_email}
                      placeholder={fieldControl['guardian_email']?.placeholder || 'guardian@example.com'}
                    />
                  )}
                  {fcVisible('guardian_whatsapp') && (
                    <PhoneNumberInput
                      label={fcLabel('guardian_whatsapp', t('biodata', 'guardian_whatsapp'))}
                      optional={!fcRequired('guardian_whatsapp')}
                      required={fcRequired('guardian_whatsapp')}
                      value={data.guardian_whatsapp ?? ''}
                      onChange={v => setData('guardian_whatsapp', v as never)}
                      error={errors.guardian_whatsapp}
                    />
                  )}
                </div>

                {fcVisible('whatsapp_number') && (
                  <PhoneNumberInput
                    label={fcLabel('whatsapp_number', t('biodata', 'whatsapp_number'))}
                    optional={!fcRequired('whatsapp_number')}
                    required={fcRequired('whatsapp_number')}
                    value={data.whatsapp_number ?? ''}
                    onChange={v => setData('whatsapp_number', v as never)}
                    error={errors.whatsapp_number}
                  />
                )}

                <SectionLabel>{t('biodata', 'contact_privacy_section')}</SectionLabel>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {fcVisible('contact_privacy') && (
                    <SearchableSelect
                      label={fcLabel('contact_privacy', t('biodata', 'contact_privacy'))}
                      value={data.contact_privacy ?? 'private'}
                      onChange={v => setData('contact_privacy', v as never)}
                      options={contactPrivacyOpts}
                      error={errors.contact_privacy}
                      required={fcRequired('contact_privacy')}
                    />
                  )}
                  {fcVisible('biodata_visibility') && (
                    <SearchableSelect
                      label={fcLabel('biodata_visibility', t('biodata', 'biodata_visibility'))}
                      value={data.biodata_visibility ?? ''}
                      onChange={v => setData('biodata_visibility', v as never)}
                      options={biodataVisibilityOpts}
                      error={errors.biodata_visibility}
                      required={fcRequired('biodata_visibility')}
                    />
                  )}
                </div>
                <p className="text-xs text-slate-400 leading-relaxed">{t('biodata', 'contact_privacy_note')}</p>

                {(fcVisible('allow_shortlist') || fcVisible('allow_contact_request')) && (
                  <div className="rounded-xl bg-slate-50 border border-slate-100 p-4 space-y-3">
                    {fcVisible('allow_shortlist') && (
                      <WizardToggle
                        value={data.allow_shortlist !== false}
                        label={fcLabel('allow_shortlist', t('biodata', 'allow_shortlist'))}
                        onChange={v => setData('allow_shortlist', v as never)}
                      />
                    )}
                    {fcVisible('allow_contact_request') && (
                      <WizardToggle
                        value={data.allow_contact_request !== false}
                        label={fcLabel('allow_contact_request', t('biodata', 'allow_contact_request'))}
                        onChange={v => setData('allow_contact_request', v as never)}
                      />
                    )}
                  </div>
                )}
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
                    required
                  />
                  <SearchableSelect
                    label={t('biodata', 'partner_age_max')}
                    value={data.partner_age_max !== '' && data.partner_age_max !== undefined ? String(data.partner_age_max) : ''}
                    onChange={v => setData('partner_age_max', (v ? parseInt(v, 10) : '') as never)}
                    options={ageOpts}
                    error={errors.partner_age_max}
                    placeholder="Max age"
                    required
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
                  {fcVisible('partner_marital_status') && (
                    <SearchableSelect
                      label={fcLabel('partner_marital_status', t('biodata', 'partner_marital_status'))}
                      value={data.partner_marital_status ?? ''}
                      onChange={v => setData('partner_marital_status', v as never)}
                      options={partnerMaritalOpts}
                      error={errors.partner_marital_status}
                      required={fcRequired('partner_marital_status')}
                    />
                  )}
                  <SearchableSelect
                    label={t('biodata', 'partner_complexion') || 'Partner Complexion'}
                    value={data.partner_complexion ?? ''}
                    onChange={v => setData('partner_complexion', v as never)}
                    options={complexionOpts}
                    error={errors.partner_complexion}
                  />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {fcVisible('partner_education') && (
                    <SearchableSelect
                      label={fcLabel('partner_education', t('biodata', 'partner_education'))}
                      value={data.partner_education ?? ''}
                      onChange={v => setData('partner_education', v as never)}
                      options={partnerEduOpts}
                      error={errors.partner_education}
                      required={fcRequired('partner_education')}
                    />
                  )}
                  <SearchableSelect
                    label={t('biodata', 'partner_family_type')}
                    value={data.partner_family_type ?? ''}
                    onChange={v => setData('partner_family_type', v as never)}
                    options={partnerFamilyOpts}
                    error={errors.partner_family_type}
                  />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'partner_economic_status')}
                    value={data.partner_economic_status ?? ''}
                    onChange={v => setData('partner_economic_status', v as never)}
                    options={financeOpts}
                    error={errors.partner_economic_status}
                  />
                  <Input
                    label={`${t('biodata', 'partner_deen_practice')} (${t('common', 'optional')})`}
                    value={data.partner_deen_practice ?? ''}
                    onChange={e => setData('partner_deen_practice', e.target.value as never)}
                    error={errors.partner_deen_practice}
                    placeholder={t('biodata', 'partner_deen_practice_ph')}
                  />
                </div>

                {(fcVisible('partner_division') || fcVisible('partner_district')) && (
                  <>
                    <SectionLabel>
                      {t('biodata', 'partner_location_required')}
                      {(fcRequired('partner_division') || fcRequired('partner_district')) && (
                        <span className="ml-0.5 text-red-500">*</span>
                      )}
                    </SectionLabel>
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
                      errors={{
                        division: errors.partner_division as string | undefined,
                        district: errors.partner_district as string | undefined,
                      }}
                      showUpazila={false}
                    />
                  </>
                )}

                {/* Additional preferred districts (multi-select chips) */}
                {fcVisible('partner_districts') && (
                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1.5">
                    {fcLabel('partner_districts', t('biodata', 'partner_districts'))} {!fcRequired('partner_districts') && <span className="text-slate-400 font-normal">({t('common', 'optional')})</span>}
                  </label>
                  <SearchableSelect
                    label=""
                    value=""
                    onChange={v => { if (v) addPartnerDistrict(v) }}
                    options={BD_CITY_OPTIONS}
                    allowFreeText
                    placeholder={t('biodata', 'partner_districts_ph')}
                  />
                  {partnerDistricts.length > 0 && (
                    <div className="flex flex-wrap gap-2 mt-2">
                      {partnerDistricts.map(d => (
                        <span key={d} className="inline-flex items-center gap-1 rounded-full bg-primary-50 border border-primary-200 px-3 py-1 text-xs font-medium text-primary-700">
                          {d}
                          <button type="button" onClick={() => removePartnerDistrict(d)}
                            className="text-primary-400 hover:text-red-600 transition-colors">
                            <X size={12} />
                          </button>
                        </span>
                      ))}
                    </div>
                  )}
                </div>
                )}

                <WizardTextarea
                  label={`${t('biodata', 'partner_special_qualities')} (${t('common', 'optional')})`}
                  value={(data.partner_special_qualities as string) ?? ''}
                  onChange={v => setData('partner_special_qualities', v as never)}
                  error={errors.partner_special_qualities}
                  placeholder={t('biodata', 'partner_special_qualities_ph')}
                  rows={3}
                  maxLength={1000}
                  assist={{ field: 'partner_special_qualities', mode: user.mode, gender: user.gender }}
                />

                <WizardTextarea
                  label={`${t('biodata', 'partner_deal_breakers')} (${t('common', 'optional')})`}
                  value={(data.partner_deal_breakers as string) ?? ''}
                  onChange={v => setData('partner_deal_breakers', v as never)}
                  error={errors.partner_deal_breakers}
                  placeholder={t('biodata', 'partner_deal_breakers_ph')}
                  rows={2}
                  maxLength={1000}
                  assist={{ field: 'partner_deal_breakers', mode: user.mode, gender: user.gender }}
                />

                {fcVisible('partner_expectations') && (
                  <WizardTextarea
                    label={fcLabel('partner_expectations', t('biodata', 'partner_expectations'))}
                    required={fcRequired('partner_expectations')}
                    value={(data.partner_expectations as string) ?? ''}
                    onChange={v => setData('partner_expectations', v as never)}
                    error={errors.partner_expectations}
                    placeholder="Describe the qualities you're looking for in a life partner..."
                    rows={5}
                    maxLength={1000}
                    assist={{ field: 'partner_expectations', mode: user.mode, gender: user.gender }}
                  />
                )}
              </>
            )}

            {/* ── Step 10: Profile Photo & Review ── */}
            {step === 10 && (
              <div className="space-y-5">
                {/* Header */}
                <div className="text-center pb-1">
                  <div className="mx-auto w-14 h-14 rounded-2xl bg-primary-50 border border-primary-100 flex items-center justify-center mb-3">
                    <Camera size={26} className="text-primary-500" />
                  </div>
                  <h3 className="text-lg font-bold text-slate-900">{t('biodata', 'step9_title')}</h3>
                  <p className="text-sm text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                    {t('biodata', 'step9_desc')}
                  </p>
                  <span className="inline-block mt-2 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                    {t('biodata', 'step9_optional_badge')}
                  </span>
                </div>

                {/* Existing photos grid */}
                {photos.length > 0 && (
                  <div>
                    <p className="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                      {t('biodata', 'photo_count', { count: String(photos.length), max: String(maxPhotos) })}
                    </p>
                    <div className="grid grid-cols-3 gap-2">
                      {photos.map((photo, idx) => (
                        <div key={idx} className={cn(
                          'relative rounded-xl overflow-hidden border-2 aspect-square bg-slate-100 group',
                          photo.is_primary ? 'border-primary-500 shadow-md' : 'border-transparent',
                        )}>
                          <img src={photoUrls[idx] ?? ''} alt="" className="w-full h-full object-cover" loading="lazy" />
                          {photo.is_primary && (
                            <div className="absolute top-1.5 left-1.5 flex items-center gap-1 rounded-full bg-primary-600 px-1.5 py-0.5 text-[10px] font-semibold text-white shadow">
                              <Star size={8} fill="currentColor" />
                              {t('biodata', 'photo_primary_badge')}
                            </div>
                          )}
                          <div className="absolute inset-0 flex flex-col items-center justify-end gap-1 p-2 opacity-0 group-hover:opacity-100 bg-black/0 group-hover:bg-black/40 transition-all">
                            {!photo.is_primary && (
                              <button type="button" onClick={() => handleSetPrimary(idx)}
                                className="w-full rounded-lg bg-white/90 py-1 text-[10px] font-semibold text-slate-800 hover:bg-white transition-colors">
                                {t('biodata', 'photo_set_primary')}
                              </button>
                            )}
                            <button type="button" onClick={() => handlePhotoDelete(idx)}
                              disabled={deletingPhotoIdx === idx}
                              className="w-full rounded-lg bg-red-500/90 py-1 text-[10px] font-semibold text-white hover:bg-red-600 transition-colors flex items-center justify-center gap-1 disabled:opacity-60">
                              <Trash2 size={10} />
                              {deletingPhotoIdx === idx ? '…' : t('biodata', 'photo_delete')}
                            </button>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                )}

                {/* Preview panel — shown after a file is selected */}
                {photoPreview && (
                  <div className="rounded-2xl border-2 border-primary-300 bg-primary-50 p-4 space-y-3">
                    <p className="text-sm font-semibold text-slate-700">{t('biodata', 'photo_preview_label')}</p>
                    <img src={photoPreview} alt="preview"
                      className="w-36 h-36 object-cover rounded-2xl border border-slate-200 mx-auto shadow" />
                    {photoError && <p className="text-xs text-red-600 text-center">{photoError}</p>}
                    <div className="flex gap-2">
                      <button type="button" onClick={clearPhotoPreview} disabled={photoUploading}
                        className="flex-1 flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors disabled:opacity-50">
                        <X size={14} />
                        {t('biodata', 'photo_cancel_preview')}
                      </button>
                      <button type="button" onClick={handlePhotoUpload} disabled={photoUploading}
                        className="flex-1 flex items-center justify-center gap-1.5 rounded-xl bg-primary-600 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition-colors disabled:opacity-60">
                        <Upload size={14} />
                        {photoUploading ? t('biodata', 'photo_uploading') : t('biodata', 'photo_confirm_upload')}
                      </button>
                    </div>
                  </div>
                )}

                {/* Drop zone — hidden when preview is showing or limit reached */}
                {!photoPreview && photos.length < maxPhotos && (
                  <div
                    onClick={() => photoInputRef.current?.click()}
                    className="rounded-2xl border-2 border-dashed border-slate-200 hover:border-primary-400 p-7 text-center cursor-pointer transition-colors group"
                  >
                    <input ref={photoInputRef} type="file"
                      accept="image/jpeg,image/png,image/webp"
                      className="hidden" onChange={handlePhotoSelect} />
                    <Upload size={22} className="mx-auto mb-2 text-slate-400 group-hover:text-primary-500 transition-colors" />
                    <p className="text-sm font-semibold text-slate-700 mb-0.5">
                      {t('biodata', 'photo_upload_btn')}
                    </p>
                    <p className="text-xs text-slate-400">{t('biodata', 'photo_file_hint')}</p>
                    {photoError && <p className="mt-2 text-xs text-red-600">{photoError}</p>}
                  </div>
                )}

                {/* Limit reached notice */}
                {!photoPreview && photos.length >= maxPhotos && (
                  <div className="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-center">
                    <p className="text-sm text-slate-500">
                      {t('biodata', 'photo_limit_reached', { max: String(maxPhotos) })}
                    </p>
                  </div>
                )}

                <p className="text-xs text-slate-400 text-center leading-relaxed">
                  {t('biodata', 'step9_note')}
                </p>

                {/* Declaration / commitment — all three required to submit */}
                <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-3">
                  <p className="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    {t('biodata', 'declaration_section')}
                  </p>
                  <div>
                    <label className="flex items-start gap-3 cursor-pointer select-none">
                      <input
                        type="checkbox"
                        checked={!!data.guardian_knows_biodata}
                        onChange={e => setData('guardian_knows_biodata', e.target.checked as never)}
                        className="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                      />
                      <span className="text-sm text-slate-700">{t('biodata', 'declare_guardian_knows')}</span>
                    </label>
                    {errors.guardian_knows_biodata && (
                      <p className="mt-1 text-xs text-red-600">{errors.guardian_knows_biodata}</p>
                    )}
                  </div>
                  <div>
                    <label className="flex items-start gap-3 cursor-pointer select-none">
                      <input
                        type="checkbox"
                        checked={!!data.info_truthful_confirmed}
                        onChange={e => setData('info_truthful_confirmed', e.target.checked as never)}
                        className="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                      />
                      <span className="text-sm text-slate-700">{t('biodata', 'declare_info_truthful')}</span>
                    </label>
                    {errors.info_truthful_confirmed && (
                      <p className="mt-1 text-xs text-red-600">{errors.info_truthful_confirmed}</p>
                    )}
                  </div>
                  <div>
                    <label className="flex items-start gap-3 cursor-pointer select-none">
                      <input
                        type="checkbox"
                        checked={!!data.accept_liability_terms}
                        onChange={e => setData('accept_liability_terms', e.target.checked as never)}
                        className="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                      />
                      <span className="text-sm text-slate-700">{t('biodata', 'declare_accept_terms')}</span>
                    </label>
                    {errors.accept_liability_terms && (
                      <p className="mt-1 text-xs text-red-600">{errors.accept_liability_terms}</p>
                    )}
                  </div>
                </div>
              </div>
            )}

            {/* ── Admin-defined custom fields for this step (Phase E3) ── */}
            {customFieldsForStep.length > 0 && (
              <div className="space-y-4 pt-2">
                <div className="flex items-center gap-2">
                  <div className="h-px flex-1 bg-slate-100" />
                  <span className="text-xs font-medium text-slate-400">{t('biodata', 'custom_fields_heading')}</span>
                  <div className="h-px flex-1 bg-slate-100" />
                </div>
                {customFieldsForStep.map(def => (
                  <CustomFieldInput
                    key={def.key}
                    def={def}
                    value={cfValue(def.key)}
                    onChange={v => setCustomField(def.key, v)}
                  />
                ))}
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
              <Button type="submit" className="flex-1" size="lg" isLoading={processing}>
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

/* ── Admin-defined custom field renderer (Phase E3) ───────────────────────── */

function CustomFieldInput({ def, value, onChange }: {
  def: CustomFieldDef
  value: unknown
  onChange: (v: unknown) => void
}) {
  const { t } = useTranslation()
  const base = 'block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500'
  const val = value ?? ''

  const wrap = (input: React.ReactNode) => (
    <div className="flex flex-col gap-1.5">
      <label className="text-sm font-medium text-slate-700">
        {def.label}{def.required && <span className="ml-0.5 text-red-500">*</span>}
      </label>
      {input}
      {def.helper && <p className="text-xs text-slate-500">{def.helper}</p>}
    </div>
  )

  switch (def.input_type) {
    case 'textarea':
      return wrap(<textarea className={base} rows={3} placeholder={def.placeholder ?? ''}
        value={String(val)} onChange={e => onChange(e.target.value)} />)

    case 'select':
    case 'radio':
      return wrap(
        <select className={base} value={String(val)} onChange={e => onChange(e.target.value)}>
          <option value="">{def.placeholder ?? '—'}</option>
          {def.options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
        </select>,
      )

    case 'multi_select': {
      const arr = Array.isArray(value) ? (value as string[]) : []
      const toggle = (v: string) => onChange(arr.includes(v) ? arr.filter(x => x !== v) : [...arr, v])
      return wrap(
        <div className="flex flex-wrap gap-2">
          {def.options.map(o => (
            <button type="button" key={o.value} onClick={() => toggle(o.value)}
              className={cn('rounded-full border px-3 py-1.5 text-sm transition-colors',
                arr.includes(o.value) ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-slate-300 text-slate-600 hover:bg-slate-50')}>
              {o.label}
            </button>
          ))}
        </div>,
      )
    }

    case 'yes_no':
      return wrap(
        <select className={base}
          value={value === true ? '1' : value === false ? '0' : ''}
          onChange={e => onChange(e.target.value === '' ? null : e.target.value === '1')}>
          <option value="">—</option>
          <option value="1">{t('common', 'yes')}</option>
          <option value="0">{t('common', 'no')}</option>
        </select>,
      )

    case 'checkbox':
      return (
        <label className="flex items-center gap-2 text-sm text-slate-700">
          <input type="checkbox" checked={!!value} onChange={e => onChange(e.target.checked)}
            className="h-4 w-4 rounded border-slate-300 text-primary-600" />
          {def.label}{def.required && <span className="text-red-500">*</span>}
        </label>
      )

    case 'date':
      return wrap(<input type="date" className={base} value={String(val)} onChange={e => onChange(e.target.value)} />)

    case 'number':
      return wrap(<input type="number" className={base} placeholder={def.placeholder ?? ''}
        value={String(val)} onChange={e => onChange(e.target.value === '' ? '' : Number(e.target.value))} />)

    case 'email':
      return wrap(<input type="email" className={base} placeholder={def.placeholder ?? ''}
        value={String(val)} onChange={e => onChange(e.target.value)} />)

    case 'phone':
      return wrap(<input type="tel" className={base} placeholder={def.placeholder ?? ''}
        value={String(val)} onChange={e => onChange(e.target.value)} />)

    default:
      return wrap(<input type="text" className={base} placeholder={def.placeholder ?? ''}
        value={String(val)} onChange={e => onChange(e.target.value)} />)
  }
}
