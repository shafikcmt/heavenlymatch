<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    public function index(): Response
    {
        $users = Registration::with('biodata')
            ->latest()
            ->paginate(30);

        return Inertia::render('Admin/Users/Index', ['users' => $users]);
    }

    public function show(string $id): Response
    {
        $user = Registration::with(['biodata'])->findOrFail($id);

        return Inertia::render('Admin/Users/Show', ['user' => $user]);
    }

    public function ban(Request $request, string $id): RedirectResponse
    {
        Registration::findOrFail($id)->ban($request->input('reason', ''));

        return back()->with('success', 'User banned.');
    }

    public function verify(Request $request, string $id): RedirectResponse
    {
        Registration::findOrFail($id)->update([
            'identity_verification_status' => 'verified',
            'identity_verified_at'         => now(),
            'identity_verified_by'         => Auth::id(),
        ]);

        return back()->with('success', 'User verified.');
    }
}
