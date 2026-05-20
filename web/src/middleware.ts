import { NextRequest, NextResponse } from "next/server";
import { verifyAccessTokenEdge } from "@/lib/auth-edge";

// ─── Route classification ─────────────────────────────────────────────────────

const PUBLIC_PATHS = [
  "/",
  "/about",
  "/how-it-works",
  "/pricing",
  "/success-stories",
  "/blog",
  "/contact",
  "/privacy",
  "/terms",
  "/faq",
];

const AUTH_ONLY_PATHS = ["/home", "/matches", "/search", "/profile", "/inbox",
  "/interests", "/shortlist", "/who-viewed", "/upgrade", "/notifications",
  "/settings", "/verification"];

const ADMIN_PATHS = ["/admin"];

const GUEST_ONLY_PATHS = ["/login", "/register", "/forgot-password"];

// ─── Helpers ─────────────────────────────────────────────────────────────────

function startsWithAny(pathname: string, paths: string[]) {
  return paths.some((p) => pathname === p || pathname.startsWith(p + "/"));
}

// ─── Middleware ───────────────────────────────────────────────────────────────

export async function middleware(req: NextRequest) {
  const { pathname } = req.nextUrl;

  // Skip static assets and Next.js internals
  if (
    pathname.startsWith("/_next") ||
    pathname.startsWith("/favicon") ||
    pathname.startsWith("/icons") ||
    pathname.match(/\.(png|jpg|jpeg|svg|ico|webp|css|js|woff2?)$/)
  ) {
    return NextResponse.next();
  }

  // Extract access token from Authorization header or cookie
  const authHeader = req.headers.get("authorization");
  const bearerToken = authHeader?.startsWith("Bearer ")
    ? authHeader.slice(7)
    : null;
  const cookieToken = req.cookies.get("hm_at")?.value ?? null;
  const token = bearerToken ?? cookieToken;

  const payload = token ? await verifyAccessTokenEdge(token) : null;
  const isAuthenticated = !!payload;

  // ── Guest-only routes (redirect authenticated users away) ─────────────────
  if (startsWithAny(pathname, GUEST_ONLY_PATHS) && isAuthenticated) {
    return NextResponse.redirect(new URL("/home", req.url));
  }

  // ── Protected dashboard routes ────────────────────────────────────────────
  if (startsWithAny(pathname, AUTH_ONLY_PATHS)) {
    if (!isAuthenticated) {
      const loginUrl = new URL("/login", req.url);
      loginUrl.searchParams.set("next", pathname);
      return NextResponse.redirect(loginUrl);
    }

    // Email must be verified to access dashboard
    if (!payload.ver && pathname !== "/verify-email") {
      return NextResponse.redirect(new URL("/verify-email", req.url));
    }
  }

  // ── Admin routes ──────────────────────────────────────────────────────────
  if (startsWithAny(pathname, ADMIN_PATHS)) {
    if (!isAuthenticated) {
      return NextResponse.redirect(new URL("/login?next=/admin", req.url));
    }
    if (!payload.role) {
      return NextResponse.redirect(new URL("/home", req.url));
    }
  }

  // ── Attach user info to headers for server components ────────────────────
  const requestHeaders = new Headers(req.headers);
  if (payload) {
    requestHeaders.set("x-user-id", payload.sub);
    requestHeaders.set("x-user-rid", payload.rid);
    requestHeaders.set("x-user-tier", payload.tier);
    requestHeaders.set("x-user-role", payload.role ?? "");
    requestHeaders.set("x-platform-mode", payload.mode);
  }

  return NextResponse.next({ request: { headers: requestHeaders } });
}

export const config = {
  matcher: [
    "/((?!_next/static|_next/image|favicon.ico).*)",
  ],
};
