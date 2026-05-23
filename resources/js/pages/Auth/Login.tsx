/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link, useForm, usePage } from '@inertiajs/react'
import GuestLayout from '@/layouts/GuestLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { Shield, CheckCircle2, Crown } from 'lucide-react'
import type { PageProps } from '@/types'
import { useTranslation } from '@/lib/i18n'

interface Props {
  canResetPassword: boolean
  status?: string
}

export default function Login({ canResetPassword, status }: Props) {
  const { t } = useTranslation()
  const { flash, googleEnabled } = usePage<PageProps & { googleEnabled: boolean }>().props

  const { data, setData, post, processing, errors } = useForm({
    email: '',
    password: '',
    remember: false,
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('login'))
  }

  const loginTrustPoints = [
    t('auth', 'trust_f4'),
    t('auth', 'trust_f3'),
    t('auth', 'trust_f2'),
  ]

  const panel = (
    <div className="space-y-7 px-2">
      {/* Brand mark */}
      <div className="flex items-center gap-3">
        <div className="h-11 w-11 rounded-2xl bg-gradient-to-br from-primary-600 to-violet-600 flex items-center justify-center">
          <Crown size={22} className="text-white" />
        </div>
        <span className="font-bold text-slate-900 text-xl tracking-tight">HeavenlyMatch</span>
      </div>

      {/* Heading */}
      <div>
        <h2 className="text-3xl font-bold text-slate-900 leading-tight mb-3">
          {t('auth', 'login_trust_title')}
        </h2>
        <p className="text-slate-600 leading-relaxed text-lg">
          {t('auth', 'login_trust_subtitle')}
        </p>
      </div>

      {/* Trust points */}
      <ul className="space-y-3">
        {loginTrustPoints.map(feature => (
          <li key={feature} className="flex items-center gap-3">
            <div className="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
              <CheckCircle2 size={14} className="text-blue-600" />
            </div>
            <span className="text-slate-700 font-medium text-sm">{feature}</span>
          </li>
        ))}
      </ul>

      {/* Stats */}
      <div className="rounded-2xl bg-gradient-to-br from-emerald-50 to-blue-50 border border-emerald-100 p-5">
        <div className="grid grid-cols-2 gap-5">
          {[
            { value: '50,000+', label: 'Members' },
            { value: '2,000+',  label: 'Marriages' },
            { value: '99.9%',   label: 'Uptime' },
            { value: '4.8/5',   label: 'Rating' },
          ].map(s => (
            <div key={s.label}>
              <p className="text-xl font-bold text-primary-700">{s.value}</p>
              <p className="text-xs text-slate-500 mt-0.5">{s.label}</p>
            </div>
          ))}
        </div>
      </div>

      {/* Privacy notice */}
      <div className="flex items-center gap-2 text-xs text-slate-400">
        <Shield size={13} />
        {t('auth', 'login_privacy_notice')}
      </div>
    </div>
  )

  return (
    <GuestLayout panel={panel}>
      <Head title="Login" />

      {status && (
        <div className="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
          {status}
        </div>
      )}

      {flash.error && (
        <div className="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
          {flash.error}
        </div>
      )}

      <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
        <h1 className="text-xl font-bold text-slate-900 mb-6">{t('auth', 'login_title')}</h1>

        <form onSubmit={submit} className="space-y-5">
          <Input
            label={t('auth', 'field_email')}
            type="email"
            value={data.email}
            onChange={e => setData('email', e.target.value)}
            error={errors.email}
            autoFocus
            required
            placeholder={t('auth', 'field_email_ph')}
          />

          <Input
            label={t('auth', 'field_password')}
            type="password"
            value={data.password}
            onChange={e => setData('password', e.target.value)}
            error={errors.password}
            required
            placeholder="••••••••"
          />

          <div className="flex items-center justify-between">
            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                checked={data.remember}
                onChange={e => setData('remember', e.target.checked)}
                className="rounded border-slate-300 text-primary-600 focus:ring-primary-500"
              />
              <span className="text-sm text-slate-600">{t('auth', 'remember_me')}</span>
            </label>

            {canResetPassword && (
              <Link
                href={route('password.request')}
                className="text-sm text-primary-600 hover:underline"
              >
                {t('auth', 'forgot_password')}
              </Link>
            )}
          </div>

          <Button type="submit" className="w-full" size="lg" isLoading={processing}>
            {t('auth', 'login_button')}
          </Button>
        </form>

        {googleEnabled && (
          <>
            <div className="mt-6 flex items-center gap-3">
              <div className="flex-1 h-px bg-slate-200" />
              <span className="text-xs text-slate-400">or continue with</span>
              <div className="flex-1 h-px bg-slate-200" />
            </div>

            <a
              href={route('auth.google')}
              className="mt-4 flex w-full items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
            >
              <svg className="h-4 w-4" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              {t('auth', 'login_google')}
            </a>
          </>
        )}
      </div>

      <p className="mt-6 text-center text-sm text-slate-500">
        {t('auth', 'no_account')}{' '}
        <Link href={route('register')} className="font-semibold text-primary-600 hover:underline">
          {t('auth', 'register_link')}
        </Link>
      </p>
    </GuestLayout>
  )
}
