<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KasirOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $orders = Order::with(['user', 'items.product'])
            ->whereIn('status', ['pending', 'paid', 'processing', 'shipped'])
            ->latest()
            ->paginate(15);

        return view('kasir.orders.index', compact('orders'));
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
        return view('kasir.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderStatusRequest $request, Order $order): RedirectResponse
    {
        $order->update([
            'status' => $request->validated()['status'],
        ]);

        $order->history()->create([
            'status' => $request->validated()['status'],
            'notes' => $request->validated()['notes'] ?? null,
            'changed_by' => auth()->id(),
        ]);

        return redirect()->route('kasir.orders.show', $order)->with('success', 'Status pesanan diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
