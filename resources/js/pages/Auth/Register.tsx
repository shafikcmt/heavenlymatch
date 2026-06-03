/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link, useForm, usePage } from '@inertiajs/react'
import axios from 'axios'
import GuestLayout from '@/layouts/GuestLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { useState, useEffect } from 'react'
import { CheckCircle, CheckCircle2, Crown, ShieldCheck, AlertCircle, Loader2 } from 'lucide-react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import type { PageProps } from '@/types'

type Step = 1 | 2 | 3

export default function Register() {
  const { t } = useTranslation()
  const { googleEnabled, facebookEnabled, requirePhoneVerification = true } = usePage<
    PageProps & { googleEnabled: boolean; facebookEnabled: boolean; requirePhoneVerification?: boolean }
  >().props
  const [step, setStep] = useState<Step>(1)

  // ── Mobile OTP verification state ──────────────────────────────────────────
  const [otpSent, setOtpSent] = useState(false)
  const [otpVerified, setOtpVerified] = useState(false)
  const [otp, setOtp] = useState('')
  const [sending, setSending] = useState(false)
  const [verifying, setVerifying] = useState(false)
  const [otpError, setOtpError] = useState('')
  const [otpInfo, setOtpInfo] = useState('')
  const [resendIn, setResendIn] = useState(0)

  // ── Email availability state ────────────────────────────────────────────────
  type EmailStatus = 'idle' | 'checking' | 'available' | 'taken' | 'invalid'
  const [emailStatus, setEmailStatus] = useState<EmailStatus>('idle')

  const { data, setData, post, processing, errors } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    gender: '' as 'male' | 'female',
    profile_created_for: 'self',
    mobile_number: '',
    platform_mode: 'general' as 'general' | 'islamic',
    terms_accepted: false,
  })

  // Prefill from URL query params (set by homepage quick registration form)
  useEffect(() => {
    const params = new URLSearchParams(window.location.search)
    const prefillName = params.get('name')
    const prefillFor = params.get('profile_created_for')
    const prefillMobile = params.get('mobile_number')
    const validFor = ['self', 'son', 'daughter', 'brother', 'sister', 'relative']
    if (prefillName) setData('name', prefillName)
    if (prefillFor && validFor.includes(prefillFor)) setData('profile_created_for', prefillFor)
    if (prefillMobile) setData('mobile_number', prefillMobile)
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  const next = () => setStep(s => Math.min(3, s + 1) as Step)
  const back = () => setStep(s => Math.max(1, s - 1) as Step)

  // Resend countdown tick
  useEffect(() => {
    if (resendIn <= 0) return
    const id = setTimeout(() => setResendIn(s => s - 1), 1000)
    return () => clearTimeout(id)
  }, [resendIn])

  // ── Email availability (debounced live check) ───────────────────────────────
  const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

  // Returns true = available, false = taken, null = could not determine
  const checkEmailAvailable = async (email: string): Promise<boolean | null> => {
    try {
      const { data: res } = await axios.post(route('register.email.check'), { email })
      return !!res?.available
    } catch {
      return null
    }
  }

  useEffect(() => {
    const email = data.email.trim()
    if (!email) { setEmailStatus('idle'); return }
    if (!EMAIL_RE.test(email)) { setEmailStatus('invalid'); return }

    setEmailStatus('checking')
    const id = setTimeout(async () => {
      const ok = await checkEmailAvailable(email)
      // On a network error, stay neutral — the server still enforces uniqueness.
      if (ok === null) { setEmailStatus('idle'); return }
      setEmailStatus(ok ? 'available' : 'taken')
    }, 500)
    return () => clearTimeout(id)
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data.email])

  // Changing the number invalidates any prior OTP / verification
  const handleMobileChange = (value: string) => {
    setData('mobile_number', value)
    if (otpSent || otpVerified) {
      setOtpSent(false)
      setOtpVerified(false)
      setOtp('')
      setOtpError('')
      setOtpInfo('')
    }
  }

  const resetVerification = () => {
    setOtpSent(false)
    setOtpVerified(false)
    setOtp('')
    setOtpError('')
    setOtpInfo('')
    setResendIn(0)
  }

  const handleSendOtp = async () => {
    if (!data.mobile_number.trim()) {
      setOtpError(t('auth', 'mobile_required'))
      return
    }
    setOtpError(''); setOtpInfo(''); setSending(true)
    try {
      const { data: res } = await axios.post(route('register.phone.send-otp'), {
        mobile_number: data.mobile_number,
      })
      setOtpSent(true)
      setOtpInfo(res?.message ?? t('auth', 'otp_sent'))
      setResendIn(res?.retry_after ?? 60)
    } catch (e: any) {
      setOtpError(e?.response?.data?.message ?? t('auth', 'otp_send_failed'))
    } finally {
      setSending(false)
    }
  }

  const handleVerifyOtp = async () => {
    setOtpError(''); setOtpInfo(''); setVerifying(true)
    try {
      const { data: res } = await axios.post(route('register.phone.verify-otp'), {
        mobile_number: data.mobile_number,
        otp,
      })
      setOtpVerified(true)
      setOtpSent(false)
      setOtpInfo(res?.message ?? t('auth', 'otp_verified'))
    } catch (e: any) {
      setOtpError(e?.response?.data?.message ?? t('auth', 'otp_invalid'))
    } finally {
      setVerifying(false)
    }
  }

  const submit = async (e: React.FormEvent) => {
    e.preventDefault()

    // Step 1 cannot advance with an invalid or already-registered email.
    if (step === 1) {
      const email = data.email.trim()
      if (!EMAIL_RE.test(email)) { setEmailStatus('invalid'); return }
      if (emailStatus === 'taken') return
      // Confirm availability if the debounced check hasn't resolved yet.
      if (emailStatus !== 'available') {
        setEmailStatus('checking')
        const ok = await checkEmailAvailable(email)
        if (ok === false) { setEmailStatus('taken'); return }
        setEmailStatus(ok === true ? 'available' : 'idle')
      }
      next()
      return
    }

    // Step 2 cannot advance until the mobile number is OTP-verified
    // (only when phone verification is enabled by admin).
    if (step === 2 && requirePhoneVerification && !otpVerified) {
      setOtpError(t('auth', 'verify_phone_first'))
      return
    }
    if (step < 3) { next(); return }
    post(route('register'))
  }

  const STEPS = [
    { number: 1, label: t('auth', 'step_account') },
    { number: 2, label: t('auth', 'step_profile') },
    { number: 3, label: t('auth', 'step_mode') },
  ]

  const trustFeatures = [
    t('auth', 'trust_f1'),
    t('auth', 'trust_f2'),
    t('auth', 'trust_f3'),
    t('auth', 'trust_f4'),
    t('auth', 'trust_f5'),
  ]

  const panel = (
    <div className="space-y-7 px-2">
      {/* Brand + heading */}
      <div>
        <div className="flex items-center gap-3 mb-5">
          <div className="h-11 w-11 rounded-2xl bg-gradient-to-br from-primary-600 to-violet-600 flex items-center justify-center">
            <Crown size={22} className="text-white" />
          </div>
          <span className="font-bold text-slate-900 text-xl tracking-tight">HeavenlyMatch</span>
        </div>
        <h2 className="text-3xl font-bold text-slate-900 leading-tight mb-3">
          {t('auth', 'trust_title')}
        </h2>
        <p className="text-slate-600 leading-relaxed">{t('auth', 'trust_subtitle')}</p>
      </div>

      {/* Trust features */}
      <ul className="space-y-3">
        {trustFeatures.map(feature => (
          <li key={feature} className="flex items-center gap-3">
            <div className="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
              <CheckCircle2 size={14} className="text-emerald-600" />
            </div>
            <span className="text-slate-700 font-medium text-sm">{feature}</span>
          </li>
        ))}
      </ul>

      {/* Testimonial */}
      <div className="rounded-2xl bg-gradient-to-br from-primary-50 to-emerald-50 border border-primary-100 p-5">
        <p className="text-slate-700 italic text-sm leading-relaxed">
          {t('auth', 'trust_quote')}
        </p>
        <p className="text-xs text-slate-500 mt-2 font-medium">
          — {t('auth', 'trust_quote_author')}
        </p>
      </div>

      {/* Stats row */}
      <div className="grid grid-cols-3 gap-3">
        {[
          { value: '50K+', label: 'Members' },
          { value: '2K+',  label: 'Marriages' },
          { value: '4.8★', label: 'Rated' },
        ].map(s => (
          <div key={s.label} className="rounded-xl bg-white border border-slate-100 py-3 text-center shadow-sm">
            <p className="text-lg font-bold text-primary-700">{s.value}</p>
            <p className="text-xs text-slate-500 mt-0.5">{s.label}</p>
          </div>
        ))}
      </div>
    </div>
  )

  return (
    <GuestLayout panel={panel}>
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

          {/* ── Step 1: Account ── */}
          {step === 1 && (
            <>
              <Input
                label={t('auth', 'field_full_name')}
                value={data.name}
                onChange={e => setData('name', e.target.value)}
                error={errors.name}
                placeholder={t('auth', 'field_full_name_ph')}
                required
                autoFocus
              />
              <div>
                <Input
                  label={t('auth', 'field_email')}
                  type="email"
                  value={data.email}
                  onChange={e => setData('email', e.target.value)}
                  error={errors.email}
                  placeholder={t('auth', 'field_email_ph')}
                  className={cn(
                    !errors.email && (emailStatus === 'taken' || emailStatus === 'invalid') &&
                      'border-red-400 focus:border-red-500 focus:ring-red-500',
                    !errors.email && emailStatus === 'available' &&
                      'border-emerald-400 focus:border-emerald-500 focus:ring-emerald-500',
                  )}
                  required
                />

                {/* Live availability feedback (hidden when a server error is shown) */}
                {!errors.email && emailStatus === 'checking' && (
                  <p className="mt-1 flex items-center gap-1 text-xs text-slate-400">
                    <Loader2 size={12} className="animate-spin" /> {t('auth', 'email_checking')}
                  </p>
                )}
                {!errors.email && emailStatus === 'available' && (
                  <p className="mt-1 flex items-center gap-1 text-xs text-emerald-600">
                    <CheckCircle2 size={12} /> {t('auth', 'email_available')}
                  </p>
                )}
                {!errors.email && emailStatus === 'invalid' && (
                  <p className="mt-1 flex items-center gap-1 text-xs text-red-600">
                    <AlertCircle size={12} /> {t('auth', 'email_invalid')}
                  </p>
                )}
                {!errors.email && emailStatus === 'taken' && (
                  <div className="mt-1.5 rounded-xl border border-red-200 bg-red-50 px-3 py-2">
                    <p className="flex items-start gap-1.5 text-xs font-medium text-red-700">
                      <AlertCircle size={13} className="mt-px shrink-0" />
                      {t('auth', 'email_taken')}
                    </p>
                    <Link
                      href={route('login')}
                      className="mt-1 inline-block text-xs font-semibold text-primary-600 hover:underline"
                    >
                      {t('auth', 'email_taken_signin')}
                    </Link>
                  </div>
                )}
              </div>
              <Input
                label={t('auth', 'field_password')}
                type="password"
                value={data.password}
                onChange={e => setData('password', e.target.value)}
                error={errors.password}
                placeholder={t('auth', 'field_password_ph')}
                required
              />
              <Input
                label={t('auth', 'field_confirm_password')}
                type="password"
                value={data.password_confirmation}
                onChange={e => setData('password_confirmation', e.target.value)}
                error={errors.password_confirmation}
                placeholder={t('auth', 'field_confirm_ph')}
                required
              />
              <Button type="submit" className="w-full" size="lg" disabled={emailStatus === 'taken'}>
                {t('auth', 'btn_continue')} →
              </Button>
            </>
          )}

          {/* ── Step 2: Profile ── */}
          {step === 2 && (
            <>
              <div>
                <p className="text-sm font-medium text-slate-700 mb-2">
                  {t('auth', 'i_am_a')} <span className="text-red-500">*</span>
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
                      {g === 'male' ? t('auth', 'groom') : t('auth', 'bride')}
                    </button>
                  ))}
                </div>
                {errors.gender && <p className="mt-1 text-xs text-red-600">{errors.gender}</p>}
              </div>

              <div>
                <p className="text-sm font-medium text-slate-700 mb-2">{t('auth', 'profile_for')}</p>
                <select
                  value={data.profile_created_for}
                  onChange={e => setData('profile_created_for', e.target.value)}
                  className="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
                  <option value="self">{t('auth', 'for_self')}</option>
                  <option value="son">{t('auth', 'for_son')}</option>
                  <option value="daughter">{t('auth', 'for_daughter')}</option>
                  <option value="brother">{t('auth', 'for_brother')}</option>
                  <option value="sister">{t('auth', 'for_sister')}</option>
                  <option value="relative">{t('auth', 'for_relative')}</option>
                </select>
              </div>

              {/* ── Mobile number (+ OTP when verification is enabled) ── */}
              {requirePhoneVerification ? (
                <div className="space-y-2">
                  <div className="flex items-end gap-2">
                    <div className="flex-1">
                      <Input
                        label={t('auth', 'field_mobile')}
                        type="tel"
                        inputMode="tel"
                        autoComplete="tel"
                        value={data.mobile_number}
                        onChange={e => handleMobileChange(e.target.value)}
                        error={errors.mobile_number}
                        placeholder={t('auth', 'field_mobile_ph')}
                        disabled={otpVerified}
                        required
                      />
                    </div>
                    {!otpVerified && (
                      <Button
                        type="button"
                        variant="outline"
                        onClick={handleSendOtp}
                        disabled={sending || !data.mobile_number.trim() || resendIn > 0}
                        isLoading={sending}
                        className="mb-0.5 whitespace-nowrap"
                      >
                        {otpSent
                          ? (resendIn > 0 ? t('auth', 'otp_resend_in', { seconds: resendIn }) : t('auth', 'btn_resend_otp'))
                          : t('auth', 'btn_send_otp')}
                      </Button>
                    )}
                  </div>

                  {/* Verified badge */}
                  {otpVerified && (
                    <div className="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2">
                      <span className="flex items-center gap-2 text-sm font-medium text-emerald-700">
                        <ShieldCheck size={16} />
                        {t('auth', 'phone_verified_badge')}
                      </span>
                      <button
                        type="button"
                        onClick={resetVerification}
                        className="text-xs font-medium text-slate-500 hover:text-slate-700 hover:underline"
                      >
                        {t('auth', 'change_number')}
                      </button>
                    </div>
                  )}

                  {/* OTP entry card */}
                  {otpSent && !otpVerified && (
                    <div className="space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                      <p className="text-xs text-slate-600">{t('auth', 'otp_sent_hint')}</p>
                      <Input
                        label={t('auth', 'otp_label')}
                        inputMode="numeric"
                        maxLength={6}
                        value={otp}
                        onChange={e => setOtp(e.target.value.replace(/\D/g, '').slice(0, 6))}
                        placeholder="123456"
                        autoFocus
                      />
                      <Button
                        type="button"
                        className="w-full"
                        onClick={handleVerifyOtp}
                        disabled={verifying || otp.length !== 6}
                        isLoading={verifying}
                      >
                        {verifying ? t('auth', 'verifying') : t('auth', 'btn_verify_otp')}
                      </Button>
                    </div>
                  )}

                  {/* Inline OTP messages */}
                  {otpError && <p className="text-xs text-red-600">{otpError}</p>}
                  {!otpError && otpInfo && (
                    <p className="text-xs text-emerald-600">{otpInfo}</p>
                  )}
                </div>
              ) : (
                /* Phone OTP disabled by admin — plain optional number, no OTP step */
                <Input
                  label={t('auth', 'field_mobile')}
                  type="tel"
                  inputMode="tel"
                  autoComplete="tel"
                  value={data.mobile_number}
                  onChange={e => setData('mobile_number', e.target.value)}
                  error={errors.mobile_number}
                  placeholder={t('auth', 'field_mobile_ph')}
                />
              )}

              <div className="flex gap-3">
                <Button type="button" variant="outline" className="flex-1" onClick={back}>
                  ← {t('auth', 'btn_back')}
                </Button>
                <Button
                  type="submit"
                  className="flex-1"
                  disabled={!data.gender || (requirePhoneVerification && !otpVerified)}
                >
                  {t('auth', 'btn_continue')} →
                </Button>
              </div>
            </>
          )}

          {/* ── Step 3: Mode selection ── */}
          {step === 3 && (
            <>
              <p className="text-sm font-medium text-slate-700 mb-1">{t('auth', 'choose_experience')}</p>

              <div className="grid grid-cols-1 gap-4">
                {([
                  {
                    mode: 'general' as const,
                    icon: '🌐',
                    label: t('auth', 'mode_general_title'),
                    description: t('auth', 'mode_general_desc'),
                    color: 'border-blue-200 hover:border-blue-400',
                    selected: 'border-blue-600 bg-blue-50',
                    badge: null as string | null,
                  },
                  {
                    mode: 'islamic' as const,
                    icon: '☪️',
                    label: t('auth', 'mode_islamic_title'),
                    description: t('auth', 'mode_islamic_desc'),
                    color: 'border-emerald-200 hover:border-emerald-400',
                    selected: 'border-emerald-600 bg-emerald-50',
                    badge: t('auth', 'mode_islamic_badge') as string | null,
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
                    {t('auth', 'terms_link')}
                  </Link>{' '}
                  and{' '}
                  <Link href="/privacy" className="text-primary-600 hover:underline" target="_blank">
                    {t('auth', 'privacy_link')}
                  </Link>
                </span>
              </label>

              <div className="flex gap-3">
                <Button type="button" variant="outline" className="flex-1" onClick={back}>
                  ← {t('auth', 'btn_back')}
                </Button>
                <Button
                  type="submit"
                  className="flex-1"
                  size="lg"
                  isLoading={processing}
                  disabled={!data.terms_accepted}
                >
                  {t('auth', 'btn_create')}
                </Button>
              </div>
            </>
          )}
        </form>
      </div>

      {/* Social signup — shown only on step 1 as a faster alternative to the form */}
      {(googleEnabled || facebookEnabled) && step === 1 && (
        <div className="mt-4">
          <div className="flex items-center gap-3">
            <div className="flex-1 h-px bg-slate-200" />
            <span className="text-xs text-slate-400">{t('auth', 'or_continue_with')}</span>
            <div className="flex-1 h-px bg-slate-200" />
          </div>
          <div className="mt-3 space-y-3">
            {googleEnabled && (
              <a
                href={route('auth.social.redirect', { provider: 'google' })}
                className="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
              >
                <svg className="h-4 w-4" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                  <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                  <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                  <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                {t('auth', 'register_google')}
              </a>
            )}
            {facebookEnabled && (
              <a
                href={route('auth.social.redirect', { provider: 'facebook' })}
                className="flex w-full items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
              >
                <svg className="h-4 w-4" viewBox="0 0 24 24" fill="#1877F2">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                {t('auth', 'register_facebook')}
              </a>
            )}
          </div>
        </div>
      )}

      <p className="mt-4 text-center text-sm text-slate-500">
        {t('auth', 'already_member')}{' '}
        <Link href={route('login')} className="font-semibold text-primary-600 hover:underline">
          {t('auth', 'login_button')}
        </Link>
      </p>
    </GuestLayout>
  )
}
