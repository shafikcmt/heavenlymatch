// ─── Shared API response envelope ────────────────────────────────────────────

export interface ApiSuccess<T = unknown> {
  data: T;
  meta?: Record<string, unknown>;
}

export interface ApiError {
  error: string;
  message?: string;
  errors?: Record<string, string[]>;
}

// ─── Auth ─────────────────────────────────────────────────────────────────────

export interface AuthUser {
  id: string;
  registrationId: string;
  email: string;
  mobile: string;
  platformMode: "GENERAL" | "ISLAMIC";
  role: string | null;
  accountStatus: string;
  subscriptionTier: "FREE" | "SILVER" | "GOLD" | "DIAMOND";
  emailVerified: boolean;
  mobileVerified: boolean;
}

export interface LoginResponse {
  accessToken: string;
  user: AuthUser;
}

// ─── Profile summary (used in cards / lists) ──────────────────────────────────

export interface ProfileCard {
  id: string;
  registrationId: string;
  name: string;
  displayName: string | null;
  age: number | null;
  gender: "MALE" | "FEMALE";
  maritalStatus: string;
  religion: string;
  sect: string | null;
  highestQualification: string | null;
  occupation: string | null;
  homeDivisionId: number | null;
  homeDistrictId: number | null;
  residingCountryId: number | null;
  heightCm: number | null;
  isFeatured: boolean;
  isVerified: boolean;
  isBoosted: boolean;
  platformMode: "GENERAL" | "ISLAMIC";
  photoVisibility: "PUBLIC" | "MEMBERS_ONLY" | "BLURRED";
  hasPhoto: boolean;
  photoId: string | null;
  completenessScore: number;
  lastActive: string | null;
  matchScore: number | null;
  scoreBreakdown: Record<string, number> | null;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    total: number;
    limit: number;
    hasNextPage: boolean;
    nextCursor: string | null;
  };
}

// ─── Notification ─────────────────────────────────────────────────────────────

export interface NotificationItem {
  id: string;
  type: string;
  title: string;
  body: string;
  data: Record<string, unknown> | null;
  readAt: string | null;
  createdAt: string;
}

// ─── Interest ─────────────────────────────────────────────────────────────────

export interface InterestItem {
  id: string;
  senderId: string;
  receiverId: string;
  status: string;
  message: string | null;
  createdAt: string;
  profile: ProfileCard;
}
