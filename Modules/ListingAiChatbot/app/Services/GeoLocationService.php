<?php

namespace Modules\ListingAiChatbot\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeoLocationService
{
    private const CACHE_TTL = 60 * 60 * 24 * 30;

    public function resolve(string $ipAddress): array
    {
        if (!$ipAddress || $this->isLocalIp($ipAddress)) {
            return [
                'country' => 'Unknown',
                'city' => 'Unknown',
                'country_code' => 'XX',
            ];
        }

        $cacheKey = "geoip:{$ipAddress}";

        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(3)
                ->get("http://ip-api.com/json/{$ipAddress}", [
                    'fields' => 'country,countryCode,city,status',
                ]);

            if ($response->successful() && $response->json('status') === 'success') {
                $data = [
                    'country' => $response->json('country', 'Unknown'),
                    'city' => $response->json('city', 'Unknown'),
                    'country_code' => $response->json('countryCode', 'XX'),
                ];
            } else {
                $data = $this->getDefault();
            }
        } catch (\Exception $e) {
            $data = $this->getDefault();
        }

        Cache::put($cacheKey, $data, self::CACHE_TTL);

        return $data;
    }

    private function isLocalIp(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1', 'localhost']) ||
               str_starts_with($ip, '192.168.') ||
               str_starts_with($ip, '10.') ||
               str_starts_with($ip, '172.16.');
    }

    private function getDefault(): array
    {
        return [
            'country' => 'Unknown',
            'city' => 'Unknown',
            'country_code' => 'XX',
        ];
    }
}
