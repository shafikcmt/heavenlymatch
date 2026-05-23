/// <reference path="../../types/ziggy.d.ts" />
import { Mail, Clock, MessageCircle, AlertTriangle } from 'lucide-react'
import MarketingLayout from '@/layouts/MarketingLayout'
import { useTranslation } from '@/lib/i18n'
import { SeoHead } from '@/components/SeoHead'
import { useState } from 'react'

const CONTACT_EMAIL = 'support@heavenlymatch.com'

export default function Contact() {
  const { t } = useTranslation()
  const [submitted, setSubmitted] = useState(false)
  const [form, setForm] = useState({ name: '', email: '', subject: '', message: '' })

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    // Opens the user's email client as a graceful fallback since no backend contact route exists yet.
    const body = encodeURIComponent(`Name: ${form.name}\n\n${form.message}`)
    const subject = encodeURIComponent(form.subject || 'HeavenlyMatch Enquiry')
    window.location.href = `mailto:${CONTACT_EMAIL}?subject=${subject}&body=${body}`
    setSubmitted(true)
  }

  return (
    <MarketingLayout>
      <SeoHead pageKey="contact" />

      {/* Hero */}
      <section className="bg-gradient-to-br from-slate-50 via-white to-primary-50 py-20 px-4 text-center">
        <div className="max-w-2xl mx-auto">
          <div className="inline-flex items-center gap-2 bg-primary-100 text-primary-700 rounded-full px-4 py-1.5 text-sm font-medium mb-6">
            <MessageCircle size={14} />
            {t('marketing', 'contact_hero_title')}
          </div>
          <h1 className="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-6">
            {t('marketing', 'contact_hero_title')}
          </h1>
          <p className="text-lg text-slate-600">{t('marketing', 'contact_hero_subtitle')}</p>
        </div>
      </section>

      <section className="py-20 px-4">
        <div className="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-5 gap-10">
          {/* Info cards */}
          <div className="lg:col-span-2 space-y-5">
            <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <div className="flex items-center gap-3 mb-3">
                <div className="h-9 w-9 rounded-xl bg-primary-50 flex items-center justify-center">
                  <Mail size={18} className="text-primary-600" />
                </div>
                <h3 className="font-semibold text-slate-900">{t('marketing', 'contact_email_title')}</h3>
              </div>
              <p className="text-sm text-slate-500 mb-2">{t('marketing', 'contact_email_desc')}</p>
              <a
                href={`mailto:${CONTACT_EMAIL}`}
                className="text-sm font-medium text-primary-600 hover:underline break-all"
              >
                {CONTACT_EMAIL}
              </a>
            </div>

            <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <div className="flex items-center gap-3 mb-3">
                <div className="h-9 w-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                  <Clock size={18} className="text-emerald-600" />
                </div>
                <h3 className="font-semibold text-slate-900">{t('marketing', 'contact_hours_title')}</h3>
              </div>
              <p className="text-sm text-slate-600">{t('marketing', 'contact_hours_desc')}</p>
            </div>

            <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <div className="flex items-center gap-3 mb-3">
                <div className="h-9 w-9 rounded-xl bg-amber-50 flex items-center justify-center">
                  <MessageCircle size={18} className="text-amber-600" />
                </div>
                <h3 className="font-semibold text-slate-900">{t('marketing', 'contact_response_title')}</h3>
              </div>
              <p className="text-sm text-slate-600">{t('marketing', 'contact_response_desc')}</p>
            </div>

            <div className="bg-red-50 rounded-2xl border border-red-200 p-6">
              <div className="flex items-center gap-3 mb-2">
                <AlertTriangle size={18} className="text-red-500 flex-shrink-0" />
                <h3 className="font-semibold text-red-900 text-sm">{t('marketing', 'contact_urgent_title')}</h3>
              </div>
              <p className="text-xs text-red-700 leading-relaxed">{t('marketing', 'contact_urgent_desc')}</p>
            </div>
          </div>

          {/* Contact form */}
          <div className="lg:col-span-3">
            <div className="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
              {submitted ? (
                <div className="text-center py-10">
                  <div className="text-5xl mb-4">✅</div>
                  <p className="font-semibold text-slate-900 text-lg">{t('marketing', 'contact_form_success')}</p>
                </div>
              ) : (
                <form onSubmit={handleSubmit} className="space-y-5">
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-1.5">
                        {t('marketing', 'contact_form_name')}
                      </label>
                      <input
                        type="text"
                        required
                        value={form.name}
                        onChange={e => setForm(f => ({ ...f, name: e.target.value }))}
                        className="w-full h-10 rounded-xl border border-slate-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-slate-700 mb-1.5">
                        {t('marketing', 'contact_form_email')}
                      </label>
                      <input
                        type="email"
                        required
                        value={form.email}
                        onChange={e => setForm(f => ({ ...f, email: e.target.value }))}
                        className="w-full h-10 rounded-xl border border-slate-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1.5">
                      {t('marketing', 'contact_form_subject')}
                    </label>
                    <input
                      type="text"
                      required
                      value={form.subject}
                      onChange={e => setForm(f => ({ ...f, subject: e.target.value }))}
                      className="w-full h-10 rounded-xl border border-slate-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1.5">
                      {t('marketing', 'contact_form_message')}
                    </label>
                    <textarea
                      required
                      rows={5}
                      value={form.message}
                      onChange={e => setForm(f => ({ ...f, message: e.target.value }))}
                      className="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"
                    />
                  </div>

                  <p className="text-xs text-slate-400">{t('marketing', 'contact_form_note')}</p>

                  <button
                    type="submit"
                    className="w-full h-12 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition-colors shadow-sm"
                  >
                    {t('marketing', 'contact_form_submit')}
                  </button>
                </form>
              )}
            </div>
          </div>
        </div>
      </section>
    </MarketingLayout>
  )
}
