import Link from "next/link";
import { Send, CheckCircle, XCircle, Clock } from "lucide-react";

type Status = "pending" | "accepted" | "declined";

const MOCK = [
  {
    id: "1",
    to: { registrationId: "HM000201", name: "Nusrat Jahan", age: 25, occupation: "Doctor", district: "Dhaka" },
    status: "pending" as Status,
    sentAt: new Date(Date.now() - 3600000 * 5).toISOString(),
  },
  {
    id: "2",
    to: { registrationId: "HM000202", name: "Ayesha Siddiqua", age: 23, occupation: "Teacher", district: "Sylhet" },
    status: "accepted" as Status,
    sentAt: new Date(Date.now() - 86400000 * 2).toISOString(),
  },
  {
    id: "3",
    to: { registrationId: "HM000203", name: "Mariam Akter", age: 26, occupation: "Engineer", district: "Chittagong" },
    status: "declined" as Status,
    sentAt: new Date(Date.now() - 86400000 * 5).toISOString(),
  },
];

function statusBadge(status: Status) {
  const map = {
    pending:  { label: "Awaiting reply", cls: "bg-amber-100 text-amber-700",    icon: Clock },
    accepted: { label: "Accepted",       cls: "bg-emerald-100 text-emerald-700", icon: CheckCircle },
    declined: { label: "Declined",       cls: "bg-red-100 text-red-600",         icon: XCircle },
  };
  const { label, cls, icon: Icon } = map[status];
  return (
    <span className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ${cls}`}>
      <Icon size={11} />
      {label}
    </span>
  );
}

export default function SentInterestsPage() {
  return (
    <div className="max-w-2xl mx-auto space-y-6">
      {/* Tabs */}
      <div className="flex gap-1 border-b border-slate-200">
        <Link
          href="/interests/received"
          className="px-4 py-2.5 text-sm font-medium text-slate-500 border-b-2 border-transparent hover:text-slate-800"
        >
          Received
        </Link>
        <Link
          href="/interests/sent"
          className="px-4 py-2.5 text-sm font-semibold text-blue-700 border-b-2 border-blue-600"
        >
          Sent ({MOCK.length})
        </Link>
      </div>

      {MOCK.length === 0 ? (
        <div className="py-20 text-center">
          <Send size={40} className="mx-auto mb-3 text-slate-300" />
          <p className="font-semibold text-slate-700">No interests sent yet</p>
          <p className="text-sm text-slate-500 mt-1">Browse profiles and send your first interest</p>
          <Link href="/search">
            <button className="mt-4 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">
              Browse profiles
            </button>
          </Link>
        </div>
      ) : (
        <div className="space-y-3">
          {MOCK.map((item) => (
            <div key={item.id} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div className="flex items-center gap-4">
                <div className="h-12 w-12 shrink-0 rounded-full bg-rose-100 flex items-center justify-center text-rose-700 font-bold text-lg">
                  {item.to.name.charAt(0)}
                </div>

                <div className="flex-1 min-w-0">
                  <div className="flex items-center justify-between gap-2 flex-wrap">
                    <div>
                      <Link
                        href={`/profile/${item.to.registrationId}`}
                        className="font-semibold text-slate-900 hover:text-blue-700"
                      >
                        {item.to.name}
                      </Link>
                      <p className="text-xs text-slate-500 mt-0.5">
                        {item.to.age} yrs
                        {item.to.occupation && ` · ${item.to.occupation}`}
                        {item.to.district && ` · ${item.to.district}`}
                      </p>
                    </div>
                    {statusBadge(item.status)}
                  </div>
                  <p className="mt-1 text-xs text-slate-400">
                    Sent on {new Date(item.sentAt).toLocaleDateString("en-GB", {
                      day: "numeric", month: "short", year: "numeric",
                    })}
                  </p>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
