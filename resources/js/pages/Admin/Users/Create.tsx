/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link, useForm } from '@inertiajs/react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { useTranslation } from '@/lib/i18n'
import { ArrowLeft } from 'lucide-react'

interface Plan { id: number; name: string }

export default function UserCreate({ plans }: { plans: Plan[] }) {
  const { t } = useTranslation()
  const { data, setData, post, processing, errors } = useForm({
    name: '', email: '', mobile_number: '',
    password: '', password_confirmation: '',
    gender: '' as '' | 'male' | 'female',
    profile_created_for: 'self',
    plan: 'free', status: 'active',
    email_verified: true, phone_verified: false, send_welcome: false,
  })

  function submit(e: React.FormEvent) { e.preventDefault(); post(route('admin.users.store')) }

  return (
    <AdminLayout>
      <Head title={t('admin', 'create_user_title')} />
      <div className="max-w-2xl space-y-5">
        <Link href={route('admin.users.index')} className="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">
          <ArrowLeft size={15} /> {t('admin', 'back_to_users')}
        </Link>
        <h1 className="text-xl font-bold text-slate-900">{t('admin', 'create_user_title')}</h1>

        <form onSubmit={submit} className="space-y-5 rounded-2xl border border-slate-200 bg-white p-6">
          <div className="grid gap-4 sm:grid-cols-2">
            <Input label={t('admin', 'field_name')} value={data.name} onChange={e => setData('name', e.target.value)} error={errors.name} required />
            <Input label={t('admin', 'field_email')} type="email" value={data.email} onChange={e => setData('email', e.target.value)} error={errors.email} required />
            <Input label={t('admin', 'field_phone')} type="tel" value={data.mobile_number} onChange={e => setData('mobile_number', e.target.value)} error={errors.mobile_number} placeholder="+880 1XXX-XXXXXX" />
            <Field label={t('admin', 'field_gender')} error={errors.gender}>
              <select value={data.gender} onChange={e => setData('gender', e.target.value as any)} className={selectCls} required>
                <option value="">—</option>
                <option value="male">{t('admin', 'user_gender_male')}</option>
                <option value="female">{t('admin', 'user_gender_female')}</option>
              </select>
            </Field>
            <Input label={t('admin', 'field_password')} type="password" value={data.password} onChange={e => setData('password', e.target.value)} error={errors.password} required />
            <Input label={t('admin', 'field_confirm_password')} type="password" value={data.password_confirmation} onChange={e => setData('password_confirmation', e.target.value)} required />
            <Field label={t('admin', 'field_plan')}>
              <select value={data.plan} onChange={e => setData('plan', e.target.value)} className={selectCls}>
                <option value="free">{t('admin', 'plan_free')}</option>
                {plans.map(p => <option key={p.id} value={String(p.id)}>{p.name}</option>)}
              </select>
            </Field>
            <Field label={t('admin', 'field_status')}>
              <select value={data.status} onChange={e => setData('status', e.target.value)} className={selectCls}>
                <option value="active">{t('admin', 'status_active')}</option>
                <option value="inactive">{t('admin', 'status_inactive')}</option>
                <option value="suspended">{t('admin', 'status_suspended')}</option>
              </select>
            </Field>
          </div>

          <div className="flex flex-wrap gap-5 border-t border-slate-100 pt-4">
            <Check label={t('admin', 'field_email_verified')} checked={data.email_verified} onChange={v => setData('email_verified', v)} />
            <Check label={t('admin', 'field_phone_verified')} checked={data.phone_verified} onChange={v => setData('phone_verified', v)} />
            <Check label={t('admin', 'field_send_welcome')} checked={data.send_welcome} onChange={v => setData('send_welcome', v)} />
          </div>

          <div className="flex justify-end gap-2">
            <Link href={route('admin.users.index')}><Button type="button" variant="outline">{t('common', 'cancel') || 'Cancel'}</Button></Link>
            <Button type="submit" isLoading={processing}>{t('admin', 'save_user')}</Button>
          </div>
        </form>
      </div>
    </AdminLayout>
  )
}

const selectCls = 'w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500'

function Field({ label, error, children }: { label: string; error?: string; children: React.ReactNode }) {
  return (
    <div className="flex flex-col gap-1.5">
      <label className="text-sm font-medium text-slate-700">{label}</label>
      {children}
      {error && <p className="text-xs text-red-600">{error}</p>}
    </div>
  )
}

function Check({ label, checked, onChange }: { label: string; checked: boolean; onChange: (v: boolean) => void }) {
  return (
    <label className="flex items-center gap-2 text-sm text-slate-700">
      <input type="checkbox" checked={checked} onChange={e => onChange(e.target.checked)} className="rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
      {label}
    </label>
  )
}
