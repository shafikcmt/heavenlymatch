@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <section class="hm-admin-page-head">
        <h1>Dashboard</h1>
    </section>

    <section class="hm-admin-stats-grid hm-admin-stats-grid-primary">
        @foreach([
            ['label' => 'Total Users', 'value' => number_format($stats['users']), 'icon' => '👥', 'tone' => 'indigo'],
            ['label' => 'Active Users', 'value' => number_format($stats['active_users']), 'icon' => '🟢', 'tone' => 'green'],
            ['label' => 'Email Unverified Users', 'value' => number_format($stats['email_unverified']), 'icon' => '✉', 'tone' => 'red'],
            ['label' => 'Mobile Unverified Users', 'value' => number_format($stats['mobile_unverified']), 'icon' => '📵', 'tone' => 'orange'],
        ] as $item)
            <article class="hm-admin-info-card hm-admin-info-card-{{ $item['tone'] }}">
                <div class="hm-admin-info-card-icon">{{ $item['icon'] }}</div>
                <div class="hm-admin-info-card-body">
                    <div class="hm-admin-info-card-label">{{ $item['label'] }}</div>
                    <div class="hm-admin-info-card-value">{{ $item['value'] }}</div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="hm-admin-stats-grid hm-admin-stats-grid-secondary">
        @foreach([
            ['label' => 'Total Payment', 'value' => '$' . number_format($stats['total_payment'], 2), 'icon' => '💲', 'tone' => 'green'],
            ['label' => 'Pending Payments', 'value' => '$' . number_format($stats['pending_payment'], 2), 'icon' => '◌', 'tone' => 'amber'],
            ['label' => 'Rejected Payments', 'value' => '$' . number_format($stats['rejected_payment'], 2), 'icon' => '⊘', 'tone' => 'red'],
            ['label' => 'Payment Charge', 'value' => '$' . number_format($stats['payment_charge'], 2), 'icon' => '%', 'tone' => 'indigo'],
            ['label' => 'Purchased Package', 'value' => '$' . number_format($stats['purchased_package'], 2), 'icon' => '◫', 'tone' => 'green-outline'],
            ['label' => 'Total Interests', 'value' => number_format($stats['total_interests']), 'icon' => '❤', 'tone' => 'pink-outline'],
            ['label' => 'Ignored Profiles', 'value' => number_format($stats['ignored_profiles']), 'icon' => '💔', 'tone' => 'orange-outline'],
            ['label' => 'Reports', 'value' => number_format($stats['reports']), 'icon' => '⚠', 'tone' => 'navy-outline'],
        ] as $item)
            <article class="hm-admin-metric-card hm-admin-metric-{{ $item['tone'] }}">
                <div class="hm-admin-metric-icon">{{ $item['icon'] }}</div>
                <div>
                    <div class="hm-admin-metric-value">{{ $item['value'] }}</div>
                    <div class="hm-admin-metric-label">{{ $item['label'] }}</div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="hm-admin-chart-grid">
        @foreach($charts as $chart)
            @php
                $gradient = collect($chart['segments'])->map(function ($segment) {
                    return $segment['color'] . ' ' . $segment['start'] . '% ' . $segment['end'] . '%';
                })->implode(', ');
            @endphp
            <article class="hm-admin-panel-card hm-admin-chart-card">
                <div class="hm-admin-panel-title">{{ $chart['title'] }}</div>
                <div class="hm-admin-donut-wrap">
                    <div class="hm-admin-donut" style="background: conic-gradient({{ $gradient }});"></div>
                </div>
                <div class="hm-admin-chart-legend">
                    @foreach($chart['segments'] as $segment)
                        <div class="hm-admin-chart-legend-item">
                            <span class="hm-admin-chart-dot" style="background:{{ $segment['color'] }}"></span>
                            <span>{{ $segment['label'] }}</span>
                            <strong>{{ $segment['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </article>
        @endforeach
    </section>

    <section class="hm-admin-grid hm-admin-two-col" style="margin-top:20px">
        <article class="hm-admin-card hm-admin-panel">
            <div class="hm-admin-section-head">
                <div>
                    <h2 class="hm-admin-section-title" style="margin-bottom:4px">Recent Users</h2>
                    <p class="hm-admin-muted" style="font-size:13px">Quick access to newly joined members.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="hm-admin-btn light">View all</a>
            </div>
            <div class="hm-admin-table-wrap">
                <table class="hm-admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                            <tr>
                                <td>
                                    <div class="hm-admin-mini-user">
                                        <div class="hm-admin-mini-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                                        <div>
                                            <div>{{ $user->name }}</div>
                                            <div class="hm-admin-muted">{{ $user->registration_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if(($user->account_status ?? 'active') === 'blocked')
                                        <span class="hm-admin-badge red">Blocked</span>
                                    @elseif($user->is_email_verified ?? false)
                                        <span class="hm-admin-badge green">Verified</span>
                                    @else
                                        <span class="hm-admin-badge yellow">Unverified</span>
                                    @endif
                                </td>
                                <td><a class="hm-admin-btn light" href="{{ route('admin.users.show', $user) }}">Open</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="hm-admin-empty">No users found.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="hm-admin-card hm-admin-panel">
            <div class="hm-admin-section-head">
                <div>
                    <h2 class="hm-admin-section-title" style="margin-bottom:4px">Recent Payments</h2>
                    <p class="hm-admin-muted" style="font-size:13px">Latest payment activity from your upgrade system.</p>
                </div>
                <a href="{{ route('admin.payments.index', ['scope' => 'all']) }}" class="hm-admin-btn light">View all</a>
            </div>
            <div class="hm-admin-table-wrap">
                <table class="hm-admin-table">
                    <thead>
                        <tr>
                            <th>Transaction</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr>
                                <td>
                                    <strong>{{ $payment->transaction_no }}</strong>
                                    <div class="hm-admin-muted">{{ $payment->plan_name }}</div>
                                </td>
                                <td>{{ $payment->customer_name ?: ($payment->registration?->name ?? 'Guest') }}</td>
                                <td>{{ $payment->formatted_amount }}</td>
                                <td>
                                    <span class="hm-admin-badge {{ $payment->status === 'paid' ? 'green' : ($payment->status === 'failed' ? 'red' : 'yellow') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="hm-admin-empty">No payment records yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
