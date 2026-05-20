<?php

namespace App\Services;

use App\Models\Biodata;
use App\Models\PhotoAccessRequest;
use App\Models\Registration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Handles photo URL generation and privacy resolution.
 *
 * Storage strategy controlled by FILESYSTEM_DISK env var:
 *   local / private  → signed application-proxy URLs  (XAMPP / shared hosting)
 *   s3 / r2          → presigned S3/R2 URLs           (cloud deployment)
 *
 * The frontend never receives raw storage paths — only time-limited tokens
 * or presigned URLs that expire within 15 minutes.
 */
class PhotoPrivacyService
{
    private const TOKEN_TTL_MINUTES = 15;

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Determine whether the viewer should see a blurred version of the photo.
     */
    public function shouldBlur(Registration $profile, ?Registration $viewer): bool
    {
        $visibility = $profile->photo_visibility;

        if ($visibility === 'blurred') {
            return true;
        }

        if ($profile->platform_mode === 'ISLAMIC') {
            if (! $viewer) {
                return true;
            }
            return ! $this->hasGrantedPhotoAccess($viewer->registration_id, $profile->registration_id);
        }

        if ($visibility === 'members_only') {
            if (! $viewer) {
                return true;
            }
            return ! $this->hasAcceptedConnection($viewer->registration_id, $profile->registration_id);
        }

        // 'public' — always show
        return false;
    }

    /**
     * Generate a serving URL for a profile photo (index 0 = primary photo).
     * Returns an application-proxy URL with an embedded HMAC token.
     * Works identically on localhost and production — no S3 required at runtime.
     */
    public function photoUrl(string $profileId, int $photoIndex = 0, ?string $viewerId = null): string
    {
        $viewerId ??= 'guest';
        $token = $this->issueToken($profileId, $viewerId);

        return route('api.photo.serve', [
            'registrationId' => $profileId,
            'photoIndex'     => $photoIndex,
        ]) . '?token=' . urlencode($token);
    }

    /**
     * For cloud deployments: generate a presigned URL directly from S3/R2,
     * bypassing the application proxy. Only call when FILESYSTEM_DISK is s3/r2.
     */
    public function presignedUrl(string $storagePath, int $ttlMinutes = self::TOKEN_TTL_MINUTES): string
    {
        $disk = config('filesystems.default');

        if (in_array($disk, ['s3', 'r2'])) {
            // S3/R2 — use SDK temporary URL
            return Storage::disk($disk)->temporaryUrl(
                $storagePath,
                now()->addMinutes($ttlMinutes)
            );
        }

        // Fallback to application proxy for local storage
        return $this->photoUrl(basename($storagePath));
    }

    /**
     * Issue a short-lived HMAC token for photo serving.
     * Token format: base64( profileId|viewerId|expiry|sha256_hmac )
     */
    public function issueToken(string $profileId, string $viewerId): string
    {
        $payload   = $profileId . '|' . $viewerId . '|' . now()->addMinutes(self::TOKEN_TTL_MINUTES)->timestamp;
        $signature = hash_hmac('sha256', $payload, config('app.key'));

        return base64_encode($payload . '|' . $signature);
    }

    /**
     * Verify a previously issued HMAC token.
     */
    public function verifyToken(string $token, string $profileId): bool
    {
        $decoded = base64_decode($token, true);
        if ($decoded === false) {
            return false;
        }

        $parts = explode('|', $decoded);
        if (count($parts) !== 4) {
            return false;
        }

        [$tokenProfileId, , $expiry, $signature] = $parts;

        if ($tokenProfileId !== $profileId) {
            return false;
        }

        if (time() > (int) $expiry) {
            return false;
        }

        $payload  = $tokenProfileId . '|' . $parts[1] . '|' . $expiry;
        $expected = hash_hmac('sha256', $payload, config('app.key'));

        return hash_equals($expected, $signature);
    }

    /**
     * Resolve the storage path for a profile photo from biodata JSON.
     */
    public function resolvePath(Biodata $biodata, int $photoIndex = 0): ?string
    {
        $photos = $biodata->photos ?? [];

        return $photos[$photoIndex]['path'] ?? null;
    }

    /**
     * Generate all photo-serving URLs for a profile (one per uploaded photo).
     * Returns an empty array if no photos are present.
     */
    public function profilePhotoUrls(Biodata $biodata, ?string $viewerId = null): array
    {
        $photos = $biodata->photos ?? [];

        return array_map(
            fn (int $i) => $this->photoUrl($biodata->registration_id, $i, $viewerId),
            array_keys($photos)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function hasGrantedPhotoAccess(string $requesterId, string $profileId): bool
    {
        return PhotoAccessRequest::where('requester_id', $requesterId)
            ->where('profile_id', $profileId)
            ->where('status', 'granted')
            ->exists();
    }

    private function hasAcceptedConnection(string $userA, string $userB): bool
    {
        return \App\Models\ConnectionRequest::where(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userA)->where('receiver_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userB)->where('receiver_id', $userA);
        })->where('status', 'accepted')->exists();
    }
}
