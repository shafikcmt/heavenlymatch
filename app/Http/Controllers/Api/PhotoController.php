<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\PhotoAccessRequest;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class PhotoController extends Controller
{
    /**
     * GET /api/photo/{registration_id}/{photo_index}?token=xxx
     *
     * Serves profile images through the application (never directly from storage)
     * so we can enforce privacy rules server-side on every request.
     * The signed token prevents hotlinking and URL scraping.
     */
    public function serve(Request $request, string $registrationId, int $photoIndex = 0): Response
    {
        $request->validate(['token' => 'required|string']);

        // Verify signed URL token (prevents hotlinking)
        if (! $this->verifyPhotoToken($request->token, $registrationId, $request->user()?->registration_id)) {
            abort(403, 'Invalid or expired photo token.');
        }

        $biodata = Biodata::where('registration_id', $registrationId)->firstOrFail();
        $reg     = Registration::where('registration_id', $registrationId)->firstOrFail();

        $photos  = $biodata->photos ?? [];
        $photo   = $photos[$photoIndex] ?? null;

        if (! $photo) {
            return $this->defaultAvatar($biodata->gender);
        }

        $viewer     = $request->user();
        $shouldBlur = $this->resolveBlur($reg, $biodata, $viewer);

        $path = Storage::disk('private')->path($photo['path']);

        if (! file_exists($path)) {
            return $this->defaultAvatar($biodata->gender);
        }

        $image = Image::make($path);

        if ($shouldBlur) {
            // Heavy blur (20px) — data-URL is still sent so no server path is exposed
            $image->blur(20)->pixelate(12);
        }

        // Watermark with registration ID to deter screenshotting
        if (! $shouldBlur) {
            $image->text(
                $registrationId . ' | HeavenlyMatch',
                $image->width() / 2,
                $image->height() - 20,
                function ($font) {
                    $font->size(12);
                    $font->color([255, 255, 255, 80]); // semi-transparent white
                    $font->align('center');
                }
            );
        }

        return response($image->encode('jpg', 85), 200)
            ->header('Content-Type', 'image/jpeg')
            ->header('Cache-Control', 'private, max-age=300')  // 5-min browser cache only
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Content-Disposition', 'inline');          // no download prompt
    }

    /**
     * POST /api/photo/request-access/{registration_id}
     * Islamic mode: viewer requests to see blurred photos; profile owner must approve.
     */
    public function requestAccess(Request $request, string $registrationId): JsonResponse
    {
        $viewer = $request->user();

        if ($viewer->registration_id === $registrationId) {
            return response()->json(['message' => 'Cannot request access to own photos.'], 422);
        }

        $existing = PhotoAccessRequest::where('requester_id', $viewer->registration_id)
            ->where('profile_id', $registrationId)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Request already ' . $existing->status . '.',
                'status'  => $existing->status,
            ], 422);
        }

        PhotoAccessRequest::create([
            'requester_id' => $viewer->registration_id,
            'profile_id'   => $registrationId,
            'status'       => 'pending',
        ]);

        // Notify the profile owner
        event(new \App\Events\PhotoAccessRequested($viewer->registration_id, $registrationId));

        return response()->json(['message' => 'Photo access request sent.'], 201);
    }

    /**
     * POST /api/photo/respond-access/{request_id}
     * Profile owner grants or denies the access request.
     */
    public function respondAccess(Request $request, int $requestId): JsonResponse
    {
        $request->validate(['action' => 'required|in:grant,deny']);

        $accessRequest = PhotoAccessRequest::findOrFail($requestId);
        $owner         = $request->user();

        if ($accessRequest->profile_id !== $owner->registration_id) {
            abort(403, 'Not your photo access request.');
        }

        $accessRequest->update([
            'status'       => $request->action === 'grant' ? 'granted' : 'denied',
            'responded_at' => now(),
        ]);

        event(new \App\Events\PhotoAccessResponded($accessRequest));

        return response()->json(['message' => 'Response recorded.']);
    }

    /**
     * POST /api/photo/token
     * Issues a short-lived signed token for the frontend to embed in image URLs.
     * Called once per profile view, valid for 15 minutes.
     */
    public function issueToken(Request $request): JsonResponse
    {
        $request->validate(['profile_id' => 'required|string']);

        $viewerId = $request->user()?->registration_id ?? 'guest';
        $token = $this->generatePhotoToken($request->profile_id, $viewerId);

        return response()->json([
            'token'      => $token,
            'expires_in' => 900, // 15 minutes
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────

    private function resolveBlur(Registration $reg, Biodata $biodata, ?Registration $viewer): bool
    {
        $visibility = $reg->photo_visibility;

        // Always blur if set to 'blurred'
        if ($visibility === 'blurred') {
            return true;
        }

        // Islamic mode: blur unless photo access explicitly granted
        if ($reg->platform_mode === 'islamic') {
            if (! $viewer) {
                return true;
            }
            $hasAccess = PhotoAccessRequest::where('requester_id', $viewer->registration_id)
                ->where('profile_id', $reg->registration_id)
                ->where('status', 'granted')
                ->exists();
            return ! $hasAccess;
        }

        // 'members_only': show clearly only if viewer has accepted connection
        if ($visibility === 'members_only') {
            if (! $viewer) {
                return true;
            }
            $isConnected = \App\Models\ConnectionRequest::where(function ($q) use ($viewer, $reg) {
                $q->where('sender_id', $viewer->registration_id)
                  ->where('receiver_id', $reg->registration_id);
            })->orWhere(function ($q) use ($viewer, $reg) {
                $q->where('sender_id', $reg->registration_id)
                  ->where('receiver_id', $viewer->registration_id);
            })->where('status', 'accepted')->exists();

            return ! $isConnected;
        }

        // 'public' — never blur
        return false;
    }

    private function generatePhotoToken(string $profileId, string $viewerId): string
    {
        $payload = $profileId . '|' . $viewerId . '|' . now()->addMinutes(15)->timestamp;
        $signature = hash_hmac('sha256', $payload, config('app.key'));
        return base64_encode($payload . '|' . $signature);
    }

    private function verifyPhotoToken(string $token, string $profileId, ?string $viewerId): bool
    {
        $decoded = base64_decode($token);
        $parts   = explode('|', $decoded);

        if (count($parts) !== 4) {
            return false;
        }

        [$tokenProfileId, $tokenViewerId, $expiry, $signature] = $parts;

        if ($tokenProfileId !== $profileId) {
            return false;
        }

        if (time() > (int) $expiry) {
            return false;
        }

        $payload  = $tokenProfileId . '|' . $tokenViewerId . '|' . $expiry;
        $expected = hash_hmac('sha256', $payload, config('app.key'));

        return hash_equals($expected, $signature);
    }

    private function defaultAvatar(string $gender): Response
    {
        $path = public_path("images/avatar-{$gender}.svg");
        return response(file_get_contents($path), 200)->header('Content-Type', 'image/svg+xml');
    }
}
