<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function pricing(): Response
    {
        $plans = $this->loadPlans();

        return Inertia::render('Marketing/Pricing', [
            'plans' => $plans,
        ]);
    }

    /**
     * Load active membership plans grouped by name, ordered by sort_order then price.
     * Returns an empty array if the table doesn't exist or is empty — page must not crash.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function loadPlans(): array
    {
        try {
            if (! Schema::hasTable('membership_plans')) {
                return [];
            }

            $rows = MembershipPlan::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('price')
                ->get([
                    'id', 'name', 'slug', 'price', 'duration_months',
                    'is_popular', 'badge', 'color_hex',
                    'interest_express_limit', 'profile_show_limit',
                    'image_upload_limit', 'profile_boost_hours',
                    'can_see_photos', 'can_send_interest',
                    'priority_placement', 'contact_view_limit',
                    'message_limit', 'shortlist_limit',
                ])
                ->toArray();

            // Group by plan name so the frontend can render duration tabs
            $grouped = [];
            foreach ($rows as $row) {
                $grouped[$row['name']][] = $row;
            }

            return $grouped;
        } catch (\Throwable) {
            return [];
        }
    }
}
