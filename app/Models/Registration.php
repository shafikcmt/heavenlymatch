<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // ✅ Important for Auth
use Illuminate\Notifications\Notifiable;

class Registration extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
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
        'remember_token', // ✅ For auth session
        'email_verification_code',
        'mobile_verification_code',
    ];

    protected $casts = [
        'is_email_verified' => 'boolean',
        'is_mobile_verified' => 'boolean',
        'email_verified_at' => 'datetime', // ✅ optional if using Laravel email verification
    ];

    public function biodata()
    {
        return $this->hasOne(Biodata::class, 'registration_id');
    }
}
