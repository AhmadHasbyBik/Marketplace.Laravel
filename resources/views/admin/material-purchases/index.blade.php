@extends('layouts.admin')

@section('title', 'Purchase Bahan Baku')

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
                    <h1 class="text-2xl font-semibold text-slate-900">Kelola purchase order bahan baku</h1>
                    <p class="text-sm text-slate-500">CRUD dilakukan via modal agar tim procurement tetap di satu layar.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    <form method="GET" class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm transition hover:border-slate-300">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari PO atau supplier" class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs text-slate-600 focus:border-sky-400 focus:outline-none" />
                        <button type="submit" class="rounded-2xl bg-[#0EA5E9] px-3 py-1 text-white">Filter</button>
                    </form>
                    @component('admin.components.button', ['variant' => 'primary', 'attrs' => 'data-open-material-purchase-create'])
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Buat PO bahan
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
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Bahan tersedia</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $materials->count() }}</p>
                <p class="text-sm text-slate-500">Bahan aktif dalam katalog</p>
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
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Daftar PO bahan</p>
                        <h2 class="text-lg font-semibold text-slate-900">Purchase order terbaru</h2>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['PO', 'Supplier', 'Tanggal', 'Total', 'Status', 'Transaksi', 'Aksi']])
                @slot('body')
                    @forelse($purchases as $purchase)
                        @php
                            $paidAmount = $purchase->paid_amount;
                            $balance = $purchase->balance;
                            $payload = json_encode([
                                'id' => $purchase->id,
                                'purchase_number' => $purchase->purchase_number,
                                'supplier_id' => $purchase->supplier_id,
                                'supplier' => $purchase->supplier?->name,
                                'purchase_date' => optional($purchase->purchase_date)->format('Y-m-d'),
                                'status' => $purchase->status,
                                'notes' => $purchase->notes,
                                'paid_amount' => $paidAmount,
                                'balance' => $balance,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $purchase->purchase_number }}</td>
                            <td class="px-4 py-3">{{ $purchase->supplier?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $purchase->purchase_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor[$purchase->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 space-y-1">
                                <div class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Pembayaran</div>
                                <div class="text-sm text-slate-600">Terbayar: Rp {{ number_format($paidAmount, 0, ',', '.') }}</div>
                                <div class="text-sm text-slate-600">Sisa: Rp {{ number_format($balance, 0, ',', '.') }}</div>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-material-purchase-transaction data-purchase='{$payload}'"])
                                        Transaksi
                                    @endcomponent
                                    <a href="{{ route('admin.material-purchases.pdf', $purchase) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-1 rounded-2xl border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-700 shadow-sm transition hover:border-slate-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-300">
                                        PDF
                                    </a>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-material-purchase-edit data-purchase='{$payload}'"])
                                        Edit
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-material-purchase-delete data-purchase='{$payload}'"])
                                        Hapus
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada purchase order bahan.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent

            <div class="flex justify-end">
                {{ $purchases->withQueryString()->links() }}
            </div>
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'material-purchase-create-modal', 'title' => 'Buat PO bahan baku', 'description' => 'Pilih supplier, status, dan tambahkan bahan.'])
        <form id="material-purchase-create-form" method="POST" action="{{ route('admin.material-purchases.store') }}" class="space-y-4">
            @csrf
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Supplier</label>
                    <select name="supplier_id" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                        <option value="" {{ old('supplier_id') === '' ? 'selected' : '' }}>Pilih supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Tanggal PO</label>
                        <input name="purchase_date" type="date" value="{{ old('purchase_date') }}" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Status</label>
                        <select name="status" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                            @php $selectedStatus = old('status', 'draft'); @endphp
                            @foreach($statusOptions as $option)
                                <option value="{{ $option }}" {{ $selectedStatus === $option ? 'selected' : '' }}>{{ ucfirst($option) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                    <textarea name="notes" rows="2" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">{{ old('notes') }}</textarea>
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Daftar bahan</p>
                        <button type="button" data-add-material-row class="rounded-2xl border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm">Tambah baris</button>
                    </div>
                    <div class="mt-3 space-y-3" id="material-item-list"></div>
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

    @component('admin.components.modal', ['id' => 'material-purchase-edit-modal', 'title' => 'Edit PO bahan baku', 'description' => 'Perbarui metadata PO.'])
        <form id="material-purchase-edit-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Supplier</label>
                    <select id="edit-purchase-supplier" name="supplier_id" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700">
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Tanggal PO</label>
                        <input id="edit-purchase-date" name="purchase_date" type="date" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Status</label>
                        <select id="edit-purchase-status" name="status" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700">
                            @foreach($statusOptions as $option)
                                <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                    <textarea id="edit-purchase-notes" name="notes" rows="2" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700"></textarea>
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

    @component('admin.components.modal', ['id' => 'material-purchase-delete-modal', 'title' => 'Hapus PO bahan baku', 'description' => 'Konfirmasi aksi ini tidak dapat dibatalkan.'])
        <form id="material-purchase-delete-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600">Kamu akan menghapus PO <span class="font-semibold" id="material-purchase-delete-target"></span>.</p>
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

    @component('admin.components.modal', ['id' => 'material-purchase-transaction-modal', 'title' => 'Catat transaksi PO', 'description' => 'Rekam pembayaran supplier untuk purchase order ini.'])
        <form id="material-purchase-transaction-form" method="POST" action="" class="space-y-4">
            @csrf
            <input type="hidden" name="transaction_material_purchase_id" id="transaction-material-purchase-id" value="{{ old('transaction_material_purchase_id') }}" />
            <p class="text-sm text-slate-600">PO <span class="font-semibold" id="transaction-purchase-number">-</span></p>
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Tanggal transaksi</label>
                    <input id="transaction-date" name="transaction_date" type="date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Metode pembayaran</label>
                    <input id="transaction-payment-method" name="payment_method" type="text" value="{{ old('payment_method', 'Transfer Bank') }}" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Jumlah (Rp)</label>
                <input id="transaction-amount" name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount') }}" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                <textarea id="transaction-notes" name="notes" rows="2" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">{{ old('notes') }}</textarea>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan transaksi
                @endcomponent
            </div>
        </form>
    @endcomponent

    <template id="material-purchase-item-template">
        <div class="grid gap-3 md:grid-cols-4 items-end rounded-2xl border border-slate-200 bg-slate-50 p-3">
            <div>
                <label class="text-[10px] font-semibold uppercase tracking-[0.35em] text-slate-400">Bahan</label>
                <select name="items[][material_id]" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">Pilih bahan</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-semibold uppercase tracking-[0.35em] text-slate-400">Jumlah</label>
                <input name="items[][quantity]" type="number" min="0.01" step="0.01" value="1" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" />
            </div>
            <div>
                <label class="text-[10px] font-semibold uppercase tracking-[0.35em] text-slate-400">Harga satuan</label>
                <input name="items[][unit_cost]" type="number" min="0" step="0.01" value="0" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" />
            </div>
            <div class="flex items-center justify-end">
                <button type="button" data-remove-material-row class="rounded-2xl border border-rose-200 bg-rose-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-rose-600 shadow-sm">Hapus</button>
            </div>
        </div>
    </template>

    @php
        $hasOldMaterialPurchaseInput = session()->hasOldInput('supplier_id');
        $oldMaterialPurchaseItems = $hasOldMaterialPurchaseInput ? session()->getOldInput('items', []) : [];
        $hasOldMaterialPurchaseTransactionInput = session()->hasOldInput('transaction_material_purchase_id');
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createModal = document.getElementById('material-purchase-create-modal');
            const editModal = document.getElementById('material-purchase-edit-modal');
            const deleteModal = document.getElementById('material-purchase-delete-modal');
            const transactionModal = document.getElementById('material-purchase-transaction-modal');
            const editForm = document.getElementById('material-purchase-edit-form');
            const deleteForm = document.getElementById('material-purchase-delete-form');
            const transactionForm = document.getElementById('material-purchase-transaction-form');
            const deleteTarget = document.getElementById('material-purchase-delete-target');
            const transactionPurchaseLabel = document.getElementById('transaction-purchase-number');
            const transactionDateInput = document.getElementById('transaction-date');
            const transactionPaymentMethodInput = document.getElementById('transaction-payment-method');
            const transactionAmountInput = document.getElementById('transaction-amount');
            const transactionNotesInput = document.getElementById('transaction-notes');
            const transactionPurchaseInput = document.getElementById('transaction-material-purchase-id');
            const itemList = document.getElementById('material-item-list');
            const template = document.getElementById('material-purchase-item-template');
            const baseRoute = '{{ route('admin.material-purchases.index') }}';
            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');

            document.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.closest('[data-modal]')));
            });

            const oldCreateItems = @json($oldMaterialPurchaseItems);
            let pendingCreateItems = Array.isArray(oldCreateItems) ? oldCreateItems : [];
            let materialRowIndex = 0;
            const shouldOpenCreateModalOnError = @json($errors->any() && $hasOldMaterialPurchaseInput);

            const appendMaterialRow = (data = null) => {
                if (!template || !itemList) return;
                const fragment = document.importNode(template.content, true);
                const row = fragment.firstElementChild;
                const materialSelect = row.querySelector('[name="items[][material_id]"]');
                const quantityInput = row.querySelector('[name="items[][quantity]"]');
                const unitCostInput = row.querySelector('[name="items[][unit_cost]"]');
                const safeData = data && typeof data === 'object' ? data : null;
                const rowIndex = materialRowIndex++;

                if (materialSelect) {
                    materialSelect.name = `items[${rowIndex}][material_id]`;
                }
                if (quantityInput) {
                    quantityInput.name = `items[${rowIndex}][quantity]`;
                }
                if (unitCostInput) {
                    unitCostInput.name = `items[${rowIndex}][unit_cost]`;
                }

                if (safeData) {
                    if (materialSelect && typeof safeData.material_id !== 'undefined') {
                        materialSelect.value = safeData.material_id;
                    }
                    if (quantityInput && typeof safeData.quantity !== 'undefined') {
                        quantityInput.value = safeData.quantity;
                    }
                    if (unitCostInput && typeof safeData.unit_cost !== 'undefined') {
                        unitCostInput.value = safeData.unit_cost;
                    }
                }

                row.querySelector('[data-remove-material-row]').addEventListener('click', () => row.remove());
                itemList.appendChild(row);
            };

            const renderMaterialRows = (rows = []) => {
                if (!itemList) return;
                itemList.innerHTML = '';
                materialRowIndex = 0;

                if (!Array.isArray(rows) || rows.length === 0) {
                    appendMaterialRow();
                    return;
                }

                rows.forEach((rowData) => appendMaterialRow(rowData));
            };

            const handleCreateModalOpen = () => {
                if (!itemList) return;

                if (pendingCreateItems.length) {
                    renderMaterialRows(pendingCreateItems);
                    pendingCreateItems = [];
                } else {
                    renderMaterialRows();
                }

                openModal(createModal);
            };

            document.querySelectorAll('[data-open-material-purchase-create]').forEach((button) => button.addEventListener('click', handleCreateModalOpen));
            document.querySelector('[data-add-material-row]')?.addEventListener('click', () => appendMaterialRow());

            if (shouldOpenCreateModalOnError) {
                handleCreateModalOpen();
            }

            document.querySelectorAll('[data-open-material-purchase-edit]').forEach((button) => button.addEventListener('click', () => {
                const payload = JSON.parse(button.dataset.purchase);
                if (!editForm) return;
                editForm.action = `${baseRoute}/${payload.id}`;
                document.getElementById('edit-purchase-supplier').value = payload.supplier_id;
                document.getElementById('edit-purchase-date').value = payload.purchase_date;
                document.getElementById('edit-purchase-status').value = payload.status;
                document.getElementById('edit-purchase-notes').value = payload.notes ?? '';
                openModal(editModal);
            }));

            document.querySelectorAll('[data-open-material-purchase-delete]').forEach((button) => button.addEventListener('click', () => {
                const payload = JSON.parse(button.dataset.purchase);
                if (!deleteForm) return;
                deleteForm.action = `${baseRoute}/${payload.id}`;
                deleteTarget.textContent = payload.purchase_number ?? payload.id;
                openModal(deleteModal);
            }));

            const transactionButtons = document.querySelectorAll('[data-open-material-purchase-transaction]');
            const oldTransactionDate = @json(old('transaction_date'));
            const oldTransactionPaymentMethod = @json(old('payment_method'));
            const oldTransactionAmount = @json(old('amount'));
            const oldTransactionNotes = @json(old('notes'));
            const rawOldTransactionPurchaseId = @json(old('transaction_material_purchase_id'));
            const oldTransactionPurchaseId = rawOldTransactionPurchaseId ? String(rawOldTransactionPurchaseId) : null;
            const shouldOpenTransactionModalOnError = @json($errors->any() && $hasOldMaterialPurchaseTransactionInput);
            const defaultPaymentMethod = 'Transfer Bank';

            const handleTransactionModalOpen = (payload) => {
                if (!transactionModal || !transactionForm || !transactionPurchaseLabel || !transactionDateInput || !transactionPaymentMethodInput || !transactionAmountInput || !transactionNotesInput || !transactionPurchaseInput) {
                    return;
                }

                const shouldUseOldValues = shouldOpenTransactionModalOnError && oldTransactionPurchaseId && String(payload.id) === oldTransactionPurchaseId;
                const balanceValue = typeof payload.balance !== 'undefined' ? payload.balance : '';

                transactionForm.action = `${baseRoute}/${payload.id}/transactions`;
                transactionPurchaseLabel.textContent = payload.purchase_number ?? payload.id;
                transactionPurchaseInput.value = payload.id;
                transactionDateInput.value = shouldUseOldValues ? (oldTransactionDate ?? '') : (payload.purchase_date ?? '');
                transactionPaymentMethodInput.value = shouldUseOldValues ? (oldTransactionPaymentMethod ?? defaultPaymentMethod) : defaultPaymentMethod;
                transactionAmountInput.value = shouldUseOldValues ? (oldTransactionAmount ?? '') : balanceValue;
                transactionNotesInput.value = shouldUseOldValues ? (oldTransactionNotes ?? '') : '';
                openModal(transactionModal);
            };

            transactionButtons.forEach((button) => button.addEventListener('click', () => {
                const payload = JSON.parse(button.dataset.purchase);
                handleTransactionModalOpen(payload);
            }));

            if (shouldOpenTransactionModalOnError && oldTransactionPurchaseId) {
                const targetButton = Array.from(transactionButtons).find((button) => {
                    try {
                        const payload = JSON.parse(button.dataset.purchase);
                        return String(payload.id) === oldTransactionPurchaseId;
                    } catch {
                        return false;
                    }
                });

                if (targetButton) {
                    handleTransactionModalOpen(JSON.parse(targetButton.dataset.purchase));
                }
            }
        });
    </script>
@endsection
