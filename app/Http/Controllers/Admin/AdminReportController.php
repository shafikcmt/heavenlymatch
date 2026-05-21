<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileReport;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminReportController extends Controller
{
    public function index(Request $request): Response
    {
        $tab = $request->input('tab', 'open');

        $counts = [
            'open'     => ProfileReport::whereIn('status', ['open', 'reviewing'])->count(),
            'resolved' => ProfileReport::where('status', 'resolved')->count(),
            'dismissed'=> ProfileReport::where('status', 'dismissed')->count(),
        ];

        $query = ProfileReport::with([
            'reporter:registration_id,name,email',
            'reported:registration_id,name,email',
        ])->latest();

        if ($tab === 'open') {
            $query->whereIn('status', ['open', 'reviewing']);
        } elseif ($tab !== 'all') {
            $query->where('status', $tab);
        }

        $reports = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Reports', [
            'reports' => $reports,
            'counts'  => $counts,
            'tab'     => $tab,
        ]);
    }

    public function resolve(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $report = ProfileReport::findOrFail($id);

        $report->update([
            'status'          => 'resolved',
            'resolution_note' => $request->input('note'),
            'resolved_by'     => Auth::user()->registration_id,
            'resolved_at'     => now(),
        ]);

        UserNotification::send(
            $report->reporter_id,
            'report',
            __('notifications.report_resolved_title'),
            __('notifications.report_resolved_body'),
        );

        return back()->with('success', __('admin.report_resolved'));
    }

    public function dismiss(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $report = ProfileReport::findOrFail($id);

        $report->update([
            'status'          => 'dismissed',
            'resolution_note' => $request->input('note'),
            'resolved_by'     => Auth::user()->registration_id,
            'resolved_at'     => now(),
        ]);

        return back()->with('success', __('admin.report_dismissed'));
    }
}
