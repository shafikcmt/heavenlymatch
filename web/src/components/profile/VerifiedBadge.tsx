import { ShieldCheck, Mail, Phone, BadgeCheck } from "lucide-react";
import { cn } from "@/lib/utils";

type VerificationLevel = "email" | "phone" | "id" | "full";

interface VerifiedBadgeProps {
  level?: VerificationLevel;
  size?: "sm" | "md" | "lg";
  showLabel?: boolean;
  className?: string;
}

const CONFIG = {
  email: {
    icon: Mail,
    label: "Email Verified",
    color: "text-blue-600 bg-blue-50",
  },
  phone: {
    icon: Phone,
    label: "Phone Verified",
    color: "text-emerald-600 bg-emerald-50",
  },
  id: {
    icon: ShieldCheck,
    label: "ID Verified",
    color: "text-amber-600 bg-amber-50",
  },
  full: {
    icon: BadgeCheck,
    label: "Fully Verified",
    color: "text-violet-600 bg-violet-50",
  },
};

const SIZE = {
  sm: { icon: 12, text: "text-[10px]", pad: "px-1.5 py-0.5" },
  md: { icon: 14, text: "text-xs",     pad: "px-2 py-0.5" },
  lg: { icon: 18, text: "text-sm",     pad: "px-2.5 py-1" },
};

export function VerifiedBadge({
  level = "email",
  size = "md",
  showLabel = false,
  className,
}: VerifiedBadgeProps) {
  const cfg = CONFIG[level];
  const sz = SIZE[size];
  const Icon = cfg.icon;

  return (
    <span
      title={cfg.label}
      className={cn(
        "inline-flex items-center gap-1 rounded-full font-semibold",
        cfg.color,
        sz.pad,
        className
      )}
    >
      <Icon size={sz.icon} />
      {showLabel && <span className={sz.text}>{cfg.label}</span>}
    </span>
  );
}
