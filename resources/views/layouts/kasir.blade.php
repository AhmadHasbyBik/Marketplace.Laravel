<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>@yield('title', 'Kasir UMKM Dapoer Cupid')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-slate-900 min-h-screen">
        <div class="flex min-h-screen">
            <main class="flex-1">
                <header class="bg-white px-6 py-4 border-b border-slate-200 flex items-center justify-between shadow-sm">
                    <span class="text-xl font-semibold text-rose-500">POS Kasir</span>
                    <a href="{{ route('front.home') }}" class="text-sm text-slate-500 hover:text-slate-900">Back to Store</a>
                </header>
                <div class="p-6">
                    @yield('content')
                </div>
            </main>
        </div>
        @include('components.toasts')
        @stack('scripts')
    </body>
</html>
