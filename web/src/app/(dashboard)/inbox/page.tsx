import Link from "next/link";
import { MessageCircle, Search } from "lucide-react";

const MOCK_CONVERSATIONS = [
  {
    id: "c1",
    with: { registrationId: "HM000201", name: "Nusrat Jahan", age: 25 },
    lastMessage: "Walaikum Assalam! Thank you for reaching out.",
    lastMessageAt: new Date(Date.now() - 900000).toISOString(),
    unread: 2,
  },
  {
    id: "c2",
    with: { registrationId: "HM000202", name: "Ayesha Siddiqua", age: 23 },
    lastMessage: "InshaAllah, let us keep in touch.",
    lastMessageAt: new Date(Date.now() - 3600000 * 3).toISOString(),
    unread: 0,
  },
  {
    id: "c3",
    with: { registrationId: "HM000203", name: "Fatema Khatun", age: 27 },
    lastMessage: "JazakAllah for the kind message.",
    lastMessageAt: new Date(Date.now() - 86400000).toISOString(),
    unread: 0,
  },
];

function timeLabel(iso: string) {
  const diff = (Date.now() - new Date(iso).getTime()) / 1000;
  if (diff < 3600)  return `${Math.floor(diff / 60)}m ago`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
  return `${Math.floor(diff / 86400)}d ago`;
}

export default function InboxPage() {
  return (
    <div className="max-w-xl mx-auto">
      <div className="mb-5 flex items-center justify-between">
        <h1 className="text-xl font-bold text-slate-900 flex items-center gap-2">
          <MessageCircle size={20} className="text-blue-600" />
          Messages
        </h1>
        <span className="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
          {MOCK_CONVERSATIONS.reduce((a, c) => a + c.unread, 0)} unread
        </span>
      </div>

      {/* Search */}
      <div className="relative mb-4">
        <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
        <input
          type="search"
          placeholder="Search conversations…"
          className="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-4 text-sm focus:border-blue-500 focus:outline-none"
        />
      </div>

      {MOCK_CONVERSATIONS.length === 0 ? (
        <div className="py-20 text-center">
          <MessageCircle size={40} className="mx-auto mb-3 text-slate-300" />
          <p className="font-semibold text-slate-700">No conversations yet</p>
          <p className="text-sm text-slate-500 mt-1">Accept an interest to start chatting</p>
        </div>
      ) : (
        <div className="space-y-2">
          {MOCK_CONVERSATIONS.map((conv) => (
            <Link
              key={conv.id}
              href={`/inbox/${conv.id}`}
              className="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:border-blue-200 hover:shadow-md transition-all"
            >
              {/* Avatar */}
              <div className="relative shrink-0">
                <div className="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg">
                  {conv.with.name.charAt(0)}
                </div>
                {conv.unread > 0 && (
                  <span className="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white">
                    {conv.unread}
                  </span>
                )}
              </div>

              <div className="flex-1 min-w-0">
                <div className="flex items-baseline justify-between gap-2">
                  <span className={`font-semibold text-slate-900 ${conv.unread > 0 ? "font-bold" : ""}`}>
                    {conv.with.name}
                  </span>
                  <span className="shrink-0 text-xs text-slate-400">{timeLabel(conv.lastMessageAt)}</span>
                </div>
                <p className={`mt-0.5 text-sm truncate ${conv.unread > 0 ? "font-semibold text-slate-800" : "text-slate-500"}`}>
                  {conv.lastMessage}
                </p>
              </div>
            </Link>
          ))}
        </div>
      )}
    </div>
  );
}
