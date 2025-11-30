@extends('layouts.admin')

@section('title', 'Daftar Order')

@section('content')
    @php
        use App\Models\Order;
        $statusOptions = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];
        $statusColorMap = [
            'pending' => 'bg-amber-100 text-amber-700',
            'paid' => 'bg-emerald-100 text-emerald-700',
            'processing' => 'bg-sky-100 text-sky-600',
            'shipped' => 'bg-indigo-100 text-indigo-700',
            'completed' => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-rose-100 text-rose-700',
        ];
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Pesanan</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Pantau seluruh order</h1>
                    <p class="text-sm text-slate-500">Filter status, cari nomor order, dan kelola status melalui modal agar tetap di satu layar.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    <form method="GET" class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm transition hover:border-slate-300">
                        <div class="flex items-center gap-2">
                            <label for="status-filter" class="text-[10px] font-semibold tracking-[0.35em] text-slate-400">Status</label>
                            <select id="status-filter" name="status" class="rounded-2xl border border-slate-200 bg-white px-3 py-1 text-xs text-slate-600 focus:border-sky-400 focus:outline-none">
                                <option value="">Semua</option>
                                @foreach($statusOptions as $statusValue)
                                    <option value="{{ $statusValue }}" {{ $status === $statusValue ? 'selected' : '' }}>{{ ucfirst($statusValue) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative flex-1 min-w-[220px]">
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nomor order" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-1 text-xs text-slate-600 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none" />
                        </div>
                        <button type="submit" class="rounded-2xl bg-[#0EA5E9] px-3 py-1 text-white">Filter</button>
                    </form>
                </div>
            </div>
        </header>

        <section class="grid gap-4 lg:grid-cols-3">
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Total Pesanan</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $orders->total() }}</p>
                <p class="text-sm text-slate-500">{{ $status ? 'Filter ' . ucfirst($status) . ' aktif' : 'Menampilkan semua status pesanan' }}</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Pesanan terakhir</p>
                <p class="text-xl font-semibold text-slate-900">{{ $orders->first()?->order_number ?? 'Belum ada' }}</p>
                <p class="text-sm text-slate-500">{{ $orders->first()?->created_at?->diffForHumans() ?? 'Menunggu data' }}</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Aktivitas user</p>
                <p class="text-xl font-semibold text-slate-900">{{ $orders->where('is_paid', true)->count() }} terbayar</p>
                <p class="text-sm text-slate-500">Segera tangani pesanan pending agar terbayar cepat.</p>
            </article>
        </section>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <div class="flex flex-wrap items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Daftar pesanan</p>
                        <h2 class="text-lg font-semibold text-slate-900">Data pesanan terbaru</h2>
                    </div>
                </div>
            </div>

            @component('admin.components.table', ['headers' => ['No Order', 'Tipe', 'Pelanggan', 'Total', 'Status', 'Bukti', 'Pengiriman', 'Tanggal', 'Aksi']])
                @slot('body')
                    @forelse($orders as $order)
                        @php
                            $payload = json_encode([
                                'id' => $order->id,
                                'status' => $order->status,
                                'order_number' => $order->order_number,
                                'notes' => $order->notes,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-[0.65rem] font-semibold {{ $order->order_type === Order::TYPE_KASIR ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $order->order_type === Order::TYPE_KASIR ? 'Kasir' : 'Customer' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p>{{ $order->user?->name ?? 'Pelanggan tamu' }}</p>
                                <p class="text-xs text-slate-400">{{ $order->address?->city ?? 'Alamat belum' }}</p>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColorMap[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($order->payment_proof_path)
                                    <button
                                        type="button"
                                        data-proof-url="{{ $order->payment_proof_url }}"
                                        data-proof-order="{{ $order->order_number }}"
                                        data-proof-trigger
                                        class="inline-flex items-center gap-1 rounded-2xl border border-emerald-200 bg-emerald-50 px-3 py-1 text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-emerald-600 transition hover:border-emerald-300 hover:bg-emerald-100"
                                    >
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                        Bukti
                                    </button>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[0.55rem] font-semibold uppercase tracking-[0.3em] text-slate-500">
                                        Belum
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $order->shippingMethod?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-edit-order='{$payload}'"])
                                        Update Status
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-delete-order='{$payload}'"])
                                        Hapus
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada order.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent

            <div class="flex justify-end">
                {{ $orders->withQueryString()->links() }}
            </div>
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'order-update-modal', 'title' => 'Perbarui Status Pesanan', 'description' => 'Tentukan status terbaru dan catatan singkat untuk customer.'])
        <form id="order-update-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_paid" value="1" />
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Status</label>
                <select id="order-status" name="status" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
                    @foreach($statusOptions as $statusOption)
                        <option value="{{ $statusOption }}">{{ ucfirst($statusOption) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catatan</label>
                <textarea id="order-notes" name="notes" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan Status
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'order-delete-modal', 'title' => 'Hapus Pesanan', 'description' => 'Pesanan yang dihapus tidak bisa dikembalikan.'])
        <form id="order-delete-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600">Kamu akan menghapus pesanan <span class="font-semibold" id="order-delete-target"></span>.</p>
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

    @component('admin.components.modal', ['id' => 'order-proof-modal', 'title' => 'Bukti Pembayaran', 'description' => 'Periksa bukti transfer untuk memvalidasi pembayaran.'])
        <div class="space-y-3 text-sm text-slate-600">
            <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Order <span class="font-semibold text-slate-900" id="order-proof-number">-</span></p>
            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3">
                <img id="order-proof-image" src="" alt="Bukti pembayaran" class="h-64 w-full rounded-2xl object-contain shadow-sm shadow-slate-200" loading="lazy" />
            </div>
            <p class="text-xs text-slate-500">Jika gambar terlalu kecil, klik kanan lalu pilih &ldquo;Buka di tab baru&rdquo; untuk memperbesar.</p>
        </div>
    @endcomponent

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const updateModal = document.getElementById('order-update-modal');
            const deleteModal = document.getElementById('order-delete-modal');
            const updateForm = document.getElementById('order-update-form');
            const deleteForm = document.getElementById('order-delete-form');
            const deleteTarget = document.getElementById('order-delete-target');
            const baseUrl = '{{ url('/admin/orders') }}';

            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');

            document.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.closest('[data-modal]')));
            });

            document.querySelectorAll('[data-edit-order]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!updateForm) return;
                    const payload = JSON.parse(button.dataset.editOrder);
                    updateForm.action = `${baseUrl}/${payload.id}`;
                    document.getElementById('order-status').value = payload.status;
                    document.getElementById('order-notes').value = payload.notes ?? '';
                    openModal(updateModal);
                });
            });

            document.querySelectorAll('[data-delete-order]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!deleteForm) return;
                    const payload = JSON.parse(button.dataset.deleteOrder);
                    deleteForm.action = `${baseUrl}/${payload.id}`;
                    if (deleteTarget) {
                        deleteTarget.textContent = payload.order_number;
                    }
                    openModal(deleteModal);
                });
            });

            const proofModal = document.getElementById('order-proof-modal');
            const proofImage = document.getElementById('order-proof-image');
            const proofOrderLabel = document.getElementById('order-proof-number');

            document.querySelectorAll('[data-proof-trigger]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!proofModal || !proofImage) return;
                    proofImage.src = button.dataset.proofUrl ?? '';
                    proofOrderLabel.textContent = button.dataset.proofOrder ?? '-';
                    openModal(proofModal);
                });
            });
        });
    </script>
@endsection
