<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Biodata;
use App\Models\BiodataField;
use App\Models\BiodataSection;
use Illuminate\Support\Collection;

/**
 * PHASE E1 — Reads the admin-governed biodata field registry.
 *
 * Single source of truth for "which sections/fields are active, where they show,
 * what conditional logic gates them, and how complete a biodata is" — derived
 * from the biodata_sections / biodata_fields tables rather than hardcoded lists.
 *
 * Falls back gracefully: if the registry tables are empty (not yet seeded), the
 * caller can keep using the legacy ProfileCompletionService. Methods here never
 * throw on an unseeded registry — they just return empty collections / 0.
 */
class BiodataFieldService
{
    /** Cached eager load so repeated calls in one request are cheap. */
    private ?Collection $sectionsCache = null;

    /**
     * All active sections (ordered) with their active fields eager-loaded.
     *
     * @return Collection<int,BiodataSection>
     */
    public function sections(): Collection
    {
        if ($this->sectionsCache !== null) {
            return $this->sectionsCache;
        }

        if (! $this->registryReady()) {
            return $this->sectionsCache = collect();
        }

        return $this->sectionsCache = BiodataSection::query()
            ->active()
            ->ordered()
            ->with(['fields' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->get();
    }

    /**
     * Sections shaped for the wizard form (only form-visible sections + fields).
     * Each field is filtered through conditional logic against $context (e.g. gender).
     *
     * @param  array<string,mixed>  $context  values used to evaluate conditional_logic
     * @return Collection<int,array<string,mixed>>
     */
    public function formSchema(array $context = [], ?string $locale = null): Collection
    {
        $locale ??= app()->getLocale();

        return $this->sections()
            ->filter(fn (BiodataSection $s) => $s->show_in_form)
            ->map(fn (BiodataSection $s) => [
                'key'         => $s->key,
                'step'        => $s->step,
                'title'       => $s->title($locale),
                'description' => $s->description($locale),
                'icon'        => $s->icon,
                'fields'      => $s->fields
                    ->where('show_in_form', true)
                    ->filter(fn (BiodataField $f) => $this->passesConditional($f, $context))
                    ->map(fn (BiodataField $f) => $this->presentField($f, $locale))
                    ->values(),
            ])
            ->values();
    }

    /**
     * Sections/fields visible on a public profile for a given viewer.
     * Private fields are dropped unless $viewerIsOwnerOrAdmin.
     *
     * @return Collection<int,array<string,mixed>>
     */
    public function profileSchema(?Biodata $biodata, bool $viewerIsOwnerOrAdmin = false, ?string $locale = null): Collection
    {
        $locale ??= app()->getLocale();
        $context = $this->contextFor($biodata);

        return $this->sections()
            ->filter(fn (BiodataSection $s) => $s->show_in_profile)
            ->map(function (BiodataSection $s) use ($biodata, $viewerIsOwnerOrAdmin, $locale, $context) {
                $fields = $s->fields
                    ->where('show_in_profile', true)
                    ->filter(fn (BiodataField $f) => $viewerIsOwnerOrAdmin || ! $f->is_private)
                    ->filter(fn (BiodataField $f) => $this->passesConditional($f, $context))
                    ->map(fn (BiodataField $f) => array_merge(
                        $this->presentField($f, $locale),
                        ['value' => $f->valueFrom($biodata)],
                    ))
                    // Only show fields that actually have a value on a profile.
                    ->filter(fn (array $f) => $this->filled($f['value']))
                    ->values();

                return [
                    'key'    => $s->key,
                    'title'  => $s->title($locale),
                    'icon'   => $s->icon,
                    'fields' => $fields,
                ];
            })
            // Drop empty sections from the profile view.
            ->filter(fn (array $s) => $s['fields']->isNotEmpty())
            ->values();
    }

    /**
     * Validation rules array keyed by storage key (model_column or custom.{field_key}),
     * for active form fields that pass conditional logic.
     *
     * @return array<string,string>
     */
    public function validationRules(array $context = []): array
    {
        $rules = [];

        foreach ($this->sections() as $section) {
            if (! $section->show_in_form) {
                continue;
            }

            foreach ($section->fields as $field) {
                if (! $field->show_in_form || ! $this->passesConditional($field, $context)) {
                    continue;
                }

                $key = $this->storageKey($field);
                $parts = [];

                $parts[] = $field->is_required ? 'required' : 'nullable';
                if ($field->validation_rules) {
                    $parts[] = $field->validation_rules;
                }

                $rules[$key] = implode('|', $parts);
            }
        }

        return $rules;
    }

    /**
     * Active fields flagged searchable/filterable — for the discovery filter UI.
     *
     * @return Collection<int,BiodataField>
     */
    public function filterableFields(): Collection
    {
        return $this->sections()
            ->flatMap(fn (BiodataSection $s) => $s->fields)
            ->filter(fn (BiodataField $f) => $f->is_filterable)
            ->values();
    }

    /**
     * Completion percentage from the registry: weighted by each section's
     * required fields being filled. Mirrors the 10×10 model but data-driven.
     *
     * @return array{percentage:int, completed_sections:array<int,string>, missing_sections:array<int,string>}
     */
    public function completion(?Biodata $biodata): array
    {
        $sections = $this->sections();

        if ($sections->isEmpty() || ! $biodata) {
            return ['percentage' => 0, 'completed_sections' => [], 'missing_sections' => $sections->pluck('key')->all()];
        }

        $context     = $this->contextFor($biodata);
        $totalWeight = 0;
        $earned      = 0;
        $completed   = [];
        $missing     = [];

        foreach ($sections as $section) {
            $weight = max(1, (int) $section->completion_weight);
            $totalWeight += $weight;

            $required = $section->fields
                ->where('is_required', true)
                ->filter(fn (BiodataField $f) => $this->passesConditional($f, $context));

            // A section with no required fields counts complete once any of its fields is filled.
            $isComplete = $required->isNotEmpty()
                ? $required->every(fn (BiodataField $f) => $this->filled($f->valueFrom($biodata)))
                : $section->fields->contains(fn (BiodataField $f) => $this->filled($f->valueFrom($biodata)));

            if ($isComplete) {
                $earned += $weight;
                $completed[] = $section->key;
            } else {
                $missing[] = $section->key;
            }
        }

        $percentage = $totalWeight > 0 ? (int) round($earned / $totalWeight * 100) : 0;

        return [
            'percentage'         => $percentage,
            'completed_sections' => $completed,
            'missing_sections'   => $missing,
        ];
    }

    /**
     * Admin-created CUSTOM fields (model_column null) to render in the wizard,
     * each tagged with the wizard step it belongs to. Existing hardcoded fields
     * are unaffected — these are appended to their section's step.
     *
     * @return Collection<int,array<string,mixed>>
     */
    public function customWizardFields(?string $locale = null): Collection
    {
        $locale ??= app()->getLocale();

        return $this->activeCustomFields(formVisible: true)
            ->map(fn (BiodataField $f) => array_merge(
                $this->presentField($f, $locale),
                [
                    'step'             => $this->effectiveStep($f),
                    'validation_rules' => $f->validation_rules,
                ],
            ))
            ->values();
    }

    /**
     * Raw custom field models for one wizard step — used server-side to validate
     * and persist their submitted values into biodatas.custom_fields.
     *
     * @return Collection<int,BiodataField>
     */
    public function customFieldsForStep(int $step): Collection
    {
        return $this->activeCustomFields(formVisible: true)
            ->filter(fn (BiodataField $f) => $this->effectiveStep($f) === $step)
            ->values();
    }

    /**
     * Custom field values to show on a profile (respecting privacy + visibility).
     *
     * @return Collection<int,array<string,mixed>>
     */
    public function customProfileFields(?Biodata $biodata, bool $viewerIsOwnerOrAdmin = false, ?string $locale = null): Collection
    {
        if (! $biodata) {
            return collect();
        }

        $locale ??= app()->getLocale();

        return $this->activeCustomFields(formVisible: false)
            ->filter(fn (BiodataField $f) => $f->show_in_profile)
            ->filter(fn (BiodataField $f) => $viewerIsOwnerOrAdmin || ! $f->is_private)
            ->map(fn (BiodataField $f) => [
                'key'        => $f->field_key,
                'label'      => $f->label($locale),
                'value'      => $f->valueFrom($biodata),
                'input_type' => $f->input_type,
            ])
            ->filter(fn (array $r) => $this->filled($r['value']))
            ->values();
    }

    /** Wizard step a custom field lands on: its section's step, clamped to 1..9. */
    public function effectiveStep(BiodataField $field): int
    {
        $step = $field->section?->step;

        return (! $step || $step < 1 || $step > 9) ? 9 : (int) $step;
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    /**
     * Active custom fields (model_column null) inside active sections.
     *
     * @return Collection<int,BiodataField>
     */
    private function activeCustomFields(bool $formVisible): Collection
    {
        if (! $this->registryReady()) {
            return collect();
        }

        return BiodataField::query()
            ->where('is_active', true)
            ->whereNull('model_column')
            ->when($formVisible, fn ($q) => $q->where('show_in_form', true))
            ->with('section')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(function (BiodataField $f) use ($formVisible) {
                if (! $f->section || ! $f->section->is_active) {
                    return false;
                }

                return $formVisible ? $f->section->show_in_form : true;
            })
            ->values();
    }

    /** Where a field's submitted value should be stored / read from. */
    private function storageKey(BiodataField $field): string
    {
        return $field->isCustom() ? "custom_fields.{$field->field_key}" : $field->model_column;
    }

    /** Serialise a field for the frontend (no stored value). */
    private function presentField(BiodataField $field, string $locale): array
    {
        return [
            'key'            => $field->field_key,
            'column'         => $field->model_column,
            'storage_key'    => $this->storageKey($field),
            'is_custom'      => $field->isCustom(),
            'label'          => $field->label($locale),
            'placeholder'    => $field->placeholder($locale),
            'helper'         => $field->helperText($locale),
            'input_type'     => $field->input_type,
            'options'        => $field->options($locale),
            'default'        => $field->default_value,
            'required'       => $field->is_required,
            'private'        => $field->is_private,
            'display_format' => $field->profile_display_format,
        ];
    }

    /**
     * Evaluate a field's conditional_logic against a context map.
     * Shape: {field, operator, value}. Unknown/empty logic → always passes.
     * Supported operators: =, !=, in, not_in, filled, empty.
     */
    private function passesConditional(BiodataField $field, array $context): bool
    {
        $logic = $field->conditional_logic;

        if (empty($logic) || empty($logic['field'])) {
            return true;
        }

        $actual   = $context[$logic['field']] ?? null;
        $operator = $logic['operator'] ?? '=';
        $expected = $logic['value'] ?? null;

        return match ($operator) {
            '='      => $actual == $expected,
            '!='     => $actual != $expected,
            'in'     => in_array($actual, (array) $expected, false),
            'not_in' => ! in_array($actual, (array) $expected, false),
            'filled' => $this->filled($actual),
            'empty'  => ! $this->filled($actual),
            default  => true,
        };
    }

    /**
     * Build the context used for conditional logic from a biodata + its owner.
     * Includes gender (from registration) plus every biodata attribute so rules
     * can branch on e.g. marital_status.
     *
     * @return array<string,mixed>
     */
    private function contextFor(?Biodata $biodata): array
    {
        if (! $biodata) {
            return [];
        }

        $context = $biodata->attributesToArray();
        $context['gender'] = $biodata->registration->gender ?? null;

        return $context;
    }

    private function filled($value): bool
    {
        if (is_array($value)) {
            return ! empty($value);
        }

        return ! is_null($value) && $value !== '';
    }

    /** The registry is usable only once both tables exist (post-migrate). */
    private function registryReady(): bool
    {
        static $ready = null;

        if ($ready === null) {
            $ready = \Illuminate\Support\Facades\Schema::hasTable('biodata_sections')
                && \Illuminate\Support\Facades\Schema::hasTable('biodata_fields');
        }

        return $ready;
    }
}
