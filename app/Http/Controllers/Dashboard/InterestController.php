<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ConnectionRequest;
use App\Models\Conversation;
use App\Models\Registration;
use App\Models\UserNotification;
use App\Notifications\HeavenlyMatchNotification;
use App\Services\ProfileCompletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InterestController extends Controller
{
    public function received(Request $request): Response
    {
        /** @var Registration $user */
        $user = Auth::user();
        $status = in_array($request->query('status'), ['pending', 'accepted', 'declined'])
            ? $request->query('status')
            : 'pending';

        $myId = $user->registration_id;

        $interests = ConnectionRequest::with(['sender.biodata', 'conversation'])
            ->where('receiver_id', $myId)
            ->where('status', $status)
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'  => ConnectionRequest::where('receiver_id', $myId)->where('status', 'pending')->count(),
            'accepted' => ConnectionRequest::where('receiver_id', $myId)->where('status', 'accepted')->count(),
            'declined' => ConnectionRequest::where('receiver_id', $myId)->where('status', 'declined')->count(),
        ];

        return Inertia::render('Dashboard/Interests/Received', [
            'interests'     => $interests,
            'counts'        => $counts,
            'currentStatus' => $status,
        ]);
    }

    public function sent(Request $request): Response
    {
        /** @var Registration $user */
        $user = Auth::user();
        $status = in_array($request->query('status'), ['pending', 'accepted', 'declined'])
            ? $request->query('status')
            : 'pending';

        $myId = $user->registration_id;

        $interests = ConnectionRequest::with(['receiver.biodata', 'conversation'])
            ->where('sender_id', $myId)
            ->where('status', $status)
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'  => ConnectionRequest::where('sender_id', $myId)->where('status', 'pending')->count(),
            'accepted' => ConnectionRequest::where('sender_id', $myId)->where('status', 'accepted')->count(),
            'declined' => ConnectionRequest::where('sender_id', $myId)->where('status', 'declined')->count(),
        ];

        return Inertia::render('Dashboard/Interests/Sent', [
            'interests'     => $interests,
            'counts'        => $counts,
            'currentStatus' => $status,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        // Block if profile completion is too low
        $completion = ProfileCompletionService::compute($user);
        if (!$completion['can_send_interest']) {
            return back()->with('error', 'Complete at least 30% of your biodata to send interest.');
        }

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
            'sender_id'       => $user->registration_id,
            'receiver_id'     => $validated['receiver_id'],
            'initial_message' => $validated['note'] ?? null,
            'status'          => 'pending',
        ]);

        $receiver = Registration::where('registration_id', $validated['receiver_id'])
            ->select(['id', 'registration_id', 'name', 'email', 'preferred_language'])
            ->first();

        if ($receiver) {
            $lang = $receiver->preferred_language ?? 'bn';

            UserNotification::send(
                $receiver->registration_id,
                'interest',
                trans('notifications.interest_received_title', ['name' => $user->name], $lang),
                trans('notifications.interest_received_body', ['name' => $user->name], $lang),
                ['from' => $user->registration_id],
            );

            $receiver->notify(new HeavenlyMatchNotification(
                subject: trans('notifications.email_subject_interest', ['name' => $user->name], $lang),
                greeting: trans('notifications.email_greeting', ['name' => $receiver->name], $lang),
                introLines: [
                    trans('notifications.interest_received_body', ['name' => $user->name], $lang),
                ],
                actionUrl: url('/interests/received'),
                actionText: trans('notifications.email_action_view_interests', [], $lang),
            ));
        }

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
            'status'      => $validated['action'] === 'accept' ? 'accepted' : 'declined',
            'responded_at'=> now(),
        ]);

        if ($validated['action'] === 'accept') {
            Conversation::firstOrCreate(
                ['connection_request_id' => $interest->id],
                [
                    'user_a_id' => $interest->sender_id,
                    'user_b_id' => $user->registration_id,
                    'is_active' => true,
                ],
            );
        }

        $sender = Registration::where('registration_id', $interest->sender_id)
            ->select(['id', 'registration_id', 'name', 'email', 'preferred_language'])
            ->first();

        if ($sender) {
            $lang = $sender->preferred_language ?? 'bn';

            if ($validated['action'] === 'accept') {
                UserNotification::send(
                    $sender->registration_id,
                    'interest',
                    trans('notifications.interest_accepted_title', ['name' => $user->name], $lang),
                    trans('notifications.interest_accepted_body', ['name' => $user->name], $lang),
                );

                $sender->notify(new HeavenlyMatchNotification(
                    subject: trans('notifications.email_subject_accepted', [], $lang),
                    greeting: trans('notifications.email_greeting', ['name' => $sender->name], $lang),
                    introLines: [
                        trans('notifications.interest_accepted_body', ['name' => $user->name], $lang),
                    ],
                    actionUrl: url('/interests/sent'),
                    actionText: trans('notifications.email_action_view_interests', [], $lang),
                ));
            } else {
                UserNotification::send(
                    $sender->registration_id,
                    'interest',
                    trans('notifications.interest_declined_title', ['name' => $user->name], $lang),
                    trans('notifications.interest_declined_body', ['name' => $user->name], $lang),
                );

                $sender->notify(new HeavenlyMatchNotification(
                    subject: trans('notifications.email_subject_declined', [], $lang),
                    greeting: trans('notifications.email_greeting', ['name' => $sender->name], $lang),
                    introLines: [
                        trans('notifications.interest_declined_body', ['name' => $user->name], $lang),
                    ],
                ));
            }
        }

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
