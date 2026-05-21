import { Head, Link, useForm } from '@inertiajs/react'
import GuestLayout from '@/layouts/GuestLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { useState } from 'react'
import { Shield, CheckCircle } from 'lucide-react'
import { cn } from '@/lib/utils'

type Step = 1 | 2 | 3

const STEPS = [
  { number: 1, label: 'Account' },
  { number: 2, label: 'Profile' },
  { number: 3, label: 'Mode' },
]

export default function Register() {
  const [step, setStep] = useState<Step>(1)

  const { data, setData, post, processing, errors } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    gender: '' as 'male' | 'female',
    profile_created_for: 'self',
    platform_mode: 'general' as 'general' | 'islamic',
    terms_accepted: false,
  })

  const next = () => setStep(s => Math.min(3, s + 1) as Step)
  const back = () => setStep(s => Math.max(1, s - 1) as Step)

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    if (step < 3) { next(); return }
    post(route('register'))
  }

  return (
    <GuestLayout title="Create your account">
      <Head title="Register" />

      {/* Step indicator */}
      <div className="flex items-center justify-center gap-2 mb-8">
        {STEPS.map((s, i) => (
          <div key={s.number} className="flex items-center gap-2">
            <div
              className={cn(
                'h-8 w-8 rounded-full flex items-center justify-center text-sm font-bold transition-all',
                step > s.number
                  ? 'bg-emerald-500 text-white'
                  : step === s.number
                    ? 'bg-primary-600 text-white'
                    : 'bg-slate-200 text-slate-400',
              )}
            >
              {step > s.number ? <CheckCircle size={16} /> : s.number}
            </div>
            <span className={cn('text-xs font-medium hidden sm:block', step === s.number ? 'text-slate-900' : 'text-slate-400')}>
              {s.label}
            </span>
            {i < STEPS.length - 1 && (
              <div className={cn('w-8 h-0.5', step > s.number ? 'bg-emerald-400' : 'bg-slate-200')} />
            )}
          </div>
        ))}
      </div>

      <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
        <form onSubmit={submit} className="space-y-5">
          {/* Step 1: Account */}
          {step === 1 && (
            <>
              <Input
                label="Full Name"
                value={data.name}
                onChange={e => setData('name', e.target.value)}
                error={errors.name}
                placeholder="Your full name"
                required
                autoFocus
              />
              <Input
                label="Email address"
                type="email"
                value={data.email}
                onChange={e => setData('email', e.target.value)}
                error={errors.email}
                placeholder="you@example.com"
                required
              />
              <Input
                label="Password"
                type="password"
                value={data.password}
                onChange={e => setData('password', e.target.value)}
                error={errors.password}
                placeholder="Min 8 characters"
                required
              />
              <Input
                label="Confirm password"
                type="password"
                value={data.password_confirmation}
                onChange={e => setData('password_confirmation', e.target.value)}
                error={errors.password_confirmation}
                placeholder="Repeat password"
                required
              />
              <Button type="submit" className="w-full" size="lg">
                Continue →
              </Button>
            </>
          )}

          {/* Step 2: Profile */}
          {step === 2 && (
            <>
              <div>
                <p className="text-sm font-medium text-slate-700 mb-2">
                  I am a <span className="text-red-500">*</span>
                </p>
                <div className="grid grid-cols-2 gap-3">
                  {(['male', 'female'] as const).map(g => (
                    <button
                      key={g}
                      type="button"
                      onClick={() => setData('gender', g)}
                      className={cn(
                        'rounded-xl border-2 py-4 text-sm font-semibold transition-all',
                        data.gender === g
                          ? 'border-primary-600 bg-primary-50 text-primary-700'
                          : 'border-slate-200 text-slate-600 hover:border-slate-300',
                      )}
                    >
                      {g === 'male' ? '👨 Groom' : '👩 Bride'}
                    </button>
                  ))}
                </div>
                {errors.gender && <p className="mt-1 text-xs text-red-600">{errors.gender}</p>}
              </div>

              <div>
                <p className="text-sm font-medium text-slate-700 mb-2">Profile is for</p>
                <select
                  value={data.profile_created_for}
                  onChange={e => setData('profile_created_for', e.target.value)}
                  className="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
                  <option value="self">Myself</option>
                  <option value="son">My Son</option>
                  <option value="daughter">My Daughter</option>
                  <option value="brother">My Brother</option>
                  <option value="sister">My Sister</option>
                  <option value="relative">A Relative</option>
                </select>
              </div>

              <div className="flex gap-3">
                <Button type="button" variant="outline" className="flex-1" onClick={back}>
                  ← Back
                </Button>
                <Button type="submit" className="flex-1" disabled={!data.gender}>
                  Continue →
                </Button>
              </div>
            </>
          )}

          {/* Step 3: Mode selection */}
          {step === 3 && (
            <>
              <p className="text-sm font-medium text-slate-700 mb-1">Choose your experience</p>

              <div className="grid grid-cols-1 gap-4">
                {([
                  {
                    mode: 'general' as const,
                    icon: '🌐',
                    label: 'General Mode',
                    description: 'Browse freely, photos visible, message after mutual interest.',
                    color: 'border-blue-200 hover:border-blue-400',
                    selected: 'border-blue-600 bg-blue-50',
                    badge: null as string | null,
                  },
                  {
                    mode: 'islamic' as const,
                    icon: '☪️',
                    label: 'Islamic / Halal Mode',
                    description: 'Biodata-first, photos blurred, Wali/Guardian notified on every request.',
                    color: 'border-emerald-200 hover:border-emerald-400',
                    selected: 'border-emerald-600 bg-emerald-50',
                    badge: 'Most Chosen' as string | null,
                  },
                ]).map(m => (
                  <button
                    key={m.mode}
                    type="button"
                    onClick={() => setData('platform_mode', m.mode)}
                    className={cn(
                      'relative rounded-2xl border-2 p-5 text-left transition-all',
                      data.platform_mode === m.mode ? m.selected : m.color,
                    )}
                  >
                    {m.badge && (
                      <span className="absolute -top-2.5 right-4 rounded-full bg-emerald-500 px-3 py-0.5 text-xs font-bold text-white">
                        ★ {m.badge}
                      </span>
                    )}
                    <div className="text-2xl mb-2">{m.icon}</div>
                    <p className="font-bold text-slate-900">{m.label}</p>
                    <p className="text-xs text-slate-500 mt-1">{m.description}</p>
                  </button>
                ))}
              </div>

              <label className="flex items-start gap-3 cursor-pointer">
                <input
                  type="checkbox"
                  checked={data.terms_accepted}
                  onChange={e => setData('terms_accepted', e.target.checked)}
                  className="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                />
                <span className="text-sm text-slate-600">
                  I agree to the{' '}
                  <Link href="/terms" className="text-primary-600 hover:underline" target="_blank">
                    Terms of Service
                  </Link>{' '}
                  and{' '}
                  <Link href="/privacy" className="text-primary-600 hover:underline" target="_blank">
                    Privacy Policy
                  </Link>
                </span>
              </label>

              <div className="flex gap-3">
                <Button type="button" variant="outline" className="flex-1" onClick={back}>
                  ← Back
                </Button>
                <Button
                  type="submit"
                  className="flex-1"
                  size="lg"
                  isLoading={processing}
                  disabled={!data.terms_accepted}
                >
                  Create Account
                </Button>
              </div>
            </>
          )}
        </form>
      </div>

      <p className="mt-4 text-center text-sm text-slate-500">
        Already have an account?{' '}
        <Link href={route('login')} className="font-semibold text-primary-600 hover:underline">
          Sign in
        </Link>
      </p>
    </GuestLayout>
  )
}
