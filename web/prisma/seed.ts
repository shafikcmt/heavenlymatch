import { PrismaClient } from "@prisma/client";
import { PrismaMariaDb } from "@prisma/adapter-mariadb";
import { createPool } from "mariadb";

const dbUrl = process.env.DATABASE_URL ?? "mariadb://root@localhost:3306/heavenlymatch";
const normalized = dbUrl.replace(/^(mysql|mariadb):\/\//, "http://");
const u = new URL(normalized);
const pool = createPool({
  host: u.hostname || "localhost",
  port: u.port ? parseInt(u.port, 10) : 3306,
  user: u.username ? decodeURIComponent(u.username) : "root",
  password: u.password ? decodeURIComponent(u.password) : undefined,
  database: u.pathname.replace(/^\//, ""),
  connectionLimit: 5,
});
const adapter = new PrismaMariaDb(pool);
const prisma = new PrismaClient({ adapter });

async function main() {
  console.log("🌱 Seeding HeavenlyMatch database...");

  // ── Countries ──────────────────────────────────────────────────────────────
  const countries = await prisma.country.createMany({
    data: [
      { name: "Bangladesh",     namebn: "বাংলাদেশ", iso2: "BD", iso3: "BGD", dialCode: "+880",  isPopular: true,  sortOrder: 1 },
      { name: "United Kingdom", namebn: "যুক্তরাজ্য",  iso2: "GB", iso3: "GBR", dialCode: "+44",   isPopular: true,  sortOrder: 2 },
      { name: "United States",  namebn: "যুক্তরাষ্ট্র", iso2: "US", iso3: "USA", dialCode: "+1",    isPopular: true,  sortOrder: 3 },
      { name: "United Arab Emirates", namebn: "সংযুক্ত আরব আমিরাত", iso2: "AE", iso3: "ARE", dialCode: "+971", isPopular: true, sortOrder: 4 },
      { name: "Australia",      namebn: "অস্ট্রেলিয়া", iso2: "AU", iso3: "AUS", dialCode: "+61",   isPopular: true,  sortOrder: 5 },
      { name: "Malaysia",       namebn: "মালয়েশিয়া",  iso2: "MY", iso3: "MYS", dialCode: "+60",   isPopular: true,  sortOrder: 6 },
      { name: "Canada",         namebn: "কানাডা",   iso2: "CA", iso3: "CAN", dialCode: "+1",    isPopular: true,  sortOrder: 7 },
      { name: "Saudi Arabia",   namebn: "সৌদি আরব",  iso2: "SA", iso3: "SAU", dialCode: "+966",  isPopular: true,  sortOrder: 8 },
      { name: "Qatar",          namebn: "কাতার",    iso2: "QA", iso3: "QAT", dialCode: "+974",  isPopular: true,  sortOrder: 9 },
      { name: "Kuwait",         namebn: "কুয়েত",    iso2: "KW", iso3: "KWT", dialCode: "+965",  isPopular: false, sortOrder: 10 },
    ],
    skipDuplicates: true,
  });
  console.log(`  ✓ ${countries.count} countries`);

  // ── Bangladesh Divisions ───────────────────────────────────────────────────
  const bd = await prisma.country.findUnique({ where: { iso2: "BD" } });
  if (!bd) throw new Error("Bangladesh not found after seed");

  const divisionData = [
    { name: "Dhaka",     namebn: "ঢাকা" },
    { name: "Chittagong", namebn: "চট্টগ্রাম" },
    { name: "Rajshahi",  namebn: "রাজশাহী" },
    { name: "Khulna",    namebn: "খুলনা" },
    { name: "Barishal",  namebn: "বরিশাল" },
    { name: "Sylhet",    namebn: "সিলেট" },
    { name: "Rangpur",   namebn: "রংপুর" },
    { name: "Mymensingh", namebn: "ময়মনসিংহ" },
  ];

  for (const d of divisionData) {
    await prisma.division.upsert({
      where: { countryId_name: { countryId: bd.id, name: d.name } },
      create: { ...d, countryId: bd.id },
      update: {},
    });
  }
  console.log(`  ✓ ${divisionData.length} divisions`);

  // ── Districts ──────────────────────────────────────────────────────────────
  const divisionMap = Object.fromEntries(
    (await prisma.division.findMany({ where: { countryId: bd.id }, select: { id: true, name: true } }))
      .map((d) => [d.name, d.id])
  ) as Record<string, number>;

  const districtsByDivision: Record<string, { name: string; namebn: string }[]> = {
    Dhaka: [
      { name: "Dhaka",       namebn: "ঢাকা" },
      { name: "Gazipur",     namebn: "গাজীপুর" },
      { name: "Narayanganj", namebn: "নারায়ণগঞ্জ" },
      { name: "Tangail",     namebn: "টাঙ্গাইল" },
      { name: "Manikganj",   namebn: "মানিকগঞ্জ" },
      { name: "Munshiganj",  namebn: "মুন্সীগঞ্জ" },
      { name: "Narsingdi",   namebn: "নরসিংদী" },
      { name: "Faridpur",    namebn: "ফরিদপুর" },
      { name: "Madaripur",   namebn: "মাদারীপুর" },
      { name: "Shariatpur",  namebn: "শরীয়তপুর" },
      { name: "Rajbari",     namebn: "রাজবাড়ী" },
      { name: "Gopalganj",   namebn: "গোপালগঞ্জ" },
      { name: "Kishoreganj", namebn: "কিশোরগঞ্জ" },
    ],
    Chittagong: [
      { name: "Chittagong", namebn: "চট্টগ্রাম" },
      { name: "Comilla",    namebn: "কুমিল্লা" },
      { name: "Cox's Bazar", namebn: "কক্সবাজার" },
      { name: "Feni",       namebn: "ফেনী" },
      { name: "Brahmanbaria", namebn: "ব্রাহ্মণবাড়িয়া" },
      { name: "Rangamati",  namebn: "রাঙ্গামাটি" },
      { name: "Noakhali",   namebn: "নোয়াখালী" },
      { name: "Chandpur",   namebn: "চাঁদপুর" },
      { name: "Lakshmipur", namebn: "লক্ষ্মীপুর" },
      { name: "Khagrachhari", namebn: "খাগড়াছড়ি" },
      { name: "Bandarban",  namebn: "বান্দরবান" },
    ],
    Sylhet: [
      { name: "Sylhet",     namebn: "সিলেট" },
      { name: "Moulvibazar", namebn: "মৌলভীবাজার" },
      { name: "Habiganj",   namebn: "হবিগঞ্জ" },
      { name: "Sunamganj",  namebn: "সুনামগঞ্জ" },
    ],
    Rajshahi: [
      { name: "Rajshahi",   namebn: "রাজশাহী" },
      { name: "Chapainawabganj", namebn: "চাঁপাইনবাবগঞ্জ" },
      { name: "Natore",     namebn: "নাটোর" },
      { name: "Naogaon",    namebn: "নওগাঁ" },
      { name: "Pabna",      namebn: "পাবনা" },
      { name: "Sirajganj",  namebn: "সিরাজগঞ্জ" },
      { name: "Bogura",     namebn: "বগুড়া" },
      { name: "Joypurhat",  namebn: "জয়পুরহাট" },
    ],
    Khulna: [
      { name: "Khulna",     namebn: "খুলনা" },
      { name: "Bagerhat",   namebn: "বাগেরহাট" },
      { name: "Satkhira",   namebn: "সাতক্ষীরা" },
      { name: "Jessore",    namebn: "যশোর" },
      { name: "Magura",     namebn: "মাগুরা" },
      { name: "Narail",     namebn: "নড়াইল" },
      { name: "Chuadanga",  namebn: "চুয়াডাঙ্গা" },
      { name: "Meherpur",   namebn: "মেহেরপুর" },
      { name: "Jhenaidah",  namebn: "ঝিনাইদহ" },
      { name: "Kushtia",    namebn: "কুষ্টিয়া" },
    ],
    Barishal: [
      { name: "Barishal",   namebn: "বরিশাল" },
      { name: "Bhola",      namebn: "ভোলা" },
      { name: "Jhalokati",  namebn: "ঝালকাঠি" },
      { name: "Patuakhali", namebn: "পটুয়াখালী" },
      { name: "Pirojpur",   namebn: "পিরোজপুর" },
      { name: "Barguna",    namebn: "বরগুনা" },
    ],
    Rangpur: [
      { name: "Rangpur",    namebn: "রংপুর" },
      { name: "Dinajpur",   namebn: "দিনাজপুর" },
      { name: "Kurigram",   namebn: "কুড়িগ্রাম" },
      { name: "Gaibandha",  namebn: "গাইবান্ধা" },
      { name: "Nilphamari", namebn: "নীলফামারী" },
      { name: "Panchagarh", namebn: "পঞ্চগড়" },
      { name: "Thakurgaon", namebn: "ঠাকুরগাঁও" },
      { name: "Lalmonirhat", namebn: "লালমনিরহাট" },
    ],
    Mymensingh: [
      { name: "Mymensingh", namebn: "ময়মনসিংহ" },
      { name: "Jamalpur",   namebn: "জামালপুর" },
      { name: "Netrokona",  namebn: "নেত্রকোণা" },
      { name: "Sherpur",    namebn: "শেরপুর" },
    ],
  };

  let districtCount = 0;
  for (const [divName, districts] of Object.entries(districtsByDivision)) {
    const divId = divisionMap[divName];
    if (!divId) continue;
    for (const d of districts) {
      await prisma.district.upsert({
        where: { divisionId_name: { divisionId: divId, name: d.name } },
        create: { ...d, divisionId: divId },
        update: {},
      });
      districtCount++;
    }
  }
  console.log(`  ✓ ${districtCount} districts`);

  // ── Subscription Plans ─────────────────────────────────────────────────────
  const plans = [
    {
      name: "Silver",   slug: "silver-1m",  tier: "SILVER" as const, durationMonths: 1,
      priceLocal: 799,  priceIntl: 9,
      features: [
        { key: "browse",   label: "Browse 50 profiles/day", included: true },
        { key: "interest", label: "10 interests/day",       included: true },
        { key: "message",  label: "10 messages/day",        included: true },
        { key: "contact",  label: "Contact unlock",         included: false },
        { key: "viewed",   label: "Who viewed me",          included: false },
      ],
      limits: { messageLimit: 10, contactUnlocks: 0, profileBoosts: 0, browseLimit: 50 },
      isPopular: false, sortOrder: 1,
    },
    {
      name: "Gold",     slug: "gold-1m",    tier: "GOLD" as const,   durationMonths: 1,
      priceLocal: 1499, priceIntl: 16,
      features: [
        { key: "browse",   label: "Unlimited browsing",   included: true },
        { key: "interest", label: "Unlimited interests",  included: true },
        { key: "message",  label: "50 messages/day",      included: true },
        { key: "contact",  label: "5 contact unlocks/mo", included: true },
        { key: "viewed",   label: "Who viewed me",        included: true },
        { key: "boost",    label: "3 profile boosts/mo",  included: true },
      ],
      limits: { messageLimit: 50, contactUnlocks: 5, profileBoosts: 3, browseLimit: -1 },
      isPopular: true,  sortOrder: 2,
    },
    {
      name: "Diamond",  slug: "diamond-1m", tier: "DIAMOND" as const, durationMonths: 1,
      priceLocal: 2999, priceIntl: 29,
      features: [
        { key: "browse",   label: "Unlimited everything",     included: true },
        { key: "contact",  label: "20 contact unlocks/mo",    included: true },
        { key: "boost",    label: "5 profile boosts/mo",      included: true },
        { key: "priority", label: "Priority search placement",included: true },
        { key: "support",  label: "Priority support",         included: true },
      ],
      limits: { messageLimit: -1, contactUnlocks: 20, profileBoosts: 5, browseLimit: -1 },
      isPopular: false, sortOrder: 3,
    },
    // 3-month plans
    {
      name: "Silver",   slug: "silver-3m",  tier: "SILVER" as const, durationMonths: 3,
      priceLocal: 1999, priceIntl: 22,
      features: [{ key: "all", label: "All Silver features", included: true }],
      limits: { messageLimit: 10, contactUnlocks: 0, profileBoosts: 0, browseLimit: 50 },
      isPopular: false, sortOrder: 4,
    },
    {
      name: "Gold",     slug: "gold-3m",    tier: "GOLD" as const,   durationMonths: 3,
      priceLocal: 3999, priceIntl: 42,
      features: [{ key: "all", label: "All Gold features", included: true }],
      limits: { messageLimit: 50, contactUnlocks: 5, profileBoosts: 3, browseLimit: -1 },
      isPopular: true,  sortOrder: 5,
    },
    {
      name: "Diamond",  slug: "diamond-3m", tier: "DIAMOND" as const, durationMonths: 3,
      priceLocal: 7499, priceIntl: 79,
      features: [{ key: "all", label: "All Diamond features", included: true }],
      limits: { messageLimit: -1, contactUnlocks: 20, profileBoosts: 5, browseLimit: -1 },
      isPopular: false, sortOrder: 6,
    },
  ];

  for (const p of plans) {
    await prisma.subscriptionPlan.upsert({
      where: { slug: p.slug },
      create: p,
      update: { priceLocal: p.priceLocal, priceIntl: p.priceIntl },
    });
  }
  console.log(`  ✓ ${plans.length} subscription plans`);

  console.log("✅ Seed complete!");
}

main()
  .catch((e) => {
    console.error("Seed failed:", e);
    process.exit(1);
  })
  .finally(() => prisma.$disconnect());
