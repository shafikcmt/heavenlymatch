<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('registrations', 'membership_plan_id')) {
                $table->unsignedBigInteger('membership_plan_id')->nullable()->after('account_status');
            }
            if (! Schema::hasColumn('registrations', 'membership_plan_name')) {
                $table->string('membership_plan_name')->nullable()->after('membership_plan_id');
            }
            if (! Schema::hasColumn('registrations', 'membership_status')) {
                $table->string('membership_status')->default('free')->after('membership_plan_name');
            }
            if (! Schema::hasColumn('registrations', 'membership_started_at')) {
                $table->timestamp('membership_started_at')->nullable()->after('membership_status');
            }
            if (! Schema::hasColumn('registrations', 'membership_expires_at')) {
                $table->timestamp('membership_expires_at')->nullable()->after('membership_started_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $columns = [
                'membership_plan_id',
                'membership_plan_name',
                'membership_status',
                'membership_started_at',
                'membership_expires_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('registrations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
