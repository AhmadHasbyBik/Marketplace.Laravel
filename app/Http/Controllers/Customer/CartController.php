<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
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

    protected function buildCartItem(Product $product, int $quantity): array
    {
        return [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $quantity,
            'stock' => $product->stock,
        ];
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

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock < 1) {
            return redirect()->back()->with('warning', 'Stok produk tidak tersedia saat ini');
        }

        $items = $this->getCartItems();
        $existingQuantity = $items[$product->id]['quantity'] ?? 0;

        if ($existingQuantity >= $product->stock) {
            return redirect()->route('front.cart.index')->with('warning', 'Stok produk telah habis di keranjang');
        }

        $desiredQuantity = $existingQuantity + $validated['quantity'];
        $finalQuantity = min($desiredQuantity, $product->stock);
        $items[$product->id] = $this->buildCartItem($product, $finalQuantity);
        $this->saveCartItems($items);

        $message = 'Produk ditambahkan ke keranjang';
        $messageType = 'success';

        if ($finalQuantity > $existingQuantity && $finalQuantity < $desiredQuantity) {
            $message = 'Stok terbatas: jumlah disesuaikan dengan ketersediaan';
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

        if ($validated['quantity'] > $product->stock) {
            $message = 'Tidak dapat memesan lebih dari stok tersedia';
            $previousQuantity = $items[$product->id]['quantity'];

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'available_stock' => $product->stock,
                    'quantity' => $previousQuantity,
                ], 422);
            }

            return redirect()->back()->with('warning', $message);
        }

        $items[$product->id] = $this->buildCartItem($product, $validated['quantity']);
        $this->saveCartItems($items);
        $subtotal = $this->calculateSubtotal(['items' => $items]);

        if ($request->wantsJson()) {
            return response()->json([
                'quantity' => $items[$product->id]['quantity'],
                'subtotal' => $subtotal,
                'available_stock' => $product->stock,
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
