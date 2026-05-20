// =============================================================================
// HeavenlyMatch — Photo Privacy System (TypeScript / Next.js API Route)
// All photos live in a PRIVATE S3 bucket.
// This module generates short-lived signed URLs and enforces visibility rules.
// =============================================================================

import { S3Client, GetObjectCommand } from "@aws-sdk/client-s3";
import { getSignedUrl } from "@aws-sdk/s3-request-presigner";
import { createHmac, timingSafeEqual } from "crypto";
import { prisma } from "@/lib/prisma";
import type { NextRequest } from "next/server";
import { NextResponse } from "next/server";

// ─── Config ──────────────────────────────────────────────────────────────────

const s3 = new S3Client({
  region:      process.env.AWS_REGION!,
  credentials: {
    accessKeyId:     process.env.AWS_ACCESS_KEY_ID!,
    secretAccessKey: process.env.AWS_SECRET_ACCESS_KEY!,
  },
});

const S3_BUCKET    = process.env.AWS_S3_PRIVATE_BUCKET!;
const HMAC_SECRET  = process.env.PHOTO_TOKEN_SECRET!; // separate from APP_SECRET
const TOKEN_TTL_S  = 900; // 15 minutes

// ─── Types ───────────────────────────────────────────────────────────────────

export type PhotoResolution = "thumbnail" | "full";

export interface ResolvedPhoto {
  url:         string;       // signed S3 URL
  blurred:     boolean;      // frontend should apply CSS blur
  watermark:   string | null;// registration ID for overlay; null = don't show
  expiresAt:   number;       // Unix timestamp
}

// ─── Token helpers ───────────────────────────────────────────────────────────

/**
 * Issue a short-lived HMAC token.
 * Encodes: profileUserId | viewerId | expiryTimestamp
 * Signed with PHOTO_TOKEN_SECRET so server-side verification is O(1).
 */
export function issuePhotoToken(profileUserId: string, viewerId: string): string {
  const expiry  = Math.floor(Date.now() / 1000) + TOKEN_TTL_S;
  const payload = `${profileUserId}|${viewerId}|${expiry}`;
  const sig     = createHmac("sha256", HMAC_SECRET).update(payload).digest("hex");
  return Buffer.from(`${payload}|${sig}`).toString("base64url");
}

/**
 * Verify a token and return parsed claims.
 * Returns null if invalid / expired / tampered.
 */
function verifyPhotoToken(
  token: string,
  expectedProfileId: string
): { viewerId: string } | null {
  try {
    const decoded  = Buffer.from(token, "base64url").toString("utf8");
    const parts    = decoded.split("|");
    if (parts.length !== 4) return null;

    const [profileId, viewerId, expiryStr, sig] = parts;
    if (profileId !== expectedProfileId) return null;

    const expiry = parseInt(expiryStr, 10);
    if (Date.now() / 1000 > expiry) return null;

    const payload  = `${profileId}|${viewerId}|${expiryStr}`;
    const expected = createHmac("sha256", HMAC_SECRET).update(payload).digest("hex");

    // Constant-time compare to prevent timing attacks
    if (!timingSafeEqual(Buffer.from(sig), Buffer.from(expected))) return null;

    return { viewerId };
  } catch {
    return null;
  }
}

// ─── Visibility Resolver ──────────────────────────────────────────────────────

async function resolveVisibility(
  profileUserId: string,
  viewerId: string | null,
  photoVisibility: string,
  platformMode: string
): Promise<{ show: boolean; blurred: boolean }> {
  // Islamic mode — blur unless photo access is explicitly granted
  if (platformMode === "ISLAMIC") {
    if (!viewerId) return { show: true, blurred: true };

    const grant = await prisma.photoAccessRequest.findUnique({
      where: {
        requesterId_profileId: {
          requesterId: viewerId,
          profileId:   profileUserId, // Using Profile.id stored in relation
        },
      },
      select: { status: true },
    });

    return { show: true, blurred: grant?.status !== "GRANTED" };
  }

  // General mode rules
  switch (photoVisibility) {
    case "PUBLIC":
      return { show: true, blurred: false };

    case "BLURRED":
      return { show: true, blurred: true };

    case "MEMBERS_ONLY":
    default: {
      if (!viewerId) return { show: true, blurred: true };

      const interest = await prisma.interest.findFirst({
        where: {
          status: "ACCEPTED",
          OR: [
            { senderId: viewerId,     receiverId: profileUserId },
            { senderId: profileUserId, receiverId: viewerId },
          ],
        },
        select: { id: true },
      });

      return { show: true, blurred: !interest };
    }
  }
}

// ─── Main API Handler ─────────────────────────────────────────────────────────

/**
 * GET /api/photo/[profileUserId]/[photoIndex]?token=xxx&res=thumbnail
 *
 * Returns JSON with a short-lived S3 signed URL.
 * Never redirects directly to S3 — all access flows through this endpoint.
 */
export async function GET(
  req: NextRequest,
  { params }: { params: { profileUserId: string; photoIndex: string } }
) {
  const { profileUserId, photoIndex: indexStr } = params;
  const token    = req.nextUrl.searchParams.get("token");
  const res      = (req.nextUrl.searchParams.get("res") ?? "thumbnail") as PhotoResolution;
  const photoIdx = parseInt(indexStr ?? "0", 10);

  // ── 1. Verify token ────────────────────────────────────────────────────────
  if (!token) {
    return NextResponse.json({ error: "Missing token" }, { status: 401 });
  }

  const claims = verifyPhotoToken(token, profileUserId);
  if (!claims) {
    return NextResponse.json({ error: "Invalid or expired token" }, { status: 403 });
  }

  const viewerId = claims.viewerId === "guest" ? null : claims.viewerId;

  // ── 2. Load profile & photos ───────────────────────────────────────────────
  const user = await prisma.user.findUnique({
    where:  { id: profileUserId },
    select: {
      platformMode:  true,
      registrationId: true,
      accountStatus: true,
      profile: {
        select: {
          photoVisibility: true,
          photos: {
            where:   { isApproved: true },
            orderBy: [{ isPrimary: "desc" }, { sortOrder: "asc" }],
            select:  { s3Key: true, thumbnailKey: true, isPrimary: true },
          },
        },
      },
    },
  });

  if (!user || user.accountStatus !== "ACTIVE" || !user.profile) {
    return NextResponse.json({ error: "Profile not found" }, { status: 404 });
  }

  const photos = user.profile.photos;
  const photo  = photos[photoIdx];

  if (!photo) {
    return NextResponse.json({ error: "Photo not found" }, { status: 404 });
  }

  // ── 3. Resolve visibility ──────────────────────────────────────────────────
  const { blurred } = await resolveVisibility(
    profileUserId,
    viewerId,
    user.profile.photoVisibility,
    user.platformMode
  );

  // ── 4. Choose S3 key ───────────────────────────────────────────────────────
  // Blurred requests always get the thumbnail (smaller, faster, less data loss)
  const s3Key =
    blurred || res === "thumbnail"
      ? (photo.thumbnailKey ?? photo.s3Key)
      : photo.s3Key;

  // ── 5. Generate signed URL (presigned, 15-min TTL) ─────────────────────────
  const command  = new GetObjectCommand({ Bucket: S3_BUCKET, Key: s3Key });
  const signedUrl = await getSignedUrl(s3, command, { expiresIn: TOKEN_TTL_S });

  // ── 6. Build response ──────────────────────────────────────────────────────
  const resolved: ResolvedPhoto = {
    url:       signedUrl,
    blurred,
    watermark: blurred ? null : user.registrationId,
    expiresAt: Math.floor(Date.now() / 1000) + TOKEN_TTL_S,
  };

  return NextResponse.json(resolved, {
    headers: {
      "Cache-Control": "private, max-age=300, no-store",
      "X-Content-Type-Options": "nosniff",
    },
  });
}

// ─── POST /api/photo/token ────────────────────────────────────────────────────

/**
 * Issues a photo token for the requesting viewer.
 * Call this once per profile page load, then embed the token in all image requests.
 *
 * Body: { profileUserId: string }
 */
export async function issueTokenHandler(req: NextRequest) {
  const body = await req.json();
  const profileUserId = body?.profileUserId as string | undefined;

  if (!profileUserId) {
    return NextResponse.json({ error: "profileUserId required" }, { status: 400 });
  }

  // Viewer ID from session (null for guests)
  const session = await getSessionUser(req);
  const viewerId = session?.id ?? "guest";

  const token = issuePhotoToken(profileUserId, viewerId);
  return NextResponse.json({ token, expiresIn: TOKEN_TTL_S });
}

// ─── React Hook (frontend) ────────────────────────────────────────────────────

export const photoHookCode = `
// hooks/useProfilePhoto.ts
import { useQuery } from "@tanstack/react-query";

interface ProfilePhotoOptions {
  profileUserId: string;
  photoIndex?: number;
  resolution?: "thumbnail" | "full";
}

export function useProfilePhoto({
  profileUserId,
  photoIndex = 0,
  resolution = "thumbnail",
}: ProfilePhotoOptions) {
  // Step 1: Get a signed token for this profile
  const tokenQuery = useQuery({
    queryKey: ["photo-token", profileUserId],
    queryFn: async () => {
      const res = await fetch("/api/photo/token", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ profileUserId }),
      });
      return res.json() as Promise<{ token: string; expiresIn: number }>;
    },
    staleTime: 12 * 60 * 1000, // 12 min (token valid 15 min)
  });

  // Step 2: Get the actual photo URL using the token
  const photoQuery = useQuery({
    queryKey: ["photo", profileUserId, photoIndex, resolution],
    queryFn: async () => {
      const token = tokenQuery.data?.token;
      if (!token) throw new Error("No token");
      const res = await fetch(
        \`/api/photo/\${profileUserId}/\${photoIndex}?token=\${token}&res=\${resolution}\`
      );
      return res.json() as Promise<{
        url: string;
        blurred: boolean;
        watermark: string | null;
        expiresAt: number;
      }>;
    },
    enabled: !!tokenQuery.data?.token,
    staleTime: 12 * 60 * 1000,
  });

  return photoQuery;
}
`;

// ─── Frontend Component ───────────────────────────────────────────────────────

export const profilePhotoComponentCode = `
// components/profile/PrivatePhoto.tsx
"use client";
import Image from "next/image";
import { cn } from "@/lib/utils";
import { useProfilePhoto } from "@/hooks/useProfilePhoto";
import { Lock, Eye } from "lucide-react";

interface PrivatePhotoProps {
  profileUserId:  string;
  photoIndex?:    number;
  resolution?:    "thumbnail" | "full";
  className?:     string;
  size?:          number;
  onRequestAccess?: () => void;
}

export function PrivatePhoto({
  profileUserId,
  photoIndex = 0,
  resolution = "thumbnail",
  className,
  size = 200,
  onRequestAccess,
}: PrivatePhotoProps) {
  const { data, isLoading, isError } = useProfilePhoto({
    profileUserId,
    photoIndex,
    resolution,
  });

  if (isLoading) {
    return (
      <div
        className={cn(
          "animate-pulse bg-gray-200 rounded-lg flex items-center justify-center",
          className
        )}
        style={{ width: size, height: size }}
      >
        <div className="w-10 h-10 bg-gray-300 rounded-full" />
      </div>
    );
  }

  if (isError || !data) {
    return <DefaultAvatar size={size} className={className} />;
  }

  return (
    <div className={cn("relative overflow-hidden rounded-lg group", className)}>
      <Image
        src={data.url}
        alt="Profile photo"
        width={size}
        height={size}
        className={cn(
          "object-cover transition-all duration-300",
          data.blurred && "blur-xl scale-110 brightness-75"
        )}
        // Prevent right-click save
        onContextMenu={(e) => e.preventDefault()}
        draggable={false}
      />

      {/* Watermark overlay (only when unblurred) */}
      {!data.blurred && data.watermark && (
        <div className="absolute bottom-1 right-1 text-[9px] text-white/40 select-none pointer-events-none font-mono">
          {data.watermark} · HeavenlyMatch
        </div>
      )}

      {/* Blur unlock CTA */}
      {data.blurred && (
        <div className="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-black/30 backdrop-blur-sm">
          <Lock className="w-6 h-6 text-white" />
          <span className="text-white text-xs font-medium">Photo Private</span>
          {onRequestAccess && (
            <button
              onClick={onRequestAccess}
              className="mt-1 px-3 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-xs rounded-full flex items-center gap-1 transition-colors"
            >
              <Eye className="w-3 h-3" /> Request Access
            </button>
          )}
        </div>
      )}

      {/* Anti-screenshot CSS overlay (visible but not obstructive) */}
      <div
        className="absolute inset-0 pointer-events-none select-none"
        style={{
          background:
            "repeating-linear-gradient(45deg, transparent, transparent 60px, rgba(255,255,255,0.02) 60px, rgba(255,255,255,0.02) 61px)",
        }}
      />
    </div>
  );
}
`;

// ─── Utility (stub — replace with your actual session implementation) ────────
async function getSessionUser(req: NextRequest): Promise<{ id: string } | null> {
  // Replace with: const session = await auth(); return session?.user ?? null;
  return null;
}
