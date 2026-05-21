import { Link } from '@inertiajs/react'
import { Crown } from 'lucide-react'

export default function GuestLayout({ children, title }: { children: React.ReactNode; title?: string }) {
  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-emerald-50 flex flex-col">
      {/* Header */}
      <header className="flex h-16 items-center px-6">
        <Link href="/" className="flex items-center gap-2.5">
          <div className="h-8 w-8 rounded-xl bg-gradient-to-br from-primary-600 to-violet-600 flex items-center justify-center">
            <Crown size={16} className="text-white" />
          </div>
          <span className="font-bold text-slate-900 tracking-tight text-lg">HeavenlyMatch</span>
        </Link>

        <nav className="ml-auto flex items-center gap-4">
          <Link href="/login" className="text-sm font-medium text-slate-600 hover:text-slate-900">
            Login
          </Link>
          <Link
            href="/register"
            className="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition-colors"
          >
            Register Free
          </Link>
        </nav>
      </header>

      {/* Content */}
      <div className="flex-1 flex items-center justify-center p-4">
        <div className="w-full max-w-md">
          {title && (
            <h1 className="text-2xl font-bold text-slate-900 text-center mb-6">{title}</h1>
          )}
          {children}
        </div>
      </div>

      {/* Footer */}
      <footer className="text-center py-6 text-xs text-slate-400">
        © {new Date().getFullYear()} HeavenlyMatch · Privacy-first matrimony for Muslims
      </footer>
    </div>
  )
}
