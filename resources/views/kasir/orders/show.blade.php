@extends('layouts.kasir')

@section('title', 'Detail Order Kasir')

@section('content')
    <div class="space-y-6">
        <h1 class="text-2xl font-semibold">Order {{ $order->order_number }}</h1>
        <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-5 space-y-2">
            <p class="text-sm text-slate-400">Status sekarang: <strong>{{ $order->status }}</strong></p>
            <form action="{{ route('kasir.orders.update', $order) }}" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <label class="text-sm text-slate-400">Ubah status</label>
                <select name="status" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm">
                    @foreach(['pending','paid','processing','shipped','completed','cancelled'] as $status)
                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <input type="text" name="notes" placeholder="Catatan" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm">
                <button type="submit" class="w-full rounded-2xl bg-rose-500 px-4 py-2 text-sm font-semibold">Perbarui</button>
            </form>
        </div>
    </div>
@endsection
