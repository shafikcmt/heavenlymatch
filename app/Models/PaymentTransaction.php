<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'registration_code',
        'membership_plan_id',
        'payment_gateway_id',
        'transaction_no',
        'external_transaction_id',
        'plan_name',
        'gateway_name',
        'duration_months',
        'amount',
        'currency',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'redirect_url',
        'reference_note',
        'payload',
        'paid_at',
        'expires_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'amount' => 'decimal:2',
        'duration_months' => 'integer',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'transaction_no';
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }

    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        $amount = (float) $this->amount;
        $formatted = $amount == floor($amount)
            ? number_format($amount, 0)
            : number_format($amount, 2);

        return trim(($this->currency ?: 'BDT') . ' ' . $formatted);
    }
}
