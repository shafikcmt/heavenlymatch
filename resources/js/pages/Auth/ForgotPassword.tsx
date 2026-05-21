import { Head, useForm } from '@inertiajs/react'
import GuestLayout from '@/layouts/GuestLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'

interface Props {
  status?: string
}

export default function ForgotPassword({ status }: Props) {
  const { data, setData, post, processing, errors } = useForm({ email: '' })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('password.email'))
  }

  return (
    <GuestLayout title="Reset your password">
      <Head title="Forgot Password" />

      <p className="text-sm text-slate-600 mb-6 text-center">
        Enter your email and we'll send you a password reset link.
      </p>

      {status && (
        <div className="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
          {status}
        </div>
      )}

      <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
        <form onSubmit={submit} className="space-y-4">
          <Input
            label="Email address"
            type="email"
            value={data.email}
            onChange={e => setData('email', e.target.value)}
            error={errors.email}
            autoFocus
            required
            placeholder="you@example.com"
          />
          <Button type="submit" className="w-full" size="lg" isLoading={processing}>
            Send Reset Link
          </Button>
        </form>
      </div>
    </GuestLayout>
  )
}
