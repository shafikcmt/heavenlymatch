import Link from "next/link";
import {
  User, Lock, Eye, Bell, UserCheck, Trash2, ChevronRight,
} from "lucide-react";

const SETTING_GROUPS = [
  {
    title: "Account",
    items: [
      { href: "/settings/account", icon: User,      label: "Account details",      desc: "Name, email, mobile number" },
      { href: "/settings/security", icon: Lock,     label: "Password & security",  desc: "Password, 2FA, login activity" },
    ],
  },
  {
    title: "Privacy",
    items: [
      { href: "/settings/privacy",  icon: Eye,      label: "Profile & photo privacy", desc: "Who can see your profile and photos" },
      { href: "/settings/guardian", icon: UserCheck, label: "Guardian / Wali",          desc: "Manage your Wali settings and notifications" },
    ],
  },
  {
    title: "Preferences",
    items: [
      { href: "/settings/notifications", icon: Bell, label: "Notifications", desc: "Email, SMS, and push notifications" },
    ],
  },
  {
    title: "Danger zone",
    items: [
      { href: "/settings/delete-account", icon: Trash2, label: "Delete account", desc: "Permanently remove your profile and data", danger: true },
    ],
  },
];

export default function SettingsPage() {
  return (
    <div className="max-w-xl mx-auto">
      <h1 className="text-xl font-bold text-slate-900 mb-6">Settings</h1>

      <div className="space-y-6">
        {SETTING_GROUPS.map((group) => (
          <div key={group.title}>
            <p className="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
              {group.title}
            </p>
            <div className="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm divide-y divide-slate-100">
              {group.items.map((item) => {
                const Icon = item.icon;
                return (
                  <Link
                    key={item.href}
                    href={item.href}
                    className="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 transition-colors group"
                  >
                    <div className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-xl ${
                      "danger" in item && item.danger
                        ? "bg-red-100 text-red-600"
                        : "bg-slate-100 text-slate-600"
                    }`}>
                      <Icon size={18} />
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className={`text-sm font-semibold ${
                        "danger" in item && item.danger ? "text-red-600" : "text-slate-900"
                      }`}>
                        {item.label}
                      </p>
                      <p className="text-xs text-slate-500 mt-0.5">{item.desc}</p>
                    </div>
                    <ChevronRight size={16} className="text-slate-300 group-hover:text-slate-500 shrink-0" />
                  </Link>
                );
              })}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
