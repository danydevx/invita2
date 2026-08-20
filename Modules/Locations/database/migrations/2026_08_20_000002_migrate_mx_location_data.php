<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $countryId = DB::table('countries')->insertGetId([
            'name' => 'México',
            'code' => 'MX',
            'currency' => 'MXN',
            'currency_symbol' => '$',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stateMappings = [];
        $states = DB::table('mx_states')->get();
        foreach ($states as $state) {
            $lat = $state->lat ?? null;
            $lng = $state->lng ?? null;

            if ($lat && abs($lat) > 1000) {
                $lat = $lat / 1000000;
            }
            if ($lng && abs($lng) > 1000) {
                $lng = $lng / 1000000;
            }

            $stateId = DB::table('states')->insertGetId([
                'country_id' => $countryId,
                'code' => $state->code,
                'name' => $state->name,
                'lat' => $lat,
                'lng' => $lng,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $stateMappings[$state->code] = $stateId;
        }

        $municipalities = DB::table('mx_municipalities')->get();
        foreach ($municipalities as $municipality) {
            if (isset($stateMappings[$municipality->state_code])) {
                $lat = $municipality->lat ?? null;
                $lng = $municipality->lng ?? null;

                if ($lat && abs($lat) > 1000) {
                    $lat = $lat / 1000000;
                }
                if ($lng && abs($lng) > 1000) {
                    $lng = $lng / 1000000;
                }

                DB::table('municipalities')->insert([
                    'state_id' => $stateMappings[$municipality->state_code],
                    'country_id' => $countryId,
                    'code' => $municipality->code,
                    'name' => $municipality->name,
                    'is_metropolitan' => $municipality->is_metropolitan ?? false,
                    'lat' => $lat,
                    'lng' => $lng,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('municipalities')->delete();
        DB::table('states')->delete();
        DB::table('countries')->delete();
    }
};
