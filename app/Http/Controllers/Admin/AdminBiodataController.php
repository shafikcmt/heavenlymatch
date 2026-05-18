<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminBiodataController extends Controller
{
    public function index(Request $request)
    {
        $query = Biodata::with('registration')->latest('id');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($builder) use ($q) {
                $builder->where('registration_id', 'like', "%{$q}%")
                    ->orWhere('groom_name', 'like', "%{$q}%")
                    ->orWhere('occupation', 'like', "%{$q}%")
                    ->orWhereHas('registration', function ($userQuery) use ($q) {
                        $userQuery->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('status') && Schema::hasColumn('biodatas', 'status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->whereHas('registration', fn ($userQuery) => $userQuery->where('gender', $request->gender));
        }

        if ($request->filled('completed') && Schema::hasColumn('biodatas', 'is_completed')) {
            $query->where('is_completed', $request->completed === 'yes');
        }

        $biodatas = $query->paginate(15)->withQueryString();

        return view('admin.biodatas.index', compact('biodatas'));
    }

    public function show(Biodata $biodata)
    {
        $biodata->load('registration');

        return view('admin.biodatas.show', compact('biodata'));
    }

    public function approve(Request $request, Biodata $biodata)
    {
        $biodata->forceFill([
            'status' => 'approved',
            'admin_note' => $request->input('admin_note'),
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejected_at' => null,
            'rejected_by' => null,
            'is_completed' => true,
        ])->save();

        return back()->with('success', 'Biodata approved successfully.');
    }

    public function reject(Request $request, Biodata $biodata)
    {
        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $biodata->forceFill([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note'),
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ])->save();

        return back()->with('success', 'Biodata rejected.');
    }

    public function pending(Biodata $biodata)
    {
        $biodata->forceFill([
            'status' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'rejected_at' => null,
            'rejected_by' => null,
        ])->save();

        return back()->with('success', 'Biodata moved to pending review.');
    }

    public function feature(Biodata $biodata)
    {
        $biodata->forceFill([
            'is_featured' => true,
            'featured_at' => now(),
        ])->save();

        return back()->with('success', 'Biodata marked as featured.');
    }

    public function unfeature(Biodata $biodata)
    {
        $biodata->forceFill([
            'is_featured' => false,
            'featured_at' => null,
        ])->save();

        return back()->with('success', 'Biodata removed from featured list.');
    }

    public function destroy(Biodata $biodata)
    {
        $biodata->delete();

        return redirect()->route('admin.biodatas.index')->with('success', 'Biodata deleted successfully.');
    }
}
