<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * PHASE A — Extended biodata fields.
 *
 * Purpose: close the gap between the existing 10-step wizard schema and the
 * fuller reference matrimonial structure (marital-status conditionals, deeper
 * deen/professional/marriage detail, granular current address, multi-district
 * partner preference, contact/privacy toggles, and stored commitment flags).
 *
 * SAFETY CONTRACT (do not violate in future edits):
 *   - Every column is NULLABLE and ADDITIVE — existing rows are untouched.
 *   - No column is renamed or dropped; the marital_status enum is NOT altered
 *     (separated / widow / widower nuance is captured via `marital_substatus`).
 *   - Each add is guarded with hasColumn so the migration is safe to re-run and
 *     safe alongside earlier ensure-* migrations on shared hosting.
 *   - Back-compat: existing `education_method` and `partner_district` stay; new
 *     `education_medium` and `partner_districts` are added beside them.
 */
return new class extends Migration
{
    /**
     * field => closure(Blueprint) that defines the column.
     * Declared as nullable; defaults only where a sensible non-null default helps.
     */
    private function columns(): array
    {
        return [
            // ── Address: granular current address + toggle ────────────────────
            'current_division'  => fn (Blueprint $t) => $t->string('current_division', 60)->nullable(),
            'current_district'  => fn (Blueprint $t) => $t->string('current_district', 60)->nullable(),
            'current_upazila'   => fn (Blueprint $t) => $t->string('current_upazila', 60)->nullable(),
            'current_area'      => fn (Blueprint $t) => $t->string('current_area', 100)->nullable(),
            'same_as_permanent' => fn (Blueprint $t) => $t->boolean('same_as_permanent')->nullable(),

            // ── Education: wider medium beside legacy education_method ─────────
            // general | qawmi | alia | english_medium | vocational | other
            'education_medium'  => fn (Blueprint $t) => $t->string('education_medium', 30)->nullable(),

            // ── Deen / Personal detail ────────────────────────────────────────
            'prayer_start_age'     => fn (Blueprint $t) => $t->string('prayer_start_age', 50)->nullable(),
            'weekly_missed_prayers' => fn (Blueprint $t) => $t->string('weekly_missed_prayers', 50)->nullable(),
            'mahram_practice'      => fn (Blueprint $t) => $t->text('mahram_practice')->nullable(),
            'islamic_books_read'   => fn (Blueprint $t) => $t->text('islamic_books_read')->nullable(),
            'deen_work_details'    => fn (Blueprint $t) => $t->text('deen_work_details')->nullable(),
            'social_media_usage'   => fn (Blueprint $t) => $t->string('social_media_usage', 150)->nullable(),
            // male
            'beard_since'          => fn (Blueprint $t) => $t->string('beard_since', 50)->nullable(),
            'pants_above_ankle'    => fn (Blueprint $t) => $t->boolean('pants_above_ankle')->nullable(),
            // female
            'niqab_since'          => fn (Blueprint $t) => $t->string('niqab_since', 50)->nullable(),
            'purdah_details'       => fn (Blueprint $t) => $t->text('purdah_details')->nullable(),

            // ── Professional ──────────────────────────────────────────────────
            // monthly | yearly | variable | private
            'income_type'        => fn (Blueprint $t) => $t->string('income_type', 20)->nullable(),
            // public | private | members_only
            'income_privacy'     => fn (Blueprint $t) => $t->string('income_privacy', 20)->nullable(),
            'workplace_type'     => fn (Blueprint $t) => $t->string('workplace_type', 100)->nullable(),
            'future_career_plan' => fn (Blueprint $t) => $t->text('future_career_plan')->nullable(),

            // ── Family extras ─────────────────────────────────────────────────
            'uncle_profession'     => fn (Blueprint $t) => $t->string('uncle_profession', 150)->nullable(),
            'family_assets_details' => fn (Blueprint $t) => $t->text('family_assets_details')->nullable(),
            'guardian_name'        => fn (Blueprint $t) => $t->string('guardian_name', 100)->nullable(),
            'guardian_whatsapp'    => fn (Blueprint $t) => $t->string('guardian_whatsapp', 20)->nullable(),

            // ── Marriage: general thoughts ────────────────────────────────────
            'why_getting_married'    => fn (Blueprint $t) => $t->text('why_getting_married')->nullable(),
            'marriage_thoughts'      => fn (Blueprint $t) => $t->text('marriage_thoughts')->nullable(),
            'marriage_timeline'      => fn (Blueprint $t) => $t->string('marriage_timeline', 60)->nullable(),
            'gift_expectation_details' => fn (Blueprint $t) => $t->text('gift_expectation_details')->nullable(),

            // ── Marriage: female intentions ───────────────────────────────────
            'wants_to_work'    => fn (Blueprint $t) => $t->boolean('wants_to_work')->nullable(),
            'continue_study'   => fn (Blueprint $t) => $t->boolean('continue_study')->nullable(),
            'continue_job'     => fn (Blueprint $t) => $t->boolean('continue_job')->nullable(),
            'preferred_living' => fn (Blueprint $t) => $t->string('preferred_living', 100)->nullable(),

            // ── Marital-status conditionals (under the 4-value enum) ───────────
            // never_married | married | divorced | widowed ; sub captures
            // separated / widow / widower / second_marriage nuance.
            'marital_substatus'          => fn (Blueprint $t) => $t->string('marital_substatus', 30)->nullable(),
            'previous_marriage_date'     => fn (Blueprint $t) => $t->date('previous_marriage_date')->nullable(),
            'divorce_date'               => fn (Blueprint $t) => $t->date('divorce_date')->nullable(),
            'divorce_reason'             => fn (Blueprint $t) => $t->text('divorce_reason')->nullable(),
            'spouse_death_date'          => fn (Blueprint $t) => $t->date('spouse_death_date')->nullable(),
            'spouse_death_reason'        => fn (Blueprint $t) => $t->text('spouse_death_reason')->nullable(),
            'child_acceptance_expectation' => fn (Blueprint $t) => $t->text('child_acceptance_expectation')->nullable(),
            'reason_for_second_marriage' => fn (Blueprint $t) => $t->text('reason_for_second_marriage')->nullable(),
            'current_wife_count'         => fn (Blueprint $t) => $t->unsignedTinyInteger('current_wife_count')->nullable(),
            'current_family_consent'     => fn (Blueprint $t) => $t->boolean('current_family_consent')->nullable(),
            'first_wife_knows'           => fn (Blueprint $t) => $t->boolean('first_wife_knows')->nullable(),
            'second_marriage_living'     => fn (Blueprint $t) => $t->string('second_marriage_living', 150)->nullable(),

            // ── Partner preference extras ─────────────────────────────────────
            'partner_economic_status'  => fn (Blueprint $t) => $t->string('partner_economic_status', 60)->nullable(),
            'partner_deen_practice'    => fn (Blueprint $t) => $t->string('partner_deen_practice', 100)->nullable(),
            'partner_special_qualities' => fn (Blueprint $t) => $t->text('partner_special_qualities')->nullable(),
            'partner_deal_breakers'    => fn (Blueprint $t) => $t->text('partner_deal_breakers')->nullable(),
            // multi-select districts beside legacy single partner_district
            'partner_districts'        => fn (Blueprint $t) => $t->json('partner_districts')->nullable(),

            // ── Contact / Privacy ─────────────────────────────────────────────
            'contact_person_name'   => fn (Blueprint $t) => $t->string('contact_person_name', 100)->nullable(),
            // public | private | admin_approved_only
            'biodata_visibility'    => fn (Blueprint $t) => $t->string('biodata_visibility', 30)->nullable(),
            'allow_shortlist'       => fn (Blueprint $t) => $t->boolean('allow_shortlist')->nullable()->default(true),
            'allow_contact_request' => fn (Blueprint $t) => $t->boolean('allow_contact_request')->nullable()->default(true),

            // ── Commitment / Declaration (now persisted, was transient only) ──
            'guardian_knows_biodata' => fn (Blueprint $t) => $t->boolean('guardian_knows_biodata')->nullable(),
            'info_truthful_confirmed' => fn (Blueprint $t) => $t->boolean('info_truthful_confirmed')->nullable(),
            'accept_liability_terms' => fn (Blueprint $t) => $t->boolean('accept_liability_terms')->nullable(),
        ];
    }

    /**
     * Long free-text VARCHAR columns that are safe to store off-page as TEXT.
     * All are non-indexed prose fields; moving them off-page (DYNAMIC row format)
     * frees inline row-size budget so the extra columns below fit under InnoDB's
     * 8126-byte inline limit. Data is preserved by the MODIFY.
     */
    private function freeTextColumns(): array
    {
        return [
            // Long (>255 byte) free-text varchars — already off-page, converted
            // for consistency (negligible inline saving).
            'profile_headline', 'social_media_usage', 'children_live_with',
            'clothing_style', 'current_area', 'family_religious_condition',
            'father_profession', 'mother_profession', 'occupation',
            'partner_occupation_pref', 'post_marriage_plan', 'religious_work',
            'residence_after_marriage', 'special_category', 'village_area',
            'workplace_type',

            // Small (<=255 byte) free-text varchars — InnoDB forces these inline,
            // so moving them off-page (TEXT) is what actually frees the budget.
            // All are non-indexed prose/personal/preference fields. Indexed search
            // keys (division, district, residing_country, religion, sect) are kept
            // as VARCHAR so they remain index-eligible.
            'beard_info', 'beard_since', 'expect_gift_from_bride', 'fiqh',
            'guardian_relationship', 'hijab_info', 'mother_tongue', 'niqab_since',
            'prayer_start_age', 'watch_entertainment', 'weekly_missed_prayers',
            'education_medium', 'partner_complexion', 'partner_marital_status',
            'partner_religion', 'current_district', 'current_division',
            'current_upazila', 'grew_up_in', 'nationality', 'partner_education',
            'partner_nationality', 'partner_residing_country', 'upazila',
        ];
    }

    /**
     * Convert the above columns to TEXT in a SINGLE ALTER (idempotent: only the
     * ones still VARCHAR are touched).
     *
     * Why one statement: each MODIFY rebuilds the table and re-runs InnoDB's
     * 8126-byte inline check against the *resulting* definition. Converting one
     * column at a time fails because the table is still over budget after a
     * single move. Batching every conversion means the rebuilt definition has
     * them all off-page at once, clearing the limit in one pass.
     */
    private function freeInlineBudget(): void
    {
        $database = DB::getDatabaseName();

        $stillVarchar = array_values(array_filter(
            $this->freeTextColumns(),
            function (string $col) use ($database) {
                if (! Schema::hasColumn('biodatas', $col)) {
                    return false;
                }

                $type = DB::table('information_schema.columns')
                    ->where('table_schema', $database)
                    ->where('table_name', 'biodatas')
                    ->where('column_name', $col)
                    ->value('DATA_TYPE');

                return $type !== null && strtolower((string) $type) === 'varchar';
            },
        ));

        if ($stillVarchar === []) {
            return;
        }

        $clauses = implode(', ', array_map(
            fn (string $col) => "MODIFY `{$col}` TEXT NULL",
            $stillVarchar,
        ));

        DB::statement("ALTER TABLE `biodatas` {$clauses}");
    }

    public function up(): void
    {
        // Make room under InnoDB's inline row-size limit before adding columns.
        $this->freeInlineBudget();

        Schema::table('biodatas', function (Blueprint $table) {
            foreach ($this->columns() as $name => $define) {
                if (! Schema::hasColumn('biodatas', $name)) {
                    $define($table);
                }
            }
        });

        // Helpful index for age-range search (birth_date was previously unindexed).
        // Wrapped so a pre-existing index never breaks the migration.
        try {
            Schema::table('biodatas', function (Blueprint $table) {
                $table->index('birth_date', 'idx_bio_birth_date');
            });
        } catch (\Throwable $e) {
            // Index already exists — ignore.
        }
    }

    public function down(): void
    {
        try {
            Schema::table('biodatas', function (Blueprint $table) {
                $table->dropIndex('idx_bio_birth_date');
            });
        } catch (\Throwable $e) {
            // ignore
        }

        Schema::table('biodatas', function (Blueprint $table) {
            foreach (array_keys($this->columns()) as $name) {
                if (Schema::hasColumn('biodatas', $name)) {
                    $table->dropColumn($name);
                }
            }
        });
    }
};
