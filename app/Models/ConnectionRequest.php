<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConnectionRequest extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
        'initial_message',
        'guardian_notified',
        'guardian_notified_at',
        'responded_at',
    ];

    protected $casts = [
        'guardian_notified'    => 'boolean',
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

    public function conversation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Conversation::class, 'connection_request_id');
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }
}
