/**
 * Edge-compatible JWT verification using Web Crypto API.
 * No Node.js or jose dependencies — safe to import in Next.js middleware.
 */

export interface TokenPayload {
  sub: string;
  rid: string;
  role: string | null;
  mode: string;
  tier: string;
  ver: boolean;
  iat?: number;
  exp?: number;
}

function b64urlDecode(input: string): Uint8Array<ArrayBuffer> {
  const padded = input.replace(/-/g, "+").replace(/_/g, "/");
  const binary = atob(padded);
  const bytes = new Uint8Array(binary.length);
  for (let i = 0; i < binary.length; i++) {
    bytes[i] = binary.charCodeAt(i);
  }
  return bytes;
}

async function getHmacKey(secret: string): Promise<CryptoKey> {
  const keyData = new TextEncoder().encode(secret);
  return crypto.subtle.importKey("raw", keyData, { name: "HMAC", hash: "SHA-256" }, false, [
    "verify",
  ]);
}

export async function verifyAccessTokenEdge(token: string): Promise<TokenPayload | null> {
  try {
    const parts = token.split(".");
    if (parts.length !== 3) return null;
    const [headerB64, payloadB64, sigB64] = parts as [string, string, string];

    const secret =
      process.env.JWT_ACCESS_SECRET ?? "fallback_dev_only_access_secret_32chars";
    const key = await getHmacKey(secret);

    const message = new TextEncoder().encode(`${headerB64}.${payloadB64}`);
    const sig = b64urlDecode(sigB64);
    const valid = await crypto.subtle.verify("HMAC", key, sig, message);
    if (!valid) return null;

    const payload = JSON.parse(
      new TextDecoder().decode(b64urlDecode(payloadB64))
    ) as TokenPayload & { exp?: number };

    if (payload.exp && Date.now() / 1000 > payload.exp) return null;

    return payload;
  } catch {
    return null;
  }
}
