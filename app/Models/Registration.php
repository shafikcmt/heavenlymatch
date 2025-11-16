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
        'registration_id', // ✅ Add custom registration ID
        'looking_for',
        'name',
        'gender',
        'email',
        'email_verification_code',
        'is_email_verified',
        'country_code',
        'mobile_number',
        'mobile_verification_code',
        'is_mobile_verified',
        'password',
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
        'email_verified_at' => 'datetime',
    ];

    // ✅ One-to-One relationship with Biodata
     public function biodata()
        {
            return $this->hasOne(Biodata::class, 'registration_id', 'registration_id');
        }

    // ✅ Auto-generate short registration ID on creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = Registration::latest('id')->first();
            $next = $last ? $last->id + 1 : 1;

            $prefix = 'HM'; // HeavenlyMatch initials
            $model->registration_id = $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
            // Example: HM000001, HM000002, etc.
        });
    }
}
