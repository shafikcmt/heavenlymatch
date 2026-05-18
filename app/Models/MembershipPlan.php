<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'duration_months',
        'validity_days',
        'interest_express_limit',
        'profile_show_limit',
        'image_upload_limit',
        'price',
        'currency',
        'features',
        'badge',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'duration_months' => 'integer',
        'validity_days' => 'integer',
        'interest_express_limit' => 'integer',
        'profile_show_limit' => 'integer',
        'image_upload_limit' => 'integer',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function payments()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        $amount = (float) $this->price;
        $formatted = $amount == floor($amount)
            ? number_format($amount, 0)
            : number_format($amount, 2);

        $currency = strtoupper((string) ($this->currency ?: 'BDT'));

        if (in_array($currency, ['BDT', 'TK', 'TAKA'], true)) {
            return '৳' . $formatted;
        }

        return trim($currency . ' ' . $formatted);
    }

    public function getDurationLabelAttribute(): string
    {
        $days = (int) ($this->validity_days ?? 0);

        if ($days === -1) {
            return 'Unlimited';
        }

        if ($days > 0) {
            return $days . ' Days';
        }

        if ((int) $this->duration_months === 12) {
            return '1 Year';
        }

        if ((int) $this->duration_months > 12 && (int) $this->duration_months % 12 === 0) {
            return ((int) $this->duration_months / 12) . ' Years';
        }

        return $this->duration_months . ' Months';
    }

    public function getInterestExpressLabelAttribute(): string
    {
        return $this->formatLimit($this->interest_express_limit);
    }

    public function getProfileShowLabelAttribute(): string
    {
        return $this->formatLimit($this->profile_show_limit);
    }

    public function getImageUploadLabelAttribute(): string
    {
        return $this->formatLimit($this->image_upload_limit);
    }

    private function formatLimit($value): string
    {
        $value = (int) ($value ?? 0);

        return $value === -1 ? 'Unlimited' : (string) $value;
    }
}
