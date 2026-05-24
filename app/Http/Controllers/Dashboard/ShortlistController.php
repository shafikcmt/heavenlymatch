<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\PhotoPrivacyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ShortlistController extends Controller
{
    public function __construct(private PhotoPrivacyService $photoPrivacy) {}

    public function index(): Response
    {
        /** @var Registration $user */
        $user = Auth::user();

        $shortlisted = DB::table('shortlists')
            ->join('registrations', 'shortlists.shortlisted_id', '=', 'registrations.registration_id')
            ->leftJoin('biodatas', 'biodatas.registration_id', '=', 'registrations.registration_id')
            ->where('shortlists.user_id', $user->registration_id)
            ->select([
                'registrations.registration_id',
                'registrations.name',
                'registrations.gender',
                'registrations.platform_mode',
                'registrations.photo_visibility',
                'biodatas.district',
                'biodatas.division',
                'biodatas.occupation',
                'biodatas.height_cm',
                'biodatas.birth_date',
                'biodatas.photos',
                'shortlists.created_at as shortlisted_at',
            ])
            ->orderByDesc('shortlists.created_at')
            ->paginate(20);

        // Decode photos JSON and compute photo_url
        $shortlisted->through(function ($row) {
            $photos = [];
            if (!empty($row->photos)) {
                $decoded = is_string($row->photos) ? json_decode($row->photos, true) : $row->photos;
                $photos  = is_array($decoded) ? $decoded : [];
            }

            $row->photo_url = !empty($photos)
                ? $this->photoPrivacy->photoUrl($row->registration_id, 0, $user->registration_id)
                : null;
            $row->has_photo = !empty($photos);
            $row->photos    = $photos;

            return $row;
        });

        return Inertia::render('Dashboard/Shortlist', [
            'shortlisted' => $shortlisted,
        ]);
    }

    public function toggle(Request $request): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        $validated = $request->validate([
            'target_id' => ['required', 'string', 'exists:registrations,registration_id'],
        ]);

        if ($validated['target_id'] === $user->registration_id) {
            return back()->with('error', __('common.error'));
        }

        $exists = DB::table('shortlists')
            ->where('user_id', $user->registration_id)
            ->where('shortlisted_id', $validated['target_id'])
            ->exists();

        if ($exists) {
            DB::table('shortlists')
                ->where('user_id', $user->registration_id)
                ->where('shortlisted_id', $validated['target_id'])
                ->delete();

            return back()->with('info', __('dashboard.shortlist_removed'));
        }

        DB::table('shortlists')->insert([
            'user_id'        => $user->registration_id,
            'shortlisted_id' => $validated['target_id'],
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()->with('info', __('dashboard.shortlist_added'));
    }
}
