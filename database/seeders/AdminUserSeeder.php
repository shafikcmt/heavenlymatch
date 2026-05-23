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
        // SECURITY NOTE: Change password immediately after first deploy.
        // This seeder is for local/dev only. Never use default credentials in production.
        $admin = Registration::firstOrNew(['email' => 'admin@heavenlymatch.test']);

        $mobile = $admin->mobile_number ?: '01700000000';

        if (! $admin->exists && Registration::where('mobile_number', $mobile)->exists()) {
            $mobile = '017' . random_int(10000000, 99999999);
        }

        // Use fill() for mass-assignable fields
        $admin->fill([
            'name'                   => 'HeavenlyMatch Admin',
            'gender'                 => 'male',
            'profile_created_for'    => 'self',
            'looking_for'            => 'bride',
            'preferred_language'     => 'en',
            'platform_mode'          => 'general',
            'country_code'           => '+880',
            'mobile_number'          => $mobile,
            'is_email_verified'      => true,
            'is_mobile_verified'     => true,
            'email_verification_code'  => null,
            'email_verification_token' => null,
            'password'               => Hash::make('admin123'),
            'terms_accepted_at'      => now(),
            'last_login_at'          => null,
        ]);

        // forceFill() for non-mass-assignable status/role fields
        $admin->forceFill([
            'email_verified_at' => now(),
            'role'              => 'admin',
            'is_admin'          => true,
            'account_status'    => 'active',
        ]);

        if (! $admin->registration_id) {
            $admin->registration_id = 'HMADMIN' . Str::upper(Str::random(3));
        }

        $admin->save();

        $this->command?->info('Admin seeded: admin@heavenlymatch.test / admin123 — CHANGE THIS PASSWORD!');
    }
}
