@extends('layouts.admin')

@section('title', 'Supplier')

@section('content')
    @php
        $statusColor = [
            true => 'bg-emerald-100 text-emerald-700',
            false => 'bg-rose-100 text-rose-700',
        ];
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Supplier</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Kelola mitra dagang</h1>
                    <p class="text-sm text-slate-500">Semua aksi (create/edit/delete) dilakukan melalui modal agar tetap di satu layar.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    @component('admin.components.button', ['variant' => 'primary', 'attrs' => 'data-open-supplier-create'])
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Tambah Supplier
                    @endcomponent
                </div>
            </div>
        </header>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-600">Setiap supplier punya kontak, status, dan catatan. Akses langsung klik tombol "Edit" untuk modal.</p>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['Nama', 'Email', 'Telepon', 'Status', 'Catatan', 'Aksi']])
                @slot('body')
                    @forelse($suppliers as $supplier)
                        @php
                            $payload = json_encode([
                                'id' => $supplier->id,
                                'name' => $supplier->name,
                                'email' => $supplier->email,
                                'phone' => $supplier->phone,
                                'address' => $supplier->address,
                                'notes' => $supplier->notes,
                                'is_active' => (bool) $supplier->is_active,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                            $safePayload = e($payload);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $supplier->name }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $supplier->email }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $supplier->phone }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor[$supplier->is_active] }}">
                                    {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <p class="text-sm text-slate-700">{{ Str::limit($supplier->notes ?? 'Belum ada catatan', 70) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-supplier-edit data-supplier='{$safePayload}'"])
                                        Edit
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-delete-supplier='{$safePayload}'"])
                                        Hapus
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada supplier.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent

            <div class="flex justify-end">
                {{ $suppliers->links() }}
            </div>
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'supplier-create-modal', 'title' => 'Tambah Supplier', 'description' => 'Masukkan informasi supplier dan status aktif.'])
        <form method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-4">
            @csrf
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama</label>
                    <input name="name" type="text" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" placeholder="Nama supplier" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Email</label>
                    <input name="email" type="email" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" placeholder="supplier@mail.com" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Telepon</label>
                    <input name="phone" type="text" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" placeholder="08xx" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Alamat</label>
                    <textarea name="address" rows="2" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" placeholder="Alamat lengkap"></textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                    <textarea name="notes" rows="2" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" placeholder="Catatan kerja sama"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0" />
                    <input id="create-active" type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-200 text-sky-500" />
                    <label for="create-active" class="text-xs font-semibold text-slate-600">Aktifkan supplier</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan supplier
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'supplier-edit-modal', 'title' => 'Edit Supplier', 'description' => 'Perbarui detail kontak dan status supplier.'])
        <form id="supplier-edit-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="supplier_id" id="edit-supplier-id" />
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama</label>
                    <input id="edit-name" name="name" type="text" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Email</label>
                    <input id="edit-email" name="email" type="email" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Telepon</label>
                    <input id="edit-phone" name="phone" type="text" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Alamat</label>
                    <textarea id="edit-address" name="address" rows="2" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700"></textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                    <textarea id="edit-notes" name="notes" rows="2" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0" />
                    <input id="edit-active" type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-200 text-sky-500" />
                    <label for="edit-active" class="text-xs font-semibold text-slate-600">Aktifkan supplier</label>
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

    @component('admin.components.modal', ['id' => 'supplier-delete-modal', 'title' => 'Hapus Supplier', 'description' => 'Supplier yang dihapus tidak dapat dikembalikan.'])
        <form id="supplier-delete-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600">Yakin ingin menghapus supplier <span class="font-semibold" id="supplier-delete-target"></span>?</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createModal = document.getElementById('supplier-create-modal');
            const editModal = document.getElementById('supplier-edit-modal');
            const deleteModal = document.getElementById('supplier-delete-modal');
            const editForm = document.getElementById('supplier-edit-form');
            const deleteForm = document.getElementById('supplier-delete-form');
            const deleteTarget = document.getElementById('supplier-delete-target');
            const baseUrl = '{{ url('/admin/suppliers') }}';

            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');

            document.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.closest('[data-modal]')));
            });

            document.querySelector('[data-open-supplier-create]')?.addEventListener('click', () => openModal(createModal));

            document.querySelectorAll('[data-open-supplier-edit]').forEach((button) => {
                button.addEventListener('click', () => {
                    const payload = JSON.parse(button.dataset.supplier);
                    if (!editForm) return;
                    editForm.action = `${baseUrl}/${payload.id}`;
                    document.getElementById('edit-supplier-id').value = payload.id;
                    document.getElementById('edit-name').value = payload.name;
                    document.getElementById('edit-email').value = payload.email;
                    document.getElementById('edit-phone').value = payload.phone;
                    document.getElementById('edit-address').value = payload.address ?? '';
                    document.getElementById('edit-notes').value = payload.notes ?? '';
                    document.getElementById('edit-active').checked = payload.is_active;
                    openModal(editModal);
                });
            });

            document.querySelectorAll('[data-delete-supplier]').forEach((button) => {
                button.addEventListener('click', () => {
                    const payload = JSON.parse(button.dataset.deleteSupplier);
                    if (!deleteForm) return;
                    deleteForm.action = `${baseUrl}/${payload.id}`;
                    deleteTarget.textContent = payload.name;
                    openModal(deleteModal);
                });
            });
        });
    </script>
@endsection
