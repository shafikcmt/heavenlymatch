<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileReport extends Model
{
    protected $table = 'profile_reports';

    protected $fillable = [
        'reporter_id', 'reported_id', 'reason', 'details', 'evidence',
        'status', 'resolved_by', 'resolution_note', 'resolved_at',
    ];

    protected $casts = [
        'evidence'    => 'array',
        'resolved_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(Registration::class, 'reporter_id', 'registration_id');
    }

    public function reported()
    {
        return $this->belongsTo(Registration::class, 'reported_id', 'registration_id');
    }
}
