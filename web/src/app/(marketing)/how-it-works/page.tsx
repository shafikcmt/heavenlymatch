import type { Metadata } from "next";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import {
  UserPlus, ShieldCheck, Sparkles, MessageCircle, Heart,
} from "lucide-react";

export const metadata: Metadata = {
  title: "How It Works — HeavenlyMatch",
  description: "Learn how HeavenlyMatch helps Muslims find their life partner safely.",
};

const STEPS = [
  {
    number: "01",
    icon: UserPlus,
    title: "Create Your Profile",
    body: "Register in minutes. Fill in your 10-step biodata — education, family background, religious practice, lifestyle, and what you are looking for in a partner.",
    color: "bg-blue-600",
  },
  {
    number: "02",
    icon: ShieldCheck,
    title: "Get Verified",
    body: "Upload your NID or passport. Our team reviews every document manually. Verified members earn a Gold shield badge that builds instant trust with potential matches.",
    color: "bg-emerald-600",
  },
  {
    number: "03",
    icon: Sparkles,
    title: "Browse & Match",
    body: "Our AI analyses 9 compatibility factors — religion, location, education, lifestyle, family values, and more — to score every profile for you. Browse daily picks or search with 15+ filters.",
    color: "bg-violet-600",
  },
  {
    number: "04",
    icon: MessageCircle,
    title: "Connect Safely",
    body: "Send a connection request with an optional introduction message. In Islamic mode, your guardian (Wali) is notified by SMS before the request is accepted.",
    color: "bg-amber-600",
  },
  {
    number: "05",
    icon: Heart,
    title: "Start Your Journey",
    body: "Once accepted, you can message through our encrypted inbox. When you are ready, use the Contact Unlock feature to access phone numbers with family guidance.",
    color: "bg-rose-600",
  },
];

const MODES = [
  {
    title: "General Mode",
    icon: "🌐",
    color: "border-blue-200 bg-blue-50",
    points: [
      "Browse profiles freely with photos visible to members",
      "Message after mutual interest accepted",
      "Full search with 15+ filters",
      "Advanced AI match scoring",
    ],
  },
  {
    title: "Islamic / Halal Mode",
    icon: "☪️",
    color: "border-emerald-200 bg-emerald-50",
    popular: true,
    points: [
      "Biodata-first — photos blurred by default",
      "Guardian / Wali receives SMS for every request",
      "Photo access must be requested and granted",
      "Controlled, moderated communication channel",
    ],
  },
];

export default function HowItWorksPage() {
  return (
    <>
      {/* Hero */}
      <section className="py-16 text-center bg-gradient-to-b from-blue-50 to-white px-4">
        <h1 className="text-4xl font-extrabold text-slate-900 mb-3">
          How HeavenlyMatch Works
        </h1>
        <p className="text-slate-500 max-w-xl mx-auto">
          A safe, structured matrimony experience designed for Muslims — with optional
          guardian involvement at every step.
        </p>
        <Link href="/register" className="mt-6 inline-block">
          <Button size="lg">Get Started Free →</Button>
        </Link>
      </section>

      {/* Steps */}
      <section className="py-16 px-4">
        <div className="mx-auto max-w-3xl">
          <div className="relative">
            {/* Vertical line */}
            <div className="absolute left-7 top-0 bottom-0 w-0.5 bg-slate-100 hidden sm:block" />

            <div className="space-y-10">
              {STEPS.map((step) => {
                const Icon = step.icon;
                return (
                  <div key={step.number} className="flex gap-6">
                    <div className={`relative flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl ${step.color} text-white shadow-md z-10`}>
                      <Icon size={24} />
                    </div>
                    <div className="flex-1 pt-2">
                      <div className="flex items-center gap-2 mb-1">
                        <span className="text-xs font-bold text-slate-400 tracking-widest">STEP {step.number}</span>
                      </div>
                      <h3 className="text-xl font-bold text-slate-900 mb-2">{step.title}</h3>
                      <p className="text-slate-600 leading-relaxed">{step.body}</p>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </section>

      {/* Two modes */}
      <section className="py-16 bg-slate-50 px-4">
        <div className="mx-auto max-w-4xl">
          <h2 className="text-2xl font-bold text-slate-900 text-center mb-8">
            Two Modes, One Platform
          </h2>
          <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
            {MODES.map((mode) => (
              <div
                key={mode.title}
                className={`relative rounded-2xl border-2 p-7 ${mode.color}`}
              >
                {"popular" in mode && mode.popular && (
                  <span className="absolute -top-3 right-5 rounded-full bg-emerald-600 px-4 py-1 text-xs font-bold text-white">
                    ★ Most Chosen
                  </span>
                )}
                <div className="text-3xl mb-3">{mode.icon}</div>
                <h3 className="text-xl font-bold text-slate-900 mb-4">{mode.title}</h3>
                <ul className="space-y-2.5">
                  {mode.points.map((p) => (
                    <li key={p} className="flex items-start gap-2 text-sm text-slate-700">
                      <span className="mt-0.5 h-4 w-4 rounded-full bg-emerald-500 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 12 12" className="h-2.5 w-2.5 text-white" fill="none" stroke="currentColor" strokeWidth="2">
                          <path d="M2 6l3 3 5-5" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                      </span>
                      {p}
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 text-center px-4">
        <h2 className="text-2xl font-bold text-slate-900 mb-3">Ready to begin?</h2>
        <p className="text-slate-500 mb-6">Free to register. Takes 5 minutes.</p>
        <div className="flex flex-col sm:flex-row gap-4 justify-center">
          <Link href="/register?mode=GENERAL">
            <Button variant="outline" size="lg">🌐 General Mode</Button>
          </Link>
          <Link href="/register?mode=ISLAMIC">
            <Button size="lg">☪️ Islamic Mode</Button>
          </Link>
        </div>
      </section>
    </>
  );
}
