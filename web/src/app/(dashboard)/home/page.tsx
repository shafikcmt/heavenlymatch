import Link from "next/link";
import { Search, Heart, Star, MessageCircle, Users } from "lucide-react";

// Quick action card
function ActionCard({
  href,
  icon: Icon,
  title,
  description,
  badge,
}: {
  href: string;
  icon: React.ElementType;
  title: string;
  description: string;
  badge?: string;
}) {
  return (
    <Link
      href={href}
      className="group relative flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-blue-200 hover:shadow-md"
    >
      {badge && (
        <span className="absolute right-4 top-4 rounded-full bg-blue-600 px-2.5 py-0.5 text-xs font-semibold text-white">
          {badge}
        </span>
      )}
      <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition-colors">
        <Icon size={20} />
      </div>
      <div>
        <p className="font-semibold text-slate-900">{title}</p>
        <p className="text-sm text-slate-500">{description}</p>
      </div>
    </Link>
  );
}

// Stat card
function StatCard({ label, value }: { label: string; value: string }) {
  return (
    <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <p className="text-2xl font-bold text-slate-900">{value}</p>
      <p className="mt-1 text-sm text-slate-500">{label}</p>
    </div>
  );
}

export default function HomePage() {
  return (
    <div className="max-w-4xl mx-auto space-y-8">
      {/* Welcome banner */}
      <div className="rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white shadow-sm">
        <p className="text-sm font-medium opacity-80">Welcome back</p>
        <h1 className="mt-1 text-2xl font-bold">Your matches are waiting</h1>
        <p className="mt-2 text-sm opacity-80">
          Complete your profile to get better match recommendations.
        </p>
        <Link
          href="/profile/edit"
          className="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-white/20 px-4 py-2 text-sm font-semibold hover:bg-white/30 transition-colors"
        >
          Complete profile →
        </Link>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <StatCard label="Profile views" value="—" />
        <StatCard label="Interests received" value="—" />
        <StatCard label="Match score avg" value="—" />
        <StatCard label="Profile strength" value="0%" />
      </div>

      {/* Quick actions */}
      <div>
        <h2 className="mb-4 text-lg font-semibold text-slate-900">Quick actions</h2>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <ActionCard
            href="/matches"
            icon={Star}
            title="View matches"
            description="See your AI-curated matches"
            badge="New"
          />
          <ActionCard
            href="/search"
            icon={Search}
            title="Search profiles"
            description="Browse all profiles with filters"
          />
          <ActionCard
            href="/interests"
            icon={Heart}
            title="Interests"
            description="See who expressed interest"
          />
          <ActionCard
            href="/inbox"
            icon={MessageCircle}
            title="Messages"
            description="Chat with your connections"
          />
          <ActionCard
            href="/who-viewed"
            icon={Users}
            title="Who viewed me"
            description="See recent profile visitors"
          />
          <ActionCard
            href="/upgrade"
            icon={Star}
            title="Upgrade plan"
            description="Unlock more features with Gold"
          />
        </div>
      </div>

      {/* Profile completion prompt */}
      <div className="rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <h3 className="font-semibold text-amber-900">Complete your profile</h3>
        <p className="mt-1 text-sm text-amber-700">
          Profiles with photos and complete biodata get 5× more responses.
        </p>
        <div className="mt-3 flex gap-3">
          <Link
            href="/profile/edit"
            className="rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 transition-colors"
          >
            Add biodata
          </Link>
          <Link
            href="/profile/photos"
            className="rounded-xl border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100 transition-colors"
          >
            Upload photos
          </Link>
        </div>
      </div>
    </div>
  );
}
