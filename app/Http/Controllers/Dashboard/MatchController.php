<?php

namespace App\Http\Controllers\Dashboard;

use App\Contracts\MatchingScorerInterface;
use App\Http\Controllers\Controller;
use App\Models\ConnectionRequest;
use App\Models\Registration;
use App\Services\PhotoPrivacyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class MatchController extends Controller
{
    public function __construct(
        private MatchingScorerInterface $scorer,
        private PhotoPrivacyService $photoPrivacy,
    ) {}

    public function index(): Response
    {
        /** @var Registration $user */
        $user = Auth::user();
        $biodata = $user->biodata;

        if (!$biodata) {
            return Inertia::render('Dashboard/Matches', [
                'matches'        => [],
                'hasBiodata'     => false,
                'membershipTier' => $user->hasActiveMembership() ? 'premium' : 'free',
            ]);
        }

        $limit   = $user->hasActiveMembership() ? 50 : 10;
        $matches = $this->scorer->topMatches($biodata, $limit);

        $candidateIds = $matches->map(fn($item) => $item['biodata']->registration_id)->all();
        $sentInterestIds = array_flip(
            ConnectionRequest::where('sender_id', $user->registration_id)
                ->whereIn('receiver_id', $candidateIds)
                ->pluck('receiver_id')
                ->all()
        );
        $shortlistedIds = array_flip(
            DB::table('shortlists')
                ->where('user_id', $user->registration_id)
                ->whereIn('shortlisted_id', $candidateIds)
                ->pluck('shortlisted_id')
                ->all()
        );

        // Transform into ProfileCard-shaped objects
        $transformed = $matches->map(function (array $item) use ($user, $sentInterestIds, $shortlistedIds) {
            $candidate = $item['biodata'];
            $reg       = $candidate->registration;
            $photos    = $candidate->photos ?? [];

            $photoUrl = !empty($photos)
                ? $this->photoPrivacy->photoUrl($candidate->registration_id, 0, $user->registration_id)
                : null;

            return [
                'registration_id'       => $candidate->registration_id,
                'name'                  => $reg?->name ?? '',
                'gender'                => $reg?->gender ?? 'male',
                'age'                   => $candidate->birth_date ? (int) now()->diffInYears($candidate->birth_date) : null,
                'marital_status'        => $candidate->marital_status,
                'religion'              => $candidate->religion ?? 'Islam',
                'sect'                  => $candidate->sect,
                'highest_qualification' => $candidate->highest_qualification,
                'occupation'            => $candidate->occupation,
                'occupation_category'   => $candidate->occupation_category,
                'district'              => $candidate->district,
                'division'              => $candidate->division,
                'residing_country'      => $candidate->residing_country ?? 'Bangladesh',
                'height_cm'             => $candidate->height_cm,
                'is_featured'           => (bool) $candidate->is_featured,
                'is_verified'           => $reg?->identity_verification_status === 'verified',
                'is_premium'            => $reg?->hasActiveMembership() ?? false,
                'is_boosted'            => false,
                'platform_mode'         => $reg?->platform_mode ?? 'general',
                'photo_visibility'      => $reg?->photo_visibility ?? 'members_only',
                'has_photo'             => !empty($photos),
                'photo_url'             => $photoUrl,
                'blurred'               => $reg ? $this->photoPrivacy->shouldBlur($reg, $user) : true,
                'completeness_score'    => $candidate->completeness_score ?? 0,
                'last_active_at'        => $candidate->last_active_at?->toISOString(),
                'match_score'           => $item['total_score'],
                'score_breakdown'       => $item['score_breakdown'],
                'interest_sent'         => isset($sentInterestIds[$candidate->registration_id]),
                'is_shortlisted'        => isset($shortlistedIds[$candidate->registration_id]),
            ];
        });

        return Inertia::render('Dashboard/Matches', [
            'matches'        => $transformed->values(),
            'hasBiodata'     => true,
            'membershipTier' => $user->hasActiveMembership() ? 'premium' : 'free',
        ]);
    }

    public function score(string $targetId): \Illuminate\Http\JsonResponse
    {
        /** @var Registration $user */
        $user = Auth::user();
        $myBiodata     = $user->biodata;
        $targetBiodata = Registration::where('registration_id', $targetId)->first()?->biodata;

        if (!$myBiodata || !$targetBiodata) {
            return response()->json(['score' => null, 'breakdown' => []], 404);
        }

        $result = $this->scorer->score($myBiodata, $targetBiodata);

        return response()->json($result);
    }
}
