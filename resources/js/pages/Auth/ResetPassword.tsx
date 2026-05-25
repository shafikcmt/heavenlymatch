import { Head, useForm } from '@inertiajs/react'
import GuestLayout from '@/layouts/GuestLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { useTranslation } from '@/lib/i18n'

interface Props {
  token: string
  email: string
}

export default function ResetPassword({ token, email }: Props) {
  const { t } = useTranslation()
  const { data, setData, post, processing, errors } = useForm({
    token,
    email,
    password: '',
    password_confirmation: '',
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('password.update'))
  }

  return (
    <GuestLayout title={t('auth', 'reset_title')}>
      <Head title={t('auth', 'reset_title')} />

      <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
        <form onSubmit={submit} className="space-y-5">
          <Input
            label={t('auth', 'field_email')}
            type="email"
            value={data.email}
            onChange={e => setData('email', e.target.value)}
            error={errors.email}
            required
          />
          <Input
            label={t('auth', 'field_password')}
            type="password"
            value={data.password}
            onChange={e => setData('password', e.target.value)}
            error={errors.password}
            autoFocus
            required
            placeholder={t('auth', 'field_password_ph')}
          />
          <Input
            label={t('auth', 'field_confirm_password')}
            type="password"
            value={data.password_confirmation}
            onChange={e => setData('password_confirmation', e.target.value)}
            error={errors.password_confirmation}
            required
            placeholder={t('auth', 'field_confirm_ph')}
          />
          <Button type="submit" className="w-full" size="lg" isLoading={processing}>
            {t('auth', 'reset_button')}
          </Button>
        </form>
      </div>
    </GuestLayout>
  )
}
