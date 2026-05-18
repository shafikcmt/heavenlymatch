<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'checkout_url',
        'merchant_id',
        'public_key',
        'secret_key',
        'sandbox',
        'instructions',
        'config',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'config' => 'array',
        'sandbox' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function payments()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function getDisplayTypeAttribute(): string
    {
        return match ($this->type) {
            'sslcommerz' => 'SSLCommerz',
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            'redirect' => 'Redirect URL',
            default => 'Manual',
        };
    }
}
