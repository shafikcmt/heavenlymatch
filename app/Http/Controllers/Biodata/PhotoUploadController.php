<?php

declare(strict_types=1);

namespace App\Http\Controllers\Biodata;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPhotoRequest;
use App\Models\PhotoAccessRequest;
use App\Models\Registration;
use App\Services\PhotoPrivacyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Inertia\Inertia;
use Inertia\Response;

class PhotoUploadController extends Controller
{
    private const MAX_PHOTOS = 6;
    private const DISK       = 'private';

    public function __construct(private PhotoPrivacyService $photoPrivacy) {}

    // GET /profile/photos
    public function index(): Response
    {
        /** @var Registration $user */
        $user    = Auth::user();
        $biodata = $user->biodata;
        $photos  = $biodata?->photos ?? [];

        // Generate signed serve-URLs server-side so the frontend never handles raw paths
        $photoUrls = array_map(
            fn (int $i) => $this->photoPrivacy->photoUrl($user->registration_id, $i, $user->registration_id),
            array_keys($photos)
        );

        // Incoming photo access requests (pending only, with requester name)
        $incomingRequests = PhotoAccessRequest::where('profile_id', $user->registration_id)
            ->where('status', 'pending')
            ->with('requester:registration_id,name')
            ->latest()
            ->get()
            ->map(fn ($r) => [
                'id'            => $r->id,
                'requester_id'  => $r->requester_id,
                'requester_name'=> $r->requester?->name ?? $r->requester_id,
                'created_at'    => $r->created_at?->diffForHumans(),
            ])
            ->all();

        return Inertia::render('Profile/Photos', [
            'photos'           => array_values($photos),
            'photoUrls'        => array_values($photoUrls),
            'photoVisibility'  => $user->photo_visibility ?? 'members_only',
            'maxPhotos'        => self::MAX_PHOTOS,
            'hasBiodata'       => $biodata !== null,
            'incomingRequests' => $incomingRequests,
        ]);
    }

    // POST /profile/photos
    public function store(UploadPhotoRequest $request): RedirectResponse
    {
        /** @var Registration $user */
        $user    = Auth::user();
        $biodata = $user->biodata;

        if (! $biodata) {
            return back()->withErrors(['photo' => __('biodata.photo_no_biodata')]);
        }

        $photos = $biodata->photos ?? [];

        if (count($photos) >= self::MAX_PHOTOS) {
            return back()->withErrors([
                'photo' => __('biodata.photo_limit_reached', ['max' => self::MAX_PHOTOS]),
            ]);
        }

        $file      = $request->file('photo');
        $filename  = Str::uuid() . '.jpg';
        $dir       = 'photos/' . $user->registration_id;
        $storagePath = "{$dir}/{$filename}";

        // Process: auto-orient (fix EXIF rotation), resize, strip metadata, save as JPEG
        try {
            $processed = Image::make($file)
                ->orientate()
                ->resize(1200, 1200, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 85);

            Storage::disk(self::DISK)->put($storagePath, (string) $processed);
        } catch (\Throwable $e) {
            // Intervention Image failed — store original file as-is
            Storage::disk(self::DISK)->putFileAs($dir, $file, $filename);
        }

        $isPrimary = empty($photos); // first-ever photo becomes primary

        $photos[] = [
            'path'        => $storagePath,
            'is_primary'  => $isPrimary,
            'uploaded_at' => now()->toISOString(),
        ];

        $biodata->update(['photos' => $photos]);

        return back()->with('success', __('biodata.photo_uploaded_success'));
    }

    // DELETE /profile/photos/{index}
    public function destroy(int $index): RedirectResponse
    {
        /** @var Registration $user */
        $user    = Auth::user();
        $biodata = $user->biodata;

        $photos = $biodata?->photos ?? [];

        if (! isset($photos[$index])) {
            return back()->with('error', __('biodata.photo_not_found'));
        }

        $wasPrimary  = (bool) ($photos[$index]['is_primary'] ?? false);
        $storagePath = $photos[$index]['path'] ?? null;

        if ($storagePath && Storage::disk(self::DISK)->exists($storagePath)) {
            Storage::disk(self::DISK)->delete($storagePath);
        }

        array_splice($photos, $index, 1);
        $photos = array_values($photos);

        // Promote the first remaining photo to primary when the primary was deleted
        if ($wasPrimary && ! empty($photos)) {
            $photos[0]['is_primary'] = true;
        }

        $biodata->update(['photos' => $photos]);

        return back()->with('success', __('biodata.photo_deleted_success'));
    }

    // PUT /profile/photos/{index}/primary
    public function setPrimary(int $index): RedirectResponse
    {
        /** @var Registration $user */
        $user    = Auth::user();
        $biodata = $user->biodata;

        $photos = $biodata?->photos ?? [];

        if (! isset($photos[$index])) {
            return back()->with('error', __('biodata.photo_not_found'));
        }

        foreach ($photos as $i => $photo) {
            $photos[$i]['is_primary'] = ($i === $index);
        }

        $biodata->update(['photos' => $photos]);

        return back()->with('success', __('biodata.photo_primary_set_success'));
    }

    // PUT /profile/photos/visibility
    public function updateVisibility(Request $request): RedirectResponse
    {
        $request->validate([
            'photo_visibility' => ['required', 'in:public,members_only,blurred'],
        ]);

        /** @var Registration $user */
        $user = Auth::user();
        $user->update(['photo_visibility' => $request->photo_visibility]);

        return back()->with('success', __('biodata.photo_visibility_updated'));
    }

    // POST /profile/photos/requests/{requestId}/respond
    public function respondRequest(Request $request, int $requestId): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:granted,denied'],
        ]);

        /** @var Registration $user */
        $user = Auth::user();

        $accessRequest = PhotoAccessRequest::where('id', $requestId)
            ->where('profile_id', $user->registration_id)
            ->where('status', 'pending')
            ->firstOrFail();

        $accessRequest->update([
            'status'       => $request->action,
            'responded_at' => now(),
        ]);

        $flashKey = $request->action === 'granted'
            ? __('biodata.photo_access_granted')
            : __('biodata.photo_access_denied');

        return back()->with('success', $flashKey);
    }
}
