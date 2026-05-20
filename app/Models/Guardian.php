<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $fillable = [
        'registration_id',
        'guardian_name',
        'relationship',
        'mobile',
        'email',
        'notification_level',
        'is_verified',
        'verification_otp',
        'otp_sent_at',
        'otp_verified_at',
    ];

    protected $hidden = ['verification_otp'];

    protected $casts = [
        'is_verified'     => 'boolean',
        'otp_sent_at'     => 'datetime',
        'otp_verified_at' => 'datetime',
    ];

    public function registration(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'registration_id', 'registration_id');
    }

    public function wantsNotification(string $event = 'connection_requests'): bool
    {
        return match ($this->notification_level) {
            'all_actions'               => true,
            'connection_requests_only'  => $event === 'connection_requests',
            default                     => false,
        };
    }
}
