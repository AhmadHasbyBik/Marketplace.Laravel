@extends('layouts.admin')

@section('title', 'Laporan Operasional')

@section('content')
    @php
        $statusOptions = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];
        $statusColor = [
            'pending' => 'bg-amber-100 text-amber-700',
            'paid' => 'bg-emerald-100 text-emerald-700',
            'processing' => 'bg-sky-100 text-sky-600',
            'shipped' => 'bg-indigo-100 text-indigo-700',
            'completed' => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-rose-100 text-rose-700',
        ];
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Laporan</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Ringkasan performa</h1>
                    <p class="text-sm text-slate-500">Filter waktu, cek pendapatan, dan lihat detail order tanpa berganti halaman.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    <form method="GET" class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm transition hover:border-slate-300">
                        <label for="report-period" class="text-[10px] font-semibold tracking-[0.35em] text-slate-400">Periode</label>
                        <select id="report-period" name="period" class="rounded-2xl border border-slate-200 bg-white px-3 py-1 text-xs text-slate-600 focus:border-sky-400 focus:outline-none">
                            <option value="30" selected>30 hari terakhir</option>
                            <option value="7">7 hari</option>
                            <option value="90">3 bulan</option>
                        </select>
                        <button type="submit" class="rounded-2xl bg-[#0EA5E9] px-3 py-1 text-white">Terapkan</button>
                    </form>
                    <button data-open-report-period class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm transition hover:border-slate-300 hover:text-slate-900">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 5H3" />
                            <path d="M12 19H3" />
                            <path d="M14 3v4" />
                            <path d="M16 17v4" />
                            <path d="M21 12h-9" />
                            <path d="M21 19h-5" />
                            <path d="M21 5h-7" />
                            <path d="M8 10v4" />
                            <path d="M8 12H3" />
                        </svg>
                        Sesuaikan periode
                    </button>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <input type="search" placeholder="Cari order" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21 21-4.34-4.34" />
                            <circle cx="11" cy="11" r="8" />
                        </svg>
                    </span>
                </div>
            </div>
        </header>

        <section class="grid gap-4 lg:grid-cols-3">
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Total Pendapatan</p>
                <p class="text-3xl font-semibold text-slate-900">Rp {{ number_format($sales, 0, ',', '.') }}</p>
                <p class="text-sm text-slate-500">30 hari terakhir</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Pembelian</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $purchases }}</p>
                <p class="text-sm text-slate-500">Rekap pembelian barang</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Pesanan terbaru</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $recentOrders->count() }}</p>
                <p class="text-sm text-slate-500">List order terakhir</p>
            </article>
        </section>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Status Pesanan</p>
                        <h2 class="text-lg font-semibold text-slate-900">Distribusi status</h2>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['Status', 'Jumlah', 'Aksi']])
                @slot('body')
                    @forelse($statusOptions as $status)
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-700">{{ ucfirst($status) }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $ordersByStatus[$status] ?? 0 }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor[$status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent
        </section>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Pesanan Terbaru</p>
                        <h2 class="text-lg font-semibold text-slate-900">Order terakhir</h2>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['Order', 'Pelanggan', 'Total', 'Tanggal', 'Aksi']])
                @slot('body')
                    @forelse($recentOrders as $order)
                        @php
                            $shippingDestinationParts = array_filter([$order->shipping_district, $order->shipping_city, $order->shipping_province]);
                            $shippingDestination = $shippingDestinationParts ? implode(', ', $shippingDestinationParts) : null;
                            $shippingMethodLabel = $order->shippingMethod?->name ?: ($order->shipping_courier ? trim($order->shipping_courier . ' ' . $order->shipping_service) : null);
                            $shippingSummaryParts = array_filter([$shippingMethodLabel, $shippingDestination]);
                            $shippingSummary = $shippingSummaryParts ? implode(' • ', $shippingSummaryParts) : null;
                            $formattedWeight = $order->shipping_weight ? number_format($order->shipping_weight, 0, ',', '.') . ' gram' : null;
                            $orderPayload = json_encode([
                                'order_number' => $order->order_number,
                                'status' => $order->status,
                                'status_label' => ucfirst($order->status),
                                'order_type' => ucfirst($order->order_type ?? 'customer'),
                                'customer_name' => $order->user?->name ?? 'Guest',
                                'customer_email' => $order->user?->email,
                                'formatted_total' => 'Rp ' . number_format($order->total, 0, ',', '.'),
                                'created_at' => $order->created_at?->format('d M Y H:i'),
                                'shipping_summary' => $shippingSummary,
                                'shipping_destination' => $shippingDestination,
                                'shipping_weight' => $formattedWeight,
                                'payment_method' => $order->payment_method,
                                'shipping_service' => $order->shipping_service,
                                'shipping_courier' => $order->shipping_courier,
                                'shipping_etd' => $order->shipping_etd,
                                'notes' => $order->notes,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">{{ $order->user?->name ?? 'Guest' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-open-order-detail data-order-detail=\'' . $orderPayload . '\''])
                                    Lihat
                                @endcomponent
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada pesanan terbaru.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent
        </section>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Produk Terlaris</p>
                        <h2 class="text-lg font-semibold text-slate-900">Top 5 produk</h2>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['Produk', 'Terjual', 'Aksi']])
                @slot('body')
                    @forelse($topProducts as $item)
                        @php
                            $product = $item->product;
                            $productPayload = json_encode([
                                'product_id' => $item->product_id,
                                'name' => $product?->name,
                                'sku' => $product?->sku,
                                'category' => $product?->category?->name,
                                'sold' => (int) $item->sold,
                                'formatted_price' => $product ? 'Rp ' . number_format($product->price, 0, ',', '.') : null,
                                'stock' => $product?->stock,
                                'is_active' => (bool) ($product?->is_active ?? false),
                                'description' => $product?->short_description,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-700">{{ $item->product?->name ?? 'Produk tidak ditemukan' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $item->sold }}</td>
                            <td class="px-4 py-3">
                                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-open-product-detail data-product-detail=\'' . $productPayload . '\''])
                                    Detail
                                @endcomponent
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'report-period-modal', 'title' => 'Sesuaikan Periode Laporan', 'description' => 'Pilih rentang tanggal untuk laporan ini.'])
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Mulai</label>
                    <input name="from" type="date" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Sampai</label>
                    <input name="to" type="date" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                    <input name="notes" type="text" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" placeholder="Contoh: Fokus campaign seasonal" />
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Terapkan
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'report-product-detail-modal', 'title' => 'Detail Produk Terlaris', 'description' => 'Lihat performa dan stok tiap produk yang sedang populer.'])
        <div class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Produk</p>
                    <p id="report-product-name" class="text-lg font-semibold text-slate-900">-</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Kategori</p>
                    <p id="report-product-category" class="text-sm text-slate-500">-</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">SKU</p>
                    <p id="report-product-sku" class="text-sm text-slate-500">-</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Status</p>
                    <span id="report-product-status" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-600">-</span>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Terjual</p>
                    <p id="report-product-sold" class="text-lg font-semibold text-slate-900">0</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Harga</p>
                    <p id="report-product-price" class="text-lg font-semibold text-slate-900">-</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Stok</p>
                    <p id="report-product-stock" class="text-sm text-slate-500">-</p>
                </div>
            </div>
            <p id="report-product-description" class="text-sm text-slate-600">Deskripsi tidak tersedia.</p>
        </div>
    @endcomponent

    @component('admin.components.modal', ['id' => 'report-order-detail-modal', 'title' => 'Detail Pesanan Terbaru', 'description' => 'Periksa pelanggan, status, dan logistik pesanan terbaru.'])
        <div class="space-y-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Order</p>
                    <p id="report-order-number" class="text-lg font-semibold text-slate-900">-</p>
                </div>
                <span id="report-order-status" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-600">-</span>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Total</p>
                    <p id="report-order-total" class="text-lg font-semibold text-slate-900">-</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Tipe</p>
                    <p id="report-order-type" class="text-sm text-slate-500">-</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Tanggal</p>
                    <p id="report-order-date" class="text-sm text-slate-500">-</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Pelanggan</p>
                    <p id="report-order-customer" class="text-sm text-slate-500">-</p>
                    <p id="report-order-email" class="text-xs text-slate-400">-</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Pengiriman</p>
                    <p id="report-order-shipping" class="text-sm text-slate-500">-</p>
                    <p id="report-order-weight" class="text-xs text-slate-400">-</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Metode Bayar</p>
                    <p id="report-order-payment" class="text-sm text-slate-500">-</p>
                </div>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Catatan</p>
                <p id="report-order-notes" class="text-sm text-slate-600">-</p>
            </div>
        </div>
    @endcomponent

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const periodModal = document.getElementById('report-period-modal');
            const productModal = document.getElementById('report-product-detail-modal');
            const orderModal = document.getElementById('report-order-detail-modal');
            const openPeriodButtons = document.querySelectorAll('[data-open-report-period]');
            const openProductButtons = document.querySelectorAll('[data-open-product-detail]');
            const openOrderButtons = document.querySelectorAll('[data-open-order-detail]');
            const closeButtons = document.querySelectorAll('[data-modal-close]');

            const productNameEl = document.getElementById('report-product-name');
            const productCategoryEl = document.getElementById('report-product-category');
            const productSkuEl = document.getElementById('report-product-sku');
            const productSoldEl = document.getElementById('report-product-sold');
            const productPriceEl = document.getElementById('report-product-price');
            const productStockEl = document.getElementById('report-product-stock');
            const productStatusEl = document.getElementById('report-product-status');
            const productDescriptionEl = document.getElementById('report-product-description');

            const orderNumberEl = document.getElementById('report-order-number');
            const orderStatusEl = document.getElementById('report-order-status');
            const orderTotalEl = document.getElementById('report-order-total');
            const orderTypeEl = document.getElementById('report-order-type');
            const orderDateEl = document.getElementById('report-order-date');
            const orderCustomerEl = document.getElementById('report-order-customer');
            const orderEmailEl = document.getElementById('report-order-email');
            const orderShippingEl = document.getElementById('report-order-shipping');
            const orderWeightEl = document.getElementById('report-order-weight');
            const orderPaymentEl = document.getElementById('report-order-payment');
            const orderNotesEl = document.getElementById('report-order-notes');

            const statusColorMap = @json($statusColor);
            const baseStatusClasses = 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold';

            const setText = (element, value, fallback = '—') => {
                if (!element) {
                    return;
                }
                element.textContent = value ?? fallback;
            };

            const parsePayload = (value) => {
                if (!value) {
                    return null;
                }
                try {
                    return JSON.parse(value);
                } catch (error) {
                    console.error('Gagal membaca data modal laporan:', error);
                    return null;
                }
            };

            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');

            closeButtons.forEach((button) => button.addEventListener('click', () => closeModal(button.closest('[data-modal]'))));
            openPeriodButtons.forEach((button) => button.addEventListener('click', () => openModal(periodModal)));

            openProductButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const payload = parsePayload(button.dataset.productDetail);
                    if (!payload) {
                        return;
                    }

                    setText(productNameEl, payload.name);
                    setText(productCategoryEl, payload.category, 'Tidak ada kategori');
                    setText(productSkuEl, payload.sku);
                    setText(productSoldEl, payload.sold ?? '0');
                    setText(productPriceEl, payload.formatted_price);
                    setText(productStockEl, payload.stock != null ? payload.stock : '0');
                    setText(productDescriptionEl, payload.description, 'Deskripsi tidak tersedia.');

                    if (productStatusEl) {
                        const isActive = payload.is_active ? true : false;
                        productStatusEl.textContent = isActive ? 'Aktif' : 'Nonaktif';
                        productStatusEl.className = isActive
                            ? 'inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700'
                            : 'inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700';
                    }

                    openModal(productModal);
                });
            });

            openOrderButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const payload = parsePayload(button.dataset.orderDetail);
                    if (!payload) {
                        return;
                    }

                    setText(orderNumberEl, payload.order_number);
                    setText(orderTotalEl, payload.formatted_total);
                    setText(orderTypeEl, payload.order_type);
                    setText(orderDateEl, payload.created_at);
                    setText(orderCustomerEl, payload.customer_name);
                    setText(orderEmailEl, payload.customer_email, 'Email tidak tersedia');
                    setText(orderShippingEl, payload.shipping_summary, 'Detail pengiriman belum tersedia');
                    setText(orderWeightEl, payload.shipping_weight, 'Berat belum diisi');
                    setText(orderPaymentEl, payload.payment_method, 'Bayar belum ditentukan');
                    setText(orderNotesEl, payload.notes, 'Tidak ada catatan tambahan');

                    if (orderStatusEl) {
                        const statusKey = (payload.status ?? '').toLowerCase();
                        const statusClass = statusColorMap[statusKey] ?? 'bg-slate-100 text-slate-600';
                        orderStatusEl.textContent = payload.status_label ?? (payload.status ? payload.status.charAt(0).toUpperCase() + payload.status.slice(1) : 'Status belum diketahui');
                        orderStatusEl.className = `${baseStatusClasses} ${statusClass}`;
                    }

                    openModal(orderModal);
                });
            });
        });
    </script>
@endsection
