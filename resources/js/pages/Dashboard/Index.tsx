import { Head, Link, usePage } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { ProfileCard } from '@/components/profile/ProfileCard'
import { Button } from '@/components/ui/Button'
import { Card, CardBody, CardTitle } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import type { PageProps, ProfileCard as ProfileCardType } from '@/types'
import {
  Sparkles, Heart, Eye, MessageCircle, CheckCircle2,
  Shield, ArrowRight, Bell, Star, TrendingUp,
} from 'lucide-react'

interface DashboardStats {
  matches_today: number
  interests_received: number
  profile_views: number
  messages_unread: number
}

interface Props {
  stats: DashboardStats
  daily_picks: ProfileCardType[]
  biodata_completeness: number
  recent_visitors: ProfileCardType[]
  is_verified: boolean
}

export default function Dashboard({ stats, daily_picks, biodata_completeness, recent_visitors, is_verified }: Props) {
  const { auth } = usePage<PageProps>().props
  const user = auth.user!

  return (
    <AppLayout>
      <Head title="Dashboard" />

      <div className="max-w-6xl mx-auto space-y-6">

        {/* Welcome strip */}
        <div className="flex items-start justify-between gap-4">
          <div>
            <h1 className="text-2xl font-bold text-slate-900">
              Assalamu Alaikum, {user.name.split(' ')[0]} 👋
            </h1>
            <p className="text-sm text-slate-500 mt-0.5">
              {new Date().toLocaleDateString('en-BD', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
            </p>
          </div>
          {user.membership_status === 'free' && (
            <Link href="/upgrade">
              <Button variant="premium" size="sm">
                ✦ Upgrade Plan
              </Button>
            </Link>
          )}
        </div>

        {/* Alerts */}
        {!user.biodata_complete && (
          <div className="rounded-2xl border border-amber-200 bg-amber-50 p-4 flex items-center gap-4">
            <div className="h-10 w-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
              <Shield size={20} className="text-amber-600" />
            </div>
            <div className="flex-1">
              <p className="text-sm font-semibold text-amber-900">Complete your biodata</p>
              <p className="text-xs text-amber-700 mt-0.5">
                Your profile is {biodata_completeness}% complete. Profiles &gt;80% get 3× more matches.
              </p>
              <div className="mt-2 h-1.5 rounded-full bg-amber-200 overflow-hidden">
                <div
                  className="h-full rounded-full bg-amber-500 transition-all"
                  style={{ width: `${biodata_completeness}%` }}
                />
              </div>
            </div>
            <Link href="/biodata/create">
              <Button variant="premium" size="sm">Continue →</Button>
            </Link>
          </div>
        )}

        {!is_verified && user.biodata_complete && (
          <div className="rounded-2xl border border-blue-200 bg-blue-50 p-4 flex items-center gap-4">
            <CheckCircle2 size={24} className="text-blue-600 shrink-0" />
            <div className="flex-1">
              <p className="text-sm font-semibold text-blue-900">Get verified — earn a Gold shield badge</p>
              <p className="text-xs text-blue-700 mt-0.5">Upload your NID or passport. Verified profiles get 5× more interest requests.</p>
            </div>
            <Link href="/verify/identity">
              <Button size="sm">Verify Now</Button>
            </Link>
          </div>
        )}

        {/* Stats row */}
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
          {[
            { label: 'New Matches',  value: stats.matches_today,      icon: Sparkles,       color: 'text-blue-600',   bg: 'bg-blue-50' },
            { label: 'Interests',    value: stats.interests_received,  icon: Heart,          color: 'text-rose-600',   bg: 'bg-rose-50' },
            { label: 'Profile Views',value: stats.profile_views,       icon: Eye,            color: 'text-violet-600', bg: 'bg-violet-50' },
            { label: 'Unread Msgs',  value: stats.messages_unread,     icon: MessageCircle,  color: 'text-emerald-600',bg: 'bg-emerald-50' },
          ].map(s => (
            <Card key={s.label} className="p-4">
              <div className={`inline-flex h-9 w-9 items-center justify-center rounded-xl ${s.bg} mb-3`}>
                <s.icon size={18} className={s.color} />
              </div>
              <p className="text-2xl font-bold text-slate-900">{s.value}</p>
              <p className="text-xs text-slate-500 mt-0.5">{s.label}</p>
            </Card>
          ))}
        </div>

        {/* Daily picks */}
        <section>
          <div className="flex items-center justify-between mb-4">
            <div className="flex items-center gap-2">
              <Sparkles size={18} className="text-blue-600" />
              <h2 className="font-bold text-slate-900">Today's Best Matches</h2>
              <Badge variant="default" size="sm">AI Curated</Badge>
            </div>
            <Link href="/matches" className="text-sm text-primary-600 hover:underline flex items-center gap-1">
              See all <ArrowRight size={14} />
            </Link>
          </div>

          {daily_picks.length > 0 ? (
            <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
              {daily_picks.map(p => (
                <ProfileCard
                  key={p.registration_id}
                  profile={p}
                  onShortlist={id => console.log('shortlist', id)}
                  onInterest={id => console.log('interest', id)}
                />
              ))}
            </div>
          ) : (
            <Card>
              <CardBody className="text-center py-12">
                <Sparkles size={32} className="mx-auto text-slate-300 mb-3" />
                <p className="text-sm font-medium text-slate-500">Complete your biodata to unlock AI matches</p>
                <Link href="/biodata/create" className="mt-3 inline-block">
                  <Button size="sm" className="mt-2">Complete Biodata</Button>
                </Link>
              </CardBody>
            </Card>
          )}
        </section>

        {/* Recent profile visitors */}
        {recent_visitors.length > 0 && (
          <section>
            <div className="flex items-center justify-between mb-4">
              <div className="flex items-center gap-2">
                <Eye size={18} className="text-violet-600" />
                <h2 className="font-bold text-slate-900">Who Viewed Your Profile</h2>
              </div>
              <Link href="/who-viewed" className="text-sm text-primary-600 hover:underline flex items-center gap-1">
                See all <ArrowRight size={14} />
              </Link>
            </div>
            <div className="flex gap-3 overflow-x-auto pb-2">
              {recent_visitors.map(v => (
                <Link key={v.registration_id} href={`/profile/${v.registration_id}`} className="shrink-0 group">
                  <div className="h-16 w-16 rounded-2xl bg-slate-100 overflow-hidden border-2 border-transparent group-hover:border-primary-400 transition-all">
                    <img
                      src={`/images/avatar-${v.gender}.svg`}
                      alt={v.name}
                      className="h-full w-full object-cover"
                    />
                  </div>
                  <p className="text-xs font-medium text-center mt-1 text-slate-700 truncate w-16">{v.name.split(' ')[0]}</p>
                </Link>
              ))}
            </div>
          </section>
        )}
      </div>
    </AppLayout>
  )
}
