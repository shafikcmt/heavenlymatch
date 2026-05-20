import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { prisma } from "@/lib/prisma";
import { getAuthUser, signAccessToken } from "@/lib/auth";

const schema = z.object({
  code: z.string().length(6).regex(/^[0-9]+$/),
});

export async function POST(req: NextRequest) {
  const user = await getAuthUser(req);
  if (!user) return NextResponse.json({ error: "UNAUTHORIZED" }, { status: 401 });

  let body: unknown;
  try { body = await req.json(); } catch {
    return NextResponse.json({ error: "INVALID_JSON" }, { status: 400 });
  }

  const parsed = schema.safeParse(body);
  if (!parsed.success) {
    return NextResponse.json({ error: "INVALID_CODE" }, { status: 422 });
  }

  const dbUser = await prisma.user.findUnique({
    where: { id: user.sub },
    select: { id: true, email: true, emailVerifiedAt: true },
  });

  if (!dbUser) return NextResponse.json({ error: "USER_NOT_FOUND" }, { status: 404 });
  if (dbUser.emailVerifiedAt) {
    return NextResponse.json({ message: "Email already verified" });
  }

  const otp = await prisma.otpCode.findFirst({
    where: {
      target: dbUser.email,
      type: "email_verify",
      code: parsed.data.code,
      usedAt: null,
      expiresAt: { gt: new Date() },
    },
  });

  if (!otp) {
    return NextResponse.json(
      { error: "INVALID_OR_EXPIRED_CODE" },
      { status: 400 }
    );
  }

  await prisma.$transaction([
    prisma.user.update({
      where: { id: dbUser.id },
      data: { emailVerifiedAt: new Date(), accountStatus: "ACTIVE" },
    }),
    prisma.otpCode.update({
      where: { id: otp.id },
      data: { usedAt: new Date() },
    }),
  ]);

  return NextResponse.json({ message: "Email verified successfully" });
}

// Resend OTP
export async function PUT(req: NextRequest) {
  const user = await getAuthUser(req);
  if (!user) return NextResponse.json({ error: "UNAUTHORIZED" }, { status: 401 });

  const dbUser = await prisma.user.findUnique({
    where: { id: user.sub },
    select: { id: true, email: true, emailVerifiedAt: true },
  });

  if (!dbUser) return NextResponse.json({ error: "NOT_FOUND" }, { status: 404 });
  if (dbUser.emailVerifiedAt) {
    return NextResponse.json({ message: "Already verified" });
  }

  // Rate limit: check last OTP sent in the past 60s
  const recent = await prisma.otpCode.findFirst({
    where: {
      target: dbUser.email,
      type: "email_verify",
      createdAt: { gt: new Date(Date.now() - 60_000) },
    },
  });

  if (recent) {
    return NextResponse.json(
      { error: "RESEND_TOO_SOON", retryAfter: 60 },
      { status: 429 }
    );
  }

  const { queueEmailOtp } = await import("@/lib/notifications/otp");
  await queueEmailOtp(dbUser.id, dbUser.email, "email_verify");

  return NextResponse.json({ message: "Verification code resent" });
}
