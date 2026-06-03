/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link, router, useForm } from '@inertiajs/react'
import { useState } from 'react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { ArrowLeft, CheckCircle, XCircle, AlertCircle, EyeOff, Eye } from 'lucide-react'
import { cn } from '@/lib/utils'

interface Registration {
  registration_id: string
  name: string
  email: string
  gender: string
  platform_mode: string
  photo_visibility: string
  identity_verification_status: string
  account_status: string
  created_at: string
}

interface BiodataDetail {
  id: number
  registration_id: string
  status: string
  admin_note: string | null
  completeness_score: number | null
  approved_at: string | null
  rejected_at: string | null
  updated_at: string
  // General
  marital_status: string | null
  birth_date: string | null
  height_cm: number | null
  weight_kg: number | null
  complexion: string | null
  blood_group: string | null
  about_me: string | null
  // Location
  division: string | null
  district: string | null
  upazila: string | null
  residing_country: string | null
  // Religion
  religion: string | null
  sect: string | null
  is_practicing: boolean | null
  prayers_info: string | null
  hijab_info: string | null
  beard_info: string | null
  wali_approval: boolean | null
  is_islamically_educated: boolean | null
  // Education
  highest_qualification: string | null
  occupation: string | null
  occupation_category: string | null
  monthly_income: number | null
  // Family
  father_alive: boolean | null
  father_profession: string | null
  mother_alive: boolean | null
  brothers: number | null
  sisters: number | null
  family_type: string | null
  family_financial_status: string | null
  // Health
  health_status: string | null
  diet: string | null
  // Partner prefs
  partner_age_min: number | null
  partner_age_max: number | null
  partner_sect: string | null
  partner_education: string | null
  partner_expectations: string | null
  // Contact
  guardian_mobile: string | null
  guardian_relationship: string | null
  whatsapp_number: string | null
  contact_privacy: string | null
  // Photos
  photos: Array<{ path: string; is_primary: boolean }> | null
  registration: Registration | null
}

interface Props {
  biodata: BiodataDetail
}

function RejectModal({ biodataId, onClose }: { biodataId: number; onClose: () => void }) {
  const { t } = useTranslation()
  const { data, setData, post, processing, errors } = useForm({ note: '' })

  function submit(e: React.FormEvent) {
    e.preventDefault()
    post(route('admin.biodatas.reject', biodataId), { onSuccess: onClose })
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
      <div className="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 className="font-bold text-slate-900 mb-1">{t('admin', 'biodata_reject')}</h3>
        <p className="text-sm text-slate-500 mb-4">{t('admin', 'rejection_note')}</p>
        <form onSubmit={submit}>
          <textarea
            value={data.note}
            onChange={e => setData('note', e.target.value)}
            rows={3}
            placeholder={t('admin', 'rejection_placeholder')}
            className={cn(
              'w-full rounded-xl border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none',
              errors.note ? 'border-red-400' : 'border-slate-200',
            )}
          />
          {errors.note && <p className="mt-1 text-xs text-red-600">{errors.note}</p>}
          <div className="flex gap-3 mt-4">
            <Button type="button" variant="outline" size="sm" onClick={onClose} className="flex-1">
              {t('common', 'cancel')}
            </Button>
            <Button
              type="submit"
              variant="destructive"
              size="sm"
              isLoading={processing}
              disabled={data.note.trim().length < 5}
              className="flex-1"
            >
              {t('admin', 'biodata_reject')}
            </Button>
          </div>
        </form>
      </div>
    </div>
  )
}

function Field({ label, value }: { label: string; value: React.ReactNode }) {
  return (
    <div>
      <dt className="text-xs font-medium text-slate-500">{label}</dt>
      <dd className="mt-0.5 text-sm text-slate-900">{value ?? <span className="text-slate-300">—</span>}</dd>
    </div>
  )
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="rounded-2xl border border-slate-200 bg-white p-5 space-y-4">
      <h3 className="text-sm font-semibold text-slate-900 border-b border-slate-100 pb-2">{title}</h3>
      <dl className="grid grid-cols-2 sm:grid-cols-3 gap-4">{children}</dl>
    </div>
  )
}

export default function BiodataShow({ biodata }: Props) {
  const { t } = useTranslation()
  const [rejectOpen, setRejectOpen] = useState(false)
  const reg = biodata.registration

  const age = biodata.birth_date
    ? Math.floor((Date.now() - new Date(biodata.birth_date).getTime()) / (365.25 * 24 * 3600 * 1000))
    : null

  function approve() {
    if (!confirm(t('admin', 'biodata_approve') + '?')) return
    router.post(route('admin.biodatas.approve', biodata.id))
  }

  function hide() {
    if (!confirm(t('admin', 'biodata_confirm_hide_title'))) return
    router.post(route('admin.biodatas.hide', biodata.id), {}, { preserveScroll: true })
  }

  function unhide() {
    if (!confirm(t('admin', 'biodata_confirm_unhide_title'))) return
    router.post(route('admin.biodatas.unhide', biodata.id), {}, { preserveScroll: true })
  }

  const statusColor =
    biodata.status === 'approved' ? 'bg-emerald-100 text-emerald-700' :
    biodata.status === 'pending'  ? 'bg-amber-100 text-amber-700' :
    biodata.status === 'rejected' ? 'bg-red-100 text-red-700' :
    'bg-slate-100 text-slate-500'

  const yesNo = (v: boolean | null) =>
    v === true ? t('common', 'yes') : v === false ? t('common', 'no') : null

  return (
    <AdminLayout>
      <Head title={t('admin', 'biodatas_title')} />

      {rejectOpen && (
        <RejectModal biodataId={biodata.id} onClose={() => setRejectOpen(false)} />
      )}

      <div className="max-w-4xl space-y-5">

        {/* Back link */}
        <Link
          href={route('admin.biodatas.index')}
          className="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-900"
        >
          <ArrowLeft size={14} />
          {t('admin', 'biodata_back_list')}
        </Link>

        {/* Header card */}
        <div className="rounded-2xl border border-slate-200 bg-white p-5">
          <div className="flex flex-wrap items-start gap-4">
            <div className="flex-1 min-w-0">
              <p className="text-lg font-bold text-slate-900">{reg?.name ?? biodata.registration_id}</p>
              <p className="text-sm text-slate-500">{reg?.email}</p>
              <div className="flex flex-wrap gap-2 mt-2 text-xs text-slate-400">
                <span>{t('admin', 'biodata_member_id')}: <strong className="text-slate-700">{reg?.registration_id}</strong></span>
                <span>·</span>
                <span className="capitalize">{reg?.gender}</span>
                <span>·</span>
                <span className="capitalize">{reg?.platform_mode}</span>
                {biodata.completeness_score != null && (
                  <>
                    <span>·</span>
                    <span>{t('admin', 'biodata_completeness')}: <strong className="text-slate-700">{biodata.completeness_score}%</strong></span>
                  </>
                )}
              </div>
            </div>
            <div className="shrink-0 text-right space-y-2">
              <span className={cn('inline-block rounded-full px-3 py-1 text-xs font-semibold capitalize', statusColor)}>
                {biodata.status}
              </span>
              {biodata.approved_at && (
                <p className="text-xs text-slate-400">{t('admin', 'approved_at')}: {new Date(biodata.approved_at).toLocaleDateString()}</p>
              )}
              {biodata.rejected_at && (
                <p className="text-xs text-slate-400">{t('admin', 'rejected_by')}: {new Date(biodata.rejected_at).toLocaleDateString()}</p>
              )}
            </div>
          </div>

          {/* Rejection note */}
          {biodata.admin_note && (
            <div className="mt-4 flex items-start gap-2 rounded-xl border border-red-100 bg-red-50 px-3 py-2.5">
              <AlertCircle size={15} className="text-red-500 mt-0.5 shrink-0" />
              <p className="text-xs text-red-700"><strong>{t('admin', 'biodata_note_label')}</strong> {biodata.admin_note}</p>
            </div>
          )}

          {/* Actions */}
          <div className="flex gap-3 mt-4 flex-wrap">
            {(biodata.status === 'pending' || biodata.status === 'rejected') && (
              <Button
                size="sm"
                onClick={approve}
                className="bg-emerald-600 hover:bg-emerald-700 gap-1.5"
              >
                <CheckCircle size={14} />
                {t('admin', 'biodata_approve')}
              </Button>
            )}
            {(biodata.status === 'pending' || biodata.status === 'approved') && (
              <Button
                size="sm"
                variant="destructive"
                onClick={() => setRejectOpen(true)}
                className="gap-1.5"
              >
                <XCircle size={14} />
                {t('admin', 'biodata_reject')}
              </Button>
            )}
            {biodata.status === 'approved' && (
              <Button size="sm" variant="outline" onClick={hide} className="gap-1.5">
                <EyeOff size={14} />
                {t('admin', 'biodata_hide')}
              </Button>
            )}
            {biodata.status === 'hidden' && (
              <Button size="sm" variant="outline" onClick={unhide} className="gap-1.5">
                <Eye size={14} />
                {t('admin', 'biodata_unhide')}
              </Button>
            )}
          </div>
        </div>

        {/* Photos indicator */}
        <div className="rounded-2xl border border-slate-200 bg-white px-5 py-3">
          <p className="text-sm text-slate-700">
            {biodata.photos && biodata.photos.length > 0
              ? t('admin', 'biodata_photos_count', { count: biodata.photos.length })
              : t('admin', 'biodata_no_photos')}
          </p>
        </div>

        {/* General Information */}
        <Section title={t('biodata', 'section_general')}>
          <Field label={t('dashboard', 'profile_label_age')}        value={age ? `${age}` : null} />
          <Field label={t('dashboard', 'profile_label_marital')}    value={biodata.marital_status} />
          <Field label={t('dashboard', 'profile_label_height')}     value={biodata.height_cm ? `${biodata.height_cm} cm` : null} />
          <Field label={t('dashboard', 'profile_label_weight')}     value={biodata.weight_kg ? `${biodata.weight_kg} kg` : null} />
          <Field label={t('dashboard', 'profile_label_complexion')} value={biodata.complexion} />
          <Field label={t('dashboard', 'profile_label_blood')}      value={biodata.blood_group} />
          {biodata.about_me && (
            <div className="col-span-full">
              <dt className="text-xs font-medium text-slate-500">{t('admin', 'biodata_label_about')}</dt>
              <dd className="mt-0.5 text-sm text-slate-900 whitespace-pre-line">{biodata.about_me}</dd>
            </div>
          )}
        </Section>

        {/* Location */}
        <Section title={t('biodata', 'section_location')}>
          <Field label={t('admin', 'biodata_label_district')}  value={biodata.district} />
          <Field label={t('admin', 'biodata_label_division')}  value={biodata.division} />
          <Field label={t('admin', 'biodata_label_upazila')}   value={biodata.upazila} />
          <Field label={t('admin', 'biodata_label_country')}   value={biodata.residing_country} />
        </Section>

        {/* Religion */}
        <Section title={t('biodata', 'section_religion')}>
          <Field label={t('dashboard', 'profile_label_religion')}  value={biodata.religion} />
          <Field label={t('dashboard', 'profile_label_sect')}      value={biodata.sect} />
          <Field label={t('dashboard', 'profile_label_prayers')}   value={biodata.prayers_info} />
          <Field label={t('dashboard', 'profile_label_wali')}      value={yesNo(biodata.wali_approval)} />
          <Field label={t('dashboard', 'profile_label_islam_edu')} value={yesNo(biodata.is_islamically_educated)} />
          {reg?.gender === 'female' && <Field label={t('admin', 'biodata_label_hijab')} value={biodata.hijab_info} />}
          {reg?.gender === 'male'   && <Field label={t('admin', 'biodata_label_beard')} value={biodata.beard_info} />}
        </Section>

        {/* Education */}
        <Section title={t('biodata', 'section_education')}>
          <Field label={t('dashboard', 'profile_label_qual')}       value={biodata.highest_qualification} />
          <Field label={t('dashboard', 'profile_label_occupation')} value={biodata.occupation} />
          <Field label={t('dashboard', 'profile_label_income')}     value={biodata.monthly_income ? `${biodata.monthly_income.toLocaleString()} BDT` : null} />
        </Section>

        {/* Family */}
        <Section title={t('biodata', 'section_family')}>
          <Field label={t('admin', 'biodata_label_father')}      value={biodata.father_alive != null ? `${yesNo(biodata.father_alive)}${biodata.father_profession ? ` — ${biodata.father_profession}` : ''}` : null} />
          <Field label={t('admin', 'biodata_label_mother')}      value={yesNo(biodata.mother_alive)} />
          <Field label={t('dashboard', 'profile_label_brothers')} value={biodata.brothers?.toString()} />
          <Field label={t('dashboard', 'profile_label_sisters')}  value={biodata.sisters?.toString()} />
          <Field label={t('dashboard', 'profile_label_family_type')} value={biodata.family_type} />
          <Field label={t('admin', 'biodata_label_family_status')} value={biodata.family_financial_status} />
        </Section>

        {/* Health & Lifestyle */}
        <Section title={t('biodata', 'section_lifestyle')}>
          <Field label={t('dashboard', 'profile_label_health')} value={biodata.health_status} />
          <Field label={t('admin', 'biodata_label_diet')}       value={biodata.diet} />
        </Section>

        {/* Partner Preferences */}
        <Section title={t('biodata', 'section_partner')}>
          <Field label={t('dashboard', 'profile_label_age_range')} value={
            biodata.partner_age_min || biodata.partner_age_max
              ? `${biodata.partner_age_min ?? '?'} – ${biodata.partner_age_max ?? '?'}`
              : null
          } />
          <Field label={t('dashboard', 'profile_label_sect')}      value={biodata.partner_sect} />
          <Field label={t('dashboard', 'profile_label_qual')}      value={biodata.partner_education} />
          {biodata.partner_expectations && (
            <div className="col-span-full">
              <dt className="text-xs font-medium text-slate-500">{t('admin', 'biodata_label_expectations')}</dt>
              <dd className="mt-0.5 text-sm text-slate-900 whitespace-pre-line">{biodata.partner_expectations}</dd>
            </div>
          )}
        </Section>

        {/* Contact & Guardian (admin sees full details) */}
        <Section title={t('admin', 'biodata_section_contact')}>
          <Field label={t('admin', 'biodata_label_guardian_rel')}  value={biodata.guardian_relationship} />
          <Field label={t('admin', 'biodata_label_guardian_mobile')} value={biodata.guardian_mobile} />
          <Field label={t('biodata', 'whatsapp_number')} value={biodata.whatsapp_number} />
          <Field label={t('biodata', 'contact_privacy')} value={biodata.contact_privacy} />
        </Section>

        {/* Bottom action bar */}
        <div className="flex gap-3 flex-wrap pb-6">
          <Link href={route('admin.biodatas.index')}>
            <Button variant="outline" size="sm">{t('admin', 'biodata_back_list')}</Button>
          </Link>
          {(biodata.status === 'pending' || biodata.status === 'rejected') && (
            <Button size="sm" onClick={approve} className="bg-emerald-600 hover:bg-emerald-700 gap-1.5">
              <CheckCircle size={14} />
              {t('admin', 'biodata_approve')}
            </Button>
          )}
          {(biodata.status === 'pending' || biodata.status === 'approved') && (
            <Button size="sm" variant="destructive" onClick={() => setRejectOpen(true)} className="gap-1.5">
              <XCircle size={14} />
              {t('admin', 'biodata_reject')}
            </Button>
          )}
          {biodata.status === 'approved' && (
            <Button size="sm" variant="outline" onClick={hide} className="gap-1.5">
              <EyeOff size={14} />
              {t('admin', 'biodata_hide')}
            </Button>
          )}
          {biodata.status === 'hidden' && (
            <Button size="sm" variant="outline" onClick={unhide} className="gap-1.5">
              <Eye size={14} />
              {t('admin', 'biodata_unhide')}
            </Button>
          )}
        </div>

      </div>
    </AdminLayout>
  )
}
