<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:100',
            'email'               => 'required|email|max:180|unique:registrations,email',
            'password'            => 'required|confirmed|min:8',
            'gender'              => 'required|in:male,female',
            'profile_created_for' => 'required|in:self,son,daughter,brother,sister,relative',
            'platform_mode'       => 'required|in:general,islamic',
            'terms_accepted'      => 'accepted',
        ]);

        $reg = Registration::create([
            'name'                => $validated['name'],
            'email'               => $validated['email'],
            'password'            => Hash::make($validated['password']),
            'gender'              => $validated['gender'],
            'looking_for'         => $validated['gender'] === 'male' ? 'bride' : 'groom',
            'profile_created_for' => $validated['profile_created_for'],
            'platform_mode'       => $validated['platform_mode'],
            'photo_visibility'    => $validated['platform_mode'] === 'islamic' ? 'blurred' : 'members_only',
            'terms_accepted_at'   => now(),
            // account_status and role intentionally omitted — migration defaults apply:
            // account_status = 'active',  role = 'user'
        ]);

        Auth::login($reg);
        $request->session()->regenerate();

        $reg->sendEmailVerificationNotification();

        return redirect()->route('biodata.wizard', ['step' => 1])
            ->with('success', 'Welcome to HeavenlyMatch! Please complete your biodata to find matches.');
    }
}
