<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biodatas', function (Blueprint $table) {
            if (!Schema::hasColumn('biodatas', 'brothers_details')) {
                $table->json('brothers_details')->nullable()->after('sisters');
            }
            if (!Schema::hasColumn('biodatas', 'sisters_details')) {
                $table->json('sisters_details')->nullable()->after('brothers_details');
            }
            if (!Schema::hasColumn('biodatas', 'has_children')) {
                $table->boolean('has_children')->nullable()->after('children_count');
            }
            if (!Schema::hasColumn('biodatas', 'children_live_with')) {
                $table->string('children_live_with', 100)->nullable()->after('has_children');
            }
            if (!Schema::hasColumn('biodatas', 'children_notes')) {
                $table->text('children_notes')->nullable()->after('children_live_with');
            }
        });
    }

    public function down(): void
    {
        Schema::table('biodatas', function (Blueprint $table) {
            $table->dropColumn([
                'brothers_details',
                'sisters_details',
                'has_children',
                'children_live_with',
                'children_notes',
            ]);
        });
    }
};
