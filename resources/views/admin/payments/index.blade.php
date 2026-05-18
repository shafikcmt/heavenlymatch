@extends('layouts.admin')

@section('title', $scopeTitle)

@section('content')
    <section class="hm-admin-page-head hm-admin-page-head-split">
        <div>
            <h1>{{ $scopeTitle }}</h1>
            <p class="hm-admin-muted" style="font-size:14px">Manage payment records, filter by status and update transaction state from one place.</p>
        </div>
        <div class="hm-admin-pill-value">Total Paid: ${{ number_format($totalAmount, 2) }}</div>
    </section>

    <section class="hm-admin-mini-stats">
        @foreach([
            ['key' => 'pending', 'label' => 'Pending'],
            ['key' => 'approved', 'label' => 'Approved'],
            ['key' => 'successful', 'label' => 'Successful'],
            ['key' => 'rejected', 'label' => 'Rejected'],
            ['key' => 'initiated', 'label' => 'Initiated'],
            ['key' => 'all', 'label' => 'All'],
        ] as $item)
            <a href="{{ route('admin.payments.index', ['scope' => $item['key']]) }}" class="hm-admin-mini-stat {{ $scope === $item['key'] ? 'active' : '' }}">
                <strong>{{ $counts[$item['key']] ?? 0 }}</strong>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </section>

    <section class="hm-admin-card hm-admin-panel" style="margin-top:18px">
        <form method="GET" class="hm-admin-filters hm-admin-filters-compact">
            <div class="hm-admin-field">
                <label>Search</label>
                <input class="hm-admin-input" type="search" name="q" value="{{ request('q') }}" placeholder="Transaction, user, plan, gateway">
            </div>
            <button class="hm-admin-btn primary" type="submit">Filter</button>
        </form>

        <div class="hm-admin-table-wrap">
            <table class="hm-admin-table">
                <thead>
                    <tr>
                        <th>Transaction</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Gateway</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>
                                <strong>{{ $payment->transaction_no }}</strong>
                                <div class="hm-admin-muted">{{ optional($payment->created_at)->format('d M Y h:i A') }}</div>
                            </td>
                            <td>
                                {{ $payment->customer_name ?: ($payment->registration?->name ?? 'Guest') }}
                                <div class="hm-admin-muted">{{ $payment->customer_phone ?: $payment->customer_email }}</div>
                            </td>
                            <td>
                                {{ $payment->plan_name }}
                                <div class="hm-admin-muted">{{ $payment->duration_months }} months</div>
                            </td>
                            <td>
                                {{ $payment->gateway_name ?: '-' }}
                                <div class="hm-admin-muted">{{ $payment->external_transaction_id ?: 'No TRX yet' }}</div>
                            </td>
                            <td>{{ $payment->formatted_amount }}</td>
                            <td>
                                <span class="hm-admin-badge {{ $payment->status === 'paid' ? 'green' : ($payment->status === 'failed' ? 'red' : ($payment->status === 'cancelled' ? 'red' : 'yellow')) }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="hm-admin-empty">No payments found for this section.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px">{{ $payments->links() }}</div>
    </section>
@endsection
