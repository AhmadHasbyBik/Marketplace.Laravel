@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-white/80 px-6 py-6 shadow-sm shadow-slate-100">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Profil</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Kelola akun {{ $user?->name ?? 'Admin' }}</h1>
                    <p class="text-sm text-slate-500">Sesuai dengan gaya dashboard admin agar manajemen user tetap konsisten.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600">
                    Terdaftar sejak {{ optional($user?->created_at)->format('d M Y') ?? '—' }}
                </div>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-100">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Akun</p>
                    <h2 class="text-xl font-semibold text-slate-900">Informasi profil</h2>
                    <p class="text-sm text-slate-500">Perbarui nama dan email agar bagian admin tetap menampilkan data terbaru.</p>
                </div>
                <div class="mt-5">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-100">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Keamanan</p>
                    <h2 class="text-xl font-semibold text-slate-900">Kata sandi</h2>
                    <p class="text-sm text-slate-500">Jaga akses dashboard dengan mengganti password secara berkala.</p>
                </div>
                <div class="mt-5">
                    @include('profile.partials.update-password-form')
                </div>
            </article>
        </section>

        <section>
            <article class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-100">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Keamanan</p>
                    <h2 class="text-xl font-semibold text-slate-900">Hapus akun</h2>
                    <p class="text-sm text-slate-500">Aksi ini bersifat permanen. Pastikan sudah mencadangkan data yang penting sebelumnya.</p>
                </div>
                <div class="mt-5">
                    @include('profile.partials.delete-user-form')
                </div>
            </article>
        </section>
    </div>
@endsection
