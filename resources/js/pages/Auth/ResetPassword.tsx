import { Head, useForm } from '@inertiajs/react'
import GuestLayout from '@/layouts/GuestLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'

interface Props {
  token: string
  email: string
}

export default function ResetPassword({ token, email }: Props) {
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
    <GuestLayout title="Set new password">
      <Head title="Reset Password" />

      <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
        <form onSubmit={submit} className="space-y-5">
          <Input
            label="Email address"
            type="email"
            value={data.email}
            onChange={e => setData('email', e.target.value)}
            error={errors.email}
            required
          />
          <Input
            label="New password"
            type="password"
            value={data.password}
            onChange={e => setData('password', e.target.value)}
            error={errors.password}
            autoFocus
            required
            placeholder="Min 8 characters"
          />
          <Input
            label="Confirm new password"
            type="password"
            value={data.password_confirmation}
            onChange={e => setData('password_confirmation', e.target.value)}
            error={errors.password_confirmation}
            required
            placeholder="Repeat password"
          />
          <Button type="submit" className="w-full" size="lg" isLoading={processing}>
            Reset Password
          </Button>
        </form>
      </div>
    </GuestLayout>
  )
}
