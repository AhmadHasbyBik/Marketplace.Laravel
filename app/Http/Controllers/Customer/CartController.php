<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Concerns\HandlesBackorders;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductionPlanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    use HandlesBackorders;

    public function __construct(ProductionPlanner $productionPlanner)
    {
        $this->productionPlanner = $productionPlanner;
    }

    protected function getCartItems(): array
    {
        return session('cart.items', []);
    }

    protected function getCart(): array
    {
        return [
            'items' => $this->getCartItems(),
        ];
    }

    protected function saveCartItems(array $items): void
    {
        $cart = session('cart', []);
        $cart['items'] = $items;
        session(['cart' => $cart]);
    }

    protected function buildCartItem(Product $product, array $analysis): array
    {
        return $this->formatCartPayload($product, $analysis);
    }

    public function index(): View
    {
        $cart = $this->getCart();
        $subtotal = $this->calculateSubtotal($cart);

        return view('front.cart.index', compact('cart', 'subtotal'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::with('materials')->findOrFail($validated['product_id']);
        $items = $this->getCartItems();
        $existingQuantity = $items[$product->id]['quantity'] ?? 0;
        $desiredQuantity = $existingQuantity + $validated['quantity'];
        $analysis = $this->describeBackorder($product, $desiredQuantity);

        if (! $analysis['production_ready']) {
            return redirect()->back()->with('warning', 'Stok dan bahan baku tidak mencukupi saat ini');
        }

        $items[$product->id] = $this->buildCartItem($product, $analysis);
        $this->saveCartItems($items);

        $message = 'Produk ditambahkan ke keranjang';
        $messageType = 'success';

        if ($analysis['backorder'] > 0) {
            $message = 'Stok terbatas: sisanya akan diproduksi dari bahan baku';
            $messageType = 'warning';
        }

        return redirect()->route('front.cart.index')->with($messageType, $message);
    }

    public function update(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $items = $this->getCartItems();

        if (! isset($items[$product->id])) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Produk tidak ditemukan di keranjang'], 404);
            }

            return redirect()->back()->with('warning', 'Produk tidak ditemukan di keranjang');
        }

        $analysis = $this->describeBackorder($product->loadMissing('materials'), $validated['quantity']);
        if (! $analysis['production_ready']) {
            $message = 'Stok dan bahan baku tidak mencukupi untuk kuantitas tersebut';

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'available_stock' => $product->stock,
                    'backorder' => $analysis['backorder'],
                ], 422);
            }

            return redirect()->back()->with('warning', $message);
        }

        $items[$product->id] = $this->buildCartItem($product, $analysis);
        $this->saveCartItems($items);
        $subtotal = $this->calculateSubtotal(['items' => $items]);

        if ($request->wantsJson()) {
            return response()->json([
                'quantity' => $items[$product->id]['quantity'],
                'subtotal' => $subtotal,
                'available_stock' => $product->stock,
                'backorder' => $analysis['backorder'],
                'production_ready' => $analysis['production_ready'],
            ]);
        }

        return redirect()->route('front.cart.index')->with('success', 'Keranjang diperbarui');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $items = $this->getCartItems();
        unset($items[$product->id]);
        $this->saveCartItems($items);

        return redirect()->route('front.cart.index')->with('success', 'Produk dihapus dari keranjang');
    }

    public function clear(): RedirectResponse
    {
        $this->saveCartItems([]);

        return redirect()->route('front.cart.index')->with('success', 'Keranjang dikosongkan');
    }

    protected function calculateSubtotal(array $cart): float
    {
        return collect($cart['items'] ?? [])->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        });
    }
}
