@extends('layouts.admin')

@section('title', 'Manajemen Inventory')

@section('content')
    @php
        $statusColor = fn($product) => $product->stock <= $product->stock_minimum
            ? 'bg-rose-100 text-rose-700'
            : 'bg-emerald-100 text-emerald-700';
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Inventori</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Pantau stok produk</h1>
                    <p class="text-sm text-slate-500">Filter, cari, dan sesuaikan stok dengan modal agar tetap cepat.</p>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <input type="search" placeholder="Cari produk" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21 21-4.34-4.34" />
                            <circle cx="11" cy="11" r="8" />
                        </svg>
                    </span>
                </div>
            </div>
        </header>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-600">Semua penyesuaian stok tercatat di bagian bawah dengan tombol modal yang sama agar konsisten.</p>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['Produk', 'SKU', 'Stok', 'Minimum', 'Status', 'Aksi']])
                @slot('body')
                    @forelse($products as $product)
                        @php
                            $payload = json_encode([
                                'id' => $product->id,
                                'name' => $product->name,
                                'sku' => $product->sku,
                                'stock' => $product->stock,
                                'minimum' => $product->stock_minimum,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                <p class="text-xs text-slate-400">{{ Str::limit($product->short_description ?? '', 70) }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $product->sku ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $product->stock }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $product->stock_minimum }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor($product) }}">
                                    {{ $product->stock <= $product->stock_minimum ? 'Kritis' : 'Sehat' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-inventory-adjust data-product='{$payload}'"])
                                        Sesuaikan
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-inventory-history data-product='{$payload}'"])
                                        Riwayat
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada produk.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent

            <div class="flex justify-end">
                {{ $products->links() }}
            </div>
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'inventory-adjust-modal', 'title' => 'Sesuaikan Stok', 'description' => 'Tambahkan atau kurangi stok produk secara cepat.'])
        <form id="inventory-adjust-form" method="POST" action="{{ route('admin.inventory.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="product_id" id="inventory-product-id" value="" />
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Produk</label>
                <input id="inventory-product-name" type="text" readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600" />
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Jenis penyesuaian</label>
                    <select name="type" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                        <option value="in">Tambah stok</option>
                        <option value="out">Kurangi stok</option>
                        <option value="adjustment">Penyesuaian net</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Jumlah</label>
                    <input name="quantity" type="number" min="1" value="1" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                <textarea name="notes" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" placeholder="Contoh: Restock gudang utama"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan penyesuaian
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'inventory-history-modal', 'title' => 'Riwayat Stok', 'description' => 'Tampilkan catatan terakhir tanpa meninggalkan tabel.'])
        <div class="space-y-3">
            <p class="text-sm text-slate-600" id="inventory-history-placeholder">Pilih produk untuk melihat riwayat.</p>
            <div class="space-y-2" id="inventory-history-list">
                <p class="text-xs text-slate-400">Tidak ada riwayat yang ditampilkan.</p>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Tutup
                @endcomponent
            </div>
        </div>
    @endcomponent

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const adjustModal = document.getElementById('inventory-adjust-modal');
            const historyModal = document.getElementById('inventory-history-modal');
            const adjustForm = document.getElementById('inventory-adjust-form');
            const productIdInput = document.getElementById('inventory-product-id');
            const productNameInput = document.getElementById('inventory-product-name');
            const historyPlaceholder = document.getElementById('inventory-history-placeholder');
            const historyList = document.getElementById('inventory-history-list');
            const baseUrl = '{{ url('/admin/inventory') }}';

            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');

            document.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.closest('[data-modal]')));
            });

            document.querySelectorAll('[data-open-inventory-adjust]').forEach((button) => {
                button.addEventListener('click', () => {
                    const payload = button.dataset.product ? JSON.parse(button.dataset.product) : null;
                    if (!payload) return;
                    productIdInput.value = payload.id;
                    productNameInput.value = payload.name;
                    adjustForm.action = '{{ route('admin.inventory.store') }}';
                    openModal(adjustModal);
                });
            });

            document.querySelectorAll('[data-open-inventory-history]').forEach((button) => {
                button.addEventListener('click', () => {
                    const payload = button.dataset.product ? JSON.parse(button.dataset.product) : null;
                    historyPlaceholder.textContent = payload ? `Riwayat stok untuk ${payload.name}` : 'Riwayat belum tersedia.';
                    historyList.innerHTML = '<p class="text-xs text-slate-500">(Riwayat akan ditampilkan setelah integrasi backend.)</p>';
                    openModal(historyModal);
                });
            });
        });
    </script>
@endsection
