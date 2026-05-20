import { z } from "zod";

export const searchFiltersSchema = z.object({
  gender: z.enum(["MALE", "FEMALE"]).optional(),
  ageMin: z.coerce.number().int().min(18).max(80).optional(),
  ageMax: z.coerce.number().int().min(18).max(80).optional(),
  religion: z.string().max(30).optional(),
  maritalStatus: z.string().max(30).optional(),
  residingCountryId: z.coerce.number().int().positive().optional(),
  divisionId: z.coerce.number().int().positive().optional(),
  // Gold+
  districtId: z.coerce.number().int().positive().optional(),
  sect: z.string().max(50).optional(),
  occupation: z.string().max(80).optional(),
  heightCmMin: z.coerce.number().int().min(100).max(250).optional(),
  heightCmMax: z.coerce.number().int().min(100).max(250).optional(),
  incomeMin: z.coerce.number().int().min(0).optional(),
  incomeMax: z.coerce.number().int().min(0).optional(),
  educationLevel: z.string().optional(),
  familyType: z.enum(["JOINT", "NUCLEAR", "FLEXIBLE"]).optional(),
  isPracticing: z.coerce.boolean().optional(),
  prayerHabits: z.string().optional(),
  isVerified: z.coerce.boolean().optional(),
  // Diamond+
  hasBeard: z.string().optional(),
  wearsHijab: z.string().optional(),
  // Pagination & sort
  sortBy: z
    .enum(["match_score", "newest", "last_active", "featured"])
    .default("match_score"),
  cursor: z.string().optional(),
  limit: z.coerce.number().int().min(1).max(30).default(12),
});

export type SearchFilters = z.infer<typeof searchFiltersSchema>;
