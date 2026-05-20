import { NextRequest, NextResponse } from "next/server";
import { createHash } from "crypto";
import { prisma } from "@/lib/prisma";
import {
  verifyRefreshJwt,
  signAccessToken,
  signRefreshToken,
  buildRefreshCookie,
  REFRESH_COOKIE,
} from "@/lib/auth";

export async function POST(req: NextRequest) {
  const refreshJwt = req.cookies.get(REFRESH_COOKIE)?.value;

  if (!refreshJwt) {
    return NextResponse.json({ error: "NO_REFRESH_TOKEN" }, { status: 401 });
  }

  const claims = await verifyRefreshJwt(refreshJwt);
  if (!claims) {
    const res = NextResponse.json(
      { error: "INVALID_REFRESH_TOKEN" },
      { status: 401 }
    );
    res.cookies.delete(REFRESH_COOKIE);
    return res;
  }

  const tokenHash = createHash("sha256")
    .update(claims.rawToken)
    .digest("hex");

  const stored = await prisma.refreshToken.findUnique({
    where: { tokenHash },
    include: {
      user: {
        include: {
          subscription: { include: { plan: { select: { tier: true } } } },
        },
      },
    },
  });

  if (!stored || stored.revokedAt || stored.expiresAt < new Date()) {
    // Token reuse attack or expired — revoke all tokens for this user
    if (stored) {
      await prisma.refreshToken.updateMany({
        where: { userId: claims.userId, revokedAt: null },
        data: { revokedAt: new Date() },
      });
    }
    const res = NextResponse.json(
      { error: "TOKEN_REUSE_DETECTED" },
      { status: 401 }
    );
    res.cookies.delete(REFRESH_COOKIE);
    return res;
  }

  const { user } = stored;
  const tier = (user.subscription?.plan.tier ?? "FREE") as string;
  const ip =
    req.headers.get("x-forwarded-for")?.split(",")[0]?.trim() ?? null;
  const ua = req.headers.get("user-agent") ?? null;

  // Rotate: revoke old, issue new
  const [accessToken, { jwt: newRefreshJwt, hash: newHash, expiresAt }] =
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

  await prisma.$transaction([
    prisma.refreshToken.update({
      where: { id: stored.id },
      data: { revokedAt: new Date() },
    }),
    prisma.refreshToken.create({
      data: {
        userId: user.id,
        tokenHash: newHash,
        deviceInfo: ua,
        ipAddress: ip,
        expiresAt,
      },
    }),
    prisma.user.update({
      where: { id: user.id },
      data: { lastActiveAt: new Date() },
    }),
  ]);

  const cookie = buildRefreshCookie(newRefreshJwt);
  const response = NextResponse.json({ accessToken });
  response.cookies.set(cookie.name, cookie.value, cookie.options);
  return response;
}
