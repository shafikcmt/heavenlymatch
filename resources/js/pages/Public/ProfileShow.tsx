/// <reference path="../../types/ziggy.d.ts" />
import { Link } from '@inertiajs/react'
import { ArrowLeft, CheckCircle2, MapPin, GraduationCap, Briefcase, Heart, LogIn, Lock } from 'lucide-react'
import MarketingLayout from '@/layouts/MarketingLayout'
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

function Row({ label, value }: { label: string; value: string | null | undefined }) {
  if (!value) return null
  return (
    <div className="flex gap-3 py-2.5 border-b border-slate-100 last:border-0">
      <span className="text-xs text-slate-500 w-36 flex-shrink-0 pt-0.5">{label}</span>
      <span className="text-sm text-slate-800 font-medium flex-1">{value}</span>
    </div>
  )
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="bg-white rounded-2xl border border-slate-200 p-5 mb-4">
      <h2 className="font-semibold text-slate-900 text-sm mb-3 pb-2 border-b border-slate-100">{title}</h2>
      {children}
    </div>
  )
}

// ── Component ─────────────────────────────────────────────────────────────────

export default function ProfileShow({ profile }: Props) {
  const heightFt = cmToFeet(profile.height_cm)

  const locationParts = [profile.upazila, profile.district, profile.division, profile.residing_country]
    .filter(Boolean)

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
            <ArrowLeft size={16} /> Back to search
          </Link>
        </div>
      </div>

      <div className="max-w-4xl mx-auto px-4 py-8">

        {/* ── Profile hero card ── */}
        <div className="bg-white rounded-2xl border border-slate-200 p-6 mb-6 flex flex-col sm:flex-row gap-5 items-start">
          {/* Avatar */}
          <div className={`h-24 w-24 rounded-2xl flex-shrink-0 flex items-center justify-center overflow-hidden ${profile.gender === 'female' ? 'bg-rose-50' : 'bg-blue-50'}`}>
            <img
              src={`/images/marketing/profile-placeholder-${profile.gender}.svg`}
              alt=""
              className="h-20 w-20 object-contain opacity-70"
              aria-hidden="true"
              onError={e => { (e.target as HTMLImageElement).style.display = 'none' }}
            />
          </div>

          {/* Info */}
          <div className="flex-1 min-w-0">
            <div className="flex flex-wrap items-center gap-2 mb-2">
              <span className={`text-xs font-bold px-2.5 py-1 rounded-full ${profile.gender === 'female' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700'}`}>
                {profile.gender === 'female' ? 'Female' : 'Male'}
              </span>
              {profile.is_verified && (
                <span className="flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                  <CheckCircle2 size={12} /> Verified
                </span>
              )}
              {profile.platform_mode === 'islamic' && (
                <span className="text-xs font-semibold text-violet-700 bg-violet-50 px-2.5 py-1 rounded-full">Islamic Mode</span>
              )}
            </div>

            {profile.profile_headline && (
              <p className="text-slate-700 font-medium text-sm mb-2 italic">&ldquo;{profile.profile_headline}&rdquo;</p>
            )}

            <div className="flex flex-wrap gap-3 text-xs text-slate-500">
              {profile.age && <span>{profile.age} years</span>}
              {heightFt && <span>{heightFt}</span>}
              {profile.marital_status && <span className="capitalize">{profile.marital_status.replace('_', ' ')}</span>}
              {locationParts.length > 0 && (
                <span className="flex items-center gap-1">
                  <MapPin size={11} /> {locationParts.join(', ')}
                </span>
              )}
              {profile.occupation && (
                <span className="flex items-center gap-1">
                  <Briefcase size={11} /> {profile.occupation}
                </span>
              )}
              {profile.highest_qualification && (
                <span className="flex items-center gap-1">
                  <GraduationCap size={11} /> {profile.highest_qualification}
                </span>
              )}
            </div>

            {profile.about_me && (
              <p className="mt-3 text-sm text-slate-600 leading-relaxed line-clamp-3">{profile.about_me}</p>
            )}
          </div>
        </div>

        {/* ── Locked contact CTA ── */}
        <div className="bg-primary-50 border border-primary-200 rounded-2xl p-6 mb-6 text-center">
          <Lock size={28} className="text-primary-400 mx-auto mb-3" />
          <h3 className="font-semibold text-primary-900 mb-1">Contact details are hidden</h3>
          <p className="text-sm text-primary-700 mb-4">
            Login or register to view phone number, email, guardian contact, and send interest.
          </p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Link
              href={route('login')}
              className="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-xl bg-white border border-primary-300 text-primary-700 font-semibold text-sm hover:bg-primary-100 transition-colors"
            >
              <LogIn size={15} /> Login
            </Link>
            <Link
              href={route('register')}
              className="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-xl bg-primary-600 text-white font-semibold text-sm hover:bg-primary-700 transition-colors shadow-md"
            >
              <Heart size={15} /> Register Free
            </Link>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">

          {/* ── Personal info ── */}
          <Section title="Personal Information">
            <Row label="Age" value={profile.age ? `${profile.age} years` : null} />
            <Row label="Height" value={profile.height_cm ? `${profile.height_cm} cm (${heightFt})` : null} />
            <Row label="Weight" value={profile.weight_kg ? `${profile.weight_kg} kg` : null} />
            <Row label="Complexion" value={profile.complexion} />
            <Row label="Blood Group" value={profile.blood_group} />
            <Row label="Mother Tongue" value={profile.mother_tongue} />
            <Row label="Marital Status" value={profile.marital_status?.replace('_', ' ')} />
            <Row label="Residing Country" value={profile.residing_country} />
          </Section>

          {/* ── Location ── */}
          <Section title="Location">
            <Row label="Division" value={profile.division} />
            <Row label="District" value={profile.district} />
            <Row label="Upazila" value={profile.upazila} />
            <Row label="Country" value={profile.residing_country} />
          </Section>

          {/* ── Religion ── */}
          <Section title="Religious Information">
            <Row label="Religion" value={profile.religion} />
            <Row label="Sect" value={profile.sect} />
            <Row label="Practicing" value={profile.is_practicing != null ? (profile.is_practicing ? 'Yes' : 'No') : null} />
            <Row label="Prayers" value={profile.prayers_info} />
            {profile.gender === 'female' && <Row label="Hijab" value={profile.hijab_info} />}
            {profile.gender === 'male' && <Row label="Beard" value={profile.beard_info} />}
          </Section>

          {/* ── Education & Career ── */}
          <Section title="Education & Career">
            <Row label="Education" value={profile.highest_qualification} />
            <Row label="Occupation" value={profile.occupation} />
            <Row label="Occupation Type" value={profile.occupation_category} />
          </Section>

          {/* ── Family ── */}
          <Section title="Family Information">
            <Row label="Family Type" value={profile.family_type} />
            <Row label="Financial Status" value={profile.family_financial_status} />
            <Row label="Home Ownership" value={profile.home_ownership} />
          </Section>

          {/* ── Lifestyle ── */}
          <Section title="Lifestyle">
            <Row label="Health Status" value={profile.health_status} />
            <Row label="Diet" value={profile.diet} />
          </Section>

        </div>

        {/* ── Partner preferences ── */}
        {(profile.partner_age_min || profile.partner_age_max || profile.partner_division || profile.partner_marital_status || profile.partner_education || profile.partner_expectations) && (
          <Section title="Partner Preferences">
            <Row label="Age Range" value={
              profile.partner_age_min && profile.partner_age_max
                ? `${profile.partner_age_min} – ${profile.partner_age_max} years`
                : profile.partner_age_min ? `${profile.partner_age_min}+ years` : null
            } />
            <Row label="Division" value={profile.partner_division} />
            <Row label="Marital Status" value={profile.partner_marital_status} />
            <Row label="Education" value={profile.partner_education} />
            <Row label="Expectations" value={profile.partner_expectations} />
          </Section>
        )}

        {/* ── Bottom CTA ── */}
        <div className="mt-6 text-center">
          <p className="text-sm text-slate-500 mb-4">Interested in this profile? Register to connect.</p>
          <Link
            href={route('register')}
            className="inline-flex items-center justify-center gap-2 h-12 px-8 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors shadow-md"
          >
            <Heart size={16} /> Create Your Profile Free
          </Link>
        </div>
      </div>
    </MarketingLayout>
  )
}
