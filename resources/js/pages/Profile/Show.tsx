/// <reference path="../../types/ziggy.d.ts" />
import { Head, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { calcAge, cmToFeetInches } from '@/lib/utils'
import { Heart, Send, Eye, EyeOff, Flag } from 'lucide-react'
import { useState } from 'react'
import { cn } from '@/lib/utils'

interface BiodataDetail {
  marital_status?: string
  birth_date?: string
  height_cm?: number
  weight_kg?: number
  complexion?: string
  blood_group?: string
  about_me?: string
  profile_headline?: string
  division?: string; district?: string; residing_country?: string
  religion?: string; sect?: string; is_practicing?: boolean; prayers_info?: string
  education_method?: string; highest_qualification?: string
  occupation?: string; occupation_category?: string; monthly_income?: number
  family_type?: string; brothers?: number; sisters?: number
  health_status?: string; diet?: string; smoking?: string
  is_islamically_educated?: boolean; wali_approval?: boolean
  partner_age_min?: number; partner_age_max?: number
  partner_expectations?: string
  guardian_mobile?: string
  completeness_score?: number
}

interface ProfileData {
  registration_id: string
  name: string
  gender: string
  platform_mode: string
  email_verified_at?: string
}

interface Props {
  profile: ProfileData
  biodata?: BiodataDetail
  photos: Array<{ path: string; is_primary: boolean; blurred: boolean }>
  interestSent: boolean
  interestReceived: boolean
  isConnected: boolean
  isShortlisted: boolean
  isOwnProfile: boolean
}

const SECTION = ({ title, children }: { title: string; children: React.ReactNode }) => (
  <div className="rounded-2xl border border-slate-200 bg-white p-6">
    <h3 className="text-base font-semibold text-slate-900 mb-4 border-b border-slate-100 pb-2">{title}</h3>
    {children}
  </div>
)

const ROW = ({ label, value }: { label: string; value?: string | number | null }) =>
  value != null ? (
    <div className="flex justify-between py-1.5 text-sm">
      <span className="text-slate-500 w-40 flex-shrink-0">{label}</span>
      <span className="text-slate-900 font-medium text-right">{value}</span>
    </div>
  ) : null

export default function ProfileShow({
  profile, biodata, photos,
  interestSent, interestReceived, isConnected, isShortlisted, isOwnProfile,
}: Props) {
  const [shortlisted, setShortlisted] = useState(isShortlisted)
  const [reportOpen, setReportOpen] = useState(false)

  const age = biodata?.birth_date ? calcAge(biodata.birth_date) : null
  const primaryPhoto = photos.find(p => p.is_primary) ?? photos[0]

  const sendInterest = () =>
    router.post(route('interests.store'), { receiver_id: profile.registration_id })

  const toggleShortlist = () => {
    router.post(route('shortlist.toggle'), { target_id: profile.registration_id }, {
      preserveScroll: true,
      onSuccess: () => setShortlisted(v => !v),
    })
  }

  return (
    <AppLayout>
      <Head title={`${profile.name} — Profile`} />

      <div className="max-w-4xl mx-auto px-4 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

          {/* Sidebar */}
          <div className="space-y-4">
            {/* Photo */}
            <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden">
              <div className="aspect-[3/4] bg-slate-100 relative flex items-center justify-center">
                {primaryPhoto ? (
                  primaryPhoto.blurred ? (
                    <div className="absolute inset-0 flex flex-col items-center justify-center bg-slate-200">
                      <EyeOff size={32} className="text-slate-400 mb-2" />
                      <p className="text-xs text-slate-500 text-center px-4">
                        Photo hidden. Send interest to request access.
                      </p>
                    </div>
                  ) : (
                    <img src={primaryPhoto.path} alt={profile.name} className="w-full h-full object-cover" />
                  )
                ) : (
                  <span className="text-8xl">{profile.gender === 'male' ? '👨' : '👩'}</span>
                )}

                {profile.platform_mode === 'islamic' && (
                  <div className="absolute top-3 right-3">
                    <Badge variant="islamic">Islamic</Badge>
                  </div>
                )}
              </div>

              <div className="p-4 text-center">
                <h1 className="text-xl font-bold text-slate-900">{profile.name}</h1>
                {biodata?.profile_headline && (
                  <p className="text-sm text-slate-500 mt-1">{biodata.profile_headline}</p>
                )}
                <p className="text-sm text-slate-400 mt-1">
                  {[age ? `${age} yrs` : null, biodata?.district, biodata?.residing_country]
                    .filter(Boolean).join(' · ')}
                </p>
                {biodata?.completeness_score !== undefined && (
                  <div className="mt-3">
                    <div className="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                      <div
                        className="h-full bg-emerald-500 rounded-full"
                        style={{ width: `${biodata.completeness_score}%` }}
                      />
                    </div>
                    <p className="text-xs text-slate-400 mt-1">{biodata.completeness_score}% complete</p>
                  </div>
                )}
              </div>
            </div>

            {/* Actions */}
            {!isOwnProfile && (
              <div className="space-y-2">
                {isConnected ? (
                  <Button className="w-full" variant="outline" disabled>
                    ✓ Connected
                  </Button>
                ) : interestSent ? (
                  <Button className="w-full" variant="outline" disabled>
                    Interest Sent
                  </Button>
                ) : interestReceived ? (
                  <p className="text-sm text-center text-emerald-600 font-medium">
                    This person sent you an interest!
                  </p>
                ) : (
                  <Button className="w-full" onClick={sendInterest}>
                    <Send size={16} className="mr-2" />
                    Send Interest
                  </Button>
                )}

                <Button
                  variant="outline"
                  className={cn('w-full gap-2', shortlisted && 'text-red-500 border-red-200')}
                  onClick={toggleShortlist}
                >
                  <Heart size={16} className={shortlisted ? 'fill-red-500 text-red-500' : ''} />
                  {shortlisted ? 'Shortlisted' : 'Shortlist'}
                </Button>

                <button
                  onClick={() => setReportOpen(true)}
                  className="w-full text-xs text-slate-400 hover:text-red-500 flex items-center justify-center gap-1 mt-1"
                >
                  <Flag size={12} /> Report Profile
                </button>
              </div>
            )}
          </div>

          {/* Main content */}
          <div className="lg:col-span-2 space-y-4">

            {biodata?.about_me && (
              <SECTION title="About Me">
                <p className="text-sm text-slate-700 leading-relaxed">{biodata.about_me}</p>
              </SECTION>
            )}

            <SECTION title="General Information">
              <ROW label="Marital Status" value={biodata?.marital_status?.replace('_', ' ')} />
              <ROW label="Age" value={age ? `${age} years` : undefined} />
              <ROW label="Height" value={biodata?.height_cm ? cmToFeetInches(biodata.height_cm) : undefined} />
              <ROW label="Complexion" value={biodata?.complexion} />
              <ROW label="Blood Group" value={biodata?.blood_group} />
              <ROW label="Health" value={biodata?.health_status?.replace('_', ' ')} />
            </SECTION>

            <SECTION title="Religion & Practice">
              <ROW label="Religion" value={biodata?.religion} />
              <ROW label="Sect / Madhab" value={biodata?.sect} />
              <ROW label="Daily Prayers" value={biodata?.prayers_info?.replace('_times', ' times daily')} />
              <ROW label="Islamically Educated" value={biodata?.is_islamically_educated ? 'Yes' : undefined} />
              <ROW label="Wali Approval" value={biodata?.wali_approval ? 'Yes' : undefined} />
            </SECTION>

            <SECTION title="Education & Profession">
              <ROW label="Qualification" value={biodata?.highest_qualification?.replace('_', ' ')} />
              <ROW label="Occupation" value={biodata?.occupation} />
              <ROW label="Monthly Income" value={biodata?.monthly_income ? `৳${biodata.monthly_income.toLocaleString()}` : undefined} />
            </SECTION>

            <SECTION title="Family">
              <ROW label="Family Type" value={biodata?.family_type} />
              <ROW label="Brothers" value={biodata?.brothers} />
              <ROW label="Sisters" value={biodata?.sisters} />
            </SECTION>

            {biodata?.partner_expectations && (
              <SECTION title="Partner Preferences">
                {(biodata.partner_age_min || biodata.partner_age_max) && (
                  <ROW label="Age Range" value={`${biodata.partner_age_min ?? '?'}–${biodata.partner_age_max ?? '?'} yrs`} />
                )}
                <p className="text-sm text-slate-700 mt-2">{biodata.partner_expectations}</p>
              </SECTION>
            )}
          </div>
        </div>

        {/* Report modal */}
        {reportOpen && (
          <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div className="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
              <h3 className="font-bold text-slate-900 mb-4">Report Profile</h3>
              <form
                onSubmit={e => {
                  e.preventDefault()
                  const fd = new FormData(e.currentTarget)
                  router.post(route('report.store', { registrationId: profile.registration_id }), {
                    reason: fd.get('reason') as string,
                    description: fd.get('description') as string,
                  }, { onSuccess: () => setReportOpen(false) })
                }}
                className="space-y-4"
              >
                <select name="reason" required className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                  <option value="">Select reason</option>
                  <option value="fake_profile">Fake Profile</option>
                  <option value="inappropriate_photo">Inappropriate Photo</option>
                  <option value="harassment">Harassment</option>
                  <option value="spam">Spam</option>
                  <option value="underage">Underage</option>
                  <option value="other">Other</option>
                </select>
                <textarea name="description" rows={3} placeholder="Additional details..." className="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm resize-none" />
                <div className="flex gap-3">
                  <Button type="button" variant="outline" className="flex-1" onClick={() => setReportOpen(false)}>
                    Cancel
                  </Button>
                  <Button type="submit" variant="destructive" className="flex-1">
                    Submit Report
                  </Button>
                </div>
              </form>
            </div>
          </div>
        )}
      </div>
    </AppLayout>
  )
}
