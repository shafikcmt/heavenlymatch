/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import { useState, useRef } from 'react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { SearchableSelect } from '@/components/ui/SearchableSelect'
import { HeightSelect } from '@/components/ui/HeightSelect'
import { WeightSelect } from '@/components/ui/WeightSelect'
import { DateOfBirthSelect } from '@/components/ui/DateOfBirthSelect'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import {
  CheckCircle, Save, Plus, Trash2, Camera, Upload, X, Star,
  User, MapPin, Moon, GraduationCap, HeartPulse, Users, HeartHandshake,
  Search, Phone, ClipboardCheck,
} from 'lucide-react'
import { BangladeshAddressPicker } from '@/components/forms/BangladeshAddressPicker'

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
  education_details?: EducationRecord[]
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
  brothers_details?: SiblingDetail[]
  sisters_details?: SiblingDetail[]
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
  has_children?: boolean
  children_count?: number | ''
  children_live_with?: string
  children_notes?: string
  residence_after_marriage?: string
  post_marriage_plan?: string
  guardian_mobile?: string
  guardian_relationship?: string
  guardian_email?: string
  whatsapp_number?: string
  contact_privacy?: string
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
  partner_family_type?: string
  partner_expectations?: string
}

interface PhotoItem {
  path: string
  is_primary: boolean
  uploaded_at: string
}

interface Props {
  step: number
  steps: Record<number, string>
  biodata: BiodataData & { completeness_score?: number }
  user: { name: string; gender: string; mode: string }
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

function WizardTextarea({ value, label, placeholder = '', rows = 4, maxLength, onChange, error }: {
  value: string; label: string; placeholder?: string
  rows?: number; maxLength?: number; onChange: (v: string) => void; error?: string
}) {
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

// ─── Education record card (module-level = stable identity) ──────────────────

const EDU_LEVEL_OPTS = [
  { value: 'SSC / O-Level', label: 'SSC / O-Level' },
  { value: 'HSC / A-Level', label: 'HSC / A-Level' },
  { value: 'Diploma', label: 'Diploma' },
  { value: 'Bachelor / Graduation', label: 'Bachelor / Graduation' },
  { value: 'Master / Post-Graduation', label: 'Master / Post-Graduation' },
  { value: 'PhD / Doctoral', label: 'PhD / Doctoral' },
  { value: 'Hafez', label: 'Hafez' },
  { value: 'Alim', label: 'Alim' },
  { value: 'Fazil', label: 'Fazil' },
  { value: 'Kamil', label: 'Kamil' },
  { value: 'Other', label: 'Other' },
]
const EDU_TYPE_OPTS = [
  { value: 'general', label: 'General / National' },
  { value: 'islamic', label: 'Islamic / Madrasa' },
  { value: 'both', label: 'Both' },
]
const RESULT_TYPE_OPTS = [
  { value: 'GPA', label: 'GPA' },
  { value: 'CGPA', label: 'CGPA' },
  { value: 'Division', label: 'Division' },
  { value: 'Class', label: 'Class' },
  { value: 'Pass', label: 'Pass' },
  { value: 'Other', label: 'Other' },
]

function EducationRecordCard({ record, index, onChange, onRemove }: {
  record: EducationRecord
  index: number
  onChange: (r: EducationRecord) => void
  onRemove: () => void
}) {
  const upd = (k: keyof EducationRecord, v: string | boolean) => onChange({ ...record, [k]: v })
  return (
    <div className="rounded-xl border border-slate-200 bg-white p-4 space-y-3 shadow-sm">
      <div className="flex items-center justify-between">
        <p className="text-xs font-semibold text-primary-600 uppercase tracking-wide">
          {index + 1}. {record.level || 'New Education Record'}
        </p>
        <button type="button" onClick={onRemove}
          className="flex items-center gap-1 text-xs text-red-500 hover:text-red-700 font-medium transition-colors">
          <Trash2 size={12} /> Remove
        </button>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <SearchableSelect label="Degree / Level" value={record.level ?? ''}
          onChange={v => upd('level', v)} options={EDU_LEVEL_OPTS} allowFreeText placeholder="e.g. SSC, Bachelor" />
        <SearchableSelect label="Education Type" value={record.edu_type ?? ''}
          onChange={v => upd('edu_type', v)} options={EDU_TYPE_OPTS} placeholder="General / Islamic" />
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <Input label="Subject / Group / Dept" value={record.subject ?? ''}
          onChange={e => upd('subject', e.target.value)} placeholder="e.g. Science, CSE, Arabic" />
        <Input label="Institute Name" value={record.institute ?? ''}
          onChange={e => upd('institute', e.target.value)} placeholder="e.g. Dhaka University" />
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <Input label="Board / University" value={record.board_university ?? ''}
          onChange={e => upd('board_university', e.target.value)} placeholder="e.g. Dhaka Board" />
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

// ─── Main Component ───────────────────────────────────────────────────────────

export default function BiodataWizard({ step, steps, biodata, user, photos = [], photoUrls = [], maxPhotos = 6 }: Props) {
  const totalSteps = Object.keys(steps).length
  const { t } = useTranslation()
  const completenessScore = biodata.completeness_score ?? 0

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
    contact_privacy: biodata.contact_privacy ?? 'private',
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

  // ── Education record helpers ─────────────────────────────────────────────────
  const eduRecords: EducationRecord[] = toEduList(data.education_details)

  const addEdu = () => {
    setData('education_details', [...eduRecords, normaliseEdu({})] as never)
  }
  const updateEdu = (idx: number, rec: EducationRecord) => {
    const next = [...eduRecords]
    next[idx] = rec
    setData('education_details', next as never)
  }
  const removeEdu = (idx: number) => {
    setData('education_details', eduRecords.filter((_, i) => i !== idx) as never)
  }

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
  const isBangladesh = !data.residing_country || data.residing_country === 'Bangladesh'
  const cityOptions = isBangladesh ? BD_CITY_OPTIONS : []

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

            {/* ── Step 1: General Info ── */}
            {step === 1 && (
              <>
                <div className="rounded-xl bg-primary-50 border border-primary-100 px-4 py-3 text-sm text-primary-700">
                  Welcome! Let's start with your basic information. Fill as much as you can — a complete profile gets more attention.
                </div>

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
                  required
                />

                <DateOfBirthSelect
                  label={t('biodata', 'birth_date')}
                  value={(data.birth_date as string) ?? ''}
                  onChange={v => setData('birth_date', v as never)}
                  error={errors.birth_date}
                  required
                />

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
                    required
                    placeholder="e.g. Bangladesh, UK..."
                  />
                  <SearchableSelect
                    label={t('biodata', 'residing_city')}
                    value={data.residing_city ?? ''}
                    onChange={v => setData('residing_city', v as never)}
                    options={cityOptions}
                    error={errors.residing_city}
                    allowFreeText
                    required
                    placeholder={isBangladesh ? 'e.g. Dhaka, Sylhet...' : 'Enter your city'}
                    emptyText={isBangladesh ? 'Type to search or add city' : 'Type your city name'}
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
                  <SearchableSelect
                    label={t('biodata', 'religion')}
                    value={data.religion ?? ''}
                    onChange={v => setData('religion', v as never)}
                    options={RELIGION_OPTIONS}
                    error={errors.religion}
                    allowFreeText
                    required
                    placeholder="Select religion..."
                  />
                  {isIslam && (
                    <SearchableSelect
                      label={t('biodata', 'sect')}
                      value={data.sect ?? ''}
                      onChange={v => setData('sect', v as never)}
                      options={SECT_OPTIONS}
                      error={errors.sect}
                      allowFreeText
                      placeholder="e.g. Hanafi, Ahle Hadith"
                    />
                  )}
                </div>

                {isIslam && (
                  <>
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
                        required
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
                {/* Summary fields */}
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
                    required
                  />
                </div>

                {/* Multiple education records */}
                <div className="space-y-3">
                  <SectionLabel>Education Records</SectionLabel>
                  <p className="text-xs text-slate-400">
                    Add each degree/certificate separately for a complete picture.
                  </p>

                  {eduRecords.length === 0 && (
                    <div className="rounded-xl border border-dashed border-slate-300 bg-slate-50 py-8 text-center">
                      <p className="text-sm text-slate-400">No education records yet.</p>
                    </div>
                  )}

                  <div className="space-y-3">
                    {eduRecords.map((rec, idx) => (
                      <EducationRecordCard
                        key={idx}
                        record={rec}
                        index={idx}
                        onChange={r => updateEdu(idx, r)}
                        onRemove={() => removeEdu(idx)}
                      />
                    ))}
                  </div>

                  <button
                    type="button"
                    onClick={addEdu}
                    className="flex items-center gap-2 w-full justify-center rounded-xl border-2 border-dashed border-primary-300 bg-primary-50 px-4 py-3 text-sm font-medium text-primary-600 hover:bg-primary-100 hover:border-primary-400 transition-colors"
                  >
                    <Plus size={16} /> Add Education Record
                  </button>
                </div>

                {/* Career */}
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
                    required
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
                  <SearchableSelect
                    label={t('biodata', 'father_profession')}
                    value={data.father_profession ?? ''}
                    onChange={v => setData('father_profession', v as never)}
                    options={PROFESSION_OPTIONS}
                    error={errors.father_profession}
                    allowFreeText
                    required
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
                    required
                    placeholder="e.g. Homemaker, Teacher"
                  />
                </div>
                <WizardToggle
                  value={!!data.mother_alive}
                  label={t('biodata', 'mother_alive')}
                  onChange={v => setData('mother_alive', v as never)}
                />

                {/* Siblings */}
                <SectionLabel>Siblings</SectionLabel>
                <div className="grid grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'brothers')}
                    value={data.brothers !== '' && data.brothers !== undefined ? String(data.brothers) : ''}
                    onChange={handleBrotherCount}
                    options={COUNT_OPTIONS}
                    error={errors.brothers}
                    placeholder="0"
                  />
                  <SearchableSelect
                    label={t('biodata', 'sisters')}
                    value={data.sisters !== '' && data.sisters !== undefined ? String(data.sisters) : ''}
                    onChange={handleSisterCount}
                    options={COUNT_OPTIONS}
                    error={errors.sisters}
                    placeholder="0"
                  />
                </div>

                {/* Brother details */}
                {broDetails.length > 0 && (
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

                {/* Sister details */}
                {sisDetails.length > 0 && (
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
                  <SearchableSelect
                    label={t('biodata', 'family_type')}
                    value={data.family_type ?? ''}
                    onChange={v => setData('family_type', v as never)}
                    options={familyTypeOpts}
                    error={errors.family_type}
                    required
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

            {/* ── Step 5: Physical & Lifestyle ── */}
            {step === 5 && (
              <>
                <SectionLabel>{t('biodata', 'section_physical')}</SectionLabel>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <HeightSelect
                    label={t('biodata', 'height')}
                    value={data.height_cm ?? ''}
                    onChange={v => setData('height_cm', v as never)}
                    error={errors.height_cm}
                    required
                  />
                  <WeightSelect
                    label={t('biodata', 'weight')}
                    value={data.weight_kg ?? ''}
                    onChange={v => setData('weight_kg', v as never)}
                    error={errors.weight_kg}
                    required
                  />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'complexion')}
                    value={data.complexion ?? ''}
                    onChange={v => setData('complexion', v as never)}
                    options={complexionOpts}
                    error={errors.complexion}
                    required
                  />
                  <SearchableSelect
                    label={t('biodata', 'blood_group')}
                    value={data.blood_group ?? ''}
                    onChange={v => setData('blood_group', v as never)}
                    options={bloodGroupOpts}
                    error={errors.blood_group}
                  />
                </div>

                <SectionLabel>{t('biodata', 'section_lifestyle')}</SectionLabel>
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
                    <WizardToggle value={!!data.wife_in_veil} label={t('biodata', 'wife_in_veil')}
                      onChange={v => setData('wife_in_veil', v as never)} />
                    <WizardToggle value={!!data.wife_study_allowed} label={t('biodata', 'wife_study_allowed')}
                      onChange={v => setData('wife_study_allowed', v as never)} />
                    <WizardToggle value={!!data.wife_job_allowed} label={t('biodata', 'wife_job_allowed')}
                      onChange={v => setData('wife_job_allowed', v as never)} />
                    <WizardToggle value={!!data.polygamy_open} label={t('biodata', 'polygamy_open')}
                      onChange={v => setData('polygamy_open', v as never)} />
                  </div>
                )}

                <WizardToggle value={!!data.guardian_agree} label={t('biodata', 'guardian_agree')}
                  onChange={v => setData('guardian_agree', v as never)} />

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <SearchableSelect
                    label={t('biodata', 'residence_after_marriage')}
                    value={data.residence_after_marriage ?? ''}
                    onChange={v => setData('residence_after_marriage', v as never)}
                    options={RESIDENCE_OPTIONS}
                    error={errors.residence_after_marriage}
                    allowFreeText
                    required
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
                  <Input
                    label={`${t('biodata', 'guardian_mobile')} (${t('common', 'optional')})`}
                    value={data.guardian_mobile ?? ''}
                    onChange={e => setData('guardian_mobile', e.target.value as never)}
                    error={errors.guardian_mobile}
                    placeholder="+88017XXXXXXXX"
                  />
                  <SearchableSelect
                    label={`${t('biodata', 'guardian_relationship')} (${t('common', 'optional')})`}
                    value={data.guardian_relationship ?? ''}
                    onChange={v => setData('guardian_relationship', v as never)}
                    options={GUARDIAN_REL_OPTIONS}
                    error={errors.guardian_relationship}
                    allowFreeText
                    placeholder="Father / Brother / Uncle"
                  />
                </div>
                <Input
                  label={`${t('biodata', 'guardian_email')} (${t('common', 'optional')})`}
                  type="email"
                  value={data.guardian_email ?? ''}
                  onChange={e => setData('guardian_email', e.target.value as never)}
                  error={errors.guardian_email}
                  placeholder="guardian@example.com"
                />

                <Input
                  label={`${t('biodata', 'whatsapp_number')} (${t('common', 'optional')})`}
                  value={data.whatsapp_number ?? ''}
                  onChange={e => setData('whatsapp_number', e.target.value as never)}
                  error={errors.whatsapp_number}
                  placeholder="01XXXXXXXXX"
                  helperText={t('biodata', 'whatsapp_hint')}
                />

                <SectionLabel>{t('biodata', 'contact_privacy_section')}</SectionLabel>
                <SearchableSelect
                  label={t('biodata', 'contact_privacy')}
                  value={data.contact_privacy ?? 'private'}
                  onChange={v => setData('contact_privacy', v as never)}
                  options={contactPrivacyOpts}
                  error={errors.contact_privacy}
                  required
                />
                <p className="text-xs text-slate-400 leading-relaxed">{t('biodata', 'contact_privacy_note')}</p>
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
                    required
                  />
                  <SearchableSelect
                    label={t('biodata', 'partner_family_type')}
                    value={data.partner_family_type ?? ''}
                    onChange={v => setData('partner_family_type', v as never)}
                    options={partnerFamilyOpts}
                    error={errors.partner_family_type}
                  />
                </div>

                <SectionLabel>{t('biodata', 'partner_location_required')}</SectionLabel>
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

                {/* Final confirmation */}
                <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                  <label className="flex items-start gap-3 cursor-pointer select-none">
                    <input
                      type="checkbox"
                      checked={!!data.confirm_correct}
                      onChange={e => setData('confirm_correct', e.target.checked as never)}
                      className="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                    />
                    <span className="text-sm text-slate-700">{t('biodata', 'confirm_correct')}</span>
                  </label>
                  {errors.confirm_correct && (
                    <p className="mt-1.5 text-xs text-red-600">{errors.confirm_correct}</p>
                  )}
                </div>
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
