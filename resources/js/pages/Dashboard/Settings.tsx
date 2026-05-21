/// <reference path="../../types/ziggy.d.ts" />
import { Head, useForm } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { useState } from 'react'
import { cn } from '@/lib/utils'

interface UserSettings {
  name: string
  email: string
  mobile?: string
  platform_mode: string
  photo_visibility: string
  account_status: string
}

interface Props { user: UserSettings }

type Tab = 'profile' | 'password' | 'danger'

export default function Settings({ user }: Props) {
  const [tab, setTab] = useState<Tab>('profile')

  const profileForm = useForm({
    name:             user.name,
    mobile:           user.mobile ?? '',
    platform_mode:    user.platform_mode,
    photo_visibility: user.photo_visibility,
  })

  const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
  })

  const deleteForm = useForm({ password: '' })

  const tabs: { key: Tab; label: string }[] = [
    { key: 'profile', label: 'Profile' },
    { key: 'password', label: 'Password' },
    { key: 'danger', label: 'Danger Zone' },
  ]

  return (
    <AppLayout>
      <Head title="Settings" />

      <div className="max-w-2xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-slate-900 mb-6">Settings</h1>

        {/* Tab nav */}
        <div className="flex gap-1 bg-slate-100 rounded-xl p-1 mb-6">
          {tabs.map(t => (
            <button
              key={t.key}
              onClick={() => setTab(t.key)}
              className={cn(
                'flex-1 rounded-lg py-2 text-sm font-medium transition-all',
                tab === t.key ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700',
              )}
            >
              {t.label}
            </button>
          ))}
        </div>

        {/* Profile settings */}
        {tab === 'profile' && (
          <div className="rounded-2xl border border-slate-200 bg-white p-8">
            <form
              onSubmit={e => { e.preventDefault(); profileForm.put(route('settings.profile')) }}
              className="space-y-5"
            >
              <Input
                label="Full Name"
                value={profileForm.data.name}
                onChange={e => profileForm.setData('name', e.target.value)}
                error={profileForm.errors.name}
                required
              />
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input
                  disabled
                  value={user.email}
                  className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-400 cursor-not-allowed"
                />
                <p className="mt-1 text-xs text-slate-400">Email cannot be changed. Contact support if needed.</p>
              </div>
              <Input
                label="Mobile Number"
                value={profileForm.data.mobile}
                onChange={e => profileForm.setData('mobile', e.target.value)}
                error={profileForm.errors.mobile}
                placeholder="+88017XXXXXXXX"
              />

              <div>
                <label className="block text-sm font-medium text-slate-700 mb-2">Platform Mode</label>
                <div className="grid grid-cols-2 gap-3">
                  {[
                    { value: 'general', label: '🌐 General' },
                    { value: 'islamic', label: '☪️ Islamic / Halal' },
                  ].map(m => (
                    <button
                      key={m.value}
                      type="button"
                      onClick={() => profileForm.setData('platform_mode', m.value)}
                      className={cn(
                        'rounded-xl border-2 py-3 text-sm font-semibold transition-all',
                        profileForm.data.platform_mode === m.value
                          ? 'border-primary-600 bg-primary-50 text-primary-700'
                          : 'border-slate-200 text-slate-600 hover:border-slate-300',
                      )}
                    >
                      {m.label}
                    </button>
                  ))}
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-slate-700 mb-2">Photo Visibility</label>
                <div className="space-y-2">
                  {[
                    { value: 'public', label: 'Public', desc: 'Anyone can see your photo' },
                    { value: 'members_only', label: 'Members Only', desc: 'Only registered members' },
                    { value: 'blurred', label: 'Blurred', desc: 'Blurred until interest is accepted' },
                  ].map(v => (
                    <label key={v.value} className="flex items-center gap-3 cursor-pointer rounded-xl border border-slate-200 px-4 py-3 hover:bg-slate-50">
                      <input
                        type="radio"
                        name="photo_visibility"
                        value={v.value}
                        checked={profileForm.data.photo_visibility === v.value}
                        onChange={() => profileForm.setData('photo_visibility', v.value)}
                        className="accent-primary-600"
                      />
                      <div>
                        <p className="text-sm font-medium text-slate-900">{v.label}</p>
                        <p className="text-xs text-slate-400">{v.desc}</p>
                      </div>
                    </label>
                  ))}
                </div>
              </div>

              <Button type="submit" isLoading={profileForm.processing} className="w-full">
                Save Changes
              </Button>
            </form>
          </div>
        )}

        {/* Password settings */}
        {tab === 'password' && (
          <div className="rounded-2xl border border-slate-200 bg-white p-8">
            <form
              onSubmit={e => { e.preventDefault(); passwordForm.put(route('settings.password'), { onSuccess: () => passwordForm.reset() }) }}
              className="space-y-5"
            >
              <Input
                label="Current Password"
                type="password"
                value={passwordForm.data.current_password}
                onChange={e => passwordForm.setData('current_password', e.target.value)}
                error={passwordForm.errors.current_password}
                required
              />
              <Input
                label="New Password"
                type="password"
                value={passwordForm.data.password}
                onChange={e => passwordForm.setData('password', e.target.value)}
                error={passwordForm.errors.password}
                required
                placeholder="Min 8 characters"
              />
              <Input
                label="Confirm New Password"
                type="password"
                value={passwordForm.data.password_confirmation}
                onChange={e => passwordForm.setData('password_confirmation', e.target.value)}
                error={passwordForm.errors.password_confirmation}
                required
              />
              <Button type="submit" isLoading={passwordForm.processing} className="w-full">
                Update Password
              </Button>
            </form>
          </div>
        )}

        {/* Danger zone */}
        {tab === 'danger' && (
          <div className="rounded-2xl border border-red-200 bg-white p-8">
            <h2 className="text-lg font-bold text-red-700 mb-2">Delete Account</h2>
            <p className="text-sm text-slate-500 mb-6">
              This action is permanent. All your data, biodata, and messages will be deleted.
            </p>
            <form
              onSubmit={e => { e.preventDefault(); deleteForm.delete(route('settings.delete')) }}
              className="space-y-4"
            >
              <Input
                label="Enter your password to confirm"
                type="password"
                value={deleteForm.data.password}
                onChange={e => deleteForm.setData('password', e.target.value)}
                error={deleteForm.errors.password}
                required
                placeholder="Your current password"
              />
              <Button
                type="submit"
                variant="destructive"
                isLoading={deleteForm.processing}
                className="w-full"
              >
                Permanently Delete My Account
              </Button>
            </form>
          </div>
        )}
      </div>
    </AppLayout>
  )
}
