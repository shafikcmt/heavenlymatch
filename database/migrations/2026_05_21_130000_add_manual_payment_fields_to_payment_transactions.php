<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds manual-payment review fields to payment_transactions.
 *
 * SAFE FOR FRESH INSTALL: skips when payment_transactions does not yet exist —
 * the clean 2026_06_01_000006 migration already includes all these columns.
 *
 * SAFE FOR EXISTING DATABASES: runs normally; hasColumn() guards prevent duplicates.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_transactions')) {
            // Fresh install — 2026_06_01_000006 already defines all these columns.
            return;
        }

        Schema::table('payment_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('payment_transactions', 'sender_number')) {
                $table->string('sender_number', 25)->nullable()->after('customer_phone');
            }

            if (! Schema::hasColumn('payment_transactions', 'screenshot_path')) {
                $table->string('screenshot_path', 500)->nullable()->after('sender_number');
            }

            if (! Schema::hasColumn('payment_transactions', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('reference_note');
            }

            if (! Schema::hasColumn('payment_transactions', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('admin_note');
            }

            if (! Schema::hasColumn('payment_transactions', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('payment_transactions')) {
            return;
        }

        Schema::table('payment_transactions', function (Blueprint $table) {
            $cols = ['sender_number', 'screenshot_path', 'admin_note', 'reviewed_by', 'reviewed_at'];
            $toDrop = array_filter($cols, fn ($c) => Schema::hasColumn('payment_transactions', $c));
            if ($toDrop) {
                $table->dropColumn(array_values($toDrop));
            }
        });
    }
};
