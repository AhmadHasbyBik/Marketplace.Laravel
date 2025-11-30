@extends('layouts.front')

@section('title', 'Katalog Produk - UMKM Dapoer Cupid')

@section('content')
    <div class="grid gap-6 lg:grid-cols-4">
        <aside class="space-y-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Kategori & Filter</h3>
                <span class="text-xs text-slate-500">{{ $categories->count() }} pilihan</span>
            </div>
            <div class="space-y-2">
                @foreach($categories as $category)
                    <a href="{{ route('front.products.index', array_merge(request()->query(), ['category' => $category->id])) }}"
                        class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm {{ request('category') == $category->id ? 'bg-rose-500 text-white' : 'bg-white text-slate-700 shadow-sm' }}">
                        <span>{{ $category->name }}</span>
                        <span class="text-xs uppercase tracking-[0.3em] text-slate-400"></span>
                    </a>
                @endforeach
            </div>
            <form method="GET" class="space-y-3">
                <label class="text-xs uppercase tracking-[0.3em] text-slate-400">Cari</label>
                <input name="search" placeholder="Nama produk atau deskripsi" value="{{ request('search') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm focus:border-rose-500">
                <button type="submit" class="w-full rounded-2xl bg-rose-500 px-4 py-2 text-sm font-semibold text-white">Terapkan filter</button>
            </form>
        </aside>
        <div class="lg:col-span-3 space-y-6">
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($products as $product)
                    <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-lg flex flex-col gap-3 hover:-translate-y-1 transition">
                        <div class="flex items-center justify-between text-slate-500">
                            <span class="text-xs uppercase tracking-[0.4em]">SKU</span>
                            <span class="text-xs">{{ $product->stock }} stok</span>
                        </div>
                        <div class="h-36 overflow-hidden rounded-3xl bg-slate-100">
                            @if($image = $product->images->first())
                                <img src="{{ $image->url }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                            @else
                                <div class="h-full w-full flex items-center justify-center text-sm font-semibold text-slate-400">
                                    {{ $product->sku ?? 'Produk' }}
                                </div>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <h2 class="text-xl font-semibold">{{ $product->name }}</h2>
                            <p class="text-sm text-slate-500 line-clamp-2">{{ $product->short_description }}</p>
                        </div>
                        <div class="flex items-center justify-between mt-auto text-sm">
                            <span class="text-slate-500">Kategori: {{ $product->category?->name ?? 'Umum' }}</span>
                            <span class="text-rose-600 font-semibold">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('front.products.show', $product) }}" class="flex items-center gap-2 text-sm font-semibold text-rose-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3-3 3M6 12h12"/>
                            </svg>
                            Lihat detail
                        </a>
                    </article>
                @endforeach
            </div>

            {{ $products->withQueryString()->links() }}
        </div>
    </div>
@endsection
