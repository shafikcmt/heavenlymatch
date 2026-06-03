/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link, usePage } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { ProfileCard } from '@/components/profile/ProfileCard'
import { Button } from '@/components/ui/Button'
import { Card, CardBody } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import type { PageProps, ProfileCard as ProfileCardType, AccessState, CompletionData } from '@/types'
import { useTranslation } from '@/lib/i18n'
import { cn } from '@/lib/utils'
import {
  Sparkles, Heart, Eye, MessageCircle, CheckCircle2,
  Shield, ArrowRight, Star, Circle, Mail,
  Edit, Search, User, Crown, AlertTriangle,
  ClipboardCheck, Camera, Users, Lock, Clock, Lightbulb,
} from 'lucide-react'

interface DashboardStats {
  matches_today: number
  interests_received: number
  interests_sent: number
  profile_views: number
  messages_unread: number
  shortlisted_count: number
}

interface Props {
  stats: DashboardStats
  daily_picks: ProfileCardType[]
  biodata_completeness: number
  biodata_status?: string | null
  rejection_reason?: string | null
  recent_visitors: ProfileCardType[]
  is_verified: boolean
}

const SECTIONS = ['general','location','religion','education','family','lifestyle','marriage','partner','photos'] as const

export default function Dashboard(props: Props) {
  const { auth, completion, access } = usePage<PageProps>().props
  const user = auth.user!

  // Non-approved users get the focused onboarding / status dashboard.
  if (access && !access.can_access_matches) {
    return (
      <AppLayout>
        <Head title="Dashboard" />
        <OnboardingDashboard
          access={access}
          completion={completion}
          rejectionReason={props.rejection_reason ?? null}
          userName={user.name}
        />
      </AppLayout>
    )
  }

  return (
    <AppLayout>
      <Head title="Dashboard" />
      <FullDashboard {...props} />
    </AppLayout>
  )
}

/* ════════════════════════════════════════════════════════════════════════════
   ONBOARDING / NON-APPROVED DASHBOARD
   States: incomplete · pending · rejected · hidden
═══════════════════════════════════════════════════════════════════════════ */
function OnboardingDashboard({ access, completion, rejectionReason, userName }: {
  access: AccessState
  completion: CompletionData | null
  rejectionReason: string | null
  userName: string
}) {
  const { t } = useTranslation()
  const pct = access.completion_percentage
  const firstName = userName.split(' ')[0] || userName

  return (
    <div className="max-w-3xl mx-auto space-y-5">
      {/* Greeting */}
      <div>
        <h1 className="text-2xl font-bold text-slate-900">
          {t('dashboard', 'dash_greeting', { name: firstName })} 👋
        </h1>
        <p className="text-sm text-slate-500 mt-0.5">
          {new Date().toLocaleDateString('en-BD', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
        </p>
      </div>

      {access.state === 'pending'  && <PendingCard t={t} />}
      {access.state === 'rejected' && <RejectedCard t={t} reason={rejectionReason} />}
      {access.state === 'hidden'   && <HiddenCard t={t} />}

      {/* Completion card — always shown except for hidden state */}
      {access.state !== 'hidden' && (
        <CompletionCard t={t} access={access} completion={completion} pct={pct} />
      )}

      {/* Motivational tips */}
      {access.state === 'incomplete' && <TipsGrid t={t} />}

      {/* Locked-features hint */}
      <div className="rounded-2xl border border-slate-200 bg-white p-4 flex items-center gap-4">
        <div className="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
          <Lock size={18} className="text-slate-400" />
        </div>
        <div className="min-w-0">
          <p className="text-sm font-semibold text-slate-800">{t('dashboard', 'onboard_locked_title')}</p>
          <p className="text-xs text-slate-500 mt-0.5">{t('dashboard', 'onboard_locked_desc')}</p>
        </div>
      </div>
    </div>
  )
}

function CompletionCard({ t, access, completion, pct }: {
  t: any; access: AccessState; completion: CompletionData | null; pct: number
}) {
  const started = pct > 0 || access.has_biodata
  const ctaUrl = completion?.next_step_url ?? access.next_step_url
  const barColor = pct >= 80 ? 'bg-emerald-500' : pct >= 50 ? 'bg-amber-500' : 'bg-primary-500'

  return (
    <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden">
      <div className="bg-gradient-to-br from-primary-600 to-primary-700 px-6 py-5 text-white">
        <div className="flex items-start gap-3">
          <div className="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
            <Sparkles size={22} />
          </div>
          <div>
            <h2 className="text-lg font-bold leading-snug">{t('dashboard', 'onboard_title')}</h2>
            <p className="text-sm text-white/85 mt-1">{t('dashboard', 'onboard_subtitle')}</p>
          </div>
        </div>
      </div>

      <div className="p-6 space-y-5">
        {/* Progress */}
        <div>
          <div className="flex items-center justify-between mb-1.5">
            <span className="text-sm font-medium text-slate-600">{t('dashboard', 'onboard_progress')}</span>
            <span className="text-sm font-bold text-slate-900">{pct}%</span>
          </div>
          <div className="h-2.5 rounded-full bg-slate-100 overflow-hidden">
            <div className={cn('h-full rounded-full transition-all', barColor)} style={{ width: `${Math.min(100, Math.max(0, pct))}%` }} />
          </div>
        </div>

        {/* Missing sections checklist */}
        {completion && (
          <div>
            <p className="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">{t('dashboard', 'dash_sections_title')}</p>
            <div className="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
              {SECTIONS.map(section => {
                const done = completion.completed_sections.includes(section)
                const raw = t('biodata', `section_label.${section}`)
                const label = raw === `section_label.${section}` ? section : raw
                return (
                  <div key={section} className="flex items-center gap-1.5">
                    {done
                      ? <CheckCircle2 size={14} className="text-emerald-500 shrink-0" />
                      : <Circle size={14} className="text-slate-300 shrink-0" />}
                    <span className={cn('text-xs', done ? 'text-slate-400 line-through' : 'text-slate-700')}>{label}</span>
                  </div>
                )
              })}
            </div>
          </div>
        )}

        {/* CTA + note */}
        <div className="flex flex-col sm:flex-row sm:items-center gap-3 pt-1">
          <Link href={ctaUrl} className="sm:w-auto">
            <Button variant="premium" size="lg" className="w-full sm:w-auto gap-1.5">
              <Edit size={16} />
              {started ? t('dashboard', 'onboard_cta_continue') : t('dashboard', 'onboard_cta_complete')}
              <ArrowRight size={16} />
            </Button>
          </Link>
          <p className="text-xs text-slate-500 flex items-center gap-1.5">
            <Lightbulb size={13} className="text-amber-500 shrink-0" />
            {t('dashboard', 'onboard_note')}
          </p>
        </div>
      </div>
    </div>
  )
}

function TipsGrid({ t }: { t: any }) {
  const tips = [
    { icon: ClipboardCheck, title: t('dashboard', 'onboard_tip_accurate'),  desc: t('dashboard', 'onboard_tip_accurate_desc'),  bg: 'bg-blue-50',    color: 'text-blue-600' },
    { icon: Camera,         title: t('dashboard', 'onboard_tip_photo'),     desc: t('dashboard', 'onboard_tip_photo_desc'),     bg: 'bg-violet-50',  color: 'text-violet-600' },
    { icon: Users,          title: t('dashboard', 'onboard_tip_family'),    desc: t('dashboard', 'onboard_tip_family_desc'),    bg: 'bg-rose-50',    color: 'text-rose-600' },
    { icon: Shield,         title: t('dashboard', 'onboard_tip_protected'), desc: t('dashboard', 'onboard_tip_protected_desc'), bg: 'bg-emerald-50', color: 'text-emerald-600' },
  ]
  return (
    <div>
      <h2 className="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">{t('dashboard', 'onboard_tips_title')}</h2>
      <div className="grid grid-cols-2 gap-3">
        {tips.map((tip, i) => (
          <div key={i} className="rounded-2xl border border-slate-200 bg-white p-4">
            <div className={cn('h-9 w-9 rounded-xl flex items-center justify-center mb-2.5', tip.bg)}>
              <tip.icon size={18} className={tip.color} />
            </div>
            <p className="text-sm font-semibold text-slate-800 leading-snug">{tip.title}</p>
            <p className="text-xs text-slate-500 mt-0.5">{tip.desc}</p>
          </div>
        ))}
      </div>
    </div>
  )
}

function PendingCard({ t }: { t: any }) {
  return (
    <div className="rounded-2xl border border-sky-200 bg-sky-50 p-5 flex items-start gap-4">
      <div className="h-10 w-10 rounded-xl bg-sky-100 flex items-center justify-center shrink-0">
        <Clock size={20} className="text-sky-600" />
      </div>
      <div className="flex-1 min-w-0">
        <div className="flex items-center gap-2 flex-wrap">
          <p className="text-sm font-semibold text-sky-900">{t('dashboard', 'state_pending_title')}</p>
          <span className="rounded-full bg-sky-600 text-white text-[11px] font-semibold px-2 py-0.5">{t('dashboard', 'state_pending_badge')}</span>
        </div>
        <p className="text-xs text-sky-700 mt-1">{t('dashboard', 'state_pending_note')}</p>
      </div>
    </div>
  )
}

function RejectedCard({ t, reason }: { t: any; reason: string | null }) {
  return (
    <div className="rounded-2xl border border-red-200 bg-red-50 p-5">
      <div className="flex items-start gap-4">
        <div className="h-10 w-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
          <AlertTriangle size={20} className="text-red-600" />
        </div>
        <div className="flex-1 min-w-0">
          <div className="flex items-center gap-2 flex-wrap">
            <p className="text-sm font-semibold text-red-900">{t('dashboard', 'state_rejected_title')}</p>
            <span className="rounded-full bg-red-600 text-white text-[11px] font-semibold px-2 py-0.5">{t('dashboard', 'state_badge_rejected')}</span>
          </div>
          <p className="text-xs text-red-700 mt-1">{t('dashboard', 'state_rejected_note')}</p>
          {reason && (
            <p className="mt-2 text-xs font-medium text-red-800 bg-red-100 rounded-lg px-3 py-2">
              {t('dashboard', 'dash_rejection_reason_label')} {reason}
            </p>
          )}
          <Link href={route('biodata.wizard')} className="inline-block mt-3">
            <Button size="sm" variant="destructive" className="gap-1.5">
              <Edit size={14} /> {t('dashboard', 'state_rejected_cta')}
            </Button>
          </Link>
        </div>
      </div>
    </div>
  )
}

function HiddenCard({ t }: { t: any }) {
  return (
    <div className="rounded-2xl border border-slate-300 bg-slate-50 p-5 flex items-start gap-4">
      <div className="h-10 w-10 rounded-xl bg-slate-200 flex items-center justify-center shrink-0">
        <Eye size={20} className="text-slate-500" />
      </div>
      <div className="flex-1 min-w-0">
        <div className="flex items-center gap-2 flex-wrap">
          <p className="text-sm font-semibold text-slate-800">{t('dashboard', 'state_hidden_title')}</p>
          <span className="rounded-full bg-slate-500 text-white text-[11px] font-semibold px-2 py-0.5">{t('dashboard', 'state_badge_hidden')}</span>
        </div>
        <p className="text-xs text-slate-600 mt-1">{t('dashboard', 'state_hidden_note')}</p>
        <Link href={route('biodata.wizard')} className="inline-block mt-3">
          <Button size="sm" variant="outline" className="gap-1.5">
            <Edit size={14} /> {t('common', 'edit_biodata')}
          </Button>
        </Link>
      </div>
    </div>
  )
}

/* ════════════════════════════════════════════════════════════════════════════
   FULL DASHBOARD (approved users) — unchanged feature set
═══════════════════════════════════════════════════════════════════════════ */
function FullDashboard({ stats, daily_picks, biodata_completeness, biodata_status, rejection_reason, recent_visitors, is_verified }: Props) {
  const { auth, completion } = usePage<PageProps>().props
  const user = auth.user!
  const { t } = useTranslation()

  const hasTrustBadge = user.is_email_verified || user.biodata_status === 'approved' || user.membership_status === 'active'

  const quickActions = [
    { icon: Edit,          lk: 'dash_quick_biodata',   href: route('biodata.wizard'),     bg: 'bg-primary-50',  color: 'text-primary-600',  badge: 0 },
    { icon: User,          lk: 'dash_quick_profile',   href: route('dashboard.profile'),  bg: 'bg-slate-100',   color: 'text-slate-600',    badge: 0 },
    { icon: Search,        lk: 'dash_quick_search',    href: route('search.index'),       bg: 'bg-blue-50',     color: 'text-blue-600',     badge: 0 },
    { icon: Sparkles,      lk: 'dash_quick_matches',   href: route('matches.index'),      bg: 'bg-violet-50',   color: 'text-violet-600',   badge: 0 },
    { icon: Heart,         lk: 'dash_quick_interests', href: route('interests.received'), bg: 'bg-rose-50',     color: 'text-rose-600',     badge: stats.interests_received },
    { icon: MessageCircle, lk: 'dash_quick_inbox',     href: route('inbox.index'),        bg: 'bg-emerald-50',  color: 'text-emerald-600',  badge: stats.messages_unread },
    { icon: Star,          lk: 'dash_quick_shortlist', href: route('shortlist.index'),    bg: 'bg-amber-50',    color: 'text-amber-600',    badge: 0 },
    { icon: Crown,         lk: 'dash_quick_upgrade',   href: route('upgrade.plans'),      bg: 'bg-orange-50',   color: 'text-orange-600',   badge: 0 },
  ]

  const statItems = [
    { lk: 'dash_stat_matches',        value: stats.matches_today,      icon: Sparkles,       color: 'text-blue-600',    bg: 'bg-blue-50',    href: route('matches.index') },
    { lk: 'dash_stat_interests',      value: stats.interests_received,  icon: Heart,          color: 'text-rose-600',    bg: 'bg-rose-50',    href: route('interests.received') },
    { lk: 'dash_stat_interests_sent', value: stats.interests_sent,      icon: ArrowRight,     color: 'text-orange-600',  bg: 'bg-orange-50',  href: route('interests.sent') },
    { lk: 'dash_stat_messages',       value: stats.messages_unread,     icon: MessageCircle,  color: 'text-emerald-600', bg: 'bg-emerald-50', href: route('inbox.index') },
    { lk: 'dash_stat_shortlist',      value: stats.shortlisted_count,   icon: Star,           color: 'text-amber-600',   bg: 'bg-amber-50',   href: route('shortlist.index') },
    { lk: 'dash_stat_views',          value: stats.profile_views,       icon: Eye,            color: 'text-violet-600',  bg: 'bg-violet-50',  href: route('profile.who-viewed') },
  ]

  return (
    <div className="max-w-6xl mx-auto space-y-6">

      {/* Welcome strip */}
      <div className="flex items-start justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">
            {t('dashboard', 'dash_greeting', { name: user.name.split(' ')[0] || user.name })} 👋
          </h1>
          <p className="text-sm text-slate-500 mt-0.5">
            {new Date().toLocaleDateString('en-BD', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
          </p>
        </div>
        {user.membership_status !== 'active' && (
          <Link href={route('upgrade.plans')}>
            <Button variant="premium" size="sm">
              ✦ {t('dashboard', 'dash_upgrade_btn')}
            </Button>
          </Link>
        )}
      </div>

      {/* Trust badges */}
      {hasTrustBadge && (
        <div className="flex flex-wrap gap-2">
          {user.is_email_verified && (
            <span className="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs px-3 py-1 font-medium">
              <Mail size={11} />
              {t('dashboard', 'trust_email_verified')}
            </span>
          )}
          {user.biodata_status === 'approved' && (
            <span className="inline-flex items-center gap-1.5 rounded-full bg-violet-50 border border-violet-200 text-violet-700 text-xs px-3 py-1 font-medium">
              <CheckCircle2 size={11} />
              {t('dashboard', 'trust_profile_approved')}
            </span>
          )}
          {user.membership_status === 'active' && (
            <span className="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs px-3 py-1 font-medium">
              <Star size={11} className="fill-amber-500" />
              {t('dashboard', 'trust_premium')}
            </span>
          )}
        </div>
      )}

      {/* Verification nudge */}
      {!is_verified && (
        <div className="rounded-2xl border border-blue-200 bg-blue-50 p-4 flex items-center gap-4">
          <CheckCircle2 size={24} className="text-blue-600 shrink-0" />
          <div className="flex-1">
            <p className="text-sm font-semibold text-blue-900">{t('dashboard', 'dash_verify_title')}</p>
            <p className="text-xs text-blue-700 mt-0.5">{t('dashboard', 'dash_verify_desc')}</p>
          </div>
          <Link href={route('verify.identity')}>
            <Button size="sm">{t('dashboard', 'dash_verify_btn')}</Button>
          </Link>
        </div>
      )}

      {/* Unread messages nudge */}
      {stats.messages_unread > 0 && (
        <div className="rounded-2xl border border-emerald-200 bg-emerald-50 p-3 flex items-center gap-4">
          <MessageCircle size={20} className="text-emerald-600 shrink-0" />
          <p className="flex-1 text-sm font-semibold text-emerald-900">
            {t('dashboard', 'dash_nudge_messages', { count: stats.messages_unread })}
          </p>
          <Link href={route('inbox.index')}>
            <Button size="sm" className="shrink-0">{t('dashboard', 'dash_go_inbox')}</Button>
          </Link>
        </div>
      )}

      {/* Pending interests nudge */}
      {stats.interests_received > 0 && (
        <div className="rounded-2xl border border-rose-200 bg-rose-50 p-3 flex items-center gap-4">
          <Heart size={20} className="text-rose-600 shrink-0" />
          <p className="flex-1 text-sm font-semibold text-rose-900">
            {t('dashboard', 'dash_nudge_interests', { count: stats.interests_received })}
          </p>
          <Link href={route('interests.received')}>
            <Button size="sm" variant="outline" className="border-rose-300 text-rose-700 hover:bg-rose-100 shrink-0">
              {t('dashboard', 'dash_go_interests')}
            </Button>
          </Link>
        </div>
      )}

      {/* Quick access cards */}
      <section>
        <h2 className="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
          {t('dashboard', 'dash_quick_title')}
        </h2>
        <div className="grid grid-cols-4 gap-2 sm:grid-cols-8">
          {quickActions.map(item => (
            <Link
              key={item.lk}
              href={item.href}
              className="group flex flex-col items-center gap-1.5 rounded-2xl p-2 hover:bg-white hover:shadow-sm transition-all"
            >
              <div className={cn('relative h-11 w-11 rounded-xl flex items-center justify-center', item.bg)}>
                <item.icon size={20} className={item.color} />
                {item.badge > 0 && (
                  <span className="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-[10px] font-bold text-white flex items-center justify-center">
                    {item.badge > 9 ? '9+' : item.badge}
                  </span>
                )}
              </div>
              <span className="text-[10px] font-medium text-center text-slate-600 group-hover:text-slate-900 leading-tight w-full truncate">
                {t('dashboard', item.lk)}
              </span>
            </Link>
          ))}
        </div>
      </section>

      {/* Stats row — 6 stats, 2 cols mobile → 3 cols tablet */}
      <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
        {statItems.map(s => (
          <Link key={s.lk} href={s.href}>
            <Card className="p-4 hover:shadow-md transition-shadow h-full">
              <div className={`inline-flex h-9 w-9 items-center justify-center rounded-xl ${s.bg} mb-3`}>
                <s.icon size={18} className={s.color} />
              </div>
              <p className="text-2xl font-bold text-slate-900">{s.value}</p>
              <p className="text-xs text-slate-500 mt-0.5">{t('dashboard', s.lk)}</p>
            </Card>
          </Link>
        ))}
      </div>

      {/* Daily picks */}
      <section>
        <div className="flex items-center justify-between mb-4">
          <div className="flex items-center gap-2">
            <Sparkles size={18} className="text-blue-600" />
            <h2 className="font-bold text-slate-900">{t('dashboard', 'dash_picks_title')}</h2>
            <Badge variant="default" size="sm">{t('dashboard', 'dash_picks_badge')}</Badge>
          </div>
          <Link href={route('matches.index')} className="text-sm text-primary-600 hover:underline flex items-center gap-1">
            {t('dashboard', 'dash_see_all')} <ArrowRight size={14} />
          </Link>
        </div>

        {daily_picks.length > 0 ? (
          <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            {daily_picks.map(p => (
              <ProfileCard key={p.registration_id} profile={p} />
            ))}
          </div>
        ) : (
          <Card>
            <CardBody className="text-center py-12">
              <Sparkles size={32} className="mx-auto text-slate-300 mb-3" />
              <p className="text-sm font-medium text-slate-500">{t('dashboard', 'dash_picks_empty')}</p>
              <Link href={route('biodata.wizard')} className="mt-3 inline-block">
                <Button size="sm" className="mt-2">{t('dashboard', 'dash_picks_empty_cta')}</Button>
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
              <h2 className="font-bold text-slate-900">{t('dashboard', 'dash_visitors_title')}</h2>
            </div>
            <Link href={route('profile.who-viewed')} className="text-sm text-primary-600 hover:underline flex items-center gap-1">
              {t('dashboard', 'dash_see_all')} <ArrowRight size={14} />
            </Link>
          </div>
          <div className="flex gap-3 overflow-x-auto pb-2">
            {recent_visitors.map(v => (
              <Link key={v.registration_id} href={route('profile.show', { registrationId: v.registration_id })} className="shrink-0 group">
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
  )
}
