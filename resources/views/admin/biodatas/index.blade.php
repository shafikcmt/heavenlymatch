@extends('layouts.admin')

@section('title', 'Biodata Review')

@section('content')
    <section class="hm-admin-hero">
        <div>
            <h1>Biodata Review</h1>
            <p>Approve, reject, feature and inspect biodata details before publishing.</p>
        </div>
        <div class="hm-admin-hero-pill">{{ $biodatas->total() }} biodatas</div>
    </section>

    <section class="hm-admin-card hm-admin-panel" style="margin-top:20px">
        <form method="GET" class="hm-admin-filters">
            <div class="hm-admin-field">
                <label>Search</label>
                <input class="hm-admin-input" type="search" name="q" value="{{ request('q') }}" placeholder="HM ID, user, occupation, email">
            </div>
            <div class="hm-admin-field">
                <label>Status</label>
                <select class="hm-admin-select" name="status">
                    <option value="">All</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
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
            <div class="hm-admin-field">
                <label>Completed</label>
                <select class="hm-admin-select" name="completed">
                    <option value="">All</option>
                    <option value="yes" @selected(request('completed') === 'yes')>Yes</option>
                    <option value="no" @selected(request('completed') === 'no')>No</option>
                </select>
            </div>
            <button class="hm-admin-btn primary" type="submit">Filter</button>
        </form>

        <div class="hm-admin-table-wrap">
            <table class="hm-admin-table">
                <thead>
                    <tr>
                        <th>Biodata</th>
                        <th>Owner</th>
                        <th>Profile</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($biodatas as $biodata)
                        @php $status = $biodata->status ?? 'pending'; @endphp
                        <tr>
                            <td>
                                <strong>{{ $biodata->registration_id }}</strong>
                                <div class="hm-admin-muted">{{ $biodata->groom_name ?: optional($biodata->registration)->name }}</div>
                            </td>
                            <td>
                                {{ optional($biodata->registration)->name ?: 'Unknown' }}
                                <div class="hm-admin-muted">{{ optional($biodata->registration)->email ?: 'No email' }}</div>
                            </td>
                            <td>{{ $biodata->height ?: 'N/A' }} · {{ $biodata->occupation ?: 'N/A' }}</td>
                            <td>{{ $biodata->present_address ?: $biodata->permanent_address ?: 'N/A' }}</td>
                            <td><span class="hm-admin-badge {{ $status === 'approved' ? 'green' : ($status === 'rejected' ? 'red' : 'yellow') }}">{{ ucfirst($status) }}</span></td>
                            <td>{!! $biodata->is_featured ? '<span class="hm-admin-badge pink">Featured</span>' : '<span class="hm-admin-badge gray">Normal</span>' !!}</td>
                            <td>{{ optional($biodata->updated_at)->format('d M Y') }}</td>
                            <td><a class="hm-admin-btn light" href="{{ route('admin.biodatas.show', $biodata) }}">Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="hm-admin-empty">No biodata found.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px">{{ $biodatas->links() }}</div>
    </section>
@endsection
