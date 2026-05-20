"use client";

import { useState, Suspense } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import Link from "next/link";
import { Eye, EyeOff, User, Mail, Phone, Lock, ChevronRight, ChevronLeft } from "lucide-react";
import {
  registerStep1Schema,
  registerStep2Schema,
  registerStep3Schema,
  registerStep4Schema,
  type RegisterStep1,
  type RegisterStep2,
  type RegisterStep3,
  type RegisterStep4,
} from "@/lib/validations/auth";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useAuthStore } from "@/stores/useAuthStore";
import type { LoginResponse } from "@/types/api";

// ── Wizard state accumulated across steps ─────────────────────────────────────
interface WizardData {
  step1?: RegisterStep1;
  step2?: RegisterStep2;
  step3?: RegisterStep3;
}

// ── Step indicator ─────────────────────────────────────────────────────────────
function StepDots({ current, total }: { current: number; total: number }) {
  return (
    <div className="flex items-center justify-center gap-2 mb-8">
      {Array.from({ length: total }, (_, i) => (
        <div
          key={i}
          className={`rounded-full transition-all duration-300 ${
            i + 1 === current
              ? "w-8 h-2 bg-blue-600"
              : i + 1 < current
              ? "w-2 h-2 bg-blue-400"
              : "w-2 h-2 bg-slate-200"
          }`}
        />
      ))}
    </div>
  );
}

// ── Step 1: Identity ──────────────────────────────────────────────────────────
function Step1({
  defaultMode,
  onNext,
}: {
  defaultMode: "GENERAL" | "ISLAMIC";
  onNext: (data: RegisterStep1) => void;
}) {
  const {
    register,
    handleSubmit,
    watch,
    setValue,
    formState: { errors },
  } = useForm<RegisterStep1>({
    resolver: zodResolver(registerStep1Schema),
    defaultValues: { platformMode: defaultMode, preferredLanguage: "bn" },
  });

  const lookingFor = watch("lookingFor");
  const gender = watch("gender");
  const mode = watch("platformMode");

  return (
    <form onSubmit={handleSubmit(onNext)} className="space-y-5">
      <div>
        <p className="text-sm font-medium text-slate-700 mb-2">I am a</p>
        <div className="grid grid-cols-2 gap-3">
          {(["GROOM", "BRIDE"] as const).map((v) => (
            <button
              key={v}
              type="button"
              onClick={() => {
                setValue("gender", v === "GROOM" ? "MALE" : "FEMALE");
                setValue("lookingFor", v === "GROOM" ? "BRIDE" : "GROOM");
              }}
              className={`rounded-xl border-2 py-3 text-sm font-semibold transition-all ${
                gender === (v === "GROOM" ? "MALE" : "FEMALE")
                  ? "border-blue-600 bg-blue-50 text-blue-700"
                  : "border-slate-200 text-slate-600 hover:border-slate-300"
              }`}
            >
              {v === "GROOM" ? "👨 Groom" : "👩 Bride"}
            </button>
          ))}
        </div>
        {errors.gender && (
          <p className="mt-1 text-xs text-red-600">{errors.gender.message}</p>
        )}
      </div>

      <input type="hidden" {...register("gender")} />
      <input type="hidden" {...register("lookingFor")} />

      <div>
        <p className="text-sm font-medium text-slate-700 mb-2">Looking for a</p>
        <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
          {lookingFor ? (lookingFor === "BRIDE" ? "👩 Bride" : "👨 Groom") : "—"}
        </div>
      </div>

      <Input
        label="Your full name"
        type="text"
        autoComplete="name"
        placeholder="e.g. Fatima Rahman"
        leftIcon={<User size={16} />}
        error={errors.name?.message}
        {...register("name")}
      />

      <div>
        <p className="text-sm font-medium text-slate-700 mb-2">Platform mode</p>
        <div className="grid grid-cols-2 gap-3">
          {(["GENERAL", "ISLAMIC"] as const).map((v) => (
            <button
              key={v}
              type="button"
              onClick={() => setValue("platformMode", v)}
              className={`rounded-xl border-2 py-3 text-sm font-semibold transition-all ${
                mode === v
                  ? "border-blue-600 bg-blue-50 text-blue-700"
                  : "border-slate-200 text-slate-600 hover:border-slate-300"
              }`}
            >
              {v === "GENERAL" ? "🌐 General" : "☪️ Islamic"}
            </button>
          ))}
        </div>
      </div>

      <Button type="submit" className="w-full" size="lg">
        Continue <ChevronRight size={16} className="ml-1" />
      </Button>
    </form>
  );
}

// ── Step 2: Email ─────────────────────────────────────────────────────────────
function Step2({
  onNext,
  onBack,
}: {
  onNext: (data: RegisterStep2) => void;
  onBack: () => void;
}) {
  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<RegisterStep2>({ resolver: zodResolver(registerStep2Schema) });

  return (
    <form onSubmit={handleSubmit(onNext)} className="space-y-5">
      <Input
        label="Email address"
        type="email"
        autoComplete="email"
        placeholder="you@example.com"
        leftIcon={<Mail size={16} />}
        error={errors.email?.message}
        {...register("email")}
      />
      <p className="text-xs text-slate-500">
        We will send a 6-digit verification code to this address.
      </p>

      <div className="flex gap-3">
        <Button type="button" variant="outline" className="flex-1" onClick={onBack}>
          <ChevronLeft size={16} className="mr-1" /> Back
        </Button>
        <Button type="submit" className="flex-1" isLoading={isSubmitting}>
          Continue <ChevronRight size={16} className="ml-1" />
        </Button>
      </div>
    </form>
  );
}

// ── Step 3: Mobile ────────────────────────────────────────────────────────────
function Step3({
  onNext,
  onBack,
}: {
  onNext: (data: RegisterStep3) => void;
  onBack: () => void;
}) {
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<RegisterStep3>({
    resolver: zodResolver(registerStep3Schema),
    defaultValues: { countryCode: "+880" },
  });

  return (
    <form onSubmit={handleSubmit(onNext)} className="space-y-5">
      <div>
        <label className="mb-1 block text-sm font-medium text-slate-700">
          Mobile number
        </label>
        <div className="flex gap-2">
          <select
            {...register("countryCode")}
            className="w-28 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none"
          >
            <option value="+880">🇧🇩 +880</option>
            <option value="+44">🇬🇧 +44</option>
            <option value="+1">🇺🇸 +1</option>
            <option value="+971">🇦🇪 +971</option>
            <option value="+61">🇦🇺 +61</option>
          </select>
          <div className="flex-1">
            <Input
              type="tel"
              autoComplete="tel"
              placeholder="01XXXXXXXXX"
              leftIcon={<Phone size={16} />}
              error={errors.mobile?.message}
              {...register("mobile")}
            />
          </div>
        </div>
      </div>
      <p className="text-xs text-slate-500">
        We may send an OTP to verify your number. Standard rates may apply.
      </p>

      <div className="flex gap-3">
        <Button type="button" variant="outline" className="flex-1" onClick={onBack}>
          <ChevronLeft size={16} className="mr-1" /> Back
        </Button>
        <Button type="submit" className="flex-1">
          Continue <ChevronRight size={16} className="ml-1" />
        </Button>
      </div>
    </form>
  );
}

// ── Step 4: Password + Terms ──────────────────────────────────────────────────
function Step4({
  onSubmit,
  onBack,
  isLoading,
  error,
}: {
  onSubmit: (data: RegisterStep4) => void;
  onBack: () => void;
  isLoading: boolean;
  error: string | null;
}) {
  const [showPwd, setShowPwd] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<RegisterStep4>({ resolver: zodResolver(registerStep4Schema) });

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
      <Input
        label="Password"
        type={showPwd ? "text" : "password"}
        autoComplete="new-password"
        placeholder="Min 8 chars, 1 uppercase, 1 number"
        leftIcon={<Lock size={16} />}
        rightElement={
          <button
            type="button"
            onClick={() => setShowPwd((v) => !v)}
            className="text-slate-400 hover:text-slate-600"
            tabIndex={-1}
          >
            {showPwd ? <EyeOff size={16} /> : <Eye size={16} />}
          </button>
        }
        error={errors.password?.message}
        {...register("password")}
      />

      <Input
        label="Confirm password"
        type={showConfirm ? "text" : "password"}
        autoComplete="new-password"
        placeholder="Re-enter your password"
        leftIcon={<Lock size={16} />}
        rightElement={
          <button
            type="button"
            onClick={() => setShowConfirm((v) => !v)}
            className="text-slate-400 hover:text-slate-600"
            tabIndex={-1}
          >
            {showConfirm ? <EyeOff size={16} /> : <Eye size={16} />}
          </button>
        }
        error={errors.confirmPassword?.message}
        {...register("confirmPassword")}
      />

      <label className="flex items-start gap-2 cursor-pointer">
        <input
          type="checkbox"
          value="true"
          className="mt-0.5 rounded border-slate-300"
          {...register("termsAccepted")}
        />
        <span className="text-sm text-slate-600">
          I agree to the{" "}
          <Link href="/terms" className="text-blue-700 hover:underline" target="_blank">
            Terms of Service
          </Link>{" "}
          and{" "}
          <Link href="/privacy" className="text-blue-700 hover:underline" target="_blank">
            Privacy Policy
          </Link>
        </span>
      </label>
      {errors.termsAccepted && (
        <p className="text-xs text-red-600">{errors.termsAccepted.message}</p>
      )}

      {error && (
        <div className="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700" role="alert">
          {error}
        </div>
      )}

      <div className="flex gap-3">
        <Button type="button" variant="outline" className="flex-1" onClick={onBack}>
          <ChevronLeft size={16} className="mr-1" /> Back
        </Button>
        <Button type="submit" className="flex-1" isLoading={isLoading}>
          Create account
        </Button>
      </div>
    </form>
  );
}

// ── Main wizard page ──────────────────────────────────────────────────────────
const STEP_TITLES = [
  { title: "Create your profile", subtitle: "Tell us who you are" },
  { title: "Your email address", subtitle: "We'll verify it right away" },
  { title: "Your mobile number", subtitle: "For account security" },
  { title: "Set your password", subtitle: "Keep your account safe" },
];

function RegisterPageInner() {
  const router = useRouter();
  const params = useSearchParams();
  const defaultMode = (params.get("mode") as "ISLAMIC" | null) ?? "GENERAL";
  const setAuth = useAuthStore((s) => s.setAuth);

  const [step, setStep] = useState(1);
  const [wizard, setWizard] = useState<WizardData>({});
  const [submitError, setSubmitError] = useState<string | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const stepMeta = STEP_TITLES[step - 1]!;

  const handleStep1 = (data: RegisterStep1) => {
    setWizard((w) => ({ ...w, step1: data }));
    setStep(2);
  };
  const handleStep2 = (data: RegisterStep2) => {
    setWizard((w) => ({ ...w, step2: data }));
    setStep(3);
  };
  const handleStep3 = (data: RegisterStep3) => {
    setWizard((w) => ({ ...w, step3: data }));
    setStep(4);
  };

  const handleStep4 = async (data: RegisterStep4) => {
    if (!wizard.step1 || !wizard.step2 || !wizard.step3) return;
    setSubmitError(null);
    setIsSubmitting(true);

    try {
      const res = await fetch("/api/auth/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          ...wizard.step1,
          ...wizard.step2,
          ...wizard.step3,
          password: data.password,
          termsAccepted: data.termsAccepted,
        }),
      });

      const json = await res.json();

      if (!res.ok) {
        const code = (json as { error?: string }).error;
        setSubmitError(
          code === "EMAIL_EXISTS"
            ? "This email is already registered. Try logging in."
            : code === "MOBILE_EXISTS"
            ? "This mobile number is already registered."
            : "Registration failed. Please try again."
        );
        return;
      }

      // Auto-login so the verify-email page can use the access token
      const loginRes = await fetch("/api/auth/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: wizard.step2.email, password: data.password, rememberMe: false }),
      });
      if (loginRes.ok) {
        const loginJson = (await loginRes.json()) as LoginResponse;
        setAuth(loginJson.user, loginJson.accessToken);
      }
      router.push("/verify-email");
    } catch {
      setSubmitError("Network error. Please check your connection.");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="w-full max-w-md">
      <div className="rounded-2xl bg-white shadow-sm border border-slate-200 p-8">
        <StepDots current={step} total={4} />

        <div className="mb-6 text-center">
          <h1 className="text-2xl font-bold text-slate-900">{stepMeta.title}</h1>
          <p className="mt-1 text-sm text-slate-500">{stepMeta.subtitle}</p>
        </div>

        {step === 1 && (
          <Step1 defaultMode={defaultMode as "GENERAL" | "ISLAMIC"} onNext={handleStep1} />
        )}
        {step === 2 && (
          <Step2 onNext={handleStep2} onBack={() => setStep(1)} />
        )}
        {step === 3 && (
          <Step3 onNext={handleStep3} onBack={() => setStep(2)} />
        )}
        {step === 4 && (
          <Step4
            onSubmit={handleStep4}
            onBack={() => setStep(3)}
            isLoading={isSubmitting}
            error={submitError}
          />
        )}

        {step === 1 && (
          <p className="mt-6 text-center text-sm text-slate-500">
            Already have an account?{" "}
            <Link href="/login" className="font-semibold text-blue-700 hover:underline">
              Sign in
            </Link>
          </p>
        )}
      </div>
    </div>
  );
}

export default function RegisterPage() {
  return (
    <Suspense fallback={<div className="w-full max-w-md animate-pulse h-96 rounded-2xl bg-white/50" />}>
      <RegisterPageInner />
    </Suspense>
  );
}
