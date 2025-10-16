<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Registration;

class UserProfileController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $registration = Registration::with('biodata')
            ->where('registration_id', $user->registration_id)
            ->first();

        $biodata = $registration->biodata ?? null;

        $photos = [];
        if (!empty($biodata->groom_photo)) $photos[] = $biodata->groom_photo;
        if (!empty($biodata->groom_photos) && is_array($biodata->groom_photos)) {
            $photos = array_merge($photos, $biodata->groom_photos);
        }

        return view('pages.user-dashboard.profiledetail', compact('registration', 'biodata', 'photos'));
    }
}
