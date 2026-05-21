<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Sender's bKash/Nagad number (may differ from customer_phone)
            $table->string('sender_number', 25)->nullable()->after('customer_phone');
            // Optional payment screenshot path (stored in storage/app/public)
            $table->string('screenshot_path', 500)->nullable()->after('sender_number');
            // Admin review fields
            $table->text('admin_note')->nullable()->after('reference_note');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('admin_note');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['sender_number', 'screenshot_path', 'admin_note', 'reviewed_by', 'reviewed_at']);
        });
    }
};
