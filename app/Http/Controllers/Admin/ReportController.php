<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Purchase;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(): View
    {
        $sales = Order::whereBetween('created_at', [now()->subMonth(), now()])->sum('total');
        $purchases = Purchase::whereBetween('created_at', [now()->subMonth(), now()])->count();
        $ordersByStatus = Order::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        $recentOrders = Order::with(['user', 'shippingMethod'])->latest()->limit(5)->get();
        $topProducts = OrderItem::with('product.category')
            ->select('product_id', DB::raw('sum(quantity) as sold'))
            ->groupBy('product_id')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact('sales', 'purchases', 'ordersByStatus', 'recentOrders', 'topProducts'));
    }
}
