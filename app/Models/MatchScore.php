<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchScore extends Model
{
    protected $fillable = [
        'user_id',
        'candidate_id',
        'total_score',
        'score_breakdown',
        'computed_at',
    ];

    protected $casts = [
        'score_breakdown' => 'array',
        'computed_at'     => 'datetime',
        'total_score'     => 'integer',
    ];

    public $timestamps = false;

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'user_id', 'registration_id');
    }

    public function candidate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Registration::class, 'candidate_id', 'registration_id');
    }

    public function candidateBiodata(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            Biodata::class,
            Registration::class,
            'registration_id', // FK on registrations pointing to match_scores.candidate_id
            'registration_id', // FK on biodatas
            'candidate_id',    // local key on match_scores
            'registration_id'  // local key on registrations
        );
    }
}
