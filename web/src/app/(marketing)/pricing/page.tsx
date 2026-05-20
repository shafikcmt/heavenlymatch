import type { Metadata } from "next";
import Link from "next/link";
import { CheckCircle, X } from "lucide-react";
import { Button } from "@/components/ui/button";

export const metadata: Metadata = {
  title: "Pricing — HeavenlyMatch",
  description: "Choose the perfect plan to find your life partner on HeavenlyMatch.",
};

const PLANS = [
  {
    name: "Gold",
    badge: null,
    price3: 3900,
    price6: 6900,
    price12: 11900,
    features: [
      "40 contact views/month",
      "200 messages/month",
      "Up to 50 shortlisted profiles",
      "Advanced search filters",
      "Gold member badge",
    ],
    notIncluded: ["Priority placement", "Profile boost", "Family support team"],
    popular: false,
    ctaVariant: "outline" as const,
  },
  {
    name: "Diamond",
    badge: "Most Popular",
    price3: 6900,
    price6: 11900,
    price12: 19900,
    features: [
      "100 contact views/month",
      "Unlimited messages",
      "Unlimited shortlist",
      "Priority placement in search",
      "1 free 24h profile boost",
      "Family support team",
      "Diamond member badge",
    ],
    notIncluded: [],
    popular: true,
    ctaVariant: "default" as const,
  },
  {
    name: "Platinum",
    badge: null,
    price3: 9900,
    price6: 16900,
    price12: 29900,
    features: [
      "Unlimited contact views",
      "Unlimited messages",
      "Unlimited shortlist",
      "Priority placement in search",
      "1 free 72h profile boost",
      "Family support team",
      "Platinum member badge",
      "Dedicated relationship advisor",
    ],
    notIncluded: [],
    popular: false,
    ctaVariant: "outline" as const,
  },
];

const FAQ = [
  {
    q: "Can I cancel my subscription at any time?",
    a: "Yes. You can cancel your subscription from Settings → Account at any time. Access continues until the end of the billing period.",
  },
  {
    q: "Is there a free trial?",
    a: "Yes — you can register and browse profiles for free. A paid plan is required to view contact details and send messages.",
  },
  {
    q: "What payment methods are accepted?",
    a: "We accept bKash, Nagad, SSLCommerz, Visa, and Mastercard. Manual bank transfer is available for Gold and above.",
  },
  {
    q: "Do you offer a money-back guarantee?",
    a: "We offer a 3-day refund if you are not satisfied with your purchase. Contact support@heavenlymatch.net within 72 hours of payment.",
  },
  {
    q: "Can I upgrade or downgrade my plan?",
    a: "Yes. You can change your plan at any time. Upgrades are pro-rated immediately; downgrades take effect at the next billing cycle.",
  },
];

export default function PricingPage() {
  return (
    <>
      {/* Hero */}
      <section className="py-16 text-center bg-gradient-to-b from-slate-50 to-white">
        <h1 className="text-4xl font-extrabold text-slate-900 mb-3">
          Simple, Transparent Pricing
        </h1>
        <p className="text-slate-500 max-w-xl mx-auto">
          Start free. Upgrade to find your life partner faster with premium features.
        </p>
      </section>

      {/* Plans */}
      <section className="pb-16 px-4">
        <div className="mx-auto max-w-5xl">
          <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
            {PLANS.map((plan) => (
              <div
                key={plan.name}
                className={`relative rounded-2xl border-2 p-7 flex flex-col ${
                  plan.popular
                    ? "border-blue-600 shadow-lg shadow-blue-100"
                    : "border-slate-200 bg-white"
                }`}
              >
                {plan.badge && (
                  <div className="absolute -top-4 left-1/2 -translate-x-1/2">
                    <span className="rounded-full bg-blue-600 px-5 py-1.5 text-xs font-bold text-white shadow">
                      ★ {plan.badge}
                    </span>
                  </div>
                )}

                <h3 className="text-xl font-bold text-slate-900 mb-1">{plan.name}</h3>
                <p className="text-3xl font-extrabold text-slate-900 mt-2">
                  ৳{plan.price3.toLocaleString()}
                  <span className="text-sm font-normal text-slate-500"> / 3 months</span>
                </p>
                <p className="text-xs text-slate-400 mb-5">
                  ৳{plan.price6.toLocaleString()} / 6 mo · ৳{plan.price12.toLocaleString()} / 12 mo
                </p>

                <ul className="flex-1 space-y-2.5 mb-6">
                  {plan.features.map((f) => (
                    <li key={f} className="flex items-start gap-2 text-sm text-slate-700">
                      <CheckCircle size={15} className="text-emerald-500 mt-0.5 shrink-0" />
                      {f}
                    </li>
                  ))}
                  {plan.notIncluded.map((f) => (
                    <li key={f} className="flex items-start gap-2 text-sm text-slate-400">
                      <X size={15} className="mt-0.5 shrink-0" />
                      {f}
                    </li>
                  ))}
                </ul>

                <Link href="/register">
                  <Button variant={plan.ctaVariant} className="w-full">
                    Get {plan.name} →
                  </Button>
                </Link>
              </div>
            ))}
          </div>

          <p className="mt-8 text-center text-sm text-slate-500">
            🔒 Secure payment · 3-day money-back guarantee · Cancel anytime
          </p>

          {/* Payment methods */}
          <div className="mt-4 flex flex-wrap justify-center gap-3">
            {["bKash", "Nagad", "SSLCommerz", "Visa", "Mastercard"].map((m) => (
              <span key={m} className="rounded-lg border border-slate-200 bg-white px-4 py-1.5 text-xs font-medium text-slate-500 shadow-sm">
                {m}
              </span>
            ))}
          </div>
        </div>
      </section>

      {/* FAQ */}
      <section className="py-16 bg-slate-50 px-4">
        <div className="mx-auto max-w-2xl">
          <h2 className="text-2xl font-bold text-slate-900 text-center mb-8">
            Frequently Asked Questions
          </h2>
          <div className="space-y-4">
            {FAQ.map((item) => (
              <details
                key={item.q}
                className="rounded-2xl border border-slate-200 bg-white p-5 group"
              >
                <summary className="font-semibold text-slate-900 cursor-pointer list-none flex justify-between items-center gap-2">
                  {item.q}
                  <span className="text-slate-400 group-open:rotate-180 transition-transform text-lg leading-none">⌄</span>
                </summary>
                <p className="mt-3 text-sm text-slate-600 leading-relaxed">{item.a}</p>
              </details>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 text-center px-4">
        <h2 className="text-2xl font-bold text-slate-900 mb-3">Ready to find your partner?</h2>
        <p className="text-slate-500 mb-6">Register free. No credit card required.</p>
        <Link href="/register">
          <Button size="lg">Create Free Account →</Button>
        </Link>
      </section>
    </>
  );
}
