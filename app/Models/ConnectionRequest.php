<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConnectionRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
        'message',
        'guardian_pending',
        'guardian_notified_at',
        'responded_at',
    ];

    protected $casts = [
        'guardian_pending'     => 'boolean',
        'guardian_notified_at' => 'datetime',
        'responded_at'         => 'datetime',
    ];

    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'sender_id', 'registration_id');
    }

    public function receiver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'receiver_id', 'registration_id');
    }

    public function scopePending($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', 'accepted');
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }
}
