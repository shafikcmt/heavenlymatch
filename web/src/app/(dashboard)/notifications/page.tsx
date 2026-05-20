import { Bell, Heart, MessageCircle, Sparkles, ShieldCheck, CheckCheck } from "lucide-react";

type NotifType = "new_interest" | "message" | "match" | "system" | "verification";

interface Notif {
  id: string;
  type: NotifType;
  title: string;
  body: string;
  readAt: string | null;
  createdAt: string;
  link?: string;
}

const MOCK: Notif[] = [
  {
    id: "n1",
    type: "new_interest",
    title: "New interest received",
    body: "Rafiqul Islam expressed interest in your profile.",
    readAt: null,
    createdAt: new Date(Date.now() - 3600000).toISOString(),
    link: "/interests/received",
  },
  {
    id: "n2",
    type: "message",
    title: "New message",
    body: "Nusrat Jahan sent you a message.",
    readAt: null,
    createdAt: new Date(Date.now() - 7200000).toISOString(),
    link: "/inbox/c1",
  },
  {
    id: "n3",
    type: "match",
    title: "New AI match found",
    body: "We found a 92% match for you today. Check your daily picks.",
    readAt: new Date(Date.now() - 86400000).toISOString(),
    createdAt: new Date(Date.now() - 86400000).toISOString(),
    link: "/matches",
  },
  {
    id: "n4",
    type: "verification",
    title: "Identity verified",
    body: "Your NID verification was approved. You now have a Verified badge.",
    readAt: new Date(Date.now() - 86400000 * 2).toISOString(),
    createdAt: new Date(Date.now() - 86400000 * 2).toISOString(),
  },
  {
    id: "n5",
    type: "system",
    title: "Profile tip",
    body: "Adding a photo increases your profile views by 5×. Upload one now.",
    readAt: new Date(Date.now() - 86400000 * 3).toISOString(),
    createdAt: new Date(Date.now() - 86400000 * 3).toISOString(),
    link: "/profile/edit",
  },
];

function notifIcon(type: NotifType) {
  const map = {
    new_interest:  { icon: Heart,         bg: "bg-rose-100",    color: "text-rose-600" },
    message:       { icon: MessageCircle, bg: "bg-blue-100",    color: "text-blue-600" },
    match:         { icon: Sparkles,      bg: "bg-amber-100",   color: "text-amber-600" },
    system:        { icon: Bell,          bg: "bg-slate-100",   color: "text-slate-600" },
    verification:  { icon: ShieldCheck,   bg: "bg-emerald-100", color: "text-emerald-600" },
  };
  const { icon: Icon, bg, color } = map[type];
  return (
    <div className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-full ${bg} ${color}`}>
      <Icon size={18} />
    </div>
  );
}

function timeLabel(iso: string) {
  const diff = (Date.now() - new Date(iso).getTime()) / 1000;
  if (diff < 3600)  return `${Math.floor(diff / 60)}m ago`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
  return `${Math.floor(diff / 86400)}d ago`;
}

export default function NotificationsPage() {
  const unread = MOCK.filter((n) => !n.readAt).length;

  return (
    <div className="max-w-xl mx-auto">
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-xl font-bold text-slate-900 flex items-center gap-2">
            <Bell size={20} className="text-blue-600" />
            Notifications
          </h1>
          {unread > 0 && (
            <p className="text-sm text-slate-500 mt-0.5">{unread} unread</p>
          )}
        </div>
        {unread > 0 && (
          <button className="flex items-center gap-1.5 text-xs text-blue-600 hover:underline">
            <CheckCheck size={13} />
            Mark all read
          </button>
        )}
      </div>

      <div className="space-y-2">
        {MOCK.map((notif) => (
          <a
            key={notif.id}
            href={notif.link ?? "#"}
            className={`flex items-start gap-4 rounded-2xl border p-4 transition-all hover:shadow-sm ${
              !notif.readAt
                ? "border-blue-200 bg-blue-50/50"
                : "border-slate-200 bg-white"
            }`}
          >
            {notifIcon(notif.type)}
            <div className="flex-1 min-w-0">
              <p className={`text-sm ${!notif.readAt ? "font-semibold text-slate-900" : "font-medium text-slate-700"}`}>
                {notif.title}
              </p>
              <p className="mt-0.5 text-sm text-slate-500 line-clamp-2">{notif.body}</p>
              <p className="mt-1.5 text-xs text-slate-400">{timeLabel(notif.createdAt)}</p>
            </div>
            {!notif.readAt && (
              <span className="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-blue-600" />
            )}
          </a>
        ))}
      </div>
    </div>
  );
}
