import { Head, useForm } from '@inertiajs/react'
import GuestLayout from '@/layouts/GuestLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { useTranslation } from '@/lib/i18n'

interface Props {
  status?: string
}

export default function ForgotPassword({ status }: Props) {
  const { t } = useTranslation()
  const { data, setData, post, processing, errors } = useForm({ email: '' })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('password.email'))
  }

  return (
    <GuestLayout title={t('auth', 'forgot_title')}>
      <Head title={t('auth', 'forgot_title')} />

      <p className="text-sm text-slate-600 mb-6 text-center">
        {t('auth', 'forgot_subtitle')}
      </p>

      {status && (
        <div className="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
          {status}
        </div>
      )}

      <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
        <form onSubmit={submit} className="space-y-4">
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
          <Button type="submit" className="w-full" size="lg" isLoading={processing}>
            {t('auth', 'forgot_button')}
          </Button>
        </form>
      </div>
    </GuestLayout>
  )
}
