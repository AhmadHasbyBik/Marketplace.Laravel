<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\View\View;

class FrontController extends Controller
{
    public function index(): View
    {
        $banners = Banner::where('is_active', true)->orderBy('order')->limit(4)->get();
        $categories = Category::active()->orderBy('order')->limit(6)->get();
        $featuredProducts = Product::with('images')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->limit(8)
            ->get();
        $reviews = Review::where('is_approved', true)
            ->latest()
            ->limit(6)
            ->get();

        return view('front.home', compact('banners', 'categories', 'featuredProducts', 'reviews'));
    }
}
