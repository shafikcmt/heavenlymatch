<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\PhotoAccessRequest;
use App\Models\Registration;
use App\Services\PhotoPrivacyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PhotoController extends Controller
{
    public function __construct(private PhotoPrivacyService $photoPrivacy) {}

    /**
     * GET /photo/{registrationId}/{photoIndex}?token=xxx
     *
     * Serves photos through the application — privacy enforced server-side on every request.
     * Raw storage paths are never exposed to the frontend.
     */
    public function serve(Request $request, string $registrationId, int $photoIndex = 0): Response
    {
        $request->validate(['token' => 'required|string']);

        if (! $this->photoPrivacy->verifyToken($request->token, $registrationId)) {
            abort(403, 'Invalid or expired photo token.');
        }

        $biodata = Biodata::where('registration_id', $registrationId)->first();
        $reg     = Registration::where('registration_id', $registrationId)->firstOrFail();

        $photos = $biodata?->photos ?? [];
        $photo  = $photos[$photoIndex] ?? null;

        if (! $photo || empty($photo['path'])) {
            // Use gender from Registration (not Biodata — Biodata has no gender column)
            return $this->defaultAvatar($reg->gender);
        }

        $path = Storage::disk('private')->path($photo['path']);

        if (! file_exists($path)) {
            return $this->defaultAvatar($reg->gender);
        }

        $viewer     = $request->user();
        $shouldBlur = $this->photoPrivacy->shouldBlur($reg, $viewer);

        $image = Image::make($path);

        if ($shouldBlur) {
            $image->blur(20)->pixelate(12);
        } else {
            // Subtle watermark to deter screenshotting (only for visible photos)
            $image->text(
                $registrationId . ' | HeavenlyMatch',
                (int) ($image->width() / 2),
                $image->height() - 20,
                function ($font) {
                    $font->size(11);
                    $font->color([255, 255, 255, 60]);
                    $font->align('center');
                }
            );
        }

        return response((string) $image->encode('jpg', 85), 200)
            ->header('Content-Type', 'image/jpeg')
            ->header('Cache-Control', 'private, max-age=300')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Content-Disposition', 'inline');
    }

    /**
     * POST /api/photo/request-access/{registrationId}
     * Islamic mode: viewer requests permission to see blurred photos.
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

        return response()->json(['message' => 'Photo access request sent.'], 201);
    }

    /**
     * POST /api/photo/respond-access/{requestId}
     * Profile owner grants or denies a photo access request.
     */
    public function respondAccess(Request $request, int $requestId): JsonResponse
    {
        $request->validate(['action' => 'required|in:grant,deny']);

        $accessRequest = PhotoAccessRequest::findOrFail($requestId);
        $owner         = $request->user();

        if ($accessRequest->profile_id !== $owner->registration_id) {
            abort(403);
        }

        $accessRequest->update([
            'status'       => $request->action === 'grant' ? 'granted' : 'denied',
            'responded_at' => now(),
        ]);

        return response()->json(['message' => 'Response recorded.']);
    }

    /**
     * POST /api/photo/token
     * Issues a short-lived HMAC token for embedding in photo URLs.
     * Valid for 15 minutes.
     */
    public function issueToken(Request $request): JsonResponse
    {
        $request->validate(['profile_id' => 'required|string']);

        $viewerId = $request->user()?->registration_id ?? 'guest';
        $token    = $this->photoPrivacy->issueToken($request->profile_id, $viewerId);

        return response()->json([
            'token'      => $token,
            'expires_in' => 900,
        ]);
    }

    private function defaultAvatar(string $gender): Response
    {
        $path = public_path('images/avatar-' . ($gender === 'female' ? 'female' : 'male') . '.svg');

        if (! file_exists($path)) {
            // Inline fallback SVG if the file doesn't exist yet
            $color = $gender === 'female' ? '#f9a8d4' : '#93c5fd';
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">'
                . '<circle cx="50" cy="50" r="50" fill="' . $color . '"/>'
                . '<circle cx="50" cy="38" r="16" fill="#fff"/>'
                . '<ellipse cx="50" cy="85" rx="28" ry="20" fill="#fff"/>'
                . '</svg>';
            return response($svg, 200)->header('Content-Type', 'image/svg+xml');
        }

        return response(file_get_contents($path), 200)->header('Content-Type', 'image/svg+xml');
    }
}
