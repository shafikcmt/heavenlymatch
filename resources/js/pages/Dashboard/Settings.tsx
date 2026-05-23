/// <reference path="../../types/ziggy.d.ts" />
import { Head, usePage, useForm } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { useState } from 'react'
import { cn } from '@/lib/utils'
import { useTranslation } from '@/lib/i18n'
import { CheckCircle, AlertCircle, Shield, Globe, Lock, Trash2 } from 'lucide-react'
import type { PageProps } from '@/types'

interface UserSettings {
  name: string
  email: string
  mobile?: string
  platform_mode: 'general' | 'islamic'
  photo_visibility: 'public' | 'members_only' | 'blurred'
  account_status: string
  registration_id: string
  preferred_language: 'en' | 'bn'
}

interface Props { user: UserSettings }

type Tab = 'account' | 'password' | 'danger'

export default function Settings({ user }: Props) {
  const { t } = useTranslation()
  const { flash } = usePage<PageProps>().props
  const [tab, setTab] = useState<Tab>('account')

  const profileForm = useForm({
    name:               user.name,
    mobile:             user.mobile ?? '',
    platform_mode:      user.platform_mode,
    photo_visibility:   user.photo_visibility,
    preferred_language: user.preferred_language,
  })

  const passwordForm = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
  })

  const deleteForm = useForm({ password: '' })

  const tabs: { key: Tab; label: string; icon: React.ReactNode }[] = [
    { key: 'account',  label: t('settings', 'tab_account'),  icon: <Globe size={15} /> },
    { key: 'password', label: t('settings', 'tab_password'), icon: <Lock size={15} /> },
    { key: 'danger',   label: t('settings', 'tab_danger'),   icon: <Trash2 size={15} /> },
  ]

  return (
    <AppLayout>
      <Head title={t('settings', 'title')} />

      <div className="max-w-2xl mx-auto px-4 py-8 space-y-6">

        {/* Page header */}
        <div>
          <h1 className="text-2xl font-bold text-slate-900">{t('settings', 'title')}</h1>
          <p className="text-sm text-slate-500 mt-1">{t('settings', 'page_subtitle')}</p>
        </div>

        {/* Flash messages */}
        {flash.success && (
          <div className="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <CheckCircle size={18} className="text-emerald-600 shrink-0" />
            <p className="text-sm text-emerald-800">{flash.success}</p>
          </div>
        )}
        {flash.error && (
          <div className="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
            <AlertCircle size={18} className="text-red-600 shrink-0" />
            <p className="text-sm text-red-800">{flash.error}</p>
          </div>
        )}

        {/* Tab nav */}
        <div className="flex gap-1 bg-slate-100 rounded-xl p-1">
          {tabs.map(tb => (
            <button
              key={tb.key}
              onClick={() => setTab(tb.key)}
              className={cn(
                'flex-1 flex items-center justify-center gap-1.5 rounded-lg py-2 text-sm font-medium transition-all',
                tab === tb.key
                  ? 'bg-white text-slate-900 shadow-sm'
                  : 'text-slate-500 hover:text-slate-700',
              )}
            >
              {tb.icon}
              {tb.label}
            </button>
          ))}
        </div>

        {/* ── Account Tab ── */}
        {tab === 'account' && (
          <form
            onSubmit={e => { e.preventDefault(); profileForm.put(route('settings.profile')) }}
            className="space-y-5"
          >
            {/* Account information */}
            <Section title={t('settings', 'section_account')}>
              {/* Member ID + status (read-only display) */}
              <div className="grid grid-cols-2 gap-4">
                <ReadonlyField label={t('settings', 'registration_id_label')} value={user.registration_id} />
                <ReadonlyField label={t('settings', 'account_status_label')} value={user.account_status} />
              </div>

              <Input
                label={t('settings', 'full_name')}
                value={profileForm.data.name}
                onChange={e => profileForm.setData('name', e.target.value)}
                error={profileForm.errors.name}
                required
              />

              <div>
                <label className="block text-sm font-medium text-slate-700 mb-1">
                  {t('settings', 'email')}
                </label>
                <input
                  disabled
                  value={user.email}
                  className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-400 cursor-not-allowed"
                />
                <p className="mt-1 text-xs text-slate-400">{t('settings', 'email_readonly_hint')}</p>
              </div>

              <Input
                label={t('settings', 'mobile_number')}
                value={profileForm.data.mobile}
                onChange={e => profileForm.setData('mobile', e.target.value)}
                error={profileForm.errors.mobile}
                placeholder={t('settings', 'mobile_placeholder')}
              />
            </Section>

            {/* Language preference */}
            <Section title={t('settings', 'section_language')}>
              <div className="grid grid-cols-2 gap-3">
                {(['en', 'bn'] as const).map(lang => (
                  <label
                    key={lang}
                    className={cn(
                      'flex items-center gap-3 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all',
                      profileForm.data.preferred_language === lang
                        ? 'border-primary-500 bg-primary-50'
                        : 'border-slate-200 hover:border-slate-300',
                    )}
                  >
                    <input
                      type="radio"
                      name="preferred_language"
                      value={lang}
                      checked={profileForm.data.preferred_language === lang}
                      onChange={() => profileForm.setData('preferred_language', lang)}
                      className="accent-primary-600"
                    />
                    <span className="text-sm font-medium text-slate-800">
                      {lang === 'en' ? t('settings', 'language_en') : t('settings', 'language_bn')}
                    </span>
                  </label>
                ))}
              </div>
              <p className="text-xs text-slate-400">{t('settings', 'language_save_hint')}</p>
            </Section>

            {/* Platform mode */}
            <Section title={t('settings', 'section_platform')}>
              <div className="grid grid-cols-2 gap-3">
                {([
                  { value: 'general', label: t('settings', 'platform_general'), desc: t('settings', 'platform_general_desc') },
                  { value: 'islamic', label: t('settings', 'platform_islamic'), desc: t('settings', 'platform_islamic_desc') },
                ] as const).map(m => (
                  <label
                    key={m.value}
                    className={cn(
                      'flex flex-col gap-0.5 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all',
                      profileForm.data.platform_mode === m.value
                        ? 'border-primary-500 bg-primary-50'
                        : 'border-slate-200 hover:border-slate-300',
                    )}
                  >
                    <div className="flex items-center gap-2">
                      <input
                        type="radio"
                        name="platform_mode"
                        value={m.value}
                        checked={profileForm.data.platform_mode === m.value}
                        onChange={() => profileForm.setData('platform_mode', m.value)}
                        className="accent-primary-600"
                      />
                      <span className="text-sm font-semibold text-slate-800">{m.label}</span>
                    </div>
                    <p className="text-xs text-slate-500 pl-5">{m.desc}</p>
                  </label>
                ))}
              </div>
            </Section>

            {/* Photo privacy */}
            <Section title={t('settings', 'section_photo')}>
              <div className="flex flex-col gap-2">
                {([
                  { value: 'public',       label: t('settings', 'photo_vis_public'),   desc: t('settings', 'photo_vis_public_desc') },
                  { value: 'members_only', label: t('settings', 'photo_vis_members'),  desc: t('settings', 'photo_vis_members_desc') },
                  { value: 'blurred',      label: t('settings', 'photo_vis_blurred'),  desc: t('settings', 'photo_vis_blurred_desc') },
                ] as const).map(v => (
                  <label
                    key={v.value}
                    className={cn(
                      'flex items-start gap-3 rounded-xl border px-4 py-3 cursor-pointer transition-colors',
                      profileForm.data.photo_visibility === v.value
                        ? 'border-primary-500 bg-primary-50'
                        : 'border-slate-200 hover:bg-slate-50',
                    )}
                  >
                    <input
                      type="radio"
                      name="photo_visibility"
                      value={v.value}
                      checked={profileForm.data.photo_visibility === v.value}
                      onChange={() => profileForm.setData('photo_visibility', v.value)}
                      className="accent-primary-600 mt-0.5"
                    />
                    <div>
                      <p className="text-sm font-medium text-slate-800">{v.label}</p>
                      <p className="text-xs text-slate-400">{v.desc}</p>
                    </div>
                  </label>
                ))}
              </div>

              {profileForm.data.platform_mode === 'islamic' && (
                <div className="flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 mt-2">
                  <Shield size={15} className="text-emerald-600 mt-0.5 shrink-0" />
                  <p className="text-xs text-emerald-800">{t('settings', 'photo_privacy_notice')}</p>
                </div>
              )}
            </Section>

            <Button
              type="submit"
              isLoading={profileForm.processing}
              disabled={profileForm.processing}
              className="w-full"
            >
              {t('settings', 'save_account')}
            </Button>
          </form>
        )}

        {/* ── Password Tab ── */}
        {tab === 'password' && (
          <form
            onSubmit={e => {
              e.preventDefault()
              passwordForm.put(route('settings.password'), {
                onSuccess: () => passwordForm.reset(),
              })
            }}
            className="space-y-5"
          >
            <Section title={t('settings', 'section_password')}>
              <Input
                label={t('settings', 'current_password')}
                type="password"
                value={passwordForm.data.current_password}
                onChange={e => passwordForm.setData('current_password', e.target.value)}
                error={passwordForm.errors.current_password}
                required
              />
              <Input
                label={t('settings', 'new_password')}
                type="password"
                value={passwordForm.data.password}
                onChange={e => passwordForm.setData('password', e.target.value)}
                error={passwordForm.errors.password}
                required
                placeholder={t('settings', 'new_password_placeholder')}
              />
              <Input
                label={t('settings', 'confirm_password')}
                type="password"
                value={passwordForm.data.password_confirmation}
                onChange={e => passwordForm.setData('password_confirmation', e.target.value)}
                error={passwordForm.errors.password_confirmation}
                required
              />

              <div className="flex items-start gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                <Shield size={15} className="text-slate-400 mt-0.5 shrink-0" />
                <p className="text-xs text-slate-500">{t('settings', 'security_notice')}</p>
              </div>
            </Section>

            <Button
              type="submit"
              isLoading={passwordForm.processing}
              disabled={passwordForm.processing}
              className="w-full"
            >
              {t('settings', 'update_password')}
            </Button>
          </form>
        )}

        {/* ── Danger Zone Tab ── */}
        {tab === 'danger' && (
          <form
            onSubmit={e => { e.preventDefault(); deleteForm.delete(route('settings.delete')) }}
            className="space-y-5"
          >
            <div className="rounded-2xl border border-red-200 bg-white p-6 space-y-4">
              <h2 className="text-base font-bold text-red-700">{t('settings', 'section_danger')}</h2>

              <div className="flex items-start gap-2 rounded-xl border border-red-100 bg-red-50 px-3 py-2.5">
                <AlertCircle size={15} className="text-red-500 mt-0.5 shrink-0" />
                <p className="text-xs text-red-700">{t('settings', 'danger_warning')}</p>
              </div>

              <Input
                label={t('settings', 'danger_confirm_label')}
                type="password"
                value={deleteForm.data.password}
                onChange={e => deleteForm.setData('password', e.target.value)}
                error={deleteForm.errors.password}
                required
              />

              <Button
                type="submit"
                variant="destructive"
                isLoading={deleteForm.processing}
                disabled={deleteForm.processing || deleteForm.data.password.length < 1}
                className="w-full"
              >
                {t('settings', 'delete_account_btn')}
              </Button>
            </div>

            <p className="text-center text-xs text-slate-400">{t('settings', 'privacy_protected')}</p>
          </form>
        )}
      </div>
    </AppLayout>
  )
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
      <h2 className="text-sm font-semibold text-slate-900 border-b border-slate-100 pb-3">{title}</h2>
      {children}
    </div>
  )
}

function ReadonlyField({ label, value }: { label: string; value: string }) {
  return (
    <div>
      <label className="block text-xs font-medium text-slate-500 mb-1">{label}</label>
      <p className="text-sm font-semibold text-slate-800 bg-slate-50 rounded-xl px-3 py-2">{value}</p>
    </div>
  )
}
