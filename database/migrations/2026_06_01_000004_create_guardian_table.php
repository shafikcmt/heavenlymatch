<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Guardian/Wali Module — Islamic mode only.
 * A user may register one primary Wali whose consent is required for connection requests.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->string('registration_id', 20)->unique();
            $table->string('guardian_name', 100);
            $table->string('relationship', 50);               // Father/Brother/Uncle/Wali/etc.
            $table->string('mobile', 20);
            $table->string('email', 100)->nullable();
            // What events this guardian gets notified about
            $table->enum('notification_level', ['all_actions', 'connection_requests_only', 'disabled'])
                ->default('connection_requests_only');
            $table->boolean('is_verified')->default(false);
            $table->string('verification_otp', 10)->nullable();
            $table->timestamp('otp_sent_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('mobile', 'idx_guardian_mobile');
            $table->foreign('registration_id')
                ->references('registration_id')
                ->on('registrations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
