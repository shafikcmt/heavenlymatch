// Types mirroring the public/json/*.json shape
export interface Division {
  id: string
  name: string
  bn_name: string
}

export interface District {
  id: string
  division_id: string
  name: string
  bn_name: string
}

export interface Upazila {
  id: string
  district_id: string
  name: string
  bn_name: string
}

export interface Union {
  id: string
  upazilla_id: string  // JSON uses "upazilla_id" (double-l)
  name: string
  bn_name: string
}

// Module-level cache — loaded once per page lifecycle, shared across all instances
let _divisions: Division[] | null = null
let _districts: District[] | null = null
let _upazilas: Upazila[] | null = null
let _unions: Union[] | null = null

async function fetchJSON<T>(url: string): Promise<T> {
  const res = await fetch(url)
  if (!res.ok) throw new Error(`[address-data] ${url} → ${res.status}`)
  return res.json() as Promise<T>
}

// ── Loaders ───────────────────────────────────────────────────────────────────

export async function loadDivisions(): Promise<Division[]> {
  if (!_divisions) _divisions = await fetchJSON<Division[]>('/json/divisions.json')
  return _divisions
}

export async function loadDistricts(): Promise<District[]> {
  if (!_districts) _districts = await fetchJSON<District[]>('/json/districts.json')
  return _districts
}

export async function loadUpazilas(): Promise<Upazila[]> {
  if (!_upazilas) _upazilas = await fetchJSON<Upazila[]>('/json/upazilas.json')
  return _upazilas
}

export async function loadUnions(): Promise<Union[]> {
  if (!_unions) _unions = await fetchJSON<Union[]>('/json/unions.json')
  return _unions
}

// ── Filter helpers ────────────────────────────────────────────────────────────

export async function getDistrictsByDivision(divisionId: string): Promise<District[]> {
  const all = await loadDistricts()
  return all.filter(d => d.division_id === divisionId)
}

export async function getUpazilasByDistrict(districtId: string): Promise<Upazila[]> {
  const all = await loadUpazilas()
  return all.filter(u => u.district_id === districtId)
}

export async function getUnionsByUpazila(upazilaId: string): Promise<Union[]> {
  const all = await loadUnions()
  // Normalize both "upazilla_id" (JSON key) and "upazila_id" defensively
  return all.filter(u => u.upazilla_id === upazilaId)
}
