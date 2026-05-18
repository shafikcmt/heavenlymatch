@extends('layouts.admin')

@section('title', $sectionMeta['title'] ?? 'System Setting')

@section('content')
    @if($section !== 'payment-gateways')
        <section class="hm-admin-page-head {{ $section === 'system-configuration' ? '' : 'hm-admin-page-head-split' }}">
            <div>
                <h1>{{ $sectionMeta['title'] }}</h1>
                @if($section !== 'system-configuration')
                    <p class="hm-admin-muted" style="font-size:14px">{{ $sectionMeta['description'] }}</p>
                @endif
            </div>
            @if($section !== 'system-configuration')
                <a href="{{ route('admin.settings.index') }}" class="hm-admin-btn light">← Back to Settings</a>
            @endif
        </section>
    @endif

    @if($section === 'payment-gateways')
        @include('admin.settings.partials.payment-gateways')
    @elseif($section === 'system-configuration')
        @include('admin.settings.partials.system-configuration')
    @else
        <section class="hm-admin-card hm-admin-panel hm-settings-form-card">
            <form method="POST" action="{{ route('admin.settings.update', $section) }}" enctype="multipart/form-data" class="hm-settings-form-grid">
                @csrf
                @method('PUT')

                @foreach($fieldDefinitions as $field)
                    @php
                        $name = $field['name'];
                        $key = $field['key'];
                        $type = $field['type'] ?? 'text';
                        $value = old($name, $settings[$key] ?? '');
                        $required = ! empty($field['required']);
                        $isWide = in_array($type, ['textarea', 'code', 'json', 'file'], true);
                    @endphp

                    <div class="hm-admin-field {{ $isWide ? 'hm-admin-wide' : '' }} {{ $type === 'checkbox' ? 'hm-settings-check-field' : '' }}">
                        @if($type === 'checkbox')
                            <label class="hm-settings-checkbox-label">
                                <input type="checkbox" name="{{ $name }}" value="1" {{ old($name, $settings[$key] ?? '0') ? 'checked' : '' }}>
                                <span>{{ $field['label'] }}</span>
                            </label>
                            @if(! empty($field['help']))<div class="hm-hint">{{ $field['help'] }}</div>@endif
                        @else
                            <label>{{ $field['label'] }} @if($required)<span class="hm-required">*</span>@endif</label>

                            @if($type === 'select')
                                <select class="hm-admin-select" name="{{ $name }}" {{ $required ? 'required' : '' }}>
                                    <option value="">Select {{ strtolower($field['label']) }}</option>
                                    @foreach(($field['options'] ?? []) as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" {{ (string) $value === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @elseif($type === 'textarea' || $type === 'json')
                                <textarea class="hm-admin-textarea" name="{{ $name }}" {{ $required ? 'required' : '' }}>{{ $value }}</textarea>
                            @elseif($type === 'code')
                                <textarea class="hm-admin-textarea hm-settings-code" name="{{ $name }}" spellcheck="false" {{ $required ? 'required' : '' }}>{{ $value }}</textarea>
                            @elseif($type === 'file')
                                <input class="hm-admin-input" type="file" name="{{ $name }}" accept=".jpg,.jpeg,.png,.webp,.svg,.ico">
                                @if(! empty($settings[$key]))
                                    <div class="hm-settings-file-preview">
                                        @php($filePath = $settings[$key])
                                        @if(preg_match('/\.(jpg|jpeg|png|webp|svg)$/i', $filePath))
                                            <img src="{{ asset($filePath) }}" alt="{{ $field['label'] }}">
                                        @endif
                                        <a href="{{ asset($filePath) }}" target="_blank">View current file</a>
                                    </div>
                                @endif
                            @elseif($type === 'number')
                                <input class="hm-admin-input" type="number" name="{{ $name }}" value="{{ $value }}" {{ $required ? 'required' : '' }}>
                            @elseif($type === 'email')
                                <input class="hm-admin-input" type="email" name="{{ $name }}" value="{{ $value }}" {{ $required ? 'required' : '' }}>
                            @elseif($type === 'url')
                                <input class="hm-admin-input" type="url" name="{{ $name }}" value="{{ $value }}" placeholder="https://example.com" {{ $required ? 'required' : '' }}>
                            @else
                                <input class="hm-admin-input" type="text" name="{{ $name }}" value="{{ $value }}" {{ $required ? 'required' : '' }}>
                            @endif

                            @if(! empty($field['help']))<div class="hm-hint">{{ $field['help'] }}</div>@endif
                        @endif
                    </div>
                @endforeach

                <div class="hm-admin-wide hm-settings-submit-row">
                    <button class="hm-admin-btn primary" type="submit">Save Changes</button>
                </div>
            </form>
        </section>
    @endif
@endsection
