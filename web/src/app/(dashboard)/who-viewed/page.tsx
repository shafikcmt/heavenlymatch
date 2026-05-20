import Link from "next/link";
import { Eye, Lock } from "lucide-react";
import { Button } from "@/components/ui/button";

const MOCK = [
  { registrationId: "HM000401", name: "Anonymous User", age: null, district: null, viewedAt: new Date(Date.now() - 1800000).toISOString(), isBlurred: true },
  { registrationId: "HM000402", name: "Tarek Hossain", age: 29, district: "Dhaka", viewedAt: new Date(Date.now() - 3600000 * 3).toISOString(), isBlurred: false },
  { registrationId: "HM000403", name: "Anonymous User", age: null, district: null, viewedAt: new Date(Date.now() - 86400000).toISOString(), isBlurred: true },
  { registrationId: "HM000404", name: "Abdullah Mamun", age: 31, district: "Sylhet", viewedAt: new Date(Date.now() - 86400000 * 2).toISOString(), isBlurred: false },
];

function timeLabel(iso: string) {
  const diff = (Date.now() - new Date(iso).getTime()) / 1000;
  if (diff < 3600)  return `${Math.floor(diff / 60)}m ago`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
  return `${Math.floor(diff / 86400)}d ago`;
}

export default function WhoViewedPage() {
  const blurredCount = MOCK.filter((v) => v.isBlurred).length;

  return (
    <div className="max-w-xl mx-auto">
      <div className="mb-6">
        <h1 className="text-xl font-bold text-slate-900 flex items-center gap-2">
          <Eye size={20} className="text-blue-600" />
          Who Viewed My Profile
        </h1>
        <p className="text-sm text-slate-500 mt-0.5">{MOCK.length} profile visits</p>
      </div>

      {/* Upgrade nudge */}
      {blurredCount > 0 && (
        <div className="mb-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 flex items-center gap-4">
          <Lock size={24} className="text-amber-500 shrink-0" />
          <div className="flex-1">
            <p className="text-sm font-semibold text-amber-900">
              {blurredCount} visitor{blurredCount > 1 ? "s" : ""} hidden
            </p>
            <p className="text-xs text-amber-700 mt-0.5">
              Upgrade to Gold to see every visitor who viewed your profile.
            </p>
          </div>
          <Link href="/upgrade">
            <Button size="sm" variant="premium">Upgrade</Button>
          </Link>
        </div>
      )}

      <div className="space-y-3">
        {MOCK.map((v, i) => (
          <div
            key={i}
            className="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
          >
            <div className={`h-12 w-12 rounded-full flex items-center justify-center font-bold text-lg shrink-0 ${
              v.isBlurred ? "bg-slate-200 text-slate-400 blur-sm" : "bg-blue-100 text-blue-700"
            }`}>
              {v.isBlurred ? "?" : v.name.charAt(0)}
            </div>

            <div className="flex-1 min-w-0">
              {v.isBlurred ? (
                <>
                  <div className="h-4 w-32 rounded bg-slate-200 blur-sm" />
                  <div className="mt-1 h-3 w-20 rounded bg-slate-100 blur-sm" />
                </>
              ) : (
                <>
                  <Link
                    href={`/profile/${v.registrationId}`}
                    className="font-semibold text-slate-900 hover:text-blue-700"
                  >
                    {v.name}
                  </Link>
                  <p className="text-xs text-slate-500 mt-0.5">
                    {v.age ? `${v.age} yrs` : ""}
                    {v.district ? ` · ${v.district}` : ""}
                  </p>
                </>
              )}
            </div>

            <span className="text-xs text-slate-400 shrink-0">{timeLabel(v.viewedAt)}</span>
          </div>
        ))}
      </div>
    </div>
  );
}
