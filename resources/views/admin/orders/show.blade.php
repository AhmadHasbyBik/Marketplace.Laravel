@extends('layouts.admin')

@section('title', 'Detail Order')

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
        $statusLabel = ucfirst(str_replace('_', ' ', $order->status));
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/90 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Detail Pesanan</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Order {{ $order->order_number }}</h1>
                    <p class="text-sm text-slate-500">Tinjauan lengkap status, pembayaran, dan pengiriman pesanan.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusColorMap[$order->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $statusLabel }}</span>
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-500">Total Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="mt-5 flex flex-wrap gap-4 text-sm text-slate-500">
                <p>Order dibuat {{ $order->created_at->format('d M Y H:i') }} · {{ $order->created_at->diffForHumans() }}</p>
                <p>Pembayaran: {{ $order->payment_method ?? 'Belum ditentukan' }}</p>
                <p>Pengiriman: {{ $order->shippingMethod?->name ?? 'Belum dipilih' }}</p>
            </div>
        </header>

        <div class="rounded-3xl border border-slate-200 bg-white/90 px-6 py-5 shadow-sm shadow-slate-100">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Bukti Pembayaran</p>
                    <h2 class="text-lg font-semibold text-slate-900">Foto slip transfer</h2>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order->payment_proof_path ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                    {{ $order->payment_proof_path ? 'Menunggu verifikasi' : 'Belum dikirim' }}
                </span>
            </div>
            @if($order->payment_proof_path)
                <div class="mt-4 overflow-hidden rounded-2xl border border-slate-100 shadow-sm shadow-slate-100">
                    <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noreferrer">
                        <img src="{{ $order->payment_proof_url }}" alt="Bukti pembayaran" class="h-52 w-full object-cover" loading="lazy" />
                    </a>
                </div>
                <p class="mt-3 text-sm text-slate-500">Klik gambar untuk melihat ukuran penuh dan putuskan apakah bukti valid.</p>
            @else
                <p class="mt-3 text-sm text-slate-500">Tidak ada bukti pembayaran yang diunggah. Tanyakan kepada customer jika perlu.</p>
            @endif
        </div>
        <section class="grid gap-6 lg:grid-cols-[1.8fr,1fr]">
            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white/90 shadow-sm shadow-slate-100">
                    <div class="flex flex-col gap-1 border-b border-slate-100 px-6 py-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Items</p>
                        <h2 class="text-lg font-semibold text-slate-900">Produk dalam keranjang</h2>
                        <p class="text-sm text-slate-500">Pastikan stok dan harga sudah sesuai sebelum menyelesaikan status.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-[0.3em] text-slate-500">
                                <tr>
                                    <th class="px-6 py-3">Produk</th>
                                    <th class="px-6 py-3">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $item)
                                    <tr class="border-b border-slate-100 last:border-b-0 hover:bg-slate-50">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-slate-900">{{ $item->product?->name ?? 'Produk terhapus' }}</p>
                                            <p class="text-xs text-slate-400">{{ $item->product?->sku }}</p>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $item->quantity }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-6 text-center text-sm text-slate-500">Belum ada item tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-col gap-2 border-t border-slate-100 px-6 py-4 text-sm text-slate-600">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Ongkir</span>
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-dashed border-slate-200 pt-3 text-base font-semibold text-slate-900">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-5 shadow-sm shadow-slate-100">
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Pelangan</p>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $order->user?->name ?? 'Pelanggan tamu' }}</h3>
                        <p class="text-sm text-slate-500">{{ $order->user?->email }}</p>
                        <p class="mt-3 text-sm text-slate-500">
                            Telepon:
                            <span class="font-semibold text-slate-900">{{ $order->user?->phone ?? '-' }}</span>
                        </p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-5 shadow-sm shadow-slate-100">
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Alamat pengiriman</p>
                        @if($order->shipping_district || $order->shipping_city || $order->shipping_province)
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $order->shipping_district ?? '—' }},
                                {{ $order->shipping_city ?? '—' }}
                            </p>
                            <p class="text-sm text-slate-600">{{ $order->shipping_province }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $order->shipping_city_id ? "Kode kota: {$order->shipping_city_id}" : '' }}
                                {{ $order->shipping_district_id ? " · Kode kecamatan: {$order->shipping_district_id}" : '' }}
                            </p>
                            <p class="mt-2 text-sm text-slate-500">Kurir: {{ strtoupper($order->shipping_courier ?? '—') }} · Layanan {{ $order->shipping_service ?? '—' }}</p>
                            <p class="text-sm text-slate-500">Estimasi: {{ $order->shipping_etd ?? '—' }}</p>
                        @else
                            <p class="text-sm text-slate-500">Belum ada tujuan pengiriman tersimpan.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white/90 shadow-sm shadow-slate-100">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Status & catatan</p>
                        <h3 class="text-lg font-semibold text-slate-900">Kelola order</h3>
                    </div>
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-4 px-6 py-5">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Status</label>
                            <select name="status" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                                @foreach(['pending','paid','processing','shipped','completed','cancelled'] as $statusOption)
                                    <option value="{{ $statusOption }}" {{ $order->status === $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                            <textarea name="notes" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" placeholder="Catatan tambahan">{{ $order->notes }}</textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                                Simpan Perubahan
                            @endcomponent
                        </div>
                    </form>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white/90 shadow-sm shadow-slate-100">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Riwayat status</p>
                        <h3 class="text-lg font-semibold text-slate-900">Timeline perubahan</h3>
                    </div>
                    <div class="space-y-3 px-6 py-5 text-sm text-slate-500">
                        @forelse($order->history as $history)
                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-2 w-2 rounded-full bg-slate-400"></span>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ $history->created_at->format('d M Y H:i') }}</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</p>
                                    <p class="text-sm text-slate-500">{{ $history->notes ?? 'Tidak ada catatan tambahan.' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-sm text-slate-500">Belum ada riwayat status.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
