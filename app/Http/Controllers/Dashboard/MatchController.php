<?php

namespace App\Http\Controllers\Dashboard;

use App\Contracts\MatchingScorerInterface;
use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MatchController extends Controller
{
    public function __construct(private MatchingScorerInterface $scorer) {}

    public function index(): Response
    {
        /** @var Registration $user */
        $user = Auth::user();
        $biodata = $user->biodata;

        if (!$biodata) {
            return Inertia::render('Dashboard/Matches', [
                'matches'        => [],
                'hasBiodata'     => false,
                'membershipTier' => $user->membership_tier ?? 'free',
            ]);
        }

        $limit = ($user->membership_tier === 'premium') ? 50 : 10;
        $matches = $this->scorer->topMatches($biodata, $limit);

        return Inertia::render('Dashboard/Matches', [
            'matches'        => $matches->values(),
            'hasBiodata'     => true,
            'membershipTier' => $user->membership_tier ?? 'free',
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
