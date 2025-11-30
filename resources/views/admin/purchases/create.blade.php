@extends('layouts.admin')

@section('title', 'Buat Purchase Order')

@section('content')
    <div class="space-y-6">
        <h1 class="text-2xl font-semibold">Buat Purchase Order</h1>
        <form action="{{ route('admin.purchases.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm text-slate-400">Supplier</label>
                    <select name="supplier_id" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-2 text-sm">
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-slate-400">Tanggal</label>
                    <input type="date" name="purchase_date" value="{{ now()->toDateString() }}" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="text-sm text-slate-400">Status</label>
                <select name="status" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-2 text-sm">
                    <option value="draft">Draft</option>
                    <option value="ordered">Ordered</option>
                    <option value="received">Received</option>
                </select>
            </div>
            <div class="rounded-3xl border border-slate-800 bg-slate-900/50 p-4 space-y-4">
                <h2 class="text-lg font-semibold">Items</h2>
                @foreach(range(0, 2) as $index)
                    <div class="grid gap-3 md:grid-cols-3">
                        <div>
                            <label class="text-sm text-slate-400">Produk</label>
                            <select name="items[{{ $index }}][product_id]" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm">
                                <option value="">Pilih produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-slate-400">Kuantitas</label>
                            <input type="number" name="items[{{ $index }}][quantity]" value="1" min="1" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-sm text-slate-400">Harga Beli</label>
                            <input type="number" name="items[{{ $index }}][unit_cost]" value="0" min="0" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm">
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="rounded-2xl bg-rose-500 px-5 py-3 text-sm font-semibold">Simpan PO</button>
        </form>
    </div>
@endsection
