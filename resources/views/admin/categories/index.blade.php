@extends('layouts.admin')

@section('title', 'Kategori Produk')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Kategori</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Kelola struktur katalog</h1>
                    <p class="text-sm text-slate-500">Gunakan panel ini untuk menciptakan kategori baru, memperbarui slug, dan memastikan statusnya aktif secara cepat.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    <div class="flex items-center gap-2">
                        <label for="category-status" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Status</label>
                        <select id="category-status" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                            <option value="">All</option>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    @component('admin.components.button', ['variant' => 'primary', 'attrs' => 'data-open-category-create'])
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Tambah Kategori
                    @endcomponent
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <input type="search" placeholder="Cari kategori" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21 21-4.34-4.34" />
                            <circle cx="11" cy="11" r="8" />
                        </svg>
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Urutan</span>
                    <select class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200">
                        <option value="latest">Terbaru</option>
                        <option value="name">Nama A - Z</option>
                        <option value="order">Urutan manual</option>
                    </select>
                </div>
            </div>
        </header>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-600">Panel ini menggunakan modal untuk semua aksi CRUD sehingga Anda tetap berada di satu layar.</p>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['Kategori', 'Slug', 'Urutan', 'Status', 'Diperbarui', 'Aksi']])
                @slot('body')
                    @forelse($categories as $category)
                        @php
                            $payload = json_encode([
                                'id' => $category->id,
                                'name' => $category->name,
                                'slug' => $category->slug,
                                'description' => $category->description,
                                'order' => $category->order,
                                'is_active' => (bool) $category->is_active,
                                'image_url' => $category->image_url,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-100 bg-slate-100">
                                        @if($category->image_url)
                                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-full w-full object-cover" />
                                        @else
                                            <span class="text-[10px] font-semibold uppercase tracking-[0.4em] text-slate-400">No image</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $category->name }}</p>
                                        <p class="text-xs text-slate-400">{{ Str::limit($category->description, 80) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $category->slug }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $category->order ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $category->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $category->updated_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "data-edit-category='{$payload}'"]) 
                                        Edit
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "data-delete-category='{$payload}'"]) 
                                        Hapus
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'category-create-modal', 'title' => 'Tambah Kategori', 'description' => 'Lengkapi data kategori baru dan simpan dalam detik.'])
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            <div class="grid gap-3">
                <div>
                    <label for="create-name" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama</label>
                    <input id="create-name" name="name" type="text" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label for="create-slug" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Slug</label>
                    <input id="create-slug" name="slug" type="text" required readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label for="create-description" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Deskripsi</label>
                    <textarea id="create-description" name="description" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                </div>
                <div>
                    <label for="create-image" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Gambar</label>
                    <div class="mt-2 h-32 w-full overflow-hidden rounded-2xl bg-slate-100">
                        <img id="create-image-preview" class="h-full w-full object-cover hidden" alt="Pratinjau gambar" />
                        <div id="create-image-placeholder" class="flex h-full w-full items-center justify-center text-xs uppercase tracking-[0.3em] text-slate-400">
                            Belum ada gambar
                        </div>
                    </div>
                    <input id="create-image" name="image" type="file" accept="image/*" class="mt-3 w-full text-sm text-slate-500" />
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <label for="create-order" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Urutan</label>
                        <input id="create-order" name="order" type="number" min="0" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                    <div class="flex items-start gap-2">
                        <input type="hidden" name="is_active" value="0" />
                        <input type="checkbox" id="create-active" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-200 text-sky-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-sky-300" />
                        <label for="create-active" class="text-xs font-semibold text-slate-600">Aktifkan</label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'category-edit-modal', 'title' => 'Edit Kategori', 'description' => 'Perbarui slug, status, dan urutan.'])
        <form id="category-edit-form" method="POST" action="" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid gap-3">
                <div>
                    <label for="edit-name" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama</label>
                    <input id="edit-name" name="name" type="text" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label for="edit-slug" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Slug</label>
                    <input id="edit-slug" name="slug" type="text" required readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label for="edit-description" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Deskripsi</label>
                    <textarea id="edit-description" name="description" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                </div>
                <div>
                    <label for="edit-image" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Gambar</label>
                    <div class="mt-2 h-32 w-full overflow-hidden rounded-2xl bg-slate-100">
                        <img id="edit-image-preview" class="h-full w-full object-cover hidden" alt="Pratinjau gambar" />
                        <div id="edit-image-placeholder" class="flex h-full w-full items-center justify-center text-xs uppercase tracking-[0.3em] text-slate-400">
                            Belum ada gambar
                        </div>
                    </div>
                    <input id="edit-image" name="image" type="file" accept="image/*" class="mt-3 w-full text-sm text-slate-500" />
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <label for="edit-order" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Urutan</label>
                        <input id="edit-order" name="order" type="number" min="0" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                    <div class="flex items-start gap-2">
                        <input type="hidden" name="is_active" value="0" />
                        <input type="checkbox" id="edit-active" name="is_active" value="1" class="h-4 w-4 rounded border-slate-200 text-sky-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-sky-300" />
                        <label for="edit-active" class="text-xs font-semibold text-slate-600">Aktifkan</label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan Perubahan
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'category-delete-modal', 'title' => 'Hapus Kategori', 'description' => 'Tindakan ini tidak dapat dibatalkan.'])
        <form id="category-delete-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600">Apakah Anda yakin ingin menghapus kategori berikut? Semua produk yang terkait akan tetap berada di dalam sistem, tetapi tag kategori akan dilepas.</p>
            <div class="text-sm font-semibold text-slate-900" id="category-delete-target"></div>
            <div class="flex justify-end gap-3 pt-2">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm'])
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
            const createModal = document.getElementById('category-create-modal');
            const editModal = document.getElementById('category-edit-modal');
            const deleteModal = document.getElementById('category-delete-modal');
            const editForm = document.getElementById('category-edit-form');
            const deleteForm = document.getElementById('category-delete-form');
            const deleteTarget = document.getElementById('category-delete-target');
            const baseUrl = '{{ url('/admin/categories') }}';

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
                const update = () => {
                    target.value = slugifyValue(source.value);
                };
                source.addEventListener('input', update);
                update();
            };

            const prepareImagePreview = (inputId, previewId, placeholderId) => {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);

                const setPreview = (source) => {
                    if (!preview || !placeholder) return;

                    if (source) {
                        preview.src = source;
                        preview.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    } else {
                        preview.classList.add('hidden');
                        placeholder.classList.remove('hidden');
                    }
                };

                if (input) {
                    input.addEventListener('change', () => {
                        const file = input.files?.[0];
                        if (!file) {
                            setPreview(null);
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = (event) => setPreview(event.target?.result ?? null);
                        reader.readAsDataURL(file);
                    });
                }

                return setPreview;
            };

            const updateCreateImagePreview = prepareImagePreview('create-image', 'create-image-preview', 'create-image-placeholder');
            const updateEditImagePreview = prepareImagePreview('edit-image', 'edit-image-preview', 'edit-image-placeholder');
            updateCreateImagePreview(null);
            updateEditImagePreview(null);

            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');
            const createImageInput = document.getElementById('create-image');
            const editImageInput = document.getElementById('edit-image');

            document.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.closest('[data-modal]')));
            });

            document.querySelector('[data-open-category-create]')?.addEventListener('click', () => {
                if (createImageInput) {
                    createImageInput.value = '';
                }
                updateCreateImagePreview(null);
                openModal(createModal);
            });

            document.querySelectorAll('[data-edit-category]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!editForm) return;
                    const payload = JSON.parse(button.dataset.editCategory);
                    editForm.action = `${baseUrl}/${payload.id}`;
                    document.getElementById('edit-name').value = payload.name;
                    document.getElementById('edit-slug').value = payload.slug;
                    document.getElementById('edit-description').value = payload.description ?? '';
                    document.getElementById('edit-order').value = payload.order ?? '';
                    document.getElementById('edit-active').checked = payload.is_active;
                    updateEditImagePreview(payload.image_url ?? null);
                    if (editImageInput) {
                        editImageInput.value = '';
                    }
                    openModal(editModal);
                });
            });

            document.querySelectorAll('[data-delete-category]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!deleteForm) return;
                    const payload = JSON.parse(button.dataset.deleteCategory);
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
