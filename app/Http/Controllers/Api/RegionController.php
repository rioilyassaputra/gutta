<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Internal API proxy for RajaOngkir.
 * API key stays server-side, never exposed to frontend.
 */
class RegionController extends Controller
{
    public function __construct(private readonly RajaOngkirService $rajaOngkir) {}

    public function provinces(): JsonResponse
    {
        $provinces = $this->rajaOngkir->getProvinces();
        return response()->json($provinces);
    }

    public function cities(Request $request): JsonResponse
    {
        $provinceId = (int) $request->query('province_id', 0);
        $cities = $this->rajaOngkir->getCities($provinceId);
        return response()->json($cities);
    }

    public function cost(Request $request): JsonResponse
    {
        $request->validate([
            'destination' => 'required|integer',
            'weight'      => 'required|integer|min:1',
        ]);

        $options = $this->rajaOngkir->getCost(
            destination: (int) $request->destination,
            weightGrams: (int) $request->weight
        );

        return response()->json($options);
    }
}
