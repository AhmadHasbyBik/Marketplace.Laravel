<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = Product::withCount('stockMovements')->orderBy('name')->paginate(20);

        return view('admin.inventory.index', compact('products'));
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
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $product = Product::find($data['product_id']);

        if (! $product) {
            return back()->with('warning', 'Produk tidak ditemukan');
        }

        $quantity = $data['quantity'];

        if ($data['type'] === 'in') {
            $product->increment('stock', $quantity);
        } elseif ($data['type'] === 'out') {
            $product->decrement('stock', $quantity);
        }

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => $data['type'],
            'quantity' => $quantity,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Stok disesuaikan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
