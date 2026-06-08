<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PHASE E1 — Admin-governed biodata field.
 *
 * A field either maps to a real `biodatas` column (model_column set) or is a
 * fully custom admin-created field (model_column NULL → value lives in
 * biodatas.custom_fields JSON, keyed by field_key).
 */
class BiodataField extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'field_key',
        'model_column',
        'label_en',
        'label_bn',
        'placeholder_en',
        'placeholder_bn',
        'helper_text_en',
        'helper_text_bn',
        'input_type',
        'options_en',
        'options_bn',
        'default_value',
        'validation_rules',
        'is_required',
        'is_active',
        'show_in_form',
        'show_in_profile',
        'show_in_admin',
        'is_private',
        'is_searchable',
        'is_filterable',
        'is_system',
        'sort_order',
        'conditional_logic',
        'profile_display_format',
    ];

    protected $casts = [
        'options_en'        => 'array',
        'options_bn'        => 'array',
        'conditional_logic' => 'array',
        'is_required'       => 'boolean',
        'is_active'         => 'boolean',
        'show_in_form'      => 'boolean',
        'show_in_profile'   => 'boolean',
        'show_in_admin'     => 'boolean',
        'is_private'        => 'boolean',
        'is_searchable'     => 'boolean',
        'is_filterable'     => 'boolean',
        'is_system'         => 'boolean',
        'sort_order'        => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(BiodataSection::class, 'section_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForForm(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('show_in_form', true);
    }

    public function scopeForProfile(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('show_in_profile', true);
    }

    /** True when the field stores its value in custom_fields JSON, not a real column. */
    public function isCustom(): bool
    {
        return empty($this->model_column);
    }

    public function label(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn' && $this->label_bn ? $this->label_bn : $this->label_en;
    }

    public function placeholder(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn' && $this->placeholder_bn ? $this->placeholder_bn : $this->placeholder_en;
    }

    public function helperText(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn' && $this->helper_text_bn ? $this->helper_text_bn : $this->helper_text_en;
    }

    /** Localised options list ([{value,label}, ...]). */
    public function options(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        return ($locale === 'bn' && ! empty($this->options_bn))
            ? (array) $this->options_bn
            : (array) ($this->options_en ?? []);
    }

    /** Resolve this field's stored value from a biodata model (column or custom JSON). */
    public function valueFrom(?Biodata $biodata)
    {
        if (! $biodata) {
            return null;
        }

        if ($this->isCustom()) {
            return data_get($biodata->custom_fields, $this->field_key);
        }

        return $biodata->{$this->model_column} ?? null;
    }
}
