<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminBiodataController extends Controller
{
    public function index(Request $request): Response
    {
        $tab = $request->input('tab', 'pending');

        $counts = [
            'pending'  => Biodata::where('status', 'pending')->count(),
            'approved' => Biodata::where('status', 'approved')->count(),
            'rejected' => Biodata::where('status', 'rejected')->count(),
            'hidden'   => Biodata::where('status', 'hidden')->count(),
            'all'      => Biodata::count(),
        ];

        $query = Biodata::with('registration:registration_id,name,email,gender,platform_mode')
            ->latest();

        if ($tab !== 'all') {
            $query->where('status', $tab);
        }

        $biodatas = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Biodatas/Index', [
            'biodatas' => $biodatas,
            'counts'   => $counts,
            'tab'      => $tab,
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $biodata = Biodata::findOrFail($id);

        if ($biodata->status === 'approved') {
            return back()->with('info', __('admin.biodata_approved'));
        }

        $biodata->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejected_at' => null,
            'rejected_by' => null,
            'admin_note'  => null,
        ]);

        UserNotification::send(
            $biodata->registration_id,
            'biodata',
            __('notifications.biodata_approved_title'),
            __('notifications.biodata_approved_body'),
        );

        return back()->with('success', __('admin.biodata_approved'));
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'note' => 'required|string|min:5|max:500',
        ]);

        $biodata = Biodata::findOrFail($id);

        if ($biodata->status === 'rejected') {
            return back()->with('info', __('admin.biodata_rejected'));
        }

        $biodata->update([
            'status'      => 'rejected',
            'admin_note'  => $request->input('note'),
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'approved_at' => null,
            'approved_by' => null,
        ]);

        UserNotification::send(
            $biodata->registration_id,
            'biodata',
            __('notifications.biodata_rejected_title'),
            __('notifications.biodata_rejected_body', ['reason' => $request->input('note')]),
        );

        return back()->with('success', __('admin.biodata_rejected'));
    }
}
