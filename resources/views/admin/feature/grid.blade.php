@extends('layouts.admin')

@section('title', $title)

@section('content')
    <section class="hm-admin-page-head">
        <h1>{{ $title }}</h1>
        <p class="hm-admin-muted" style="font-size:14px">{{ $description ?? '' }}</p>
    </section>

    <section class="hm-admin-feature-grid">
        @forelse($items as $item)
            <article class="hm-admin-feature-card">
                <div class="hm-admin-feature-card-title">{{ $item['name'] ?? '-' }}</div>
                <div class="hm-admin-feature-card-value">{{ $item['value'] ?? 0 }}</div>
            </article>
        @empty
            <div class="hm-admin-empty hm-admin-card hm-admin-panel">No data available.</div>
        @endforelse
    </section>
@endsection
