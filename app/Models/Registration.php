<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Registration extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        // ── Core identity ─────────────────────────────────────────────────────
        'registration_id',
        'name',
        'gender',
        'profile_created_for',          // was missing — breaks registration create()
        'looking_for',                  // was missing — breaks registration create()
        'google_id',                    // added by 2026_05_21 migration

        // ── Platform & language ───────────────────────────────────────────────
        'platform_mode',                // was missing — breaks registration create()
        'preferred_language',

        // ── Email auth ────────────────────────────────────────────────────────
        'email',
        'email_verification_code',
        'email_verification_token',
        'email_verification_sent_at',
        'is_email_verified',

        // ── Mobile auth ───────────────────────────────────────────────────────
        'country_code',
        'mobile_number',
        'mobile_verification_code',
        'is_mobile_verified',

        // ── Password & session ────────────────────────────────────────────────
        'password',
        'terms_accepted_at',
        'last_login_at',

        // ── Photo privacy ─────────────────────────────────────────────────────
        'photo_visibility',             // was missing — breaks registration create()

        // ── Membership ────────────────────────────────────────────────────────
        'membership_plan_id',
        'membership_plan_name',         // added by 2026_05_21 migration
        'membership_status',
        'membership_started_at',
        'membership_expires_at',

        // ── Boost ─────────────────────────────────────────────────────────────
        'is_boosted',
        'boost_expires_at',
        'profile_views_count',

        // ── Blocking ─────────────────────────────────────────────────────────
        'blocked_at',
        'blocked_reason',

        // ── Identity verification (written by admin only) ─────────────────────
        'nid_number',
        'nid_image_front',
        'nid_image_back',
        'passport_number',
        'passport_image',
        'identity_verification_status', // was missing — breaks AdminUserController verify()
        'identity_verified_at',         // was missing — breaks AdminUserController verify()
        'identity_verified_by',         // was missing — breaks AdminUserController verify()
        'identity_rejection_reason',

        // ── 2FA ───────────────────────────────────────────────────────────────
        'two_factor_enabled',
        'two_factor_secret',

        // ── Account lifecycle ─────────────────────────────────────────────────
        'deactivated_at',
        'deletion_requested_at',

        // ── INTENTIONALLY OMITTED (never mass-assignable) ─────────────────────
        // 'role'           — must use dedicated setRole() or direct DB update by trusted code
        // 'is_admin'       — must use dedicated promoteToAdmin() or seeder only
        // 'account_status' — must use ban()/suspend()/activate() methods only
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
        'mobile_verification_code',
    ];

    protected $casts = [
        // Booleans
        'is_email_verified'   => 'boolean',
        'is_mobile_verified'  => 'boolean',
        'is_admin'            => 'boolean',
        'is_boosted'          => 'boolean',
        'two_factor_enabled'  => 'boolean',

        // Integers
        'profile_views_count' => 'integer',

        // Datetimes
        'email_verified_at'          => 'datetime',
        'email_verification_sent_at' => 'datetime',
        'terms_accepted_at'          => 'datetime',
        'last_login_at'              => 'datetime',
        'blocked_at'                 => 'datetime',
        'membership_started_at'      => 'datetime',
        'membership_expires_at'      => 'datetime',
        'boost_expires_at'           => 'datetime',
        'identity_verified_at'       => 'datetime',
        'deactivated_at'             => 'datetime',
        'deletion_requested_at'      => 'datetime',
    ];

    public function biodata()
    {
        return $this->hasOne(Biodata::class, 'registration_id', 'registration_id');
    }

    public function payments()
    {
        // Third arg = local key on registrations (HM000001 string, not the integer PK)
        return $this->hasMany(PaymentTransaction::class, 'registration_id', 'registration_id');
    }

    public function activePayment()
    {
        return $this->hasOne(PaymentTransaction::class, 'registration_id', 'registration_id')
            ->where('status', 'paid')
            ->latestOfMany();
    }

    // ── MustVerifyEmail overrides ────────────────────────────────────────────
    // The schema uses is_email_verified (boolean) as the authoritative flag,
    // plus email_verified_at for timestamp. Both are set together on verify.

    public function hasVerifiedEmail(): bool
    {
        return $this->is_email_verified === true;
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'is_email_verified' => true,
            'email_verified_at' => now(),
        ])->save();
    }

    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    public function hasActiveMembership(): bool
    {
        return ($this->membership_status === 'active')
            && $this->membership_expires_at
            && $this->membership_expires_at->isFuture();
    }

    public function isAdmin(): bool
    {
        return (bool) ($this->is_admin ?? false) || (($this->role ?? 'user') === 'admin');
    }

    // ── Trusted action methods (use forceFill so non-fillable status fields ──────
    // ── can be updated by admin/system code without opening mass-assignment). ─────

    public function ban(string $reason = ''): void
    {
        $this->forceFill([
            'account_status' => 'banned',
            'blocked_at'     => now(),
            'blocked_reason' => $reason ?: null,
        ])->save();
    }

    public function suspend(string $reason = ''): void
    {
        $this->forceFill([
            'account_status' => 'suspended',
            'blocked_at'     => now(),
            'blocked_reason' => $reason ?: null,
        ])->save();
    }

    public function activate(): void
    {
        $this->forceFill([
            'account_status' => 'active',
            'blocked_at'     => null,
            'blocked_reason' => null,
        ])->save();
    }

    public function setRole(string $role): void
    {
        $this->forceFill(['role' => $role])->save();
    }

    public function promoteToAdmin(): void
    {
        $this->forceFill(['is_admin' => true, 'role' => 'admin'])->save();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if (! empty($model->registration_id)) {
                return;
            }

            // Use MAX(id)+1 instead of latest()->first() to reduce (but not eliminate)
            // the race window. A true fix requires a DB-level sequence or unique retry loop,
            // but for this platform's registration volume MAX is acceptable.
            $next = (static::max('id') ?? 0) + 1;
            $model->registration_id = 'HM' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
        });
    }
}
