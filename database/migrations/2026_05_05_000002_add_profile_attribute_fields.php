<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('registrations')) {
            Schema::table('registrations', function (Blueprint $table) {
                if (! Schema::hasColumn('registrations', 'religion')) {
                    $table->string('religion', 120)->nullable()->after('gender');
                }
                if (! Schema::hasColumn('registrations', 'marital_status')) {
                    $table->string('marital_status', 120)->nullable()->after('religion');
                }
                if (! Schema::hasColumn('registrations', 'blood_group')) {
                    $table->string('blood_group', 30)->nullable()->after('marital_status');
                }
            });
        }

        if (Schema::hasTable('biodatas')) {
            Schema::table('biodatas', function (Blueprint $table) {
                if (! Schema::hasColumn('biodatas', 'religion')) {
                    $table->string('religion', 120)->nullable()->after('registration_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('biodatas')) {
            Schema::table('biodatas', function (Blueprint $table) {
                if (Schema::hasColumn('biodatas', 'religion')) {
                    $table->dropColumn('religion');
                }
            });
        }

        if (Schema::hasTable('registrations')) {
            Schema::table('registrations', function (Blueprint $table) {
                foreach (['blood_group', 'marital_status', 'religion'] as $column) {
                    if (Schema::hasColumn('registrations', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
