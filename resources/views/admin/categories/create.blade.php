@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="space-y-4">
        <h1 class="text-2xl font-semibold">Tambah Kategori</h1>
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm text-slate-400">Nama</label>
                <input type="text" name="name" class="w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm">
            </div>
            <div>
                <label class="text-sm text-slate-400">Slug</label>
                <input type="text" name="slug" class="w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm">
            </div>
            <div>
                <label class="text-sm text-slate-400">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm"></textarea>
            </div>
            <div>
                <label class="text-sm text-slate-400">Gambar</label>
                <input type="file" name="image" accept="image/*" class="w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm text-slate-200" />
            </div>
            <div class="flex gap-2">
                <button type="submit" class="rounded-2xl bg-rose-500 px-4 py-2 text-sm font-semibold">Simpan</button>
            </div>
        </form>
    </div>
@endsection
