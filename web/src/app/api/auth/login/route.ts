import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import bcrypt from "bcryptjs";
import { prisma } from "@/lib/prisma";
import {
  signAccessToken,
  signRefreshToken,
  buildRefreshCookie,
} from "@/lib/auth";

const schema = z.object({
  email: z
    .string()
    .email()
    .transform((v) => v.toLowerCase().trim()),
  password: z.string().min(1),
});

export async function POST(req: NextRequest) {
  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ error: "INVALID_JSON" }, { status: 400 });
  }

  const parsed = schema.safeParse(body);
  if (!parsed.success) {
    return NextResponse.json({ error: "INVALID_INPUT" }, { status: 400 });
  }

  const { email, password } = parsed.data;

  const user = await prisma.user.findUnique({
    where: { email },
    include: {
      subscription: { include: { plan: { select: { tier: true } } } },
    },
  });

  // Constant-time compare — prevents user enumeration via timing
  const DUMMY_HASH =
    "$2b$12$LdTw1qgFRs3z5o/vMhfOSOpI3UhgjuGjm.rmzM.NWFDn0h0kMB5Ry";
  const hashToCheck = user?.passwordHash ?? DUMMY_HASH;
  const valid = await bcrypt.compare(password, hashToCheck);

  if (!user || !valid) {
    return NextResponse.json({ error: "INVALID_CREDENTIALS" }, { status: 401 });
  }

  if (user.accountStatus === "SUSPENDED") {
    return NextResponse.json(
      { error: "ACCOUNT_SUSPENDED", reason: user.blockedReason },
      { status: 403 }
    );
  }
  if (user.accountStatus === "DELETED") {
    return NextResponse.json({ error: "ACCOUNT_DELETED" }, { status: 403 });
  }

  const tier = (user.subscription?.plan.tier ?? "FREE") as string;
  const ip = req.headers.get("x-forwarded-for")?.split(",")[0]?.trim() ?? null;
  const ua = req.headers.get("user-agent") ?? null;

  const [accessToken, { jwt: refreshJwt, hash: refreshHash, expiresAt }] =
    await Promise.all([
      signAccessToken({
        sub: user.id,
        rid: user.registrationId,
        role: user.role,
        mode: user.platformMode,
        tier,
        ver: !!user.emailVerifiedAt,
      }),
      signRefreshToken(user.id),
    ]);

  await Promise.all([
    prisma.refreshToken.create({
      data: {
        userId: user.id,
        tokenHash: refreshHash,
        deviceInfo: ua,
        ipAddress: ip,
        expiresAt,
      },
    }),
    prisma.user.update({
      where: { id: user.id },
      data: { lastLoginAt: new Date(), lastActiveAt: new Date() },
    }),
  ]);

  const cookie = buildRefreshCookie(refreshJwt);
  const response = NextResponse.json({
    accessToken,
    user: {
      id: user.id,
      registrationId: user.registrationId,
      email: user.email,
      mobile: user.mobile,
      platformMode: user.platformMode,
      role: user.role,
      accountStatus: user.accountStatus,
      subscriptionTier: tier,
      emailVerified: !!user.emailVerifiedAt,
      mobileVerified: !!user.mobileVerifiedAt,
    },
  });

  response.cookies.set(cookie.name, cookie.value, cookie.options);
  return response;
}
