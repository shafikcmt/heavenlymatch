<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds contact + privacy fields used by the new "Contact & Privacy" wizard step.
 *  - whatsapp_number : optional WhatsApp contact (normalised +880 format)
 *  - contact_privacy : who may see the contact details (private/request/matches)
 * Both nullable — existing rows are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biodatas', function (Blueprint $table) {
            if (! Schema::hasColumn('biodatas', 'whatsapp_number')) {
                $table->string('whatsapp_number', 20)->nullable()->after('guardian_email');
            }
            if (! Schema::hasColumn('biodatas', 'contact_privacy')) {
                $table->string('contact_privacy', 20)->nullable()->default('private')->after('whatsapp_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('biodatas', function (Blueprint $table) {
            foreach (['whatsapp_number', 'contact_privacy'] as $col) {
                if (Schema::hasColumn('biodatas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
