// =============================================================================
// HeavenlyMatch — TypeScript Matching Engine v2
// Uses weighted multi-factor scoring; ML-ready (swap scoreXxx for model calls)
// =============================================================================

import type { Profile, Preference, User, EducationLevel, Religion } from "@prisma/client";

// ─── Types ────────────────────────────────────────────────────────────────────

export type FullProfile = Profile & {
  user: User;
  preferences: Preference | null;
};

export interface ScoreBreakdown {
  age: number;
  location: number;
  religion: number;
  sect: number;
  education: number;
  occupation: number;
  height: number;
  lifestyle: number;
  family: number;
  activity: number;
}

export interface MatchResult {
  userId: string;
  registrationId: string;
  totalScore: number;          // 0-100
  scoreBreakdown: ScoreBreakdown;
  profile: FullProfile;
}

// ─── Weight Configuration (must sum to 100) ──────────────────────────────────
// Adjust here to A/B test different matching strategies.

export const MATCHING_WEIGHTS: Record<keyof ScoreBreakdown, number> = {
  religion:   22, // Most critical for Muslim matrimony
  age:        16,
  location:   14,
  education:  11,
  sect:       10,
  occupation:  9,
  height:      6,
  lifestyle:   6,
  family:      4,
  activity:    2,
} as const;

// Validate weights sum to 100 at import time
const totalWeight = Object.values(MATCHING_WEIGHTS).reduce((a, b) => a + b, 0);
if (totalWeight !== 100) {
  throw new Error(`MATCHING_WEIGHTS must sum to 100, got ${totalWeight}`);
}

// ─── Education Hierarchy ──────────────────────────────────────────────────────

const EDUCATION_RANK: Record<EducationLevel, number> = {
  PRIMARY:          1,
  JSC:              2,
  SSC:              3,
  HSC:              4,
  DIPLOMA:          4,
  GRADUATION:       5,
  POST_GRADUATION:  6,
  PHD:              7,
  HAFEZ:            5,
  ALIM:             5,
  FAZIL:            6,
  KAMIL:            7,
};

// Sunni madhhabs that are broadly compatible
const SUNNI_COMPATIBLE = new Set([
  "Hanafi", "Shafi", "Maliki", "Hanbali", "Ahle Sunnah",
  "Ahle Hadith", "Salafi", "Deobandi", "Barelvi",
]);

// Occupation category groupings for partial matches
const OCCUPATION_GROUPS: Record<string, string[]> = {
  Business:    ["Business", "Entrepreneur", "Self Employed", "Import Export"],
  Government:  ["Government Job", "Civil Service", "Military", "Police", "BCS"],
  Finance:     ["Bank Job", "Finance", "Accounting", "CA"],
  Technology:  ["IT Professional", "Software Engineer", "Web Developer", "Data Scientist"],
  Education:   ["Teacher", "Professor", "Lecturer", "Researcher", "Tutor"],
  Medical:     ["Doctor", "Nurse", "Pharmacist", "Dentist", "Medical Officer"],
  Engineering: ["Engineer", "Architect", "Contractor"],
  Religious:   ["Imam", "Islamic Scholar", "Madrasa Teacher", "Mufti"],
};

// ─── Core Engine ─────────────────────────────────────────────────────────────

export class MatchingEngine {
  /**
   * Score compatibility between a seeker and a candidate.
   * Each sub-score is 0-100; weighted sum gives totalScore.
   */
  score(seeker: FullProfile, candidate: FullProfile): MatchResult {
    const prefs = seeker.preferences;
    const now = new Date();

    const breakdown: ScoreBreakdown = {
      religion:   this.scoreReligion(seeker, candidate, prefs),
      age:        this.scoreAge(seeker, candidate, prefs, now),
      location:   this.scoreLocation(seeker, candidate, prefs),
      education:  this.scoreEducation(candidate, prefs),
      sect:       this.scoreSect(seeker, candidate, prefs),
      occupation: this.scoreOccupation(candidate, prefs),
      height:     this.scoreHeight(candidate, prefs),
      lifestyle:  this.scoreLifestyle(seeker, candidate),
      family:     this.scoreFamily(seeker, candidate, prefs),
      activity:   this.scoreActivity(candidate.user, now),
    };

    const totalScore = Math.round(
      Object.entries(breakdown).reduce(
        (sum, [key, raw]) =>
          sum + (raw / 100) * MATCHING_WEIGHTS[key as keyof ScoreBreakdown],
        0
      )
    );

    return {
      userId:         candidate.userId,
      registrationId: candidate.user.registrationId,
      totalScore:     Math.min(100, Math.max(0, totalScore)),
      scoreBreakdown: breakdown,
      profile:        candidate,
    };
  }

  // ─── Sub-Scorers (each returns 0-100) ──────────────────────────────────────

  private scoreReligion(
    seeker: FullProfile,
    candidate: FullProfile,
    prefs: Preference | null
  ): number {
    const required = prefs?.partnerReligion;

    // Hard exclusion: seeker specified a religion and candidate doesn't match
    if (required && required !== candidate.religion) return 0;

    // Same religion = base 70; different = 20 (if no hard requirement)
    return candidate.religion === seeker.religion ? 100 : 20;
  }

  private scoreSect(
    seeker: FullProfile,
    candidate: FullProfile,
    prefs: Preference | null
  ): number {
    // Only meaningful within Islam
    if (seeker.religion !== "ISLAM" || candidate.religion !== "ISLAM") return 80;

    const preferred = prefs?.partnerSect ?? seeker.sect;
    if (!preferred) return 75; // No preference = neutral-positive

    const cSect = candidate.sect ?? "";
    if (cSect === preferred) return 100;

    // Both are within the Sunni family = partial compatibility
    if (SUNNI_COMPATIBLE.has(preferred) && SUNNI_COMPATIBLE.has(cSect)) return 65;

    return 10;
  }

  private scoreAge(
    seeker: FullProfile,
    candidate: FullProfile,
    prefs: Preference | null,
    now: Date
  ): number {
    if (!candidate.dateOfBirth) return 50;

    const candidateAge = this.ageInYears(candidate.dateOfBirth, now);
    const minAge = prefs?.partnerAgeMin ?? (seeker.gender === "MALE" ? 18 : 22);
    const maxAge = prefs?.partnerAgeMax ?? (seeker.gender === "MALE" ? 40 : 50);

    if (candidateAge >= minAge && candidateAge <= maxAge) {
      // Bonus for center of preference range (Gaussian-like curve)
      const mid = (minAge + maxAge) / 2;
      const halfRange = Math.max((maxAge - minAge) / 2, 1);
      const deviation = Math.abs(candidateAge - mid) / halfRange;
      return Math.round(100 - deviation * 20); // at most -20 for edge of range
    }

    // Outside range: partial credit within 3 years buffer
    const outside = Math.max(0, minAge - candidateAge, candidateAge - maxAge);
    if (outside <= 3) return Math.round(55 - outside * 12);
    return 0;
  }

  private scoreLocation(
    seeker: FullProfile,
    candidate: FullProfile,
    prefs: Preference | null
  ): number {
    let score = 0;

    // Country match (40 pts)
    const preferredCountry = prefs?.partnerResidingCountryId;
    if (preferredCountry) {
      score += candidate.residingCountryId === preferredCountry ? 40 : 0;
    } else {
      score += 30; // No country preference = partial credit
    }

    // Division match (35 pts) — only relevant if both are BD-based
    const preferredDiv = prefs?.preferredDivisionId;
    if (preferredDiv) {
      score += candidate.homeDivisionId === preferredDiv ? 35 : 0;
    } else if (
      candidate.residingCountryId === seeker.residingCountryId &&
      candidate.homeDivisionId === seeker.homeDivisionId
    ) {
      score += 25; // Same division bonus even without explicit preference
    } else {
      score += 10;
    }

    // District match (25 pts)
    const preferredDist = prefs?.preferredDistrictId;
    if (preferredDist) {
      score += candidate.homeDistrictId === preferredDist ? 25 : 0;
    } else if (candidate.homeDistrictId === seeker.homeDistrictId) {
      score += 15;
    } else {
      score += 5;
    }

    return Math.min(score, 100);
  }

  private scoreEducation(candidate: FullProfile, prefs: Preference | null): number {
    if (!prefs?.partnerEducation) return 70; // No preference

    const required = EDUCATION_RANK[prefs.partnerEducation] ?? 0;
    const actual   = candidate.highestQualification
      ? (EDUCATION_RANK[candidate.highestQualification] ?? 0)
      : 0;

    if (actual >= required) return 100;

    const deficit = required - actual;
    return Math.max(0, 100 - deficit * 20);
  }

  private scoreOccupation(candidate: FullProfile, prefs: Preference | null): number {
    if (!prefs?.partnerOccupation) return 70;

    const preferred  = prefs.partnerOccupation;
    const actual     = candidate.occupation ?? "";

    if (actual === preferred) return 100;

    // Category-level match
    for (const [, members] of Object.entries(OCCUPATION_GROUPS)) {
      if (members.includes(preferred) && members.includes(actual)) return 75;
    }

    // Income-range partial credit (candidate may differ in title but matches income)
    const incomeMatch = this.scoreIncomeRange(candidate, prefs);
    return Math.round(incomeMatch * 0.4); // 40% of income score as proxy
  }

  private scoreIncomeRange(candidate: FullProfile, prefs: Preference | null): number {
    const min = prefs?.partnerIncomeMin;
    const max = prefs?.partnerIncomeMax;
    const income = candidate.monthlyIncome;

    if (!income) return 50;
    if (!min && !max) return 70;

    const meetsMin = !min || income >= min;
    const meetsMax = !max || income <= max;

    if (meetsMin && meetsMax) return 100;
    if (meetsMin && !meetsMax) return 60; // Earns more than expected — usually acceptable
    return 20;
  }

  private scoreHeight(candidate: FullProfile, prefs: Preference | null): number {
    const min = prefs?.partnerHeightCmMin;
    const max = prefs?.partnerHeightCmMax;
    const h   = candidate.heightCm;

    if (!min && !max) return 80; // No preference
    if (!h) return 50;           // No data on candidate

    const meetsMin = !min || h >= min;
    const meetsMax = !max || h <= max;
    if (meetsMin && meetsMax) return 100;

    const outside = Math.max(0, (min ?? 0) - h, h - (max ?? Infinity));
    return outside <= 5 ? 60 : 20; // 5cm tolerance
  }

  private scoreLifestyle(seeker: FullProfile, candidate: FullProfile): number {
    let score = 0;
    let checked = 0;

    // Prayer habits (strong signal for Islamic matrimony)
    if (seeker.prayerHabits && candidate.prayerHabits) {
      const PRAYER_RANK: Record<string, number> = {
        "5_times": 3, regularly: 2, sometimes: 1, rarely: 0,
      };
      const seekerRank    = PRAYER_RANK[seeker.prayerHabits]    ?? 1;
      const candidateRank = PRAYER_RANK[candidate.prayerHabits] ?? 1;
      const diff = Math.abs(seekerRank - candidateRank);
      score += diff === 0 ? 35 : diff === 1 ? 20 : 5;
      checked++;
    }

    // Diet compatibility
    if (seeker.diet && candidate.diet) {
      score += seeker.diet === candidate.diet ? 30 : 10;
      checked++;
    }

    // Smoking (strong penalty)
    if (candidate.smoking === "yes") score -= 25;

    // Family type preference
    if (seeker.familyType && candidate.familyType) {
      score += seeker.familyType === candidate.familyType ? 35 : 15;
      checked++;
    }

    if (checked === 0) return 60;
    return Math.min(100, Math.max(0, Math.round(score)));
  }

  private scoreFamily(
    seeker: FullProfile,
    candidate: FullProfile,
    prefs: Preference | null
  ): number {
    let score = 0;

    const STATUS_RANK: Record<string, number> = {
      lower: 1, lower_middle: 2, middle: 3, upper_middle: 4, upper: 5,
    };

    const seekerStatus    = STATUS_RANK[seeker.familyFinancialStatus ?? ""] ?? 0;
    const candidateStatus = STATUS_RANK[candidate.familyFinancialStatus ?? ""] ?? 0;

    if (seekerStatus && candidateStatus) {
      const diff = Math.abs(seekerStatus - candidateStatus);
      score += Math.max(0, 50 - diff * 12);
    } else {
      score += 25;
    }

    // Family type match
    const preferredFamilyType = prefs?.partnerFamilyType;
    if (preferredFamilyType && candidate.familyType === preferredFamilyType) {
      score += 30;
    } else if (!preferredFamilyType) {
      score += 20;
    }

    // Guardian agrees (Islamic mode positive signal)
    if (candidate.guardianAgrees === true) score += 20;

    return Math.min(score, 100);
  }

  private scoreActivity(user: User, now: Date): number {
    if (!user.lastActiveAt) return 30;

    const days = this.daysDiff(user.lastActiveAt, now);
    if (days <= 1)   return 100;
    if (days <= 7)   return 85;
    if (days <= 30)  return 60;
    if (days <= 90)  return 35;
    return 10;
  }

  // ─── Utilities ─────────────────────────────────────────────────────────────

  private ageInYears(dob: Date, now: Date): number {
    const diff = now.getTime() - dob.getTime();
    return Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
  }

  private daysDiff(past: Date, now: Date): number {
    return Math.floor((now.getTime() - past.getTime()) / (1000 * 60 * 60 * 24));
  }
}

// ─── Cold-Start Logic ─────────────────────────────────────────────────────────
/**
 * For users who just registered (no preferences filled yet):
 * Use gender-age defaults and their home district as the only signals.
 * This ensures they see relevant profiles immediately.
 */
export function buildColdStartPreferences(profile: Profile): Partial<Preference> {
  if (!profile.dateOfBirth) return {};

  const age = Math.floor(
    (Date.now() - new Date(profile.dateOfBirth).getTime()) / (365.25 * 24 * 3600 * 1000)
  );

  if (profile.gender === "MALE") {
    return {
      partnerAgeMin: Math.max(18, age - 8),
      partnerAgeMax: age + 2,
      partnerReligion: profile.religion as Religion,
      preferredDivisionId: profile.homeDivisionId ?? undefined,
    };
  } else {
    return {
      partnerAgeMin: age,
      partnerAgeMax: age + 10,
      partnerReligion: profile.religion as Religion,
      preferredDivisionId: profile.homeDivisionId ?? undefined,
    };
  }
}

// ─── Batch Compute (used by nightly BullMQ job) ──────────────────────────────
export interface BatchScoreInput {
  seekerId: string;
  seekerProfile: FullProfile;
  candidates: FullProfile[];
  topN?: number;
}

export function computeBatchScores({
  seekerProfile,
  candidates,
  topN = 30,
}: BatchScoreInput): MatchResult[] {
  const engine = new MatchingEngine();

  return candidates
    .map((c) => engine.score(seekerProfile, c))
    .sort((a, b) => b.totalScore - a.totalScore)
    .slice(0, topN);
}
