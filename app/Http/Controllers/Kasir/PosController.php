<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Requests\PosTransactionRequest;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\PosTransaction;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->input('q');
        $categoryId = $request->input('category_id');

        $products = Product::with('category')->where('is_active', true)
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(30)
            ->get();

        $categories = Category::active()
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('kasir.pos.index', compact('products', 'categories', 'categoryId'));
    }

    public function store(PosTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $transaction = PosTransaction::create([
            'transaction_number' => 'POS-' . now()->format('YmdHis'),
            'user_id' => auth()->id(),
            'subtotal' => $data['subtotal'],
            'discount' => $data['discount'] ?? 0,
            'tax' => $data['tax'] ?? 0,
            'total' => $data['total'],
            'payment_method' => $data['payment_method'],
            'status' => 'completed',
        ]);

        $order = Order::create([
            'order_number' => $transaction->transaction_number,
            'user_id' => auth()->id(),
            'payment_method' => $data['payment_method'],
            'subtotal' => $data['subtotal'],
            'discount' => $data['discount'] ?? 0,
            'shipping_cost' => 0,
            'total' => $data['total'],
            'status' => 'completed',
            'is_paid' => true,
            'notes' => $data['notes'] ?? null,
            'order_type' => Order::TYPE_KASIR,
        ]);

        foreach ($data['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (! $product) {
                continue;
            }

            $transaction->items()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => 0,
                'total' => $item['quantity'] * $item['unit_price'],
            ]);

            $product->decrement('stock', $item['quantity']);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'out',
                'quantity' => $item['quantity'],
                'notes' => 'POS #' . $transaction->transaction_number,
            ]);
        }

        return redirect()->route('kasir.pos.index')->with('success', 'Transaksi POS tersimpan');
    }
}
