/// <reference path="../../types/ziggy.d.ts" />
import { Head, router } from '@inertiajs/react'
import { useState, type ReactNode } from 'react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { CheckCircle, AlertCircle, Clock, Star, Check, X } from 'lucide-react'
import { cn } from '@/lib/utils'

interface Plan {
  id: number
  name: string
  slug: string
  duration_months: number
  price: number
  currency: string
  badge: string | null
  is_popular: boolean
  features: string[] | null
  priority_placement: boolean
  family_support: boolean
  contact_view_limit: number
  message_limit: number
  profile_boost_hours: number
}

interface Gateway {
  id: number
  name: string
  slug: string
  type: string
  merchant_id: string | null
  instructions: string | null
}

interface PendingPayment {
  transaction_no: string
  plan_name: string
  amount: number
  is_submitted: boolean
}

interface Props {
  plans: Plan[]
  gateways: Gateway[]
  currentPlan: string | null
  currentPlanId: number | null
  membershipStatus: string
  membershipExpires: string | null
  pendingPayment: PendingPayment | null
}

function getCtaLabel(
  plan: Plan,
  currentPlanId: number | null,
  membershipStatus: string,
  t: (ns: string, k: string) => string,
): string {
  if (plan.id === currentPlanId && membershipStatus === 'active') return t('pricing', 'cta_renew_plan')
  if (membershipStatus === 'active') return t('pricing', 'cta_upgrade_now')
  return t('pricing', 'cta_choose_plan')
}

export default function Plans({
  plans, gateways, currentPlan, currentPlanId, membershipStatus, membershipExpires, pendingPayment,
}: Props) {
  const { t } = useTranslation()
  const [selectedPlan, setSelectedPlan] = useState<Plan | null>(null)
  const [selectedGateway, setSelectedGateway] = useState<Gateway | null>(
    gateways.length === 1 ? gateways[0]! : null,
  )
  const [processing, setProcessing] = useState(false)

  function handleCheckout() {
    if (!selectedPlan || !selectedGateway || processing) return
    setProcessing(true)
    router.post(route('upgrade.checkout'), {
      plan_id:    selectedPlan.id,
      gateway_id: selectedGateway.id,
    }, { onError: () => setProcessing(false) })
  }

  const isActive = membershipStatus === 'active'

  return (
    <AppLayout>
      <Head title={t('pricing', 'page_title')} />

      <div className="max-w-5xl mx-auto px-4 py-8">
        {/* Header */}
        <div className="text-center mb-10">
          <Star className="mx-auto text-amber-400 mb-3" size={32} />
          <h1 className="text-2xl font-extrabold text-slate-900 mb-2">{t('pricing', 'page_title')}</h1>
          <p className="text-slate-500">{t('pricing', 'page_subtitle')}</p>
        </div>

        {/* Active membership banner */}
        {isActive && (
          <div className="mb-8 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 flex items-center gap-3">
            <CheckCircle size={20} className="text-emerald-600 shrink-0" />
            <div>
              <p className="text-sm font-semibold text-emerald-800">
                {currentPlan} — {t('pricing', 'cta_current_plan')}
              </p>
              {membershipExpires && (
                <p className="text-xs text-emerald-600 mt-0.5">
                  {t('dashboard', 'plan_expires', { date: membershipExpires })}
                </p>
              )}
            </div>
          </div>
        )}

        {/* Pending payment banner */}
        {pendingPayment && (
          <div className="mb-8 rounded-2xl bg-amber-50 border border-amber-200 px-5 py-4 flex items-center gap-3">
            <Clock size={20} className="text-amber-600 shrink-0" />
            <div className="flex-1 min-w-0">
              <p className="text-sm font-semibold text-amber-800">
                {t('pricing', 'payment_pending')}
              </p>
              <p className="text-xs text-amber-600 mt-0.5">
                {pendingPayment.plan_name} — ৳{pendingPayment.amount.toLocaleString('en-BD')}
              </p>
            </div>
            {!pendingPayment.is_submitted && (
              <Button
                size="sm"
                variant="outline"
                onClick={() => router.get(route('upgrade.manual', pendingPayment.transaction_no))}
              >
                {t('pricing', 'complete_payment')}
              </Button>
            )}
          </div>
        )}

        {plans.length === 0 ? (
          <div className="text-center py-16 text-slate-400">
            <Star size={36} className="mx-auto mb-4 opacity-40" />
            <p>{t('pricing', 'plans_empty')}</p>
          </div>
        ) : (
          <>
            {/* Plans grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
              {plans.map(plan => {
                const isSelected = selectedPlan?.id === plan.id
                const isCurrentActive = plan.id === currentPlanId && membershipStatus === 'active'

                return (
                  <button
                    key={plan.id}
                    type="button"
                    onClick={() => setSelectedPlan(isSelected ? null : plan)}
                    className={cn(
                      'relative rounded-3xl border-2 p-6 text-left transition-all duration-200 focus:outline-none',
                      isSelected
                        ? 'border-primary-600 shadow-lg shadow-primary-100 bg-primary-50/30'
                        : isCurrentActive
                        ? 'border-emerald-400 bg-emerald-50/20 hover:border-emerald-500'
                        : plan.is_popular
                        ? 'border-amber-300 hover:border-amber-400'
                        : 'border-slate-200 hover:border-slate-300',
                    )}
                  >
                    {plan.badge && (
                      <div className={cn(
                        'absolute -top-3 left-1/2 -translate-x-1/2 text-white text-xs font-bold px-3 py-1 rounded-full',
                        plan.is_popular ? 'bg-amber-500' : 'bg-primary-600',
                      )}>
                        {plan.badge}
                      </div>
                    )}

                    {isSelected && (
                      <div className="absolute top-4 right-4 h-5 w-5 rounded-full bg-primary-600 flex items-center justify-center">
                        <Check size={12} className="text-white" />
                      </div>
                    )}

                    {isCurrentActive && !isSelected && (
                      <div className="absolute top-4 right-4">
                        <span className="text-[10px] font-semibold text-emerald-600 bg-emerald-100 rounded-full px-2 py-0.5">
                          {t('pricing', 'cta_current_plan')}
                        </span>
                      </div>
                    )}

                    <h3 className="font-bold text-slate-900 text-base mb-0.5">{plan.name}</h3>
                    <p className="text-xs text-slate-400 mb-4">
                      {plan.duration_months}{' '}
                      {plan.duration_months === 1 ? 'month' : 'months'}
                    </p>

                    <div className="flex items-baseline gap-1 mb-5">
                      <span className={cn(
                        'text-3xl font-extrabold',
                        isSelected ? 'text-primary-600' : isCurrentActive ? 'text-emerald-600' : 'text-slate-900',
                      )}>
                        ৳{plan.price.toLocaleString('en-BD')}
                      </span>
                      <span className="text-xs text-slate-400">
                        / {plan.duration_months === 1 ? 'month' : `${plan.duration_months} mo`}
                      </span>
                    </div>

                    {Array.isArray(plan.features) && plan.features.length > 0 && (
                      <ul className="space-y-2">
                        {plan.features.slice(0, 5).map((f, i) => (
                          <li key={i} className="flex items-start gap-2 text-xs text-slate-600">
                            <CheckCircle size={13} className="text-emerald-500 shrink-0 mt-0.5" />
                            {f}
                          </li>
                        ))}
                      </ul>
                    )}
                  </button>
                )
              })}
            </div>

            {/* Feature comparison matrix */}
            <div className="mb-8 rounded-2xl border border-slate-200 bg-white overflow-hidden">
              <div className="px-5 py-3 border-b border-slate-100 bg-slate-50">
                <h2 className="text-sm font-semibold text-slate-700">{t('pricing', 'matrix_title')}</h2>
              </div>
              <div className="overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-100">
                      <th className="text-left px-5 py-3 text-xs font-semibold text-slate-500 min-w-[160px]">
                        {t('pricing', 'matrix_feature')}
                      </th>
                      {plans.map(p => (
                        <th
                          key={p.id}
                          className={cn(
                            'px-3 py-3 text-center text-xs font-semibold min-w-[90px]',
                            p.id === currentPlanId && membershipStatus === 'active'
                              ? 'text-emerald-700'
                              : 'text-slate-500',
                          )}
                        >
                          {p.name}
                          {p.id === currentPlanId && membershipStatus === 'active' && (
                            <span className="block text-[10px] font-normal text-emerald-500 normal-case">
                              ({t('pricing', 'cta_current_plan')})
                            </span>
                          )}
                        </th>
                      ))}
                    </tr>
                  </thead>
                  <tbody>
                    <MatrixRow
                      label={t('pricing', 'feature_contacts')}
                      plans={plans}
                      render={p =>
                        p.contact_view_limit === 0
                          ? <span className="text-emerald-600 font-semibold text-xs">{t('pricing', 'unlimited')}</span>
                          : <span className="text-xs font-medium text-slate-700">{p.contact_view_limit}</span>
                      }
                    />
                    <MatrixRow
                      label={t('pricing', 'feature_messages')}
                      plans={plans}
                      render={p =>
                        p.message_limit === 0
                          ? <span className="text-emerald-600 font-semibold text-xs">{t('pricing', 'unlimited')}</span>
                          : <span className="text-xs font-medium text-slate-700">{p.message_limit}</span>
                      }
                    />
                    <MatrixRow
                      label={t('pricing', 'feature_boost_hours')}
                      plans={plans}
                      render={p =>
                        p.profile_boost_hours > 0
                          ? <span className="text-xs font-medium text-slate-700">{p.profile_boost_hours}h</span>
                          : <X size={13} className="mx-auto text-slate-300" />
                      }
                    />
                    <MatrixRow
                      label={t('pricing', 'feature_priority_placement')}
                      plans={plans}
                      render={p =>
                        p.priority_placement
                          ? <Check size={14} className="mx-auto text-emerald-500" />
                          : <X size={13} className="mx-auto text-slate-300" />
                      }
                    />
                    <MatrixRow
                      label={t('pricing', 'feature_family_support')}
                      plans={plans}
                      render={p =>
                        p.family_support
                          ? <Check size={14} className="mx-auto text-emerald-500" />
                          : <X size={13} className="mx-auto text-slate-300" />
                      }
                    />
                  </tbody>
                </table>
              </div>
            </div>

            {/* Gateway selection (visible only when plan selected + multiple gateways) */}
            {selectedPlan && gateways.length > 1 && (
              <div className="mb-6">
                <p className="text-sm font-medium text-slate-700 mb-3">
                  {t('pricing', 'payment_method')}
                </p>
                <div className="flex flex-wrap gap-3">
                  {gateways.map(gw => (
                    <button
                      key={gw.id}
                      type="button"
                      onClick={() => setSelectedGateway(gw)}
                      className={cn(
                        'flex items-center gap-2 rounded-xl border-2 px-4 py-2.5 text-sm font-medium transition-colors',
                        selectedGateway?.id === gw.id
                          ? 'border-primary-600 bg-primary-50 text-primary-700'
                          : 'border-slate-200 text-slate-600 hover:border-slate-300',
                      )}
                    >
                      {gw.name}
                    </button>
                  ))}
                </div>
              </div>
            )}

            {/* Checkout CTA */}
            <div className="flex flex-col items-center gap-3">
              {!selectedPlan ? (
                <p className="text-sm text-slate-400 flex items-center gap-1.5">
                  <AlertCircle size={14} />
                  {t('pricing', 'select_plan_hint')}
                </p>
              ) : (
                <Button
                  size="lg"
                  variant="premium"
                  disabled={!selectedGateway || processing}
                  isLoading={processing}
                  onClick={handleCheckout}
                  className="min-w-56"
                >
                  {getCtaLabel(selectedPlan, currentPlanId, membershipStatus, t)}
                  {' — ৳'}{selectedPlan.price.toLocaleString('en-BD')}
                </Button>
              )}
            </div>
          </>
        )}
      </div>
    </AppLayout>
  )
}

function MatrixRow({
  label,
  plans,
  render,
}: {
  label: string
  plans: Plan[]
  render: (p: Plan) => ReactNode
}) {
  return (
    <tr className="border-b border-slate-50 last:border-0 hover:bg-slate-50/50">
      <td className="px-5 py-3 text-xs text-slate-600 font-medium">{label}</td>
      {plans.map(p => (
        <td key={p.id} className="px-3 py-3 text-center">
          {render(p)}
        </td>
      ))}
    </tr>
  )
}
