import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import bcrypt from "bcryptjs";
import { prisma } from "@/lib/prisma";
import { generateRegistrationId } from "@/lib/utils";
import { queueEmailOtp } from "@/lib/notifications/otp";

const schema = z.object({
  lookingFor: z.enum(["BRIDE", "GROOM"]),
  name: z.string().min(2).max(100),
  gender: z.enum(["MALE", "FEMALE"]),
  email: z
    .string()
    .email()
    .transform((v) => v.toLowerCase().trim()),
  mobile: z.string().regex(/^[0-9]{10,11}$/),
  countryCode: z.string().default("+880"),
  password: z
    .string()
    .min(8)
    .regex(/[A-Z]/)
    .regex(/[0-9]/),
  termsAccepted: z.literal(true),
  platformMode: z.enum(["GENERAL", "ISLAMIC"]).optional().default("GENERAL"),
  preferredLanguage: z.enum(["bn", "en"]).optional().default("bn"),
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
    return NextResponse.json(
      { error: "VALIDATION_ERROR", errors: parsed.error.flatten().fieldErrors },
      { status: 422 }
    );
  }

  const d = parsed.data;

  // Normalise mobile: strip leading 0, prepend country code
  const normalised = `${d.countryCode}${d.mobile.replace(/^0/, "")}`;

  // Check uniqueness in one query
  const existing = await prisma.user.findFirst({
    where: {
      OR: [{ email: d.email }, { mobile: normalised }],
    },
    select: { email: true, mobile: true },
  });

  if (existing?.email === d.email) {
    return NextResponse.json({ error: "EMAIL_EXISTS" }, { status: 409 });
  }
  if (existing?.mobile === normalised) {
    return NextResponse.json({ error: "MOBILE_EXISTS" }, { status: 409 });
  }

  const [passwordHash, lastUser] = await Promise.all([
    bcrypt.hash(d.password, 12),
    prisma.user.findFirst({
      orderBy: { registrationId: "desc" },
      select: { registrationId: true },
    }),
  ]);

  const registrationId = generateRegistrationId(lastUser?.registrationId ?? null);

  const user = await prisma.user.create({
    data: {
      registrationId,
      email: d.email,
      mobile: normalised,
      countryCode: d.countryCode,
      passwordHash,
      platformMode: d.platformMode,
      preferredLanguage: d.preferredLanguage,
      termsAcceptedAt: new Date(),
      accountStatus: "PENDING_VERIFICATION",
    },
    select: {
      id: true,
      registrationId: true,
      email: true,
    },
  });

  // Fire OTP email (non-blocking)
  await queueEmailOtp(user.id, user.email, "email_verify").catch(() => null);

  return NextResponse.json(
    {
      message: "Registration successful. Check your email for the verification code.",
      userId: user.id,
      registrationId: user.registrationId,
    },
    { status: 201 }
  );
}
