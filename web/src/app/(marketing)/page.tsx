import type { Metadata } from "next";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
  ShieldCheck,
  Users,
  Heart,
  Star,
  Lock,
  Globe,
  CheckCircle2,
} from "lucide-react";

export const metadata: Metadata = {
  title: "HeavenlyMatch — Trusted Halal Matrimony for Bangladeshis Worldwide",
  description:
    "Find your perfect Muslim life partner. Strict privacy controls, guardian/wali involvement, NID-verified profiles. Bangladesh's most trusted matrimony platform.",
};

// ─── Static data (replace with DB calls for live stats) ──────────────────────

const STATS = [
  { value: "50,000+", label: "Registered Profiles" },
  { value: "5,000+", label: "Successful Matches" },
  { value: "95%",    label: "Verified Members" },
  { value: "12+",    label: "Countries" },
];

const FEATURES = [
  {
    icon: ShieldCheck,
    title: "NID & Passport Verified",
    desc: "Every profile is manually reviewed. ID-verified members earn a Gold badge visible to all.",
    color: "text-amber-600 bg-amber-50",
  },
  {
    icon: Lock,
    title: "Photos Stay Private",
    desc: "Choose who sees your photos. Blur by default, reveal only to accepted matches or by request.",
    color: "text-blue-600 bg-blue-50",
  },
  {
    icon: Users,
    title: "Guardian / Wali Involvement",
    desc: "In Islamic mode, your Wali receives SMS notifications for every connection request.",
    color: "text-emerald-600 bg-emerald-50",
  },
  {
    icon: Heart,
    title: "AI Compatibility Matching",
    desc: "Our 10-factor engine scores every profile for you — religion, location, education, lifestyle and more.",
    color: "text-rose-600 bg-rose-50",
  },
  {
    icon: Globe,
    title: "NRB Friendly",
    desc: "Dedicated filters for Bangladeshis living in UK, UAE, USA, Australia, Malaysia & Canada.",
    color: "text-violet-600 bg-violet-50",
  },
  {
    icon: Star,
    title: "Premium Trust Rating",
    desc: "Completeness score, activity badges, and admin-approved status — know who you are talking to.",
    color: "text-cyan-600 bg-cyan-50",
  },
];

const HOW_IT_WORKS = [
  {
    step: "01",
    title: "Create Your Profile",
    desc: "Fill in your 10-step biodata — education, family, religion, lifestyle, and what you are looking for.",
  },
  {
    step: "02",
    title: "Get Verified",
    desc: "Upload your NID or passport for a Verified badge that builds instant trust.",
  },
  {
    step: "03",
    title: "Browse & Match",
    desc: "Our AI suggests daily matches. Use 15+ filters to narrow your search to the perfect candidate.",
  },
  {
    step: "04",
    title: "Connect Safely",
    desc: "Send a connection request. In Islamic mode, your guardian is notified first.",
  },
];

const SUCCESS_STORIES = [
  {
    groom: "Rafiqul Islam",
    bride: "Fatema Begum",
    location: "Dhaka, Bangladesh",
    quote: "We found each other through HeavenlyMatch's daily match feature. Alhamdulillah, married in 3 months.",
    year: "2024",
  },
  {
    groom: "Tarek Hassan",
    bride: "Nusrat Jahan",
    location: "London, UK → Sylhet",
    quote: "As an NRB, I was worried about finding a practicing Muslim partner. HeavenlyMatch made it easy.",
    year: "2024",
  },
  {
    groom: "Abdullah Al-Mamun",
    bride: "Sumaiya Akter",
    location: "Chittagong, Bangladesh",
    quote: "The Islamic mode and guardian notifications gave our families the confidence to proceed.",
    year: "2025",
  },
];

// ─── Page ─────────────────────────────────────────────────────────────────────

export default function LandingPage() {
  return (
    <>
      {/* ── Hero ─────────────────────────────────────────────────────────────── */}
      <section className="relative overflow-hidden bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 text-white">
        {/* Decorative circles */}
        <div className="pointer-events-none absolute -top-32 -right-32 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl" />
        <div className="pointer-events-none absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl" />

        <div className="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:py-40">
          <div className="flex flex-col items-center text-center gap-6">
            <Badge className="bg-white/20 text-white border-0 text-sm px-4 py-1">
              🌟 Bangladesh&apos;s Most Trusted Halal Matrimony
            </Badge>

            <h1 className="text-4xl font-extrabold tracking-tight sm:text-6xl lg:text-7xl text-balance max-w-4xl">
              Find Your{" "}
              <span className="text-amber-400">Perfect</span>{" "}
              Halal Life Partner
            </h1>

            <p className="max-w-2xl text-lg text-blue-100 text-balance">
              Join 50,000+ Muslims from Bangladesh and around the world. With strict
              privacy controls, guardian involvement, and AI-powered matching —
              finding your soulmate has never been this safe or this easy.
            </p>

            {/* Quick search */}
            <div className="mt-4 flex w-full max-w-2xl flex-col gap-3 rounded-2xl bg-white/10 p-4 backdrop-blur-sm sm:flex-row">
              <select className="flex-1 rounded-lg bg-white px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">Looking for…</option>
                <option value="BRIDE">A Bride (Female)</option>
                <option value="GROOM">A Groom (Male)</option>
              </select>
              <select className="flex-1 rounded-lg bg-white px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">Age range</option>
                <option value="18-25">18 – 25</option>
                <option value="26-30">26 – 30</option>
                <option value="31-35">31 – 35</option>
                <option value="36-45">36 – 45</option>
              </select>
              <Link href="/register" className="shrink-0">
                <Button size="lg" variant="secondary" className="w-full sm:w-auto">
                  Search Free →
                </Button>
              </Link>
            </div>

            <p className="text-sm text-blue-200">
              Free to join · No credit card required
            </p>
          </div>
        </div>
      </section>

      {/* ── Stats ──────────────────────────────────────────────────────────────── */}
      <section className="border-y border-slate-200 bg-white py-10">
        <div className="mx-auto max-w-5xl px-4 sm:px-6">
          <dl className="grid grid-cols-2 gap-6 text-center sm:grid-cols-4">
            {STATS.map((s) => (
              <div key={s.label}>
                <dt className="text-3xl font-extrabold text-blue-700">{s.value}</dt>
                <dd className="mt-1 text-sm text-slate-500">{s.label}</dd>
              </div>
            ))}
          </dl>
        </div>
      </section>

      {/* ── Mode selector ──────────────────────────────────────────────────────── */}
      <section className="py-16 bg-slate-50">
        <div className="mx-auto max-w-5xl px-4 sm:px-6">
          <div className="text-center mb-10">
            <h2 className="text-3xl font-bold text-slate-900">
              Choose Your Experience
            </h2>
            <p className="mt-2 text-slate-500">
              Two distinct modes designed for different needs
            </p>
          </div>

          <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
            {/* General Mode */}
            <Link
              href="/register?mode=GENERAL"
              className="group relative rounded-2xl border-2 border-slate-200 bg-white p-8 hover:border-blue-500 hover:shadow-lg transition-all duration-200"
            >
              <div className="text-4xl mb-4">💍</div>
              <h3 className="text-xl font-bold text-slate-900 mb-2">
                General Matrimony Mode
              </h3>
              <p className="text-slate-500 text-sm mb-4">
                Modern approach — browse freely, message after mutual interest,
                rich profile with photos visible to members.
              </p>
              <ul className="space-y-1 text-sm text-slate-600">
                {["Advanced AI matching","Open communication after acceptance","Full profile browsing"].map((f) => (
                  <li key={f} className="flex items-center gap-2">
                    <CheckCircle2 className="h-4 w-4 text-emerald-500 shrink-0" />
                    {f}
                  </li>
                ))}
              </ul>
              <span className="mt-6 inline-block rounded-lg bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 group-hover:bg-blue-700 group-hover:text-white transition-colors">
                Get Started →
              </span>
            </Link>

            {/* Islamic Mode */}
            <Link
              href="/register?mode=ISLAMIC"
              className="group relative rounded-2xl border-2 border-emerald-200 bg-white p-8 hover:border-emerald-500 hover:shadow-lg transition-all duration-200"
            >
              <Badge variant="success" className="absolute top-4 right-4 text-xs">
                ★ Most Popular
              </Badge>
              <div className="text-4xl mb-4">🕌</div>
              <h3 className="text-xl font-bold text-slate-900 mb-2">
                Islamic / Halal Mode
              </h3>
              <p className="text-slate-500 text-sm mb-4">
                Inspired by Ordeekdin — biodata first, photos blurred by default,
                guardian notifications, controlled communication.
              </p>
              <ul className="space-y-1 text-sm text-slate-600">
                {["Photos blurred until approved","Guardian / Wali SMS notifications","Biodata-first approach"].map((f) => (
                  <li key={f} className="flex items-center gap-2">
                    <CheckCircle2 className="h-4 w-4 text-emerald-500 shrink-0" />
                    {f}
                  </li>
                ))}
              </ul>
              <span className="mt-6 inline-block rounded-lg bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                Get Started →
              </span>
            </Link>
          </div>
        </div>
      </section>

      {/* ── Features ───────────────────────────────────────────────────────────── */}
      <section className="py-16 bg-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-slate-900">
              Why HeavenlyMatch?
            </h2>
            <p className="mt-2 text-slate-500 max-w-xl mx-auto">
              Built specifically for the Muslim community with trust, privacy, and
              Islamic values at the core.
            </p>
          </div>

          <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {FEATURES.map((f) => {
              const Icon = f.icon;
              return (
                <div
                  key={f.title}
                  className="rounded-2xl border border-slate-100 bg-slate-50 p-6 hover:shadow-md transition-shadow"
                >
                  <div className={`mb-4 inline-flex rounded-xl p-3 ${f.color}`}>
                    <Icon size={22} />
                  </div>
                  <h3 className="font-bold text-slate-900 mb-1">{f.title}</h3>
                  <p className="text-sm text-slate-500">{f.desc}</p>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* ── How it works ───────────────────────────────────────────────────────── */}
      <section className="py-16 bg-slate-50">
        <div className="mx-auto max-w-5xl px-4 sm:px-6">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-slate-900">How It Works</h2>
          </div>
          <div className="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
            {HOW_IT_WORKS.map((step) => (
              <div key={step.step} className="text-center">
                <div className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-700 text-white text-xl font-extrabold">
                  {step.step}
                </div>
                <h3 className="font-bold text-slate-900 mb-2">{step.title}</h3>
                <p className="text-sm text-slate-500">{step.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── Success stories ────────────────────────────────────────────────────── */}
      <section className="py-16 bg-white">
        <div className="mx-auto max-w-6xl px-4 sm:px-6">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-slate-900">
              Couples Who Found Love Here
            </h2>
          </div>
          <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
            {SUCCESS_STORIES.map((s) => (
              <div
                key={s.groom}
                className="rounded-2xl border border-slate-100 bg-slate-50 p-6"
              >
                <div className="mb-4 flex items-center gap-3">
                  <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                    {s.groom.charAt(0)}
                  </div>
                  <div>
                    <p className="font-semibold text-sm text-slate-900">
                      {s.groom} & {s.bride}
                    </p>
                    <p className="text-xs text-slate-500">{s.location} · {s.year}</p>
                  </div>
                </div>
                <p className="text-sm text-slate-600 italic">
                  &ldquo;{s.quote}&rdquo;
                </p>
                <div className="mt-3 flex gap-0.5">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} size={14} className="fill-amber-400 text-amber-400" />
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── CTA Banner ─────────────────────────────────────────────────────────── */}
      <section className="bg-gradient-to-r from-blue-700 to-indigo-700 py-16 text-white text-center">
        <div className="mx-auto max-w-2xl px-4">
          <h2 className="text-3xl font-extrabold mb-4">
            Start Your Journey Today
          </h2>
          <p className="text-blue-100 mb-8">
            Join thousands of Muslims who found their perfect partner on HeavenlyMatch.
            Free to register, no credit card required.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/register">
              <Button size="xl" variant="secondary">
                Create Free Account
              </Button>
            </Link>
            <Link href="/how-it-works">
              <Button size="xl" variant="outline" className="border-white text-white hover:bg-white/10">
                Learn More
              </Button>
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
