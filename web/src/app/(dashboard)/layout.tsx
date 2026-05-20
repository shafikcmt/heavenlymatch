import type { ReactNode } from "react";
import Link from "next/link";
import {
  Home,
  Users,
  Search,
  Heart,
  MessageCircle,
  Bell,
  Star,
  Settings,
  User,
} from "lucide-react";

const NAV = [
  { href: "/home", icon: Home, label: "Home" },
  { href: "/matches", icon: Star, label: "Matches" },
  { href: "/search", icon: Search, label: "Search" },
  { href: "/interests", icon: Heart, label: "Interests" },
  { href: "/inbox", icon: MessageCircle, label: "Inbox" },
];

const NAV_SECONDARY = [
  { href: "/profile", icon: User, label: "My Profile" },
  { href: "/notifications", icon: Bell, label: "Notifications" },
  { href: "/settings", icon: Settings, label: "Settings" },
];

export default function DashboardLayout({ children }: { children: ReactNode }) {
  return (
    <div className="flex min-h-screen bg-slate-50">
      {/* Sidebar — desktop */}
      <aside className="hidden lg:flex w-60 shrink-0 flex-col border-r border-slate-200 bg-white px-4 py-6">
        <Link href="/home" className="mb-8 flex items-center gap-2 px-2 font-bold text-blue-700 text-xl">
          <span role="img" aria-label="rings">💍</span> HeavenlyMatch
        </Link>

        <nav className="flex flex-1 flex-col gap-1">
          {NAV.map(({ href, icon: Icon, label }) => (
            <Link
              key={href}
              href={href}
              className="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900"
            >
              <Icon size={18} />
              {label}
            </Link>
          ))}

          <div className="my-3 h-px bg-slate-100" />

          {NAV_SECONDARY.map(({ href, icon: Icon, label }) => (
            <Link
              key={href}
              href={href}
              className="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900"
            >
              <Icon size={18} />
              {label}
            </Link>
          ))}
        </nav>

        <div className="mt-auto">
          <Link
            href="/upgrade"
            className="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-opacity"
          >
            ✨ Upgrade to Gold
          </Link>
        </div>
      </aside>

      {/* Main */}
      <div className="flex flex-1 flex-col">
        {/* Top bar — mobile */}
        <header className="flex lg:hidden h-14 items-center justify-between border-b border-slate-200 bg-white px-4">
          <Link href="/home" className="font-bold text-blue-700">💍 HeavenlyMatch</Link>
          <div className="flex gap-3">
            <Link href="/notifications">
              <Bell size={20} className="text-slate-500" />
            </Link>
            <Link href="/profile">
              <User size={20} className="text-slate-500" />
            </Link>
          </div>
        </header>

        <main className="flex-1 overflow-y-auto px-4 py-6 lg:px-8">
          {children}
        </main>

        {/* Bottom nav — mobile */}
        <nav className="flex lg:hidden border-t border-slate-200 bg-white">
          {NAV.map(({ href, icon: Icon, label }) => (
            <Link
              key={href}
              href={href}
              className="flex flex-1 flex-col items-center py-3 gap-1 text-xs text-slate-500 hover:text-blue-600"
            >
              <Icon size={20} />
              {label}
            </Link>
          ))}
        </nav>
      </div>
    </div>
  );
}
