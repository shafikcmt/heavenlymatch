/// <reference path="../../types/ziggy.d.ts" />
import { Link } from '@inertiajs/react'
import { ArrowLeft, CheckCircle2, MapPin, GraduationCap, Briefcase, Heart, LogIn, Lock } from 'lucide-react'
import MarketingLayout from '@/layouts/MarketingLayout'
import { useTranslation } from '@/lib/i18n'
import { SeoHead } from '@/components/SeoHead'

// ── Types ─────────────────────────────────────────────────────────────────────

interface PublicProfile {
  id: string
  gender: 'male' | 'female'
  age: number | null
  height_cm: number | null
  weight_kg: number | null
  complexion: string | null
  blood_group: string | null
  mother_tongue: string | null
  marital_status: string | null
  division: string | null
  district: string | null
  upazila: string | null
  residing_country: string | null
  religion: string | null
  sect: string | null
  is_practicing: boolean | null
  prayers_info: string | null
  hijab_info: string | null
  beard_info: string | null
  highest_qualification: string | null
  occupation: string | null
  occupation_category: string | null
  about_me: string | null
  profile_headline: string | null
  family_type: string | null
  family_financial_status: string | null
  home_ownership: string | null
  health_status: string | null
  diet: string | null
  partner_age_min: number | null
  partner_age_max: number | null
  partner_division: string | null
  partner_marital_status: string | null
  partner_education: string | null
  partner_expectations: string | null
  is_verified: boolean
  avatar_num: number
  platform_mode: 'general' | 'islamic'
}

interface Props {
  profile: PublicProfile
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function cmToFeet(cm: number | null) {
  if (!cm) return null
  const totalIn = cm / 2.54
  return `${Math.floor(totalIn / 12)}′${Math.round(totalIn % 12)}″`
}

// ── Sub-components ────────────────────────────────────────────────────────────

function Row({ label, value }: { label: string; value: string | null | undefined }) {
  if (!value) return null
  return (
    <div className="flex items-baseline gap-3 py-2 border-b border-slate-50 last:border-0">
      <span className="text-[11px] text-slate-400 font-medium w-32 flex-shrink-0 uppercase tracking-wide">{label}</span>
      <span className="text-sm text-slate-800 font-medium flex-1">{value}</span>
    </div>
  )
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="bg-white rounded-2xl border border-slate-200 overflow-hidden mb-4 shadow-sm">
      <div className="px-5 py-3 bg-slate-50/70 border-b border-slate-100">
        <h2 className="text-sm font-semibold text-slate-800 tracking-tight">{title}</h2>
      </div>
      <div className="px-5 py-4">
        {children}
      </div>
    </div>
  )
}

// ── Component ─────────────────────────────────────────────────────────────────

export default function ProfileShow({ profile }: Props) {
  const { t } = useTranslation()
  const heightFt = cmToFeet(profile.height_cm)

  const locationParts = [profile.upazila, profile.district, profile.division, profile.residing_country]
    .filter(Boolean)

  const partnerAgeText = profile.partner_age_min && profile.partner_age_max
    ? t('marketing', 'public_age_both', { min: profile.partner_age_min, max: profile.partner_age_max })
    : profile.partner_age_min
      ? t('marketing', 'public_age_min_only', { min: profile.partner_age_min })
      : null

  return (
    <MarketingLayout>
      <SeoHead pageKey="home" />

      {/* ── Header bar ── */}
      <div className="bg-slate-900 text-white py-6 px-4">
        <div className="max-w-4xl mx-auto flex items-center gap-4">
          <Link
            href={route('profiles.index')}
            className="flex items-center gap-1.5 text-slate-400 hover:text-white text-sm transition-colors"
          >
            <ArrowLeft size={16} /> {t('marketing', 'public_back_search')}
          </Link>
        </div>
      </div>

      <div className="max-w-4xl mx-auto px-4 py-8">

        {/* ── Profile hero card ── */}
        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
          <div className="flex flex-col sm:flex-row gap-0">
            {/* Avatar strip */}
            <div className={`sm:w-36 h-32 sm:h-auto flex-shrink-0 flex items-center justify-center ${profile.gender === 'female' ? 'bg-gradient-to-br from-rose-50 to-pink-100' : 'bg-gradient-to-br from-blue-50 to-sky-100'}`}>
              <img
                src={`/images/marketing/profile-placeholder-${profile.gender}.svg`}
                alt=""
                className="h-20 w-20 object-contain opacity-60"
                aria-hidden="true"
                onError={e => { (e.target as HTMLImageElement).style.display = 'none' }}
              />
            </div>

            {/* Info */}
            <div className="flex-1 min-w-0 p-5">
              {/* Badges row */}
              <div className="flex flex-wrap items-center gap-1.5 mb-2.5">
                <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${profile.gender === 'female' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700'}`}>
                  {profile.gender === 'female' ? t('common', 'female') : t('common', 'male')}
                </span>
                {profile.is_verified && (
                  <span className="flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    <CheckCircle2 size={10} /> {t('common', 'verified')}
                  </span>
                )}
                {profile.platform_mode === 'islamic' && (
                  <span className="text-[10px] font-semibold text-violet-700 bg-violet-50 px-2 py-0.5 rounded-full border border-violet-200">
                    {t('marketing', 'public_islamic_mode')}
                  </span>
                )}
              </div>

              {profile.profile_headline && (
                <p className="text-slate-600 text-sm mb-2 italic leading-snug">&ldquo;{profile.profile_headline}&rdquo;</p>
              )}

              {/* Key stats */}
              <div className="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 mb-2">
                {profile.age && <span className="font-medium text-slate-700">{t('marketing', 'public_age_years', { n: profile.age })}</span>}
                {heightFt && <span>{heightFt}</span>}
                {profile.marital_status && (
                  <span>{t('biodata', profile.marital_status) || profile.marital_status.replace('_', ' ')}</span>
                )}
              </div>

              <div className="flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-400">
                {locationParts.length > 0 && (
                  <span className="flex items-center gap-1">
                    <MapPin size={10} /> {locationParts.join(', ')}
                  </span>
                )}
                {profile.occupation && (
                  <span className="flex items-center gap-1">
                    <Briefcase size={10} /> {profile.occupation}
                  </span>
                )}
                {profile.highest_qualification && (
                  <span className="flex items-center gap-1">
                    <GraduationCap size={10} /> {profile.highest_qualification}
                  </span>
                )}
              </div>

              {profile.about_me && (
                <p className="mt-2.5 text-sm text-slate-600 leading-relaxed line-clamp-2 border-t border-slate-100 pt-2.5">{profile.about_me}</p>
              )}
            </div>
          </div>
        </div>

        {/* ── Locked contact CTA ── */}
        <div className="bg-primary-50 border border-primary-200 rounded-2xl p-6 mb-6 text-center">
          <Lock size={28} className="text-primary-400 mx-auto mb-3" />
          <h3 className="font-semibold text-primary-900 mb-1">{t('marketing', 'public_contact_locked')}</h3>
          <p className="text-sm text-primary-700 mb-4">{t('marketing', 'public_contact_locked_desc')}</p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Link
              href={route('login')}
              className="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-xl bg-white border border-primary-300 text-primary-700 font-semibold text-sm hover:bg-primary-100 transition-colors"
            >
              <LogIn size={15} /> {t('marketing', 'nav_sign_in')}
            </Link>
            <Link
              href={route('register')}
              className="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 transition-colors shadow-md"
            >
              <Heart size={15} /> {t('marketing', 'nav_join_free')}
            </Link>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">

          {/* ── Personal info ── */}
          <Section title={t('marketing', 'public_section_personal')}>
            <Row label={t('common', 'age')}               value={profile.age ? t('marketing', 'public_age_years', { n: profile.age }) : null} />
            <Row label={t('marketing', 'public_label_height')} value={profile.height_cm ? `${profile.height_cm} cm (${heightFt})` : null} />
            <Row label={t('marketing', 'public_label_weight')} value={profile.weight_kg ? `${profile.weight_kg} kg` : null} />
            <Row label={t('biodata', 'complexion')}        value={profile.complexion} />
            <Row label={t('biodata', 'blood_group')}       value={profile.blood_group} />
            <Row label={t('biodata', 'mother_tongue')}     value={profile.mother_tongue} />
            <Row label={t('biodata', 'marital_status')}    value={profile.marital_status ? (t('biodata', profile.marital_status) || profile.marital_status) : null} />
            <Row label={t('biodata', 'residing_country')}  value={profile.residing_country} />
          </Section>

          {/* ── Location ── */}
          <Section title={t('biodata', 'section_location')}>
            <Row label={t('biodata', 'division')}  value={profile.division} />
            <Row label={t('biodata', 'district')}  value={profile.district} />
            <Row label={t('biodata', 'upazila')}   value={profile.upazila} />
            <Row label={t('biodata', 'residing_country')} value={profile.residing_country} />
          </Section>

          {/* ── Religion ── */}
          <Section title={t('biodata', 'section_religion')}>
            <Row label={t('biodata', 'religion')}         value={profile.religion} />
            <Row label={t('biodata', 'sect')}             value={profile.sect} />
            <Row label={t('biodata', 'is_practicing')}    value={profile.is_practicing != null ? (profile.is_practicing ? t('common', 'yes') : t('common', 'no')) : null} />
            <Row label={t('biodata', 'prayers_info')}     value={profile.prayers_info} />
            {profile.gender === 'female' && <Row label={t('biodata', 'hijab_info')} value={profile.hijab_info} />}
            {profile.gender === 'male'   && <Row label={t('biodata', 'beard_info')} value={profile.beard_info} />}
          </Section>

          {/* ── Education & Career ── */}
          <Section title={t('marketing', 'public_section_career')}>
            <Row label={t('biodata', 'highest_qualification')} value={profile.highest_qualification} />
            <Row label={t('biodata', 'occupation')}            value={profile.occupation} />
            <Row label={t('biodata', 'occupation_category')}   value={profile.occupation_category} />
          </Section>

          {/* ── Family ── */}
          <Section title={t('biodata', 'section_family')}>
            <Row label={t('biodata', 'family_type')}             value={profile.family_type} />
            <Row label={t('biodata', 'family_financial_status')} value={profile.family_financial_status} />
            <Row label={t('biodata', 'home_ownership')}          value={profile.home_ownership} />
          </Section>

          {/* ── Lifestyle ── */}
          <Section title={t('biodata', 'section_lifestyle')}>
            <Row label={t('biodata', 'health_status')} value={profile.health_status} />
            <Row label={t('biodata', 'diet')}          value={profile.diet} />
          </Section>

        </div>

        {/* ── Partner preferences ── */}
        {(partnerAgeText || profile.partner_division || profile.partner_marital_status || profile.partner_education || profile.partner_expectations) && (
          <Section title={t('biodata', 'section_partner')}>
            <Row label={t('marketing', 'public_age_range')}      value={partnerAgeText} />
            <Row label={t('biodata', 'partner_division')}        value={profile.partner_division} />
            <Row label={t('biodata', 'partner_marital_status')}  value={profile.partner_marital_status} />
            <Row label={t('biodata', 'partner_education')}       value={profile.partner_education} />
            <Row label={t('biodata', 'partner_expectations')}    value={profile.partner_expectations} />
          </Section>
        )}

        {/* ── Bottom CTA ── */}
        <div className="mt-6 text-center">
          <p className="text-sm text-slate-500 mb-4">{t('marketing', 'public_register_connect')}</p>
          <Link
            href={route('register')}
            className="inline-flex items-center justify-center gap-2 h-12 px-8 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors shadow-md"
          >
            <Heart size={16} /> {t('marketing', 'public_create_profile')}
          </Link>
        </div>
      </div>
    </MarketingLayout>
  )
}
