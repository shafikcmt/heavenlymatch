<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::with('biodata')->latest('id');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('mobile_number', 'like', "%{$q}%")
                    ->orWhere('registration_id', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status') && Schema::hasColumn('registrations', 'account_status')) {
            $query->where('account_status', $request->status);
        }

        if ($request->filled('verified') && Schema::hasColumn('registrations', 'is_email_verified')) {
            $query->where('is_email_verified', $request->verified === 'yes');
        }

        if ($request->filled('mobile_verified') && Schema::hasColumn('registrations', 'is_mobile_verified')) {
            $query->where('is_mobile_verified', $request->mobile_verified === 'yes');
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'active':
                    if (Schema::hasColumn('registrations', 'account_status')) {
                        $query->where('account_status', '!=', 'blocked');
                    }
                    break;
                case 'banned':
                    if (Schema::hasColumn('registrations', 'account_status')) {
                        $query->where('account_status', 'blocked');
                    }
                    break;
                case 'email-unverified':
                    if (Schema::hasColumn('registrations', 'is_email_verified')) {
                        $query->where(function ($builder) {
                            $builder->whereNull('is_email_verified')->orWhere('is_email_verified', false);
                        });
                    }
                    break;
                case 'mobile-unverified':
                    if (Schema::hasColumn('registrations', 'is_mobile_verified')) {
                        $query->where(function ($builder) {
                            $builder->whereNull('is_mobile_verified')->orWhere('is_mobile_verified', false);
                        });
                    }
                    break;
                case 'kyc-unverified':
                    $query->whereDoesntHave('biodata');
                    break;
                case 'kyc-pending':
                    $query->whereHas('biodata', function ($builder) {
                        if (Schema::hasColumn('biodatas', 'status')) {
                            $builder->where('status', 'pending');
                        }
                    });
                    break;
            }
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(Registration $user)
    {
        $user->load('biodata');

        return view('admin.users.show', compact('user'));
    }

    public function verifyEmail(Registration $user)
    {
        $user->forceFill([
            'is_email_verified' => true,
            'email_verified_at' => $user->email_verified_at ?: now(),
        ])->save();

        return back()->with('success', 'User email marked as verified.');
    }

    public function makeAdmin(Registration $user)
    {
        $user->forceFill([
            'is_admin' => true,
            'role' => 'admin',
        ])->save();

        return back()->with('success', 'User is now an admin.');
    }

    public function removeAdmin(Registration $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot remove your own admin access.');
        }

        $user->forceFill([
            'is_admin' => false,
            'role' => 'user',
        ])->save();

        return back()->with('success', 'Admin access removed.');
    }

    public function block(Request $request, Registration $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot block your own account.');
        }

        $user->forceFill([
            'account_status' => 'blocked',
            'blocked_at' => now(),
            'blocked_reason' => $request->input('blocked_reason'),
        ])->save();

        return back()->with('success', 'User has been blocked.');
    }

    public function unblock(Registration $user)
    {
        $user->forceFill([
            'account_status' => 'active',
            'blocked_at' => null,
            'blocked_reason' => null,
        ])->save();

        return back()->with('success', 'User has been unblocked.');
    }

    public function destroy(Registration $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
