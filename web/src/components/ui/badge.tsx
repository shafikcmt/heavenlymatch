import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/lib/utils";

const badgeVariants = cva(
  "inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold transition-colors",
  {
    variants: {
      variant: {
        default:   "bg-blue-100 text-blue-800",
        secondary: "bg-slate-100 text-slate-700",
        success:   "bg-emerald-100 text-emerald-800",
        warning:   "bg-amber-100 text-amber-800",
        danger:    "bg-red-100 text-red-800",
        premium:   "bg-gradient-to-r from-amber-400 to-yellow-500 text-white",
        outline:   "border border-slate-200 text-slate-700",
        // Subscription tiers
        silver:    "bg-slate-200 text-slate-700",
        gold:      "bg-amber-100 text-amber-700 border border-amber-300",
        diamond:   "bg-gradient-to-r from-blue-500 to-violet-500 text-white",
        free:      "bg-slate-100 text-slate-500",
      },
    },
    defaultVariants: { variant: "default" },
  }
);

export interface BadgeProps
  extends React.HTMLAttributes<HTMLSpanElement>,
    VariantProps<typeof badgeVariants> {}

function Badge({ className, variant, ...props }: BadgeProps) {
  return (
    <span className={cn(badgeVariants({ variant }), className)} {...props} />
  );
}

export { Badge, badgeVariants };
