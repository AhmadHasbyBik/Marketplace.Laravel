@extends('layouts.admin')

@section('title', 'Purchase Orders')

@section('content')
    @php
        use Illuminate\Support\Str;
        $statusOptions = ['draft', 'ordered', 'received'];
        $statusColor = [
            'draft' => 'bg-amber-100 text-amber-700',
            'ordered' => 'bg-sky-100 text-sky-600',
            'received' => 'bg-emerald-100 text-emerald-700',
        ];
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Purchases</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Kelola purchase orders</h1>
                    <p class="text-sm text-slate-500">Semua aksi CRUD dilakukan via modal tanpa berpindah halaman.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    <form method="GET" class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm transition hover:border-slate-300">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari PO atau supplier" class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs text-slate-600 focus:border-sky-400 focus:outline-none" />
                        <button type="submit" class="rounded-2xl bg-[#0EA5E9] px-3 py-1 text-white">Filter</button>
                    </form>
                    @component('admin.components.button', ['variant' => 'primary', 'attrs' => 'data-open-purchase-create'])
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Buat PO
                    @endcomponent
                </div>
            </div>
        </header>

        <section class="grid gap-4 lg:grid-cols-3">
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Total PO</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $purchases->total() }}</p>
                <p class="text-sm text-slate-500">{{ $recentPurchases->count() }} order terbaru</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Supplier aktif</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $suppliers->count() }}</p>
                <p class="text-sm text-slate-500">Mitra siap kirim</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Produk tersedia</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $products->count() }}</p>
                <p class="text-sm text-slate-500">Barang aktif</p>
            </article>
        </section>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Status PO</p>
                        <h2 class="text-lg font-semibold text-slate-900">Distribusi status</h2>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['Status', 'Jumlah', 'Aksi']])
                @slot('body')
                    @forelse($statusOptions as $status)
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-700">{{ ucfirst($status) }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $statusCounts[$status] ?? 0 }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor[$status] }}">
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
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Daftar PO</p>
                        <h2 class="text-lg font-semibold text-slate-900">Purchase orders terbaru</h2>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['PO', 'Supplier', 'Tanggal', 'Total', 'Status', 'Aksi']])
                @slot('body')
                    @forelse($purchases as $purchase)
                        @php
                            $payload = json_encode([
                                'id' => $purchase->id,
                                'supplier_id' => $purchase->supplier_id,
                                'supplier' => $purchase->supplier->name,
                                'purchase_date' => $purchase->purchase_date,
                                'status' => $purchase->status,
                                'total' => $purchase->total,
                                'notes' => $purchase->notes,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $purchase->purchase_number }}</td>
                            <td class="px-4 py-3">{{ $purchase->supplier->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $purchase->purchase_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor[$purchase->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-purchase-edit data-purchase='{$payload}'"])
                                        Edit
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-purchase-delete data-purchase='{$payload}'"])
                                        Hapus
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada purchase order.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent

            <div class="flex justify-end">
                {{ $purchases->withQueryString()->links() }}
            </div>
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'purchase-create-modal', 'title' => 'Buat Purchase Order', 'description' => 'Isi supplier, tanggal, status, dan tambahkan item.'])
        <form id="purchase-create-form" method="POST" action="{{ route('admin.purchases.store') }}" class="space-y-4">
            @csrf
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Supplier</label>
                    <select name="supplier_id" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                        <option value="">Pilih supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Tanggal PO</label>
                        <input name="purchase_date" type="date" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Status</label>
                        <select name="status" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                            @foreach($statusOptions as $option)
                                <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                    <textarea name="notes" rows="2" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Daftar Item</p>
                        <button type="button" data-add-purchase-item class="rounded-2xl border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm">Tambah baris</button>
                    </div>
                    <div class="mt-3 space-y-3" id="purchase-item-list"></div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan PO
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'purchase-edit-modal', 'title' => 'Edit Purchase Order', 'description' => 'Perbarui supplier, status, dan catatan.'])
        <form id="purchase-edit-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Supplier</label>
                    <select id="edit-supplier" name="supplier_id" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700">
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Tanggal PO</label>
                        <input id="edit-date" name="purchase_date" type="date" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Status</label>
                        <select id="edit-status" name="status" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700">
                            @foreach($statusOptions as $option)
                                <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                    <textarea id="edit-notes" name="notes" rows="2" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan perubahan
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'purchase-delete-modal', 'title' => 'Hapus Purchase Order', 'description' => 'PO yang dihapus tidak bisa dikembalikan.'])
        <form id="purchase-delete-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600">Kamu akan menghapus PO <span class="font-semibold" id="purchase-delete-target"></span>.</p>
            <div class="flex justify-end gap-3 pt-2">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'type' => 'submit'])
                    Konfirmasi Hapus
                @endcomponent
            </div>
        </form>
    @endcomponent

    <template id="purchase-item-template">
        <div class="grid gap-3 md:grid-cols-4 items-end rounded-2xl border border-slate-200 bg-slate-50 p-3">
            <div>
                <label class="text-[10px] font-semibold uppercase tracking-[0.35em] text-slate-400">Produk</label>
                <select name="items[][product_id]" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">Pilih produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-semibold uppercase tracking-[0.35em] text-slate-400">Jumlah</label>
                <input name="items[][quantity]" type="number" min="1" value="1" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" />
            </div>
            <div>
                <label class="text-[10px] font-semibold uppercase tracking-[0.35em] text-slate-400">Harga satuan</label>
                <input name="items[][unit_cost]" type="number" min="0" step="0.01" value="0" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" />
            </div>
            <div class="flex items-center justify-end">
                <button type="button" data-remove-item class="rounded-2xl border border-rose-200 bg-rose-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-rose-600 shadow-sm">Hapus</button>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createModal = document.getElementById('purchase-create-modal');
            const editModal = document.getElementById('purchase-edit-modal');
            const deleteModal = document.getElementById('purchase-delete-modal');
            const editForm = document.getElementById('purchase-edit-form');
            const deleteForm = document.getElementById('purchase-delete-form');
            const deleteTarget = document.getElementById('purchase-delete-target');
            const itemList = document.getElementById('purchase-item-list');
            const itemTemplate = document.getElementById('purchase-item-template').content;
            const openCreateButtons = document.querySelectorAll('[data-open-purchase-create]');
            const openEditButtons = document.querySelectorAll('[data-open-purchase-edit]');
            const openDeleteButtons = document.querySelectorAll('[data-open-purchase-delete]');
            const modalCloseButtons = document.querySelectorAll('[data-modal-close]');
            const baseIndex = '{{ route('admin.purchases.index') }}';
            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');

            openCreateButtons.forEach((button) => button.addEventListener('click', () => {
                itemList.innerHTML = '';
                addItemRow();
                openModal(createModal);
            }));

            openEditButtons.forEach((button) => button.addEventListener('click', () => {
                const payload = JSON.parse(button.dataset.purchase);
                if (!editForm) return;
                editForm.action = `${baseIndex}/${payload.id}`;
                document.getElementById('edit-supplier').value = payload.supplier_id;
                document.getElementById('edit-date').value = payload.purchase_date;
                document.getElementById('edit-status').value = payload.status;
                document.getElementById('edit-notes').value = payload.notes ?? '';
                openModal(editModal);
            }));

            openDeleteButtons.forEach((button) => button.addEventListener('click', () => {
                const payload = JSON.parse(button.dataset.purchase);
                if (!deleteForm) return;
                deleteForm.action = `${baseIndex}/${payload.id}`;
                deleteTarget.textContent = payload.purchase_number;
                openModal(deleteModal);
            }));

            modalCloseButtons.forEach((button) => button.addEventListener('click', () => closeModal(button.closest('[data-modal]'))));

            let itemCounter = 0;

            const addItemRow = () => {
                const rowFragment = document.importNode(itemTemplate, true);
                const rowElement = rowFragment.firstElementChild;
                const index = itemCounter++;

                const productSelect = rowElement.querySelector('[name="items[][product_id]"]');
                const quantityInput = rowElement.querySelector('[name="items[][quantity]"]');
                const unitCostInput = rowElement.querySelector('[name="items[][unit_cost]"]');

                if (productSelect) {
                    productSelect.name = `items[${index}][product_id]`;
                }

                if (quantityInput) {
                    quantityInput.name = `items[${index}][quantity]`;
                }

                if (unitCostInput) {
                    unitCostInput.name = `items[${index}][unit_cost]`;
                }
                rowElement.querySelector('[data-remove-item]').addEventListener('click', () => {
                    rowElement.remove();
                });
                itemList.appendChild(rowElement);
            };

            document.querySelector('[data-add-purchase-item]')?.addEventListener('click', addItemRow);
        });
    </script>
@endsection
