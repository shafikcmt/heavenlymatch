<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoAccessRequest extends Model
{
    protected $fillable = [
        'requester_id',
        'profile_id',
        'status',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function requester(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'requester_id', 'registration_id');
    }

    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'profile_id', 'registration_id');
    }

    public function isGranted(): bool
    {
        return $this->status === 'granted';
    }
}
