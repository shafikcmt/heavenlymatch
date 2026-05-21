<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Registration;
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

        $notifications = $user->notifications()
            ->latest()
            ->paginate(30);

        return Inertia::render('Dashboard/Notifications', [
            'notifications' => $notifications,
            'unreadCount'   => $user->unreadNotifications()->count(),
        ]);
    }

    public function markRead(string $id): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        $user->notifications()->where('id', $id)->update(['read_at' => now()]);

        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        /** @var Registration $user */
        $user = Auth::user();

        $user->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
