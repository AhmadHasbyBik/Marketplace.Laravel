<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = Purchase::with('supplier')->latest()->paginate(15);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $statusCounts = Purchase::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        $recentPurchases = Purchase::with('supplier')->latest()->limit(4)->get();

        return view('admin.purchases.index', compact('purchases', 'suppliers', 'products', 'statusCounts', 'recentPurchases'));
    }

    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    public function store(PurchaseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $items = collect($data['items'])->filter(fn ($item) => ! empty($item['product_id']));

        if ($items->isEmpty()) {
            return back()->with('warning', 'Tambahkan minimal satu item');
        }

        $total = $items->sum(fn ($item) => $item['quantity'] * $item['unit_cost']);

        $purchase = Purchase::create([
            'purchase_number' => 'PO-' . now()->format('YmdHis'),
            'supplier_id' => $data['supplier_id'],
            'purchase_date' => $data['purchase_date'],
            'total' => $total,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $itemTotal = $item['quantity'] * $item['unit_cost'];

            $purchase->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'total' => $itemTotal,
            ]);

            if ($product) {
                $product->increment('stock', $item['quantity']);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'notes' => "PO {$purchase->purchase_number}",
                ]);
            }
        }

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase order tersimpan');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'items.product']);

        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'status' => ['required', 'in:draft,ordered,received'],
            'notes' => ['nullable', 'string'],
        ]);
        $purchase->update([
            'supplier_id' => $data['supplier_id'],
            'purchase_date' => $data['purchase_date'],
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('admin.purchases.index')->with('success', 'PO diperbarui');
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        $purchase->delete();

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase dihapus');
    }
}
