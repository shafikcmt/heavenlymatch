export interface AuthUser {
  registration_id: string
  name: string
  gender: 'male' | 'female'
  platform_mode: 'general' | 'islamic'
  photo_visibility: 'public' | 'members_only' | 'blurred'
  account_status: 'active' | 'inactive' | 'suspended' | 'banned'
  role: string
  is_admin: boolean
  membership_status: 'free' | 'active' | 'expired' | 'cancelled'
  membership_plan: string | null
  membership_expires: string | null
  is_email_verified: boolean
  biodata_status: 'draft' | 'pending' | 'approved' | 'rejected' | 'hidden' | null
  biodata_complete: boolean
}

export interface Flash {
  success: string | null
  error: string | null
  info: string | null
}

export interface PageProps {
  auth: { user: AuthUser | null }
  flash: Flash
  ziggy: { location: string; url: string; port: number | null; defaults: Record<string, unknown>; routes: Record<string, unknown> }
  locale: 'en' | 'bn'
  translations: Record<string, Record<string, unknown>>
  [key: string]: unknown
}

export interface ProfileCard {
  registration_id: string
  name: string
  gender: 'male' | 'female'
  age: number | null
  marital_status: string | null
  religion: string
  sect: string | null
  highest_qualification: string | null
  occupation: string | null
  occupation_category: string | null
  district: string | null
  division: string | null
  residing_country: string
  height_cm: number | null
  is_featured: boolean
  is_verified: boolean
  is_boosted: boolean
  platform_mode: 'general' | 'islamic'
  photo_visibility: 'public' | 'members_only' | 'blurred'
  has_photo: boolean
  photo_url: string | null
  completeness_score: number
  last_active_at: string | null
  match_score: number | null
  score_breakdown: Record<string, number> | null
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  from: number | null
  last_page: number
  per_page: number
  to: number | null
  total: number
  prev_page_url: string | null
  next_page_url: string | null
  first_page_url: string
  last_page_url: string
  links: Array<{ url: string | null; label: string; active: boolean }>
}

export interface MembershipPlan {
  id: number
  name: string
  slug: string
  tier: 'free' | 'silver' | 'gold' | 'diamond'
  price_bdt: number
  duration_months: number
  contact_views_per_month: number | null
  messages_per_month: number | null
  max_shortlist: number | null
  features: string[]
  is_popular: boolean
}

export interface PaymentPlan {
  id: number
  name: string
  slug: string
  duration_months: number
  price: number
  currency: string
  badge: string | null
  is_popular: boolean
  features: string[] | null
  priority_placement: boolean
  family_support: boolean
  contact_view_limit: number
  message_limit: number
}

export interface PaymentGatewayOption {
  id: number
  name: string
  slug: string
  type: string
  merchant_id: string | null
  instructions: string | null
}

export interface SearchFilters {
  gender?: 'male' | 'female'
  age_min?: number
  age_max?: number
  religion?: string
  sect?: string
  marital_status?: string
  division?: string
  district?: string
  residing_country?: string
  height_cm_min?: number
  height_cm_max?: number
  occupation_category?: string
  income_min?: number
  income_max?: number
  highest_qualification?: string
  family_type?: string
  is_practicing?: boolean
  has_beard?: boolean
  wears_hijab?: boolean
  platform_mode?: 'general' | 'islamic'
  sort?: 'match_score' | 'newest' | 'last_active' | 'featured'
  page?: number
}
