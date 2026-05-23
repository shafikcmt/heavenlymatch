<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(): Response
    {
        /** @var Registration $user */
        $user = Auth::user();
        $regId = $user->registration_id;

        $notifications = UserNotification::where('user_id', $regId)
            ->latest()
            ->paginate(30);

        $unreadCount = UserNotification::where('user_id', $regId)
            ->whereNull('read_at')
            ->count();

        return Inertia::render('Dashboard/Notifications', [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }

    public function markRead(string $id): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        UserNotification::where('id', $id)
            ->where('user_id', $user->registration_id)
            ->update(['read_at' => now()]);

        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        UserNotification::where('user_id', $user->registration_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
