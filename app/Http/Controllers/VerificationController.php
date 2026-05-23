<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class VerificationController extends Controller
{
    public function identity(Request $request): Response
    {
        /** @var Registration $user */
        $user    = Auth::user();
        $biodata = $user->biodata;

        return Inertia::render('Verification/Identity', [
            'isEmailVerified' => $user->is_email_verified,
            'email'           => $user->email,
            'identityStatus'  => $user->identity_verification_status ?? 'unverified',
            'biodataStatus'   => $biodata?->status ?? null,
            'biodataComplete' => (bool) ($biodata?->is_completed),
            'hasPhotos'       => ! empty($biodata?->photos),
            'isPremium'       => $user->hasActiveMembership(),
            'membershipStatus'=> $user->membership_status ?? 'free',
        ]);
    }
}
