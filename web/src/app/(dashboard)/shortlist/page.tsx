import Link from "next/link";
import { Heart } from "lucide-react";
import { ProfileCard } from "@/components/profile/ProfileCard";
import { Button } from "@/components/ui/button";
import type { ProfileCard as ProfileCardType } from "@/types/api";

const MOCK: ProfileCardType[] = Array.from({ length: 4 }, (_, i) => ({
  id: `s${i}`,
  registrationId: `HM00030${i + 1}`,
  name: ["Halima Khatun", "Rabeya Begum", "Sufia Islam", "Zahra Akter"][i] ?? "Profile",
  displayName: null,
  age: 22 + i * 2,
  gender: "FEMALE",
  maritalStatus: "never_married",
  religion: "Islam",
  sect: null,
  highestQualification: ["graduation", "post_graduation", "hsc", "graduation"][i] ?? null,
  occupation: ["Teacher", "Nurse", "Student", "Business"][i] ?? null,
  homeDivisionId: null,
  homeDistrictId: null,
  residingCountryId: null,
  heightCm: 155 + i * 3,
  isFeatured: i === 0,
  isVerified: i % 2 === 0,
  isBoosted: false,
  platformMode: "GENERAL",
  photoVisibility: "MEMBERS_ONLY",
  hasPhoto: false,
  photoId: null,
  completenessScore: 65 + i * 8,
  lastActive: null,
  matchScore: 70 + i * 5,
  scoreBreakdown: null,
}));

export default function ShortlistPage() {
  return (
    <div className="max-w-4xl mx-auto">
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-xl font-bold text-slate-900 flex items-center gap-2">
            <Heart size={20} className="text-rose-500" />
            Shortlist
          </h1>
          <p className="text-sm text-slate-500 mt-0.5">{MOCK.length} saved profiles</p>
        </div>
      </div>

      {MOCK.length === 0 ? (
        <div className="py-20 text-center">
          <Heart size={40} className="mx-auto mb-3 text-slate-300" />
          <p className="font-semibold text-slate-700">Your shortlist is empty</p>
          <p className="text-sm text-slate-500 mt-1">
            Click the ♡ on any profile to save it here
          </p>
          <Link href="/search">
            <Button className="mt-4">Browse profiles</Button>
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          {MOCK.map((p) => (
            <ProfileCard key={p.registrationId} profile={p} />
          ))}
        </div>
      )}
    </div>
  );
}
