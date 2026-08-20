<?php

namespace Tests\Feature\Member;

use App\Http\Controllers\Member\GalleryController;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Modules\Listings\Models\Listing;
use Modules\ListingGallery\Models\ListingGalleryImage;
use Tests\TestCase;

class GalleryBulkUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_upload_ten_images_in_one_request(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $business = Listing::withoutEvents(fn () => Listing::create([
            'user_id' => $user->id,
            'name' => 'Negocio',
            'slug' => 'negocio',
            'listing_type' => 'generic',
        ]));
        $files = collect(range(1, 10))
            ->map(fn (int $index) => UploadedFile::fake()->image("image-{$index}.jpg"))
            ->all();
        $request = Request::create("/member/businesses/{$business->id}/gallery", 'POST', [
            'title' => 'Galeria',
        ], [], ['files' => $files]);
        $request->setUserResolver(fn () => $user);
        $activity = Mockery::mock(ActivityService::class);
        $activity->shouldReceive('log')->times(10);

        app(GalleryController::class)->store($request, $business, $activity);

        $this->assertSame(10, ListingGalleryImage::where('listing_id', $business->id)->count());
    }

    public function test_more_than_ten_images_are_rejected(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $business = Listing::withoutEvents(fn () => Listing::create([
            'user_id' => $user->id,
            'name' => 'Negocio',
            'slug' => 'negocio',
            'listing_type' => 'generic',
        ]));
        $files = collect(range(1, 11))
            ->map(fn (int $index) => UploadedFile::fake()->image("image-{$index}.jpg"))
            ->all();
        $request = Request::create("/member/businesses/{$business->id}/gallery", 'POST', [], [], ['files' => $files]);
        $request->setUserResolver(fn () => $user);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(GalleryController::class)->store(
            $request,
            $business,
            Mockery::mock(ActivityService::class)
        );
    }
}
