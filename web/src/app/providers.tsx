"use client";

import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { useState, useEffect, type ReactNode } from "react";
import { useAuthStore } from "@/stores/useAuthStore";

function AuthHydrator() {
  const setLoading = useAuthStore((s) => s.setLoading);
  const setToken = useAuthStore((s) => s.setToken);

  useEffect(() => {
    // Attempt silent token refresh on mount
    const refresh = async () => {
      try {
        const res = await fetch("/api/auth/refresh", { method: "POST" });
        if (res.ok) {
          const { accessToken } = await res.json();
          setToken(accessToken);
        }
      } catch {
        // Refresh failed — user is not logged in
      } finally {
        setLoading(false);
      }
    };
    void refresh();
  }, [setLoading, setToken]);

  return null;
}

export function Providers({ children }: { children: ReactNode }) {
  const [queryClient] = useState(
    () =>
      new QueryClient({
        defaultOptions: {
          queries: {
            staleTime: 5 * 60 * 1000, // 5 min
            gcTime: 10 * 60 * 1000,   // 10 min
            retry: (failureCount, error: unknown) => {
              // Don't retry on 4xx errors
              if (
                error instanceof Error &&
                "status" in error &&
                typeof (error as { status: number }).status === "number" &&
                (error as { status: number }).status < 500
              ) {
                return false;
              }
              return failureCount < 2;
            },
          },
        },
      })
  );

  return (
    <QueryClientProvider client={queryClient}>
      <AuthHydrator />
      {children}
      {process.env.NODE_ENV === "development" && (
        <ReactQueryDevtools initialIsOpen={false} />
      )}
    </QueryClientProvider>
  );
}
