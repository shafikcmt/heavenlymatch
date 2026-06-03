<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerificationCode extends Model
{
    protected $fillable = [
        'email',
        'code_hash',
        'expires_at',
        'attempts',
        'verified_at',
        'ip_address',
        'user_agent',
    ];

    protected $hidden = [
        'code_hash',   // never expose the hash
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
        'attempts'    => 'integer',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
