// =============================================================================
// HeavenlyMatch — Advanced Search API (TypeScript / Next.js / Prisma)
// GET /api/profiles/search
// =============================================================================

import { NextResponse, type NextRequest } from "next/server";
import { z } from "zod";
import { prisma } from "@/lib/prisma";
import { getAuthUser } from "@/lib/auth";
import type { Prisma } from "@prisma/client";

// ─── Validation Schema ────────────────────────────────────────────────────────

const searchSchema = z.object({
  // Basic (all users)
  gender:           z.enum(["MALE", "FEMALE"]).optional(),
  ageMin:           z.coerce.number().int().min(18).max(80).optional(),
  ageMax:           z.coerce.number().int().min(18).max(80).optional(),
  religion:         z.string().max(30).optional(),
  maritalStatus:    z.string().max(30).optional(),
  residingCountryId: z.coerce.number().optional(),
  divisionId:       z.coerce.number().optional(),

  // Premium-only filters (Gold+)
  districtId:       z.coerce.number().optional(),
  sect:             z.string().max(50).optional(),
  occupation:       z.string().max(80).optional(),
  heightCmMin:      z.coerce.number().int().min(100).max(250).optional(),
  heightCmMax:      z.coerce.number().int().min(100).max(250).optional(),
  incomeMin:        z.coerce.number().int().min(0).optional(),
  incomeMax:        z.coerce.number().int().min(0).optional(),
  educationLevel:   z.string().optional(),
  familyType:       z.enum(["JOINT", "NUCLEAR", "FLEXIBLE"]).optional(),
  isPracticing:     z.coerce.boolean().optional(),
  prayerHabits:     z.string().optional(),
  isVerified:       z.coerce.boolean().optional(),

  // Diamond-only
  hasBeard:         z.string().optional(),
  wearsHijab:       z.string().optional(),

  // Sorting & pagination
  sortBy:           z.enum(["match_score", "newest", "last_active", "featured"]).default("match_score"),
  cursor:           z.string().optional(),   // cursor-based pagination (Profile.id)
  limit:            z.coerce.number().int().min(1).max(30).default(12),
});

export type SearchFilters = z.infer<typeof searchSchema>;

// ─── Tier gates ──────────────────────────────────────────────────────────────

const PREMIUM_FILTERS  = ["districtId", "sect", "occupation", "incomeMin", "incomeMax", "educationLevel", "familyType", "isPracticing", "prayerHabits", "isVerified", "heightCmMin", "heightCmMax"] as const;
const DIAMOND_FILTERS  = ["hasBeard", "wearsHijab"] as const;

// ─── Handler ─────────────────────────────────────────────────────────────────

export async function GET(req: NextRequest) {
  const authUser = await getAuthUser(req);
  if (!authUser) {
    return NextResponse.json({ error: "UNAUTHORIZED" }, { status: 401 });
  }

  const params = Object.fromEntries(req.nextUrl.searchParams.entries());
  const parsed = searchSchema.safeParse(params);

  if (!parsed.success) {
    return NextResponse.json({ errors: parsed.error.flatten() }, { status: 422 });
  }

  const filters = parsed.data;
  const tier    = authUser.subscriptionTier; // from JWT

  // ── Enforce premium gates ──────────────────────────────────────────────────
  const isPremium  = ["SILVER", "GOLD", "DIAMOND"].includes(tier);
  const isDiamond  = tier === "DIAMOND";
  const isGoldPlus = ["GOLD", "DIAMOND"].includes(tier);

  for (const key of PREMIUM_FILTERS) {
    if (filters[key] !== undefined && !isGoldPlus) {
      return NextResponse.json({
        error: "PREMIUM_REQUIRED",
        requiredTier: "GOLD",
        filter: key,
      }, { status: 403 });
    }
  }

  for (const key of DIAMOND_FILTERS) {
    if (filters[key] !== undefined && !isDiamond) {
      return NextResponse.json({
        error: "PREMIUM_REQUIRED",
        requiredTier: "DIAMOND",
        filter: key,
      }, { status: 403 });
    }
  }

  // ── Build Prisma where clause ──────────────────────────────────────────────
  const where = buildWhereClause(filters, authUser.sub);

  // ── Build orderBy ──────────────────────────────────────────────────────────
  const orderBy = buildOrderBy(filters.sortBy);

  // ── Execute with cursor pagination ─────────────────────────────────────────
  const [total, profiles] = await Promise.all([
    prisma.profile.count({ where }),
    prisma.profile.findMany({
      where,
      orderBy,
      take:   filters.limit + 1,   // +1 to determine hasNextPage
      cursor: filters.cursor ? { id: filters.cursor } : undefined,
      skip:   filters.cursor ? 1 : 0,
      include: {
        user: {
          select: {
            id: true,
            registrationId: true,
            platformMode: true,
            accountStatus: true,
            lastActiveAt: true,
            subscription: {
              select: { plan: { select: { tier: true } } },
            },
          },
        },
        photos: {
          where:   { isApproved: true, isPrimary: true },
          select:  { id: true, thumbnailKey: true, visibility: true },
          take:    1,
        },
        preferences: {
          select: { partnerAgeMin: true, partnerAgeMax: true },
        },
        // For match score lookup
        _count: { select: { photoAccessRequests: true } },
      },
    }),
  ]);

  const hasNextPage = profiles.length > filters.limit;
  if (hasNextPage) profiles.pop(); // remove the extra item

  // ── Batch-load match scores from cache table ───────────────────────────────
  const profileUserIds = profiles.map((p) => p.user.id);

  const matchScores = await prisma.matchScore.findMany({
    where: {
      userId:      authUser.sub,
      candidateId: { in: profileUserIds },
    },
    select: { candidateId: true, totalScore: true, scoreBreakdown: true },
  });

  const scoreMap = new Map(matchScores.map((ms) => [ms.candidateId, ms]));

  // ── Format response ────────────────────────────────────────────────────────
  const data = profiles.map((profile) => {
    const score = scoreMap.get(profile.user.id);
    return formatProfileCard(profile, score ?? null, tier);
  });

  return NextResponse.json({
    data,
    meta: {
      total,
      limit:      filters.limit,
      hasNextPage,
      nextCursor: hasNextPage ? profiles[profiles.length - 1]?.id : null,
      filtersApplied: Object.keys(filters).filter(
        (k) => filters[k as keyof SearchFilters] !== undefined
      ),
      premiumFiltersActive: isGoldPlus,
    },
  });
}

// ─── Where Clause Builder ─────────────────────────────────────────────────────

function buildWhereClause(
  filters: SearchFilters,
  currentUserId: string
): Prisma.ProfileWhereInput {
  const ageFilters: Prisma.ProfileWhereInput = {};

  if (filters.ageMin || filters.ageMax) {
    const now = new Date();
    ageFilters.dateOfBirth = {};
    if (filters.ageMax) {
      ageFilters.dateOfBirth.gte = new Date(
        now.getFullYear() - filters.ageMax,
        now.getMonth(),
        now.getDate()
      );
    }
    if (filters.ageMin) {
      ageFilters.dateOfBirth.lte = new Date(
        now.getFullYear() - filters.ageMin,
        now.getMonth(),
        now.getDate()
      );
    }
  }

  return {
    // Exclude own profile
    userId:   { not: currentUserId },
    status:   "approved",
    isCompleted: true,

    // Exclude blocked users
    user: {
      accountStatus: "ACTIVE",
      deactivatedAt: null,
      deletionRequestedAt: null,
      blockedBy: { none: { blockerId: currentUserId } },
      blocks:    { none: { blockedId: currentUserId } },
    },

    // ── Basic filters ──────────────────────────────────────────────────────
    ...(filters.gender && { gender: filters.gender }),
    ...(filters.religion && { religion: filters.religion as any }),
    ...(filters.maritalStatus && { maritalStatus: filters.maritalStatus as any }),
    ...(filters.residingCountryId && { residingCountryId: filters.residingCountryId }),
    ...(filters.divisionId && { homeDivisionId: filters.divisionId }),

    // ── Age ────────────────────────────────────────────────────────────────
    ...ageFilters,

    // ── Premium filters ────────────────────────────────────────────────────
    ...(filters.districtId && { homeDistrictId: filters.districtId }),
    ...(filters.sect && { sect: { contains: filters.sect, mode: "insensitive" } }),
    ...(filters.educationLevel && { highestQualification: filters.educationLevel as any }),
    ...(filters.familyType && { familyType: filters.familyType }),
    ...(filters.isPracticing !== undefined && { isPracticing: filters.isPracticing }),
    ...(filters.prayerHabits && { prayerHabits: filters.prayerHabits }),
    ...(filters.isVerified && {
      user: {
        verificationRequests: {
          some: { type: "nid", status: "VERIFIED" },
        },
      },
    }),

    ...(filters.occupation && {
      occupation: { contains: filters.occupation, mode: "insensitive" },
    }),

    ...(filters.heightCmMin && { heightCm: { gte: filters.heightCmMin } }),
    ...(filters.heightCmMax && { heightCm: { lte: filters.heightCmMax } }),
    ...(filters.incomeMin && { monthlyIncome: { gte: filters.incomeMin } }),
    ...(filters.incomeMax && { monthlyIncome: { lte: filters.incomeMax } }),

    // ── Diamond filters ────────────────────────────────────────────────────
    ...(filters.hasBeard && { hasBeard: filters.hasBeard }),
    ...(filters.wearsHijab && { wearsHijab: filters.wearsHijab }),
  };
}

// ─── Order By Builder ─────────────────────────────────────────────────────────

function buildOrderBy(sortBy: string): Prisma.ProfileOrderByWithRelationInput[] {
  const boostSort: Prisma.ProfileOrderByWithRelationInput = {
    user: { boosts: { _count: "desc" } },
  };

  switch (sortBy) {
    case "newest":
      return [boostSort, { createdAt: "desc" }];
    case "last_active":
      return [boostSort, { user: { lastActiveAt: "desc" } }];
    case "featured":
      return [{ isFeatured: "desc" }, { completenessScore: "desc" }];
    case "match_score":
    default:
      return [boostSort, { completenessScore: "desc" }, { user: { lastActiveAt: "desc" } }];
  }
}

// ─── Response Formatter ───────────────────────────────────────────────────────

function formatProfileCard(
  profile: any,
  matchScore: any | null,
  viewerTier: string
): object {
  const age = profile.dateOfBirth
    ? Math.floor((Date.now() - new Date(profile.dateOfBirth).getTime()) / (365.25 * 24 * 3600 * 1000))
    : null;

  const primaryPhoto = profile.photos[0] ?? null;

  return {
    id:                  profile.userId,
    registrationId:      profile.user.registrationId,
    name:                profile.name,
    displayName:         profile.displayName,
    age,
    gender:              profile.gender,
    maritalStatus:       profile.maritalStatus,
    religion:            profile.religion,
    sect:                profile.sect,
    highestQualification: profile.highestQualification,
    occupation:          profile.occupation,
    homeDivisionId:      profile.homeDivisionId,
    homeDistrictId:      profile.homeDistrictId,
    residingCountryId:   profile.residingCountryId,
    heightCm:            profile.heightCm,
    isFeatured:          profile.isFeatured,
    isVerified:          false, // resolved separately if needed
    isBoosted:           profile.user.boosts?.some((b: any) => b.isActive) ?? false,
    platformMode:        profile.user.platformMode,
    photoVisibility:     profile.photoVisibility,
    hasPhoto:            !!primaryPhoto,
    // Don't include S3 key — frontend uses /api/photo/token route
    photoId:             primaryPhoto?.id ?? null,
    completenessScore:   profile.completenessScore,
    lastActive:          profile.user.lastActiveAt,
    matchScore:          matchScore?.totalScore ?? null,
    scoreBreakdown:      viewerTier !== "FREE" ? matchScore?.scoreBreakdown ?? null : null,
  };
}
