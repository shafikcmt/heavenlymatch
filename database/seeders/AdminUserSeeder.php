<?php

namespace Database\Seeders;

use App\Models\Registration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Registration::firstOrNew(['email' => 'admin@heavenlymatch.test']);
        $mobile = $admin->mobile_number ?: '01700000000';

        if (! $admin->exists && Registration::where('mobile_number', $mobile)->exists()) {
            $mobile = '017' . random_int(10000000, 99999999);
        }

        $admin->fill([
            'name' => 'HeavenlyMatch Admin',
            'gender' => 'male',
            'profile_for' => 'self',
            'preferred_language' => 'en',
            'country_code' => '+880',
            'mobile_number' => $mobile,
            'is_email_verified' => true,
            'is_mobile_verified' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'terms_accepted_at' => now(),
            'role' => 'admin',
            'is_admin' => true,
            'account_status' => 'active',
        ]);

        if (! $admin->registration_id) {
            $admin->registration_id = 'HMADMIN' . Str::upper(Str::random(3));
        }

        $admin->save();
    }
}
