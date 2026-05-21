<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'data',
        'channel',
        'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'user_id', 'registration_id');
    }

    /**
     * Create a web notification for a user.
     */
    public static function send(string $userId, string $type, string $title, string $body, array $data = []): void
    {
        static::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data,
            'channel' => 'web',
        ]);
    }
}
