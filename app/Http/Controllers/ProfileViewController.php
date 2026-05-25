<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use App\Models\ConnectionRequest;
use App\Models\Conversation;
use App\Models\PhotoAccessRequest;
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

        $biodata  = $profile->biodata;

        /** @var Registration|null $viewer */
        $viewer   = Auth::user();
        $viewerId = $viewer?->registration_id;

        if ($viewerId && $viewerId !== $registrationId) {
            ProfileView::firstOrCreate(
                ['profile_id' => $registrationId, 'viewer_id' => $viewerId],
                ['created_at' => now()]
            );
        }

        // ── Social state ──────────────────────────────────────────────────────
        $interestSent      = false;
        $interestReceived  = false;
        $isConnected       = false;
        $isShortlisted     = false;
        $isAlreadyReported = false;
        $conversationId    = null;

        if ($viewerId) {
            $interestSent = ConnectionRequest::where('sender_id', $viewerId)
                ->where('receiver_id', $registrationId)->exists();

            $interestReceived = ConnectionRequest::where('sender_id', $registrationId)
                ->where('receiver_id', $viewerId)->pending()->exists();

            $isConnected = ConnectionRequest::where('status', 'accepted')
                ->where(fn ($q) => $q->where('sender_id', $viewerId)->where('receiver_id', $registrationId))
                ->orWhere(fn ($q) => $q->where('sender_id', $registrationId)->where('receiver_id', $viewerId)->where('status', 'accepted'))
                ->exists();

            if ($isConnected) {
                $conversationId = Conversation::where(function ($q) use ($viewerId, $registrationId) {
                    $q->where('user_a_id', $viewerId)->where('user_b_id', $registrationId);
                })->orWhere(function ($q) use ($viewerId, $registrationId) {
                    $q->where('user_a_id', $registrationId)->where('user_b_id', $viewerId);
                })->value('id');
            }

            $isShortlisted = \Illuminate\Support\Facades\DB::table('shortlists')
                ->where('user_id', $viewerId)
                ->where('shortlisted_id', $registrationId)
                ->exists();

            $isAlreadyReported = \Illuminate\Support\Facades\DB::table('profile_reports')
                ->where('reporter_id', $viewerId)
                ->where('reported_id', $registrationId)
                ->exists();
        }

        // ── Sanitize biodata ─────────────────────────────────────────────────
        // Strip private contact fields for viewers who are not connected and
        // not viewing their own profile. Guardian contact is shown only via
        // the Dashboard/Profile page (own view) or after connection.
        $biodataData = null;
        if ($biodata) {
            $biodataData = $biodata->toArray();
            $biodataData['birth_date'] = $biodata->birth_date?->format('Y-m-d');

            $isOwner = $viewerId === $registrationId;
            if (! $isOwner && ! $isConnected) {
                unset(
                    $biodataData['guardian_mobile'],
                    $biodataData['guardian_email'],
                    $biodataData['guardian_relationship'],
                    $biodataData['permanent_address'],
                );
            }
        }

        // ── Photos via signed serving URLs (no raw storage paths) ────────────
        $photos = collect($biodata?->photos ?? [])
            ->values()
            ->map(function ($photo, int $index) use ($profile, $viewer, $viewerId) {
                return [
                    'url'        => $this->photoPrivacy->photoUrl($profile->registration_id, $index, $viewerId),
                    'is_primary' => (bool) ($photo['is_primary'] ?? ($index === 0)),
                    'blurred'    => $this->photoPrivacy->shouldBlur($profile, $viewer),
                ];
            })
            ->all();

        // ── Photo access request status (Islamic mode only) ─────────────────
        $photoAccessStatus = null;
        if ($viewerId && $viewerId !== $registrationId && $profile->platform_mode === 'islamic') {
            $accessRequest = PhotoAccessRequest::where('requester_id', $viewerId)
                ->where('profile_id', $registrationId)
                ->first();
            $photoAccessStatus = $accessRequest?->status;
        }

        return Inertia::render('Profile/Show', [
            'profile' => [
                'registration_id' => $profile->registration_id,
                'name'            => $profile->name,
                'gender'          => $profile->gender,
                'platform_mode'   => $profile->platform_mode,
            ],
            'biodata'            => $biodataData,
            'photos'             => $photos,
            'interestSent'       => $interestSent,
            'interestReceived'   => $interestReceived,
            'isConnected'        => $isConnected,
            'conversationId'     => $conversationId,
            'isShortlisted'      => $isShortlisted,
            'isOwnProfile'       => $viewerId === $registrationId,
            'isAlreadyReported'  => $isAlreadyReported,
            'photoAccessStatus'  => $photoAccessStatus,
            'profileTrust'       => [
                'isEmailVerified'    => $profile->is_email_verified,
                'isIdentityVerified' => $profile->identity_verification_status === 'verified',
                'biodataApproved'    => $biodata?->status === 'approved',
                'isPremium'          => $profile->hasActiveMembership(),
            ],
        ]);
    }

    public function myProfile(): Response
    {
        /** @var Registration $user */
        $user    = Auth::user();
        $biodata = $user->biodata;

        $biodataData = null;
        if ($biodata) {
            $biodataData = $biodata->toArray();
            $biodataData['birth_date'] = $biodata->birth_date?->format('Y-m-d');
        }

        $photos = collect($biodata?->photos ?? [])
            ->values()
            ->map(function ($photo, int $index) use ($user) {
                return [
                    'url'        => $this->photoPrivacy->photoUrl($user->registration_id, $index, $user->registration_id),
                    'is_primary' => (bool) ($photo['is_primary'] ?? ($index === 0)),
                    'blurred'    => false,
                ];
            })
            ->all();

        return Inertia::render('Dashboard/Profile', [
            'biodata' => $biodataData,
            'photos'  => $photos,
            'user'    => [
                'name'                         => $user->name,
                'gender'                       => $user->gender,
                'registration_id'              => $user->registration_id,
                'account_status'               => $user->account_status,
                'is_email_verified'            => $user->is_email_verified,
                'identity_verification_status' => $user->identity_verification_status,
            ],
            'trust' => [
                'isEmailVerified'    => $user->is_email_verified,
                'isIdentityVerified' => $user->identity_verification_status === 'verified',
                'biodataApproved'    => $biodata?->status === 'approved',
                'isPremium'          => $user->hasActiveMembership(),
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
