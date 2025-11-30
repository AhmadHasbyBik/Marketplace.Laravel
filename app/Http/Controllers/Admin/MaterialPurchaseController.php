<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialPurchaseRequest;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Models\MaterialPurchase;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class MaterialPurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = MaterialPurchase::with(['supplier', 'items.material', 'transactions'])
            ->withSum('transactions', 'amount')
            ->latest()
            ->paginate(15);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $materials = Material::where('is_active', true)->orderBy('name')->get();
        $statusCounts = MaterialPurchase::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        $recentPurchases = MaterialPurchase::with('supplier')->latest()->limit(4)->get();

        return view('admin.material-purchases.index', compact('purchases', 'suppliers', 'materials', 'statusCounts', 'recentPurchases'));
    }

    public function store(MaterialPurchaseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $items = collect($data['items']);

        $total = $items->sum(fn ($item) => $item['quantity'] * $item['unit_cost']);

        $purchase = MaterialPurchase::create([
            'purchase_number' => 'MP-' . now()->format('YmdHis'),
            'supplier_id' => $data['supplier_id'],
            'purchase_date' => $data['purchase_date'],
            'total' => $total,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($items as $item) {
            $material = Material::find($item['material_id']);
            $itemTotal = $item['quantity'] * $item['unit_cost'];

            $purchase->items()->create([
                'material_id' => $item['material_id'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'total' => $itemTotal,
            ]);

            if ($material) {
                $material->increment('stock', $item['quantity']);

                MaterialMovement::create([
                    'material_id' => $material->id,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'notes' => "MP {$purchase->purchase_number}",
                ]);
            }
        }

        return redirect()->route('admin.material-purchases.index')->with('success', 'PO bahan baku tersimpan');
    }

    public function update(Request $request, MaterialPurchase $materialPurchase): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'status' => ['required', 'in:draft,ordered,received'],
            'notes' => ['nullable', 'string'],
        ]);

        $materialPurchase->update([
            'supplier_id' => $data['supplier_id'],
            'purchase_date' => $data['purchase_date'],
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('admin.material-purchases.index')->with('success', 'PO bahan diperbarui');
    }

    public function destroy(MaterialPurchase $materialPurchase): RedirectResponse
    {
        $materialPurchase->delete();

        return redirect()->route('admin.material-purchases.index')->with('success', 'PO bahan baku dihapus');
    }

    public function pdf(MaterialPurchase $materialPurchase): Response
    {
        $materialPurchase->load(['supplier', 'items.material', 'transactions']);
        $pdf = Pdf::loadView('admin.material-purchases.pdf', compact('materialPurchase'));

        return $pdf->download("PO-{$materialPurchase->purchase_number}.pdf");
    }
}
