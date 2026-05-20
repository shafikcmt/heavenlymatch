import { SignJWT, jwtVerify, type JWTPayload } from "jose";
import { createHash, randomBytes, timingSafeEqual } from "crypto";
import { cookies } from "next/headers";
import type { NextRequest } from "next/server";

// ─── Config ───────────────────────────────────────────────────────────────────

const ACCESS_SECRET = new TextEncoder().encode(
  process.env.JWT_ACCESS_SECRET ?? "fallback_dev_only_access_secret_32chars"
);
const REFRESH_SECRET = new TextEncoder().encode(
  process.env.JWT_REFRESH_SECRET ?? "fallback_dev_only_refresh_secret_32chars"
);

export const ACCESS_TTL_S = 15 * 60;          // 15 min
export const REFRESH_TTL_S = 7 * 24 * 60 * 60; // 7 days

export const REFRESH_COOKIE = "hm_rt";

// ─── Payload type ─────────────────────────────────────────────────────────────

export interface TokenPayload extends JWTPayload {
  sub: string;              // User.id (cuid)
  rid: string;              // registrationId HM000001
  role: string | null;
  mode: string;             // platformMode
  tier: string;             // subscriptionTier FREE|SILVER|GOLD|DIAMOND
  ver: boolean;             // emailVerified
}

// ─── Issue ────────────────────────────────────────────────────────────────────

export async function signAccessToken(
  payload: Omit<TokenPayload, "iat" | "exp">
): Promise<string> {
  return new SignJWT(payload)
    .setProtectedHeader({ alg: "HS256" })
    .setIssuedAt()
    .setExpirationTime(`${ACCESS_TTL_S}s`)
    .sign(ACCESS_SECRET);
}

export async function signRefreshToken(userId: string): Promise<{
  jwt: string;
  hash: string;
  expiresAt: Date;
}> {
  const raw = randomBytes(64).toString("hex");
  const hash = createHash("sha256").update(raw).digest("hex");
  const expiresAt = new Date(Date.now() + REFRESH_TTL_S * 1000);

  const jwt = await new SignJWT({ sub: userId })
    .setProtectedHeader({ alg: "HS256" })
    .setIssuedAt()
    .setExpirationTime(`${REFRESH_TTL_S}s`)
    .setJti(raw)
    .sign(REFRESH_SECRET);

  return { jwt, hash, expiresAt };
}

// ─── Verify ───────────────────────────────────────────────────────────────────

export async function verifyAccessToken(
  token: string
): Promise<TokenPayload | null> {
  try {
    const { payload } = await jwtVerify(token, ACCESS_SECRET);
    return payload as TokenPayload;
  } catch {
    return null;
  }
}

export async function verifyRefreshJwt(
  jwt: string
): Promise<{ userId: string; rawToken: string } | null> {
  try {
    const { payload } = await jwtVerify(jwt, REFRESH_SECRET);
    return {
      userId: payload.sub as string,
      rawToken: payload.jti as string,
    };
  } catch {
    return null;
  }
}

// ─── HMAC photo token ─────────────────────────────────────────────────────────

const PHOTO_SECRET =
  process.env.PHOTO_TOKEN_SECRET ?? "fallback_dev_photo_secret_32chars";
const PHOTO_TTL_S = 900; // 15 min

export function issuePhotoToken(
  profileUserId: string,
  viewerId: string
): string {
  const expiry = Math.floor(Date.now() / 1000) + PHOTO_TTL_S;
  const payload = `${profileUserId}|${viewerId}|${expiry}`;
  const sig = createHash("sha256")
    .update(payload + PHOTO_SECRET)
    .digest("hex");
  return Buffer.from(`${payload}|${sig}`).toString("base64url");
}

export function verifyPhotoToken(
  token: string,
  expectedProfileId: string
): { viewerId: string } | null {
  try {
    const decoded = Buffer.from(token, "base64url").toString("utf8");
    const parts = decoded.split("|");
    if (parts.length !== 4) return null;

    const [profileId, viewerId, expiryStr, sig] = parts as [
      string,
      string,
      string,
      string,
    ];

    if (profileId !== expectedProfileId) return null;
    if (Date.now() / 1000 > parseInt(expiryStr, 10)) return null;

    const payload = `${profileId}|${viewerId}|${expiryStr}`;
    const expected = createHash("sha256")
      .update(payload + PHOTO_SECRET)
      .digest("hex");

    if (
      !timingSafeEqual(
        Buffer.from(sig, "hex"),
        Buffer.from(expected, "hex")
      )
    )
      return null;

    return { viewerId };
  } catch {
    return null;
  }
}

// ─── Extract from request ─────────────────────────────────────────────────────

export async function getAuthUser(
  req: NextRequest
): Promise<TokenPayload | null> {
  const auth = req.headers.get("authorization");
  const token = auth?.startsWith("Bearer ") ? auth.slice(7) : null;
  if (!token) return null;
  return verifyAccessToken(token);
}

export async function getAuthUserFromCookies(): Promise<TokenPayload | null> {
  const store = await cookies();
  const token = store.get("hm_at")?.value;
  if (!token) return null;
  return verifyAccessToken(token);
}

// ─── Cookie helpers ───────────────────────────────────────────────────────────

export function buildRefreshCookie(jwt: string) {
  return {
    name: REFRESH_COOKIE,
    value: jwt,
    options: {
      httpOnly: true,
      secure: process.env.NODE_ENV === "production",
      sameSite: "lax" as const,
      maxAge: REFRESH_TTL_S,
      path: "/api/auth",
    },
  };
}
