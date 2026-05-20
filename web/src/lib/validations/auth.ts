import { z } from "zod";

export const registerStep1Schema = z.object({
  lookingFor: z.enum(["BRIDE", "GROOM"], "Please select who you are looking for"),
  name: z
    .string()
    .min(2, "Name must be at least 2 characters")
    .max(100, "Name must be under 100 characters")
    .regex(/^[\p{L}\s'-]+$/u, "Name can only contain letters"),
  gender: z.enum(["MALE", "FEMALE"], "Please select your gender"),
  platformMode: z.enum(["GENERAL", "ISLAMIC"]),
  preferredLanguage: z.enum(["bn", "en"]),
});

export const registerStep2Schema = z.object({
  email: z
    .string()
    .email("Please enter a valid email address")
    .max(255)
    .transform((v) => v.toLowerCase().trim()),
});

export const registerStep3Schema = z.object({
  countryCode: z.string(),
  mobile: z
    .string()
    .regex(/^[0-9]{10,11}$/, "Enter a valid 10-11 digit mobile number"),
});

export const registerStep4Schema = z
  .object({
    password: z
      .string()
      .min(8, "Password must be at least 8 characters")
      .max(72)
      .regex(/[A-Z]/, "Must contain at least one uppercase letter")
      .regex(/[0-9]/, "Must contain at least one number"),
    confirmPassword: z.string(),
    termsAccepted: z.literal(true, "You must accept the terms"),
  })
  .refine((d) => d.password === d.confirmPassword, {
    message: "Passwords do not match",
    path: ["confirmPassword"],
  });

export const loginSchema = z.object({
  email: z.string().email().transform((v) => v.toLowerCase().trim()),
  password: z.string().min(1, "Password is required"),
  rememberMe: z.boolean(),
});

export const verifyOtpSchema = z.object({
  code: z
    .string()
    .length(6, "Verification code must be 6 digits")
    .regex(/^[0-9]+$/, "Code must be numeric"),
});

export const forgotPasswordSchema = z.object({
  email: z.string().email().transform((v) => v.toLowerCase().trim()),
});

export const resetPasswordSchema = z
  .object({
    token: z.string().min(1),
    password: z
      .string()
      .min(8)
      .regex(/[A-Z]/, "Must contain at least one uppercase letter")
      .regex(/[0-9]/, "Must contain at least one number"),
    confirmPassword: z.string(),
  })
  .refine((d) => d.password === d.confirmPassword, {
    message: "Passwords do not match",
    path: ["confirmPassword"],
  });

export type RegisterStep1 = z.infer<typeof registerStep1Schema>;
export type RegisterStep2 = z.infer<typeof registerStep2Schema>;
export type RegisterStep3 = z.infer<typeof registerStep3Schema>;
export type RegisterStep4 = z.infer<typeof registerStep4Schema>;
export type LoginInput = z.infer<typeof loginSchema>;
export type ForgotPasswordInput = z.infer<typeof forgotPasswordSchema>;
export type ResetPasswordInput = z.infer<typeof resetPasswordSchema>;
