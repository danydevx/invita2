<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $businesses = DB::table('businesses')->select('id', 'name')->orderBy('id')->get();

        foreach ($businesses as $business) {
            $primaryId = DB::table('business_galleries')
                ->where('listing_id', $business->id)
                ->where('is_primary', true)
                ->value('id');

            if (! $primaryId) {
                $primaryId = DB::table('business_galleries')->insertGetId([
                    'listing_id' => $business->id,
                    'name' => 'Galería principal',
                    'description' => 'Galería principal de '.$business->name,
                    'is_primary' => true,
                    'is_active' => true,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('business_gallery_images')
                ->where('listing_id', $business->id)
                ->whereNull('business_gallery_id')
                ->update(['business_gallery_id' => $primaryId]);
        }
    }

    public function down(): void
    {
        DB::table('business_gallery_images')
            ->whereNull('business_gallery_id')
            ->update(['business_gallery_id' => null]);
    }
};
