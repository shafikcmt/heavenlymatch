<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * All social interaction tables.
 * Supersedes: 2026_05_20_000003_create_core_interaction_tables.php
 *
 * Tables: connection_requests, conversations, messages,
 *         shortlists, profile_views, photo_access_requests, match_scores
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── Connection Requests ───────────────────────────────────────────
        // Islamic mode adds guardian_pending step before accepted.
        Schema::create('connection_requests', function (Blueprint $table) {
            $table->id();
            $table->string('sender_id', 20);
            $table->string('receiver_id', 20);
            $table->enum('status', ['pending', 'accepted', 'declined', 'guardian_pending', 'withdrawn'])
                ->default('pending');
            $table->text('initial_message')->nullable();
            $table->boolean('guardian_notified')->default(false);
            $table->timestamp('guardian_notified_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['sender_id', 'receiver_id'], 'uq_conn_pair');
            $table->index(['receiver_id', 'status'], 'idx_conn_receiver_status');
            $table->index(['sender_id', 'status'], 'idx_conn_sender_status');

            $table->foreign('sender_id')->references('registration_id')->on('registrations')->onDelete('cascade');
            $table->foreign('receiver_id')->references('registration_id')->on('registrations')->onDelete('cascade');
        });

        // ─── Conversations ────────────────────────────────────────────────
        // Created only after a connection request is accepted.
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('user_a_id', 20);
            $table->string('user_b_id', 20);
            $table->unsignedBigInteger('connection_request_id');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->unique(['user_a_id', 'user_b_id'], 'uq_convo_pair');
            $table->index(['user_a_id', 'last_message_at'], 'idx_convo_user_a');
            $table->index(['user_b_id', 'last_message_at'], 'idx_convo_user_b');

            $table->foreign('connection_request_id')
                ->references('id')
                ->on('connection_requests')
                ->onDelete('cascade');
        });

        // ─── Messages ─────────────────────────────────────────────────────
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->string('sender_id', 20);
            $table->text('body');
            $table->enum('type', ['text', 'image', 'system'])->default('text');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at'], 'idx_msg_convo_time');
            $table->index(['sender_id', 'created_at'], 'idx_msg_sender');

            $table->foreign('conversation_id')
                ->references('id')
                ->on('conversations')
                ->onDelete('cascade');
        });

        // ─── Shortlists / Favourites ──────────────────────────────────────
        Schema::create('shortlists', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 20);
            $table->string('shortlisted_id', 20);
            $table->string('note', 200)->nullable();          // private note for self
            $table->timestamps();

            $table->unique(['user_id', 'shortlisted_id'], 'uq_shortlist_pair');
            $table->index('user_id', 'idx_shortlist_user');

            $table->foreign('user_id')->references('registration_id')->on('registrations')->onDelete('cascade');
            $table->foreign('shortlisted_id')->references('registration_id')->on('registrations')->onDelete('cascade');
        });

        // ─── Profile Views Log ────────────────────────────────────────────
        Schema::create('profile_views', function (Blueprint $table) {
            $table->id();
            $table->string('viewer_id', 20)->nullable();      // null = guest
            $table->string('profile_id', 20);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('viewed_at')->useCurrent();

            $table->index(['profile_id', 'viewed_at'], 'idx_pview_profile_time');
            $table->index(['viewer_id', 'profile_id'], 'idx_pview_viewer_profile');
        });

        // ─── Photo Access Requests (Islamic / blurred mode) ───────────────
        Schema::create('photo_access_requests', function (Blueprint $table) {
            $table->id();
            $table->string('requester_id', 20);
            $table->string('profile_id', 20);
            $table->enum('status', ['pending', 'granted', 'denied'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['requester_id', 'profile_id'], 'uq_photo_req_pair');
            $table->index(['profile_id', 'status'], 'idx_photo_req_profile_status');

            $table->foreign('requester_id')->references('registration_id')->on('registrations')->onDelete('cascade');
            $table->foreign('profile_id')->references('registration_id')->on('registrations')->onDelete('cascade');
        });

        // ─── AI Match Score Cache ─────────────────────────────────────────
        // Pre-computed nightly by ComputeMatchScoresJob at 02:00 BDT.
        // score_breakdown: {"age":20,"location":15,"religion":15,...}
        Schema::create('match_scores', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 20);
            $table->string('candidate_id', 20);
            $table->unsignedTinyInteger('total_score');        // 0-100
            $table->json('score_breakdown')->nullable();
            $table->timestamp('computed_at')->useCurrent();

            $table->unique(['user_id', 'candidate_id'], 'uq_match_pair');
            $table->index(['user_id', 'total_score'], 'idx_match_user_score');

            $table->foreign('user_id')->references('registration_id')->on('registrations')->onDelete('cascade');
            $table->foreign('candidate_id')->references('registration_id')->on('registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_scores');
        Schema::dropIfExists('photo_access_requests');
        Schema::dropIfExists('profile_views');
        Schema::dropIfExists('shortlists');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('connection_requests');
    }
};
