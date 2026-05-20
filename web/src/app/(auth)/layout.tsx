import type { ReactNode } from "react";
import Link from "next/link";

export default function AuthLayout({ children }: { children: ReactNode }) {
  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 flex flex-col">
      {/* Minimal header */}
      <header className="flex h-16 items-center justify-between px-6">
        <Link
          href="/"
          className="flex items-center gap-2 font-bold text-blue-700 text-xl"
        >
          <span className="text-2xl" role="img" aria-label="rings">💍</span>
          HeavenlyMatch
        </Link>
        <Link
          href="/contact"
          className="text-sm text-slate-500 hover:text-blue-700 transition-colors"
        >
          Need help?
        </Link>
      </header>

      {/* Form area */}
      <main className="flex flex-1 items-center justify-center px-4 py-12">
        {children}
      </main>

      <footer className="py-6 text-center text-xs text-slate-400">
        © {new Date().getFullYear()} HeavenlyMatch ·{" "}
        <Link href="/privacy" className="hover:text-blue-600">Privacy</Link>
        {" · "}
        <Link href="/terms" className="hover:text-blue-600">Terms</Link>
      </footer>
    </div>
  );
}
