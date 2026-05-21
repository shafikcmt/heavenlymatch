import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]): string {
  return twMerge(clsx(inputs))
}

/** Convert height in cm to feet+inches string: 165 → "5′5″" */
export function cmToFeetInches(cm: number): string {
  const totalInches = cm / 2.54
  const feet = Math.floor(totalInches / 12)
  const inches = Math.round(totalInches % 12)
  return `${feet}′${inches}″`
}

/** Calculate age from ISO date string */
export function calcAge(birthDate: string): number {
  const today = new Date()
  const dob = new Date(birthDate)
  let age = today.getFullYear() - dob.getFullYear()
  if (today < new Date(today.getFullYear(), dob.getMonth(), dob.getDate())) age--
  return age
}

/** Format BDT amount: 6900 → "৳6,900" */
export function formatBdt(amount: number): string {
  return `৳${amount.toLocaleString('en-BD')}`
}

/** Relative time: "2 hours ago", "3 days ago" */
export function relativeTime(isoDate: string): string {
  const diff = Date.now() - new Date(isoDate).getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 60) return `${mins}m ago`
  const hrs = Math.floor(mins / 60)
  if (hrs < 24) return `${hrs}h ago`
  const days = Math.floor(hrs / 24)
  return `${days}d ago`
}

/** Score color: 81+ blue, 66+ green, 41+ amber, else red */
export function scoreColor(score: number): string {
  if (score >= 81) return 'text-blue-600'
  if (score >= 66) return 'text-emerald-600'
  if (score >= 41) return 'text-amber-500'
  return 'text-red-500'
}

export function scoreRingColor(score: number): string {
  if (score >= 81) return '#2563eb'
  if (score >= 66) return '#059669'
  if (score >= 41) return '#f59e0b'
  return '#ef4444'
}
