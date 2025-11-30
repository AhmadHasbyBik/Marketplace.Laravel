@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
    <div class="space-y-4">
        <h1 class="text-2xl font-semibold">Edit Kategori</h1>
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-sm text-slate-400">Nama</label>
                <input type="text" name="name" value="{{ $category->name }}" class="w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm">
            </div>
            <div>
                <label class="text-sm text-slate-400">Slug</label>
                <input type="text" name="slug" value="{{ $category->slug }}" class="w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm">
            </div>
            <div>
                <label class="text-sm text-slate-400">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm">{{ $category->description }}</textarea>
            </div>
            @if($category->image_url)
                <div>
                    <label class="text-sm text-slate-400">Gambar saat ini</label>
                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="mt-2 h-32 w-full rounded-2xl border border-slate-700 object-cover" />
                </div>
            @endif
            <div>
                <label class="text-sm text-slate-400">Ganti gambar</label>
                <input type="file" name="image" accept="image/*" class="w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm text-slate-200" />
            </div>
            <div class="flex gap-2">
                <button type="submit" class="rounded-2xl bg-rose-500 px-4 py-2 text-sm font-semibold">Simpan perubahan</button>
            </div>
        </form>
    </div>
@endsection
