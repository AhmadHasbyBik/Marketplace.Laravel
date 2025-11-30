<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with(['category', 'reviews'])
            ->where('is_active', true)
            ->when($request->filled('category'), fn($q) => $q->where('category_id', $request->input('category')))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%' . $request->input('search') . '%'))
            ->orderBy('name')
            ->paginate(12);

        $categories = Category::active()->orderBy('order')->get();

        return view('front.products.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        $product->load(['images', 'reviews.user']);

        return view('front.products.show', compact('product'));
    }
}
