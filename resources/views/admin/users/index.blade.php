@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <section class="hm-admin-hero">
        <div>
            <h1>User Management</h1>
            <p>Search, verify, block and manage members from one clean dashboard.</p>
        </div>
        <div class="hm-admin-hero-pill">{{ $users->total() }} users</div>
    </section>

    <section class="hm-admin-card hm-admin-panel" style="margin-top:20px">
        <form method="GET" class="hm-admin-filters">
            <div class="hm-admin-field">
                <label>Search</label>
                <input class="hm-admin-input" type="search" name="q" value="{{ request('q') }}" placeholder="Name, email, phone, HM ID">
            </div>
            <div class="hm-admin-field">
                <label>Account status</label>
                <select class="hm-admin-select" name="status">
                    <option value="">All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
                </select>
            </div>
            <div class="hm-admin-field">
                <label>Email verified</label>
                <select class="hm-admin-select" name="verified">
                    <option value="">All</option>
                    <option value="yes" @selected(request('verified') === 'yes')>Verified</option>
                    <option value="no" @selected(request('verified') === 'no')>Unverified</option>
                </select>
            </div>
            <div class="hm-admin-field">
                <label>Gender</label>
                <select class="hm-admin-select" name="gender">
                    <option value="">All</option>
                    <option value="male" @selected(request('gender') === 'male')>Male</option>
                    <option value="female" @selected(request('gender') === 'female')>Female</option>
                </select>
            </div>
            <button class="hm-admin-btn primary" type="submit">Search</button>
        </form>

        <div class="hm-admin-table-wrap">
            <table class="hm-admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Verification</th>
                        <th>Status</th>
                        <th>Biodata</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="hm-admin-mini-user">
                                    <div class="hm-admin-mini-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                                    <div>
                                        <div>{{ $user->name }}</div>
                                        <div class="hm-admin-muted">{{ $user->registration_id }} · {{ ucfirst($user->gender ?? 'N/A') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $user->email }}
                                <div class="hm-admin-muted">{{ $user->country_code }} {{ $user->mobile_number }}</div>
                            </td>
                            <td>
                                @if($user->is_email_verified ?? false)
                                    <span class="hm-admin-badge green">Email verified</span>
                                @else
                                    <span class="hm-admin-badge yellow">Need verify</span>
                                @endif
                            </td>
                            <td>
                                @if(($user->account_status ?? 'active') === 'blocked')
                                    <span class="hm-admin-badge red">Blocked</span>
                                @elseif($user->is_admin ?? false)
                                    <span class="hm-admin-badge pink">Admin</span>
                                @else
                                    <span class="hm-admin-badge green">Active</span>
                                @endif
                            </td>
                            <td>{{ optional($user->biodata)->status ? ucfirst(optional($user->biodata)->status) : 'Not created' }}</td>
                            <td>{{ optional($user->created_at)->format('d M Y') }}</td>
                            <td><a class="hm-admin-btn light" href="{{ route('admin.users.show', $user) }}">Manage</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="hm-admin-empty">No users match your filters.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px">{{ $users->links() }}</div>
    </section>
@endsection
