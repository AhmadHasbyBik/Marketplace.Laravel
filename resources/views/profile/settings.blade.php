@extends('layouts.admin')

@section('title', 'Pengaturan Akun')

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Pengaturan</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Kontrol preferensi {{ $user?->name ?? 'Pengguna' }}</h1>
                    <p class="text-sm text-slate-500">Semua opsi tampilan dan notifikasi dikumpulkan di sini agar tetap konsisten dengan dashboard admin.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600">
                    Last login {{ optional($user?->last_login_at)->format('d M Y') ?? 'belum tercatat' }}
                </div>
            </div>
        </header>

{{--
        <section class="grid gap-6 lg:grid-cols-3">
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-100">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Preferensi tampilan</p>
                    <h2 class="text-lg font-semibold text-slate-900">Bahasa & tema</h2>
                    <p class="text-sm text-slate-500">Sesuaikan pengalaman admin dengan bahasa dan mode warna.</p>
                </div>
                <div class="mt-5 space-y-4 text-sm text-slate-600">
                    <p class="flex items-center justify-between">
                        <span>Bahasa antarmuka</span>
                        <span class="text-slate-900 font-semibold">Bahasa Indonesia</span>
                    </p>
                    <p class="flex items-center justify-between">
                        <span>Mode gelap</span>
                        <button class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-900">Aktifkan</button>
                    </p>
                    <p class="flex items-center justify-between">
                        <span>Mode fokus</span>
                        <span class="text-emerald-600 font-semibold">Otamatik</span>
                    </p>
                </div>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-100">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Notifikasi</p>
                    <h2 class="text-lg font-semibold text-slate-900">Alur pemberitahuan</h2>
                    <p class="text-sm text-slate-500">Kelola channel notifikasi tanpa meninggalkan dashboard.</p>
                </div>
                <div class="mt-5 space-y-4">
                    @foreach([
                        'Email untuk order baru' => true,
                        'SMS untuk stok kritis' => false,
                        'Pengingat meeting harian' => true,
                    ] as $label => $checked)
                        <label class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            <span>{{ $label }}</span>
                            <input type="checkbox" {{ $checked ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-sky-600" />
                        </label>
                    @endforeach
                    <button type="button" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">Kelola saluran lain</button>
                </div>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-100">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Integrasi</p>
                    <h2 class="text-lg font-semibold text-slate-900">Akses eksternal</h2>
                    <p class="text-sm text-slate-500">Pantau aplikasi yang terhubung ke akun Anda.</p>
                </div>
                <div class="mt-5 space-y-4 text-sm text-slate-600">
                    <p class="flex items-center justify-between">
                        <span>API Token</span>
                        <span class="text-emerald-600 font-semibold">Aktif</span>
                    </p>
                    <p class="flex items-center justify-between">
                        <span>Integrasi POS</span>
                        <span class="text-slate-500">Menunggu</span>
                    </p>
                    <p class="flex items-center justify-between">
                        <span>Notifikasi via Slack</span>
                        <button class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-900">Atur</button>
                    </p>
                </div>
            </article>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-100">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Audit</p>
                    <h2 class="text-lg font-semibold text-slate-900">Status keamanan</h2>
                </div>
                <dl class="mt-5 space-y-4 text-sm text-slate-600">
                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <dt>Password terakhir diubah</dt>
                        <dd class="text-slate-900 font-semibold">{{ optional($user?->password_updated_at)->format('d M Y') ?? 'Belum pernah' }}</dd>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <dt>Akses login aktif</dt>
                        <dd class="text-emerald-600 font-semibold">2 perangkat</dd>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <dt>Autentikasi dua faktor</dt>
                        <dd class="text-sky-600 font-semibold">Belum diaktifkan</dd>
                    </div>
                </dl>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-100">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Catatan</p>
                    <h2 class="text-lg font-semibold text-slate-900">Pembaruan sistem</h2>
                    <p class="text-sm text-slate-500">Tim Cupid akan mengumumkan perubahan fitur di sini.</p>
                </div>
                <div class="mt-5 space-y-4 text-sm text-slate-600">
                    <div class="rounded-2xl border border-slate-100 bg-amber-50 px-4 py-3 text-amber-700">
                        Perubahan logging akan diterapkan mulai 3 hari ke depan.
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-slate-600">
                        Opsi export data tersedia di halaman laporan admin.
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3 text-slate-600">
                        Jangan bagikan token API di luar organisasi.
                    </div>
                </div>
            </article>
        </section>
        --}}
    </div>
@endsection
