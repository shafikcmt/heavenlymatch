@extends('layouts.admin')

@section('title', 'Manage Packages')

@section('content')
    @php
        $planPayload = $plans->mapWithKeys(function ($plan) {
            return [
                $plan->id => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'interest_express_limit' => $plan->interest_express_limit ?? -1,
                    'profile_show_limit' => $plan->profile_show_limit ?? -1,
                    'image_upload_limit' => $plan->image_upload_limit ?? -1,
                    'validity_days' => $plan->validity_days ?? ($plan->duration_months * 30),
                    'price' => (float) $plan->price,
                    'badge' => $plan->badge,
                    'sort_order' => $plan->sort_order,
                    'is_active' => (bool) $plan->is_active,
                    'is_popular' => (bool) $plan->is_popular,
                    'features_text' => implode("\n", $plan->features ?: []),
                ],
            ];
        });
    @endphp

    <section class="hm-admin-page-head hm-admin-page-head-split">
        <div>
            <h1>Manage Packages</h1>
            <p class="hm-admin-muted" style="font-size:14px">Create and manage all premium packages with a clean popup form and Bangladeshi Taka pricing.</p>
        </div>
        <button type="button" class="hm-admin-btn primary hm-package-add-btn" onclick="openPlanModal('create')">＋ Add New</button>
    </section>

    <section class="hm-admin-card hm-admin-panel hm-package-panel">
        <div class="hm-admin-table-wrap">
            <table class="hm-admin-table hm-package-table">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Name</th>
                        <th>Interest Express Limit</th>
                        <th>Profile Show Limit</th>
                        <th>Image Upload Limit</th>
                        <th>Validity Period</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $index => $plan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="hm-package-name-cell">{{ $plan->name }}</td>
                            <td>
                                @if($plan->interest_express_limit === -1)
                                    <span class="hm-package-limit-pill">Unlimited</span>
                                @else
                                    {{ $plan->interest_express_label }}
                                @endif
                            </td>
                            <td>
                                @if($plan->profile_show_limit === -1)
                                    <span class="hm-package-limit-pill">Unlimited</span>
                                @else
                                    {{ $plan->profile_show_label }}
                                @endif
                            </td>
                            <td>
                                @if($plan->image_upload_limit === -1)
                                    <span class="hm-package-limit-pill">Unlimited</span>
                                @else
                                    {{ $plan->image_upload_label }}
                                @endif
                            </td>
                            <td>{{ $plan->duration_label }}</td>
                            <td class="hm-package-price-cell">{{ $plan->formatted_price }}</td>
                            <td>
                                <span class="hm-package-status {{ $plan->is_active ? 'enabled' : 'disabled' }}">
                                    {{ $plan->is_active ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td>
                                <div class="hm-package-actions">
                                    <button type="button" class="hm-admin-btn light hm-package-edit-btn" onclick="openPlanModal('edit', {{ $plan->id }})">✎ Edit</button>
                                    <form method="POST" action="{{ route('admin.settings.plans.toggle-status', $plan) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="hm-admin-btn {{ $plan->is_active ? 'danger' : 'primary' }} hm-package-toggle-btn">
                                            {{ $plan->is_active ? '⊘ Disable' : '✓ Enable' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9"><div class="hm-admin-empty">No membership packages available. Click “Add New” to create your first plan.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="hm-modal-backdrop" id="planModalBackdrop" onclick="closePlanModal()"></div>
    <section class="hm-modal-card" id="planModalCard" aria-hidden="true">
        <div class="hm-modal-head">
            <h2 id="planModalTitle">Add New Package</h2>
            <button type="button" class="hm-modal-close" onclick="closePlanModal()">×</button>
        </div>
        <form method="POST" id="planModalForm" action="{{ route('admin.settings.plans.store') }}" class="hm-package-modal-form">
            @csrf
            <input type="hidden" name="_method" id="planMethod" value="POST">
            <input type="hidden" name="currency" value="BDT">
            <input type="hidden" name="_mode" id="planMode" value="create">

            <div class="hm-admin-field hm-admin-wide">
                <label>Name <span class="hm-required">*</span></label>
                <input class="hm-admin-input" type="text" name="name" id="plan_name" required>
            </div>

            <div class="hm-admin-field hm-admin-wide">
                <label>Interest Express Limit <span class="hm-hint">(Enter -1 for unlimited period)</span> <span class="hm-required">*</span></label>
                <input class="hm-admin-input" type="number" name="interest_express_limit" id="plan_interest_express_limit" required>
            </div>

            <div class="hm-admin-field hm-admin-wide">
                <label>Profile Show Limit <span class="hm-hint">(Enter -1 for unlimited period)</span> <span class="hm-required">*</span></label>
                <input class="hm-admin-input" type="number" name="profile_show_limit" id="plan_profile_show_limit" required>
            </div>

            <div class="hm-admin-field hm-admin-wide">
                <label>Image Upload Limit <span class="hm-hint">(Enter -1 for unlimited period)</span> <span class="hm-required">*</span></label>
                <input class="hm-admin-input" type="number" name="image_upload_limit" id="plan_image_upload_limit" required>
            </div>

            <div class="hm-admin-field hm-admin-wide">
                <label>Validity Period <span class="hm-hint">(In days, enter -1 for unlimited period)</span> <span class="hm-required">*</span></label>
                <input class="hm-admin-input" type="number" name="validity_days" id="plan_validity_days" required>
            </div>

            <div class="hm-admin-field hm-admin-wide">
                <label>Price <span class="hm-required">*</span></label>
                <div class="hm-price-input-group">
                    <span class="hm-price-prefix">৳</span>
                    <input class="hm-admin-input" type="number" name="price" id="plan_price" step="0.01" min="0" required>
                    <span class="hm-price-suffix">BDT</span>
                </div>
            </div>

            <div class="hm-admin-field hm-admin-wide">
                <label>Badge / Tag</label>
                <input class="hm-admin-input" type="text" name="badge" id="plan_badge" placeholder="Most popular">
            </div>

            <div class="hm-admin-field hm-admin-wide">
                <label>Extra feature notes</label>
                <textarea class="hm-admin-textarea" name="features_text" id="plan_features_text" placeholder="Premium badge on profile&#10;Priority support"></textarea>
            </div>

            <div class="hm-package-modal-inline">
                <div class="hm-admin-field">
                    <label>Sort Order</label>
                    <input class="hm-admin-input" type="number" name="sort_order" id="plan_sort_order" min="0" value="0">
                </div>
                <div class="hm-admin-field">
                    <label>Status</label>
                    <div class="hm-admin-switch-row hm-package-switch-row">
                        <label><input type="checkbox" name="is_active" id="plan_is_active" value="1" checked> Enabled</label>
                        <label><input type="checkbox" name="is_popular" id="plan_is_popular" value="1"> Popular</label>
                    </div>
                </div>
            </div>

            <div class="hm-modal-actions">
                <button type="submit" class="hm-admin-btn primary hm-package-submit-btn" id="planSubmitBtn">Submit</button>
            </div>
        </form>
    </section>

@endsection

@push('scripts')
<script>
    const packagePlans = @json($planPayload);
    const planModalBackdrop = document.getElementById('planModalBackdrop');
    const planModalCard = document.getElementById('planModalCard');
    const planModalTitle = document.getElementById('planModalTitle');
    const planModalForm = document.getElementById('planModalForm');
    const planMethod = document.getElementById('planMethod');
    const planMode = document.getElementById('planMode');
    const planSubmitBtn = document.getElementById('planSubmitBtn');
    const updateActionTemplate = @json(route('admin.settings.plans.update', ['plan' => 'PLAN_ID']));

    function fillPlanForm(data = {}) {
        document.getElementById('plan_name').value = data.name ?? '';
        document.getElementById('plan_interest_express_limit').value = data.interest_express_limit ?? '';
        document.getElementById('plan_profile_show_limit').value = data.profile_show_limit ?? '';
        document.getElementById('plan_image_upload_limit').value = data.image_upload_limit ?? '';
        document.getElementById('plan_validity_days').value = data.validity_days ?? '';
        document.getElementById('plan_price').value = data.price ?? '';
        document.getElementById('plan_badge').value = data.badge ?? '';
        document.getElementById('plan_features_text').value = data.features_text ?? '';
        document.getElementById('plan_sort_order').value = data.sort_order ?? 0;
        document.getElementById('plan_is_active').checked = data.is_active ?? true;
        document.getElementById('plan_is_popular').checked = data.is_popular ?? false;
    }

    function openPlanModal(mode = 'create', id = null) {
        if (mode === 'edit' && id && packagePlans[id]) {
            const plan = packagePlans[id];
            planModalTitle.textContent = 'Edit Package';
            planSubmitBtn.textContent = 'Update Package';
            planModalForm.action = updateActionTemplate.replace('PLAN_ID', id);
            planMethod.value = 'PATCH';
            planMode.value = 'edit';
            fillPlanForm(plan);
        } else {
            planModalTitle.textContent = 'Add New Package';
            planSubmitBtn.textContent = 'Submit';
            planModalForm.action = @json(route('admin.settings.plans.store'));
            planMethod.value = 'POST';
            planMode.value = 'create';
            fillPlanForm({
                interest_express_limit: '',
                profile_show_limit: '',
                image_upload_limit: '',
                validity_days: '',
                price: '',
                badge: '',
                features_text: '',
                sort_order: 0,
                is_active: true,
                is_popular: false,
            });
        }

        document.body.classList.add('hm-modal-open');
        planModalBackdrop.classList.add('active');
        planModalCard.classList.add('active');
    }

    function closePlanModal() {
        document.body.classList.remove('hm-modal-open');
        planModalBackdrop.classList.remove('active');
        planModalCard.classList.remove('active');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closePlanModal();
        }
    });
</script>
@endpush
