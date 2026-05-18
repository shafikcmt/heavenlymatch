@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
    @php
        $iconMap = [
            'gear' => '⚙',
            'image' => '▣',
            'sliders' => '⚙',
            'bell' => '♢',
            'credit-card' => '▭',
            'globe' => '◉',
            'frontend' => '▧',
            'pages' => '☷',
            'user-check' => '☑',
            'user-circle' => '☻',
            'language' => '文',
            'extension' => '✣',
            'shield' => '⬡',
            'robot' => '▣',
            'cookie' => '◌',
            'css' => '▤',
            'sitemap' => '☷',
        ];
    @endphp

    <section class="hm-admin-page-head">
        <h1>System Settings</h1>
    </section>

    <section class="hm-settings-search-card">
        <span class="hm-settings-search-icon">⌕</span>
        <input type="search" id="hmSettingsSearch" placeholder="Search..." autocomplete="off">
    </section>

    <section class="hm-settings-grid" id="hmSettingsGrid">
        @foreach($settingsCards as $slug => $card)
            <a href="{{ route('admin.settings.edit', $slug) }}"
               class="hm-settings-card {{ ! empty($card['highlight']) ? 'is-highlighted' : '' }}"
               data-settings-card
               data-title="{{ strtolower($card['title'] . ' ' . $card['description'] . ' ' . ($card['summary'] ?? '')) }}">
                <div class="hm-settings-icon-box">{{ $iconMap[$card['icon']] ?? '⚙' }}</div>
                <div class="hm-settings-card-copy">
                    <h2>{{ $card['title'] }}</h2>
                    <p>{{ $card['description'] }}</p>
                    @if(! empty($card['summary']))
                        <span>{{ $card['summary'] }}</span>
                    @endif
                </div>
                <div class="hm-settings-watermark">{{ $iconMap[$card['icon']] ?? '⚙' }}</div>
            </a>
        @endforeach
    </section>

    <div class="hm-settings-empty" id="hmSettingsEmpty" hidden>No setting module matched your search.</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const search = document.getElementById('hmSettingsSearch');
        const cards = Array.from(document.querySelectorAll('[data-settings-card]'));
        const empty = document.getElementById('hmSettingsEmpty');

        search.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            let visibleCount = 0;

            cards.forEach(function (card) {
                const visible = card.dataset.title.includes(query);
                card.style.display = visible ? '' : 'none';
                if (visible) visibleCount += 1;
            });

            empty.hidden = visibleCount > 0;
        });
    });
</script>
@endpush
