<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\RajaOngkirLocationController;
use App\Http\Controllers\Customer\ShippingRateController;
use App\Http\Controllers\Customer\CustomerOrderController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\Kasir\KasirOrderController;
use App\Http\Controllers\Kasir\PosController;
use App\Http\Controllers\ProfileController;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('categories/{category}/image', function (Category $category) {
    if (! $category->image) {
        abort(404);
    }

    return Storage::disk('public')->response($category->image);
})->name('categories.image');

Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::get('about', function (): View {
    return view('front.about');
})->name('front.about');

Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->roles()->where('name', 'admin')->exists()) {
        return redirect()->route('admin.dashboard');
    }

    if ($user && $user->roles()->where('name', 'kasir')->exists()) {
        return redirect()->route('kasir.pos.index');
    }

    return redirect()->route('front.home');
})->name('dashboard');

Route::prefix('products')->name('front.products.')->group(function () {
    Route::get('/', [CustomerProductController::class, 'index'])->name('index');
    Route::get('/{product:slug}', [CustomerProductController::class, 'show'])->name('show');
});

Route::get('cart', [CartController::class, 'index'])->name('front.cart.index');
Route::post('cart', [CartController::class, 'store'])->name('front.cart.store');
Route::match(['put', 'patch'], 'cart/{product}', [CartController::class, 'update'])->name('front.cart.update');
Route::delete('cart/{product}', [CartController::class, 'destroy'])->name('front.cart.destroy');
Route::post('cart/clear', [CartController::class, 'clear'])->name('front.cart.clear');

Route::middleware('auth')->group(function () {
    Route::get('checkout', [CheckoutController::class, 'index'])->name('front.checkout.index');
    Route::post('checkout', [CheckoutController::class, 'store'])->name('front.checkout.store');
    Route::post('checkout/buy-now', [CheckoutController::class, 'buyNow'])->name('front.checkout.buyNow');
    Route::patch('checkout/buy-now/{product}', [CheckoutController::class, 'updateQuickItem'])->name('front.checkout.quickUpdate');
    Route::get('shipping/costs', [ShippingRateController::class, 'index'])->name('front.shipping.costs');
    Route::get('rajaongkir/provinces', [RajaOngkirLocationController::class, 'provinces'])->name('front.rajaongkir.provinces');
    Route::get('rajaongkir/cities', [RajaOngkirLocationController::class, 'cities'])->name('front.rajaongkir.cities');
    Route::get('rajaongkir/districts', [RajaOngkirLocationController::class, 'districts'])->name('front.rajaongkir.districts');
    Route::post('reviews', [CustomerReviewController::class, 'store'])->name('front.reviews.store');
    Route::get('orders/{order}/invoice', [CustomerOrderController::class, 'invoice'])->name('front.orders.invoice');
    Route::post('orders/{order}/payment-proof', [CustomerOrderController::class, 'uploadPaymentProof'])->name('front.orders.payment-proof');
    Route::resource('orders', CustomerOrderController::class)
        ->names('front.orders')
        ->only(['index', 'show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::resource('products', AdminProductController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('products/{product}/featured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggleFeatured');
    Route::resource('categories', CategoryController::class);
    Route::resource('banners', BannerController::class);
    Route::resource('shipping-methods', ShippingController::class)->parameters([
        'shipping-methods' => 'method',
    ]);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::resource('reviews', ReviewController::class)->only(['index', 'update', 'destroy']);
    Route::resource('purchases', PurchaseController::class);
    Route::resource('users', AdminUserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
});

Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::resource('orders', KasirOrderController::class)
        ->only(['index', 'show', 'update'])
        ->names([
            'index' => 'orders.index',
            'show' => 'orders.show',
            'update' => 'orders.update',
        ]);
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos', [PosController::class, 'store'])->name('pos.store');
});

require __DIR__.'/auth.php';
