<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileBoost extends Model
{
    protected $table = 'profile_boosts';

    protected $fillable = [
        'user_id',
        'transaction_id',
        'duration_hours',
        'started_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'user_id', 'registration_id');
    }
}
