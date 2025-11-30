@extends('layouts.admin')

@section('title', 'Review Produk')

@section('content')
    @php
        use Illuminate\Support\Str;
        $statusOptions = ['pending', 'approved', 'rejected'];
        $statusColor = [
            'pending' => 'bg-amber-100 text-amber-700',
            'approved' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-rose-100 text-rose-700',
        ];
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Ulasan</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Pantau review pelanggan</h1>
                    <p class="text-sm text-slate-500">Filter status, cari produk, dan kelola review via modal.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    <form method="GET" class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm transition hover:border-slate-300">
                        <label for="status-filter" class="text-[10px] font-semibold tracking-[0.35em] text-slate-400">Status</label>
                        <select id="status-filter" name="status" class="rounded-2xl border border-slate-200 bg-white px-3 py-1 text-xs text-slate-600 focus:border-sky-400 focus:outline-none">
                            <option value="">Semua</option>
                            @foreach($statusOptions as $statusValue)
                                <option value="{{ $statusValue }}" {{ ($status ?? '') === $statusValue ? 'selected' : '' }}>{{ ucfirst($statusValue) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-2xl bg-[#0EA5E9] px-3 py-1 text-white">Filter</button>
                    </form>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari produk atau komentar" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21 21-4.34-4.34" />
                            <circle cx="11" cy="11" r="8" />
                        </svg>
                    </span>
                </div>
            </div>
        </header>

        <section class="grid gap-4 lg:grid-cols-3">
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Total Ulasan</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $reviews->total() }}</p>
                <p class="text-sm text-slate-500">{{ $status ? 'Menampilkan ' . ucfirst($status) . ' saja' : 'Semua status ditampilkan' }}</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Ulasan Bulan Ini</p>
                <p class="text-xl font-semibold text-slate-900">{{ $reviews->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                <p class="text-sm text-slate-500">Tuntaskan agar rating tetap positif.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Review Pending</p>
                <p class="text-xl font-semibold text-slate-900">{{ $reviews->where('is_approved', false)->where('is_rejected', false)->count() }}</p>
                <p class="text-sm text-slate-500">Selesaikan dalam modal di bawah.</p>
            </article>
        </section>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Daftar review</p>
                        <h2 class="text-lg font-semibold text-slate-900">Komentar terbaru</h2>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['Produk', 'Pengulas', 'Rating', 'Komentar', 'Status', 'Aksi']])
                @slot('body')
                    @forelse($reviews as $review)
                        @php
                            $currentStatus = $review->is_approved ? 'approved' : ($review->is_rejected ? 'rejected' : 'pending');
                            $payload = json_encode([
                                'id' => $review->id,
                                'review' => $review->comment,
                                'status' => $currentStatus,
                                'product' => $review->product?->name,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $review->product?->name ?? 'Produk hilang' }}</p>
                                <p class="text-xs text-slate-400">{{ $review->product?->sku ? 'SKU: ' . $review->product->sku : 'Tanpa SKU' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $review->user?->name ?? 'Pelanggan tamu' }}</p>
                                <p class="text-xs text-slate-400">{{ $review->user?->email ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="flex items-center gap-1 text-sm font-semibold">
                                    <span class="text-amber-400">{{ str_repeat('★', $review->rating) }}</span>
                                    {{ $review->rating }}/5
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <p class="text-sm text-slate-700">{{ Str::limit($review->comment, 90) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor[$currentStatus] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($currentStatus) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-review-update data-action='approve' data-review='{$payload}'"])
                                        Setujui
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-review-update data-action='reject' data-review='{$payload}'"])
                                        Tolak
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-delete-review='{$payload}'"])
                                        Hapus
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada review.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent

            <div class="flex justify-end">
                {{ $reviews->withQueryString()->links() }}
            </div>
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'review-update-modal', 'title' => 'Perbarui Status Ulasan', 'description' => 'Tentukan apakah ulasan disetujui atau ditolak.'])
        <form id="review-update-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <input id="review-update-action" type="hidden" name="action" value="approve" />
            <p class="text-sm text-slate-600">Anda akan <span class="font-semibold" id="review-update-direction">menyetujui</span> ulasan dari <span class="font-semibold" id="review-update-product"></span>.</p>
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan internal (opsional)</label>
                <textarea name="notes" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Terapkan
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'review-delete-modal', 'title' => 'Hapus Ulasan', 'description' => 'Ulasan yang dihapus tidak bisa dikembalikan.'])
        <form id="review-delete-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600">Kamu akan menghapus ulasan dari <span class="font-semibold" id="review-delete-target"></span>.</p>
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
            const updateModal = document.getElementById('review-update-modal');
            const deleteModal = document.getElementById('review-delete-modal');
            const updateForm = document.getElementById('review-update-form');
            const deleteForm = document.getElementById('review-delete-form');
            const updateActionInput = document.getElementById('review-update-action');
            const updateDirection = document.getElementById('review-update-direction');
            const updateProduct = document.getElementById('review-update-product');
            const deleteTarget = document.getElementById('review-delete-target');
            const baseUrl = '{{ url('/admin/reviews') }}';

            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');

            document.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.closest('[data-modal]')));
            });

            document.querySelectorAll('[data-open-review-update]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!updateForm) return;
                    const payload = JSON.parse(button.dataset.review);
                    const action = button.dataset.action;
                    updateForm.action = `${baseUrl}/${payload.id}`;
                    updateActionInput.value = action;
                    updateDirection.textContent = action === 'reject' ? 'menolak' : 'menyetujui';
                    updateProduct.textContent = payload.product ?? 'produk ini';
                    openModal(updateModal);
                });
            });

            document.querySelectorAll('[data-delete-review]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!deleteForm) return;
                    const payload = JSON.parse(button.dataset.deleteReview);
                    deleteForm.action = `${baseUrl}/${payload.id}`;
                    if (deleteTarget) {
                        deleteTarget.textContent = payload.product ?? 'produk ini';
                    }
                    openModal(deleteModal);
                });
            });
        });
    </script>
@endsection
