@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
    <section class="hm-admin-hero">
        <div>
            <h1>{{ $user->name }}</h1>
            <p>{{ $user->registration_id }} · {{ $user->email }}</p>
        </div>
        <div class="hm-admin-actions">
            <a href="{{ route('admin.users.index') }}" class="hm-admin-btn light">← Back</a>
            @if($user->biodata)
                <a href="{{ route('admin.biodatas.show', $user->biodata) }}" class="hm-admin-btn light">Open biodata</a>
            @endif
        </div>
    </section>

    <section class="hm-admin-detail" style="margin-top:20px">
        <aside class="hm-admin-card hm-admin-profile-card">
            <div class="hm-admin-profile-photo">{{ ($user->gender ?? 'male') === 'female' ? '👩' : '👨' }}</div>
            <div class="hm-admin-profile-name">{{ $user->name }}</div>
            <div class="hm-admin-muted">{{ $user->registration_id }}</div>
            <div style="margin-top:12px">
                @if(($user->account_status ?? 'active') === 'blocked')
                    <span class="hm-admin-badge red">Blocked</span>
                @elseif($user->is_admin ?? false)
                    <span class="hm-admin-badge pink">Admin</span>
                @else
                    <span class="hm-admin-badge green">Active member</span>
                @endif
            </div>

            <div class="hm-admin-info-list">
                <div class="hm-admin-info-row"><span>Email</span><span>{{ $user->email }}</span></div>
                <div class="hm-admin-info-row"><span>Phone</span><span>{{ $user->country_code }} {{ $user->mobile_number }}</span></div>
                <div class="hm-admin-info-row"><span>Gender</span><span>{{ ucfirst($user->gender ?? 'N/A') }}</span></div>
                <div class="hm-admin-info-row"><span>Religion</span><span>{{ $user->religion ?: 'N/A' }}</span></div>
                <div class="hm-admin-info-row"><span>Blood group</span><span>{{ $user->blood_group ?: 'N/A' }}</span></div>
                <div class="hm-admin-info-row"><span>Marital status</span><span>{{ $user->marital_status ?: 'N/A' }}</span></div>
                <div class="hm-admin-info-row"><span>Profile for</span><span>{{ ucfirst($user->profile_for ?? 'N/A') }}</span></div>
                <div class="hm-admin-info-row"><span>Joined</span><span>{{ optional($user->created_at)->format('d M Y') }}</span></div>
                <div class="hm-admin-info-row"><span>Last login</span><span>{{ optional($user->last_login_at)->format('d M Y h:i A') ?: 'Never' }}</span></div>
            </div>
        </aside>

        <section class="hm-admin-sections">
            <article class="hm-admin-card hm-admin-panel">
                <h2 class="hm-admin-section-title">Admin actions</h2>
                <div class="hm-admin-actions">
                    @if(!($user->is_email_verified ?? false))
                        <form method="POST" action="{{ route('admin.users.verify-email', $user) }}">@csrf @method('PATCH')<button class="hm-admin-btn primary">Mark email verified</button></form>
                    @endif

                    @if(!($user->is_admin ?? false))
                        <form method="POST" action="{{ route('admin.users.make-admin', $user) }}">@csrf @method('PATCH')<button class="hm-admin-btn light">Make admin</button></form>
                    @else
                        <form method="POST" action="{{ route('admin.users.remove-admin', $user) }}">@csrf @method('PATCH')<button class="hm-admin-btn light">Remove admin</button></form>
                    @endif

                    @if(($user->account_status ?? 'active') === 'blocked')
                        <form method="POST" action="{{ route('admin.users.unblock', $user) }}">@csrf @method('PATCH')<button class="hm-admin-btn primary">Unblock user</button></form>
                    @else
                        <form method="POST" action="{{ route('admin.users.block', $user) }}">@csrf @method('PATCH')<button class="hm-admin-btn danger">Block user</button></form>
                    @endif

                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user permanently?')">
                        @csrf @method('DELETE')
                        <button class="hm-admin-btn danger">Delete</button>
                    </form>
                </div>
                @if($user->blocked_reason)
                    <div class="hm-admin-note-box" style="margin-top:14px">Blocked reason: {{ $user->blocked_reason }}</div>
                @endif
            </article>

            <article class="hm-admin-card hm-admin-panel">
                <h2 class="hm-admin-section-title">Biodata summary</h2>
                @if($user->biodata)
                    @php $b = $user->biodata; @endphp
                    <div class="hm-admin-kv">
                        <div class="hm-admin-kv-item"><div class="hm-admin-kv-label">Status</div><div class="hm-admin-kv-value">{{ ucfirst($b->status ?? 'pending') }}</div></div>
                        <div class="hm-admin-kv-item"><div class="hm-admin-kv-label">Completed</div><div class="hm-admin-kv-value">{{ $b->is_completed ? 'Yes' : 'No' }}</div></div>
                        <div class="hm-admin-kv-item"><div class="hm-admin-kv-label">Religion</div><div class="hm-admin-kv-value">{{ $b->religion ?: ($user->religion ?: 'Not set') }}</div></div>
                        <div class="hm-admin-kv-item"><div class="hm-admin-kv-label">Age / Birth date</div><div class="hm-admin-kv-value">{{ $b->birth_date ?: 'Not set' }}</div></div>
                        <div class="hm-admin-kv-item"><div class="hm-admin-kv-label">Height</div><div class="hm-admin-kv-value">{{ $b->height ?: 'Not set' }}</div></div>
                        <div class="hm-admin-kv-item"><div class="hm-admin-kv-label">Occupation</div><div class="hm-admin-kv-value">{{ $b->occupation ?: 'Not set' }}</div></div>
                        <div class="hm-admin-kv-item"><div class="hm-admin-kv-label">Present address</div><div class="hm-admin-kv-value">{{ $b->present_address ?: 'Not set' }}</div></div>
                    </div>
                @else
                    <div class="hm-admin-empty">This user has not created biodata yet.</div>
                @endif
            </article>
        </section>
    </section>
@endsection
