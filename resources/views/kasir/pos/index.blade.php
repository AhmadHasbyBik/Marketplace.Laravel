@extends('layouts.kasir')

@section('title', 'POS Kasir')

@section('content')
    @php
        $productsForJs = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float) $product->price,
                'stock' => (int) $product->stock,
                'category' => $product->category?->name,
            ];
        });

        $summaryStock = $products->sum('stock');
        $summaryValue = $products->sum(fn ($product) => $product->stock * $product->price);
        $baseParams = array_filter(['q' => request('q')]);
    @endphp

    <div x-data='posCart(@json($productsForJs))' class="space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 space-y-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-rose-500">Live POS</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Kasir UMKM Dapoer Cupid</h1>
                    <p class="text-sm text-slate-500">Pantau produk, tambahkan ke keranjang, dan selesaikan transaksi dalam satu tampilan.</p>
                </div>
                <div class="flex flex-wrap gap-3 text-sm">
                    <div class="space-y-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-500">
                        <p class="text-[0.65rem] uppercase tracking-[0.3em]">Produk tampil</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $products->count() }}</p>
                    </div>
                    <div class="space-y-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-500">
                        <p class="text-[0.65rem] uppercase tracking-[0.3em]">Total stok</p>
                        <p class="text-lg font-semibold text-slate-900">{{ number_format($summaryStock) }}</p>
                    </div>
                    <div class="space-y-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-500">
                        <p class="text-[0.65rem] uppercase tracking-[0.3em]">Nilai barang</p>
                        <p class="text-lg font-semibold text-slate-900">Rp {{ number_format($summaryValue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-[1fr,auto]">
                <form method="GET" class="grid gap-3 md:grid-cols-[1fr,auto]">
                    <label class="sr-only" for="q">Cari produk</label>
                    <input
                        id="q"
                        name="q"
                        value="{{ request('q') }}"
                        type="text"
                        placeholder="Cari nama produk, SKU, atau kategori"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-rose-400 focus:outline-none"
                    >
                    <button
                        type="submit"
                        class="rounded-2xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                    >
                        Cari Produk
                    </button>
                </form>
                <div class="flex flex-wrap gap-2 overflow-x-auto pb-1">
                    <a
                        href="{{ route('kasir.pos.index', $baseParams) }}"
                        class="rounded-full border border-slate-200 bg-slate-100 px-4 py-1 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:border-rose-500 hover:text-rose-500"
                    >Semua</a>
                    @foreach($categories as $category)
                        <a
                            href="{{ route('kasir.pos.index', array_filter(['q' => request('q'), 'category_id' => $category->id])) }}"
                            class="flex items-center gap-2 rounded-full border px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-wide {{ $categoryId == $category->id ? 'border-rose-300 bg-rose-50 text-rose-600' : 'border-slate-200 bg-slate-100 text-slate-500' }}"
                        >
                            <span>{{ $category->name }}</span>
                            <span class="text-slate-400">({{ $category->products_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <template x-for="product in products" :key="product.id">
                        <div class="group rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900" x-text="product.name"></p>
                                    <p class="text-xs text-slate-400" x-text="'SKU: ' + product.sku"></p>
                                </div>
                                <span class="text-xs uppercase tracking-[0.3em] text-slate-400" x-text="product.category || 'Umum'"></span>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <p class="text-lg font-semibold text-rose-500" x-text="formatCurrency(product.price)"></p>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400" x-text="product.stock + ' stok'"></p>
                            </div>
                            <div class="mt-5 flex items-center gap-3">
                                <button
                                    type="button"
                                    class="flex-1 rounded-2xl border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 transition hover:border-rose-500 hover:text-rose-500 disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="product.stock === 0"
                                    @click="addProduct(product)"
                                >
                                    Tambah ke keranjang
                                </button>
                                <span class="text-[0.65rem] text-rose-500" x-show="product.stock === 0">Habis</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[1fr,0.85fr]">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Ringkasan Transaksi</p>
                        <h2 class="text-xl font-semibold text-slate-900">Keranjang</h2>
                        <p class="text-sm text-slate-500">{{ now()->format('d M Y H:i') }}</p>
                    </div>
                    <button
                        type="button"
                        class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-600 transition hover:border-rose-500 hover:text-rose-500"
                        @click="clearCart"
                    >Kosongkan</button>
                </div>

                <div class="mt-6 space-y-5">
                    <template x-if="cartItems.length">
                        <div class="space-y-4">
                            <template x-for="(item, index) in cartItems" :key="item.product_id">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900" x-text="item.name"></p>
                                            <p class="text-xs text-slate-500" x-text="'SKU: ' + item.sku"></p>
                                            <p class="text-xs text-slate-500" x-text="formatCurrency(item.price)"></p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                class="rounded-xl border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600"
                                                @click="decrease(item)"
                                            >-</button>
                                            <input
                                                type="number"
                                                min="1"
                                                class="w-16 rounded-xl border border-slate-200 bg-white px-2 py-1 text-xs text-slate-800"
                                                x-model.number="item.quantity"
                                                :max="item.stock"
                                                @input="item.quantity = Math.min(Math.max(1, item.quantity), item.stock)"
                                            >
                                            <button
                                                type="button"
                                                class="rounded-xl border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600"
                                                @click="increase(item)"
                                            >+</button>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                                        <p x-text="'Jumlah: ' + item.quantity"></p>
                                        <div class="flex items-center gap-3">
                                            <span x-text="formatCurrency(item.price * item.quantity)"></span>
                                            <button
                                                type="button"
                                                class="text-rose-500"
                                                @click="removeItem(index)"
                                            >Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!cartItems.length">
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-white/80 p-6 text-center text-sm text-slate-500">
                            Tambahkan produk untuk memulai transaksi.
                        </div>
                    </template>
                </div>
            </section>

            <form
                action="{{ route('kasir.pos.store') }}"
                method="POST"
                class="rounded-3xl border border-slate-200 bg-white p-6 space-y-6 shadow-sm"
            >
                @csrf
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs uppercase tracking-[0.3em] text-slate-400">
                        <span>Subtotal</span>
                        <span x-text="formatCurrency(subtotal)"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs uppercase tracking-[0.3em] text-slate-400">
                        <span>Diskon</span>
                        <span x-text="formatCurrency(discountValue)"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs uppercase tracking-[0.3em] text-slate-400">
                        <span>PPN</span>
                        <span x-text="formatCurrency(taxAmount)"></span>
                    </div>
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-900">
                        <span>Total</span>
                        <span x-text="formatCurrency(total)"></span>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-500">Diskon (IDR)</label>
                    <input
                        type="number"
                        name="discount"
                        min="0"
                        step="1"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700"
                        x-model.number="discount"
                    >
                    <div class="flex flex-wrap items-center gap-3">
                        <label class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-500">PPN (%)</label>
                        <input
                            type="range"
                            min="0"
                            max="15"
                            step="0.5"
                            x-model.number="taxPercentage"
                            class="h-1 w-full cursor-pointer appearance-none rounded-full bg-slate-200"
                        >
                        <span class="text-sm text-slate-700" x-text="taxPercentage + '%'">0%</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-600" @click="setDiscountPercentage(0.05)">Diskon 5%</button>
                        <button type="button" class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-600" @click="setDiscountPercentage(0.1)">Diskon 10%</button>
                        <button type="button" class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-600" @click="setDiscountPercentage(0.15)">Diskon 15%</button>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan/Kasir</label>
                    <input
                        type="text"
                        name="notes"
                        placeholder="Misal: pelanggan VIP"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700"
                        x-model="notes"
                    >
                </div>

                <div class="space-y-3">
                    <label class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-500">Metode Pembayaran</label>
                    <select
                        name="payment_method"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700"
                        x-model="paymentMethod"
                    >
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                        <option value="edc">EDC</option>
                    </select>
                </div>

                <div class="hidden">
                    <template x-for="(item, index) in cartItems" :key="item.product_id">
                        <div>
                            <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                            <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity">
                            <input type="hidden" :name="`items[${index}][unit_price]`" :value="item.price">
                        </div>
                    </template>
                    <input type="hidden" name="subtotal" :value="subtotal">
                    <input type="hidden" name="tax" :value="taxAmount">
                    <input type="hidden" name="total" :value="total">
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full rounded-2xl bg-gradient-to-r from-rose-500 to-fuchsia-500 px-4 py-3 text-sm font-semibold uppercase tracking-[0.3em] text-white transition disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!cartItems.length"
                    >
                        Selesai & Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function posCart(initialProducts) {
            const currencyFormatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            });

            return {
                products: initialProducts,
                cartItems: [],
                discount: 0,
                taxPercentage: 0,
                paymentMethod: 'cash',
                notes: '',
                get subtotal() {
                    return this.cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
                },
                get taxAmount() {
                    return Math.max(0, (this.taxPercentage / 100) * this.subtotal);
                },
                get discountValue() {
                    if (this.subtotal === 0) return 0;
                    return Math.min(Math.max(this.discount, 0), this.subtotal);
                },
                get total() {
                    return Math.max(0, this.subtotal - this.discountValue + this.taxAmount);
                },
                formatCurrency(value) {
                    return currencyFormatter.format(Math.max(value, 0));
                },
                addProduct(product) {
                    if (!product || product.stock <= 0) return;
                    const existing = this.cartItems.find((item) => item.product_id === product.id);
                    if (existing) {
                        if (existing.quantity < product.stock) {
                            existing.quantity += 1;
                        }
                        return;
                    }
                    this.cartItems.push({
                        product_id: product.id,
                        name: product.name,
                        sku: product.sku,
                        price: product.price,
                        stock: product.stock,
                        quantity: 1,
                    });
                },
                increase(item) {
                    if (item.quantity < item.stock) {
                        item.quantity += 1;
                    }
                },
                decrease(item) {
                    if (item.quantity > 1) {
                        item.quantity -= 1;
                    }
                },
                removeItem(index) {
                    this.cartItems.splice(index, 1);
                },
                clearCart() {
                    this.cartItems = [];
                    this.discount = 0;
                    this.taxPercentage = 0;
                },
                setDiscountPercentage(rate) {
                    this.discount = Math.round(Math.min(this.subtotal, Math.max(0, this.subtotal * rate)));
                },
            };
        }
    </script>
@endpush
