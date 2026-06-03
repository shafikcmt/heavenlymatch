<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Biodata;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    public function home(): Response|RedirectResponse
    {
        // Logged-in users never see the guest marketing homepage — send them to
        // their proper dashboard (admins → admin panel, normal users → dashboard).
        if ($user = auth()->user()) {
            $isAdmin = (bool) ($user->is_admin ?? false) || (($user->role ?? 'user') === 'admin');

            return redirect($isAdmin ? route('admin.dashboard') : route('dashboard'));
        }

        $heroPath    = SystemSetting::get('marketing.hero_image', '');
        $successPath = SystemSetting::get('marketing.success_image', '');

        $featuredProfiles = collect();
        try {
            $featuredProfiles = Biodata::where('status', 'approved')
                ->where('is_completed', true)
                ->with('registration:registration_id,name,gender,identity_verification_status,account_status')
                ->whereHas('registration', fn ($q) => $q->where('account_status', 'active'))
                ->orderByDesc('completeness_score')
                ->limit(10)
                ->get()
                ->map(fn ($bio) => [
                    'id'          => $bio->registration_id,
                    'first_name'  => explode(' ', $bio->registration->name ?? '')[0] ?? '—',
                    'gender'      => $bio->registration->gender ?? 'male',
                    'age'         => $bio->birth_date ? (int) now()->diffInYears($bio->birth_date) : null,
                    'district'    => $bio->district,
                    'occupation'  => $bio->occupation,
                    'avatar_num'  => abs(crc32((string) $bio->registration_id)) % 4 + 1,
                    'is_verified' => ($bio->registration->identity_verification_status ?? '') === 'verified',
                ])
                ->values();
        } catch (\Throwable) {
            // Table may not exist on fresh installs — degrade gracefully
        }

        return Inertia::render('Marketing/Home', [
            'heroImageUrl'     => $heroPath ? Storage::disk('public')->url($heroPath) : null,
            'successImageUrl'  => $successPath ? Storage::disk('public')->url($successPath) : null,
            'featuredProfiles' => $featuredProfiles,
        ]);
    }

    public function howItWorks(): Response
    {
        return Inertia::render('Marketing/HowItWorks');
    }

    public function about(): Response
    {
        return Inertia::render('Marketing/About');
    }

    public function contact(): Response
    {
        return Inertia::render('Marketing/Contact');
    }

    public function terms(): Response
    {
        return Inertia::render('Legal/Terms');
    }

    public function privacy(): Response
    {
        return Inertia::render('Legal/Privacy');
    }

    public function blog(): Response
    {
        return Inertia::render('Blog/Index');
    }

    public function blogShow(string $slug): Response
    {
        return Inertia::render('Blog/Show', ['slug' => $slug]);
    }

    public function robots(): HttpResponse
    {
        $sitemap = url('/sitemap.xml');

        $content = <<<TXT
        User-agent: *
        Disallow: /admin
        Disallow: /dashboard
        Disallow: /inbox
        Disallow: /settings
        Disallow: /upgrade
        Disallow: /verify
        Disallow: /biodata
        Disallow: /interests
        Disallow: /shortlist
        Disallow: /notifications
        Disallow: /who-viewed

        Allow: /
        Allow: /how-it-works
        Allow: /about
        Allow: /pricing
        Allow: /contact
        Allow: /terms
        Allow: /privacy
        Allow: /blog

        Sitemap: {$sitemap}
        TXT;

        // Remove leading spaces caused by heredoc indentation
        $content = implode("\n", array_map('ltrim', explode("\n", $content)));

        return response($content, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    public function sitemap(): HttpResponse
    {
        $pages = [
            ['loc' => url('/'),             'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => url('/how-it-works'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => url('/pricing'),      'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => url('/about'),        'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => url('/contact'),      'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => url('/terms'),        'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => url('/privacy'),      'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($pages as $page) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$page['loc']}</loc>\n";
            $xml .= "    <changefreq>{$page['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$page['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function switchLocale(Request $request, string $locale): RedirectResponse
    {
        $supported = ['en', 'bn'];
        if (! in_array($locale, $supported, true)) {
            abort(422, 'Unsupported locale.');
        }

        session(['locale' => $locale]);

        if ($user = auth()->user()) {
            $user->forceFill(['preferred_language' => $locale])->save();
        }

        return back()->withCookie(cookie()->forever('locale', $locale));
    }
}
