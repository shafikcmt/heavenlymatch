<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bangladesh geographic lookup tables.
 * Seeded with all 8 divisions and their districts.
 * Upazilas can be added incrementally as needed.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── Divisions ────────────────────────────────────────────────────────
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 60)->unique();
            $table->string('name_bn', 60)->unique();
            $table->string('slug', 60)->unique();
            $table->timestamps();
        });

        // ─── Districts ────────────────────────────────────────────────────────
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('division_id');
            $table->string('name_en', 80)->unique();
            $table->string('name_bn', 80);
            $table->string('slug', 80)->unique();
            $table->timestamps();

            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
            $table->index('division_id', 'idx_district_division');
        });

        // ─── Upazilas ─────────────────────────────────────────────────────────
        Schema::create('upazilas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id');
            $table->string('name_en', 100);
            $table->string('name_bn', 100);
            $table->string('slug', 100);
            $table->timestamps();

            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->index('district_id', 'idx_upazila_district');
        });

        // ─── Seed divisions ───────────────────────────────────────────────────
        $now = now();
        $divisions = [
            ['name_en' => 'Dhaka',      'name_bn' => 'ঢাকা',      'slug' => 'dhaka'],
            ['name_en' => 'Chattogram', 'name_bn' => 'চট্টগ্রাম', 'slug' => 'chattogram'],
            ['name_en' => 'Rajshahi',   'name_bn' => 'রাজশাহী',   'slug' => 'rajshahi'],
            ['name_en' => 'Khulna',     'name_bn' => 'খুলনা',     'slug' => 'khulna'],
            ['name_en' => 'Sylhet',     'name_bn' => 'সিলেট',     'slug' => 'sylhet'],
            ['name_en' => 'Barishal',   'name_bn' => 'বরিশাল',    'slug' => 'barishal'],
            ['name_en' => 'Rangpur',    'name_bn' => 'রংপুর',     'slug' => 'rangpur'],
            ['name_en' => 'Mymensingh', 'name_bn' => 'ময়মনসিংহ', 'slug' => 'mymensingh'],
        ];

        foreach ($divisions as &$d) {
            $d['created_at'] = $now;
            $d['updated_at'] = $now;
        }
        DB::table('divisions')->insert($divisions);

        // ─── Seed key districts ───────────────────────────────────────────────
        $divisionMap = DB::table('divisions')->pluck('id', 'slug');

        $districts = [
            // Dhaka
            ['division_id' => $divisionMap['dhaka'], 'name_en' => 'Dhaka',        'name_bn' => 'ঢাকা',         'slug' => 'dhaka-district'],
            ['division_id' => $divisionMap['dhaka'], 'name_en' => 'Gazipur',      'name_bn' => 'গাজীপুর',      'slug' => 'gazipur'],
            ['division_id' => $divisionMap['dhaka'], 'name_en' => 'Narayanganj',  'name_bn' => 'নারায়ণগঞ্জ',  'slug' => 'narayanganj'],
            ['division_id' => $divisionMap['dhaka'], 'name_en' => 'Manikganj',    'name_bn' => 'মানিকগঞ্জ',    'slug' => 'manikganj'],
            ['division_id' => $divisionMap['dhaka'], 'name_en' => 'Munshiganj',   'name_bn' => 'মুন্সীগঞ্জ',   'slug' => 'munshiganj'],
            ['division_id' => $divisionMap['dhaka'], 'name_en' => 'Narsingdi',    'name_bn' => 'নরসিংদী',      'slug' => 'narsingdi'],
            ['division_id' => $divisionMap['dhaka'], 'name_en' => 'Faridpur',     'name_bn' => 'ফরিদপুর',      'slug' => 'faridpur'],
            ['division_id' => $divisionMap['dhaka'], 'name_en' => 'Tangail',      'name_bn' => 'টাঙ্গাইল',     'slug' => 'tangail'],
            // Chattogram
            ['division_id' => $divisionMap['chattogram'], 'name_en' => 'Chattogram', 'name_bn' => 'চট্টগ্রাম',  'slug' => 'chattogram-district'],
            ['division_id' => $divisionMap['chattogram'], 'name_en' => "Cox's Bazar",  'name_bn' => "কক্সবাজার", 'slug' => 'coxs-bazar'],
            ['division_id' => $divisionMap['chattogram'], 'name_en' => 'Comilla',   'name_bn' => 'কুমিল্লা',    'slug' => 'comilla'],
            ['division_id' => $divisionMap['chattogram'], 'name_en' => 'Noakhali',  'name_bn' => 'নোয়াখালী',   'slug' => 'noakhali'],
            ['division_id' => $divisionMap['chattogram'], 'name_en' => 'Feni',      'name_bn' => 'ফেনী',        'slug' => 'feni'],
            ['division_id' => $divisionMap['chattogram'], 'name_en' => 'Chandpur',  'name_bn' => 'চাঁদপুর',     'slug' => 'chandpur'],
            // Sylhet
            ['division_id' => $divisionMap['sylhet'], 'name_en' => 'Sylhet',      'name_bn' => 'সিলেট',        'slug' => 'sylhet-district'],
            ['division_id' => $divisionMap['sylhet'], 'name_en' => 'Moulvibazar', 'name_bn' => 'মৌলভীবাজার',  'slug' => 'moulvibazar'],
            ['division_id' => $divisionMap['sylhet'], 'name_en' => 'Habiganj',    'name_bn' => 'হবিগঞ্জ',      'slug' => 'habiganj'],
            ['division_id' => $divisionMap['sylhet'], 'name_en' => 'Sunamganj',   'name_bn' => 'সুনামগঞ্জ',    'slug' => 'sunamganj'],
            // Rajshahi
            ['division_id' => $divisionMap['rajshahi'], 'name_en' => 'Rajshahi',  'name_bn' => 'রাজশাহী',      'slug' => 'rajshahi-district'],
            ['division_id' => $divisionMap['rajshahi'], 'name_en' => 'Bogura',    'name_bn' => 'বগুড়া',        'slug' => 'bogura'],
            ['division_id' => $divisionMap['rajshahi'], 'name_en' => 'Natore',    'name_bn' => 'নাটোর',        'slug' => 'natore'],
            // Khulna
            ['division_id' => $divisionMap['khulna'], 'name_en' => 'Khulna',     'name_bn' => 'খুলনা',         'slug' => 'khulna-district'],
            ['division_id' => $divisionMap['khulna'], 'name_en' => 'Jessore',    'name_bn' => 'যশোর',          'slug' => 'jessore'],
            ['division_id' => $divisionMap['khulna'], 'name_en' => 'Satkhira',   'name_bn' => 'সাতক্ষীরা',     'slug' => 'satkhira'],
            // Barishal
            ['division_id' => $divisionMap['barishal'], 'name_en' => 'Barishal', 'name_bn' => 'বরিশাল',        'slug' => 'barishal-district'],
            ['division_id' => $divisionMap['barishal'], 'name_en' => 'Patuakhali', 'name_bn' => 'পটুয়াখালী', 'slug' => 'patuakhali'],
            // Rangpur
            ['division_id' => $divisionMap['rangpur'], 'name_en' => 'Rangpur',   'name_bn' => 'রংপুর',         'slug' => 'rangpur-district'],
            ['division_id' => $divisionMap['rangpur'], 'name_en' => 'Dinajpur',  'name_bn' => 'দিনাজপুর',      'slug' => 'dinajpur'],
            // Mymensingh
            ['division_id' => $divisionMap['mymensingh'], 'name_en' => 'Mymensingh', 'name_bn' => 'ময়মনসিংহ', 'slug' => 'mymensingh-district'],
            ['division_id' => $divisionMap['mymensingh'], 'name_en' => 'Jamalpur',   'name_bn' => 'জামালপুর',  'slug' => 'jamalpur'],
        ];

        foreach ($districts as &$d) {
            $d['created_at'] = $now;
            $d['updated_at'] = $now;
        }
        DB::table('districts')->insert($districts);
    }

    public function down(): void
    {
        Schema::dropIfExists('upazilas');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('divisions');
    }
};
