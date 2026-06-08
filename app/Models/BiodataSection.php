<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PHASE E1 — Admin-governed biodata section.
 *
 * A section groups biodata fields (mirrors a wizard step). Admins can toggle
 * activity/visibility, reorder, and re-label without touching code or columns.
 */
class BiodataSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title_en',
        'title_bn',
        'description_en',
        'description_bn',
        'icon',
        'step',
        'sort_order',
        'completion_weight',
        'is_active',
        'show_in_form',
        'show_in_profile',
        'show_in_admin',
    ];

    protected $casts = [
        'step'              => 'integer',
        'sort_order'        => 'integer',
        'completion_weight' => 'integer',
        'is_active'         => 'boolean',
        'show_in_form'      => 'boolean',
        'show_in_profile'   => 'boolean',
        'show_in_admin'     => 'boolean',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(BiodataField::class, 'section_id')->orderBy('sort_order');
    }

    /** Only fields the wizard form should render. */
    public function formFields(): HasMany
    {
        return $this->fields()
            ->where('is_active', true)
            ->where('show_in_form', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Localised title for the given locale ('bn' or anything else → en). */
    public function title(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn' && $this->title_bn ? $this->title_bn : $this->title_en;
    }

    public function description(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        return $locale === 'bn' && $this->description_bn ? $this->description_bn : $this->description_en;
    }
}
