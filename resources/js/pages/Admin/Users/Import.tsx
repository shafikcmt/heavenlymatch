/// <reference path="../../../types/ziggy.d.ts" />
import { Head, Link, useForm } from '@inertiajs/react'
import { useState } from 'react'
import axios from 'axios'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { useTranslation } from '@/lib/i18n'
import { cn } from '@/lib/utils'
import { ArrowLeft, FileDown, CheckCircle2, AlertCircle } from 'lucide-react'

interface PreviewRow { line: number; data: Record<string, any>; errors: string[]; valid: boolean }
interface Preview { rows: PreviewRow[]; total: number; valid: number; invalid: number }

const SAMPLE = `name,email,phone,gender,password,status,plan,email_verified,phone_verified
Abdullah Rahman,abdullah@example.com,01712345678,male,Secret123,active,free,yes,no
Fatima Khatun,fatima@example.com,01812345679,female,Secret123,active,free,yes,yes`

export default function UsersImport() {
  const { t } = useTranslation()
  const [file, setFile] = useState<File | null>(null)
  const [preview, setPreview] = useState<Preview | null>(null)
  const [previewing, setPreviewing] = useState(false)
  const { data, setData, post, processing } = useForm<{ file: File | null; valid_only: boolean }>({ file: null, valid_only: true })

  function downloadSample() {
    const blob = new Blob([SAMPLE], { type: 'text/csv' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url; a.download = 'users-sample.csv'; a.click()
    URL.revokeObjectURL(url)
  }

  function onFile(f: File | null) {
    setFile(f); setData('file', f); setPreview(null)
  }

  async function runPreview() {
    if (!file) return
    setPreviewing(true)
    try {
      const fd = new FormData()
      fd.append('file', file)
      const { data: res } = await axios.post(route('admin.users.import.preview'), fd)
      setPreview(res)
    } catch {
      setPreview(null)
    } finally {
      setPreviewing(false)
    }
  }

  function runImport(e: React.FormEvent) {
    e.preventDefault()
    post(route('admin.users.import.store'), { forceFormData: true })
  }

  return (
    <AdminLayout>
      <Head title={t('admin', 'import_title')} />
      <div className="max-w-3xl space-y-5">
        <Link href={route('admin.users.index')} className="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">
          <ArrowLeft size={15} /> {t('admin', 'back_to_users')}
        </Link>
        <h1 className="text-xl font-bold text-slate-900">{t('admin', 'import_title')}</h1>

        <div className="rounded-2xl border border-slate-200 bg-white p-6 space-y-4">
          <p className="text-sm text-slate-600">{t('admin', 'import_instructions')}</p>
          <button onClick={downloadSample} className="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:underline">
            <FileDown size={15} /> {t('admin', 'download_sample')}
          </button>

          <form onSubmit={runImport} className="space-y-4">
            <input
              type="file" accept=".csv,text/csv"
              onChange={e => onFile(e.target.files?.[0] ?? null)}
              className="block text-sm text-slate-600 file:mr-3 file:rounded-lg file:border file:border-slate-200 file:bg-slate-50 file:px-3 file:py-1.5 file:text-sm file:font-medium hover:file:bg-slate-100"
            />

            <div className="flex flex-wrap items-center gap-3">
              <Button type="button" variant="outline" onClick={runPreview} disabled={!file || previewing} isLoading={previewing}>
                {t('admin', 'preview_btn')}
              </Button>
              <label className="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" checked={data.valid_only} onChange={e => setData('valid_only', e.target.checked)} className="rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                {t('admin', 'import_valid_only')}
              </label>
              <Button type="submit" disabled={!file || processing} isLoading={processing}>{t('admin', 'run_import')}</Button>
            </div>
          </form>
        </div>

        {preview && (
          <div className="rounded-2xl border border-slate-200 bg-white p-6 space-y-3">
            <div className="flex gap-4 text-sm">
              <span className="text-slate-600">{t('admin', 'import_total')}: <b>{preview.total}</b></span>
              <span className="text-emerald-600">{t('admin', 'import_valid')}: <b>{preview.valid}</b></span>
              <span className="text-red-600">{t('admin', 'import_invalid')}: <b>{preview.invalid}</b></span>
            </div>
            <div className="overflow-x-auto rounded-xl border border-slate-100">
              <table className="w-full text-sm min-w-[640px]">
                <thead>
                  <tr className="border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <th className="px-3 py-2">{t('admin', 'import_col_line')}</th>
                    <th className="px-3 py-2">{t('admin', 'field_name')}</th>
                    <th className="px-3 py-2">{t('admin', 'field_email')}</th>
                    <th className="px-3 py-2">{t('admin', 'field_gender')}</th>
                    <th className="px-3 py-2">{t('admin', 'import_col_result')}</th>
                  </tr>
                </thead>
                <tbody>
                  {preview.rows.map(r => (
                    <tr key={r.line} className={cn('border-b border-slate-50 last:border-0', !r.valid && 'bg-red-50/40')}>
                      <td className="px-3 py-2 text-slate-400">{r.line}</td>
                      <td className="px-3 py-2 text-slate-700">{r.data.name || '—'}</td>
                      <td className="px-3 py-2 text-slate-700">{r.data.email || '—'}</td>
                      <td className="px-3 py-2 text-slate-600">{r.data.gender || '—'}</td>
                      <td className="px-3 py-2">
                        {r.valid
                          ? <span className="inline-flex items-center gap-1 text-emerald-600"><CheckCircle2 size={13} /> {t('admin', 'import_row_ok')}</span>
                          : <span className="inline-flex items-center gap-1 text-red-600"><AlertCircle size={13} /> {r.errors.join(' ')}</span>}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>
    </AdminLayout>
  )
}
