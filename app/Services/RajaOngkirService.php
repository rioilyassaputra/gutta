<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    private string $apiKey;
    private string $baseUrl;
    private int $originCityId;

    public function __construct()
    {
        $this->apiKey       = config('rajaongkir.api_key', '');
        $this->baseUrl      = config('rajaongkir.base_url');
        $this->originCityId = (int) config('rajaongkir.origin_city_id');
    }

    /**
     * Get shipping cost options.
     * Results are cached in Redis for 45 minutes.
     *
     * @return array List of courier options with cost details
     */
    public function getCost(int $destination, int $weightGrams): array
    {
        // If no API key configured, return empty (skip gracefully)
        if (empty($this->apiKey)) {
            return $this->getMockShippingOptions();
        }

        $origin = $this->originCityId;
        $cacheKey = "ongkir:{$origin}:{$destination}:{$weightGrams}";

        return Cache::remember($cacheKey, 2700, function () use ($origin, $destination, $weightGrams) {
            try {
                $couriers = explode(',', config('rajaongkir.couriers', 'jne,jnt,sicepat,pos'));
                $results = [];

                foreach ($couriers as $courier) {
                    $response = Http::asForm()->withHeaders([
                        'key' => $this->apiKey,
                    ])->post("{$this->baseUrl}/calculate/domestic-cost", [
                        'origin'      => (int) $origin,
                        'destination' => (int) $destination,
                        'weight'      => (int) $weightGrams,
                        'courier'     => trim($courier),
                    ]);

                    if ($response->successful()) {
                        $costs = $response->json('data', []);
                        if (is_array($costs)) {
                            foreach ($costs as $cost) {
                                $results[] = [
                                    'courier'      => $cost['code'] ?? trim($courier),
                                    'courier_name' => $cost['name'] ?? strtoupper(trim($courier)),
                                    'service'      => $cost['service'] ?? '',
                                    'description'  => $cost['description'] ?? '',
                                    'cost'         => (float) ($cost['cost'] ?? 0),
                                    'etd'          => trim(str_ireplace(['days', 'day', 'hari'], '', $cost['etd'] ?? '')),
                                ];
                            }
                        }
                    }
                }

                // Filter out cargo/trucking services if total weight is light (< 5 kg) to prevent confusing tariffs
                if ($weightGrams < 5000) {
                    $results = array_filter($results, function ($item) {
                        $service = strtoupper($item['service'] ?? '');
                        $description = strtolower($item['description'] ?? '');
                        
                        $isCargo = str_contains($service, 'JTR') || 
                                   str_contains($service, 'GOKIL') || 
                                   str_contains($service, 'CARGO') || 
                                   str_contains($service, 'KARGO') || 
                                   str_contains($service, 'TRUCKING') ||
                                   str_contains($description, 'trucking') ||
                                   str_contains($description, 'cargo') ||
                                   str_contains($description, 'kargo');

                        return ! $isCargo;
                    });

                    $results = array_values($results);
                }

                return $results;
            } catch (\Exception $e) {
                Log::error('RajaOngkir API error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get all provinces.
     */
    public function getProvinces(): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockProvinces();
        }

        return Cache::remember('rajaongkir:provinces', 86400, function () {
            try {
                $response = Http::withHeaders(['key' => $this->apiKey])
                    ->get("{$this->baseUrl}/destination/province");

                $provinces = $response->json('data', []);
                if (!is_array($provinces)) {
                    return [];
                }
                
                $results = [];
                foreach ($provinces as $prov) {
                    $results[] = [
                        'province_id' => (string) ($prov['id'] ?? ''),
                        'province'    => $prov['name'] ?? '',
                    ];
                }
                return $results;
            } catch (\Exception $e) {
                Log::error('RajaOngkir provinces error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get cities by province ID.
     */
    public function getCities(int $provinceId): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockCities();
        }

        return Cache::remember("rajaongkir:cities:{$provinceId}", 86400, function () use ($provinceId) {
            try {
                $response = Http::withHeaders(['key' => $this->apiKey])
                    ->get("{$this->baseUrl}/destination/city/{$provinceId}");

                $cities = $response->json('data', []);
                if (!is_array($cities)) {
                    return [];
                }

                $results = [];
                foreach ($cities as $c) {
                    $results[] = [
                        'city_id'   => (string) ($c['id'] ?? ''),
                        'city_name' => $c['name'] ?? '',
                        'type'      => '',
                    ];
                }
                return $results;
            } catch (\Exception $e) {
                Log::error('RajaOngkir cities error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Mock shipping options — used when no API key is configured (dev mode).
     */
    private function getMockShippingOptions(): array
    {
        return [
            ['courier' => 'jne', 'courier_name' => 'JNE', 'service' => 'REG', 'description' => 'Layanan Reguler', 'cost' => 15000, 'etd' => '3-4'],
            ['courier' => 'jne', 'courier_name' => 'JNE', 'service' => 'YES', 'description' => 'Yakin Esok Sampai', 'cost' => 35000, 'etd' => '1-1'],
            ['courier' => 'jnt', 'courier_name' => 'J&T', 'service' => 'EZ', 'description' => 'J&T Express', 'cost' => 12000, 'etd' => '3-5'],
            ['courier' => 'sicepat', 'courier_name' => 'SiCepat', 'service' => 'HALU', 'description' => 'SiCepat Halu', 'cost' => 10000, 'etd' => '3-5'],
        ];
    }

    /**
     * Mock provinces for dev without API key.
     */
    private function getMockProvinces(): array
    {
        return [
            ['province_id' => '10', 'province' => 'Jawa Tengah'],
            ['province_id' => '9', 'province' => 'Jawa Timur'],
            ['province_id' => '6', 'province' => 'DKI Jakarta'],
            ['province_id' => '11', 'province' => 'Jawa Barat'],
            ['province_id' => '3', 'province' => 'Bali'],
        ];
    }

    /**
     * Mock cities for dev without API key.
     */
    private function getMockCities(): array
    {
        return [
            ['city_id' => '75', 'city_name' => 'Boyolali', 'type' => 'Kabupaten'],
            ['city_id' => '499', 'city_name' => 'Surakarta', 'type' => 'Kota'],
            ['city_id' => '114', 'city_name' => 'Jakarta Pusat', 'type' => 'Kota'],
            ['city_id' => '22', 'city_name' => 'Bandung', 'type' => 'Kota'],
            ['city_id' => '444', 'city_name' => 'Surabaya', 'type' => 'Kota'],
        ];
    }
}
