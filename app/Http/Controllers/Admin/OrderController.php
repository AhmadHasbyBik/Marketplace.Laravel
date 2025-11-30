<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesBackorders;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusRequest;
use App\Models\Order;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\ProductionPlanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    use HandlesBackorders;

    public function __construct(ProductionPlanner $productionPlanner)
    {
        $this->productionPlanner = $productionPlanner;
    }
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
            'changed_by' => Auth::id(),
        ]);

        $order->loadMissing('items.product');

        if ($data['status'] === 'waiting_materials') {
            $order->needs_materials = true;
            $order->save();
        }

        if ($data['status'] === 'completed') {
            $insufficient = collect();
            foreach ($order->items as $item) {
            /** @var Product|null $product */
            $product = $item->product;
                $backorderQty = (int) ($item->meta['backorder_quantity'] ?? 0);
                if ($backorderQty <= 0 || ! $product) {
                    continue;
                }

                if (! $this->productionPlanner->canProduce($product, $backorderQty)) {
                    $insufficient->push($product->name ?? 'Produk');
                }
            }

            if ($insufficient->isNotEmpty()) {
                $order->needs_materials = true;
                $order->save();
                return redirect()->back()->with('warning', 'Bahan baku tidak mencukupi untuk: ' . $insufficient->unique()->implode(', '));
            }

            foreach ($order->items as $item) {
                $product = $item->product;
                if (! $product) {
                    continue;
                }

                $reservedStock = (int) ($item->meta['reserved_stock'] ?? min($item->quantity, $product->stock));
                if ($reservedStock > 0) {
                    $decrement = min($reservedStock, max((int) $product->stock, 0));
                    if ($decrement > 0) {
                        $product->decrement('stock', $decrement);
                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => Auth::id(),
                            'type' => 'out',
                            'quantity' => $decrement,
                            'notes' => "Order #{$order->order_number} selesai",
                        ]);
                    }
                }

                $backorderQty = (int) ($item->meta['backorder_quantity'] ?? 0);
                if ($backorderQty > 0) {
                    $requirements = $this->productionPlanner->requiredMaterials($product, $backorderQty);
                    foreach ($requirements as $requirement) {
                        /** @var Material $material */
                        $material = $requirement['material'];
                        $needed = $requirement['needed'];
                        if ($needed <= 0 || $material->stock <= 0) {
                            continue;
                        }

                        $consume = min($needed, max((float) $material->stock, 0.0));
                        if ($consume <= 0) {
                            continue;
                        }

                        $material->decrement('stock', $consume);
                        MaterialMovement::create([
                            'material_id' => $material->id,
                            'user_id' => Auth::id(),
                            'type' => 'out',
                            'quantity' => $consume,
                            'notes' => "Produksi order #{$order->order_number}",
                        ]);
                    }
                }
            }

            $order->needs_materials = false;
            $order->save();
        }

        if (in_array($data['status'], ['cancelled'])) {
            $order->needs_materials = false;
            $order->save();
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
