<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Idempotent biodata field registry (Phase E1) — seed before any
            // biodata is generated so the registry/sections exist up front.
            BiodataFieldSeeder::class,
            DummyMatrimonySeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
