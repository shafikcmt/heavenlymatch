<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InboxController extends Controller
{
    public function index(): Response
    {
        /** @var Registration $user */
        $user = Auth::user();

        $conversations = Conversation::where(function ($q) use ($user) {
                $q->where('participant_a', $user->registration_id)
                  ->orWhere('participant_b', $user->registration_id);
            })
            ->with(['lastMessage'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return Inertia::render('Dashboard/Inbox/Index', [
            'conversations' => $conversations,
            'myId'          => $user->registration_id,
        ]);
    }

    public function show(int $conversationId): Response
    {
        /** @var Registration $user */
        $user = Auth::user();
        $myId = $user->registration_id;

        $conversation = Conversation::where(function ($q) use ($myId) {
                $q->where('participant_a', $myId)->orWhere('participant_b', $myId);
            })
            ->findOrFail($conversationId);

        $messages = Message::where('conversation_id', $conversationId)
            ->oldest()
            ->get(['id', 'sender_id', 'body', 'is_read', 'created_at']);

        // Mark unread messages as read
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $myId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $otherId = $conversation->otherParty($myId);
        $other   = Registration::with('biodata')->where('registration_id', $otherId)->first();

        return Inertia::render('Dashboard/Inbox/Thread', [
            'conversation' => $conversation,
            'messages'     => $messages,
            'other'        => $other,
            'myId'         => $myId,
        ]);
    }

    public function send(Request $request, int $conversationId): JsonResponse
    {
        /** @var Registration $user */
        $user = Auth::user();
        $myId = $user->registration_id;

        $conversation = Conversation::where(function ($q) use ($myId) {
                $q->where('participant_a', $myId)->orWhere('participant_b', $myId);
            })
            ->findOrFail($conversationId);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $myId,
            'body'            => $validated['body'],
            'is_read'         => false,
        ]);

        $conversation->touch();

        return response()->json([
            'id'         => $message->id,
            'sender_id'  => $message->sender_id,
            'body'       => $message->body,
            'created_at' => $message->created_at,
        ]);
    }

    public function poll(int $conversationId, int $afterId): JsonResponse
    {
        /** @var Registration $user */
        $user = Auth::user();
        $myId = $user->registration_id;

        Conversation::where(function ($q) use ($myId) {
                $q->where('participant_a', $myId)->orWhere('participant_b', $myId);
            })
            ->findOrFail($conversationId);

        $messages = Message::where('conversation_id', $conversationId)
            ->where('id', '>', $afterId)
            ->where('sender_id', '!=', $myId)
            ->oldest()
            ->get(['id', 'sender_id', 'body', 'created_at']);

        if ($messages->isNotEmpty()) {
            Message::where('conversation_id', $conversationId)
                ->where('sender_id', '!=', $myId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json($messages);
    }
}
