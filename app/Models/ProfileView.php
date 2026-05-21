<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileView extends Model
{
    public $timestamps = false;

    protected $fillable = ['viewer_id', 'profile_id', 'ip_address', 'viewed_at'];

    protected $casts = ['viewed_at' => 'datetime'];

    public function viewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'viewer_id', 'registration_id');
    }

    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'profile_id', 'registration_id');
    }
}
