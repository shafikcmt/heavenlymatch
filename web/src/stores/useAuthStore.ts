"use client";

import { create } from "zustand";
import { persist, createJSONStorage } from "zustand/middleware";
import type { AuthUser } from "@/types/api";

interface AuthState {
  user: AuthUser | null;
  accessToken: string | null;
  isLoading: boolean;

  // Actions
  setAuth: (user: AuthUser, token: string) => void;
  setToken: (token: string) => void;
  clearAuth: () => void;
  setLoading: (v: boolean) => void;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      user: null,
      accessToken: null,
      isLoading: true,

      setAuth: (user, accessToken) => set({ user, accessToken, isLoading: false }),
      setToken: (accessToken) => set({ accessToken }),
      clearAuth: () => set({ user: null, accessToken: null, isLoading: false }),
      setLoading: (isLoading) => set({ isLoading }),
    }),
    {
      name: "hm_auth",
      storage: createJSONStorage(() => sessionStorage),
      // Don't persist accessToken in storage — it's short-lived
      partialize: (state) => ({ user: state.user }),
    }
  )
);

// Selector hooks
export const useUser = () => useAuthStore((s) => s.user);
export const useIsAuthenticated = () => useAuthStore((s) => !!s.user);
export const useSubscriptionTier = () =>
  useAuthStore((s) => s.user?.subscriptionTier ?? "FREE");
export const useIsPremium = () =>
  useAuthStore((s) =>
    ["SILVER", "GOLD", "DIAMOND"].includes(s.user?.subscriptionTier ?? "FREE")
  );
export const useIsGoldPlus = () =>
  useAuthStore((s) =>
    ["GOLD", "DIAMOND"].includes(s.user?.subscriptionTier ?? "FREE")
  );
