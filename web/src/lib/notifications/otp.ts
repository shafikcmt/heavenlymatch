import { prisma } from "@/lib/prisma";

const OTP_TTL_MS = 10 * 60 * 1000; // 10 minutes

function generateOtp(): string {
  return Math.floor(100_000 + Math.random() * 900_000).toString();
}

export async function queueEmailOtp(
  userId: string,
  email: string,
  type: "email_verify" | "password_reset"
): Promise<void> {
  const code = generateOtp();

  await prisma.otpCode.create({
    data: {
      target: email,
      type,
      code,
      expiresAt: new Date(Date.now() + OTP_TTL_MS),
    },
  });

  // In production: push to BullMQ email queue
  // For now, log to console in dev
  if (process.env.NODE_ENV === "development") {
    console.log(`[DEV OTP] ${type} for ${email}: ${code}`);
    return;
  }

  // Production: send via Resend
  try {
    const { Resend } = await import("resend");
    const resend = new Resend(process.env.RESEND_API_KEY);

    const subject =
      type === "email_verify"
        ? "Verify your HeavenlyMatch email"
        : "Reset your HeavenlyMatch password";

    await resend.emails.send({
      from: process.env.EMAIL_FROM ?? "HeavenlyMatch <noreply@heavenlymatch.com>",
      to: email,
      subject,
      html: buildOtpEmail(code, type),
    });
  } catch (err) {
    console.error("[OTP EMAIL ERROR]", err);
  }
}

export async function queueSmsOtp(
  userId: string,
  mobile: string,
  type: "phone_verify" | "guardian_verify"
): Promise<void> {
  const code = generateOtp();

  await prisma.otpCode.create({
    data: {
      target: mobile,
      type,
      code,
      expiresAt: new Date(Date.now() + OTP_TTL_MS),
    },
  });

  if (process.env.NODE_ENV === "development") {
    console.log(`[DEV OTP] ${type} for ${mobile}: ${code}`);
    return;
  }

  try {
    const twilio = (await import("twilio")).default;
    const client = twilio(
      process.env.TWILIO_ACCOUNT_SID!,
      process.env.TWILIO_AUTH_TOKEN!
    );

    await client.messages.create({
      body: `Your HeavenlyMatch verification code is: ${code}. Valid for 10 minutes.`,
      from: process.env.TWILIO_PHONE_NUMBER!,
      to: mobile,
    });
  } catch (err) {
    console.error("[OTP SMS ERROR]", err);
  }
}

function buildOtpEmail(code: string, type: string): string {
  const action =
    type === "email_verify" ? "verify your email" : "reset your password";

  return `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8" />
      <style>
        body { font-family: Inter, sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 12px; padding: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .logo { font-size: 22px; font-weight: 700; color: #1b4fd8; margin-bottom: 24px; }
        .code { font-size: 40px; font-weight: 800; letter-spacing: 8px; color: #1b4fd8; text-align: center; padding: 20px; background: #f0f4ff; border-radius: 8px; margin: 24px 0; }
        .footer { font-size: 12px; color: #94a3b8; margin-top: 24px; }
      </style>
    </head>
    <body>
      <div class="container">
        <div class="logo">HeavenlyMatch ✨</div>
        <h2 style="color:#0f172a;margin:0 0 8px">Your verification code</h2>
        <p style="color:#64748b;margin:0 0 16px">Use this code to ${action}. It expires in 10 minutes.</p>
        <div class="code">${code}</div>
        <p style="color:#64748b;font-size:14px">If you didn't request this, please ignore this email.</p>
        <div class="footer">HeavenlyMatch — The most trusted halal matrimony platform</div>
      </div>
    </body>
    </html>
  `;
}
