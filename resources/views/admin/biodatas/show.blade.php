@extends('layouts.admin')

@section('title', 'Biodata Details')

@section('content')
    @php
        $user = $biodata->registration;
        $status = $biodata->status ?? 'pending';
        $sections = [
            'Basic' => [
                'Religion' => $biodata->religion ?: optional($user)->religion,
                'Marital status' => $biodata->marital_status,
                'Birth date' => $biodata->birth_date,
                'Height' => $biodata->height,
                'Weight' => $biodata->weight,
                'Complexion' => $biodata->complexion,
                'Blood group' => $biodata->blood_group,
                'Nationality' => $biodata->nationality,
            ],
            'Address' => [
                'Permanent address' => $biodata->permanent_address,
                'Present address' => $biodata->present_address,
                'Village / Area' => $biodata->village_area,
                'Grew up' => $biodata->grew_up,
            ],
            'Education' => [
                'Education method' => $biodata->education_method,
                'Highest qualification' => $biodata->highest_qualification,
                'Graduation subject' => $biodata->graduation_subject,
                'Graduation institution' => $biodata->graduation_institution,
                'Other education' => $biodata->other_education,
                'Islamic titles' => $biodata->islamic_titles,
            ],
            'Family' => [
                'Father name' => $biodata->father_name,
                'Father alive' => $biodata->father_alive,
                'Father profession' => $biodata->father_profession,
                'Mother name' => $biodata->mother_name,
                'Mother alive' => $biodata->mother_alive,
                'Mother profession' => $biodata->mother_profession,
                'Brothers' => $biodata->brothers,
                'Sisters' => $biodata->sisters,
                'Financial status' => $biodata->family_financial_status,
                'Religious condition' => $biodata->family_religious_condition,
            ],
            'Personal and religious' => [
                'Clothing style' => $biodata->clothing_style,
                'Beard info' => $biodata->beard_info,
                'Prayers info' => $biodata->prayers_info,
                'Mahram / Non-mahram' => $biodata->mahram_nonmahram,
                'Quran recitation' => $biodata->quran_recitation,
                'Fiqh' => $biodata->fiqh,
                'Books read' => $biodata->books_read,
                'Diseases' => $biodata->diseases,
            ],
            'Occupation' => [
                'Occupation' => $biodata->occupation,
                'Profession details' => $biodata->profession_details,
                'Monthly income' => $biodata->monthly_income,
            ],
            'Expected partner' => [
                'Age' => $biodata->partner_age,
                'Complexion' => $biodata->partner_complexion,
                'Height' => $biodata->partner_height,
                'Education' => $biodata->partner_education,
                'District' => $biodata->partner_district,
                'Marital status' => $biodata->partner_marital_status,
                'Profession' => $biodata->partner_profession,
                'Expectations' => $biodata->partner_expectations,
            ],
            'Contact' => [
                'Candidate name' => $biodata->groom_name,
                'Candidate mobile' => $biodata->groom_mobile,
                'Guardian mobile' => $biodata->guardian_mobile,
                'Guardian relationship' => $biodata->guardian_relationship,
                'Guardian email' => $biodata->guardian_email,
            ],
        ];
    @endphp

    <section class="hm-admin-hero">
        <div>
            <h1>Biodata {{ $biodata->registration_id }}</h1>
            <p>{{ optional($user)->name ?: 'Unknown user' }} · {{ $biodata->height ?: 'Height N/A' }} · {{ $biodata->occupation ?: 'Occupation N/A' }}</p>
        </div>
        <div class="hm-admin-actions">
            <a href="{{ route('admin.biodatas.index') }}" class="hm-admin-btn light">← Back</a>
            <span class="hm-admin-badge {{ $status === 'approved' ? 'green' : ($status === 'rejected' ? 'red' : 'yellow') }}">{{ ucfirst($status) }}</span>
        </div>
    </section>

    <section class="hm-admin-detail" style="margin-top:20px">
        <aside class="hm-admin-card hm-admin-profile-card">
            <div class="hm-admin-profile-photo">{{ optional($user)->gender === 'female' ? '👩' : '👨' }}</div>
            <div class="hm-admin-profile-name">{{ $biodata->groom_name ?: optional($user)->name ?: 'No name' }}</div>
            <div class="hm-admin-muted">{{ $biodata->registration_id }}</div>
            <div style="margin-top:12px">
                <span class="hm-admin-badge {{ $status === 'approved' ? 'green' : ($status === 'rejected' ? 'red' : 'yellow') }}">{{ ucfirst($status) }}</span>
                @if($biodata->is_featured)
                    <span class="hm-admin-badge pink">Featured</span>
                @endif
            </div>

            <div class="hm-admin-info-list">
                <div class="hm-admin-info-row"><span>Owner</span><span>{{ optional($user)->name ?: 'Unknown' }}</span></div>
                <div class="hm-admin-info-row"><span>Email</span><span>{{ optional($user)->email ?: 'N/A' }}</span></div>
                <div class="hm-admin-info-row"><span>Phone</span><span>{{ optional($user)->country_code }} {{ optional($user)->mobile_number }}</span></div>
                <div class="hm-admin-info-row"><span>Completed</span><span>{{ $biodata->is_completed ? 'Yes' : 'No' }}</span></div>
                <div class="hm-admin-info-row"><span>Score</span><span>{{ $biodata->profile_score ?? 0 }}%</span></div>
                <div class="hm-admin-info-row"><span>Updated</span><span>{{ optional($biodata->updated_at)->format('d M Y') }}</span></div>
            </div>
        </aside>

        <section class="hm-admin-sections">
            <article class="hm-admin-card hm-admin-panel">
                <h2 class="hm-admin-section-title">Review actions</h2>
                <form method="POST" action="{{ route('admin.biodatas.reject', $biodata) }}" style="display:grid;gap:12px;margin-bottom:14px">
                    @csrf @method('PATCH')
                    <div class="hm-admin-field">
                        <label>Admin note / rejection reason</label>
                        <textarea class="hm-admin-textarea" name="admin_note" placeholder="Write a short note for internal tracking or rejection reason">{{ old('admin_note', $biodata->admin_note) }}</textarea>
                    </div>
                    <div class="hm-admin-actions">
                        <button formaction="{{ route('admin.biodatas.approve', $biodata) }}" class="hm-admin-btn primary">Approve</button>
                        <button class="hm-admin-btn danger">Reject</button>
                        <button formaction="{{ route('admin.biodatas.pending', $biodata) }}" class="hm-admin-btn light">Move pending</button>
                    </div>
                </form>

                <div class="hm-admin-actions">
                    @if($biodata->is_featured)
                        <form method="POST" action="{{ route('admin.biodatas.unfeature', $biodata) }}">@csrf @method('PATCH')<button class="hm-admin-btn light">Remove featured</button></form>
                    @else
                        <form method="POST" action="{{ route('admin.biodatas.feature', $biodata) }}">@csrf @method('PATCH')<button class="hm-admin-btn pink">Mark featured</button></form>
                    @endif
                    <form method="POST" action="{{ route('admin.biodatas.destroy', $biodata) }}" onsubmit="return confirm('Delete this biodata permanently?')">
                        @csrf @method('DELETE')
                        <button class="hm-admin-btn danger">Delete biodata</button>
                    </form>
                </div>
            </article>

            @foreach($sections as $title => $items)
                <article class="hm-admin-card hm-admin-panel">
                    <h2 class="hm-admin-section-title">{{ $title }}</h2>
                    <div class="hm-admin-kv">
                        @foreach($items as $label => $value)
                            <div class="hm-admin-kv-item">
                                <div class="hm-admin-kv-label">{{ $label }}</div>
                                <div class="hm-admin-kv-value">{{ filled($value) ? $value : 'Not provided' }}</div>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </section>
    </section>
@endsection
