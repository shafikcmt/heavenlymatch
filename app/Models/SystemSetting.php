<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public const CACHE_KEY = 'hm_system_settings';

    public static function allSettings(): array
    {
        if (! Schema::hasTable('system_settings')) {
            return [];
        }

        return Cache::remember(self::CACHE_KEY, now()->addMinutes(10), function () {
            return self::query()->pluck('value', 'key')->toArray();
        });
    }

    public static function get(string $key, $default = null)
    {
        $settings = self::allSettings();

        $value = array_key_exists($key, $settings) && $settings[$key] !== null && $settings[$key] !== ''
            ? $settings[$key]
            : $default;

        // The default starter app name can remain in the database if APP_NAME was never changed.
        // Keep the public UI branded as HeavenlyMatch.
        if (in_array($key, ['general.site_name', 'notification.mail_from_name', 'seo.meta_title'], true)
            && is_string($value)
            && trim(strtolower($value)) === 'laravel') {
            return 'HeavenlyMatch';
        }

        return $value;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default ? '1' : '0');

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function setValue(string $key, $value, string $type = 'string'): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $type = 'json';
        }

        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );

        Cache::forget(self::CACHE_KEY);
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $payload) {
            if (is_array($payload) && array_key_exists('value', $payload)) {
                self::setValue($key, $payload['value'], $payload['type'] ?? 'string');
            } else {
                self::setValue($key, $payload);
            }
        }
    }
}
