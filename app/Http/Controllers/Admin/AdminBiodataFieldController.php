<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BiodataField;
use App\Models\BiodataSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * PHASE E2 — Admin Biodata Field Control UI.
 *
 * Lets admins govern the biodata registry seeded in E1: reorder/relabel/toggle
 * sections and fields, and create fully custom fields (stored in
 * biodatas.custom_fields JSON, never a new DB column).
 *
 * SAFETY:
 *   - System fields (is_system) cannot be deleted or re-keyed — only deactivated
 *     and relabelled. Their model_column mapping is immutable.
 *   - A section that still contains any system field cannot be deleted.
 *   - Admin-created fields are ALWAYS custom (model_column = null); their field_key
 *     may not collide with a real biodatas column or an existing field.
 */
class AdminBiodataFieldController extends Controller
{
    /** Widgets an admin may pick for a field. */
    private const INPUT_TYPES = [
        'text', 'textarea', 'select', 'multi_select', 'radio', 'checkbox',
        'date', 'number', 'phone', 'email', 'yes_no', 'file', 'repeater',
    ];

    /** Input types whose options array is meaningful. */
    private const OPTION_TYPES = ['select', 'multi_select', 'radio'];

    public function index(): Response
    {
        $sections = BiodataSection::ordered()
            ->with(['fields' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->get()
            ->map(fn (BiodataSection $s) => [
                'id'                => $s->id,
                'key'               => $s->key,
                'title_en'          => $s->title_en,
                'title_bn'          => $s->title_bn,
                'description_en'    => $s->description_en,
                'description_bn'    => $s->description_bn,
                'icon'              => $s->icon,
                'step'              => $s->step,
                'sort_order'        => $s->sort_order,
                'completion_weight' => $s->completion_weight,
                'is_active'         => $s->is_active,
                'show_in_form'      => $s->show_in_form,
                'show_in_profile'   => $s->show_in_profile,
                'show_in_admin'     => $s->show_in_admin,
                // A section is locked from deletion while it holds any system field.
                'has_system_fields' => $s->fields->contains('is_system', true),
                'fields'            => $s->fields->map(fn (BiodataField $f) => [
                    'id'               => $f->id,
                    'section_id'       => $f->section_id,
                    'field_key'        => $f->field_key,
                    'model_column'     => $f->model_column,
                    'is_custom'        => $f->isCustom(),
                    'label_en'         => $f->label_en,
                    'label_bn'         => $f->label_bn,
                    'placeholder_en'   => $f->placeholder_en,
                    'placeholder_bn'   => $f->placeholder_bn,
                    'helper_text_en'   => $f->helper_text_en,
                    'helper_text_bn'   => $f->helper_text_bn,
                    'input_type'       => $f->input_type,
                    'options_en'       => $f->options_en ?? [],
                    'options_bn'       => $f->options_bn ?? [],
                    'default_value'    => $f->default_value,
                    'validation_rules' => $f->validation_rules,
                    'is_required'      => $f->is_required,
                    'is_active'        => $f->is_active,
                    'show_in_form'     => $f->show_in_form,
                    'show_in_profile'  => $f->show_in_profile,
                    'show_in_admin'    => $f->show_in_admin,
                    'is_private'       => $f->is_private,
                    'is_searchable'    => $f->is_searchable,
                    'is_filterable'    => $f->is_filterable,
                    'is_system'        => $f->is_system,
                    'sort_order'       => $f->sort_order,
                    'conditional_logic' => $f->conditional_logic,
                ]),
            ]);

        return Inertia::render('Admin/BiodataFields/Index', [
            'sections'   => $sections,
            'inputTypes' => self::INPUT_TYPES,
        ]);
    }

    // ── Sections ───────────────────────────────────────────────────────────────

    public function storeSection(Request $request): RedirectResponse
    {
        $data = $this->validateSection($request);
        $data['key'] = $this->uniqueSectionKey($data['key'] ?? $data['title_en']);
        $data['sort_order'] = (int) (BiodataSection::max('sort_order') + 1);

        BiodataSection::create($data);

        return back()->with('success', __('admin.bf_section_created'));
    }

    public function updateSection(Request $request, BiodataSection $section): RedirectResponse
    {
        $data = $this->validateSection($request);
        // key is immutable after creation to keep completion/lookup stable.
        unset($data['key']);

        $section->update($data);

        return back()->with('success', __('admin.bf_section_updated'));
    }

    public function destroySection(BiodataSection $section): RedirectResponse
    {
        if ($section->fields()->where('is_system', true)->exists()) {
            return back()->with('error', __('admin.bf_section_locked'));
        }

        // Only custom fields remain → cascade delete is safe (their JSON values
        // in biodatas.custom_fields are simply left orphaned and ignored).
        $section->delete();

        return back()->with('success', __('admin.bf_section_deleted'));
    }

    // ── Fields ─────────────────────────────────────────────────────────────────

    public function storeField(Request $request): RedirectResponse
    {
        $data = $this->validateField($request, null);

        // Admin-created fields are always custom: value lives in custom_fields JSON.
        $data['model_column'] = null;
        $data['is_system']    = false;
        $data['field_key']    = $this->uniqueFieldKey($data['field_key'] ?? $data['label_en']);
        $data['sort_order']   = (int) (BiodataField::where('section_id', $data['section_id'])->max('sort_order') + 1);
        $data = $this->normaliseOptions($data);

        BiodataField::create($data);

        return back()->with('success', __('admin.bf_field_created'));
    }

    public function updateField(Request $request, BiodataField $field): RedirectResponse
    {
        $data = $this->validateField($request, $field);

        // Immutable on update regardless of system/custom: identity + storage mapping.
        unset($data['field_key'], $data['model_column']);

        // System fields keep their widget (must match the real column type).
        if ($field->is_system) {
            unset($data['input_type']);
        }

        $data = $this->normaliseOptions($data);

        $field->update($data);

        return back()->with('success', __('admin.bf_field_updated'));
    }

    public function destroyField(BiodataField $field): RedirectResponse
    {
        if ($field->is_system) {
            return back()->with('error', __('admin.bf_field_locked'));
        }

        $field->delete();

        return back()->with('success', __('admin.bf_field_deleted'));
    }

    // ── Reordering ───────────────────────────────────────────────────────────

    public function reorderSections(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:biodata_sections,id'],
        ]);

        foreach (array_values($validated['ids']) as $i => $id) {
            BiodataSection::where('id', $id)->update(['sort_order' => $i + 1]);
        }

        return back();
    }

    public function reorderFields(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => ['required', 'integer', 'exists:biodata_sections,id'],
            'ids'        => ['required', 'array'],
            'ids.*'      => ['integer', 'exists:biodata_fields,id'],
        ]);

        foreach (array_values($validated['ids']) as $i => $id) {
            BiodataField::where('id', $id)
                ->where('section_id', $validated['section_id'])
                ->update(['sort_order' => $i + 1]);
        }

        return back();
    }

    // ── Validation helpers ─────────────────────────────────────────────────────

    /** @return array<string,mixed> */
    private function validateSection(Request $request): array
    {
        return $request->validate([
            'key'               => ['nullable', 'string', 'max:50', 'alpha_dash'],
            'title_en'          => ['required', 'string', 'max:150'],
            'title_bn'          => ['required', 'string', 'max:150'],
            'description_en'    => ['nullable', 'string', 'max:300'],
            'description_bn'    => ['nullable', 'string', 'max:300'],
            'icon'              => ['nullable', 'string', 'max:50'],
            'step'              => ['nullable', 'integer', 'min:1', 'max:50'],
            'completion_weight' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_active'         => ['boolean'],
            'show_in_form'      => ['boolean'],
            'show_in_profile'   => ['boolean'],
            'show_in_admin'     => ['boolean'],
        ]);
    }

    /** @return array<string,mixed> */
    private function validateField(Request $request, ?BiodataField $field): array
    {
        return $request->validate([
            'section_id'        => ['required', 'integer', 'exists:biodata_sections,id'],
            'field_key'         => ['nullable', 'string', 'max:80', 'alpha_dash'],
            'label_en'          => ['required', 'string', 'max:200'],
            'label_bn'          => ['required', 'string', 'max:200'],
            'placeholder_en'    => ['nullable', 'string', 'max:250'],
            'placeholder_bn'    => ['nullable', 'string', 'max:250'],
            'helper_text_en'    => ['nullable', 'string', 'max:300'],
            'helper_text_bn'    => ['nullable', 'string', 'max:300'],
            'input_type'        => ['required', Rule::in(self::INPUT_TYPES)],
            'options_en'        => ['nullable', 'array'],
            'options_en.*.value' => ['required_with:options_en', 'string', 'max:120'],
            'options_en.*.label' => ['required_with:options_en', 'string', 'max:200'],
            'options_bn'        => ['nullable', 'array'],
            'options_bn.*.value' => ['required_with:options_bn', 'string', 'max:120'],
            'options_bn.*.label' => ['required_with:options_bn', 'string', 'max:200'],
            'default_value'     => ['nullable', 'string', 'max:250'],
            'validation_rules'  => ['nullable', 'string', 'max:250'],
            'is_required'       => ['boolean'],
            'is_active'         => ['boolean'],
            'show_in_form'      => ['boolean'],
            'show_in_profile'   => ['boolean'],
            'show_in_admin'     => ['boolean'],
            'is_private'        => ['boolean'],
            'is_searchable'     => ['boolean'],
            'is_filterable'     => ['boolean'],
            'conditional_logic'          => ['nullable', 'array'],
            'conditional_logic.field'    => ['nullable', 'string', 'max:80'],
            'conditional_logic.operator' => ['nullable', 'string', 'max:10'],
            'conditional_logic.value'    => ['nullable'],
        ]);
    }

    /** Drop option arrays for non-option widgets and empty conditional logic. */
    private function normaliseOptions(array $data): array
    {
        if (! in_array($data['input_type'] ?? null, self::OPTION_TYPES, true)) {
            $data['options_en'] = null;
            $data['options_bn'] = null;
        }

        if (empty($data['conditional_logic']['field'] ?? null)) {
            $data['conditional_logic'] = null;
        }

        return $data;
    }

    /** Slugged, collision-free section key. */
    private function uniqueSectionKey(string $seed): string
    {
        $base = Str::slug($seed, '_') ?: 'section';
        $key  = $base;
        $i    = 1;

        while (BiodataSection::where('key', $key)->exists()) {
            $key = $base.'_'.(++$i);
        }

        return Str::limit($key, 50, '');
    }

    /**
     * Collision-free custom field key. Must not match a real biodatas column
     * (so custom values never shadow a mapped field) nor an existing field_key.
     */
    private function uniqueFieldKey(string $seed): string
    {
        $base = Str::slug($seed, '_') ?: 'field';
        if (Schema::hasColumn('biodatas', $base)) {
            $base = 'cf_'.$base;
        }

        $key = $base;
        $i   = 1;
        while (BiodataField::where('field_key', $key)->exists() || Schema::hasColumn('biodatas', $key)) {
            $key = $base.'_'.(++$i);
        }

        return Str::limit($key, 80, '');
    }
}
