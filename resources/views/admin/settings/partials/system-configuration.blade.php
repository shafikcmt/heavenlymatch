<section class="hm-system-config-card">
    @foreach($systemFeatureRows as $feature)
        <div class="hm-system-config-row">
            <div class="hm-system-config-copy">
                <h3>{{ $feature['title'] }}</h3>
                <p>{!! $feature['description'] !!}</p>
            </div>
            <form method="POST" action="{{ route('admin.settings.system.toggle', $feature['slug']) }}" class="hm-system-config-action">
                @csrf
                @method('PATCH')
                <button type="submit" class="hm-module-toggle {{ $feature['enabled'] ? 'enabled' : 'disabled' }}" title="Click to {{ $feature['enabled'] ? 'disable' : 'enable' }} {{ $feature['title'] }}">
                    {{ $feature['enabled'] ? 'Enable' : 'Disable' }}
                </button>
            </form>
        </div>
    @endforeach
</section>
