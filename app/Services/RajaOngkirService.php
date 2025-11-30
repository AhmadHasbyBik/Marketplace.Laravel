<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RajaOngkirService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1'), '/');
        $this->apiKey = config('services.rajaongkir.key');
        $this->timeout = (int) config('services.rajaongkir.timeout', 10);
    }

    public function provinces(): array
    {
        $response = $this->request('destination/province');

        return $this->normalizeRegions(
            $response,
            ['province_id', 'ProvinceId', 'id'],
            ['province', 'Province', 'name']
        );
    }

    public function cities(?string $provinceId = null): array
    {
        $endpoint = 'destination/city';
        $query = [];
        if ($provinceId) {
            $endpoint = "destination/city/{$provinceId}";
        }

        $response = $this->request($endpoint, 'GET', $query);

        return $this->normalizeRegions(
            $response,
            ['city_id', 'CityId', 'id'],
            ['city', 'City', 'name'],
            [
                'province_id' => ['province_id', 'ProvinceId'],
                'province' => ['province', 'Province'],
                'type' => ['type', 'Type'],
            ]
        );
    }

    public function districts(string $cityId): array
    {
        $endpoint = "destination/district/{$cityId}";
        $response = $this->request($endpoint);

        return $this->normalizeRegions(
            $response,
            ['district_id', 'DistrictId', 'id'],
            ['district', 'District', 'name'],
            [
                'city_id' => ['city_id', 'CityId'],
                'city' => ['city', 'City'],
                'type' => ['type', 'Type'],
            ]
        );
    }

    public function cost(string $originId, string $destinationId, int $weight, string $courier, string|int|float|null $price = null): array
    {
        $payload = $this->buildCostPayload($originId, $destinationId, $weight, $courier, $price);

        $response = $this->request('calculate/district/domestic-cost', 'POST', [], $payload);

        return $this->groupCostServices($response->json('data', []));
    }

    protected function request(string $endpoint, string $method = 'GET', array $query = [], array $body = []): Response
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Rajaongkir API key belum diatur di .env (RAJAONGKIR_API_KEY)');
        }

        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $headers = [
            'Key' => $this->apiKey,
            'key' => $this->apiKey,
            'Accept' => 'application/json',
        ];

        $request = Http::withHeaders($headers)->timeout($this->timeout);

        $response = match (strtoupper($method)) {
            'GET' => $request->get($url, $query),
            default => $request->asForm()->post($url, $body),
        };

        if ($response->failed()) {
            Log::warning('RajaOngkir API call failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            $response->throw();
        }

        return $response;
    }

    protected function buildCostPayload(string $originId, string $destinationId, int $weight, string $courier, string|int|float|null $price = null): array
    {
        return [
            'origin' => (string) $originId,
            'destination' => (string) $destinationId,
            'weight' => max(1, $weight),
            'courier' => $courier,
            'price' => $this->resolvePriceValue($price),
        ];
    }

    protected function groupCostServices(array $data): array
    {
        $groups = [];

        foreach ($data as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $code = (string) strtolower(Arr::get($entry, 'code', ''));
            if ($code === '') {
                continue;
            }

            if (!isset($groups[$code])) {
                $groups[$code] = [
                    'name' => Arr::get($entry, 'name', $code),
                    'code' => $code,
                    'costs' => [],
                ];
            }

            $costValue = Arr::get($entry, 'cost', Arr::get($entry, 'price', 0));
            $normalizedCost = $this->normalizeCostValue($costValue);
            if ($normalizedCost <= 0) {
                continue;
            }

            $groups[$code]['costs'][] = [
                'service' => Arr::get($entry, 'service', Arr::get($entry, 'name')),
                'description' => Arr::get($entry, 'description'),
                'cost' => [
                    'value' => $normalizedCost,
                    'etd' => Arr::get($entry, 'etd'),
                ],
                'etd' => Arr::get($entry, 'etd'),
            ];
        }

        return array_values($groups);
    }

    protected function normalizeCostValue(mixed $value): int
    {
        if (!is_numeric($value)) {
            return 0;
        }

        return (int) round((float) $value);
    }

    protected function normalizeRegions(Response $response, array $idKeys, array $nameKeys, array $extraFields = []): array
    {
        $items = $this->extractList($response);

        return array_values(array_filter(array_map(function ($item) use ($idKeys, $nameKeys, $extraFields) {
            if (!is_array($item)) {
                return null;
            }

            $id = $this->valueFromKeys($item, $idKeys);
            $name = $this->valueFromKeys($item, $nameKeys);

            if ($id === null || $name === null) {
                return null;
            }

            $normalized = [
                'id' => (string) $id,
                'name' => $name,
            ];

            foreach ($extraFields as $field => $aliases) {
                $value = $this->valueFromKeys($item, (array) $aliases);
                if ($value !== null) {
                    $normalized[$field] = $value;
                }
            }

            return $normalized;
        }, $items)));
    }

    protected function extractList(Response $response): array
    {
        $payload = $response->json();

        if (isset($payload['data']) && is_array($payload['data'])) {
            return $payload['data'];
        }

        if (isset($payload['results']) && is_array($payload['results'])) {
            return $payload['results'];
        }

        if (isset($payload['rajaongkir']['results']) && is_array($payload['rajaongkir']['results'])) {
            return $payload['rajaongkir']['results'];
        }

        if (isset($payload['rajaongkir']['data']) && is_array($payload['rajaongkir']['data'])) {
            return $payload['rajaongkir']['data'];
        }

        return [];
    }

    protected function valueFromKeys(array $item, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $item) && $item[$key] !== null && $item[$key] !== '') {
                return (string) $item[$key];
            }
        }

        return null;
    }

    protected function resolvePriceValue(string|int|float|null $price): string|int
    {
        if ($price === null) {
            return 'lowest';
        }

        if (is_string($price) && strtolower($price) === 'lowest') {
            return 'lowest';
        }

        $numeric = (float) $price;
        if ($numeric <= 0) {
            return 'lowest';
        }

        return (int) round($numeric);
    }
}
