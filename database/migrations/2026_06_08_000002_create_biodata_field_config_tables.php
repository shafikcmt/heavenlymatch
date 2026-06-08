<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PHASE E1 — Admin Biodata Field Control registry.
 *
 * Adds a configuration layer that lets admins govern biodata sections and
 * fields (active/required/visibility/labels/order/conditional logic) WITHOUT
 * touching the hardcoded columns. Existing columns are untouched; admin-created
 * "custom" fields store their values in biodatas.custom_fields (JSON).
 *
 * SAFETY: all additive. No existing column changed/removed. Idempotent guards.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('biodata_sections')) {
            Schema::create('biodata_sections', function (Blueprint $table) {
                $table->id();
                $table->string('key', 50)->unique();
                $table->string('title_en', 150);
                $table->string('title_bn', 150);
                $table->string('description_en', 300)->nullable();
                $table->string('description_bn', 300)->nullable();
                $table->string('icon', 50)->nullable();
                $table->unsignedTinyInteger('step')->nullable();        // maps to wizard step (1-10)
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->unsignedTinyInteger('completion_weight')->default(10);
                $table->boolean('is_active')->default(true);
                $table->boolean('show_in_form')->default(true);
                $table->boolean('show_in_profile')->default(true);
                $table->boolean('show_in_admin')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'sort_order'], 'idx_bsec_active_order');
            });
        }

        if (! Schema::hasTable('biodata_fields')) {
            Schema::create('biodata_fields', function (Blueprint $table) {
                $table->id();
                $table->foreignId('section_id')->constrained('biodata_sections')->cascadeOnDelete();
                $table->string('field_key', 80)->unique();
                // Real biodatas column this maps to. NULL = custom field (value in custom_fields JSON).
                $table->string('model_column', 80)->nullable();

                // Labels / hints (EN + BN)
                $table->string('label_en', 200);
                $table->string('label_bn', 200);
                $table->string('placeholder_en', 250)->nullable();
                $table->string('placeholder_bn', 250)->nullable();
                $table->string('helper_text_en', 300)->nullable();
                $table->string('helper_text_bn', 300)->nullable();

                // Rendering
                // text|textarea|select|multi_select|radio|checkbox|date|number|phone|email|yes_no|file|repeater
                $table->string('input_type', 30)->default('text');
                $table->json('options_en')->nullable();   // [{value,label}, ...]
                $table->json('options_bn')->nullable();
                $table->string('default_value', 250)->nullable();
                $table->string('validation_rules', 250)->nullable();   // pipe rules, e.g. "string|max:200"

                // Behaviour flags
                $table->boolean('is_required')->default(false);
                $table->boolean('is_active')->default(true);
                $table->boolean('show_in_form')->default(true);
                $table->boolean('show_in_profile')->default(true);
                $table->boolean('show_in_admin')->default(true);
                $table->boolean('is_private')->default(false);     // hide from strangers on public profile
                $table->boolean('is_searchable')->default(false);
                $table->boolean('is_filterable')->default(false);
                $table->boolean('is_system')->default(false);      // seeded/critical → cannot be deleted, only deactivated
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->json('conditional_logic')->nullable();     // {field, operator, value}
                $table->string('profile_display_format', 50)->nullable(); // e.g. currency, yes_no, date

                $table->timestamps();

                $table->index(['section_id', 'sort_order'], 'idx_bfield_section_order');
                $table->index(['is_active', 'show_in_form'], 'idx_bfield_active_form');
            });
        }

        // Custom-field value bag on biodatas (admin-created fields only).
        if (Schema::hasTable('biodatas') && ! Schema::hasColumn('biodatas', 'custom_fields')) {
            Schema::table('biodatas', function (Blueprint $table) {
                $table->json('custom_fields')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('biodatas') && Schema::hasColumn('biodatas', 'custom_fields')) {
            Schema::table('biodatas', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }
        Schema::dropIfExists('biodata_fields');
        Schema::dropIfExists('biodata_sections');
    }
};
