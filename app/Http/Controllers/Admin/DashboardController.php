<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Review;
use App\Models\ShippingMethod;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $ordersCount = Order::count();
        $productsCount = Product::count();
        $lowStock = Product::whereColumn('stock', '<=', 'stock_minimum')->count();
        $categoriesCount = Category::count();
        $suppliersCount = Supplier::count();
        $bannersCount = Banner::count();
        $purchasesCount = Purchase::count();
        $reviewsCount = Review::count();
        $shippingCount = ShippingMethod::count();
        $recentOrders = Order::with('user')->latest('created_at')->limit(4)->get();

        return view('admin.dashboard', compact(
            'ordersCount',
            'productsCount',
            'lowStock',
            'categoriesCount',
            'suppliersCount',
            'bannersCount',
            'purchasesCount',
            'reviewsCount',
            'shippingCount',
            'recentOrders'
        ));
    }
}
