<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class RajaOngkirLocationController extends Controller
{
    public function __construct(protected RajaOngkirService $raja)
    {
    }

    public function provinces(): JsonResponse
    {
        try {
            $provinces = $this->raja->provinces();
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Gagal memuat daftar provinsi: ' . $exception->getMessage(),
            ], 500);
        }

        return response()->json(['data' => $provinces]);
    }

    public function cities(Request $request): JsonResponse
    {
        $request->validate([
            'province_id' => ['nullable', 'string'],
        ]);

        try {
            $cities = $this->raja->cities($request->input('province_id'));
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Gagal memuat daftar kota/kabupaten: ' . $exception->getMessage(),
            ], 500);
        }

        return response()->json(['data' => $cities]);
    }

    public function districts(Request $request): JsonResponse
    {
        $request->validate([
            'city_id' => ['required', 'string'],
        ]);

        try {
            $districts = $this->raja->districts($request->input('city_id'));
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Gagal memuat daftar kecamatan/desa: ' . $exception->getMessage(),
            ], 500);
        }

        return response()->json(['data' => $districts]);
    }
}
