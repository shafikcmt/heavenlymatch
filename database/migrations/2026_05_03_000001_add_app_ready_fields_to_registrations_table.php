<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $columns = Schema::getColumnListing('registrations');
            if (! in_array('profile_for', $columns, true)) {
                $table->string('profile_for')->default('self');
            }
            if (! in_array('preferred_language', $columns, true)) {
                $table->string('preferred_language', 10)->default('bn');
            }
            if (! in_array('status', $columns, true)) {
                $table->string('status')->default('pending');
            }
            if (! in_array('terms_accepted_at', $columns, true)) {
                $table->timestamp('terms_accepted_at')->nullable();
            }
            if (! in_array('last_login_at', $columns, true)) {
                $table->timestamp('last_login_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            foreach (['profile_for', 'preferred_language', 'status', 'terms_accepted_at', 'last_login_at'] as $column) {
                if (Schema::hasColumn('registrations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
