<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\Registration;
use App\Models\UserNotification;
use App\Notifications\HeavenlyMatchNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $query = $this->buildQuery($request, $tab);

        $biodatas = $query->paginate(20)->withQueryString()
            ->through(fn (Biodata $b) => $this->presentRow($b));

        return Inertia::render('Admin/Biodatas/Index', [
            'biodatas' => $biodatas,
            'counts'   => $counts,
            'tab'      => $tab,
            'filters'  => $request->only($this->filterKeys()),
            'sects'    => $this->sectOptions(),
        ]);
    }

    public function show(int $id): Response
    {
        $biodata = Biodata::with([
            'registration:id,registration_id,name,email,gender,platform_mode,photo_visibility,identity_verification_status,account_status,created_at',
        ])->findOrFail($id);

        return Inertia::render('Admin/Biodatas/Show', [
            'biodata' => $biodata,
        ]);
    }

    /**
     * GET /admin/biodatas/{id}/preview  (JSON)
     * Lightweight curated payload for the admin quick-preview modal.
     * Admin-only (route is behind auth + admin middleware). Sensitive
     * contact data is masked exactly as on the full detail page.
     */
    public function preview(int $id): JsonResponse
    {
        $b = Biodata::with([
            'registration:id,registration_id,name,email,gender,platform_mode,is_email_verified,is_mobile_verified,membership_status,membership_plan_name,identity_verification_status,account_status,created_at',
        ])->findOrFail($id);

        $reg = $b->registration;

        $age = $b->birth_date
            ? (int) $b->birth_date->diffInYears(now())
            : null;

        return response()->json([
            'id'                 => $b->id,
            'registration_id'    => $b->registration_id,
            'status'             => $b->status,
            'completeness_score' => $b->completeness_score,
            'admin_note'         => $b->admin_note,
            'updated_at'         => optional($b->updated_at)->toDateTimeString(),
            'member' => $reg ? [
                'name'                => $reg->name,
                'email'               => $reg->email,
                'gender'              => $reg->gender,
                'platform_mode'       => $reg->platform_mode,
                'is_email_verified'   => (bool) $reg->is_email_verified,
                'is_mobile_verified'  => (bool) $reg->is_mobile_verified,
                'membership_status'   => $reg->membership_status,
                'membership_plan_name'=> $reg->membership_plan_name,
                'identity_status'     => $reg->identity_verification_status,
            ] : null,
            'basic' => [
                'age'            => $age,
                'marital_status' => $b->marital_status,
                'height_cm'      => $b->height_cm,
                'weight_kg'      => $b->weight_kg,
                'complexion'     => $b->complexion,
                'blood_group'    => $b->blood_group,
                'about_me'       => $b->about_me,
            ],
            'location' => [
                'district'         => $b->district,
                'division'         => $b->division,
                'upazila'          => $b->upazila,
                'residing_country' => $b->residing_country,
            ],
            'religion' => [
                'religion'                => $b->religion,
                'sect'                    => $b->sect,
                'prayers_info'            => $b->prayers_info,
                'is_practicing'           => $b->is_practicing,
                'is_islamically_educated' => $b->is_islamically_educated,
                'hijab_info'              => $b->hijab_info,
                'beard_info'              => $b->beard_info,
            ],
            'education' => [
                'highest_qualification' => $b->highest_qualification,
                'occupation'            => $b->occupation,
                'occupation_category'   => $b->occupation_category,
                'monthly_income'        => $b->monthly_income,
            ],
            'family' => [
                'father_alive'            => $b->father_alive,
                'father_profession'       => $b->father_profession,
                'mother_alive'            => $b->mother_alive,
                'brothers'                => $b->brothers,
                'sisters'                 => $b->sisters,
                'family_type'             => $b->family_type,
                'family_financial_status' => $b->family_financial_status,
            ],
            'partner' => [
                'age_min'      => $b->partner_age_min,
                'age_max'      => $b->partner_age_max,
                'sect'         => $b->partner_sect,
                'education'    => $b->partner_education,
                'expectations' => $b->partner_expectations,
            ],
            'contact' => [
                'guardian_relationship' => $b->guardian_relationship,
                'guardian_mobile'       => $b->guardian_mobile
                    ? substr($b->guardian_mobile, 0, 4) . '***' . substr($b->guardian_mobile, -2)
                    : null,
                'whatsapp_number'       => $b->whatsapp_number,
                'contact_privacy'       => $b->contact_privacy,
            ],
            'photos_count' => is_array($b->photos) ? count($b->photos) : 0,
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $biodata = Biodata::findOrFail($id);

        if ($biodata->status === 'approved') {
            return back()->with('info', __('admin.biodata_approved'));
        }

        $this->applyApprove($biodata);
        $this->notifyApproved($biodata);

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

        $this->applyReject($biodata, $request->input('note'));
        $this->notifyRejected($biodata, $request->input('note'));

        return back()->with('success', __('admin.biodata_rejected'));
    }

    public function hide(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $biodata = Biodata::findOrFail($id);

        if ($biodata->status === 'hidden') {
            return back()->with('info', __('admin.biodata_hidden'));
        }

        $biodata->update([
            'status'     => 'hidden',
            'admin_note' => $request->input('note') ?: $biodata->admin_note,
        ]);

        UserNotification::send(
            $biodata->registration_id,
            'biodata',
            __('notifications.biodata_hidden_title'),
            __('notifications.biodata_hidden_body'),
        );

        return back()->with('success', __('admin.biodata_hidden'));
    }

    public function unhide(int $id): RedirectResponse
    {
        $biodata = Biodata::findOrFail($id);

        if ($biodata->status !== 'hidden') {
            return back()->with('info', __('admin.biodata_unhidden'));
        }

        // Hidden profiles were previously visible → restore to approved.
        $biodata->update([
            'status'      => 'approved',
            'approved_at' => $biodata->approved_at ?? now(),
            'approved_by' => $biodata->approved_by ?? Auth::id(),
        ]);

        UserNotification::send(
            $biodata->registration_id,
            'biodata',
            __('notifications.biodata_unhidden_title'),
            __('notifications.biodata_unhidden_body'),
        );

        return back()->with('success', __('admin.biodata_unhidden'));
    }

    /**
     * Bulk approve / reject / hide / unhide on a set of biodata ids.
     * In-app notifications only (no per-row emails) to avoid mail flooding.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,hide,unhide',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer',
            'note'   => 'required_if:action,reject|nullable|string|min:5|max:500',
        ]);

        $action  = $validated['action'];
        $note    = $validated['note'] ?? '';
        $targets = Biodata::whereIn('id', $validated['ids'])->get();

        $count = 0;
        DB::transaction(function () use ($targets, $action, $note, &$count) {
            foreach ($targets as $b) {
                $changed = match ($action) {
                    'approve' => $b->status !== 'approved' ? tap(true, fn () => $this->applyApprove($b)) : false,
                    'reject'  => $b->status !== 'rejected' ? tap(true, fn () => $this->applyReject($b, $note)) : false,
                    'hide'    => $b->status !== 'hidden'   ? tap(true, fn () => $b->update(['status' => 'hidden'])) : false,
                    'unhide'  => $b->status === 'hidden'   ? tap(true, fn () => $b->update(['status' => 'approved', 'approved_at' => $b->approved_at ?? now(), 'approved_by' => $b->approved_by ?? Auth::id()])) : false,
                    default   => false,
                };

                if ($changed) {
                    $this->notifyBulk($b, $action, $note);
                    $count++;
                }
            }
        });

        return back()->with('success', __('admin.biodata_bulk_done', ['count' => $count]));
    }

    // ── Internals ────────────────────────────────────────────────────────────

    private function buildQuery(Request $request, string $tab): Builder
    {
        $query = Biodata::with([
            'registration:registration_id,name,email,gender,platform_mode,is_email_verified,is_mobile_verified,membership_status,membership_plan_name',
        ]);

        // Tab governs status unless on "all" (where an explicit status filter may apply).
        if ($tab !== 'all') {
            $query->where('status', $tab);
        } elseif ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('registration_id', 'like', "%{$search}%")
                  ->orWhereHas('registration', function (Builder $r) use ($search) {
                      $r->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('registration_id', 'like', "%{$search}%");
                  });
            });
        }

        if ($gender = $request->input('gender')) {
            $query->whereHas('registration', fn (Builder $r) => $r->where('gender', $gender));
        }

        if ($sect = $request->input('sect')) {
            $query->where('sect', $sect);
        }

        if (($min = $request->input('min_completeness')) !== null && $min !== '') {
            $query->where('completeness_score', '>=', (int) $min);
        }

        if ($from = $request->input('updated_from')) {
            $query->whereDate('updated_at', '>=', $from);
        }
        if ($to = $request->input('updated_to')) {
            $query->whereDate('updated_at', '<=', $to);
        }

        return match ($request->input('sort')) {
            'completeness' => $query->orderByDesc('completeness_score')->orderByDesc('updated_at'),
            'newest'       => $query->orderByDesc('created_at'),
            default        => $query->latest('updated_at'),
        };
    }

    private function filterKeys(): array
    {
        return ['search', 'gender', 'sect', 'status', 'min_completeness', 'updated_from', 'updated_to', 'sort'];
    }

    /** Distinct, non-empty sect values for the madhhab filter dropdown. */
    private function sectOptions(): array
    {
        return Biodata::query()
            ->whereNotNull('sect')
            ->where('sect', '!=', '')
            ->distinct()
            ->orderBy('sect')
            ->pluck('sect')
            ->values()
            ->all();
    }

    private function presentRow(Biodata $b): array
    {
        $reg = $b->registration;

        return [
            'id'                 => $b->id,
            'registration_id'    => $b->registration_id,
            'status'             => $b->status,
            'admin_note'         => $b->admin_note,
            'completeness_score' => $b->completeness_score,
            'district'           => $b->district,
            'division'           => $b->division,
            'sect'               => $b->sect,
            'photos_count'       => is_array($b->photos) ? count($b->photos) : 0,
            'updated_at'         => optional($b->updated_at)->toDateTimeString(),
            'registration'       => $reg ? [
                'registration_id'      => $reg->registration_id,
                'name'                 => $reg->name,
                'email'                => $reg->email,
                'gender'               => $reg->gender,
                'platform_mode'        => $reg->platform_mode,
                'is_email_verified'    => (bool) $reg->is_email_verified,
                'is_mobile_verified'   => (bool) $reg->is_mobile_verified,
                'membership_status'    => $reg->membership_status,
                'membership_plan_name' => $reg->membership_plan_name,
            ] : null,
        ];
    }

    private function applyApprove(Biodata $biodata): void
    {
        $biodata->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejected_at' => null,
            'rejected_by' => null,
            'admin_note'  => null,
        ]);
    }

    private function applyReject(Biodata $biodata, string $note): void
    {
        $biodata->update([
            'status'      => 'rejected',
            'admin_note'  => $note,
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    private function notifyApproved(Biodata $biodata): void
    {
        UserNotification::send(
            $biodata->registration_id,
            'biodata',
            __('notifications.biodata_approved_title'),
            __('notifications.biodata_approved_body'),
        );

        $member = $this->member($biodata->registration_id);
        if ($member) {
            $lang = $member->preferred_language ?? 'bn';
            $member->notify(new HeavenlyMatchNotification(
                subject: trans('notifications.email_subject_biodata', [], $lang),
                greeting: trans('notifications.email_greeting', ['name' => $member->name], $lang),
                introLines: [
                    trans('notifications.biodata_approved_title', [], $lang),
                    trans('notifications.biodata_approved_body', [], $lang),
                ],
                actionUrl: url('/dashboard'),
                actionText: trans('notifications.email_action_go_dashboard', [], $lang),
            ));
        }
    }

    private function notifyRejected(Biodata $biodata, string $note): void
    {
        UserNotification::send(
            $biodata->registration_id,
            'biodata',
            __('notifications.biodata_rejected_title'),
            __('notifications.biodata_rejected_body', ['reason' => $note]),
        );

        $member = $this->member($biodata->registration_id);
        if ($member) {
            $lang = $member->preferred_language ?? 'bn';
            $member->notify(new HeavenlyMatchNotification(
                subject: trans('notifications.email_subject_biodata', [], $lang),
                greeting: trans('notifications.email_greeting', ['name' => $member->name], $lang),
                introLines: [
                    trans('notifications.biodata_rejected_title', [], $lang),
                    trans('notifications.biodata_rejected_body', ['reason' => $note], $lang),
                ],
                actionUrl: url('/biodata/wizard'),
                actionText: trans('notifications.email_action_edit_biodata', [], $lang),
            ));
        }
    }

    /** In-app notification used for bulk actions (no email to avoid flooding). */
    private function notifyBulk(Biodata $biodata, string $action, string $note): void
    {
        [$title, $body] = match ($action) {
            'approve' => [__('notifications.biodata_approved_title'), __('notifications.biodata_approved_body')],
            'reject'  => [__('notifications.biodata_rejected_title'), __('notifications.biodata_rejected_body', ['reason' => $note])],
            'hide'    => [__('notifications.biodata_hidden_title'), __('notifications.biodata_hidden_body')],
            'unhide'  => [__('notifications.biodata_unhidden_title'), __('notifications.biodata_unhidden_body')],
            default   => [null, null],
        };

        if ($title) {
            UserNotification::send($biodata->registration_id, 'biodata', $title, $body);
        }
    }

    private function member(string $registrationId): ?Registration
    {
        return Registration::where('registration_id', $registrationId)
            ->select(['id', 'registration_id', 'name', 'email', 'preferred_language'])
            ->first();
    }
}
