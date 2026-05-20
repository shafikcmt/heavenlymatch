// =============================================================================
// HeavenlyMatch — Authentication Module (TypeScript / Next.js API Routes)
// JWT Access Token (15min) + Refresh Token (7 days, rotated on use)
// =============================================================================

import { SignJWT, jwtVerify, type JWTPayload } from "jose";
import { cookies } from "next/headers";
import { NextResponse, type NextRequest } from "next/server";
import { prisma } from "@/lib/prisma";
import { createHash, randomBytes } from "crypto";
import bcrypt from "bcryptjs";
import { z } from "zod";

// ─── Config ──────────────────────────────────────────────────────────────────

const ACCESS_SECRET  = new TextEncoder().encode(process.env.JWT_ACCESS_SECRET!);
const REFRESH_SECRET = new TextEncoder().encode(process.env.JWT_REFRESH_SECRET!);
const ACCESS_TTL     = 15 * 60;           // 15 minutes
const REFRESH_TTL    = 7 * 24 * 60 * 60;  // 7 days

// ─── Types ───────────────────────────────────────────────────────────────────

export interface AccessTokenPayload extends JWTPayload {
  sub:             string; // User.id (cuid)
  registrationId:  string; // HM000001
  role:            string | null;
  platformMode:    string;
  subscriptionTier: string;
}

// ─── Token Issuance ──────────────────────────────────────────────────────────

export async function signAccessToken(payload: Omit<AccessTokenPayload, "iat" | "exp">): Promise<string> {
  return new SignJWT(payload)
    .setProtectedHeader({ alg: "HS256" })
    .setIssuedAt()
    .setExpirationTime(`${ACCESS_TTL}s`)
    .sign(ACCESS_SECRET);
}

export async function signRefreshToken(userId: string): Promise<{
  token: string;
  hash: string;
  expiresAt: Date;
}> {
  const raw      = randomBytes(64).toString("hex");
  const hash     = createHash("sha256").update(raw).digest("hex");
  const expiresAt = new Date(Date.now() + REFRESH_TTL * 1000);

  // JWT wrapper (client gets this; we store only the hash)
  const token = await new SignJWT({ sub: userId })
    .setProtectedHeader({ alg: "HS256" })
    .setIssuedAt()
    .setExpirationTime(`${REFRESH_TTL}s`)
    .setJti(raw) // embed raw token as JTI — client sends full JWT back
    .sign(REFRESH_SECRET);

  return { token, hash, expiresAt };
}

export async function verifyAccessToken(token: string): Promise<AccessTokenPayload | null> {
  try {
    const { payload } = await jwtVerify(token, ACCESS_SECRET);
    return payload as AccessTokenPayload;
  } catch {
    return null;
  }
}

export async function verifyAndRotateRefreshToken(
  refreshJwt: string,
  deviceInfo?: string,
  ipAddress?: string
): Promise<{ accessToken: string; refreshToken: string } | null> {
  try {
    const { payload } = await jwtVerify(refreshJwt, REFRESH_SECRET);
    const rawToken  = payload.jti as string;
    const userId    = payload.sub as string;
    const tokenHash = createHash("sha256").update(rawToken).digest("hex");

    // Look up stored hash
    const stored = await prisma.refreshToken.findUnique({
      where: { tokenHash },
      include: { user: { include: { subscription: { include: { plan: true } } } } },
    });

    if (!stored || stored.revokedAt || stored.expiresAt < new Date()) {
      // Token reuse detected or expired — revoke ALL tokens for this user
      if (stored) {
        await prisma.refreshToken.updateMany({
          where: { userId, revokedAt: null },
          data:  { revokedAt: new Date() },
        });
      }
      return null;
    }

    const user = stored.user;

    // ── Rotate: revoke old, issue new ──────────────────────────────────────
    await prisma.$transaction(async (tx) => {
      await tx.refreshToken.update({
        where: { id: stored.id },
        data:  { revokedAt: new Date() },
      });

      const { hash: newHash, expiresAt: newExpiry } = await signRefreshToken(userId);
      await tx.refreshToken.create({
        data: {
          userId,
          tokenHash:  newHash,
          deviceInfo: deviceInfo ?? stored.deviceInfo,
          ipAddress:  ipAddress  ?? stored.ipAddress,
          expiresAt:  newExpiry,
        },
      });
    });

    const tier = user.subscription?.plan.tier ?? "FREE";

    const accessToken = await signAccessToken({
      sub:             user.id,
      registrationId:  user.registrationId,
      role:            user.role,
      platformMode:    user.platformMode,
      subscriptionTier: tier,
    });

    const { token: newRefreshToken } = await signRefreshToken(userId);

    return { accessToken, refreshToken: newRefreshToken };
  } catch {
    return null;
  }
}

// ─── Registration Validation ──────────────────────────────────────────────────

const registerSchema = z.object({
  lookingFor:     z.enum(["BRIDE", "GROOM"]),
  name:           z.string().min(2).max(100),
  gender:         z.enum(["MALE", "FEMALE"]),
  email:          z.string().email().max(255),
  mobile:         z.string().regex(/^\+?[0-9]{10,15}$/),
  countryCode:    z.string().default("+880"),
  password:       z.string().min(8).max(72),
  termsAccepted:  z.literal(true),
  platformMode:   z.enum(["GENERAL", "ISLAMIC"]).default("GENERAL"),
  preferredLang:  z.enum(["bn", "en"]).default("bn"),
});

// ─── POST /api/auth/register ──────────────────────────────────────────────────

export async function registerHandler(req: NextRequest) {
  const body   = await req.json();
  const parsed = registerSchema.safeParse(body);

  if (!parsed.success) {
    return NextResponse.json({ errors: parsed.error.flatten() }, { status: 422 });
  }

  const data = parsed.data;

  // Check uniqueness
  const [emailExists, mobileExists] = await Promise.all([
    prisma.user.findUnique({ where: { email: data.email }, select: { id: true } }),
    prisma.user.findUnique({ where: { mobile: data.mobile }, select: { id: true } }),
  ]);

  if (emailExists)  return NextResponse.json({ error: "EMAIL_EXISTS" }, { status: 409 });
  if (mobileExists) return NextResponse.json({ error: "MOBILE_EXISTS" }, { status: 409 });

  const passwordHash = await bcrypt.hash(data.password, 12);
  const registrationId = await generateRegistrationId();

  const user = await prisma.user.create({
    data: {
      registrationId,
      email:            data.email,
      mobile:           `${data.countryCode}${data.mobile.replace(/^0/, "")}`,
      countryCode:      data.countryCode,
      passwordHash,
      platformMode:     data.platformMode,
      preferredLanguage: data.preferredLang,
      termsAcceptedAt:  new Date(),
      accountStatus:    "PENDING_VERIFICATION",
    },
  });

  // Send email OTP (queue to avoid blocking response)
  await queueEmailOtp(user.id, data.email);

  return NextResponse.json(
    { message: "Registration successful. Check your email for the verification code.", userId: user.id },
    { status: 201 }
  );
}

// ─── POST /api/auth/login ─────────────────────────────────────────────────────

const loginSchema = z.object({
  email:    z.string().email(),
  password: z.string().min(1),
});

export async function loginHandler(req: NextRequest) {
  const body   = await req.json();
  const parsed = loginSchema.safeParse(body);

  if (!parsed.success) {
    return NextResponse.json({ error: "INVALID_INPUT" }, { status: 400 });
  }

  const user = await prisma.user.findUnique({
    where:   { email: parsed.data.email },
    include: { subscription: { include: { plan: true } } },
  });

  // Constant-time check to prevent user enumeration
  const dummyHash = "$2b$12$dummy_hash_to_prevent_timing_attack";
  const hash      = user?.passwordHash ?? dummyHash;
  const valid     = await bcrypt.compare(parsed.data.password, hash);

  if (!user || !valid) {
    return NextResponse.json({ error: "INVALID_CREDENTIALS" }, { status: 401 });
  }

  if (user.accountStatus === "SUSPENDED") {
    return NextResponse.json({ error: "ACCOUNT_SUSPENDED", reason: user.blockedReason }, { status: 403 });
  }

  if (user.accountStatus === "DELETED") {
    return NextResponse.json({ error: "ACCOUNT_DELETED" }, { status: 403 });
  }

  const tier = user.subscription?.plan.tier ?? "FREE";
  const ip   = req.headers.get("x-forwarded-for") ?? req.ip;

  const [accessToken, { token: refreshToken, hash: refreshHash, expiresAt }] = await Promise.all([
    signAccessToken({
      sub:             user.id,
      registrationId:  user.registrationId,
      role:            user.role,
      platformMode:    user.platformMode,
      subscriptionTier: tier,
    }),
    signRefreshToken(user.id),
  ]);

  await Promise.all([
    prisma.refreshToken.create({
      data: {
        userId:    user.id,
        tokenHash: refreshHash,
        ipAddress: ip,
        expiresAt,
      },
    }),
    prisma.user.update({
      where: { id: user.id },
      data:  { lastLoginAt: new Date(), lastActiveAt: new Date() },
    }),
  ]);

  const cookieStore = await cookies();
  cookieStore.set("refresh_token", refreshToken, {
    httpOnly: true,
    secure:   process.env.NODE_ENV === "production",
    sameSite: "lax",
    maxAge:   REFRESH_TTL,
    path:     "/api/auth",
  });

  return NextResponse.json({
    accessToken,
    user: {
      id:             user.id,
      registrationId: user.registrationId,
      email:          user.email,
      platformMode:   user.platformMode,
      role:           user.role,
      accountStatus:  user.accountStatus,
      subscriptionTier: tier,
    },
  });
}

// ─── GET /api/auth/refresh ────────────────────────────────────────────────────

export async function refreshHandler(req: NextRequest) {
  const cookieStore  = await cookies();
  const refreshJwt   = cookieStore.get("refresh_token")?.value;

  if (!refreshJwt) {
    return NextResponse.json({ error: "NO_REFRESH_TOKEN" }, { status: 401 });
  }

  const ip      = req.headers.get("x-forwarded-for") ?? req.ip;
  const device  = req.headers.get("user-agent") ?? undefined;
  const tokens  = await verifyAndRotateRefreshToken(refreshJwt, device, ip ?? undefined);

  if (!tokens) {
    const response = NextResponse.json({ error: "INVALID_REFRESH_TOKEN" }, { status: 401 });
    response.cookies.delete("refresh_token");
    return response;
  }

  const response = NextResponse.json({ accessToken: tokens.accessToken });
  response.cookies.set("refresh_token", tokens.refreshToken, {
    httpOnly: true,
    secure:   process.env.NODE_ENV === "production",
    sameSite: "lax",
    maxAge:   REFRESH_TTL,
    path:     "/api/auth",
  });

  return response;
}

// ─── Middleware helper ────────────────────────────────────────────────────────

export async function getAuthUser(req: NextRequest): Promise<AccessTokenPayload | null> {
  const authHeader = req.headers.get("authorization");
  const token      = authHeader?.startsWith("Bearer ") ? authHeader.slice(7) : null;
  if (!token) return null;
  return verifyAccessToken(token);
}

// ─── Utility ─────────────────────────────────────────────────────────────────

async function generateRegistrationId(): Promise<string> {
  const last = await prisma.user.findFirst({
    orderBy: { registrationId: "desc" },
    select:  { registrationId: true },
  });

  const lastNum = last ? parseInt(last.registrationId.replace("HM", ""), 10) : 0;
  return `HM${String(lastNum + 1).padStart(6, "0")}`;
}

async function queueEmailOtp(userId: string, email: string): Promise<void> {
  // Implementation: push to BullMQ email queue
  // Stub shown here for clarity
  const code = Math.floor(100000 + Math.random() * 900000).toString();
  await prisma.otpCode.create({
    data: {
      target:    email,
      type:      "email_verify",
      code,
      expiresAt: new Date(Date.now() + 10 * 60 * 1000), // 10 min
    },
  });
  // TODO: await emailQueue.add("send-otp", { email, code, type: "email_verify" });
}
