<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ShortlistController extends Controller
{
    public function index(): Response
    {
        /** @var Registration $user */
        $user = Auth::user();

        $shortlisted = DB::table('shortlists')
            ->join('registrations', 'shortlists.target_id', '=', 'registrations.registration_id')
            ->leftJoin('biodatas', 'biodatas.registration_id', '=', 'registrations.registration_id')
            ->where('shortlists.user_id', $user->registration_id)
            ->select([
                'registrations.registration_id',
                'registrations.name',
                'registrations.gender',
                'registrations.platform_mode',
                'biodatas.district',
                'biodatas.division',
                'biodatas.occupation',
                'biodatas.height_cm',
                'biodatas.birth_date',
                'biodatas.photos',
                'biodatas.complexion',
                'shortlists.created_at as shortlisted_at',
            ])
            ->orderByDesc('shortlists.created_at')
            ->paginate(20);

        return Inertia::render('Dashboard/Shortlist', [
            'shortlisted' => $shortlisted,
        ]);
    }

    public function toggle(Request $request): \Illuminate\Http\JsonResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        $validated = $request->validate([
            'target_id' => ['required', 'string', 'exists:registrations,registration_id'],
        ]);

        if ($validated['target_id'] === $user->registration_id) {
            return response()->json(['error' => 'Cannot shortlist yourself.'], 422);
        }

        $exists = DB::table('shortlists')
            ->where('user_id', $user->registration_id)
            ->where('target_id', $validated['target_id'])
            ->exists();

        if ($exists) {
            DB::table('shortlists')
                ->where('user_id', $user->registration_id)
                ->where('target_id', $validated['target_id'])
                ->delete();

            return response()->json(['shortlisted' => false]);
        }

        DB::table('shortlists')->insert([
            'user_id'    => $user->registration_id,
            'target_id'  => $validated['target_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['shortlisted' => true]);
    }
}
