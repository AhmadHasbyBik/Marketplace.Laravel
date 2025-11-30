<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusRequest;
use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $orders = Order::with(['user', 'address', 'shippingMethod'])
            ->when($status, fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        $order->load(['items.product', 'history.changedBy']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order): View
    {
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderStatusRequest $request, Order $order): RedirectResponse
    {
        $data = $request->validated();
        $order->fill([
            'status' => $data['status'],
            'is_paid' => $data['status'] !== 'pending',
        ])->save();

        $order->history()->create([
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'changed_by' => auth()->id(),
        ]);

        $order->loadMissing('items.product');

        if ($data['status'] === 'completed') {
            $order->items->each(function ($item) use ($order) {
                $product = $item->product;
                if ($product) {
                    $product->decrement('stock', $item->quantity);
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'notes' => "Order #{$order->order_number} selesai",
                    ]);
                }
            });
        }

        return redirect()->route('admin.orders.index')->with('success', 'Status pesanan diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan dihapus');
    }
}
