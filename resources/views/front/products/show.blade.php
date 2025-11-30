@extends('layouts.front')

@section('title', $product->name . ' - UMKM Dapoer Cupid')

@section('content')
    <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                @if($product->images->isNotEmpty())
                    <div class="overflow-hidden rounded-3xl bg-slate-100">
                        <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}" class="h-72 w-full object-cover" />
                    </div>
                    <div class="mt-4 grid grid-cols-4 gap-2">
                        @foreach($product->images as $image)
                            <div class="h-16 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                <img src="{{ $image->url }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-72 rounded-3xl bg-slate-100 flex items-center justify-center text-slate-400 font-semibold">
                        Foto produk
                    </div>
                @endif
                <h1 class="text-3xl font-semibold mt-5">{{ $product->name }}</h1>
                <p class="text-sm text-slate-500 mt-2">{{ $product->short_description }}</p>
                <div class="mt-4">
                    <strong class="text-sm text-slate-500">Kategori:</strong>
                    <span class="text-sm text-rose-600">{{ $product->category?->name }}</span>
                </div>
                <p class="mt-5 text-slate-600 leading-relaxed">{{ $product->description }}</p>
            </div>
        </div>
        <aside class="space-y-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-2xl font-semibold text-rose-600">Rp{{ number_format($product->price, 0, ',', '.') }}</div>
                <p class="text-sm text-slate-500">Stok tersedia: {{ $product->stock }}</p>
                <form action="{{ route('front.cart.store') }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="text-sm font-semibold text-slate-600">
                        Kuantitas <span class="font-normal text-slate-500">1</span>
                    </div>
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full rounded-2xl bg-rose-500 px-3 py-2 text-white font-semibold">Tambah ke Keranjang</button>
                </form>
                <form action="{{ route('front.checkout.buyNow') }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full rounded-2xl border border-rose-200 bg-white px-3 py-2 text-rose-600 font-semibold shadow-sm transition hover:bg-rose-50">Beli Sekarang</button>
                </form>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold">Review & Rating</h3>
                @foreach($product->reviews->take(3) as $review)
                    <div class="border-t border-slate-100 py-3 text-sm text-slate-500">
                        <div class="flex items-center justify-between">
                            <span>{{ $review->user?->name ?? 'Pelanggan' }}</span>
                            <span class="text-yellow-400">{{ str_repeat('★', $review->rating) }}</span>
                        </div>
                        <p>{{ $review->comment }}</p>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>
@endsection
