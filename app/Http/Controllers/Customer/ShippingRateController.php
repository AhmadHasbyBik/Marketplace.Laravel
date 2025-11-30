<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use App\Services\RajaOngkirService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Throwable;

class ShippingRateController extends Controller
{
    public function __construct(protected RajaOngkirService $raja)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'destination_city_id' => ['required', 'string'],
            'destination_city_name' => ['required', 'string'],
            'destination_province' => ['nullable', 'string'],
            'destination_district_id' => ['required', 'string'],
            'destination_district_name' => ['required', 'string'],
            'weight' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'regex:/^(?:lowest|\\d+(?:\\.\\d+)?)$/i'],
        ]);

        $quickItems = session('checkout.quick_items', []);
        $cartItems = ! empty($quickItems) ? $quickItems : session('cart.items', []);
        if (empty($cartItems)) {
            return response()->json(['message' => 'Keranjang kosong'], 422);
        }

        $originCityId = config('services.rajaongkir.origin_city_id');
        if (empty($originCityId)) {
            return response()->json(['message' => 'Origin city belum dikonfigurasi'], 422);
        }

        $couriers = collect(config('services.rajaongkir.couriers', []))->filter()->values();
        if ($couriers->isEmpty()) {
            return response()->json(['message' => 'Tidak ada kurir tersedia'], 422);
        }

        $destinationCityId = $request->input('destination_city_id');
        $destinationCityName = $request->input('destination_city_name');
        $destinationProvince = $request->input('destination_province');
        $weight = max(1, (int) $request->input('weight', 0));
        $price = null;
        if ($request->filled('price')) {
            $rawPrice = (string) $request->input('price');
            if (is_numeric($rawPrice)) {
                $price = max(0, (float) $rawPrice);
            } else {
                $trimmed = trim($rawPrice);
                if ($trimmed !== '') {
                    $price = $trimmed;
                }
            }
        }

        $fallbackMessage = null;
        $destinationDistrictId = $request->input('destination_district_id');
        $destinationDistrictName = $request->input('destination_district_name');

        $destination = [
            'city_id' => $destinationCityId,
            'district_id' => $destinationDistrictId,
            'city' => $destinationCityName,
            'district' => $destinationDistrictName,
            'province' => $destinationProvince,
        ];

        try {
            $rates = $this->buildRatesFromRaja($couriers, $originCityId, $destinationDistrictId, $weight, $price);
        } catch (RequestException $exception) {
            $fallbackMessage = 'Tarif dihitung berdasarkan metode pengiriman manual karena layanan RajaOngkir tidak tersedia.';
            $rates = $this->fallbackRates($couriers);

            if ($rates->isEmpty()) {
                return response()->json(['message' => 'Gagal mengambil tarif ongkir: ' . $exception->getMessage()], 500);
            }
        } catch (Throwable $exception) {
            return response()->json(['message' => 'Gagal mengambil tarif ongkir: ' . $exception->getMessage()], 500);
        }

        return response()->json([
            'data' => $rates,
            'destination' => $destination,
            'total_weight' => $weight,
            'fallback_message' => $fallbackMessage,
        ]);
    }

    protected function buildRatesFromRaja(Collection $couriers, string $originCityId, string $destinationId, int $totalWeight, string|float|null $price): Collection
    {
        $availableCouriers = $couriers->filter()->values();
        if ($availableCouriers->isEmpty()) {
            return collect();
        }

        $methodMap = ShippingMethod::whereIn('slug', $availableCouriers)
            ->where('type', 'courier')
            ->get()
            ->keyBy('slug');

        $courierPayload = $availableCouriers->implode(':');
        $costs = $this->raja->cost($originCityId, $destinationId, $totalWeight, $courierPayload, $price);

        return collect($costs)->map(function ($carrier) use ($methodMap) {
            $code = (string) Arr::get($carrier, 'code', '');
            if ($code === '') {
                return null;
            }

            $method = $methodMap->get($code);

            $services = collect(Arr::get($carrier, 'costs', []))->map(function ($service) use ($code, $method) {
                $costEntry = Arr::get($service, 'cost', Arr::get($service, 'price', 0));
                $value = is_array($costEntry)
                    ? Arr::get($costEntry, 'value', Arr::get($costEntry, 'price', 0))
                    : $costEntry;

                return [
                    'courier' => $code,
                    'method_id' => $method?->id,
                    'method_name' => $method?->name,
                    'service' => Arr::get($service, 'service', Arr::get($service, 'name')),
                    'description' => Arr::get($service, 'description'),
                    'cost' => (int) max(0, $value ?? 0),
                    'etd' => Arr::get($service, 'etd'),
                ];
            })->filter(function ($info) {
                return (int) ($info['cost'] ?? 0) > 0;
            })->values();

            if ($services->isEmpty()) {
                return null;
            }

            return [
                'courier' => $code,
                'method_id' => $method?->id,
                'method_name' => $method?->name,
                'services' => $services,
            ];
        })->filter()->values();
    }

    protected function fallbackRates(Collection $couriers): Collection
    {
        $methods = ShippingMethod::where('is_active', true)->get();

        $methodSources = $methods->map(function (ShippingMethod $method) {
            return [
                'id' => $method->id,
                'slug' => $method->slug,
                'name' => $method->name,
                'description' => $method->description,
                'type' => $method->type,
                'flat_rate' => $method->flat_rate,
                'estimation' => $method->estimation,
            ];
        });

        if ($methodSources->isEmpty()) {
            $methodSources = collect($this->defaultShippingMethods());
        }

        $methodsBySlug = $methodSources->keyBy('slug');

        $courierRates = $couriers->map(function (string $courier) use ($methodsBySlug) {
            $method = $methodsBySlug->get($courier);
            if (! $method) {
                return null;
            }

            return $this->buildFallbackRate($courier, $method);
        })->filter()->values();

        $pickupRates = $methodsBySlug->filter(fn ($method) => ($method['type'] ?? null) === 'pickup')
            ->map(fn ($method) => $this->buildFallbackRate('pickup', $method))
            ->values();

        return $courierRates->merge($pickupRates)->values();
    }

    protected function buildFallbackRate(string $courier, array $method): array
    {
        $price = max(0, round((float) ($method['flat_rate'] ?? 0)));

        $service = [
            'courier' => $courier,
            'method_id' => $method['id'] ?? null,
            'method_name' => $method['name'] ?? null,
            'service' => $method['name'] ?? null,
            'description' => $method['description'] ?? null,
            'cost' => (int) $price,
            'etd' => $method['estimation'] ?? null,
        ];

        return [
            'courier' => $courier,
            'method_id' => $method['id'] ?? null,
            'method_name' => $method['name'] ?? null,
            'services' => collect([$service])->values(),
        ];
    }

    protected function defaultShippingMethods(): array
    {
        return [
            [
                'slug' => 'jne',
                'name' => 'JNE Reguler',
                'description' => 'Tarif tetap JNE Reguler.',
                'type' => 'courier',
                'flat_rate' => 25000,
                'estimation' => '2-4 hari',
            ],
            [
                'slug' => 'pos',
                'name' => 'POS Indonesia',
                'description' => 'Layanan POS Indonesia Reguler.',
                'type' => 'courier',
                'flat_rate' => 22000,
                'estimation' => '3-5 hari',
            ],
            [
                'slug' => 'tiki',
                'name' => 'TIKI Reguler',
                'description' => 'Tarif standar TIKI Reguler.',
                'type' => 'courier',
                'flat_rate' => 23000,
                'estimation' => '2-4 hari',
            ],
            [
                'slug' => 'pickup',
                'name' => 'Ambil di Toko',
                'description' => 'Ambil langsung pesanan di toko.',
                'type' => 'pickup',
                'flat_rate' => 0,
                'estimation' => 'Siap dalam 1 hari',
            ],
        ];
    }
}
