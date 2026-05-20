<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConnectionRequest;
use App\Models\Guardian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConnectionController extends Controller
{
    /**
     * POST /api/connections/send
     * Send a connection request to another profile.
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|string|exists:registrations,registration_id',
            'message'     => 'nullable|string|max:500',
        ]);

        $sender     = $request->user();
        $receiverId = $request->receiver_id;

        if ($sender->registration_id === $receiverId) {
            return response()->json(['message' => 'Cannot send connection request to yourself.'], 422);
        }

        $existing = ConnectionRequest::where(function ($q) use ($sender, $receiverId) {
            $q->where('sender_id', $sender->registration_id)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($sender, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $sender->registration_id);
        })->whereIn('status', ['pending', 'accepted'])->first();

        if ($existing) {
            return response()->json([
                'message' => 'A connection request already exists.',
                'status'  => $existing->status,
            ], 422);
        }

        // Islamic mode: guardian must be notified before request is visible to receiver
        $guardianPending = false;
        $receiver = \App\Models\Registration::where('registration_id', $receiverId)->first();

        if ($receiver && $receiver->platform_mode === 'ISLAMIC') {
            $guardian = Guardian::where('registration_id', $receiverId)->first();
            if ($guardian && $guardian->wantsNotification('connection_requests')) {
                $guardianPending = true;
                // TODO: dispatch GuardianSmsNotification::class
            }
        }

        $connection = ConnectionRequest::create([
            'sender_id'       => $sender->registration_id,
            'receiver_id'     => $receiverId,
            'status'          => 'pending',
            'message'         => $request->message,
            'guardian_pending'=> $guardianPending,
        ]);

        return response()->json([
            'message'          => $guardianPending
                ? 'Request sent. Guardian will be notified first.'
                : 'Connection request sent.',
            'connection_id'    => $connection->id,
            'guardian_pending' => $guardianPending,
        ], 201);
    }

    /**
     * POST /api/connections/{id}/respond
     * Accept or decline an incoming connection request.
     */
    public function respond(Request $request, int $id): JsonResponse
    {
        $request->validate(['action' => 'required|in:accept,decline']);

        $connection = ConnectionRequest::findOrFail($id);
        $user       = $request->user();

        if ($connection->receiver_id !== $user->registration_id) {
            abort(403, 'Not your connection request.');
        }

        if ($connection->status !== 'pending') {
            return response()->json(['message' => 'Request already ' . $connection->status . '.'], 422);
        }

        $newStatus = $request->action === 'accept' ? 'accepted' : 'declined';
        $connection->update([
            'status'       => $newStatus,
            'responded_at' => now(),
        ]);

        return response()->json(['message' => 'Connection ' . $newStatus . '.']);
    }

    /**
     * GET /api/connections/received
     * Paginated list of received pending requests.
     */
    public function received(Request $request): JsonResponse
    {
        $user = $request->user();

        $connections = ConnectionRequest::where('receiver_id', $user->registration_id)
            ->where('status', 'pending')
            ->with('sender:id,registration_id,name,gender,platform_mode')
            ->latest()
            ->paginate(20);

        return response()->json($connections);
    }

    /**
     * GET /api/connections/sent
     * Paginated list of sent requests with their status.
     */
    public function sent(Request $request): JsonResponse
    {
        $user = $request->user();

        $connections = ConnectionRequest::where('sender_id', $user->registration_id)
            ->with('receiver:id,registration_id,name,gender,platform_mode')
            ->latest()
            ->paginate(20);

        return response()->json($connections);
    }

    /**
     * DELETE /api/connections/{id}
     * Withdraw a pending sent request.
     */
    public function withdraw(Request $request, int $id): JsonResponse
    {
        $connection = ConnectionRequest::findOrFail($id);

        if ($connection->sender_id !== $request->user()->registration_id) {
            abort(403, 'Not your connection request.');
        }

        if ($connection->status !== 'pending') {
            return response()->json(['message' => 'Can only withdraw pending requests.'], 422);
        }

        $connection->delete();

        return response()->json(['message' => 'Connection request withdrawn.']);
    }
}
