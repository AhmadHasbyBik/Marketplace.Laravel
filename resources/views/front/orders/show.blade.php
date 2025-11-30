@extends('layouts.front')

@section('title', 'Detail Pesanan - UMKM Dapoer Cupid')

@section('content')
    @php
        use Illuminate\Support\Str;
        $paymentOptions = config('payment.options', []);
        $paymentInfo = $paymentOptions[$order->payment_method] ?? null;
        $courierLabel = $order->shipping_courier ? strtoupper($order->shipping_courier) : null;
        $shippingAddress = $order->address;
        $recipientName = $shippingAddress?->recipient_name ?? $order->user?->name ?? 'Nama belum tersedia';
        $addressSegments = array_filter([
            $shippingAddress?->street,
            $shippingAddress?->city,
            $shippingAddress?->province,
            $order->shipping_district,
            $order->shipping_city,
            $order->shipping_province,
        ]);
        $addressLine = $addressSegments ? implode(', ', array_unique($addressSegments)) : '-';
        $phoneNumber = $shippingAddress?->phone ?? $order->user?->phone ?? '-';
        $isCompleted = Str::lower($order->status ?? '') === 'completed';
        $oldProductId = old('product_id');
        $oldReviewProduct = $oldProductId ? $order->items->firstWhere('product_id', $oldProductId)?->product : null;
        $reviewedProductIds = $reviewedProductIds ?? [];
    @endphp
    <div
        class="space-y-6"
        x-data="{ reviewTarget: null }"
        x-init="
            @if($oldProductId)
                reviewTarget = { id: @js($oldProductId), name: @js($oldReviewProduct?->name ?? 'Produk') };
                @if($errors->any())
                    $dispatch('open-modal', 'order-review-modal');
                @endif
            @endif
        "
    >
        <h1 class="text-3xl font-semibold">Pesanan {{ $order->order_number }}</h1>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-slate-500">Status</span>
                <span class="font-semibold text-rose-600">{{ $order->status }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-slate-500">Total</span>
                <span class="font-semibold">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <div class="grid gap-4 text-sm text-slate-500 md:grid-cols-2">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Alamat Pengiriman</p>
                    <p class="text-sm text-slate-900 font-semibold">{{ $recipientName }}</p>
                    <p class="text-sm text-slate-500">{{ $addressLine }}</p>
                    <p class="text-sm text-slate-500">{{ $phoneNumber }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Pengiriman</p>
                    <p class="text-sm text-slate-900 font-semibold">{{ $order->shippingMethod?->name ?? 'Kurir eksternal' }}</p>
                    <p>Kurir: {{ $courierLabel ?? '—' }}</p>
                    <p>Layanan: {{ $order->shipping_service ?? '—' }}</p>
                    <p>Estimasi: {{ $order->shipping_etd ?? '—' }}</p>
                    <p>Ongkir: Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="text-sm text-slate-500">
                <p class="font-semibold">Pembayaran: {{ $paymentInfo['label'] ?? Str::title(str_replace('_', ' ', $order->payment_method)) }}</p>
            </div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 space-y-3">
            <h2 class="text-lg font-semibold">Items</h2>
            <ul class="space-y-3">
                @foreach($order->items as $item)
                    <li class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                        <div class="space-y-1">
                            <p class="font-semibold text-slate-900">{{ $item->product?->name ?? 'Produk tidak ditemukan' }}</p>
                            <p class="text-xs text-slate-500">x{{ $item->quantity }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-semibold text-slate-900">Rp{{ number_format($item->total, 0, ',', '.') }}</span>
                            @if($isCompleted && $item->product && !in_array($item->product_id, $reviewedProductIds))
                                <button
                                    type="button"
                                    class="rounded-full border border-rose-500 px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-[0.35em] text-rose-600 transition hover:bg-rose-50"
                                    @click="reviewTarget = { id: @js($item->product_id), name: @js($item->product->name) }; $dispatch('open-modal', 'order-review-modal')"
                                >
                                    Ulas produk
                                </button>
                            @elseif(in_array($item->product_id, $reviewedProductIds))
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-[0.35em] text-emerald-600">Ulasan terkirim</span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
            @if($isCompleted)
                <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Pesanan selesai, pilih produk untuk memberi ulasan.</p>
            @endif
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 space-y-3">
            <h2 class="text-lg font-semibold">Instruksi Pembayaran</h2>
            <p class="text-sm text-slate-500">
                {{ $paymentInfo['details'] ?? 'Tunggu konfirmasi status pembayaran oleh admin setelah Anda menyelesaikan transfer atau QRIS.' }}
            </p>
            @if($paymentInfo)
                <div class="space-y-2 text-sm text-slate-600">
                    <p class="font-semibold text-slate-900">{{ $paymentInfo['label'] }}</p>
                    <p class="text-xs text-slate-500">{{ $paymentInfo['tagline'] }}</p>
                </div>
            @else
                <p class="text-xs text-slate-400">Metode pembayaran tidak dikenali, silakan hubungi admin untuk mendapatkan instruksi.</p>
            @endif
        </div>
        @if($order->is_paid)
            <div class="rounded-3xl border border-slate-200 bg-white p-6 space-y-3">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Invoice</p>
                        <p class="text-lg font-semibold text-slate-900">Unduh sebagai PDF</p>
                        <p class="text-sm text-slate-500">Dokumen ini menampilkan detail pesanan</p>
                    </div>
                    <a
                        href="{{ route('front.orders.invoice', $order) }}"
                        class="rounded-2xl bg-slate-900 px-5 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white transition hover:bg-slate-800"
                        target="_blank"
                    >
                        Download Invoice
                    </a>
                </div>
            </div>
        @endif
        <div class="rounded-3xl border border-slate-200 bg-white p-6 space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Bukti Pembayaran</p>
                    <p class="text-lg font-semibold text-slate-900">Unggah bukti transfer</p>
                    <p class="text-sm text-slate-500">Kirim slip transfer agar admin bisa memverifikasi pembayaran lebih cepat.</p>
                </div>
                <button
                    type="button"
                    class="shrink-0 rounded-2xl border border-rose-500 bg-rose-500/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-rose-600 transition hover:bg-rose-50"
                    @click="$dispatch('open-modal', 'order-payment-proof-modal')"
                >
                    Unggah bukti
                </button>
            </div>
            @if($order->payment_proof_path)
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Bukti terakhir</p>
                    <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noreferrer" class="mt-3 block">
                        <img
                            src="{{ $order->payment_proof_url }}"
                            alt="Bukti pembayaran"
                            class="h-48 w-full rounded-2xl border border-slate-200 object-contain"
                            loading="lazy"
                        />
                    </a>
                    <p class="mt-3 text-xs text-slate-500">Admin akan meninjau bukti ini untuk memperbarui status pesanan.</p>
                </div>
            @else
                <p class="text-sm text-slate-500">Belum ada bukti pembayaran. Tekan tombol di atas untuk mengunggah.</p>
            @endif
        </div>
        <x-modal name="order-review-modal" focusable maxWidth="lg">
            <form method="POST" action="{{ route('front.reviews.store') }}" class="space-y-6 px-6 py-5">
                @csrf
                <div>
                    <p class="text-xs uppercase tracking-[0.5em] text-slate-400">Ulas Produk</p>
                    <p class="text-lg font-semibold text-slate-900" x-text="reviewTarget?.name ?? 'Pilih produk untuk memulai'"></p>
                    @error('product_id')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <input type="hidden" name="product_id" :value="reviewTarget?.id">
                <input type="hidden" name="order_reference" value="{{ $order->order_number }}">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-900" for="rating">Rating</label>
                    <select
                        id="rating"
                        name="rating"
                        required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-100"
                    >
                        <option value="">Pilih rating</option>
                        @foreach(range(5, 1) as $rating)
                            <option value="{{ $rating }}" @selected(old('rating') == $rating)>{{ $rating }} bintang</option>
                        @endforeach
                    </select>
                    @error('rating')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-900" for="comment">Komentar</label>
                    <textarea
                        id="comment"
                        name="comment"
                        rows="4"
                        required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-100"
                        placeholder="Ceritakan pengalamanmu memakai produk ini..."
                    >{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="button"
                        class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold uppercase tracking-[0.3em] text-slate-600 transition hover:border-slate-300 hover:text-slate-900"
                        @click="$dispatch('close-modal', 'order-review-modal')"
                    >
                        Batal
                    </button>
                    <x-primary-button x-bind:disabled="!reviewTarget || !reviewTarget.id">Kirim ulasan</x-primary-button>
                </div>
            </form>
        </x-modal>
        <x-modal name="order-payment-proof-modal" focusable maxWidth="md">
            <form
                method="POST"
                action="{{ route('front.orders.payment-proof', $order) }}"
                class="space-y-5 px-6 py-5"
                enctype="multipart/form-data"
                x-data="{ proofPreview: null }"
            >
                @csrf
                <div>
                    <p class="text-xs uppercase tracking-[0.5em] text-slate-400">Upload Bukti</p>
                    <p class="text-lg font-semibold text-slate-900">Lampirkan foto bukti pembayaran</p>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-900" for="payment_proof">Foto bukti</label>
                    <input
                        id="payment_proof"
                        name="payment_proof"
                        type="file"
                        accept="image/*"
                        required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-100"
                        @change="
                            const file = $event.target.files?.[0];
                            proofPreview = file ? URL.createObjectURL(file) : null;
                        "
                    />
                    <p class="text-xs text-slate-500">Kami menerima JPG/PNG maksimum 5MB.</p>
                    @error('payment_proof')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <template x-if="proofPreview">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3 text-sm text-slate-600">
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Preview</p>
                        <img
                            :src="proofPreview"
                            alt="Preview bukti pembayaran"
                            class="mt-3 h-40 w-full rounded-2xl object-cover"
                        />
                    </div>
                </template>
                @if($order->payment_proof_path)
                    <p class="text-xs text-slate-500">Mengunggah ulang akan menggantikan bukti yang sudah ada.</p>
                @endif
                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="button"
                        class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold uppercase tracking-[0.3em] text-slate-600 transition hover:border-slate-300 hover:text-slate-900"
                        @click="$dispatch('close-modal', 'order-payment-proof-modal')"
                    >
                        Keluar
                    </button>
                    <x-primary-button type="submit">Kirim bukti</x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>
@endsection
