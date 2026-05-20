import Link from "next/link";
import { Heart, CheckCircle, XCircle, Clock, MessageCircle } from "lucide-react";
import { Button } from "@/components/ui/button";

type InterestStatus = "pending" | "accepted" | "declined";

interface InterestItem {
  id: string;
  from: { registrationId: string; name: string; age: number; occupation: string | null; district: string | null };
  message: string | null;
  status: InterestStatus;
  sentAt: string;
}

const MOCK: InterestItem[] = [
  {
    id: "1",
    from: { registrationId: "HM000101", name: "Rafiqul Islam", age: 28, occupation: "Engineer", district: "Dhaka" },
    message: "Assalamu Alaikum. I came across your profile and felt we might be compatible. I would love to learn more about you.",
    status: "pending",
    sentAt: new Date(Date.now() - 7200000).toISOString(),
  },
  {
    id: "2",
    from: { registrationId: "HM000102", name: "Tarek Hossain", age: 31, occupation: "Doctor", district: "Chittagong" },
    message: null,
    status: "accepted",
    sentAt: new Date(Date.now() - 86400000).toISOString(),
  },
  {
    id: "3",
    from: { registrationId: "HM000103", name: "Abdullah Al-Mamun", age: 26, occupation: "Teacher", district: "Sylhet" },
    message: "I found your profile through HeavenlyMatch and would like to connect.",
    status: "pending",
    sentAt: new Date(Date.now() - 172800000).toISOString(),
  },
];

function statusBadge(status: InterestStatus) {
  const map = {
    pending:  { label: "Pending",  cls: "bg-amber-100 text-amber-700",   icon: Clock },
    accepted: { label: "Accepted", cls: "bg-emerald-100 text-emerald-700", icon: CheckCircle },
    declined: { label: "Declined", cls: "bg-red-100 text-red-600",         icon: XCircle },
  };
  const { label, cls, icon: Icon } = map[status];
  return (
    <span className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ${cls}`}>
      <Icon size={11} />
      {label}
    </span>
  );
}

export default function ReceivedInterestsPage() {
  return (
    <div className="max-w-2xl mx-auto space-y-6">
      {/* Tabs */}
      <div className="flex gap-1 border-b border-slate-200">
        <Link
          href="/interests/received"
          className="px-4 py-2.5 text-sm font-semibold text-blue-700 border-b-2 border-blue-600"
        >
          Received ({MOCK.filter((i) => i.status === "pending").length})
        </Link>
        <Link
          href="/interests/sent"
          className="px-4 py-2.5 text-sm font-medium text-slate-500 border-b-2 border-transparent hover:text-slate-800"
        >
          Sent
        </Link>
      </div>

      {MOCK.length === 0 ? (
        <div className="py-20 text-center">
          <Heart size={40} className="mx-auto mb-3 text-slate-300" />
          <p className="font-semibold text-slate-700">No interests yet</p>
          <p className="text-sm text-slate-500 mt-1">Complete your profile to attract more matches</p>
          <Link href="/profile/edit">
            <Button className="mt-4">Complete profile</Button>
          </Link>
        </div>
      ) : (
        <div className="space-y-3">
          {MOCK.map((item) => (
            <div
              key={item.id}
              className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <div className="flex items-start gap-4">
                {/* Avatar placeholder */}
                <div className="h-12 w-12 shrink-0 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg">
                  {item.from.name.charAt(0)}
                </div>

                <div className="flex-1 min-w-0">
                  <div className="flex items-center justify-between gap-2 flex-wrap">
                    <div>
                      <Link
                        href={`/profile/${item.from.registrationId}`}
                        className="font-semibold text-slate-900 hover:text-blue-700"
                      >
                        {item.from.name}
                      </Link>
                      <p className="text-xs text-slate-500 mt-0.5">
                        {item.from.age} yrs
                        {item.from.occupation && ` · ${item.from.occupation}`}
                        {item.from.district && ` · ${item.from.district}`}
                      </p>
                    </div>
                    {statusBadge(item.status)}
                  </div>

                  {item.message && (
                    <p className="mt-2 text-sm text-slate-600 italic line-clamp-2">
                      &ldquo;{item.message}&rdquo;
                    </p>
                  )}

                  <p className="mt-2 text-xs text-slate-400">
                    {new Date(item.sentAt).toLocaleDateString("en-GB", {
                      day: "numeric", month: "short", year: "numeric",
                    })}
                  </p>

                  {item.status === "pending" && (
                    <div className="mt-3 flex gap-2">
                      <Button size="sm" className="gap-1">
                        <CheckCircle size={13} />
                        Accept
                      </Button>
                      <Button size="sm" variant="outline" className="gap-1 text-red-600 border-red-200 hover:bg-red-50">
                        <XCircle size={13} />
                        Decline
                      </Button>
                      <Link href={`/profile/${item.from.registrationId}`}>
                        <Button size="sm" variant="ghost">View profile</Button>
                      </Link>
                    </div>
                  )}

                  {item.status === "accepted" && (
                    <div className="mt-3 flex gap-2">
                      <Button size="sm" variant="outline" className="gap-1">
                        <MessageCircle size={13} />
                        Message
                      </Button>
                      <Link href={`/profile/${item.from.registrationId}`}>
                        <Button size="sm" variant="ghost">View profile</Button>
                      </Link>
                    </div>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
