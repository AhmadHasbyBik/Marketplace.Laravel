@extends('layouts.admin')

@section('title', 'Metode Pengiriman')

@section('content')
    @php
        use Illuminate\Support\Str;
        $typeLabels = [
            'courier' => 'Kurir',
            'pickup' => 'Ambil di toko',
        ];
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Pengiriman</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Kelola metode logistik</h1>
                    <p class="text-sm text-slate-500">Semua pengaturan pengiriman dibuat, diedit, atau dihapus melalui modal yang ringan dan rapi.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    @component('admin.components.button', ['variant' => 'primary', 'attrs' => 'data-open-shipping-create'])
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Tambah Metode
                    @endcomponent
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <input type="search" placeholder="Cari nama metode" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" />
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
                <p class="text-sm text-slate-600">Setiap metode bisa memiliki jenis, zona, biaya, dan estimasi berbeda, kelola semuanya tanpa meninggalkan halaman ini.</p>
            </div>

            @component('admin.components.table', ['headers' => ['Nama', 'Tipe', 'Biaya', 'Estimasi', 'Status', 'Aksi']])
                @slot('body')
                    @forelse($methods as $method)
                        @php
                            $payload = json_encode([
                                'id' => $method->id,
                                'name' => $method->name,
                                'slug' => $method->slug,
                                'description' => $method->description,
                                'type' => $method->type,
                                'flat_rate' => $method->flat_rate,
                                'estimation' => $method->estimation,
                                'is_active' => (bool) $method->is_active,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                            $typeLabel = $typeLabels[$method->type] ?? ucfirst($method->type);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $method->name }}</p>
                                <p class="text-xs text-slate-400">{{ Str::limit($method->description, 80) }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $typeLabel }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">Rp {{ number_format($method->flat_rate, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $method->estimation ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $method->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $method->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-edit-shipping='{$payload}'"])
                                        Edit
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-delete-shipping='{$payload}'"])
                                        Hapus
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada metode pengiriman.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent

            <div class="flex justify-end">
                {{ $methods->links() }}
            </div>
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'shipping-create-modal', 'title' => 'Tambah Metode Pengiriman', 'description' => 'Isi nama, tipe, biaya, dan estimasi untuk metode baru.'])
        <form method="POST" action="{{ route('admin.shipping-methods.store') }}" class="space-y-4">
            @csrf
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama</label>
                    <input id="create-name" name="name" type="text" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Slug</label>
                    <input id="create-slug" name="slug" type="text" required readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Deskripsi</label>
                    <textarea name="description" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Tipe</label>
                        <select name="type" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                            <option value="courier">Kurir</option>
                            <option value="pickup">Ambil di toko</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Biaya flat</label>
                        <input name="flat_rate" type="number" min="0" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Estimasi</label>
                    <input name="estimation" type="text" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0" />
                    <input type="checkbox" id="create-active" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-200 text-sky-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-sky-300" />
                    <label for="create-active" class="text-xs font-semibold text-slate-600">Aktifkan metode</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan metode
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'shipping-edit-modal', 'title' => 'Edit Metode Pengiriman', 'description' => 'Perbarui nama, tipe, atau estimasi sesuai kebutuhan.'])
        <form id="shipping-edit-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama</label>
                    <input id="edit-name" name="name" type="text" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Slug</label>
                    <input id="edit-slug" name="slug" type="text" required readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Deskripsi</label>
                    <textarea id="edit-description" name="description" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Tipe</label>
                        <select id="edit-type" name="type" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                            <option value="courier">Kurir</option>
                            <option value="pickup">Ambil di toko</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Biaya flat</label>
                        <input id="edit-flat-rate" name="flat_rate" type="number" min="0" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Estimasi</label>
                    <input id="edit-estimation" name="estimation" type="text" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0" />
                    <input id="edit-active" type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-200 text-sky-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-sky-300" />
                    <label for="edit-active" class="text-xs font-semibold text-slate-600">Aktifkan metode</label>
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

    @component('admin.components.modal', ['id' => 'shipping-delete-modal', 'title' => 'Hapus Metode Pengiriman', 'description' => 'Metode yang sudah dihapus tidak bisa dikembalikan.'])
        <form id="shipping-delete-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600">Yakin ingin menghapus metode <span class="font-semibold" id="shipping-delete-target"></span>?</p>
            <div class="flex justify-end gap-3 pt-2">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'type' => 'submit'])
                    Hapus
                @endcomponent
            </div>
        </form>
    @endcomponent

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createModal = document.getElementById('shipping-create-modal');
            const editModal = document.getElementById('shipping-edit-modal');
            const deleteModal = document.getElementById('shipping-delete-modal');
            const editForm = document.getElementById('shipping-edit-form');
            const deleteForm = document.getElementById('shipping-delete-form');
            const deleteTarget = document.getElementById('shipping-delete-target');
            const baseUrl = '{{ url('/admin/shipping-methods') }}';

            const slugifyValue = (value) => value
                .toString()
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/[\s_]+/g, '-')
                .replace(/-+/g, '-');

            const bindSlugPair = (sourceId, targetId) => {
                const source = document.getElementById(sourceId);
                const target = document.getElementById(targetId);
                if (!source || !target) return;
                const updateSlug = () => {
                    target.value = slugifyValue(source.value);
                };
                source.addEventListener('input', updateSlug);
                updateSlug();
            };

            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');

            document.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.closest('[data-modal]')));
            });

            document.querySelector('[data-open-shipping-create]')?.addEventListener('click', () => openModal(createModal));

            document.querySelectorAll('[data-edit-shipping]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!editForm) return;
                    const payload = JSON.parse(button.dataset.editShipping);
                    editForm.action = `${baseUrl}/${payload.id}`;
                    document.getElementById('edit-name').value = payload.name;
                    document.getElementById('edit-slug').value = payload.slug;
                    document.getElementById('edit-description').value = payload.description ?? '';
                    document.getElementById('edit-type').value = payload.type;
                    document.getElementById('edit-flat-rate').value = payload.flat_rate;
                    document.getElementById('edit-estimation').value = payload.estimation ?? '';
                    document.getElementById('edit-active').checked = payload.is_active;
                    openModal(editModal);
                });
            });

            document.querySelectorAll('[data-delete-shipping]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!deleteForm) return;
                    const payload = JSON.parse(button.dataset.deleteShipping);
                    deleteForm.action = `${baseUrl}/${payload.id}`;
                    if (deleteTarget) {
                        deleteTarget.textContent = payload.name;
                    }
                    openModal(deleteModal);
                });
            });

            bindSlugPair('create-name', 'create-slug');
            bindSlugPair('edit-name', 'edit-slug');
        });
    </script>
@endsection
