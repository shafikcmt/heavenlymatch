<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * In-app notification feed.
 * Types: new_interest, message, match, photo_request, system, guardian_alert
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 20);
            $table->string('type', 50);             // new_interest|message|match|photo_request|system
            $table->string('title', 200);
            $table->text('body');
            $table->json('data')->nullable();        // {from_user, link, ...}
            $table->enum('channel', ['web', 'email', 'sms'])->default('web');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at'], 'idx_notif_user_read');
            $table->index(['user_id', 'type', 'created_at'], 'idx_notif_user_type');

            $table->foreign('user_id')
                ->references('registration_id')
                ->on('registrations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
