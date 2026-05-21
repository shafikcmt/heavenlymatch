import { Head, Link } from '@inertiajs/react'
import { Button } from '@/components/ui/Button'

interface Props { status: number }

const MESSAGES: Record<number, { title: string; desc: string }> = {
  403: { title: 'Access Denied', desc: "You don't have permission to view this page." },
  404: { title: 'Page Not Found', desc: "The page you're looking for doesn't exist." },
  419: { title: 'Session Expired', desc: 'Your session has expired. Please refresh and try again.' },
  500: { title: 'Server Error', desc: 'Something went wrong on our end. Please try again later.' },
}

export default function HttpError({ status }: Props) {
  const { title, desc } = MESSAGES[status] ?? { title: 'Error', desc: 'An unexpected error occurred.' }
  return (
    <>
      <Head title={`${status} — ${title}`} />
      <div className="min-h-screen flex items-center justify-center bg-slate-50 px-4">
        <div className="text-center">
          <p className="text-7xl font-extrabold text-primary-600 mb-4">{status}</p>
          <h1 className="text-2xl font-bold text-slate-900 mb-2">{title}</h1>
          <p className="text-slate-500 mb-8">{desc}</p>
          <Link href="/"><Button>Go Home</Button></Link>
        </div>
      </div>
    </>
  )
}
