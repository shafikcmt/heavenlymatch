<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminBiodataController extends Controller
{
    public function index(): Response
    {
        $biodatas = Biodata::with('registration')
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Biodatas/Index', ['biodatas' => $biodatas]);
    }

    public function approve(int $id): RedirectResponse
    {
        $biodata = Biodata::findOrFail($id);

        if ($biodata->status === 'approved') {
            return back()->with('info', 'Biodata is already approved.');
        }

        $biodata->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),   // unsignedBigInteger FK → registrations.id (integer PK)
            'rejected_at' => null,
            'rejected_by' => null,
            'admin_note'  => null,
        ]);

        return back()->with('success', 'Biodata approved.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'note' => 'required|string|min:5|max:500',
        ]);

        $biodata = Biodata::findOrFail($id);

        if ($biodata->status === 'rejected') {
            return back()->with('info', 'Biodata is already rejected.');
        }

        $biodata->update([
            'status'      => 'rejected',
            'admin_note'  => $request->input('note'),
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),   // unsignedBigInteger FK → registrations.id (integer PK)
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('success', 'Biodata rejected.');
    }
}
