import type { Metadata, Viewport } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import { Providers } from "./providers";

const inter = Inter({
  subsets: ["latin"],
  variable: "--font-inter",
  display: "swap",
});

export const metadata: Metadata = {
  metadataBase: new URL(
    process.env.NEXT_PUBLIC_APP_URL ?? "https://heavenlymatch.com"
  ),
  title: {
    default: "HeavenlyMatch — Trusted Halal Matrimony Platform",
    template: "%s | HeavenlyMatch",
  },
  description:
    "Find your perfect halal life partner. Bangladesh's most trusted matrimony platform for Muslims worldwide with strict privacy controls and guardian involvement.",
  keywords: [
    "bangladeshi matrimony",
    "muslim matrimony",
    "halal marriage",
    "nikkah platform",
    "islamic matrimony bangladesh",
    "NRB matrimony",
  ],
  openGraph: {
    type: "website",
    locale: "en_US",
    alternateLocale: "bn_BD",
    siteName: "HeavenlyMatch",
    images: [{ url: "/og-image.png", width: 1200, height: 630 }],
  },
  twitter: {
    card: "summary_large_image",
    site: "@heavenlymatch",
  },
  robots: {
    index: true,
    follow: true,
    googleBot: { index: true, follow: true },
  },
};

export const viewport: Viewport = {
  width: "device-width",
  initialScale: 1,
  themeColor: [
    { media: "(prefers-color-scheme: light)", color: "#1B4FD8" },
    { media: "(prefers-color-scheme: dark)", color: "#1e3a8a" },
  ],
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en" suppressHydrationWarning>
      <body className={`${inter.variable} font-sans antialiased bg-slate-50`}>
        <Providers>{children}</Providers>
      </body>
    </html>
  );
}
