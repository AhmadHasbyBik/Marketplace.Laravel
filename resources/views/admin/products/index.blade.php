@extends('layouts.admin')

@section('title', 'Produk')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Produk</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Kelola katalog utama</h1>
                    <p class="text-sm text-slate-500">Semua aksi (create/edit/delete) terjadi via modal agar tetap berada di
                        satu layar.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    <button type="button"
                        class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm transition hover:border-slate-300 hover:text-slate-900">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 5H3" />
                            <path d="M12 19H3" />
                            <path d="M14 3v4" />
                            <path d="M16 17v4" />
                            <path d="M21 12h-9" />
                            <path d="M21 19h-5" />
                            <path d="M21 5h-7" />
                            <path d="M8 10v4" />
                            <path d="M8 12H3" />
                        </svg>
                        Filter produk
                    </button>
                    @component('admin.components.button', ['variant' => 'primary', 'attrs' => 'data-open-product-create'])
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Tambah Produk
                    @endcomponent
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <input type="search" placeholder="Cari produk"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21 21-4.34-4.34" />
                            <circle cx="11" cy="11" r="8" />
                        </svg>
                    </span>
                </div>
            </div>
        </header>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-sm text-slate-600">Gunakan pencarian, filter, dan tombol aksi untuk menambahkan produk baru
                    tanpa meninggalkan halaman ini.</p>
            </div>

            @component('admin.components.table', [
                'headers' => ['Nama', 'Kategori', 'Harga', 'Stok', 'Status', 'Unggulan', 'Aksi'],
            ])
                @slot('body')
                    @forelse($products as $product)
                        @php
                            $payload = json_encode(
                                [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'slug' => $product->slug,
                                    'price' => $product->price,
                                    'stock' => $product->stock,
                                    'stock_minimum' => $product->stock_minimum,
                                    'category' => $product->category_id,
                                    'weight' => $product->weight ?? 0,
                                    'short_description' => $product->short_description,
                                    'is_active' => (bool) $product->is_active,
                                    'is_featured' => (bool) $product->is_featured,
                                ],
                                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP,
                            );
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 overflow-hidden rounded-2xl bg-slate-50">
                                        <img src="{{ $product->images->first()?->url ?? asset('images/product-placeholder.svg') }}"
                                            alt="{{ $product->name }}" class="h-full w-full object-cover" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                        <p class="text-xs text-slate-400">{{ Str::limit($product->short_description, 70) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $product->category?->name ?? 'Belum terkait' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $product->stock }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-2">

                                    {{-- Badge Status Featured --}}
                                    <span
                                        class="inline-flex items-center justify-center text-[11px] font-semibold px-2.5 py-1 rounded-xl shadow-sm
            transition
            {{ $product->is_featured
                ? 'bg-amber-50 text-amber-700 border border-amber-200'
                : 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                                        {{ $product->is_featured ? 'Unggulan' : 'Biasa' }}
                                    </span>

                                    {{-- Toggle Button --}}
                                    <form method="POST" action="{{ route('admin.products.toggleFeatured', $product) }}"
                                        class="text-left">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                            class="w-full text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200
                border
                {{ $product->is_featured
                    ? 'text-amber-700 border-amber-200 bg-amber-50 hover:bg-amber-100 hover:border-amber-300'
                    : 'text-slate-600 border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300' }}">
                                            {{ $product->is_featured ? 'Nonaktifkan unggulan' : 'Jadikan unggulan' }}
                                        </button>
                                    </form>

                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', [
                                        'variant' => 'secondary',
                                        'size' => 'sm',
                                        'class' => 'px-3',
                                        'attrs' => "type='button' data-edit-product='{$payload}'",
                                    ])
                                        Edit
                                    @endcomponent
                                    @component('admin.components.button', [
                                        'variant' => 'danger',
                                        'size' => 'sm',
                                        'class' => 'px-3',
                                        'attrs' => "type='button' data-delete-product='{$payload}'",
                                    ])
                                        Hapus
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada produk.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent

            <div class="flex justify-end">
                {{ $products->links() }}
            </div>
        </section>
    </div>

    @component('admin.components.modal', [
        'id' => 'product-create-modal',
        'title' => 'Tambah Produk',
        'description' => 'Isi nama, kategori, harga, dan stok untuk membuat produk baru.',
    ])
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama</label>
                    <input id="create-name" name="name" type="text" required
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Slug</label>
                    <input id="create-slug" name="slug" type="text" required readonly
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Deskripsi singkat</label>
                    <textarea name="short_description" rows="3"
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                </div>
                <div class="grid gap-3 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Harga</label>
                        <input name="price" type="number" min="0"
                            class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Stok</label>
                        <input name="stock" type="number" min="0"
                            class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Berat (gram)</label>
                        <input name="weight" type="number" min="0" placeholder="1000"
                            class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Minimum stok</label>
                    <input name="stock_minimum" type="number" min="0"
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Kategori</label>
                    <select name="category_id" required
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Gambar produk</label>
                    <input type="file" name="images[]" accept="image/*" multiple
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    <p class="text-xs text-slate-400 mt-1">Unggah hingga 4 gambar (maks 2MB per file).</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0" />
                    <input type="checkbox" id="create-active" name="is_active" value="1" checked
                        class="h-4 w-4 rounded border-slate-200 text-sky-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-sky-300" />
                    <label for="create-active" class="text-xs font-semibold text-slate-600">Aktifkan produk</label>
                </div>
                <div class="flex flex-col gap-1">
                    <input type="hidden" name="is_featured" value="0" />
                    <div class="flex items-center gap-2">
                        <input id="create-featured" type="checkbox" name="is_featured" value="1"
                            class="h-4 w-4 rounded border-slate-200 text-sky-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-sky-300" />
                        <label for="create-featured" class="text-xs font-semibold text-slate-600">Tandai produk sebagai
                            unggulan</label>
                    </div>
                    <p class="text-xs text-slate-400">Produk unggulan akan ditampilkan di laman depan.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', [
                    'variant' => 'secondary',
                    'size' => 'sm',
                    'attrs' => 'type="button" data-modal-close',
                ])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan produk
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', [
        'id' => 'product-edit-modal',
        'title' => 'Edit Produk',
        'description' => 'Perbarui nama, harga, stok, atau status aktif.',
    ])
        <form id="product-edit-form" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama</label>
                    <input id="edit-name" name="name" type="text" required
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Slug</label>
                    <input id="edit-slug" name="slug" type="text" required readonly
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Deskripsi singkat</label>
                    <textarea id="edit-short-description" name="short_description" rows="3"
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                </div>
                <div class="grid gap-3 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Harga</label>
                        <input id="edit-price" name="price" type="number" min="0"
                            class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Stok</label>
                        <input id="edit-stock" name="stock" type="number" min="0"
                            class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Berat (gram)</label>
                        <input id="edit-weight" name="weight" type="number" min="0" placeholder="1000"
                            class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Minimum stok</label>
                    <input id="edit-stock-minimum" name="stock_minimum" type="number" min="0"
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Kategori</label>
                    <select id="edit-category" name="category_id" required
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Tambahkan gambar</label>
                    <input type="file" name="images[]" accept="image/*" multiple
                        class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100" />
                    <p class="text-xs text-slate-400 mt-1">Gambar baru akan ditambahkan ke galeri tanpa menghapus yang lama.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0" />
                    <input id="edit-active" type="checkbox" name="is_active" value="1"
                        class="h-4 w-4 rounded border-slate-200 text-sky-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-sky-300" />
                    <label for="edit-active" class="text-xs font-semibold text-slate-600">Aktifkan produk</label>
                </div>
                <div class="flex flex-col gap-1">
                    <input type="hidden" name="is_featured" value="0" />
                    <div class="flex items-center gap-2">
                        <input id="edit-featured" type="checkbox" name="is_featured" value="1"
                            class="h-4 w-4 rounded border-slate-200 text-sky-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-sky-300" />
                        <label for="edit-featured" class="text-xs font-semibold text-slate-600">Tandai produk sebagai
                            unggulan</label>
                    </div>
                    <p class="text-xs text-slate-400">Produk unggulan akan muncul di hero landing page.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', [
                    'variant' => 'secondary',
                    'size' => 'sm',
                    'attrs' => 'type="button" data-modal-close',
                ])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan perubahan
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', [
        'id' => 'product-delete-modal',
        'title' => 'Hapus Produk',
        'description' => 'Produk akan dihapus secara permanen.',
    ])
        <form id="product-delete-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600">Apakah Anda yakin ingin menghapus produk berikut dari katalog?</p>
            <div class="text-sm font-semibold text-slate-900" id="product-delete-target"></div>
            <div class="flex justify-end gap-3 pt-2">
                @component('admin.components.button', [
                    'variant' => 'secondary',
                    'size' => 'sm',
                    'attrs' => 'type="button" data-modal-close',
                ])
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
            const createModal = document.getElementById('product-create-modal');
            const editModal = document.getElementById('product-edit-modal');
            const deleteModal = document.getElementById('product-delete-modal');
            const editForm = document.getElementById('product-edit-form');
            const deleteForm = document.getElementById('product-delete-form');
            const deleteTarget = document.getElementById('product-delete-target');
            const baseUrl = '{{ url('/admin/products') }}';

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

            document.querySelector('[data-open-product-create]')?.addEventListener('click', () => openModal(
                createModal));

            document.querySelectorAll('[data-edit-product]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!editForm) return;
                    const payload = JSON.parse(button.dataset.editProduct);
                    editForm.action = `${baseUrl}/${payload.id}`;
                    document.getElementById('edit-name').value = payload.name;
                    document.getElementById('edit-slug').value = payload.slug;
                    document.getElementById('edit-short-description').value = payload
                        .short_description ?? '';
                    document.getElementById('edit-price').value = payload.price;
                    document.getElementById('edit-stock').value = payload.stock;
                    document.getElementById('edit-weight').value = payload.weight ?? '';
                    document.getElementById('edit-stock-minimum').value = payload.stock_minimum ??
                        '';
                    document.getElementById('edit-category').value = payload.category ?? '';
                    document.getElementById('edit-active').checked = payload.is_active;
                    document.getElementById('edit-featured').checked = payload.is_featured;
                    openModal(editModal);
                });
            });

            document.querySelectorAll('[data-delete-product]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!deleteForm) return;
                    const payload = JSON.parse(button.dataset.deleteProduct);
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
