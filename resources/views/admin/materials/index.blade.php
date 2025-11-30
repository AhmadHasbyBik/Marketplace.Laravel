@extends('layouts.admin')

@section('title', 'Bahan Baku')

@section('content')
    @php
        use Illuminate\Support\Str;
        $statusBadge = fn ($material) => $material->is_active
            ? 'bg-emerald-100 text-emerald-700'
            : 'bg-slate-100 text-slate-600';
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Inventory</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Kelola bahan baku</h1>
                    <p class="text-sm text-slate-500">Pantau, sesuaikan, dan tautkan bahan dengan produk tanpa meninggalkan halaman.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    @component('admin.components.button', ['variant' => 'secondary', 'attrs' => 'data-open-material-create'])
                        Tambah bahan
                    @endcomponent
                    @component('admin.components.button', ['variant' => 'primary', 'attrs' => 'data-scroll-recipes'])
                        Atur resep produk
                    @endcomponent
                </div>
            </div>
        </header>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Daftar bahan</p>
                        <h2 class="text-lg font-semibold text-slate-900">Stok & status realtime</h2>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['Bahan', 'SKU', 'Unit', 'Stok', 'Status', 'Aksi']])
                @slot('body')
                    @forelse($materials as $material)
                        @php
                            $payload = json_encode([
                                'id' => $material->id,
                                'name' => $material->name,
                                'sku' => $material->sku,
                                'unit' => $material->unit,
                                'stock' => $material->stock,
                                'description' => $material->description,
                                'is_active' => $material->is_active,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

                            $history = json_encode($material->movements->map(fn($movement) => [
                                'material' => $material->name,
                                'type' => $movement->type,
                                'quantity' => $movement->quantity,
                                'notes' => $movement->notes,
                                'when' => $movement->created_at->format('d M Y H:i'),
                            ]), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $material->name }}</p>
                                <p class="text-xs text-slate-400">{{ Str::limit($material->description ?? '-', 80) }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $material->sku ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $material->unit }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ number_format($material->stock, 2, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadge($material) }}">
                                    {{ $material->is_active ? 'Aktif' : 'Dinonaktifkan' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-edit-material='{$payload}'"])
                                        Edit
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-adjust-material='{$payload}'"])
                                        Atur stok
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-history-material='{$history}'"])
                                        Riwayat
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada bahan baku.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent

            <div class="flex justify-end">
                {{ $materials->links() }}
            </div>
        </section>

        <section id="recipe-section" class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Rasio bahan baku</p>
                        <h2 class="text-lg font-semibold text-slate-900">Tentukan resep per produk</h2>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-6 shadow-sm shadow-slate-100">
                <form method="POST" action="{{ route('admin.materials.recipes') }}" class="space-y-4">
                    @csrf
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Produk</label>
                            <select name="product_id" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                                <option value="">Pilih produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Bahan per unit</p>
                                <p class="text-xs text-slate-400">Tentukan bahan baku plus jumlah untuk satu produk.</p>
                            </div>
                            <button type="button" data-add-recipe-row class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm transition hover:border-slate-300">
                                Tambah baris
                            </button>
                        </div>
                    </div>
                    <div class="space-y-3" id="recipe-item-list"></div>
                    <div class="flex justify-end gap-3 pt-1">
                        @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="reset"'])
                            Kosongkan
                        @endcomponent
                        @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                            Simpan resep
                        @endcomponent
                    </div>
                </form>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Resep tersimpan</p>
                <div class="mt-3 space-y-3">
                    @foreach($products as $product)
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <div class="flex items-center justify-between text-sm text-slate-600">
                                <span>{{ $product->name }}</span>
                                <span>{{ $product->materials->count() }} bahan</span>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @if($product->materials->isEmpty())
                                    <span class="text-xs text-slate-500">Belum ada bahan yang ditautkan.</span>
                                @endif
                                @foreach($product->materials as $material)
                                    <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ $material->name }} · {{ number_format($material->pivot->quantity, 2, ',', '.') }} {{ $material->unit }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'material-create-modal', 'title' => 'Tambah bahan baku', 'description' => 'Isi nama, unit, dan stok awal.'])
        <form method="POST" action="{{ route('admin.materials.store') }}" class="space-y-4">
            @csrf
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama bahan</label>
                    <input name="name" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">SKU</label>
                    <input name="sku" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Unit</label>
                    <input name="unit" value="pcs" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Stok awal</label>
                    <input name="stock" type="number" step="0.01" min="0" value="0" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Deskripsi</label>
                <textarea name="description" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" placeholder="Opsional"></textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0" />
                <input id="material-active" type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-slate-600" />
                <label for="material-active" class="text-sm text-slate-600">Aktifkan bahan</label>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan bahan
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'material-edit-modal', 'title' => 'Edit bahan baku', 'description' => 'Perbarui informasi dasar bahan.'])
        <form id="material-edit-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama bahan</label>
                    <input id="edit-name" name="name" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">SKU</label>
                    <input id="edit-sku" name="sku" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Unit</label>
                    <input id="edit-unit" name="unit" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Status</label>
                    <input type="hidden" name="is_active" value="0" />
                    <select id="edit-active" name="is_active" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Deskripsi</label>
                <textarea id="edit-description" name="description" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700"></textarea>
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

    @component('admin.components.modal', ['id' => 'material-adjust-modal', 'title' => 'Sesuaikan stok', 'description' => 'Tambah, kurangi, atau set stok bahan.'])
        <form id="material-adjust-form" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Bahan</label>
                <input id="adjust-material-name" type="text" readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600" />
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
                    <input name="quantity" type="number" step="0.01" min="0" value="1" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                <textarea name="notes" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700"></textarea>
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

    @component('admin.components.modal', ['id' => 'material-history-modal', 'title' => 'Riwayat stok', 'description' => 'Aktivitas penyesuaian terakhir.'])
        <div class="space-y-3">
            <p class="text-sm text-slate-600" id="history-title">Pilih bahan untuk melihat riwayat.</p>
            <div class="space-y-2" id="history-list">
                <p class="text-xs text-slate-400">Tidak ada riwayat yang ditampilkan.</p>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Tutup
                @endcomponent
            </div>
        </div>
    @endcomponent

    <template id="recipe-row-template">
            <div class="grid gap-3 md:grid-cols-2 items-end rounded-2xl border border-slate-200 bg-slate-50 p-3">
            <div>
                <label class="text-[10px] font-semibold uppercase tracking-[0.35em] text-slate-400">Bahan</label>
                <select name="materials[__INDEX__][material_id]" data-recipe-field="material" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <option value="">Pilih bahan</option>
                    @foreach($materialsList as $material)
                        <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-semibold uppercase tracking-[0.35em] text-slate-400">Jumlah per unit</label>
                <input name="materials[__INDEX__][quantity]" data-recipe-field="quantity" type="number" step="0.01" min="0" value="0" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" />
            </div>
            <div class="flex items-center justify-end">
                <button type="button" data-remove-recipe-row class="rounded-2xl border border-rose-200 bg-rose-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-rose-600 shadow-sm">Hapus</button>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');
            const modalCloseButtons = document.querySelectorAll('[data-modal-close]');

            modalCloseButtons.forEach((button) => {
                button.addEventListener('click', () => closeModal(button.closest('[data-modal]')));
            });

            const editModal = document.getElementById('material-edit-modal');
            const adjustModal = document.getElementById('material-adjust-modal');
            const historyModal = document.getElementById('material-history-modal');
            const editForm = document.getElementById('material-edit-form');
            const adjustForm = document.getElementById('material-adjust-form');
            const historyList = document.getElementById('history-list');
            const historyTitle = document.getElementById('history-title');

            document.querySelectorAll('[data-open-material-create]').forEach((button) => button.addEventListener('click', () => openModal(document.getElementById('material-create-modal'))));

            document.querySelectorAll('[data-edit-material]').forEach((button) => {
                button.addEventListener('click', () => {
                    const payload = JSON.parse(button.dataset.editMaterial);
                    if (!editForm) return;
                    editForm.action = `{{ route('admin.materials.index') }}/${payload.id}`;
                    document.getElementById('edit-name').value = payload.name;
                    document.getElementById('edit-sku').value = payload.sku || '';
                    document.getElementById('edit-unit').value = payload.unit;
                    document.getElementById('edit-description').value = payload.description || '';
                    document.getElementById('edit-active').value = payload.is_active ? '1' : '0';
                    openModal(editModal);
                });
            });

            document.querySelectorAll('[data-adjust-material]').forEach((button) => {
                button.addEventListener('click', () => {
                    const payload = JSON.parse(button.dataset.adjustMaterial);
                    if (!adjustForm) return;
                    adjustForm.action = `{{ route('admin.materials.index') }}/${payload.id}/movement`;
                    document.getElementById('adjust-material-name').value = payload.name;
                    openModal(adjustModal);
                });
            });

            document.querySelectorAll('[data-history-material]').forEach((button) => {
                button.addEventListener('click', () => {
                    const history = JSON.parse(button.dataset.historyMaterial) || [];
                    historyTitle.textContent = `Riwayat stok untuk ${history[0]?.material ?? 'bahan'}`;
                    historyList.innerHTML = '';
                    if (!history.length) {
                        historyList.innerHTML = '<p class="text-xs text-slate-400">Belum ada aktivitas.</p>';
                    } else {
                        history.forEach((entry) => {
                            const row = document.createElement('div');
                            row.className = 'rounded-2xl border border-slate-100 bg-white px-3 py-2 text-sm text-slate-600';
                            row.innerHTML = `${entry.when} &middot; ${entry.type === 'in' ? 'Tambah' : 'Kurangi'} ${entry.quantity} ${entry.notes ? '&middot; ' + entry.notes : ''}`;
                            historyList.appendChild(row);
                        });
                    }
                    openModal(historyModal);
                });
            });

            const addRecipeRowButton = document.querySelector('[data-add-recipe-row]');
            const recipeList = document.getElementById('recipe-item-list');
            const recipeTemplate = document.getElementById('recipe-row-template');
            let recipeRowIndex = 0;

            const addRecipeRow = () => {
                if (!recipeTemplate || !recipeList) return;
                const fragment = document.importNode(recipeTemplate.content, true);
                const row = fragment.firstElementChild;
                const index = recipeRowIndex++;
                row.querySelectorAll('[data-recipe-field]').forEach((field) => {
                    const fieldName = field.getAttribute('name') ?? '';
                    if (!fieldName.includes('__INDEX__')) return;
                    field.setAttribute('name', fieldName.replace('__INDEX__', index));
                });
                row.querySelector('[data-remove-recipe-row]').addEventListener('click', () => row.remove());
                recipeList.appendChild(row);
            };

            addRecipeRowButton?.addEventListener('click', addRecipeRow);
            addRecipeRow();

            document.querySelector('[data-scroll-recipes]')?.addEventListener('click', () => {
                document.querySelector('#recipe-section')?.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
@endsection
