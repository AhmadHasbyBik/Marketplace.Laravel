<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $hasOrdered = Order::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->whereHas('items', fn($q) => $q->where('product_id', $data['product_id']))
            ->exists();

        if (! $hasOrdered) {
            return redirect()->back()->with('warning', 'Hanya bisa memberikan ulasan setelah pembelian selesai');
        }

        $reviewExists = Review::where('user_id', auth()->id())
            ->where('product_id', $data['product_id'])
            ->where('order_reference', $data['order_reference'])
            ->exists();

        if ($reviewExists) {
            return redirect()->back()->with('warning', 'Produk ini sudah diulas pada pesanan tersebut');
        }

        Review::create(array_merge($data, [
            'user_id' => auth()->id(),
            'is_approved' => false,
        ]));

        return redirect()->back()->with('success', 'Ulasan dikirim, tunggu persetujuan admin');
    }
}
