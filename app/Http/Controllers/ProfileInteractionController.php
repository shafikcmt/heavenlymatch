<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileInteractionController extends Controller
{
    public function shortlist(Request $request, Biodata $biodata): RedirectResponse
    {
        $ids = collect($request->session()->get('shortlisted_profile_ids', []))
            ->push($biodata->id)
            ->unique()
            ->values()
            ->all();

        $request->session()->put('shortlisted_profile_ids', $ids);

        return back()->with('success', 'Profile added to shortlist.');
    }

    public function interest(Request $request, Biodata $biodata): RedirectResponse
    {
        $ids = collect($request->session()->get('interest_profile_ids', []))
            ->push($biodata->id)
            ->unique()
            ->values()
            ->all();

        $request->session()->put('interest_profile_ids', $ids);

        return back()->with('success', 'Interest sent successfully.');
    }

    public function chat(Request $request, Biodata $biodata): RedirectResponse
    {
        $ids = collect($request->session()->get('interest_profile_ids', []))
            ->push($biodata->id)
            ->unique()
            ->values()
            ->all();

        $request->session()->put('interest_profile_ids', $ids);

        return redirect()->route('inbox')->with('success', 'Chat request created for this profile.');
    }
}
