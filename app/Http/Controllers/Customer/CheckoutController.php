<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $quickCart = session('checkout.quick_items', []);
        $isQuickCheckout = ! empty($quickCart);
        $cart = $isQuickCheckout ? $quickCart : session('cart.items', []);
        if (empty($cart)) {
            return redirect()->route('front.cart.index')->with('warning', 'Keranjang kosong');
        }

        $productIds = collect($cart)->pluck('product_id')->filter()->unique()->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $totalWeight = $this->calculateCartWeight($cart, $products);

        $subTotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        $shippingConfig = [
            'provinces_url' => route('front.rajaongkir.provinces'),
            'cities_url' => route('front.rajaongkir.cities'),
            'districts_url' => route('front.rajaongkir.districts'),
            'costs_url' => route('front.shipping.costs'),
        ];

        return view('front.checkout.index', compact(
            'cart',
            'totalWeight',
            'subTotal',
            'shippingConfig',
            'isQuickCheckout'
        ));
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $quickCart = session('checkout.quick_items', []);
        $cart = ! empty($quickCart) ? $quickCart : session('cart.items', []);
        if (empty($cart)) {
            return redirect()->route('front.cart.index')->with('warning', 'Keranjang kosong');
        }

        $validated = $request->validated();
        $subTotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $shippingCost = (float) $validated['shipping_cost'];
        $shippingCourier = $validated['shipping_courier'];
        $shippingService = $validated['shipping_service'];
        $shippingEtd = $validated['shipping_etd'] ?? null;
        $shippingWeight = $this->calculateCartWeight($cart);
        $destinationCityId = $validated['destination_city_id'];
        $destinationCityName = $validated['destination_city_name'];
        $destinationProvince = $validated['destination_province'];
        $destinationDistrictId = $validated['destination_district_id'] ?? null;
        $destinationDistrictName = $validated['destination_district_name'] ?? null;

        $automaticShippingMethodId = $this->ensureShippingMethodFromOrder($validated);
        $shippingMethodId = $validated['shipping_method_id'] ?? $automaticShippingMethodId;

        $order = Order::create([
            'order_number' => Str::upper('DPC' . now()->format('YmdHis')),
            'user_id' => auth()->id(),
            'shipping_method_id' => $shippingMethodId,
            'shipping_courier' => $shippingCourier,
            'shipping_service' => $shippingService,
            'shipping_etd' => $shippingEtd,
            'shipping_city_id' => $destinationCityId,
            'shipping_city' => $destinationCityName,
            'shipping_province' => $destinationProvince,
            'shipping_district_id' => $destinationDistrictId,
            'shipping_district' => $destinationDistrictName,
            'shipping_weight' => $shippingWeight,
            'payment_method' => $validated['payment_method'],
            'subtotal' => $subTotal,
            'shipping_cost' => $shippingCost,
            'total' => $subTotal + $shippingCost,
            'status' => 'pending',
            'is_paid' => false,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total' => $item['quantity'] * $item['price'],
            ]);
        }

        $order->history()->create([
            'status' => 'pending',
            'notes' => 'Pesanan dibuat oleh customer',
            'changed_by' => auth()->id(),
        ]);

        if (! empty($quickCart)) {
            session()->forget('checkout.quick_items');
        } else {
            session()->forget('cart');
        }

        return redirect()->route('front.orders.index')->with('success', 'Pesanan berhasil dibuat');
    }

    public function buyNow(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        if ($product->stock < 1) {
            return redirect()->back()->with('warning', 'Stok produk tidak tersedia saat ini');
        }

        $quantity = (int) ($validated['quantity'] ?? 1);
        $quantity = max(1, min($quantity, max($product->stock, 1)));

        session()->put('checkout.quick_items', [
            $product->id => $this->buildQuickCartItem($product, $quantity),
        ]);

        return redirect()->route('front.checkout.index');
    }

    public function updateQuickItem(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $quickItems = session('checkout.quick_items', []);
        if (! isset($quickItems[$product->id])) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Produk tidak ditemukan di pembelian cepat'], 404);
            }

            return redirect()->route('front.checkout.index')->with('warning', 'Produk tidak ditemukan di pembelian cepat');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $quantity = max(1, min($validated['quantity'], max($product->stock, 1)));

        if ($validated['quantity'] > $product->stock) {
            session()->flash('warning', 'Kuantitas disesuaikan berdasarkan stok tersedia');
        }

        $quickItems[$product->id]['quantity'] = $quantity;
        session()->put('checkout.quick_items', $quickItems);

        if ($request->wantsJson()) {
            return response()->json(['quantity' => $quantity]);
        }

        return redirect()->route('front.checkout.index');
    }

    private function buildQuickCartItem(Product $product, int $quantity): array
    {
        return [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $quantity,
            'stock' => $product->stock,
            'weight' => $product->weight ?? null,
        ];
    }

    private function ensureShippingMethodFromOrder(array $validated): ?int
    {
        if (! empty($validated['shipping_method_id'])) {
            return null;
        }

        $courier = trim((string) ($validated['shipping_courier'] ?? ''));
        if ($courier === '') {
            return null;
        }

        $slug = Str::slug($courier);
        if ($slug === '') {
            return null;
        }

        $method = ShippingMethod::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $this->formatShippingMethodName($courier, $validated['shipping_service'] ?? null),
                'description' => 'Disinkronkan otomatis dari pesanan customer.',
                'type' => $slug === 'pickup' ? 'pickup' : 'courier',
                'flat_rate' => max(0, (float) ($validated['shipping_cost'] ?? 0)),
                'estimation' => $validated['shipping_etd'] ?? null,
                'is_active' => true,
            ]
        );

        return $method->id;
    }

    private function formatShippingMethodName(string $courier, ?string $service): string
    {
        $parts = [];
        if ($courier !== '') {
            $parts[] = Str::upper($courier);
        }
        if (! empty($service)) {
            $parts[] = Str::upper($service);
        }

        return $parts ? implode(' · ', $parts) : 'Kurir';
    }

    private function calculateCartWeight(array $cart, ?Collection $products = null): int
    {
        $defaultWeight = (int) config('services.rajaongkir.default_weight', 1000);

        if ($products === null) {
            $productIds = collect($cart)->pluck('product_id')->filter()->unique()->toArray();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        }

        $totalWeight = 0;
        foreach ($cart as $item) {
            $product = $products->get($item['product_id']);
            $perItemWeight = $product ? max((int) $product->weight, 0) : 0;
            $perItemWeight = $perItemWeight > 0 ? $perItemWeight : $defaultWeight;
            $totalWeight += $perItemWeight * max(1, (int) ($item['quantity'] ?? 1));
        }

        return $totalWeight;
    }
}
