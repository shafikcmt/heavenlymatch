<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConnectionRequest;
use App\Models\MembershipPlan;
use App\Models\Registration;
use App\Services\PhoneOtpService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    public function __construct(private readonly PhoneOtpService $phone)
    {
    }

    // ── Listing ────────────────────────────────────────────────────────────────

    public function index(Request $request): Response
    {
        $users = $this->buildQuery($request)
            ->with(['biodata:registration_id,status,is_completed'])
            ->select($this->listColumns())
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users'   => $users,
            'filters' => $request->only($this->filterKeys()),
            'plans'   => $this->plansList(),
            'authRegistrationId' => Auth::user()?->registration_id,
            'trashedCount' => Registration::onlyTrashed()->count(),
        ]);
    }

    // ── Create ─────────────────────────────────────────────────────────────────

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'plans' => $this->plansList(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateUser($request, isCreate: true);

        $reg = new Registration([
            'name'                => $data['name'],
            'email'               => $data['email'],
            'gender'              => $data['gender'],
            'looking_for'         => $data['gender'] === 'male' ? 'bride' : 'groom',
            'profile_created_for' => $data['profile_created_for'] ?? 'self',
            'platform_mode'       => 'general',
            'photo_visibility'    => 'members_only',
            'country_code'        => '+880',
            'mobile_number'       => $data['mobile_number'],   // already normalized
            'terms_accepted_at'   => now(),
        ]);
        $reg->password = Hash::make($data['password']);
        $reg->save();

        // Verification flags (admin-set, no OTP).
        $reg->markEmailVerified(! empty($data['email_verified']));
        if ($reg->mobile_number) {
            $reg->markPhoneVerified(! empty($data['phone_verified']));
        }

        $this->applyStatus($reg, $data['status'] ?? 'active');
        $this->applyPlan($reg, $data['plan'] ?? 'free');

        if (! empty($data['send_welcome'])) {
            $this->safeMail($reg->email, 'Welcome to HeavenlyMatch',
                "Assalamu Alaikum {$reg->name},\n\nYour HeavenlyMatch account has been created. Your member ID is {$reg->registration_id}.\n\nYou can sign in with your email and the password provided to you.");
        }

        return redirect()->route('admin.users.show', $reg->registration_id)
            ->with('success', __('admin.user_created'));
    }

    // ── Detail ─────────────────────────────────────────────────────────────────

    public function show(string $id): Response
    {
        $user = $this->findUser($id, withTrashed: true)->load('biodata');

        $payments = $user->payments()
            ->latest()
            ->take(10)
            ->get(['id', 'transaction_no', 'plan_name', 'amount', 'status', 'created_at', 'external_transaction_id']);

        return Inertia::render('Admin/Users/Show', [
            'user'     => $this->presentUser($user),
            'payments' => $payments,
            'stats'    => [
                'interests_sent'     => ConnectionRequest::where('sender_id', $user->registration_id)->count(),
                'interests_received' => ConnectionRequest::where('receiver_id', $user->registration_id)->count(),
            ],
            'authRegistrationId' => Auth::user()?->registration_id,
        ]);
    }

    // ── Edit ───────────────────────────────────────────────────────────────────

    public function edit(string $id): Response
    {
        $user = $this->findUser($id);

        return Inertia::render('Admin/Users/Edit', [
            'user'  => $this->presentUser($user),
            'plans' => $this->plansList(),
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $user = $this->findUser($id);
        $data = $this->validateUser($request, isCreate: false, ignoreId: $user->id);

        $user->fill([
            'name'                => $data['name'],
            'email'               => $data['email'],
            'gender'              => $data['gender'],
            'looking_for'         => $data['gender'] === 'male' ? 'bride' : 'groom',
            'profile_created_for' => $data['profile_created_for'] ?? $user->profile_created_for,
            'mobile_number'       => $data['mobile_number'],
        ]);
        $user->save();

        if (! empty($data['password'])) {
            $user->forceFill(['password' => Hash::make($data['password'])])->save();
        }

        // Verified flags follow the admin's explicit checkboxes (a changed email/phone
        // simply won't be ticked unless the admin re-confirms it).
        $user->markEmailVerified(! empty($data['email_verified']));
        $user->markPhoneVerified(! empty($user->mobile_number) && ! empty($data['phone_verified']));

        $this->applyStatus($user, $data['status'] ?? $user->account_status);
        $this->applyPlan($user, $data['plan'] ?? 'free');

        return redirect()->route('admin.users.show', $user->registration_id)
            ->with('success', __('admin.user_updated'));
    }

    // ── Soft delete / restore / permanent delete ────────────────────────────────

    public function destroy(string $id): RedirectResponse
    {
        $user = $this->findUser($id);
        $this->assertActionable($user);

        $user->delete(); // soft delete

        return redirect()->route('admin.users.index')->with('success', __('admin.user_deleted'));
    }

    public function restore(string $id): RedirectResponse
    {
        $this->findUser($id, withTrashed: true)->restore();

        return back()->with('success', __('admin.user_restored'));
    }

    public function forceDelete(string $id): RedirectResponse
    {
        $user = $this->findUser($id, withTrashed: true);
        $this->assertActionable($user);

        $user->forceDelete();

        return redirect()->route('admin.users.index', ['trashed' => 1])
            ->with('success', __('admin.user_force_deleted'));
    }

    // ── Bulk actions ─────────────────────────────────────────────────────────────

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,activate,suspend,verify_email,verify_phone,change_plan,restore,force_delete',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'string',
            'plan'   => 'nullable|string',
        ]);

        $action = $validated['action'];
        $destructive = in_array($action, ['delete', 'suspend', 'force_delete'], true);

        $query = Registration::query();
        if (in_array($action, ['restore', 'force_delete'], true)) {
            $query->withTrashed();
        }
        $targets = $query->whereIn('registration_id', $validated['ids'])->get();

        $count = 0;
        DB::transaction(function () use ($targets, $action, $destructive, $validated, &$count) {
            foreach ($targets as $user) {
                // Never let an admin hit themselves or another admin with destructive actions.
                if ($destructive && ($this->isSelf($user) || $user->isProtectedAccount())) {
                    continue;
                }

                match ($action) {
                    'delete'       => $user->delete(),
                    'restore'      => $user->restore(),
                    'force_delete' => $user->forceDelete(),
                    'activate'     => $user->activate(),
                    'suspend'      => $user->suspend(__('admin.bulk_suspend_reason')),
                    'verify_email' => $user->markEmailVerified(true),
                    'verify_phone' => $user->markPhoneVerified(true),
                    'change_plan'  => $this->applyPlan($user, $validated['plan'] ?? 'free'),
                    default        => null,
                };
                $count++;
            }
        });

        return back()->with('success', __('admin.bulk_done', ['count' => $count]));
    }

    // ── Reset password ───────────────────────────────────────────────────────────

    public function resetPassword(Request $request, string $id): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $user = $this->findUser($id);

        $request->validate([
            'mode'     => 'required|in:manual,generate',
            'password' => 'required_if:mode,manual|nullable|string|min:8|confirmed',
            'notify'   => 'nullable|boolean',
        ]);

        $newPassword = $request->input('mode') === 'generate'
            ? Str::password(12)
            : (string) $request->input('password');

        $user->forceFill(['password' => Hash::make($newPassword)])->save();

        if ($request->boolean('notify')) {
            $this->safeMail($user->email, 'Your HeavenlyMatch password was reset',
                "Assalamu Alaikum {$user->name},\n\nAn administrator has reset your password. Your new password is:\n\n{$newPassword}\n\nPlease sign in and change it from your settings.");
        }

        // Generated passwords are shown once to the admin (never logged).
        $shown = $request->input('mode') === 'generate' ? $newPassword : null;

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => __('admin.password_reset'), 'password' => $shown]);
        }

        return back()->with('success', __('admin.password_reset'));
    }

    // ── CSV import ───────────────────────────────────────────────────────────────

    public function importForm(): Response
    {
        return Inertia::render('Admin/Users/Import', [
            'plans' => $this->plansList(),
        ]);
    }

    public function importPreview(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);

        $rows = $this->parseCsv($request->file('file')->getRealPath());

        $preview = [];
        $valid = 0;
        $seenEmails = [];
        foreach ($rows as $i => $row) {
            [$clean, $errors] = $this->validateImportRow($row, $seenEmails);
            if (! $errors) {
                $valid++;
                $seenEmails[] = mb_strtolower($clean['email']);
            }
            // Never echo back the plain password.
            unset($clean['password']);
            $preview[] = ['line' => $i + 2, 'data' => $clean, 'errors' => $errors, 'valid' => empty($errors)];
        }

        return response()->json([
            'rows'    => $preview,
            'total'   => count($preview),
            'valid'   => $valid,
            'invalid' => count($preview) - $valid,
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file'       => 'required|file|mimes:csv,txt|max:2048',
            'valid_only' => 'nullable|boolean',
        ]);

        $rows = $this->parseCsv($request->file('file')->getRealPath());

        $parsed = [];
        $seenEmails = [];
        $invalid = 0;
        foreach ($rows as $row) {
            [$clean, $errors] = $this->validateImportRow($row, $seenEmails);
            if ($errors) {
                $invalid++;
            } else {
                $seenEmails[] = mb_strtolower($clean['email']);
            }
            $parsed[] = compact('clean', 'errors');
        }

        if ($invalid > 0 && ! $request->boolean('valid_only')) {
            return back()->with('error', __('admin.import_has_errors', ['count' => $invalid]));
        }

        $created = 0;
        DB::transaction(function () use ($parsed, &$created) {
            foreach ($parsed as $p) {
                if ($p['errors']) {
                    continue;
                }
                $d = $p['clean'];
                $reg = new Registration([
                    'name'                => $d['name'],
                    'email'               => $d['email'],
                    'gender'              => $d['gender'],
                    'looking_for'         => $d['gender'] === 'male' ? 'bride' : 'groom',
                    'profile_created_for' => 'self',
                    'platform_mode'       => 'general',
                    'photo_visibility'    => 'members_only',
                    'country_code'        => '+880',
                    'mobile_number'       => $d['mobile_number'],
                    'terms_accepted_at'   => now(),
                ]);
                $reg->password = Hash::make($d['password']);
                $reg->save();

                $reg->markEmailVerified($d['email_verified']);
                if ($reg->mobile_number) {
                    $reg->markPhoneVerified($d['phone_verified']);
                }
                $this->applyStatus($reg, $d['status']);
                $this->applyPlan($reg, $d['plan']);
                $created++;
            }
        });

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.import_done', ['count' => $created]));
    }

    // ── Export ───────────────────────────────────────────────────────────────────

    public function export(Request $request): StreamedResponse
    {
        $filename = 'users-' . now()->format('Ymd-His') . '.csv';

        $headers = ['name', 'email', 'phone', 'member_id', 'gender', 'status', 'plan',
            'email_verified', 'phone_verified', 'biodata_status', 'joined_at'];

        return response()->streamDownload(function () use ($request, $headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);

            $this->buildQuery($request)
                ->with('biodata:registration_id,status')
                ->select($this->listColumns())
                ->chunk(500, function ($chunk) use ($out) {
                    foreach ($chunk as $u) {
                        fputcsv($out, [
                            $u->name,
                            $u->email,
                            $u->mobile_number,
                            $u->registration_id,
                            $u->gender,
                            $u->account_status,
                            $u->membership_status === 'active' ? ($u->membership_plan_name ?: 'premium') : 'free',
                            $u->is_email_verified ? 'yes' : 'no',
                            $u->is_mobile_verified ? 'yes' : 'no',
                            $u->biodata?->status ?? 'none',
                            optional($u->created_at)->toDateTimeString(),
                        ]);
                    }
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ── Legacy quick actions (kept for backward compatibility) ────────────────────

    public function ban(Request $request, string $id): RedirectResponse
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $user = $this->findUser($id);
        $this->assertActionable($user);
        $user->ban($request->input('reason', ''));

        return back()->with('success', __('admin.user_banned'));
    }

    public function unban(string $id): RedirectResponse
    {
        $this->findUser($id)->activate();

        return back()->with('success', __('admin.user_unbanned'));
    }

    public function suspend(Request $request, string $id): RedirectResponse
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $user = $this->findUser($id);
        $this->assertActionable($user);
        $user->suspend($request->input('reason', ''));

        return back()->with('success', __('admin.user_suspended'));
    }

    public function activate(string $id): RedirectResponse
    {
        $this->findUser($id)->activate();

        return back()->with('success', __('admin.user_activated'));
    }

    public function verify(string $id): RedirectResponse
    {
        $this->findUser($id)->forceFill([
            'identity_verification_status' => 'verified',
            'identity_verified_at'         => now(),
            'identity_verified_by'         => Auth::id(),
        ])->save();

        return back()->with('success', __('admin.user_verified'));
    }

    // ── Internals ────────────────────────────────────────────────────────────────

    /** Shared filtered query used by index() and export(). */
    private function buildQuery(Request $request): Builder
    {
        $query = $request->boolean('trashed')
            ? Registration::onlyTrashed()
            : Registration::query();

        $query->latest();

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

        if (($ev = $request->input('email_verified')) !== null && $ev !== '') {
            $query->where('is_email_verified', (bool) (int) $ev);
        }

        if (($pv = $request->input('phone_verified')) !== null && $pv !== '') {
            $query->where('is_mobile_verified', (bool) (int) $pv);
        }

        if ($bio = $request->input('biodata_status')) {
            if ($bio === 'none') {
                $query->whereDoesntHave('biodata');
            } else {
                $query->whereHas('biodata', fn ($q) => $q->where('status', $bio));
            }
        }

        if ($from = $request->input('joined_from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('joined_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    private function filterKeys(): array
    {
        return ['search', 'status', 'gender', 'membership', 'email_verified',
            'phone_verified', 'biodata_status', 'joined_from', 'joined_to', 'trashed'];
    }

    /** Explicit, non-sensitive columns sent to the client (no password/tokens/secrets). */
    private function listColumns(): array
    {
        return ['id', 'registration_id', 'name', 'email', 'mobile_number', 'gender',
            'account_status', 'membership_status', 'membership_plan_name',
            'is_email_verified', 'is_mobile_verified', 'is_admin', 'role',
            'last_login_at', 'created_at', 'deleted_at'];
    }

    private function presentUser(Registration $u): array
    {
        return [
            'id'                  => $u->id,
            'registration_id'     => $u->registration_id,
            'name'                => $u->name,
            'email'               => $u->email,
            'mobile_number'       => $u->mobile_number,
            'gender'              => $u->gender,
            'profile_created_for' => $u->profile_created_for,
            'account_status'      => $u->account_status,
            'membership_status'   => $u->membership_status,
            'membership_plan_id'  => $u->membership_plan_id,
            'membership_plan_name'=> $u->membership_plan_name,
            'is_email_verified'   => (bool) $u->is_email_verified,
            'is_mobile_verified'  => (bool) $u->is_mobile_verified,
            'is_admin'            => (bool) $u->is_admin,
            'role'                => $u->role,
            'identity_verification_status' => $u->identity_verification_status,
            'last_login_at'       => optional($u->last_login_at)->toDateTimeString(),
            'created_at'          => optional($u->created_at)->toDateTimeString(),
            'deleted_at'          => optional($u->deleted_at)->toDateTimeString(),
            'biodata'             => $u->biodata ? [
                'id'           => $u->biodata->id,
                'status'       => $u->biodata->status,
                'is_completed' => (bool) $u->biodata->is_completed,
            ] : null,
        ];
    }

    private function plansList(): array
    {
        return MembershipPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name'])
            ->toArray();
    }

    /**
     * Shared validation for create + update.
     */
    private function validateUser(Request $request, bool $isCreate, ?int $ignoreId = null): array
    {
        $rules = [
            'name'                => 'required|string|max:100',
            'email'               => ['required', 'email', 'max:180',
                Rule::unique('registrations', 'email')->ignore($ignoreId)],
            'gender'              => 'required|in:male,female',
            'profile_created_for' => 'nullable|in:self,son,daughter,brother,sister,relative',
            'mobile_number'       => 'nullable|string|max:20',
            'status'              => 'nullable|in:active,inactive,suspended,banned',
            'plan'                => 'nullable|string',
            'email_verified'      => 'nullable|boolean',
            'phone_verified'      => 'nullable|boolean',
            'send_welcome'        => 'nullable|boolean',
            'password'            => $isCreate ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed',
        ];

        $data = $request->validate($rules, ['email.unique' => __('auth.email_taken')]);

        // Normalize + uniqueness-check the phone (when provided).
        if (! empty($data['mobile_number'])) {
            $normalized = $this->phone->normalizePhone($data['mobile_number']);
            if ($normalized === null) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'mobile_number' => __('auth.otp_invalid_phone'),
                ]);
            }
            $dupe = Registration::withTrashed()
                ->where('mobile_number', $normalized)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists();
            if ($dupe) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'mobile_number' => __('auth.otp_phone_taken'),
                ]);
            }
            $data['mobile_number'] = $normalized;
        } else {
            $data['mobile_number'] = null;
        }

        return $data;
    }

    private function applyStatus(Registration $user, string $status): void
    {
        match ($status) {
            'active'    => $user->activate(),
            'suspended' => $user->suspend(),
            'banned'    => $user->ban(),
            default     => $user->forceFill(['account_status' => 'inactive'])->save(),
        };
    }

    private function applyPlan(Registration $user, string $plan): void
    {
        if ($plan === 'free' || $plan === '') {
            $user->setMembership('free');

            return;
        }

        $model = MembershipPlan::find($plan);
        $user->setMembership($model ?: 'free');
    }

    /** Parse a CSV file into associative rows keyed by the header line. */
    private function parseCsv(string $path): array
    {
        $rows = [];
        if (! is_readable($path) || ($handle = fopen($path, 'r')) === false) {
            return $rows;
        }

        $header = null;
        while (($line = fgetcsv($handle)) !== false) {
            if ($line === [null] || $line === false) {
                continue;
            }
            if ($header === null) {
                $header = array_map(fn ($h) => strtolower(trim((string) $h)), $line);
                continue;
            }
            // Pad/truncate to header length.
            $line = array_pad(array_slice($line, 0, count($header)), count($header), '');
            $rows[] = array_combine($header, array_map(fn ($v) => trim((string) $v), $line));
        }
        fclose($handle);

        return $rows;
    }

    /**
     * Validate a single CSV row. Returns [cleanData, errors[]].
     * Expected columns: name,email,phone,gender,password,status,plan,email_verified,phone_verified
     */
    private function validateImportRow(array $row, array $seenEmails): array
    {
        $errors = [];

        $name   = trim((string) ($row['name'] ?? ''));
        $email  = mb_strtolower(trim((string) ($row['email'] ?? '')));
        $phone  = trim((string) ($row['phone'] ?? ''));
        $gender = strtolower(trim((string) ($row['gender'] ?? '')));
        $pass   = (string) ($row['password'] ?? '');
        $status = strtolower(trim((string) ($row['status'] ?? 'active'))) ?: 'active';
        $plan   = trim((string) ($row['plan'] ?? 'free')) ?: 'free';

        if ($name === '') {
            $errors[] = __('admin.import_err_name');
        }
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = __('admin.import_err_email');
        } elseif (in_array($email, $seenEmails, true) || Registration::withTrashed()->where('email', $email)->exists()) {
            $errors[] = __('admin.import_err_email_dupe');
        }
        if (! in_array($gender, ['male', 'female'], true)) {
            $errors[] = __('admin.import_err_gender');
        }
        if (strlen($pass) < 8) {
            $errors[] = __('admin.import_err_password');
        }

        $normalizedPhone = null;
        if ($phone !== '') {
            $normalizedPhone = $this->phone->normalizePhone($phone);
            if ($normalizedPhone === null) {
                $errors[] = __('admin.import_err_phone');
            } elseif (Registration::withTrashed()->where('mobile_number', $normalizedPhone)->exists()) {
                $errors[] = __('admin.import_err_phone_dupe');
            }
        }

        if (! in_array($status, ['active', 'inactive', 'suspended', 'banned'], true)) {
            $status = 'active';
        }

        $clean = [
            'name'           => $name,
            'email'          => $email,
            'mobile_number'  => $normalizedPhone,
            'gender'         => in_array($gender, ['male', 'female'], true) ? $gender : '',
            'password'       => $pass,
            'status'         => $status,
            'plan'           => $plan,
            'email_verified' => $this->truthy($row['email_verified'] ?? ''),
            'phone_verified' => $this->truthy($row['phone_verified'] ?? ''),
        ];

        return [$clean, $errors];
    }

    private function truthy($value): bool
    {
        return in_array(strtolower(trim((string) $value)), ['1', 'yes', 'true', 'y'], true);
    }

    private function safeMail(string $to, string $subject, string $body): void
    {
        try {
            Mail::raw($body, function ($m) use ($to, $subject) {
                $m->to($to)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::warning('Admin user mail failed: ' . $e->getMessage());
        }
    }

    private function isSelf(Registration $user): bool
    {
        return (int) $user->id === (int) Auth::id();
    }

    /** Block destructive actions against self or another admin. */
    private function assertActionable(Registration $user): void
    {
        if ($this->isSelf($user)) {
            abort(403, __('admin.cannot_action_self'));
        }
        if ($user->isProtectedAccount()) {
            abort(403, __('admin.cannot_action_admin'));
        }
    }

    /**
     * Find a Registration by registration_id ("HM000001") or integer PK.
     */
    private function findUser(string $id, bool $withTrashed = false): Registration
    {
        $query = $withTrashed ? Registration::withTrashed() : Registration::query();

        return $query->where('registration_id', $id)
            ->orWhere('id', is_numeric($id) ? (int) $id : -1)
            ->firstOrFail();
    }
}
