<?php

declare(strict_types=1);

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
    public function index(Request $request): Response
    {
        $query = Registration::with('biodata')
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('registration_id', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('account_status', $status);
        }

        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        if ($membership = $request->input('membership')) {
            $query->where('membership_status', $membership);
        }

        $users = $query->paginate(30)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users'   => $users,
            'filters' => $request->only(['search', 'status', 'gender', 'membership']),
        ]);
    }

    public function show(string $id): Response
    {
        $user = Registration::with(['biodata'])->findOrFail($id);

        $payments = $user->payments()
            ->latest()
            ->take(10)
            ->get(['id', 'transaction_no', 'plan_name', 'amount', 'status', 'created_at', 'external_transaction_id']);

        return Inertia::render('Admin/Users/Show', [
            'user'     => $user,
            'payments' => $payments,
        ]);
    }

    public function ban(Request $request, string $id): RedirectResponse
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        Registration::findOrFail($id)->ban($request->input('reason', ''));

        return back()->with('success', __('admin.user_banned'));
    }

    public function unban(string $id): RedirectResponse
    {
        Registration::findOrFail($id)->activate();

        return back()->with('success', __('admin.user_unbanned'));
    }

    public function suspend(Request $request, string $id): RedirectResponse
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        Registration::findOrFail($id)->suspend($request->input('reason', ''));

        return back()->with('success', __('admin.user_suspended'));
    }

    public function activate(string $id): RedirectResponse
    {
        Registration::findOrFail($id)->activate();

        return back()->with('success', __('admin.user_activated'));
    }

    public function verify(string $id): RedirectResponse
    {
        Registration::findOrFail($id)->update([
            'identity_verification_status' => 'verified',
            'identity_verified_at'         => now(),
            'identity_verified_by'         => Auth::id(),
        ]);

        return back()->with('success', __('admin.user_verified'));
    }
}
