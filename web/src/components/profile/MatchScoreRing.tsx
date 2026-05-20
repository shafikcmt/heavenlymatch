"use client";

import { useEffect, useRef } from "react";
import { cn } from "@/lib/utils";

interface MatchScoreRingProps {
  score: number; // 0-100
  size?: number;
  strokeWidth?: number;
  className?: string;
  animate?: boolean;
}

function scoreColor(score: number): string {
  if (score >= 81) return "#1B4FD8"; // blue — excellent
  if (score >= 66) return "#10B981"; // green — good
  if (score >= 41) return "#F59E0B"; // amber — fair
  return "#EF4444";                  // red — low
}

export function MatchScoreRing({
  score,
  size = 56,
  strokeWidth = 5,
  className,
  animate = true,
}: MatchScoreRingProps) {
  const clampedScore = Math.min(100, Math.max(0, score));
  const center = size / 2;
  const radius = center - strokeWidth;
  const circumference = 2 * Math.PI * radius;
  const targetOffset = circumference * (1 - clampedScore / 100);

  const circleRef = useRef<SVGCircleElement>(null);

  useEffect(() => {
    if (!animate || !circleRef.current) return;
    // Start at full offset (invisible), animate to target
    circleRef.current.style.strokeDashoffset = String(circumference);
    const frame = requestAnimationFrame(() => {
      if (circleRef.current) {
        circleRef.current.style.transition =
          "stroke-dashoffset 1.4s cubic-bezier(0.16, 1, 0.3, 1)";
        circleRef.current.style.strokeDashoffset = String(targetOffset);
      }
    });
    return () => cancelAnimationFrame(frame);
  }, [clampedScore, circumference, targetOffset, animate]);

  const color = scoreColor(clampedScore);

  return (
    <div
      className={cn("relative inline-flex items-center justify-center", className)}
      style={{ width: size, height: size }}
      title={`Match score: ${clampedScore}%`}
    >
      <svg
        width={size}
        height={size}
        viewBox={`0 0 ${size} ${size}`}
        className="-rotate-90"
        aria-hidden
      >
        {/* Background track */}
        <circle
          cx={center}
          cy={center}
          r={radius}
          fill="none"
          stroke="#E2E8F0"
          strokeWidth={strokeWidth}
        />
        {/* Score arc */}
        <circle
          ref={circleRef}
          cx={center}
          cy={center}
          r={radius}
          fill="none"
          stroke={color}
          strokeWidth={strokeWidth}
          strokeLinecap="round"
          strokeDasharray={circumference}
          strokeDashoffset={animate ? circumference : targetOffset}
          style={{ transition: animate ? undefined : "none" }}
        />
      </svg>

      {/* Center label */}
      <span
        className="absolute font-bold tabular-nums"
        style={{
          fontSize: size * 0.22,
          color,
          lineHeight: 1,
        }}
      >
        {clampedScore}%
      </span>
    </div>
  );
}
