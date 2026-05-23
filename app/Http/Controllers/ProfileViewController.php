<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use App\Models\ConnectionRequest;
use App\Models\ProfileView;
use App\Models\Registration;
use App\Services\PhotoPrivacyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileViewController extends Controller
{
    public function __construct(private PhotoPrivacyService $photoPrivacy) {}

    public function show(string $registrationId): Response
    {
        $profile = Registration::with('biodata')
            ->where('registration_id', $registrationId)
            ->where('account_status', 'active')
            ->firstOrFail();

        $biodata = $profile->biodata;

        /** @var Registration|null $viewer */
        $viewer  = Auth::user();
        $viewerId = $viewer?->registration_id;

        if ($viewerId && $viewerId !== $registrationId) {
            ProfileView::firstOrCreate(
                ['profile_id' => $registrationId, 'viewer_id' => $viewerId],
                ['created_at' => now()]
            );
            $biodata?->update(['last_active_at' => $biodata->last_active_at]); // no touch
        }

        $interestSent    = false;
        $interestReceived = false;
        $isConnected     = false;
        $isShortlisted   = false;

        if ($viewerId) {
            $interestSent = ConnectionRequest::where('sender_id', $viewerId)
                ->where('receiver_id', $registrationId)->exists();

            $interestReceived = ConnectionRequest::where('sender_id', $registrationId)
                ->where('receiver_id', $viewerId)->pending()->exists();

            $isConnected = ConnectionRequest::where('status', 'accepted')
                ->where(fn($q) =>
                    $q->where('sender_id', $viewerId)->where('receiver_id', $registrationId)
                )
                ->orWhere(fn($q) =>
                    $q->where('sender_id', $registrationId)->where('receiver_id', $viewerId)->where('status', 'accepted')
                )->exists();

            $isShortlisted = \Illuminate\Support\Facades\DB::table('shortlists')
                ->where('user_id', $viewerId)
                ->where('shortlisted_id', $registrationId)
                ->exists();

            $isAlreadyReported = \Illuminate\Support\Facades\DB::table('profile_reports')
                ->where('reporter_id', $viewerId)
                ->where('reported_id', $registrationId)
                ->exists();
        } else {
            $isAlreadyReported = false;
        }

        // Determine photo visibility
        $photos = collect($biodata?->photos ?? [])
            ->map(function ($photo) use ($viewer, $profile) {
                $blurred = $this->photoPrivacy->shouldBlur($profile, $viewer);
                return array_merge($photo, ['blurred' => $blurred]);
            });

        return Inertia::render('Profile/Show', [
            'profile'            => $profile,
            'biodata'            => $biodata,
            'photos'             => $photos,
            'interestSent'       => $interestSent,
            'interestReceived'   => $interestReceived,
            'isConnected'        => $isConnected,
            'isShortlisted'      => $isShortlisted,
            'isOwnProfile'       => $viewerId === $registrationId,
            'isAlreadyReported'  => $isAlreadyReported,
            'profileTrust'       => [
                'isEmailVerified'    => $profile->is_email_verified,
                'isIdentityVerified' => $profile->identity_verification_status === 'verified',
                'biodataApproved'    => $biodata?->status === 'approved',
                'isPremium'          => $profile->hasActiveMembership(),
            ],
        ]);
    }

    public function whoViewed(): Response
    {
        /** @var Registration $user */
        $user = Auth::user();

        $isPremium = $user->hasActiveMembership();

        $viewers = ProfileView::where('profile_id', $user->registration_id)
            ->with('viewer.biodata')
            ->latest()
            ->when(!$isPremium, fn($q) => $q->limit(5))
            ->paginate($isPremium ? 20 : 5);

        return Inertia::render('Profile/WhoViewed', [
            'viewers'    => $viewers,
            'isPremium'  => $isPremium,
            'totalViews' => ProfileView::where('profile_id', $user->registration_id)->count(),
        ]);
    }
}
