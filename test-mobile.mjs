import { chromium } from 'playwright';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const BASE = 'http://127.0.0.1:8765';
const OUT = path.join(__dirname, 'docs', 'screenshots');

// iPhone 14 Pro viewport
const MOBILE = { width: 390, height: 844, deviceScaleFactor: 2, isMobile: true, hasTouch: true };

async function shot(page, name) {
  await page.waitForTimeout(800);
  await page.screenshot({ path: path.join(OUT, `${name}.png`), fullPage: false });
  console.log(`✓ ${name}.png`);
}

(async () => {
  const browser = await chromium.launch({ headless: true });
  const ctx = await browser.newContext({ viewport: MOBILE, userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15' });
  const page = await ctx.newPage();

  // ── 1. Marketing home ───────────────────────────────────────────────
  console.log('\n── Marketing home ──');
  await page.goto(BASE + '/', { waitUntil: 'networkidle' });
  await shot(page, '01-home-mobile');

  // ── 2. Login page ──────────────────────────────────────────────────
  console.log('\n── Login ──');
  await page.goto(BASE + '/login', { waitUntil: 'networkidle' });
  await shot(page, '02-login-mobile');

  // ── 3. Log in with test credentials ────────────────────────────────
  console.log('\n── Logging in… ──');
  await page.fill('input[name="email"], input[type="email"]', 'abdullah.mamun@heavenlymatch.test');
  await page.fill('input[name="password"], input[type="password"]', 'password123');
  await page.click('button[type="submit"]');
  await page.waitForURL(/\/(dashboard|matches|search)/, { timeout: 12000 }).catch(() => {});
  await page.waitForTimeout(1500);
  await shot(page, '03-dashboard-mobile');

  // ── 4. Matches page ─────────────────────────────────────────────────
  console.log('\n── Matches ──');
  await page.goto(BASE + '/matches', { waitUntil: 'networkidle' });
  await shot(page, '04-matches-mobile');

  // ── 5. Search page ──────────────────────────────────────────────────
  console.log('\n── Search ──');
  await page.goto(BASE + '/search', { waitUntil: 'networkidle' });
  await shot(page, '05-search-mobile');

  // ── 6. Shortlist page ───────────────────────────────────────────────
  console.log('\n── Shortlist ──');
  await page.goto(BASE + '/shortlist', { waitUntil: 'networkidle' });
  await shot(page, '06-shortlist-mobile');

  // ── 7. Click first profile (if any results visible) ─────────────────
  console.log('\n── Profile detail (from matches) ──');
  await page.goto(BASE + '/matches', { waitUntil: 'networkidle' });
  // Match profile links like /profile/HM900002 (registration IDs)
  const profileLinks = await page.$$('a[href*="/profile/HM"]');
  const firstProfile = profileLinks[0] || null;
  if (firstProfile) {
    const href = await firstProfile.getAttribute('href');
    console.log('  → clicking profile link:', href);
    await firstProfile.click();
    await page.waitForTimeout(2000);
    console.log('  → current URL:', page.url());
    await shot(page, '07-profile-detail-mobile');
    // Scroll down to see section tabs
    await page.evaluate(() => window.scrollTo(0, 400));
    await page.waitForTimeout(600);
    await shot(page, '08-profile-detail-scrolled');
  } else {
    console.log('  (no profiles to click — skipping profile detail)');
  }

  // ── 8. Scroll matches to see more cards ─────────────────────────────
  await page.goto(BASE + '/matches', { waitUntil: 'networkidle' });
  await page.evaluate(() => window.scrollTo(0, 600));
  await page.waitForTimeout(600);
  await shot(page, '09-matches-scrolled');

  // ── 9. Notifications ────────────────────────────────────────────────
  console.log('\n── Notifications ──');
  await page.goto(BASE + '/notifications', { waitUntil: 'networkidle' });
  await shot(page, '10-notifications-mobile');

  await browser.close();
  console.log('\nAll screenshots saved to docs/screenshots/');
})().catch(e => { console.error(e); process.exit(1); });
