<?php

namespace App\Providers;

use App\Http\Middleware\RoleMiddleware;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::aliasMiddleware('role', RoleMiddleware::class);
        View::composer('layouts.admin', function ($view) {
            $notifications = collect();

            $recentOrders = Order::with('user')->latest('created_at')->limit(4)->get();
            foreach ($recentOrders as $order) {
                $notifications->push([
                    'id' => "order-{$order->id}",
                    'type' => 'order',
                    'title' => "Pesanan #{$order->order_number}",
                    'description' => $order->user?->name ? "oleh {$order->user->name}" : 'Pelanggan tamu',
                    'meta' => ucfirst(str_replace('_', ' ', $order->status)),
                    'time' => $order->created_at->diffForHumans(),
                    'url' => url("/admin/orders/{$order->id}"),
                ]);
            }

            $lowStockProducts = Product::whereColumn('stock', '<=', 'stock_minimum')
                ->orderBy('stock', 'asc')
                ->limit(3)
                ->get();

            foreach ($lowStockProducts as $product) {
                $notifications->push([
                    'id' => "inventory-{$product->id}",
                    'type' => 'inventory',
                    'title' => "{$product->name} hampir habis",
                    'description' => "Stok {$product->stock}, minimum {$product->stock_minimum}",
                    'meta' => 'Segera restock',
                    'time' => now()->diffForHumans(),
                    'url' => url('/admin/products'),
                ]);
            }

            $notifications = $notifications->values();
            $seenNotifications = collect(session('admin_seen_notifications', []))->map(fn ($value) => (string) $value)->values()->all();
            $unreadNotifications = $notifications->reject(fn ($notification) => in_array($notification['id'], $seenNotifications, true));
            $unreadNotificationIds = $unreadNotifications->pluck('id')->values()->all();
            $view->with('dashboardNotifications', $notifications);
            $view->with('unreadNotificationsCount', $unreadNotifications->count());
            $view->with('unreadNotificationIds', $unreadNotificationIds);
        });
    }
}
