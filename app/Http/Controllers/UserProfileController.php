<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    public function showProfile($id = null)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        if ($id) {
            $biodata = Biodata::with('registration')->findOrFail($id);
            $registration = $biodata->registration;
        } else {
            $registration = Registration::with('biodata')
                ->where('registration_id', $user->registration_id)
                ->first();
            $biodata = $registration?->biodata;
        }

        $photos = [];
        if (! empty($biodata?->groom_photo)) {
            $photos[] = $biodata->groom_photo;
        }

        return view('pages.user-dashboard.profiledetail', compact('registration', 'biodata', 'photos'));
    }
}
