<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ConnectionRequest;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InterestController extends Controller
{
    public function received(): Response
    {
        /** @var Registration $user */
        $user = Auth::user();

        $interests = ConnectionRequest::with(['sender.biodata'])
            ->where('receiver_id', $user->registration_id)
            ->pending()
            ->latest()
            ->paginate(20);

        return Inertia::render('Dashboard/Interests/Received', [
            'interests' => $interests,
        ]);
    }

    public function sent(): Response
    {
        /** @var Registration $user */
        $user = Auth::user();

        $interests = ConnectionRequest::with(['receiver.biodata'])
            ->where('sender_id', $user->registration_id)
            ->latest()
            ->paginate(20);

        return Inertia::render('Dashboard/Interests/Sent', [
            'interests' => $interests,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        $validated = $request->validate([
            'receiver_id' => ['required', 'string', 'exists:registrations,registration_id'],
            'note'        => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['receiver_id'] === $user->registration_id) {
            return back()->with('error', 'You cannot send interest to yourself.');
        }

        $exists = ConnectionRequest::where('sender_id', $user->registration_id)
            ->where('receiver_id', $validated['receiver_id'])
            ->exists();

        if ($exists) {
            return back()->with('info', 'Interest already sent.');
        }

        ConnectionRequest::create([
            'sender_id'   => $user->registration_id,
            'receiver_id' => $validated['receiver_id'],
            'note'        => $validated['note'] ?? null,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Interest sent successfully!');
    }

    public function respond(Request $request, int $id): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        $validated = $request->validate([
            'action' => ['required', 'in:accept,reject'],
        ]);

        $interest = ConnectionRequest::where('id', $id)
            ->where('receiver_id', $user->registration_id)
            ->pending()
            ->firstOrFail();

        $interest->update([
            'status'      => $validated['action'] === 'accept' ? 'accepted' : 'rejected',
            'responded_at'=> now(),
        ]);

        $message = $validated['action'] === 'accept' ? 'Interest accepted!' : 'Interest declined.';

        return back()->with('success', $message);
    }

    public function withdraw(int $id): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        ConnectionRequest::where('id', $id)
            ->where('sender_id', $user->registration_id)
            ->pending()
            ->firstOrFail()
            ->delete();

        return back()->with('success', 'Interest withdrawn.');
    }
}
