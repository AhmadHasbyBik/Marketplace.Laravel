<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'UMKM Dapoer Cupid')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">
        <header class="bg-gradient-to-br from-slate-900 to-rose-900 shadow-lg sticky top-0 z-20">
            @php
                $cartItems = collect(session('cart.items', []));
                $cartCount = $cartItems->count();
            @endphp
            <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between gap-4 px-6 py-5 text-white">
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-white/10 p-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Dapoer Cupid" class="h-10 w-10 object-contain" />
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-[0.6em] text-white/60">Dapoer Cupid</p>
                        <p class="text-xl font-semibold">Cake & Cookies</p>
                    </div>
                </div>
                <div class="flex items-center gap-5 text-sm font-semibold">
                    <a href="{{ route('front.home') }}" class="hover:text-rose-200">Home</a>
                    <a href="{{ route('front.products.index') }}" class="hover:text-rose-200">Produk</a>
                    <a href="{{ route('front.about') }}" class="hover:text-rose-200">About Us</a>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <details class="relative">
                        <summary class="flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-1 text-sm font-semibold transition hover:border-white/50 hover:bg-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 6h14l-1.5 9h-12z" />
                                <circle cx="9" cy="20" r="1.5" />
                                <circle cx="17" cy="20" r="1.5" />
                            </svg>
                            <span>Keranjang</span>
                            @if($cartCount > 0)
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[0.6rem] font-black uppercase text-white">{{ $cartCount }}</span>
                            @endif
                        </summary>
                        <div class="absolute right-0 mt-2 w-80 rounded-2xl border border-white/30 bg-white/95 p-4 text-slate-900 shadow-2xl backdrop-blur">
                            <div class="flex items-center justify-between text-sm font-semibold text-slate-500">
                                <span>Ringkasan</span>
                                <span>{{ $cartItems->count() }} item</span>
                            </div>
                            <div class="mt-3 space-y-3 max-h-56 overflow-auto pr-1">
                                @forelse($cartItems as $item)
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="space-y-1">
                                            <p class="font-semibold">{{ $item['name'] }}</p>
                                            <p class="text-xs text-slate-500">x{{ $item['quantity'] }}</p>
                                        </div>
                                        <span class="text-sm font-semibold text-rose-600">Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500">Keranjang masih kosong.</p>
                                @endforelse
                            </div>
                            <div class="mt-4 flex items-center justify-between text-sm font-semibold">
                                <span>Total</span>
                                <span class="text-rose-600">Rp{{ number_format($cartItems->sum(fn($item) => $item['price'] * $item['quantity']), 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('front.cart.index') }}" class="mt-4 block rounded-2xl bg-rose-500 px-4 py-2 text-center text-sm font-semibold uppercase tracking-[0.3em] text-white transition hover:bg-rose-600">Checkout</a>
                        </div>
                    </details>
                    @auth
                        <a href="{{ route('front.orders.index') }}" class="text-white/80 hover:text-white">Pesanan Saya</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-white/80 hover:text-white">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-white/80 hover:text-white">Login</a>
                        <a href="{{ route('register') }}" class="text-white/80 hover:text-white">Register</a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="flex-1 max-w-6xl mx-auto mt-8 px-6">
            @yield('content')
        </main>

        <footer class="mt-16 bg-white border-t">
            <div class="max-w-6xl mx-auto px-6 py-8 flex justify-between text-slate-500 text-sm">
                <div>
                    <div class="font-semibold text-slate-900">UMKM Dapoer Cupid</div>
                    <p>Sawah, Dukuh Mojo, Kec. Mojoagung, Kabupaten Jombang, Jawa Timur 61482</p>
                    <p>+62 812-3456-7890</p>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="#" class="hover:text-rose-500">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-rose-500">Syarat & Ketentuan</a>
                </div>
            </div>
        </footer>
        @include('components.toasts')
    </body>
</html>
