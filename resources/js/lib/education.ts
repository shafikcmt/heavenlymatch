/**
 * Centralised education model — single source of truth for the biodata wizard,
 * profile display, and validation hints.
 *
 * The biodata `education_medium` column is the EDUCATION SYSTEM (general / qawmi /
 * alia / english_medium / vocational / other). Each system has its own ordered
 * ladder of levels (rank ascending). "Highest Qualification" and every "Education
 * Record" level must come from the SAME system, and a record may not rank ABOVE
 * the chosen highest qualification.
 *
 * Level VALUES are stable keys (e.g. `ssc`, `takmil`) used in storage; their
 * display text is resolved from lang files via `lvl_<value>` keys. Legacy/free-text
 * level strings stored before this model are tolerated everywhere (treated as
 * unknown → always "valid", shown verbatim).
 */

export type EduSystem =
  | 'general'
  | 'qawmi'
  | 'alia'
  | 'english_medium'
  | 'vocational'
  | 'other'

export interface EduOption {
  value: string
  labelKey: string
  rank: number
}

/** The 6 education systems, in display order. */
export const EDU_SYSTEMS: { value: EduSystem; labelKey: string }[] = [
  { value: 'general',        labelKey: 'edu_medium_general' },
  { value: 'qawmi',          labelKey: 'edu_medium_qawmi' },
  { value: 'alia',           labelKey: 'edu_medium_alia' },
  { value: 'english_medium', labelKey: 'edu_medium_english' },
  { value: 'vocational',     labelKey: 'edu_medium_vocational' },
  { value: 'other',          labelKey: 'edu_medium_other' },
]

/** Ordered level ladders per system (rank ascending; `other` rank 99 = top/free). */
const SYSTEM_LEVELS: Record<EduSystem, EduOption[]> = {
  general: [
    { value: 'below_class5', labelKey: 'lvl_below_class5', rank: 1 },
    { value: 'class5',       labelKey: 'lvl_class5',       rank: 2 },
    { value: 'class8',       labelKey: 'lvl_class8',       rank: 3 },
    { value: 'ssc',          labelKey: 'lvl_ssc',          rank: 4 },
    { value: 'hsc',          labelKey: 'lvl_hsc',          rank: 5 },
    { value: 'diploma',      labelKey: 'lvl_diploma',      rank: 6 },
    { value: 'bachelor',     labelKey: 'lvl_bachelor',     rank: 7 },
    { value: 'masters',      labelKey: 'lvl_masters',      rank: 8 },
    { value: 'phd',          labelKey: 'lvl_phd',          rank: 9 },
    { value: 'other',        labelKey: 'lvl_other',        rank: 99 },
  ],
  qawmi: [
    { value: 'hifz',         labelKey: 'lvl_hifz',         rank: 1 },
    { value: 'maktab',       labelKey: 'lvl_maktab',       rank: 2 },
    { value: 'mutawassitah', labelKey: 'lvl_mutawassitah', rank: 3 },
    { value: 'sanawiyah',    labelKey: 'lvl_sanawiyah',    rank: 4 },
    { value: 'fazilat',      labelKey: 'lvl_fazilat',      rank: 5 },
    { value: 'takmil',       labelKey: 'lvl_takmil',       rank: 6 },
    { value: 'ifta',         labelKey: 'lvl_ifta',         rank: 7 },
    { value: 'other',        labelKey: 'lvl_other',        rank: 99 },
  ],
  alia: [
    { value: 'ebtedayee',    labelKey: 'lvl_ebtedayee',    rank: 1 },
    { value: 'jdc',          labelKey: 'lvl_jdc',          rank: 2 },
    { value: 'dakhil',       labelKey: 'lvl_dakhil',       rank: 3 },
    { value: 'alim',         labelKey: 'lvl_alim',         rank: 4 },
    { value: 'fazil',        labelKey: 'lvl_fazil',        rank: 5 },
    { value: 'kamil',        labelKey: 'lvl_kamil',        rank: 6 },
    { value: 'other',        labelKey: 'lvl_other',        rank: 99 },
  ],
  english_medium: [
    { value: 'class5',       labelKey: 'lvl_class5',       rank: 2 },
    { value: 'class8',       labelKey: 'lvl_class8',       rank: 3 },
    { value: 'o_level',      labelKey: 'lvl_o_level',      rank: 4 },
    { value: 'a_level',      labelKey: 'lvl_a_level',      rank: 5 },
    { value: 'diploma',      labelKey: 'lvl_diploma',      rank: 6 },
    { value: 'bachelor',     labelKey: 'lvl_bachelor',     rank: 7 },
    { value: 'masters',      labelKey: 'lvl_masters',      rank: 8 },
    { value: 'phd',          labelKey: 'lvl_phd',          rank: 9 },
    { value: 'other',        labelKey: 'lvl_other',        rank: 99 },
  ],
  vocational: [
    { value: 'class8',       labelKey: 'lvl_class8',       rank: 3 },
    { value: 'ssc_voc',      labelKey: 'lvl_ssc_voc',      rank: 4 },
    { value: 'hsc_voc',      labelKey: 'lvl_hsc_voc',      rank: 5 },
    { value: 'diploma',      labelKey: 'lvl_diploma',      rank: 6 },
    { value: 'bachelor',     labelKey: 'lvl_bachelor',     rank: 7 },
    { value: 'other',        labelKey: 'lvl_other',        rank: 99 },
  ],
  // `other` system → no fixed ladder; details captured as free-text records.
  other: [],
}

export function isEduSystem(v: string | null | undefined): v is EduSystem {
  return !!v && Object.prototype.hasOwnProperty.call(SYSTEM_LEVELS, v)
}

/** All levels of a system (the Highest-Qualification option list). */
export function levelsForSystem(system: string | null | undefined): EduOption[] {
  return isEduSystem(system) ? SYSTEM_LEVELS[system] : []
}

/** Rank of a level within a system, or null if unknown (legacy/free text). */
export function rankOf(system: string | null | undefined, value: string): number | null {
  const found = levelsForSystem(system).find(l => l.value === value)
  return found ? found.rank : null
}

/** Is the chosen highest qualification a valid level of the system? */
export function isHighestValidForSystem(system: string | null | undefined, highest: string): boolean {
  if (!highest) return true
  if (system === 'other') return true
  return rankOf(system, highest) !== null
}

/**
 * Record-level options allowed for (system, highest): every system level whose
 * rank is ≤ the highest qualification's rank. With no/unknown highest, the full
 * ladder is offered.
 */
export function recordLevelsFor(system: string | null | undefined, highest: string): EduOption[] {
  const all = levelsForSystem(system)
  const cap = rankOf(system, highest)
  return cap == null ? all : all.filter(l => l.rank <= cap)
}

/**
 * Is a record's level valid given (system, highest)? Unknown/legacy/free-text
 * levels are tolerated (true). Known levels must not rank above the highest.
 */
export function isRecordLevelValid(system: string | null | undefined, highest: string, level: string): boolean {
  if (!level) return true
  const r = rankOf(system, level)
  if (r == null) return true // legacy / free text
  const cap = rankOf(system, highest)
  return cap == null ? true : r <= cap
}

/**
 * Sensible default level for a NEW record: the highest qualification itself if
 * still free, otherwise the top unused level at-or-below it. '' when nothing fits
 * (or for the free-text `other` system).
 */
export function nextDefaultLevel(
  system: string | null | undefined,
  highest: string,
  used: string[],
): string {
  // Without a highest qualification we cannot safely pick a default (would risk
  // defaulting to the top of the ladder, e.g. PhD) — leave it for the user.
  if (!highest || system === 'other') return ''
  const avail = recordLevelsFor(system, highest).filter(l => !used.includes(l.value))
  if (avail.length === 0) return ''
  // Prefer the highest-ranked available (closest to the highest qualification).
  return avail.reduce((top, l) => (l.rank > top.rank ? l : top), avail[0]!).value
}

/** lang key for a level value within a system (for localised display). */
export function levelLabelKey(system: string | null | undefined, value: string): string | null {
  return levelsForSystem(system).find(l => l.value === value)?.labelKey ?? null
}
