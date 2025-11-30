<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Concerns\HandlesBackorders;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductionPlanner;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    use HandlesBackorders;

    public function __construct(ProductionPlanner $productionPlanner)
    {
        $this->productionPlanner = $productionPlanner;
    }

    public function index(Request $request): View
    {
        $products = Product::with(['category', 'reviews', 'materials'])
            ->where('is_active', true)
            ->when($request->filled('category'), fn($q) => $q->where('category_id', $request->input('category')))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%' . $request->input('search') . '%'))
            ->orderBy('name')
            ->paginate(12);

        $categories = Category::active()->orderBy('order')->get();

        $products->getCollection()->transform(function (Product $product) {
            $analysis = $this->describeBackorder($product->loadMissing('materials'), 1);
            $product->backorder_available = $analysis['backorder'] > 0 && $analysis['production_ready'];
            return $product;
        });

        return view('front.products.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        $product->load(['images', 'reviews.user', 'materials']);
        $analysis = $this->describeBackorder($product, 1);
        $product->backorder_available = $analysis['backorder'] > 0 && $analysis['production_ready'];

        return view('front.products.show', compact('product'));
    }
}
