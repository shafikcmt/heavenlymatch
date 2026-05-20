import type { ReactNode } from "react";
import Link from "next/link";
import { Button } from "@/components/ui/button";

export default function MarketingLayout({ children }: { children: ReactNode }) {
  return (
    <div className="min-h-screen flex flex-col">
      {/* ── Header ─────────────────────────────────────────────── */}
      <header className="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/90 backdrop-blur-sm">
        <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6">
          {/* Logo */}
          <Link
            href="/"
            className="flex items-center gap-2 font-bold text-blue-700 text-xl"
          >
            <span className="text-2xl" role="img" aria-label="rings">💍</span>
            HeavenlyMatch
          </Link>

          {/* Nav */}
          <nav className="hidden md:flex items-center gap-6 text-sm text-slate-600">
            <Link href="/how-it-works" className="hover:text-blue-700 transition-colors">
              How it Works
            </Link>
            <Link href="/success-stories" className="hover:text-blue-700 transition-colors">
              Success Stories
            </Link>
            <Link href="/pricing" className="hover:text-blue-700 transition-colors">
              Pricing
            </Link>
            <Link href="/blog" className="hover:text-blue-700 transition-colors">
              Blog
            </Link>
          </nav>

          {/* Auth buttons */}
          <div className="flex items-center gap-3">
            <Link href="/login">
              <Button variant="ghost" size="sm">Log In</Button>
            </Link>
            <Link href="/register">
              <Button size="sm">Get Started Free</Button>
            </Link>
          </div>
        </div>
      </header>

      {/* ── Page content ──────────────────────────────────────── */}
      <main className="flex-1">{children}</main>

      {/* ── Footer ─────────────────────────────────────────────── */}
      <footer className="border-t border-slate-200 bg-white py-12 mt-16">
        <div className="mx-auto max-w-7xl px-4 sm:px-6">
          <div className="grid grid-cols-2 gap-8 md:grid-cols-4">
            <div>
              <p className="font-bold text-blue-700 text-lg mb-3">
                💍 HeavenlyMatch
              </p>
              <p className="text-sm text-slate-500">
                The most trusted halal matrimony platform for Bangladeshis worldwide.
              </p>
            </div>
            <div>
              <p className="font-semibold text-slate-900 mb-3">Platform</p>
              <ul className="space-y-2 text-sm text-slate-500">
                <li><Link href="/how-it-works" className="hover:text-blue-700">How it Works</Link></li>
                <li><Link href="/pricing" className="hover:text-blue-700">Pricing</Link></li>
                <li><Link href="/success-stories" className="hover:text-blue-700">Success Stories</Link></li>
              </ul>
            </div>
            <div>
              <p className="font-semibold text-slate-900 mb-3">Support</p>
              <ul className="space-y-2 text-sm text-slate-500">
                <li><Link href="/faq" className="hover:text-blue-700">FAQ</Link></li>
                <li><Link href="/contact" className="hover:text-blue-700">Contact Us</Link></li>
                <li><Link href="/blog" className="hover:text-blue-700">Blog</Link></li>
              </ul>
            </div>
            <div>
              <p className="font-semibold text-slate-900 mb-3">Legal</p>
              <ul className="space-y-2 text-sm text-slate-500">
                <li><Link href="/privacy" className="hover:text-blue-700">Privacy Policy</Link></li>
                <li><Link href="/terms" className="hover:text-blue-700">Terms of Service</Link></li>
              </ul>
            </div>
          </div>

          <div className="mt-10 flex flex-col items-center gap-2 border-t border-slate-100 pt-8 text-xs text-slate-400 sm:flex-row sm:justify-between">
            <p>© {new Date().getFullYear()} HeavenlyMatch. All rights reserved.</p>
            <p>Made with ❤️ for the Muslim community worldwide</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
