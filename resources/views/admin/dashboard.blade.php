@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    @php
        $statusColorMap = [
            'pending' => 'bg-amber-100 text-amber-700',
            'paid' => 'bg-emerald-100 text-emerald-700',
            'processing' => 'bg-sky-100 text-sky-600',
            'shipped' => 'bg-indigo-100 text-indigo-700',
            'completed' => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-rose-100 text-rose-700',
        ];
    @endphp

    @php
        $rajaConfig = config('services.rajaongkir');
        $rajaCouriers = collect($rajaConfig['couriers'] ?? []);
        $rajaBase = $rajaConfig['base_url'] ?? 'https://rajaongkir.komerce.id/api/v1';
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Dashboard</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Ringkasan operasional UMKM Dapoer Cupid</h1>
                    <p class="text-sm text-slate-500">Menjaga segala aktivitas admin tetap dalam satu halaman: order, produk, inventory, dan laporan.</p>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <input type="search" placeholder="Cari insight" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21 21-4.34-4.34" />
                            <circle cx="11" cy="11" r="8" />
                        </svg>
                    </span>
                </div>
            </div>
        </header>

        <section class="grid gap-4 md:grid-cols-3">
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Pesanan</p>
                <p class="text-3xl font-semibold text-slate-900">{{ number_format($ordersCount) }}</p>
                <p class="text-sm text-slate-500">Order terus bergerak, pantau status pending hingga completed.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Produk</p>
                <p class="text-3xl font-semibold text-slate-900">{{ number_format($productsCount) }}</p>
                <p class="text-sm text-slate-500">Katalog aktif dan siap tampil di storefront pelanggan.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Stok kritis</p>
                <p class="text-3xl font-semibold text-slate-900">{{ number_format($lowStock) }}</p>
                <p class="text-sm text-slate-500">Bantu tim logistik mengisi ulang stok terendah.</p>
            </article>
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm shadow-slate-100">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Metrics supplier</p>
                    <span class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Sumber terverifikasi</span>
                </div>
                <div class="mt-5 space-y-4 text-sm text-slate-600">
                    <p>Supplier aktif: <span class="font-semibold text-slate-900">{{ $suppliersCount }}</span></p>
                    <p>Shipping methods: <span class="font-semibold text-slate-900">{{ $shippingCount }}</span></p>
                    <p>Banner & promo hidup: <span class="font-semibold text-slate-900">{{ $bannersCount }}</span></p>
                    <p>Review terbaru: <span class="font-semibold text-slate-900">{{ $reviewsCount }}</span></p>
                </div>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm shadow-slate-100">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Trend transaksi</p>
                    <span class="rounded-2xl bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-600">Fokus</span>
                </div>
                <p class="mt-4 text-sm text-slate-500">{{ $purchasesCount }} pembelian sudah tercatat. Gunakan panel kategori dan produk untuk membalas kebutuhan in-demand.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm'])
                        Refresh data
                    @endcomponent
                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm'])
                        Export laporan
                    @endcomponent
                </div>
            </article>
        </section>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Pesanan terbaru</p>
                        <h2 class="text-lg font-semibold text-slate-900">Riwayat fulfilment</h2>
                    </div>
                </div>
            </div>
            @component('admin.components.table', ['headers' => ['No Order', 'Pelanggan', 'Status', 'Total', 'Tanggal', 'Aksi']])
                @slot('body')
                    @forelse($recentOrders as $order)
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">{{ $order->user?->name ?? 'Pelanggan tamu' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColorMap[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm'])
                                    Lihat
                                @endcomponent
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada pesanan terbaru.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent
        </section>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Raja Ongkir</p>
                        <h2 class="text-lg font-semibold text-slate-900">Koneksi Komerce API</h2>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Realtime tarif</span>
                </div>
                <div class="mt-4 space-y-2 text-sm text-slate-600">
                    <p>
                        Base URL:
                        <span class="font-semibold text-slate-900">{{ $rajaBase }}</span>
                    </p>
                    <p>
                        Origin city ID:
                        <span class="font-semibold text-slate-900">{{ $rajaConfig['origin_city_id'] ?? '-' }}</span>
                    </p>
                    <p>
                        Kurir aktif:
                        <span class="font-semibold text-slate-900">{{ $rajaCouriers->filter()->map(fn ($item) => strtoupper($item))->join(', ') ?: 'Belum ditentukan' }}</span>
                    </p>
                    <p>
                        Sinkronkan slug metode pengiriman dengan nama kurir (jne, pos, tiki, dll) agar checkout otomatis menyimpan pilihan pelanggan.
                    </p>
                </div>
                <div class="mt-5 flex flex-wrap gap-3">
                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" onclick="window.location.href=\'' . route('admin.shipping-methods.index') . '\'"'])
                        Kelola Raja Ongkir
                    @endcomponent
                    <span class="text-xs text-slate-400">Pastikan API Key, origin city, dan dokumen kurir sudah diisi.</span>
                </div>
            </div>
        </section>
    </div>
@endsection
