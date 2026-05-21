/// <reference path="../../types/ziggy.d.ts" />
import { Head, Link } from '@inertiajs/react'
import { Button } from '@/components/ui/Button'
import { Shield, Heart, Users, Star, CheckCircle } from 'lucide-react'

export default function Home() {
  return (
    <>
      <Head title="HeavenlyMatch — Halal Matrimony for Bangladeshi Muslims" />

      <div className="min-h-screen bg-white">
        {/* Navbar */}
        <nav className="border-b border-slate-100 sticky top-0 bg-white/95 backdrop-blur z-40">
          <div className="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <Link href={route('home')} className="flex items-center gap-2">
              <div className="h-8 w-8 rounded-lg bg-primary-600 flex items-center justify-center">
                <Heart size={18} className="text-white fill-white" />
              </div>
              <span className="font-bold text-slate-900 text-lg">HeavenlyMatch</span>
            </Link>
            <div className="flex items-center gap-3">
              <Link href={route('how-it-works')} className="hidden sm:block text-sm text-slate-600 hover:text-slate-900">
                How it works
              </Link>
              <Link href={route('pricing')} className="hidden sm:block text-sm text-slate-600 hover:text-slate-900">
                Pricing
              </Link>
              <Link href={route('login')}>
                <Button variant="outline" size="sm">Sign in</Button>
              </Link>
              <Link href={route('register')}>
                <Button size="sm">Join Free</Button>
              </Link>
            </div>
          </div>
        </nav>

        {/* Hero */}
        <section className="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-emerald-50 py-20 px-4">
          <div className="max-w-4xl mx-auto text-center">
            <div className="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 rounded-full px-4 py-1.5 text-sm font-medium mb-6">
              <Shield size={14} />
              Bangladesh's most trusted Halal matrimony platform
            </div>

            <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-tight mb-6">
              Find Your Perfect{' '}
              <span className="gradient-text">Halal Match</span>
            </h1>

            <p className="text-lg text-slate-600 max-w-2xl mx-auto mb-10">
              Join thousands of Muslim families finding their life partners with dignity, privacy, and Shariah compliance.
              Guardian-notified. Photo-protected. AI-matched.
            </p>

            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link href={route('register')}>
                <Button size="xl" className="w-full sm:w-auto shadow-lg shadow-primary-200">
                  Create Free Profile →
                </Button>
              </Link>
              <Link href={route('how-it-works')}>
                <Button variant="outline" size="xl" className="w-full sm:w-auto">
                  How It Works
                </Button>
              </Link>
            </div>

            <p className="mt-4 text-xs text-slate-400">
              Free to join · No credit card required · Verified profiles
            </p>
          </div>

          {/* Stats */}
          <div className="max-w-4xl mx-auto mt-16 grid grid-cols-2 sm:grid-cols-4 gap-6">
            {[
              { label: 'Active Members', value: '50,000+' },
              { label: 'Successful Marriages', value: '3,200+' },
              { label: 'Daily New Profiles', value: '200+' },
              { label: 'Satisfaction Rate', value: '94%' },
            ].map(s => (
              <div key={s.label} className="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                <p className="text-2xl font-extrabold text-primary-600">{s.value}</p>
                <p className="text-xs text-slate-500 mt-1">{s.label}</p>
              </div>
            ))}
          </div>
        </section>

        {/* Modes */}
        <section className="py-20 px-4 bg-white">
          <div className="max-w-5xl mx-auto">
            <h2 className="text-3xl font-bold text-slate-900 text-center mb-3">Two modes, one mission</h2>
            <p className="text-slate-500 text-center mb-12 max-w-xl mx-auto">
              Choose the experience that fits your values. You can switch anytime.
            </p>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="rounded-3xl border-2 border-blue-200 bg-blue-50 p-8">
                <div className="text-4xl mb-4">🌐</div>
                <h3 className="text-xl font-bold text-slate-900 mb-2">General Mode</h3>
                <p className="text-slate-600 text-sm mb-4">
                  Browse freely, photos visible to members, message after mutual interest.
                </p>
                <ul className="space-y-2">
                  {['Photos visible to members', 'Smart AI matching', 'Direct messaging', 'Advanced search filters'].map(f => (
                    <li key={f} className="flex items-center gap-2 text-sm text-slate-700">
                      <CheckCircle size={16} className="text-blue-500 flex-shrink-0" />
                      {f}
                    </li>
                  ))}
                </ul>
              </div>

              <div className="rounded-3xl border-2 border-emerald-400 bg-emerald-50 p-8 relative">
                <div className="absolute -top-3 right-6 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                  ★ Most Chosen
                </div>
                <div className="text-4xl mb-4">☪️</div>
                <h3 className="text-xl font-bold text-slate-900 mb-2">Islamic / Halal Mode</h3>
                <p className="text-slate-600 text-sm mb-4">
                  Biodata-first approach with photo privacy and Wali notification system.
                </p>
                <ul className="space-y-2">
                  {['Photos blurred by default', 'Guardian/Wali notified', 'Request photo access', 'Islamic compatibility scoring'].map(f => (
                    <li key={f} className="flex items-center gap-2 text-sm text-slate-700">
                      <CheckCircle size={16} className="text-emerald-500 flex-shrink-0" />
                      {f}
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </section>

        {/* How it works */}
        <section className="py-20 px-4 bg-slate-50">
          <div className="max-w-5xl mx-auto">
            <h2 className="text-3xl font-bold text-slate-900 text-center mb-12">How it works</h2>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-8">
              {[
                { step: '1', icon: '📝', title: 'Create Biodata', desc: 'Fill your detailed profile in 9 easy steps. More info = better matches.' },
                { step: '2', icon: '💑', title: 'Get Matched', desc: 'Our AI engine scores compatibility across religion, education, location and 20+ factors.' },
                { step: '3', icon: '🤝', title: 'Connect Safely', desc: 'Send interest, chat after acceptance. Guardian notified at every step in Islamic mode.' },
              ].map(s => (
                <div key={s.step} className="text-center">
                  <div className="h-14 w-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-3xl mx-auto mb-4">
                    {s.icon}
                  </div>
                  <div className="inline-block bg-primary-600 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center mb-2">
                    {s.step}
                  </div>
                  <h3 className="font-bold text-slate-900 mb-2">{s.title}</h3>
                  <p className="text-sm text-slate-500">{s.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* CTA */}
        <section className="py-20 px-4 bg-primary-600 text-white">
          <div className="max-w-2xl mx-auto text-center">
            <h2 className="text-3xl font-bold mb-4">Start your journey today</h2>
            <p className="text-primary-100 mb-8">
              Thousands of families found their match on HeavenlyMatch. Your story starts here.
            </p>
            <Link href={route('register')}>
              <Button variant="secondary" size="xl" className="shadow-xl">
                Create Free Profile →
              </Button>
            </Link>
          </div>
        </section>

        {/* Footer */}
        <footer className="border-t border-slate-100 py-10 px-4">
          <div className="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-400">
            <p>© {new Date().getFullYear()} HeavenlyMatch. All rights reserved.</p>
            <div className="flex gap-6">
              <Link href={route('terms')} className="hover:text-slate-600">Terms</Link>
              <Link href={route('privacy')} className="hover:text-slate-600">Privacy</Link>
              <Link href={route('contact')} className="hover:text-slate-600">Contact</Link>
            </div>
          </div>
        </footer>
      </div>
    </>
  )
}
