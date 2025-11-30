<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = Product::with(['category', 'images'])->latest()->paginate(15);
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $payload = $request->safe()->except('images');
        $product = Product::create($payload);

        $this->attachImages($product, $request->file('images'));

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dibuat');
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $payload = $request->safe()->except('images');
        $product->update($payload);

        $this->attachImages($product, $request->file('images'));

        return redirect()->route('admin.products.index')->with('success', 'Produk diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk dihapus');
    }

    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update(['is_featured' => ! $product->is_featured]);

        return redirect()->route('admin.products.index')->with('success', 'Status unggulan produk diperbarui');
    }

    private function attachImages(Product $product, ?array $files): void
    {
        if (empty($files)) {
            return;
        }

        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $order = $product->images()->max('order') ?? -1;

        foreach ($files as $file) {
            if (! $file?->isValid()) {
                continue;
            }

            $path = $file->store('products', 'public');

            $media = Media::create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => $file->getClientMimeType(),
                'disk' => 'public',
                'meta' => [
                    'size' => $file->getSize(),
                    'extension' => $file->extension(),
                ],
                'folder' => 'products',
                'user_id' => auth()->id(),
            ]);

            $order++;

            $product->images()->create([
                'media_id' => (string) $media->id,
                'alt_text' => $product->name,
                'order' => $order,
                'is_primary' => ! $hasPrimary,
            ]);

            $hasPrimary = true;
        }
    }
}
