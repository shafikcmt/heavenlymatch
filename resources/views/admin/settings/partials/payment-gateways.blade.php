@php
    $tab = $activeGatewayTab ?? 'automatic';
    $search = $gatewaySearch ?? '';
    $currencyCount = function ($gateway, string $key, int $fallback = 0): int {
        $config = is_array($gateway->config) ? $gateway->config : [];
        $value = $config[$key] ?? null;

        if (is_array($value)) {
            return count(array_filter($value));
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        if (is_string($value) && trim($value) !== '') {
            return count(array_filter(array_map('trim', explode(',', $value))));
        }

        return $fallback;
    };
@endphp

<section class="hm-gateway-tabs">
    <a class="hm-gateway-tab {{ $tab === 'automatic' ? 'active' : '' }}" href="{{ route('admin.settings.edit', ['section' => 'payment-gateways', 'tab' => 'automatic']) }}">
        <span class="hm-gateway-tab-icon">▭</span> Automatic Gateway
    </a>
    <a class="hm-gateway-tab {{ $tab === 'manual' ? 'active' : '' }}" href="{{ route('admin.settings.edit', ['section' => 'payment-gateways', 'tab' => 'manual']) }}">
        <span class="hm-gateway-tab-icon">▣</span> Manual Gateway
    </a>
</section>

<section class="hm-gateway-page-head">
    <h2>{{ $tab === 'manual' ? 'Manual Gateways' : 'Automatic Gateways' }}</h2>
    <div class="hm-gateway-tools">
        <form method="GET" action="{{ route('admin.settings.edit', 'payment-gateways') }}" class="hm-gateway-search">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search...">
            <button type="submit">⌕</button>
        </form>
        @if($tab === 'manual')
            <button type="button" class="hm-admin-btn hm-gateway-add-btn" data-hm-modal-open="#hmAddManualGateway">+ Add New</button>
        @endif
    </div>
</section>

@if($tab === 'manual')
    <section class="hm-gateway-table-card">
        <div class="hm-admin-table-wrap">
            <table class="hm-admin-table hm-gateway-table">
                <thead>
                    <tr>
                        <th>Gateway</th>
                        <th>Status</th>
                        <th class="hm-gateway-action-col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($manualGateways as $gateway)
                        <tr>
                            <td>{{ $gateway->name }}</td>
                            <td><span class="hm-gateway-status {{ $gateway->is_active ? 'enabled' : 'disabled' }}">{{ $gateway->is_active ? 'Enabled' : 'Disabled' }}</span></td>
                            <td>
                                <div class="hm-gateway-actions">
                                    <button type="button" class="hm-gateway-btn edit" data-hm-modal-open="#hmGatewayEdit{{ $gateway->id }}">✎ Edit</button>
                                    <form method="POST" action="{{ route('admin.settings.gateways.update', $gateway) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="name" value="{{ $gateway->name }}">
                                        <input type="hidden" name="type" value="manual">
                                        <input type="hidden" name="checkout_url" value="{{ $gateway->checkout_url }}">
                                        <input type="hidden" name="merchant_id" value="{{ $gateway->merchant_id }}">
                                        <input type="hidden" name="public_key" value="{{ $gateway->public_key }}">
                                        <input type="hidden" name="instructions" value="{{ $gateway->instructions }}">
                                        <input type="hidden" name="sort_order" value="{{ $gateway->sort_order }}">
                                        @if($gateway->sandbox)<input type="hidden" name="sandbox" value="1">@endif
                                        @if($gateway->is_default)<input type="hidden" name="is_default" value="1">@endif
                                        @if(! $gateway->is_active)<input type="hidden" name="is_active" value="1">@endif
                                        <button type="submit" class="hm-gateway-btn {{ $gateway->is_active ? 'disable' : 'enable' }}">{{ $gateway->is_active ? '⊘ Disable' : '◉ Enable' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="hm-admin-empty">No manual gateways found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@else
    <section class="hm-gateway-table-card">
        <div class="hm-admin-table-wrap">
            <table class="hm-admin-table hm-gateway-table">
                <thead>
                    <tr>
                        <th>Gateway</th>
                        <th>Supported Currency</th>
                        <th>Enabled Currency</th>
                        <th>Status</th>
                        <th class="hm-gateway-action-col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($automaticGateways as $gateway)
                        @php
                            $supportedFallback = $gateway->type === 'redirect' ? 0 : 1;
                            $supportedCurrencyCount = $currencyCount($gateway, 'supported_currencies', $supportedFallback);
                            $enabledCurrencyCount = $currencyCount($gateway, 'enabled_currencies', $gateway->is_active ? min(1, max(1, $supportedCurrencyCount)) : 0);
                        @endphp
                        <tr>
                            <td>{{ $gateway->name }}</td>
                            <td>{{ $supportedCurrencyCount }}</td>
                            <td>{{ $enabledCurrencyCount }}</td>
                            <td><span class="hm-gateway-status {{ $gateway->is_active ? 'enabled' : 'disabled' }}">{{ $gateway->is_active ? 'Enabled' : 'Disabled' }}</span></td>
                            <td>
                                <div class="hm-gateway-actions">
                                    <button type="button" class="hm-gateway-btn edit" data-hm-modal-open="#hmGatewayEdit{{ $gateway->id }}">✎ Edit</button>
                                    <form method="POST" action="{{ route('admin.settings.gateways.update', $gateway) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="name" value="{{ $gateway->name }}">
                                        <input type="hidden" name="type" value="{{ $gateway->type }}">
                                        <input type="hidden" name="checkout_url" value="{{ $gateway->checkout_url }}">
                                        <input type="hidden" name="merchant_id" value="{{ $gateway->merchant_id }}">
                                        <input type="hidden" name="public_key" value="{{ $gateway->public_key }}">
                                        <input type="hidden" name="instructions" value="{{ $gateway->instructions }}">
                                        <input type="hidden" name="sort_order" value="{{ $gateway->sort_order }}">
                                        @if($gateway->sandbox)<input type="hidden" name="sandbox" value="1">@endif
                                        @if($gateway->is_default)<input type="hidden" name="is_default" value="1">@endif
                                        @if(! $gateway->is_active)<input type="hidden" name="is_active" value="1">@endif
                                        <button type="submit" class="hm-gateway-btn {{ $gateway->is_active ? 'disable' : 'enable' }}">{{ $gateway->is_active ? '⊘ Disable' : '◉ Enable' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="hm-admin-empty">No automatic gateways found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endif

@foreach($gateways as $gateway)
    <div class="hm-modal-backdrop" data-hm-modal-close="#hmGatewayEdit{{ $gateway->id }}"></div>
    <div class="hm-modal-card hm-gateway-modal" id="hmGatewayEdit{{ $gateway->id }}" aria-hidden="true">
        <div class="hm-modal-head">
            <h2>Edit {{ $gateway->name }}</h2>
            <button type="button" class="hm-modal-close" data-hm-modal-close="#hmGatewayEdit{{ $gateway->id }}">×</button>
        </div>
        <form method="POST" action="{{ route('admin.settings.gateways.update', $gateway) }}" class="hm-package-modal-form hm-gateway-modal-form">
            @csrf
            @method('PATCH')
            <div class="hm-admin-field">
                <label>Gateway name</label>
                <input class="hm-admin-input" name="name" value="{{ old('name', $gateway->name) }}" required>
            </div>
            <div class="hm-admin-field">
                <label>Gateway type</label>
                @if($gateway->type === 'manual')
                    <input type="hidden" name="type" value="manual">
                    <input class="hm-admin-input" value="Manual" disabled>
                @else
                    <select class="hm-admin-select" name="type" required>
                        @foreach(['redirect' => 'Custom Redirect', 'sslcommerz' => 'SSLCommerz', 'bkash' => 'bKash', 'nagad' => 'Nagad'] as $value => $label)
                            <option value="{{ $value }}" {{ $gateway->type === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="hm-admin-field">
                <label>Checkout URL</label>
                <input class="hm-admin-input" name="checkout_url" value="{{ old('checkout_url', $gateway->checkout_url) }}" placeholder="https://provider.example/checkout">
            </div>
            <div class="hm-admin-field">
                <label>Merchant / Store ID</label>
                <input class="hm-admin-input" name="merchant_id" value="{{ old('merchant_id', $gateway->merchant_id) }}">
            </div>
            <div class="hm-admin-field">
                <label>Public key</label>
                <input class="hm-admin-input" name="public_key" value="{{ old('public_key', $gateway->public_key) }}">
            </div>
            <div class="hm-admin-field">
                <label>Secret key / Store password</label>
                <input class="hm-admin-input" name="secret_key" type="password" placeholder="Leave blank to keep current secret">
            </div>
            <div class="hm-admin-field hm-admin-wide">
                <label>Instructions shown to users</label>
                <textarea class="hm-admin-textarea" name="instructions">{{ old('instructions', $gateway->instructions) }}</textarea>
            </div>
            <div class="hm-admin-field">
                <label>Sort order</label>
                <input class="hm-admin-input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $gateway->sort_order) }}">
            </div>
            <div class="hm-admin-switch-row hm-admin-wide">
                <label><input type="checkbox" name="is_active" value="1" {{ $gateway->is_active ? 'checked' : '' }}> Active</label>
                <label><input type="checkbox" name="is_default" value="1" {{ $gateway->is_default ? 'checked' : '' }}> Default</label>
                <label><input type="checkbox" name="sandbox" value="1" {{ $gateway->sandbox ? 'checked' : '' }}> Sandbox mode</label>
            </div>
            <div class="hm-modal-actions hm-admin-wide">
                <button class="hm-admin-btn hm-package-submit-btn" type="submit">Save Gateway</button>
            </div>
        </form>
    </div>
@endforeach

<div class="hm-modal-backdrop" data-hm-modal-close="#hmAddManualGateway"></div>
<div class="hm-modal-card hm-gateway-modal" id="hmAddManualGateway" aria-hidden="true">
    <div class="hm-modal-head">
        <h2>Add Manual Gateway</h2>
        <button type="button" class="hm-modal-close" data-hm-modal-close="#hmAddManualGateway">×</button>
    </div>
    <form method="POST" action="{{ route('admin.settings.gateways.store') }}" class="hm-package-modal-form hm-gateway-modal-form">
        @csrf
        <input type="hidden" name="type" value="manual">
        <div class="hm-admin-field">
            <label>Gateway name</label>
            <input class="hm-admin-input" name="name" placeholder="Bank Wire" required>
        </div>
        <div class="hm-admin-field">
            <label>Merchant / Account</label>
            <input class="hm-admin-input" name="merchant_id" placeholder="Account number or mobile wallet number">
        </div>
        <div class="hm-admin-field hm-admin-wide">
            <label>Instructions shown to users</label>
            <textarea class="hm-admin-textarea" name="instructions" placeholder="Send payment to this account and submit your transaction ID."></textarea>
        </div>
        <div class="hm-admin-field">
            <label>Sort order</label>
            <input class="hm-admin-input" type="number" name="sort_order" min="0" value="0">
        </div>
        <div class="hm-admin-switch-row hm-admin-wide">
            <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
            <label><input type="checkbox" name="is_default" value="1"> Default</label>
            <label><input type="checkbox" name="sandbox" value="1" checked> Sandbox mode</label>
        </div>
        <div class="hm-modal-actions hm-admin-wide">
            <button class="hm-admin-btn hm-package-submit-btn" type="submit">Add Gateway</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const openModal = function (selector) {
            const modal = document.querySelector(selector);
            if (!modal) return;
            const backdrop = document.querySelector('[data-hm-modal-close="' + selector + '"].hm-modal-backdrop');
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            if (backdrop) backdrop.classList.add('active');
            document.body.classList.add('hm-modal-open');
        };

        const closeModal = function (selector) {
            const modal = document.querySelector(selector);
            if (!modal) return;
            const backdrop = document.querySelector('[data-hm-modal-close="' + selector + '"].hm-modal-backdrop');
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            if (backdrop) backdrop.classList.remove('active');
            document.body.classList.remove('hm-modal-open');
        };

        document.querySelectorAll('[data-hm-modal-open]').forEach(function (button) {
            button.addEventListener('click', function () {
                openModal(button.getAttribute('data-hm-modal-open'));
            });
        });

        document.querySelectorAll('[data-hm-modal-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(button.getAttribute('data-hm-modal-close'));
            });
        });
    });
</script>
@endpush
