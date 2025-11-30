@extends('layouts.front')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'UMKM Dapoer Cupid - Landing Page')

@section('content')
    <section class="space-y-8">
        <div class="rounded-3xl bg-gradient-to-br from-rose-500 to-orange-400 text-white p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 opacity-50 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.25),_transparent_55%)]"></div>
            <div class="relative z-10 max-w-3xl space-y-4">
                <p class="text-xs uppercase tracking-[0.6em] text-white/60">UMKM Dapoer Cupid</p>
                <h1 class="text-4xl sm:text-5xl font-bold leading-tight">Kreasi kuliner lokal modern, siap menginspirasi momen spesialmu.</h1>
                <p class="text-lg text-white/90">Pesan online atau datang ke counter kasir kami—semua ditangani oleh sistem terpadu, dari checkout hingga stok inventori.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('front.products.index') }}" class="px-6 py-3 rounded-full bg-white text-rose-500 font-semibold shadow-lg hover:bg-rose-50">Belanja Sekarang</a>
                    <a href="#product-featured" class="px-6 py-3 rounded-full border border-white/60 text-white font-semibold hover:border-white hover:text-white">Lihat Produk</a>
                </div>
                <div class="grid gap-3 sm:grid-cols-3 text-sm">
                    <div class="bg-white/10 rounded-2xl px-4 py-3 flex flex-col gap-2">
                        <span class="text-white font-semibold">24/7 Support</span>
                        <span class="text-white/70">Tim responsif via WhatsApp</span>
                    </div>
                    <div class="bg-white/10 rounded-2xl px-4 py-3 flex flex-col gap-2">
                        <span class="text-white font-semibold">POS & Online</span>
                        <span class="text-white/70">Laporan kasir dan belanja daring</span>
                    </div>
                    <div class="bg-white/10 rounded-2xl px-4 py-3 flex flex-col gap-2">
                        <span class="text-white font-semibold">Gratis Ongkir</span>
                        <span class="text-white/70">Untuk area Jombang</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($banners as $banner)
                <article class="group rounded-3xl border border-slate-200 bg-white p-5 shadow-lg transition hover:-translate-y-1">
                    <div class="flex items-center justify-between text-slate-500">
                        <span class="text-xs uppercase tracking-widest">{{ $banner->type }}</span>
                        <span class="text-sm text-rose-500">{{ $banner->order ? "URUT #{$banner->order}" : 'New' }}</span>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900 mt-3">{{ $banner->title }}</h3>
                    <p class="text-sm text-slate-500 mt-2">{{ $banner->subtitle }}</p>
                    @if($banner->cta_text)
                        <a href="{{ $banner->cta_url ?? '#' }}" class="inline-flex items-center gap-2 text-rose-500 text-sm mt-4 hover:text-rose-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M5 12h14m0 0-4-4m4 4-4 4"/>
                            </svg>
                            {{ $banner->cta_text }}
                        </a>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="mt-14 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Kategori Pilihan</h2>
                <p class="text-sm text-slate-500">Pilih mood kulinermu hari ini</p>
            </div>
            <a href="{{ route('front.products.index') }}" class="text-sm text-rose-600 underline">Lihat semua kategori</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($categories as $category)
                <a href="{{ route('front.products.index', ['category' => $category->id]) }}"
                    class="group rounded-3xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col gap-3 pt-5 transition hover:-translate-y-0.5 hover:shadow-lg hover:border-rose-400">
                    <div class="h-40 w-full overflow-hidden rounded-2xl bg-slate-100">
                        @if($category->image_url)
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-full w-full object-cover" />
                        @else
                            <div class="flex h-full w-full items-center justify-center text-xs uppercase tracking-[0.3em] text-slate-400">
                                Tanpa gambar
                            </div>
                        @endif
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold">{{ $category->name }}</h3>
                        <p class="text-sm text-slate-500">{{ $category->description }}</p>
                        <div class="flex items-center gap-2 text-sm font-semibold text-rose-500">
                            <span>Telusuri kategori</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M5 12h14m0 0-4-4m4 4-4 4"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mt-14 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold">Produk unggulan</h2>
                <p class="text-sm text-slate-500">Katalog yang paling banyak dilirik minggu ini</p>
            </div>
            <a href="{{ route('front.products.index') }}" class="text-sm text-rose-600 underline">Lihat seluruh katalog</a>
        </div>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            @foreach($featuredProducts as $product)
                <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-lg transition hover:-translate-y-1">
                    <div class="overflow-hidden rounded-3xl bg-slate-100">
                        @if($image = $product->images->first())
                            <img src="{{ $image->url }}" alt="{{ $product->name }}" class="h-40 w-full object-cover" />
                        @else
                            <img src="{{ asset('images/product-placeholder.svg') }}" alt="Placeholder produk" class="h-40 w-full object-cover" />
                        @endif
                    </div>
                    <div class="space-y-1 mt-4">
                        <div class="flex items-center justify-between text-xs uppercase tracking-[0.4em] text-slate-400">
                            <span>SKU</span>
                            <span>{{ $product->stock }} stok</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $product->name }}</h3>
                        <p class="text-sm text-slate-500 line-clamp-2">{{ Str::limit($product->short_description, 80) }}</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="text-slate-500">Kategori: {{ $product->category?->name ?? 'Umum' }}</span>
                        <span class="text-rose-600 font-semibold">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('front.products.show', $product) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-rose-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3-3 3M6 12h12"/>
                        </svg>
                        Lihat detail
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="mt-14 grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg">
            <h2 class="text-2xl font-semibold">Testimonial</h2>
            <p class="text-sm text-slate-500 mb-4">Suara hati pelanggan</p>
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <article class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-sm space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-900">{{ $review->user?->name ?? 'Pelanggan' }}</span>
                            <span class="text-yellow-400">{{ str_repeat('★', $review->rating) }}</span>
                        </div>
                        <p class="text-slate-600 leading-relaxed">{{ $review->comment }}</p>
                    </article>
                @endforeach
            </div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 to-rose-600 p-6 shadow-2xl text-white space-y-4">
            <h2 class="text-2xl font-semibold">Highlight Services</h2>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white/70" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4l3 9 4-18 3 9h4"/>
                    </svg>
                    <div>
                        <p class="font-semibold">Live Stock Monitoring</p>
                        <p class="text-sm text-white/70">Stok real-time dari POS & pembelian</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white/70" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c2.2 0 4-1.8 4-4S14.2 0 12 0 8 1.8 8 4s1.8 4 4 4zm0 4c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
                    </svg>
                    <div>
                        <p class="font-semibold">Kasir Friendly Interface</p>
                        <p class="text-sm text-white/70">Tombol besar & cetak struk otomatis</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white/70" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h5m4 0h2m4 0h5M6 17h12"/>
                    </svg>
                    <div>
                        <p class="font-semibold">Content Manager</p>
                        <p class="text-sm text-white/70">Banner, media, dan review siap tampil</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
