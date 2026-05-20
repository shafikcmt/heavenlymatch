"use client";

import { create } from "zustand";
import type { SearchFilters } from "@/lib/validations/search";

interface SearchState {
  filters: Partial<SearchFilters>;
  setFilter: <K extends keyof SearchFilters>(
    key: K,
    value: SearchFilters[K] | undefined
  ) => void;
  setFilters: (filters: Partial<SearchFilters>) => void;
  resetFilters: () => void;
  activeFilterCount: () => number;
}

const IGNORED_KEYS: Array<keyof SearchFilters> = ["cursor", "limit", "sortBy"];

export const useSearchStore = create<SearchState>((set, get) => ({
  filters: {},

  setFilter: (key, value) =>
    set((s) => ({ filters: { ...s.filters, [key]: value } })),

  setFilters: (filters) =>
    set((s) => ({ filters: { ...s.filters, ...filters } })),

  resetFilters: () => set({ filters: {} }),

  activeFilterCount: () => {
    const f = get().filters;
    return Object.entries(f).filter(
      ([k, v]) => v !== undefined && !IGNORED_KEYS.includes(k as keyof SearchFilters)
    ).length;
  },
}));
