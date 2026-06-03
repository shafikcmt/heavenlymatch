<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stores hashed email OTP codes used during registration email verification.
 * Codes are never stored in plain text and are pruned after successful use.
 * Mirrors phone_verification_codes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email', 180);
            $table->string('code_hash');              // bcrypt/argon hash of the 6-digit code
            $table->timestamp('expires_at');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['email', 'created_at']);   // fast lookup of latest code per email
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_verification_codes');
    }
};
