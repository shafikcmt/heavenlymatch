<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
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
        'sender_number',
        'screenshot_path',
        'reference_note',
        'payload',
        'paid_at',
        'expires_at',
    ];

    protected $casts = [
        'payload'        => 'array',
        'amount'         => 'decimal:2',
        'duration_months'=> 'integer',
        'paid_at'        => 'datetime',
        'expires_at'     => 'datetime',
        'reviewed_at'    => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'transaction_no';
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id', 'registration_id');
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

        return '৳' . $formatted;
    }

    /** Scope: only transactions that have been submitted by the user (have sender details). */
    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('external_transaction_id');
    }

    /** Scope: transactions awaiting admin review. */
    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending')->submitted();
    }
}
