@extends('layouts.front')

@section('title', 'Pesanan Saya - UMKM Dapoer Cupid')

@section('content')
    @php
        $statusStyles = [
            'pending' => 'bg-rose-50 text-rose-700',
            'paid' => 'bg-emerald-50 text-emerald-700',
            'processing' => 'bg-sky-50 text-sky-600',
            'shipped' => 'bg-indigo-50 text-indigo-700',
            'completed' => 'bg-emerald-50 text-emerald-700',
            'cancelled' => 'bg-slate-100 text-slate-500',
        ];
        $statusBadgeDefault = 'bg-slate-100 text-slate-600';
    @endphp
    <div class="space-y-6">
        <h1 class="text-3xl font-semibold">Pesanan Saya</h1>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 space-y-4">
            @foreach($orders as $order)
                <div class="border-b border-slate-100 pb-4 last:border-b-0 last:pb-0">
                    <a href="{{ route('front.orders.show', $order) }}" class="flex items-center justify-between gap-4">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $order->order_number }}</p>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusStyles[$order->status] ?? $statusBadgeDefault }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full border border-rose-100 bg-white px-4 py-1.5 text-xs font-semibold tracking-wide text-rose-600 shadow-[0_20px_40px_rgba(236,72,153,0.25)] transition hover:bg-rose-50 hover:shadow-[0_20px_50px_rgba(239,68,68,0.35)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                            Lihat detail
                        </span>
                    </a>
                </div>
            @endforeach
        </div>
        {{ $orders->links() }}
    </div>
@endsection
