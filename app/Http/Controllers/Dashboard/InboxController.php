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
        $user  = Auth::user();
        $myId  = $user->registration_id;

        $conversations = Conversation::where(function ($q) use ($myId) {
                $q->where('user_a_id', $myId)
                  ->orWhere('user_b_id', $myId);
            })
            ->with([
                'latestMessage',
                'userA:registration_id,name,gender',
                'userB:registration_id,name,gender',
            ])
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->through(function ($convo) use ($myId) {
                $other = $convo->user_a_id === $myId ? $convo->userB : $convo->userA;
                $convo->other_participant = $other ? [
                    'registration_id' => $other->registration_id,
                    'name'            => $other->name,
                    'gender'          => $other->gender,
                ] : null;
                $convo->unread_count = Message::where('conversation_id', $convo->id)
                    ->where('sender_id', '!=', $myId)
                    ->whereNull('read_at')
                    ->count();
                return $convo;
            });

        return Inertia::render('Dashboard/Inbox/Index', [
            'conversations' => $conversations,
            'myId'          => $myId,
        ]);
    }

    public function show(int $conversationId): Response
    {
        /** @var Registration $user */
        $user = Auth::user();
        $myId = $user->registration_id;

        $conversation = Conversation::where(function ($q) use ($myId) {
                $q->where('user_a_id', $myId)->orWhere('user_b_id', $myId);
            })
            ->findOrFail($conversationId);

        $messages = Message::where('conversation_id', $conversationId)
            ->oldest()
            ->get(['id', 'sender_id', 'body', 'read_at', 'created_at']);

        // Mark unread messages as read
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $myId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $otherId = $conversation->user_a_id === $myId
            ? $conversation->user_b_id
            : $conversation->user_a_id;

        $other = Registration::with('biodata')->where('registration_id', $otherId)->first();

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
                $q->where('user_a_id', $myId)->orWhere('user_b_id', $myId);
            })
            ->findOrFail($conversationId);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $myId,
            'body'            => $validated['body'],
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
                $q->where('user_a_id', $myId)->orWhere('user_b_id', $myId);
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
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return response()->json($messages);
    }
}
