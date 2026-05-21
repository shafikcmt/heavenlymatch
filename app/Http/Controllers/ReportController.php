<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function store(Request $request, string $registrationId): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        $validated = $request->validate([
            'reason'      => ['required', 'in:fake_profile,inappropriate_photo,harassment,spam,underage,other'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $target = Registration::where('registration_id', $registrationId)
            ->where('account_status', 'active')
            ->firstOrFail();

        DB::table('profile_reports')->insertOrIgnore([
            'reporter_id'  => $user->registration_id,
            'reported_id'  => $target->registration_id,
            'reason'       => $validated['reason'],
            'description'  => $validated['description'] ?? null,
            'status'       => 'pending',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return back()->with('success', 'Report submitted. We will review it within 24 hours.');
    }
}
