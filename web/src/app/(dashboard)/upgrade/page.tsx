import Link from "next/link";
import { CheckCircle, X, Zap, Crown, Diamond } from "lucide-react";
import { Button } from "@/components/ui/button";

const PLANS = [
  {
    name: "Gold",
    icon: Zap,
    color: "text-amber-600",
    bg: "bg-amber-50",
    border: "border-amber-200",
    prices: { 3: 3900, 6: 6900, 12: 11900 },
    features: [
      { label: "Contact views", value: "40 / month" },
      { label: "Messages", value: "200 / month" },
      { label: "Shortlist", value: "Up to 50" },
      { label: "Priority placement", value: false },
      { label: "Profile boost", value: false },
      { label: "Family support", value: false },
    ],
    popular: false,
    cta: "Get Gold",
  },
  {
    name: "Diamond",
    icon: Diamond,
    color: "text-blue-600",
    bg: "bg-blue-50",
    border: "border-blue-500",
    prices: { 3: 6900, 6: 11900, 12: 19900 },
    features: [
      { label: "Contact views", value: "100 / month" },
      { label: "Messages", value: "Unlimited" },
      { label: "Shortlist", value: "Unlimited" },
      { label: "Priority placement", value: true },
      { label: "Profile boost (24h)", value: "1 included" },
      { label: "Family support", value: true },
    ],
    popular: true,
    cta: "Get Diamond",
  },
  {
    name: "Platinum",
    icon: Crown,
    color: "text-violet-600",
    bg: "bg-violet-50",
    border: "border-violet-200",
    prices: { 3: 9900, 6: 16900, 12: 29900 },
    features: [
      { label: "Contact views", value: "Unlimited" },
      { label: "Messages", value: "Unlimited" },
      { label: "Shortlist", value: "Unlimited" },
      { label: "Priority placement", value: true },
      { label: "Profile boost (72h)", value: "1 included" },
      { label: "Family support", value: true },
    ],
    popular: false,
    cta: "Get Platinum",
  },
] as const;

type Duration = 3 | 6 | 12;

// ── Page (server component — client duration picker would need "use client") ───

export default function UpgradePage() {
  const duration: Duration = 3;

  return (
    <div className="max-w-4xl mx-auto">
      <div className="text-center mb-8">
        <h1 className="text-2xl font-extrabold text-slate-900">Unlock Your Perfect Match</h1>
        <p className="mt-2 text-slate-500">
          Premium members get 5× more responses and access to all profiles.
        </p>
      </div>

      {/* Duration toggle — static, needs client for interactivity */}
      <div className="mb-8 flex justify-center">
        <div className="inline-flex rounded-xl border border-slate-200 bg-white p-1 shadow-sm gap-1">
          {([3, 6, 12] as Duration[]).map((d) => (
            <span
              key={d}
              className={`rounded-lg px-4 py-2 text-sm font-semibold cursor-pointer transition-colors ${
                d === duration
                  ? "bg-blue-700 text-white shadow-sm"
                  : "text-slate-600 hover:bg-slate-100"
              }`}
            >
              {d} months
              {d === 6 && <span className="ml-1.5 rounded-full bg-emerald-500 px-1.5 py-0.5 text-[10px] text-white">-17%</span>}
              {d === 12 && <span className="ml-1.5 rounded-full bg-emerald-500 px-1.5 py-0.5 text-[10px] text-white">-50%</span>}
            </span>
          ))}
        </div>
      </div>

      {/* Plan cards */}
      <div className="grid grid-cols-1 gap-5 md:grid-cols-3">
        {PLANS.map((plan) => {
          const Icon = plan.icon;
          const price = plan.prices[duration];
          const perMonth = Math.round(price / duration);

          return (
            <div
              key={plan.name}
              className={`relative rounded-2xl border-2 p-6 ${plan.border} ${plan.bg} flex flex-col`}
            >
              {plan.popular && (
                <div className="absolute -top-3.5 left-1/2 -translate-x-1/2">
                  <span className="rounded-full bg-blue-600 px-4 py-1 text-xs font-bold text-white shadow">
                    ★ Most Popular
                  </span>
                </div>
              )}

              <div className={`mb-3 inline-flex items-center gap-2 font-bold text-xl ${plan.color}`}>
                <Icon size={22} />
                {plan.name}
              </div>

              <div className="mb-5">
                <span className="text-3xl font-extrabold text-slate-900">৳{price.toLocaleString()}</span>
                <span className="text-slate-500 text-sm"> / {duration} months</span>
                <p className="text-xs text-slate-500 mt-0.5">৳{perMonth.toLocaleString()} / month</p>
              </div>

              <ul className="flex-1 space-y-2 mb-6">
                {plan.features.map((f) => (
                  <li key={f.label} className="flex items-center justify-between text-sm">
                    <span className="text-slate-600">{f.label}</span>
                    {f.value === true ? (
                      <CheckCircle size={15} className="text-emerald-500" />
                    ) : f.value === false ? (
                      <X size={15} className="text-slate-300" />
                    ) : (
                      <span className="font-medium text-slate-800">{f.value}</span>
                    )}
                  </li>
                ))}
              </ul>

              <Button
                className="w-full"
                variant={plan.popular ? "default" : "outline"}
              >
                {plan.cta}
              </Button>
            </div>
          );
        })}
      </div>

      {/* Pay-per-use */}
      <div className="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h3 className="font-bold text-slate-900 mb-4">Pay-per-use options</h3>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          {[
            { name: "Contact Unlock", price: "৳199", desc: "See full contact details of one profile" },
            { name: "Profile Boost", price: "৳299", desc: "Feature your profile for 24 hours" },
            { name: "7-Day Spotlight", price: "৳999", desc: "Top placement in search results for 7 days" },
          ].map((item) => (
            <div key={item.name} className="rounded-xl border border-slate-200 p-4">
              <p className="font-semibold text-slate-900">{item.name}</p>
              <p className="text-xl font-bold text-blue-700 mt-1">{item.price}</p>
              <p className="text-xs text-slate-500 mt-1">{item.desc}</p>
              <button className="mt-3 w-full rounded-lg border border-blue-200 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50 transition-colors">
                Purchase
              </button>
            </div>
          ))}
        </div>
      </div>

      {/* Guarantee */}
      <p className="mt-6 text-center text-sm text-slate-500">
        🔒 Secure payment · 3-day money-back guarantee · Cancel anytime
      </p>

      {/* Payment methods */}
      <div className="mt-3 flex flex-wrap justify-center gap-3 text-xs text-slate-400">
        {["bKash", "Nagad", "SSLCommerz", "Visa/Mastercard"].map((m) => (
          <span key={m} className="rounded-lg border border-slate-200 bg-white px-3 py-1.5 font-medium">
            {m}
          </span>
        ))}
      </div>
    </div>
  );
}
