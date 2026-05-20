import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { prisma } from "@/lib/prisma";
import { getAuthUser } from "@/lib/auth";

const verifySchema = z.object({
  code: z.string().length(6).regex(/^[0-9]+$/),
});

const sendSchema = z.object({
  mobile: z.string().regex(/^[0-9]{10,11}$/),
  countryCode: z.string().default("+880"),
});

// POST: verify the OTP
export async function POST(req: NextRequest) {
  const user = await getAuthUser(req);
  if (!user) return NextResponse.json({ error: "UNAUTHORIZED" }, { status: 401 });

  const parsed = verifySchema.safeParse(await req.json().catch(() => ({})));
  if (!parsed.success) {
    return NextResponse.json({ error: "INVALID_CODE" }, { status: 422 });
  }

  const dbUser = await prisma.user.findUnique({
    where: { id: user.sub },
    select: { id: true, mobile: true, mobileVerifiedAt: true },
  });

  if (!dbUser) return NextResponse.json({ error: "NOT_FOUND" }, { status: 404 });
  if (dbUser.mobileVerifiedAt) {
    return NextResponse.json({ message: "Phone already verified" });
  }

  const otp = await prisma.otpCode.findFirst({
    where: {
      target: dbUser.mobile,
      type: "phone_verify",
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
      data: { mobileVerifiedAt: new Date() },
    }),
    prisma.otpCode.update({
      where: { id: otp.id },
      data: { usedAt: new Date() },
    }),
  ]);

  return NextResponse.json({ message: "Phone verified successfully" });
}

// PUT: send phone OTP
export async function PUT(req: NextRequest) {
  const user = await getAuthUser(req);
  if (!user) return NextResponse.json({ error: "UNAUTHORIZED" }, { status: 401 });

  const parsed = sendSchema.safeParse(await req.json().catch(() => ({})));
  if (!parsed.success) {
    return NextResponse.json(
      { error: "VALIDATION_ERROR", errors: parsed.error.flatten() },
      { status: 422 }
    );
  }

  const normalised = `${parsed.data.countryCode}${parsed.data.mobile.replace(/^0/, "")}`;

  // Check mobile not taken by another user
  const conflict = await prisma.user.findFirst({
    where: { mobile: normalised, id: { not: user.sub } },
    select: { id: true },
  });
  if (conflict) {
    return NextResponse.json({ error: "MOBILE_IN_USE" }, { status: 409 });
  }

  // Rate limit: 60s cooldown
  const recent = await prisma.otpCode.findFirst({
    where: {
      target: normalised,
      type: "phone_verify",
      createdAt: { gt: new Date(Date.now() - 60_000) },
    },
  });
  if (recent) {
    return NextResponse.json(
      { error: "RESEND_TOO_SOON", retryAfter: 60 },
      { status: 429 }
    );
  }

  // Update mobile on user record before sending OTP
  await prisma.user.update({
    where: { id: user.sub },
    data: { mobile: normalised, mobileVerifiedAt: null },
  });

  const { queueSmsOtp } = await import("@/lib/notifications/otp");
  await queueSmsOtp(user.sub, normalised, "phone_verify");

  return NextResponse.json({ message: "Verification code sent" });
}
