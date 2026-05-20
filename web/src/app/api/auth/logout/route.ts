import { NextRequest, NextResponse } from "next/server";
import { createHash } from "crypto";
import { prisma } from "@/lib/prisma";
import { verifyRefreshJwt, REFRESH_COOKIE } from "@/lib/auth";

export async function POST(req: NextRequest) {
  const refreshJwt = req.cookies.get(REFRESH_COOKIE)?.value;

  if (refreshJwt) {
    const claims = await verifyRefreshJwt(refreshJwt).catch(() => null);
    if (claims) {
      const tokenHash = createHash("sha256")
        .update(claims.rawToken)
        .digest("hex");
      // Revoke this specific token (not all — allows multi-device)
      await prisma.refreshToken
        .updateMany({
          where: { tokenHash, revokedAt: null },
          data: { revokedAt: new Date() },
        })
        .catch(() => null);
    }
  }

  const response = NextResponse.json({ message: "Logged out" });
  response.cookies.delete(REFRESH_COOKIE);
  return response;
}
