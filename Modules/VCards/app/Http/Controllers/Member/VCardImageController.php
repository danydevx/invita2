<?php

namespace Modules\VCards\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Listings\Models\Listing;
use Modules\VCards\Models\VCard;

class VCardImageController extends Controller
{
    public function uploadLogo(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $file = $request->file('image');
        if (!$file) {
            return response()->json(['error' => 'No se encontró imagen.'], 422);
        }

        $maxSize = 2048;
        if ($file->getSize() > $maxSize * 1024) {
            return response()->json(['error' => 'La imagen debe ser menor a 2MB.'], 422);
        }

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/svg+xml'])) {
            return response()->json(['error' => 'Solo se permiten archivos JPG, PNG, WebP o SVG.'], 422);
        }

        if ($vcard->logo) {
            Storage::disk('public')->delete($vcard->logo);
        }

        $path = $file->store('vcard-logos', 'public');

        $vcard->update(['logo' => $path]);

        return response()->json([
            'logo' => $path,
            'logo_url' => Storage::url($path),
        ]);
    }

    public function deleteLogo(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        if ($vcard->logo) {
            Storage::disk('public')->delete($vcard->logo);
            $vcard->update(['logo' => null]);
        }

        return response()->json(['success' => true]);
    }

    public function uploadBadge(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $file = $request->file('image');
        if (!$file) {
            return response()->json(['error' => 'No se encontró imagen.'], 422);
        }

        $maxSize = 2048;
        if ($file->getSize() > $maxSize * 1024) {
            return response()->json(['error' => 'La imagen debe ser menor a 2MB.'], 422);
        }

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/svg+xml'])) {
            return response()->json(['error' => 'Solo se permiten archivos JPG, PNG, WebP o SVG.'], 422);
        }

        if ($vcard->badge) {
            Storage::disk('public')->delete($vcard->badge);
        }

        $path = $file->store('vcard-badges', 'public');

        $vcard->update(['badge' => $path]);

        return response()->json([
            'badge' => $path,
            'badge_url' => Storage::url($path),
        ]);
    }

    public function deleteBadge(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        if ($vcard->badge) {
            Storage::disk('public')->delete($vcard->badge);
            $vcard->update(['badge' => null]);
        }

        return response()->json(['success' => true]);
    }

    public function uploadProfilePhoto(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $file = $request->file('image');
        if (!$file) {
            return response()->json(['error' => 'No se encontró imagen.'], 422);
        }

        $maxSize = 2048;
        if ($file->getSize() > $maxSize * 1024) {
            return response()->json(['error' => 'La imagen debe ser menor a 2MB.'], 422);
        }

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])) {
            return response()->json(['error' => 'Solo se permiten archivos JPG, PNG o WebP.'], 422);
        }

        if ($vcard->profile_photo) {
            Storage::disk('public')->delete($vcard->profile_photo);
        }

        $path = $file->store('vcard-photos', 'public');

        $vcard->update(['profile_photo' => $path]);

        return response()->json([
            'profile_photo' => $path,
            'profile_photo_url' => Storage::url($path),
        ]);
    }

    public function deleteProfilePhoto(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        if ($vcard->profile_photo) {
            Storage::disk('public')->delete($vcard->profile_photo);
            $vcard->update(['profile_photo' => null]);
        }

        return response()->json(['success' => true]);
    }

    public function uploadHeroBackgroundImage(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $file = $request->file('image');
        if (!$file) {
            return response()->json(['error' => 'No se encontró imagen.'], 422);
        }

        $maxSize = 4096;
        if ($file->getSize() > $maxSize * 1024) {
            return response()->json(['error' => 'La imagen debe ser menor a 4MB.'], 422);
        }

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/svg+xml'])) {
            return response()->json(['error' => 'Solo se permiten archivos JPG, PNG, WebP o SVG.'], 422);
        }

        if ($vcard->hero_background_image) {
            Storage::disk('public')->delete($vcard->hero_background_image);
        }

        $path = $file->store('vcard-hero-backgrounds', 'public');

        $vcard->update(['hero_background_image' => $path]);

        return response()->json([
            'hero_background_image' => $path,
            'hero_background_image_url' => Storage::url($path),
        ]);
    }

    public function deleteHeroBackgroundImage(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        if ($vcard->hero_background_image) {
            Storage::disk('public')->delete($vcard->hero_background_image);
            $vcard->update(['hero_background_image' => null]);
        }

        return response()->json(['success' => true]);
    }
}
