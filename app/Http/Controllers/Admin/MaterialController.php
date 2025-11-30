<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialMovement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function index(): View
    {
        $materials = Material::withCount('movements')
            ->with(['movements' => fn ($query) => $query->latest()->take(3)])
            ->orderBy('name')
            ->paginate(20);

        $materialsList = Material::orderBy('name')->get();
        $products = Product::with('materials')->orderBy('name')->get();

        return view('admin.materials.index', compact('materials', 'materialsList', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:materials,sku'],
            'unit' => ['required', 'string', 'max:64'],
            'stock' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        Material::create([                
            'name' => $data['name'],
            'sku' => $data['sku'] ?? null,
            'unit' => $data['unit'],
            'stock' => $data['stock'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('admin.materials.index')->with('success', 'Bahan baku baru ditambahkan');
    }

    public function update(Request $request, Material $material): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:materials,sku,'.$material->id],
            'unit' => ['required', 'string', 'max:64'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $material->update([
            'name' => $data['name'],
            'sku' => $data['sku'] ?? null,
            'unit' => $data['unit'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('admin.materials.index')->with('success', 'Bahan baku diperbarui');
    }

    public function destroy(Material $material): RedirectResponse
    {
        $material->delete();

        return redirect()->route('admin.materials.index')->with('success', 'Bahan baku dihapus');
    }

    public function movement(Request $request, Material $material): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $currentStock = (float) $material->stock;
        $quantity = (float) $data['quantity'];
        $newStock = $currentStock;
        $change = 0;

        if ($data['type'] === 'in') {
            $newStock = $currentStock + $quantity;
            $change = $quantity;
        } elseif ($data['type'] === 'out') {
            $decrement = min($quantity, $currentStock);
            $newStock = $currentStock - $decrement;
            $change = -$decrement;
        } else {
            $newStock = $quantity;
            $change = $newStock - $currentStock;
        }

        $material->update(['stock' => $newStock]);

        if ($change !== 0) {
            MaterialMovement::create([
                'material_id' => $material->id,
                'user_id' => auth()->id(),
                'type' => $change > 0 ? 'in' : 'out',
                'quantity' => abs($change),
                'notes' => $data['notes'] ?? null,
            ]);
        }

        return redirect()->route('admin.materials.index')->with('success', 'Stok bahan baku diperbarui');
    }

    public function saveRecipe(Request $request): RedirectResponse
    {
        $materials = collect($request->input('materials', []))
            ->filter(function ($item) {
                return isset($item['material_id']) && trim((string) ($item['material_id'] ?? '')) !== '' &&
                    isset($item['quantity']) && trim((string) ($item['quantity'] ?? '')) !== '';
            })
            ->map(function ($item) {
                return [
                    'material_id' => $item['material_id'],
                    'quantity' => (float) $item['quantity'],
                ];
            })
            ->values()
            ->all();

        $request->merge(['materials' => $materials]);

        $data = Validator::make([
            'product_id' => $request->input('product_id'),
            'materials' => $materials,
        ], [
            'product_id' => ['required', 'exists:products,id'],
            'materials' => ['required', 'array', 'min:1'],
            'materials.*.material_id' => ['required', 'exists:materials,id'],
            'materials.*.quantity' => ['required', 'numeric', 'min:0.0001'],
        ], [
            'materials.required' => 'Tambahkan minimal satu bahan terlebih dahulu',
            'materials.min' => 'Tambahkan minimal satu bahan terlebih dahulu',
        ])->validate();

        $syncData = collect($data['materials'] ?? [])->mapWithKeys(function ($item) {
            return [$item['material_id'] => ['quantity' => $item['quantity']]];
        })->all();

        $product = Product::find($data['product_id']);
        $product->materials()->sync($syncData);

        return redirect()->route('admin.materials.index')->with('success', 'Rasio bahan baku tersimpan');
    }
}
