<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\PaymentTransaction;
use App\Models\Registration;
use App\Models\UserAttribute;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminFeatureController extends Controller
{
    public function payments(Request $request, string $scope = 'all')
    {
        if (Schema::hasTable('payment_transactions')) {
            $query = PaymentTransaction::with(['registration', 'plan', 'gateway'])->latest('id');

            match ($scope) {
                'pending' => $query->whereIn('status', ['pending', 'submitted']),
                'approved', 'successful' => $query->where('status', 'paid'),
                'rejected' => $query->whereIn('status', ['failed', 'cancelled', 'refunded']),
                'initiated' => $query->where('status', 'pending'),
                default => null,
            };

            if ($request->filled('q')) {
                $q = trim((string) $request->input('q'));
                $query->where(function ($builder) use ($q) {
                    $builder->where('transaction_no', 'like', "%{$q}%")
                        ->orWhere('external_transaction_id', 'like', "%{$q}%")
                        ->orWhere('plan_name', 'like', "%{$q}%")
                        ->orWhere('gateway_name', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhere('customer_email', 'like', "%{$q}%")
                        ->orWhere('customer_phone', 'like', "%{$q}%")
                        ->orWhere('registration_code', 'like', "%{$q}%");
                });
            }

            $payments = $query->paginate(15)->withQueryString();

            $counts = [
                'all' => PaymentTransaction::count(),
                'pending' => PaymentTransaction::whereIn('status', ['pending', 'submitted'])->count(),
                'approved' => PaymentTransaction::where('status', 'paid')->count(),
                'successful' => PaymentTransaction::where('status', 'paid')->count(),
                'rejected' => PaymentTransaction::whereIn('status', ['failed', 'cancelled', 'refunded'])->count(),
                'initiated' => PaymentTransaction::where('status', 'pending')->count(),
            ];
            $totalAmount = (float) PaymentTransaction::where('status', 'paid')->sum('amount');
        } else {
            $payments = new LengthAwarePaginator([], 0, 15);
            $counts = ['all' => 0, 'pending' => 0, 'approved' => 0, 'successful' => 0, 'rejected' => 0, 'initiated' => 0];
            $totalAmount = 0;
        }

        $scopeTitles = [
            'all' => 'All Payments',
            'pending' => 'Pending Payments',
            'approved' => 'Approved Payments',
            'successful' => 'Successful Payments',
            'rejected' => 'Rejected Payments',
            'initiated' => 'Initiated Payments',
        ];

        return view('admin.payments.index', [
            'payments' => $payments,
            'scope' => $scope,
            'scopeTitle' => $scopeTitles[$scope] ?? 'Payments',
            'counts' => $counts,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function attributes(string $type)
    {
        abort_unless(UserAttribute::isValidType($type), 404);

        $meta = UserAttribute::meta($type);
        $attributes = UserAttribute::itemsFor($type);

        return view('admin.attributes.index', [
            'type' => $type,
            'title' => $meta['title'],
            'singular' => $meta['singular'],
            'columnLabel' => $meta['column'],
            'attributes' => $attributes,
            'canManage' => Schema::hasTable('user_attributes'),
        ]);
    }

    public function storeAttribute(Request $request, string $type)
    {
        abort_unless(UserAttribute::isValidType($type), 404);

        if (! Schema::hasTable('user_attributes')) {
            return back()->with('error', 'Please run migrations first so user attributes can be saved.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('user_attributes')->where(fn ($query) => $query->where('type', $type)),
            ],
            'sort_order' => 'nullable|integer|min:0|max:999999',
            'is_active' => 'nullable|boolean',
        ]);

        UserAttribute::create([
            'type' => $type,
            'name' => trim($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', UserAttribute::meta($type)['singular'] . ' added successfully.');
    }

    public function updateAttribute(Request $request, string $type, UserAttribute $attribute)
    {
        abort_unless(UserAttribute::isValidType($type) && $attribute->type === $type, 404);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('user_attributes')
                    ->where(fn ($query) => $query->where('type', $type))
                    ->ignore($attribute->id),
            ],
            'sort_order' => 'nullable|integer|min:0|max:999999',
            'is_active' => 'nullable|boolean',
        ]);

        $attribute->update([
            'name' => trim($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', UserAttribute::meta($type)['singular'] . ' updated successfully.');
    }

    public function destroyAttribute(string $type, UserAttribute $attribute)
    {
        abort_unless(UserAttribute::isValidType($type) && $attribute->type === $type, 404);

        $attribute->delete();

        return back()->with('success', UserAttribute::meta($type)['singular'] . ' deleted successfully.');
    }

    public function interactions(string $type)
    {
        $maps = [
            'interests' => [
                'title' => 'Interests',
                'description' => 'This section is ready for user-interest records. Use it as the reference-style admin page.',
                'items' => [
                    ['name' => 'Sent Today', 'value' => 0],
                    ['name' => 'Sent This Week', 'value' => 0],
                    ['name' => 'Accepted', 'value' => 0],
                    ['name' => 'Pending', 'value' => 0],
                ],
            ],
            'ignored-profile' => [
                'title' => 'Ignored Profiles',
                'description' => 'Track ignored profiles and moderation signals here.',
                'items' => [
                    ['name' => 'Ignored Today', 'value' => 0],
                    ['name' => 'Ignored This Week', 'value' => 0],
                    ['name' => 'Recovered', 'value' => 0],
                    ['name' => 'Still Ignored', 'value' => 0],
                ],
            ],
            'reports' => [
                'title' => 'User Reports',
                'description' => 'Moderation report dashboard placeholder in the same visual style as your reference.',
                'items' => [
                    ['name' => 'Open Reports', 'value' => 0],
                    ['name' => 'Resolved Reports', 'value' => 0],
                    ['name' => 'Spam Cases', 'value' => 0],
                    ['name' => 'Critical Flags', 'value' => 0],
                ],
            ],
        ];

        abort_unless(isset($maps[$type]), 404);

        return view('admin.feature.grid', [
            'title' => $maps[$type]['title'],
            'description' => $maps[$type]['description'],
            'items' => $maps[$type]['items'],
            'context' => 'interaction',
        ]);
    }

    public function tickets(string $scope = 'all')
    {
        $allTickets = collect([
            ['ticket_no' => 'TKT-1001', 'subject' => 'Unable to upload biodata photo', 'priority' => 'High', 'status' => 'pending', 'updated_at' => now()->subHours(2)],
            ['ticket_no' => 'TKT-1002', 'subject' => 'Manual payment still pending', 'priority' => 'High', 'status' => 'answered', 'updated_at' => now()->subHours(8)],
            ['ticket_no' => 'TKT-1003', 'subject' => 'Need password reset help', 'priority' => 'Normal', 'status' => 'closed', 'updated_at' => now()->subDay()],
            ['ticket_no' => 'TKT-1004', 'subject' => 'Mobile verification not working', 'priority' => 'Urgent', 'status' => 'pending', 'updated_at' => now()->subDays(2)],
        ]);

        $tickets = match ($scope) {
            'pending' => $allTickets->where('status', 'pending')->values(),
            'closed' => $allTickets->where('status', 'closed')->values(),
            'answered' => $allTickets->where('status', 'answered')->values(),
            default => $allTickets,
        };

        return view('admin.feature.table', [
            'title' => match ($scope) {
                'pending' => 'Pending Ticket',
                'closed' => 'Closed Ticket',
                'answered' => 'Answered Ticket',
                default => 'All Ticket',
            },
            'description' => 'This module follows the same left-menu and card style as your reference dashboard. Connect your real support ticket table later if needed.',
            'columns' => ['Ticket No', 'Subject', 'Priority', 'Status', 'Updated'],
            'rows' => $tickets->map(fn ($ticket) => [
                $ticket['ticket_no'],
                $ticket['subject'],
                $ticket['priority'],
                ucfirst($ticket['status']),
                $ticket['updated_at']->format('d M Y h:i A'),
            ]),
        ]);
    }

    public function reports(string $type)
    {
        $rows = collect();
        $columns = [];
        $description = 'Report-style pages to match the reference layout.';
        $title = 'Report';

        if ($type === 'login-history') {
            $title = 'Login History';
            $columns = ['User', 'Email', 'Last Login', 'Status'];
            $rows = Registration::latest('last_login_at')->take(12)->get()->map(function ($user) {
                return [
                    $user->name,
                    $user->email,
                    optional($user->last_login_at)->format('d M Y h:i A') ?: 'Never',
                    ($user->account_status ?? 'active') === 'blocked' ? 'Blocked' : 'Active',
                ];
            });
        } elseif ($type === 'notification-history') {
            $title = 'Notification History';
            $columns = ['Channel', 'Audience', 'Title', 'Sent At'];
            $rows = collect([
                ['Email', 'All Users', 'Weekly membership offers', now()->subDay()->format('d M Y h:i A')],
                ['Push', 'Premium Members', 'Diamond benefits reminder', now()->subDays(2)->format('d M Y h:i A')],
                ['SMS', 'Pending Payments', 'Complete your payment', now()->subDays(4)->format('d M Y h:i A')],
            ]);
        } elseif ($type === 'purchase-history') {
            $title = 'Purchase History';
            $columns = ['Transaction', 'User', 'Plan', 'Amount', 'Status'];
            $rows = Schema::hasTable('payment_transactions')
                ? PaymentTransaction::latest('id')->take(15)->get()->map(fn ($payment) => [
                    $payment->transaction_no,
                    $payment->customer_name ?: ($payment->registration?->name ?? '-'),
                    $payment->plan_name,
                    $payment->formatted_amount,
                    ucfirst($payment->status),
                ])
                : collect();
        }

        return view('admin.feature.table', compact('title', 'description', 'columns', 'rows'));
    }

    public function extra(string $type)
    {
        $title = 'Extra';
        $description = 'Extra tools area styled like the reference dashboard.';
        $items = [];

        if ($type === 'application') {
            $title = 'Application';
            $items = [
                ['name' => 'App Name', 'value' => config('app.name', 'HeavenlyMatch')],
                ['name' => 'Environment', 'value' => app()->environment()],
                ['name' => 'Debug Mode', 'value' => config('app.debug') ? 'Enabled' : 'Disabled'],
                ['name' => 'URL', 'value' => config('app.url')],
            ];
        } elseif ($type === 'server') {
            $title = 'Server';
            $items = [
                ['name' => 'PHP Version', 'value' => PHP_VERSION],
                ['name' => 'Laravel Version', 'value' => app()->version()],
                ['name' => 'Timezone', 'value' => config('app.timezone')],
                ['name' => 'Locale', 'value' => config('app.locale')],
            ];
        } elseif ($type === 'cache') {
            $title = 'Cache';
            $items = [
                ['name' => 'Config Cache', 'value' => 'Ready'],
                ['name' => 'Route Cache', 'value' => 'Ready'],
                ['name' => 'View Cache', 'value' => 'Ready'],
                ['name' => 'Optimization', 'value' => 'Run php artisan optimize:clear anytime to refresh'],
            ];
        } elseif ($type === 'update') {
            $title = 'Update';
            $items = [
                ['name' => 'Current Build', 'value' => 'Admin UI reference upgrade'],
                ['name' => 'Responsive Layout', 'value' => 'Enabled'],
                ['name' => 'Payment Setup', 'value' => 'Dynamic'],
                ['name' => 'Next Step', 'value' => 'Connect remaining modules with your own tables if needed'],
            ];
        } else {
            abort(404);
        }

        return view('admin.feature.grid', compact('title', 'description', 'items'));
    }

    public function notifications()
    {
        return view('admin.feature.table', [
            'title' => 'Send Notification',
            'description' => 'Starter page for the reference-style notification module.',
            'columns' => ['Channel', 'Target', 'Message', 'Status'],
            'rows' => collect([
                ['Email', 'All users', 'Membership plan price updated', 'Draft'],
                ['SMS', 'Pending payments', 'Please complete your payment', 'Queued'],
                ['Push', 'Premium users', 'Enjoy your premium support benefits', 'Sent'],
            ]),
        ]);
    }

    private function countByField(string $field, string $value): int
    {
        if (! Schema::hasTable('biodatas') || ! Schema::hasColumn('biodatas', $field)) {
            return 0;
        }

        return Biodata::where($field, $value)->count();
    }
}
