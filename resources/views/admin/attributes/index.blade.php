@extends('layouts.admin')

@section('title', $title)

@section('content')
    @php
        $attributePayload = $attributes->filter(fn ($attribute) => $attribute->exists)->mapWithKeys(function ($attribute) {
            return [
                $attribute->id => [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'sort_order' => $attribute->sort_order ?? 0,
                    'is_active' => (bool) ($attribute->is_active ?? true),
                ],
            ];
        });
    @endphp

    <section class="hm-admin-page-head hm-admin-page-head-split hm-attribute-page-head">
        <div>
            <h1>{{ $title }}</h1>
        </div>
        @if($canManage)
            <button type="button" class="hm-admin-btn light hm-attribute-add-btn" onclick="openAttributeModal('create')">＋ Add New</button>
        @else
            <button type="button" class="hm-admin-btn light hm-attribute-add-btn" disabled title="Run migrations first">＋ Add New</button>
        @endif
    </section>

    @unless($canManage)
        <div class="hm-admin-alert error">Run <strong>php artisan migrate</strong> to enable adding, editing and deleting user attributes.</div>
    @endunless

    <section class="hm-admin-card hm-admin-panel hm-attribute-panel">
        <div class="hm-admin-table-wrap">
            <table class="hm-admin-table hm-package-table hm-attribute-table">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>{{ $columnLabel }}</th>
                        <th class="hm-attribute-action-head">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attributes as $index => $attribute)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="hm-attribute-name-cell">{{ $attribute->name }}</td>
                            <td>
                                <div class="hm-attribute-actions">
                                    @if($canManage && $attribute->exists)
                                        <button type="button" class="hm-admin-btn light hm-attribute-edit-btn" onclick="openAttributeModal('edit', {{ $attribute->id }})">✎ Edit</button>
                                        <form method="POST" action="{{ route('admin.attributes.destroy', ['type' => $type, 'attribute' => $attribute]) }}" onsubmit="return confirm('Delete this {{ strtolower($singular) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="hm-admin-btn light hm-attribute-delete-btn">🗑 Delete</button>
                                        </form>
                                    @else
                                        <button type="button" class="hm-admin-btn light hm-attribute-edit-btn" disabled>✎ Edit</button>
                                        <button type="button" class="hm-admin-btn light hm-attribute-delete-btn" disabled>🗑 Delete</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3"><div class="hm-admin-empty">No {{ strtolower($singular) }} records found. Click “Add New” to create one.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($canManage)
        <div class="hm-modal-backdrop" id="attributeModalBackdrop" onclick="closeAttributeModal()"></div>
        <section class="hm-modal-card hm-attribute-modal" id="attributeModalCard" aria-hidden="true">
            <div class="hm-modal-head">
                <h2 id="attributeModalTitle">Add New {{ $singular }}</h2>
                <button type="button" class="hm-modal-close" onclick="closeAttributeModal()">×</button>
            </div>
            <form method="POST" id="attributeModalForm" action="{{ route('admin.attributes.store', ['type' => $type]) }}" class="hm-package-modal-form">
                @csrf
                <input type="hidden" name="_method" id="attributeMethod" value="POST">

                <div class="hm-admin-field hm-admin-wide">
                    <label>{{ $columnLabel }} <span class="hm-required">*</span></label>
                    <input class="hm-admin-input" type="text" name="name" id="attribute_name" required maxlength="120" autocomplete="off">
                </div>

                <div class="hm-package-modal-inline">
                    <div class="hm-admin-field">
                        <label>Sort Order</label>
                        <input class="hm-admin-input" type="number" name="sort_order" id="attribute_sort_order" min="0" value="0">
                    </div>
                    <div class="hm-admin-field">
                        <label>Status</label>
                        <div class="hm-admin-switch-row hm-package-switch-row">
                            <label><input type="checkbox" name="is_active" id="attribute_is_active" value="1" checked> Enabled</label>
                        </div>
                    </div>
                </div>

                <div class="hm-modal-actions">
                    <button type="submit" class="hm-admin-btn primary hm-package-submit-btn" id="attributeSubmitBtn">Submit</button>
                </div>
            </form>
        </section>
    @endif
@endsection

@push('styles')
<style>
    .hm-attribute-page-head{margin-top:28px;margin-bottom:24px}
    .hm-attribute-page-head h1{font-size:20px;font-weight:600;color:#1f3656}
    .hm-attribute-add-btn{color:#4b3dff;border-color:#4b3dff;background:#fff;padding:8px 14px;border-radius:4px}
    .hm-attribute-panel{padding:0;overflow:hidden;border-radius:6px}
    .hm-attribute-table{min-width:720px}
    .hm-attribute-table thead th{padding:16px 24px;font-size:13px;text-transform:none;letter-spacing:0;font-weight:900}
    .hm-attribute-table tbody td{padding:15px 24px;height:62px;color:#5d6e89;font-weight:800}
    .hm-attribute-name-cell{text-align:center;font-weight:900;color:#556784}
    .hm-attribute-action-head{text-align:right!important}
    .hm-attribute-table tbody td:last-child{text-align:right}
    .hm-attribute-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap}
    .hm-attribute-edit-btn{border-color:#4b3dff;color:#4b3dff;background:#fff;padding:8px 11px;border-radius:4px}
    .hm-attribute-delete-btn{border-color:#ff1f1f;color:#ff1f1f;background:#fff;padding:8px 11px;border-radius:4px}
    .hm-attribute-edit-btn:hover,.hm-attribute-delete-btn:hover{background:#fff;transform:none}
    .hm-attribute-modal{width:min(540px,calc(100vw - 28px))}
</style>
@endpush

@if($canManage)
@push('scripts')
<script>
    const userAttributes = @json($attributePayload);
    const attributeModalBackdrop = document.getElementById('attributeModalBackdrop');
    const attributeModalCard = document.getElementById('attributeModalCard');
    const attributeModalTitle = document.getElementById('attributeModalTitle');
    const attributeModalForm = document.getElementById('attributeModalForm');
    const attributeMethod = document.getElementById('attributeMethod');
    const attributeSubmitBtn = document.getElementById('attributeSubmitBtn');
    const attributeStoreAction = @json(route('admin.attributes.store', ['type' => $type]));
    const attributeUpdateActionTemplate = @json(route('admin.attributes.update', ['type' => $type, 'attribute' => 'ATTRIBUTE_ID']));

    function fillAttributeForm(data = {}) {
        document.getElementById('attribute_name').value = data.name ?? '';
        document.getElementById('attribute_sort_order').value = data.sort_order ?? 0;
        document.getElementById('attribute_is_active').checked = data.is_active ?? true;
    }

    function openAttributeModal(mode = 'create', id = null) {
        if (mode === 'edit' && id && userAttributes[id]) {
            attributeModalTitle.textContent = 'Edit {{ $singular }}';
            attributeSubmitBtn.textContent = 'Update {{ $singular }}';
            attributeModalForm.action = attributeUpdateActionTemplate.replace('ATTRIBUTE_ID', id);
            attributeMethod.value = 'PATCH';
            fillAttributeForm(userAttributes[id]);
        } else {
            attributeModalTitle.textContent = 'Add New {{ $singular }}';
            attributeSubmitBtn.textContent = 'Submit';
            attributeModalForm.action = attributeStoreAction;
            attributeMethod.value = 'POST';
            fillAttributeForm({ sort_order: {{ $attributes->count() + 1 }}, is_active: true });
        }

        document.body.classList.add('hm-modal-open');
        attributeModalBackdrop.classList.add('active');
        attributeModalCard.classList.add('active');
        setTimeout(() => document.getElementById('attribute_name')?.focus(), 80);
    }

    function closeAttributeModal() {
        document.body.classList.remove('hm-modal-open');
        attributeModalBackdrop.classList.remove('active');
        attributeModalCard.classList.remove('active');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAttributeModal();
        }
    });
</script>
@endpush
@endif
