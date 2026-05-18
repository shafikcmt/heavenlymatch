<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class UserAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public const TYPES = [
        'religion' => [
            'title' => 'All Religion',
            'singular' => 'Religion',
            'column' => 'Name',
            'defaults' => ['Islam', 'Christian', 'Buddhist', 'Hindu'],
        ],
        'blood-group' => [
            'title' => 'Blood Groups',
            'singular' => 'Blood Group',
            'column' => 'Name',
            'defaults' => ['A+', 'A-'],
        ],
        'marital-status' => [
            'title' => 'Marital Status',
            'singular' => 'Marital Status',
            'column' => 'Title',
            'defaults' => ['Single', 'Married', 'Divorced', 'Widow'],
        ],
    ];

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function isValidType(string $type): bool
    {
        return array_key_exists($type, self::TYPES);
    }

    public static function meta(string $type): array
    {
        abort_unless(self::isValidType($type), 404);

        return self::TYPES[$type];
    }

    public static function defaultsFor(string $type): array
    {
        return self::TYPES[$type]['defaults'] ?? [];
    }

    public static function itemsFor(string $type, bool $activeOnly = false)
    {
        if (! self::isValidType($type)) {
            return collect();
        }

        if (! Schema::hasTable('user_attributes')) {
            return collect(self::defaultsFor($type))->map(function ($name, $index) use ($type) {
                return new self([
                    'type' => $type,
                    'name' => $name,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
            });
        }

        $query = self::query()->ofType($type)->orderBy('sort_order')->orderBy('name');

        if ($activeOnly) {
            $query->active();
        }

        $items = $query->get();

        if ($items->isEmpty() && ! $activeOnly) {
            return collect(self::defaultsFor($type))->map(function ($name, $index) use ($type) {
                return new self([
                    'type' => $type,
                    'name' => $name,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
            });
        }

        return $items;
    }

    public static function optionsFor(string $type): array
    {
        return self::itemsFor($type, true)->pluck('name')->values()->all();
    }
}
