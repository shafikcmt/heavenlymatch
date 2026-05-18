<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biodatas', function (Blueprint $table) {
            $columns = Schema::getColumnListing('biodatas');

            foreach ([
                'biodata_type', 'previous_marriage_details', 'brothers_info', 'sisters_info', 'home_ownership',
                'niqab_since', 'prayers_qaza_weekly', 'religious_work', 'favorite_scholars', 'profession_halal_status',
                'marriage_plan', 'privacy_consent', 'completion_status', 'approval_status', 'admin_note'
            ] as $column) {
                if (! in_array($column, $columns, true)) {
                    $table->text($column)->nullable();
                }
            }

            if (! in_array('children_count', $columns, true)) {
                $table->integer('children_count')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('biodatas', function (Blueprint $table) {
            foreach ([
                'biodata_type', 'previous_marriage_details', 'children_count', 'brothers_info', 'sisters_info',
                'home_ownership', 'niqab_since', 'prayers_qaza_weekly', 'religious_work', 'favorite_scholars',
                'profession_halal_status', 'marriage_plan', 'privacy_consent', 'completion_status', 'approval_status', 'admin_note'
            ] as $column) {
                if (Schema::hasColumn('biodatas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
