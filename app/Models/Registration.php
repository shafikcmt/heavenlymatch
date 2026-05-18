<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Registration extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'registration_id',
        'name',
        'gender',
        'religion',
        'marital_status',
        'blood_group',
        'profile_for',
        'preferred_language',
        'email',
        'email_verification_code',
        'email_verification_token',
        'email_verification_sent_at',
        'is_email_verified',
        'country_code',
        'mobile_number',
        'mobile_verification_code',
        'is_mobile_verified',
        'password',
        'terms_accepted_at',
        'last_login_at',
        'role',
        'is_admin',
        'account_status',
        'blocked_at',
        'blocked_reason',
        'membership_plan_id',
        'membership_plan_name',
        'membership_status',
        'membership_started_at',
        'membership_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
        'mobile_verification_code',
    ];

    protected $casts = [
        'is_email_verified' => 'boolean',
        'is_mobile_verified' => 'boolean',
        'is_admin' => 'boolean',
        'email_verified_at' => 'datetime',
        'email_verification_sent_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
        'last_login_at' => 'datetime',
        'blocked_at' => 'datetime',
        'membership_started_at' => 'datetime',
        'membership_expires_at' => 'datetime',
    ];

    public function biodata()
    {
        return $this->hasOne(Biodata::class, 'registration_id', 'registration_id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentTransaction::class, 'registration_id');
    }

    public function activePayment()
    {
        return $this->hasOne(PaymentTransaction::class, 'registration_id')->where('status', 'paid')->latestOfMany();
    }

    public function hasActiveMembership(): bool
    {
        return ($this->membership_status === 'active')
            && $this->membership_expires_at
            && $this->membership_expires_at->isFuture();
    }

    public function isAdmin(): bool
    {
        return (bool) ($this->is_admin ?? false) || (($this->role ?? 'user') === 'admin');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! empty($model->registration_id)) {
                return;
            }

            $last = Registration::latest('id')->first();
            $next = $last ? $last->id + 1 : 1;
            $model->registration_id = 'HM' . str_pad($next, 6, '0', STR_PAD_LEFT);
        });
    }
}
