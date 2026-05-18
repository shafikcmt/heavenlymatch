<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('user_attributes')) {
            Schema::create('user_attributes', function (Blueprint $table) {
                $table->id();
                $table->string('type', 60);
                $table->string('name', 120);
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['type', 'name']);
                $table->index(['type', 'is_active', 'sort_order']);
            });
        }

        $now = now();
        $defaults = [
            'religion' => ['Islam', 'Christian', 'Buddhist', 'Hindu'],
            'blood-group' => ['A+', 'A-'],
            'marital-status' => ['Single', 'Married', 'Divorced', 'Widow'],
        ];

        foreach ($defaults as $type => $items) {
            foreach ($items as $index => $name) {
                $exists = DB::table('user_attributes')
                    ->where('type', $type)
                    ->where('name', $name)
                    ->exists();

                if (! $exists) {
                    DB::table('user_attributes')->insert([
                        'type' => $type,
                        'name' => $name,
                        'sort_order' => $index + 1,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_attributes');
    }
};
