<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'email',
        'email_verification_code',
        'is_email_verified',
        'country_code',
        'mobile_number',
        'mobile_verification_code',
        'is_mobile_verified',
        'password',
    ];

        public function biodata()
            {
                return $this->hasOne(Biodata::class, 'registration_id');
            }

}
