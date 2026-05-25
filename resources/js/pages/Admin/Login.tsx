import { Head, useForm, usePage } from '@inertiajs/react'
import { Crown, Eye, EyeOff } from 'lucide-react'
import { useState } from 'react'
import type { PageProps } from '@/types'

interface Props {
  status?: string
}

export default function AdminLogin({ status }: Props) {
  const { flash } = usePage<PageProps>().props
  const { data, setData, post, processing, errors } = useForm({
    email: '',
    password: '',
  })
  const [showPassword, setShowPassword] = useState(false)

  function submit(e: React.FormEvent) {
    e.preventDefault()
    post('/admin/login')
  }

  return (
    <>
      <Head title="Admin Login — HeavenlyMatch" />

      <div className="min-h-screen bg-slate-950 flex items-center justify-center p-4">
        <div className="w-full max-w-sm">
          {/* Logo */}
          <div className="flex flex-col items-center mb-8">
            <div className="h-12 w-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center mb-3 shadow-lg">
              <Crown size={22} className="text-white" />
            </div>
            <h1 className="text-xl font-bold text-white">HeavenlyMatch Admin</h1>
            <p className="text-sm text-slate-400 mt-1">Sign in to the admin panel</p>
          </div>

          {/* Status / error flash */}
          {status && (
            <div className="mb-4 rounded-lg bg-emerald-900/50 border border-emerald-700 px-4 py-3 text-sm text-emerald-300">
              {status}
            </div>
          )}
          {flash.error && (
            <div className="mb-4 rounded-lg bg-red-900/50 border border-red-700 px-4 py-3 text-sm text-red-300">
              {flash.error}
            </div>
          )}

          <form onSubmit={submit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-slate-300 mb-1.5">
                Email address
              </label>
              <input
                type="email"
                autoComplete="email"
                value={data.email}
                onChange={e => setData('email', e.target.value)}
                className="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                placeholder="admin@example.com"
                required
              />
              {errors.email && (
                <p className="mt-1 text-xs text-red-400">{errors.email}</p>
              )}
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-300 mb-1.5">
                Password
              </label>
              <div className="relative">
                <input
                  type={showPassword ? 'text' : 'password'}
                  autoComplete="current-password"
                  value={data.password}
                  onChange={e => setData('password', e.target.value)}
                  className="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2.5 pr-10 text-sm text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                  placeholder="••••••••"
                  required
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(v => !v)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200"
                  tabIndex={-1}
                >
                  {showPassword ? <EyeOff size={15} /> : <Eye size={15} />}
                </button>
              </div>
              {errors.password && (
                <p className="mt-1 text-xs text-red-400">{errors.password}</p>
              )}
            </div>

            <button
              type="submit"
              disabled={processing}
              className="w-full rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-slate-950 disabled:opacity-60 transition-colors"
            >
              {processing ? 'Signing in…' : 'Sign in to Admin'}
            </button>
          </form>

          <p className="mt-6 text-center text-xs text-slate-600">
            Not an admin?{' '}
            <a href="/login" className="text-slate-400 hover:text-white transition-colors">
              Back to user login
            </a>
          </p>
        </div>
      </div>
    </>
  )
}
