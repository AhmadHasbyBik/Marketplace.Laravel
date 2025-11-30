@extends('layouts.kasir')

@section('title', 'Daftar Order Kasir')

@section('content')
    <div class="space-y-4">
        <h1 class="text-2xl font-semibold">Order Online</h1>
        <div class="rounded-3xl border border-slate-800 bg-slate-900/80 p-4 space-y-3">
            @foreach($orders as $order)
                <a href="{{ route('kasir.orders.show', $order) }}" class="block rounded-2xl border border-slate-800 p-3 text-sm hover:border-rose-500">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold">{{ $order->order_number }}</span>
                        <span class="text-xs text-rose-400">{{ $order->status }}</span>
                    </div>
                    <p class="text-xs text-slate-400">{{ $order->user?->name }}</p>
                </a>
            @endforeach
        </div>
    </div>
@endsection
