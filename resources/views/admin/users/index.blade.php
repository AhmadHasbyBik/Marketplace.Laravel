@extends('layouts.admin')

@section('title', 'Manajemen User')

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
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Users</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Kelola akun tim</h1>
                    <p class="text-sm text-slate-500">CRUD user via modal agar konsisten dengan UI kategori.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 pt-3 md:pt-0">
                    <form method="GET" class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-600 shadow-sm transition hover:border-slate-300">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama/email" class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs text-slate-600 focus:border-sky-400 focus:outline-none" />
                        <button type="submit" class="rounded-2xl bg-[#0EA5E9] px-3 py-1 text-white">Filter</button>
                    </form>
                    @component('admin.components.button', ['variant' => 'primary', 'attrs' => 'data-open-user-create'])
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Tambah User
                    @endcomponent
                </div>
            </div>
        </header>

        <section class="space-y-3">
            <div class="rounded-3xl border border-slate-200 bg-white/90 px-5 py-4 shadow-sm shadow-slate-100">
                <p class="text-sm text-slate-600">Konsistenkan role & status user dengan tombol modal yang sama seperti kategori.</p>
            </div>

            @component('admin.components.table', ['headers' => ['Nama', 'Email', 'Role', 'Status', 'Aksi']])
                @slot('body')
                    @forelse($users as $user)
                        @php
                            $payload = json_encode([
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'phone' => $user->phone,
                                'role_id' => optional($user->roles->first())->id,
                                'is_active' => (bool) $user->is_active,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        @endphp
                        <tr class="border-b border-slate-100 transition hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $user->phone ?? 'Telepon belum' }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ optional($user->roles->first())->name ?? 'Tanpa role' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor[$user->is_active] }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-user-edit data-user='{$payload}'"])
                                        Edit
                                    @endcomponent
                                    @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'class' => 'px-3', 'attrs' => "type='button' data-open-user-delete data-user='{$payload}'"])
                                        Hapus
                                    @endcomponent
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada user.</td>
                        </tr>
                    @endforelse
                @endslot
            @endcomponent

            <div class="flex justify-end">
                {{ $users->withQueryString()->links() }}
            </div>
        </section>
    </div>

    @component('admin.components.modal', ['id' => 'user-create-modal', 'title' => 'Tambah User', 'description' => 'Isi nama, email, peran, dan password.'])
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div class="grid gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Nama</label>
                    <input name="name" type="text" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Email</label>
                    <input name="email" type="email" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Telepon</label>
                    <input name="phone" type="tel" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Password</label>
                        <input name="password" type="password" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Role</label>
                        <select name="role_id" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600">
                            <option value="">Pilih role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0" />
                    <input id="create-active" type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-200 text-sky-500" />
                    <label for="create-active" class="text-xs font-semibold text-slate-600">Aktifkan akun</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-1">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'primary', 'size' => 'sm', 'type' => 'submit'])
                    Simpan user
                @endcomponent
            </div>
        </form>
    @endcomponent

    @component('admin.components.modal', ['id' => 'user-edit-modal', 'title' => 'Edit User', 'description' => 'Perbarui nama, email, peran, dan status.'])
        <form id="user-edit-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
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
                    <input id="edit-phone" name="phone" type="tel" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Password (opsional)</label>
                        <input id="edit-password" name="password" type="password" class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Role</label>
                        <select id="edit-role" name="role_id" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600">
                            <option value="">Pilih role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0" />
                    <input id="edit-active" type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-200 text-sky-500" />
                    <label for="edit-active" class="text-xs font-semibold text-slate-600">Aktifkan akun</label>
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

    @component('admin.components.modal', ['id' => 'user-delete-modal', 'title' => 'Hapus User', 'description' => 'User yang dihapus kehilangan akses permanen.'])
        <form id="user-delete-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600">Anda akan menghapus user <span class="font-semibold" id="user-delete-target"></span>.</p>
            <div class="flex justify-end gap-3 pt-2">
                @component('admin.components.button', ['variant' => 'secondary', 'size' => 'sm', 'attrs' => 'type="button" data-modal-close'])
                    Batal
                @endcomponent
                @component('admin.components.button', ['variant' => 'danger', 'size' => 'sm', 'type' => 'submit'])
                    Hapus akun
                @endcomponent
            </div>
        </form>
    @endcomponent

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createModal = document.getElementById('user-create-modal');
            const editModal = document.getElementById('user-edit-modal');
            const deleteModal = document.getElementById('user-delete-modal');
            const editForm = document.getElementById('user-edit-form');
            const deleteForm = document.getElementById('user-delete-form');
            const deleteTarget = document.getElementById('user-delete-target');
            const openCreateButtons = document.querySelectorAll('[data-open-user-create]');
            const openEditButtons = document.querySelectorAll('[data-open-user-edit]');
            const openDeleteButtons = document.querySelectorAll('[data-open-user-delete]');
            const modalCloseButtons = document.querySelectorAll('[data-modal-close]');
            const baseIndex = '{{ route('admin.users.index') }}';

            const openModal = (element) => element?.classList.remove('hidden');
            const closeModal = (element) => element?.classList.add('hidden');

            openCreateButtons.forEach((button) => button.addEventListener('click', () => openModal(createModal)));

            openEditButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const payload = JSON.parse(button.dataset.user);
                    if (!editForm) return;
                    editForm.action = `${baseIndex}/${payload.id}`;
                    document.getElementById('edit-name').value = payload.name;
                    document.getElementById('edit-email').value = payload.email;
                    document.getElementById('edit-phone').value = payload.phone ?? '';
                    document.getElementById('edit-role').value = payload.role_id ?? '';
                    document.getElementById('edit-active').checked = payload.is_active;
                    openModal(editModal);
                });
            });

            openDeleteButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const payload = JSON.parse(button.dataset.user);
                    if (!deleteForm) return;
                    deleteForm.action = `${baseIndex}/${payload.id}`;
                    deleteTarget.textContent = payload.name;
                    openModal(deleteModal);
                });
            });

            modalCloseButtons.forEach((button) => button.addEventListener('click', () => closeModal(button.closest('[data-modal]'))));
        });
    </script>
@endsection
