<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('registrations')) {
            Schema::table('registrations', function (Blueprint $table) {
                if (! Schema::hasColumn('registrations', 'profile_for')) {
                    $table->string('profile_for')->nullable()->after('gender');
                }
                if (! Schema::hasColumn('registrations', 'preferred_language')) {
                    $table->string('preferred_language', 10)->default('bn')->after('profile_for');
                }
                if (! Schema::hasColumn('registrations', 'terms_accepted_at')) {
                    $table->timestamp('terms_accepted_at')->nullable()->after('password');
                }
                if (! Schema::hasColumn('registrations', 'last_login_at')) {
                    $table->timestamp('last_login_at')->nullable()->after('terms_accepted_at');
                }
                if (! Schema::hasColumn('registrations', 'role')) {
                    $table->string('role', 30)->default('user')->after('last_login_at');
                }
                if (! Schema::hasColumn('registrations', 'is_admin')) {
                    $table->boolean('is_admin')->default(false)->after('role');
                }
                if (! Schema::hasColumn('registrations', 'account_status')) {
                    $table->string('account_status', 30)->default('active')->after('is_admin');
                }
                if (! Schema::hasColumn('registrations', 'blocked_at')) {
                    $table->timestamp('blocked_at')->nullable()->after('account_status');
                }
                if (! Schema::hasColumn('registrations', 'blocked_reason')) {
                    $table->text('blocked_reason')->nullable()->after('blocked_at');
                }
            });
        }

        if (Schema::hasTable('biodatas')) {
            Schema::table('biodatas', function (Blueprint $table) {
                if (! Schema::hasColumn('biodatas', 'status')) {
                    $table->string('status', 30)->default('pending')->after('is_completed');
                }
                if (! Schema::hasColumn('biodatas', 'admin_note')) {
                    $table->text('admin_note')->nullable()->after('status');
                }
                if (! Schema::hasColumn('biodatas', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('admin_note');
                }
                if (! Schema::hasColumn('biodatas', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
                }
                if (! Schema::hasColumn('biodatas', 'rejected_at')) {
                    $table->timestamp('rejected_at')->nullable()->after('approved_by');
                }
                if (! Schema::hasColumn('biodatas', 'rejected_by')) {
                    $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
                }
                if (! Schema::hasColumn('biodatas', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('rejected_by');
                }
                if (! Schema::hasColumn('biodatas', 'featured_at')) {
                    $table->timestamp('featured_at')->nullable()->after('is_featured');
                }
                if (! Schema::hasColumn('biodatas', 'profile_score')) {
                    $table->unsignedTinyInteger('profile_score')->default(0)->after('featured_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('biodatas')) {
            Schema::table('biodatas', function (Blueprint $table) {
                foreach (['profile_score','featured_at','is_featured','rejected_by','rejected_at','approved_by','approved_at','admin_note','status'] as $column) {
                    if (Schema::hasColumn('biodatas', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('registrations')) {
            Schema::table('registrations', function (Blueprint $table) {
                foreach (['blocked_reason','blocked_at','account_status','is_admin','role','last_login_at','terms_accepted_at','preferred_language','profile_for'] as $column) {
                    if (Schema::hasColumn('registrations', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
