"use client";

import { useState, useRef, useEffect } from "react";
import { useRouter } from "next/navigation";
import { Mail } from "lucide-react";
import { Button } from "@/components/ui/button";
import { useAuthStore } from "@/stores/useAuthStore";

export default function VerifyEmailPage() {
  const router = useRouter();
  const accessToken = useAuthStore((s) => s.accessToken);
  const user = useAuthStore((s) => s.user);

  const [code, setCode] = useState(["", "", "", "", "", ""]);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [resendCountdown, setResendCountdown] = useState(0);
  const inputRefs = useRef<(HTMLInputElement | null)[]>([]);

  useEffect(() => {
    inputRefs.current[0]?.focus();
  }, []);

  useEffect(() => {
    if (resendCountdown <= 0) return;
    const t = setTimeout(() => setResendCountdown((c) => c - 1), 1000);
    return () => clearTimeout(t);
  }, [resendCountdown]);

  const handleInput = (i: number, value: string) => {
    const digit = value.replace(/\D/g, "").slice(-1);
    const next = [...code];
    next[i] = digit;
    setCode(next);
    if (digit && i < 5) {
      inputRefs.current[i + 1]?.focus();
    }
  };

  const handleKeyDown = (i: number, e: React.KeyboardEvent) => {
    if (e.key === "Backspace" && !code[i] && i > 0) {
      inputRefs.current[i - 1]?.focus();
    }
  };

  const handlePaste = (e: React.ClipboardEvent) => {
    const pasted = e.clipboardData.getData("text").replace(/\D/g, "").slice(0, 6);
    if (pasted.length === 6) {
      setCode(pasted.split(""));
      inputRefs.current[5]?.focus();
    }
  };

  const submitCode = async () => {
    const fullCode = code.join("");
    if (fullCode.length !== 6) {
      setError("Please enter all 6 digits.");
      return;
    }
    if (!accessToken) {
      router.push("/login?next=/verify-email");
      return;
    }

    setError(null);
    setIsLoading(true);
    try {
      const res = await fetch("/api/auth/verify-email", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${accessToken}`,
        },
        body: JSON.stringify({ code: fullCode }),
      });
      const json = await res.json();
      if (!res.ok) {
        const errCode = (json as { error?: string }).error;
        setError(
          errCode === "INVALID_OR_EXPIRED_CODE"
            ? "Incorrect or expired code. Please try again or resend."
            : "Verification failed. Please try again."
        );
        return;
      }
      setSuccess(true);
      setTimeout(() => router.push("/home"), 1500);
    } catch {
      setError("Network error. Please check your connection.");
    } finally {
      setIsLoading(false);
    }
  };

  const resendCode = async () => {
    if (!accessToken) return;
    setError(null);
    try {
      const res = await fetch("/api/auth/verify-email", {
        method: "PUT",
        headers: { Authorization: `Bearer ${accessToken}` },
      });
      if (res.ok) {
        setResendCountdown(60);
      } else {
        const json = await res.json() as { error?: string; retryAfter?: number };
        if (json.error === "RESEND_TOO_SOON") {
          setResendCountdown(json.retryAfter ?? 60);
        }
      }
    } catch {
      setError("Could not resend. Please try again.");
    }
  };

  if (success) {
    return (
      <div className="w-full max-w-md text-center">
        <div className="rounded-2xl bg-white shadow-sm border border-slate-200 p-10">
          <div className="text-5xl mb-4">✅</div>
          <h1 className="text-2xl font-bold text-slate-900">Email verified!</h1>
          <p className="mt-2 text-slate-500">Redirecting you to your dashboard…</p>
        </div>
      </div>
    );
  }

  const maskedEmail = user?.email
    ? user.email.replace(/(.{2})(.*)(?=@)/, (_, a, b) => a + "*".repeat(b.length))
    : "your email";

  return (
    <div className="w-full max-w-md">
      <div className="rounded-2xl bg-white shadow-sm border border-slate-200 p-8">
        <div className="mb-8 text-center">
          <div className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-50">
            <Mail className="h-7 w-7 text-blue-600" />
          </div>
          <h1 className="text-2xl font-bold text-slate-900">Verify your email</h1>
          <p className="mt-2 text-sm text-slate-500">
            We sent a 6-digit code to{" "}
            <span className="font-semibold text-slate-700">{maskedEmail}</span>
          </p>
        </div>

        <div className="mb-6 flex justify-center gap-2" onPaste={handlePaste}>
          {code.map((digit, i) => (
            <input
              key={i}
              ref={(el) => { inputRefs.current[i] = el; }}
              type="text"
              inputMode="numeric"
              maxLength={1}
              value={digit}
              onChange={(e) => handleInput(i, e.target.value)}
              onKeyDown={(e) => handleKeyDown(i, e)}
              className={`h-12 w-10 rounded-xl border-2 text-center text-lg font-bold transition-all focus:outline-none ${
                digit
                  ? "border-blue-500 bg-blue-50 text-blue-700"
                  : "border-slate-200 text-slate-900 focus:border-blue-400"
              }`}
            />
          ))}
        </div>

        {error && (
          <div className="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700" role="alert">
            {error}
          </div>
        )}

        <Button
          className="w-full"
          size="lg"
          onClick={submitCode}
          isLoading={isLoading}
          disabled={code.join("").length < 6}
        >
          Verify email
        </Button>

        <div className="mt-4 text-center text-sm text-slate-500">
          Didn&apos;t receive the code?{" "}
          {resendCountdown > 0 ? (
            <span className="text-slate-400">Resend in {resendCountdown}s</span>
          ) : (
            <button
              onClick={resendCode}
              className="font-semibold text-blue-700 hover:underline"
            >
              Resend
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
