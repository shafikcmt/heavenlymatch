<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['user_a_id', 'user_b_id', 'connection_request_id', 'is_active', 'last_message_at'];

    protected $casts = ['is_active' => 'boolean', 'last_message_at' => 'datetime'];

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function userA(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'user_a_id', 'registration_id');
    }

    public function userB(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'user_b_id', 'registration_id');
    }

    public function latestMessage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function otherParty(string $myId): ?Registration
    {
        $otherId = $this->user_a_id === $myId ? $this->user_b_id : $this->user_a_id;
        return Registration::where('registration_id', $otherId)->first();
    }
}
