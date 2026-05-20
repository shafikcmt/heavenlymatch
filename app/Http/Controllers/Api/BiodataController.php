<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BiodataController extends Controller
{
    /**
     * GET /api/biodata/me
     */
    public function show(Request $request): JsonResponse
    {
        $biodata = $request->user()->biodata;

        if (! $biodata) {
            return response()->json(['message' => 'Biodata not started yet.'], 404);
        }

        return response()->json($biodata);
    }

    /**
     * PUT /api/biodata/me
     * Upsert the current user's biodata. Steps can be submitted incrementally.
     */
    public function update(Request $request): JsonResponse
    {
        $user    = $request->user();
        $biodata = $user->biodata ?? new Biodata(['registration_id' => $user->registration_id]);

        // Whitelist safe biodata fields — never allow status/admin fields from client
        $allowed = $request->except([
            'registration_id', 'status', 'admin_note', 'approved_at',
            'approved_by', 'rejected_at', 'rejected_by', 'profile_score',
        ]);

        $biodata->fill($allowed);

        // Auto-compute completeness
        $biodata->is_completed = $this->isComplete($biodata);

        $biodata->save();

        return response()->json([
            'message'      => 'Biodata saved.',
            'is_completed' => $biodata->is_completed,
        ]);
    }

    /**
     * GET /api/biodata/{registrationId}
     * Public profile view — only returns approved, completed biodatas.
     */
    public function profile(Request $request, string $registrationId): JsonResponse
    {
        $biodata = Biodata::where('registration_id', $registrationId)
            ->where('status', 'approved')
            ->where('is_completed', true)
            ->with('registration:registration_id,name,gender,platform_mode,photo_visibility,membership_plan_name,membership_status')
            ->firstOrFail();

        // Redact guardian contact from non-members
        $viewer = $request->user();
        if (! $viewer || ! $viewer->hasActiveMembership()) {
            $biodata->makeHidden(['guardian_mobile', 'guardian_email']);
        }

        return response()->json($biodata);
    }

    private function isComplete(Biodata $b): bool
    {
        $required = [
            'marital_status', 'birth_date', 'height', 'nationality',
            'highest_qualification', 'occupation', 'family_financial_status',
        ];

        foreach ($required as $field) {
            if (empty($b->$field)) {
                return false;
            }
        }

        return true;
    }
}
