/// <reference path="../../types/ziggy.d.ts" />
import { Head, router, useForm } from '@inertiajs/react'
import { useState } from 'react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { CheckCircle, AlertCircle, Clock, Star } from 'lucide-react'
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
  membershipStatus: string
  membershipExpires: string | null
  pendingPayment: PendingPayment | null
}

export default function Plans({
  plans, gateways, currentPlan, membershipStatus, membershipExpires, pendingPayment,
}: Props) {
  const { t } = useTranslation()
  const [selectedPlan, setSelectedPlan] = useState<Plan | null>(null)
  const [selectedGateway, setSelectedGateway] = useState<Gateway | null>(
    gateways.length === 1 ? gateways[0]! : null,
  )

  const { post, processing } = useForm({})

  function handleCheckout() {
    if (!selectedPlan || !selectedGateway) return
    router.post(route('upgrade.checkout'), {
      plan_id:    selectedPlan.id,
      gateway_id: selectedGateway.id,
    })
  }

  const isActive = membershipStatus === 'active'

  return (
    <AppLayout>
      <Head title={t('pricing', 'page_title')} />

      <div className="max-w-5xl mx-auto">
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
                {currentPlan} — Active membership
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
                Complete Payment
              </Button>
            )}
          </div>
        )}

        {/* Plans grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
          {plans.map(plan => {
            const isSelected = selectedPlan?.id === plan.id
            return (
              <button
                key={plan.id}
                type="button"
                onClick={() => setSelectedPlan(isSelected ? null : plan)}
                className={cn(
                  'relative rounded-3xl border-2 p-6 text-left transition-all duration-200 focus:outline-none',
                  isSelected
                    ? 'border-primary-600 shadow-lg shadow-primary-100 bg-primary-50/30'
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
                    <CheckCircle size={12} className="text-white" />
                  </div>
                )}

                <h3 className="font-bold text-slate-900 text-base mb-0.5">{plan.name}</h3>
                <p className="text-xs text-slate-400 mb-4">
                  {plan.duration_months} {plan.duration_months === 1 ? 'month' : 'months'}
                </p>

                <div className="flex items-baseline gap-1 mb-5">
                  <span className={cn(
                    'text-3xl font-extrabold',
                    isSelected ? 'text-primary-600' : 'text-slate-900',
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

        {/* Gateway selection */}
        {gateways.length > 1 && (
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

        {/* Checkout button */}
        <div className="flex flex-col items-center gap-3">
          {!selectedPlan && (
            <p className="text-sm text-slate-400 flex items-center gap-1.5">
              <AlertCircle size={14} />
              Select a plan above to continue
            </p>
          )}
          <Button
            size="lg"
            variant="premium"
            disabled={!selectedPlan || !selectedGateway || processing}
            isLoading={processing}
            onClick={handleCheckout}
            className="min-w-48"
          >
            {selectedPlan
              ? `${t('pricing', 'get_started')} — ৳${selectedPlan.price.toLocaleString('en-BD')}`
              : t('pricing', 'get_started')}
          </Button>
          <p className="text-xs text-slate-400">{t('pricing', 'unlock_title')}</p>
        </div>
      </div>
    </AppLayout>
  )
}
