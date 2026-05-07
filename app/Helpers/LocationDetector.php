<?php

namespace App\Helpers;

use App\Models\City;
use App\Models\Region;
use Illuminate\Support\Facades\Http;

/**
 * Detects user's region/city for location-based homepage filtering.
 *
 * Priority: Session (user preference) > Default fallback (Cebu / Central Visayas)
 *
 * IP Geolocation (fromIp()) is available but DISABLED by default.
 * Re-enable by uncommenting the call in detectCity() if and when
 * we have listings across all regions and user consent is handled.
 */
class LocationDetector
{
    /**
     * Detect user's city from session, then IP, then fallback.
     */
    public static function detectCity(): ?City
    {
        // 1. User explicitly set a location in session
        $sessionCityId = session('detected_city_id');
        if ($sessionCityId) {
            $city = City::find($sessionCityId);
            if ($city) return $city;
        }

        // ⛔ IP geolocation is disabled due to privacy concerns,
        // inaccurate results (VPN/CG-NAT), and empty listing pages.
        // To re-enable, uncomment the three lines below:
        // $city = self::fromIp();
        // if ($city) return $city;

        return null;
    }

    /**
     * Detect user's region (province or region level) for filtering.
     * Returns the highest-precision location available.
     */
    public static function detectRegion(): ?Region
    {
        $city = self::detectCity();
        if ($city && $city->region) {
            return $city->region;
        }

        // Fallback: try parent's region
        if ($city && $city->parent && $city->parent->region) {
            return $city->parent->region;
        }

        return null;
    }

    /**
     * Detect the province (parent city of type province) from user location.
     */
    public static function detectProvince(): ?City
    {
        $city = self::detectCity();
        if (!$city) return null;

        // If city itself is a province-level entry
        if ($city->type === 'province') {
            // Check if it has child municipalities with listings
            return $city;
        }

        // If city has a parent (municipality → province)
        if ($city->parent && $city->parent->type === 'province') {
            return $city->parent;
        }

        return null;
    }

    /**
     * Get user's approximate location from IP via ip-api.com (free, no key needed).
     */
    protected static function fromIp(): ?City
    {
        $ip = request()->ip();

        // Don't geolocate local/private IPs
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost']) ||
            str_starts_with($ip, '192.168.') ||
            str_starts_with($ip, '10.') ||
            str_starts_with($ip, '172.')) {
            return null;
        }

        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=city,region,countryCode,query");
            if (!$response->successful()) return null;

            $data = $response->json();
            if (($data['countryCode'] ?? '') !== 'PH') return null;

            $cityName = $data['city'] ?? '';
            $regionName = $data['region'] ?? '';

            // Try to match by city name first
            $city = City::where('is_active', true)
                ->where('name', 'LIKE', "%{$cityName}%")
                ->where('type', '!=', 'province')
                ->first();

            if ($city) return $city;

            // Fallback: try matching region
            $region = Region::where('name', 'LIKE', "%{$regionName}%")->first();
            if ($region) {
                // Return first city in that region as reference
                return City::where('is_active', true)
                    ->where('region_id', $region->id)
                    ->where('type', '!=', 'province')
                    ->first();
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Default fallback region (Central Visayas — Cebu).
     */
    public static function defaultRegion(): ?Region
    {
        return Region::where('slug', 'central-visayas')->first();
    }

    /**
     * Default fallback province (Cebu).
     */
    public static function defaultProvince(): ?City
    {
        return City::where('slug', 'cebu')->where('type', 'province')->first();
    }

    /**
     * Set a custom city ID in session.
     */
    public static function setSessionCity(int $cityId): void
    {
        session(['detected_city_id' => $cityId]);
    }

    /**
     * Clear session location.
     */
    public static function clearSession(): void
    {
        session()->forget('detected_city_id');
    }
}
