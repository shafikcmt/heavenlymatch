import { Head, useForm } from '@inertiajs/react'
import GuestLayout from '@/layouts/GuestLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { MailCheck } from 'lucide-react'

interface Props {
  email?: string
  status?: string
}

export default function VerifyEmail({ email, status }: Props) {
  const { t } = useTranslation()
  const { post, processing } = useForm({})

  function resend(e: React.FormEvent) {
    e.preventDefault()
    post(route('verification.send'))
  }

  return (
    <GuestLayout title={t('auth', 'verify_title')}>
      <Head title={t('auth', 'verify_title')} />

      <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card text-center space-y-5">

        <div className="flex justify-center">
          <div className="flex h-16 w-16 items-center justify-center rounded-full bg-primary-50">
            <MailCheck size={32} className="text-primary-600" />
          </div>
        </div>

        <div>
          <h1 className="text-xl font-bold text-slate-900 mb-1">
            {t('auth', 'verify_title')}
          </h1>
          {email && (
            <p className="text-sm text-slate-500">
              {t('auth', 'verify_subtitle').replace(':email', email)}
            </p>
          )}
        </div>

        <p className="text-sm text-slate-600">
          {t('auth', 'verify_check_inbox')}
        </p>

        {status && (
          <div className="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
            {status}
          </div>
        )}

        <form onSubmit={resend}>
          <Button
            type="submit"
            className="w-full"
            size="lg"
            isLoading={processing}
          >
            {t('auth', 'verify_resend')}
          </Button>
        </form>

        <a
          href={route('logout')}
          className="block text-xs text-slate-400 hover:text-slate-600 transition-colors"
          onClick={(e) => {
            e.preventDefault()
            // POST logout
            const form = document.createElement('form')
            form.method = 'POST'
            form.action = route('logout')
            const csrf = document.createElement('input')
            csrf.type = 'hidden'
            csrf.name = '_token'
            csrf.value = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? ''
            form.appendChild(csrf)
            document.body.appendChild(form)
            form.submit()
          }}
        >
          {t('auth', 'verify_logout')}
        </a>
      </div>
    </GuestLayout>
  )
}
