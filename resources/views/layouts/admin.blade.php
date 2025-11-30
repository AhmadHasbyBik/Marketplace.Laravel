<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dashboard Admin UMKM Dapoer Cupid')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        @php
            $navItems = [
                [
                    'label' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'description' => 'Ringkasan operasional',
                    'icon' => '<rect width="7" height="9" x="3" y="3" rx="1" /><rect width="7" height="5" x="14" y="3" rx="1" /><rect width="7" height="9" x="14" y="12" rx="1" /><rect width="7" height="5" x="3" y="16" rx="1" />',
                    'color' => 'from-sky-400/80 to-cyan-500/70',
                    'activePattern' => 'admin.dashboard',
                ],
                [
                    'label' => 'Kategori',
                    'route' => 'admin.categories.index',
                    'description' => 'Atur struktur katalog',
                    'icon' => '<path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z" /><circle cx="7.5" cy="7.5" r=".5" fill="currentColor" />',
                    'color' => 'from-amber-400/80 to-orange-400/60',
                    'activePattern' => 'admin.categories.*',
                ],
                [
                    'label' => 'Produk',
                    'route' => 'admin.products.index',
                    'description' => 'Kelola katalog lengkap',
                    'icon' => '<path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z" /><path d="M12 22V12" /><polyline points="3.29 7 12 12 20.71 7" /><path d="m7.5 4.27 9 5.15" />',
                    'color' => 'from-cyan-500/80 to-sky-500/60',
                    'activePattern' => 'admin.products.*',
                ],
                [
                    'label' => 'Pengiriman',
                    'route' => 'admin.shipping-methods.index',
                    'description' => 'Metode logistik',
                    'icon' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2" /><path d="M15 18H9" /><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14" /><circle cx="17" cy="18" r="2" /><circle cx="7" cy="18" r="2" />',
                    'color' => 'from-lime-400/90 to-emerald-500/80',
                    'activePattern' => 'admin.shipping-methods.*',
                ],
                [
                    'label' => 'Order',
                    'route' => 'admin.orders.index',
                    'description' => 'Lacak pesanan pelanggan',
                    'icon' => '<rect width="8" height="4" x="8" y="2" rx="1" ry="1" /><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" /><path d="M12 11h4" /><path d="M12 16h4" /><path d="M8 11h.01" /><path d="M8 16h.01" />',
                    'color' => 'from-rose-400/80 to-red-500/80',
                    'activePattern' => 'admin.orders.*',
                ],
                [
                    'label' => 'Ulasan',
                    'route' => 'admin.reviews.index',
                    'description' => 'Pantau rating & komentar',
                    'icon' => '<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z" />',
                    'color' => 'from-amber-400/80 to-yellow-500/70',
                    'activePattern' => 'admin.reviews.*',
                ],
                [
                    'label' => 'Inventory',
                    'route' => 'admin.inventory.index',
                    'description' => 'Pantau stok aman',
                    'icon' => '<path d="M4 10c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2" /><path d="M10 16c-1.1 0-2-.9-2-2v-4c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2" /><rect width="8" height="8" x="14" y="14" rx="2" />',
                    'color' => 'from-cyan-400/80 to-sky-500/80',
                    'activePattern' => 'admin.inventory.*',
                ],
                [
                    'label' => 'Supplier',
                    'route' => 'admin.suppliers.index',
                    'description' => 'Kelola mitra dagang',
                    'icon' => '<path d="m11 17 2 2a1 1 0 1 0 3-3" /><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4" /><path d="m21 3 1 11h-2" /><path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3" /><path d="M3 4h8" />',
                    'color' => 'from-sky-400/70 to-cyan-500/80',
                    'activePattern' => 'admin.suppliers.*',
                ],
                [
                    'label' => 'Laporan',
                    'route' => 'admin.reports.index',
                    'description' => 'Insight keuangan & stok',
                    'icon' => '<path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" /><path d="M14 2v5a1 1 0 0 0 1 1h5" /><path d="m16 13-3.5 3.5-2-2L8 17" />',
                    'color' => 'from-slate-400/80 to-slate-500/80',
                    'activePattern' => 'admin.reports.*',
                ],
                [
                    'label' => 'Pembelian',
                    'route' => 'admin.purchases.index',
                    'description' => 'Rekam pembelian & pembayaran',
                    'icon' => '<path d="M16 10a4 4 0 0 1-8 0" /><path d="M3.103 6.034h17.794" /><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z" />',
                    'color' => 'from-fuchsia-500/80 to-purple-500/70',
                    'activePattern' => 'admin.purchases.*',
                ],
                        [
                            'label' => 'Users',
                            'route' => 'admin.users.index',
                            'description' => 'Atur pengguna & role',
                            'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><path d="M16 3.128a4 4 0 0 1 0 7.744" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><circle cx="9" cy="7" r="4" />',
                            'color' => 'from-slate-400/80 to-slate-500/80',
                            'activePattern' => 'admin.users.*',
                        ],
            ];
            $utilityItem = [
                'label' => 'Settings',
                'route' => 'profile.edit',
                'icon' => '<path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915" /><circle cx="12" cy="12" r="3" />',
            ];
            $dashboardNotifications = $dashboardNotifications ?? collect();
            $unreadNotificationsCount = $unreadNotificationsCount ?? 0;
            $unreadNotificationIds = $unreadNotificationIds ?? [];
        @endphp
        <div class="relative flex min-h-screen">
            <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-30 -translate-x-full flex w-72 flex-col justify-between border-r border-slate-200 bg-white px-6 py-8 shadow-lg shadow-slate-200 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:shadow-none">
                <div class="space-y-8">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-cyan-500 text-white shadow-inner">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12L12 3l9 9v9H3z" />
                                <path d="M9.5 21V12h5v9" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-lg font-semibold text-slate-900">Dapoer Cupid</p>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-500">Admin Panel</p>
                        </div>
                    </a>
                    <nav class="space-y-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Menu Utama</p>
                        <div class="space-y-2">
                            @foreach($navItems as $item)
                                @php
                                    $pattern = $item['activePattern'] ?? $item['route'];
                                    $isActive = request()->routeIs($pattern);
                                @endphp
                                <a href="{{ route($item['route']) }}" class="group flex items-center gap-3 rounded-3xl border border-slate-100 bg-white px-3 py-3 text-sm font-medium text-slate-700 transition hover:border-slate-200 hover:bg-slate-50 {{ $isActive ? 'shadow-lg text-slate-900 ring-1 ring-sky-200' : '' }}">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br {{ $item['color'] }} text-white shadow-inner">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            {!! $item['icon'] !!}
                                        </svg>
                                    </span>
                                    <div>
                                        <p>{{ $item['label'] }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </nav>
                </div>
            </aside>
            <div class="flex-1">
                <div id="sidebar-backdrop" class="fixed inset-0 z-20 hidden bg-slate-900/40 lg:hidden"></div>
                <header class="sticky top-0 z-10 border-b border-slate-200 bg-white/80 px-4 py-4 backdrop-blur lg:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-1 items-center gap-3">
                            <button id="sidebar-toggle" class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-slate-300 lg:hidden">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 5h16" />
                                    <path d="M4 12h16" />
                                    <path d="M4 19h16" />
                                </svg>
                            </button>
                            <div class="flex flex-1 items-center gap-3">
                                <div class="relative flex-1">
                                    <input type="search" placeholder="Cari modul atau data" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200" aria-label="Cari modul atau data" />
                                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m21 21-4.34-4.34" />
                                            <circle cx="11" cy="11" r="8" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                x-data='notificationBell({{ $unreadNotificationsCount }}, @json($unreadNotificationIds))'
                                x-cloak
                                @keydown.escape.window="open = false"
                                class="relative"
                            >
                                <button
                                    type="button"
                                    @click="toggle()"
                                    aria-haspopup="true"
                                    :aria-expanded="open"
                                    class="relative flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-slate-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-sky-500"
                                >
                                    <span class="sr-only">Buka panel notifikasi</span>
                                    <span
                                        class="absolute -right-0.5 -top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-semibold text-white"
                                        x-show="badgeCount > 0"
                                        x-text="badgeCount"
                                    ></span>
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10.268 21a2 2 0 0 0 3.464 0" />
                                        <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326" />
                                    </svg>
                                </button>
                                <div
                                    x-show="open"
                                    x-transition:enter="transform duration-200 ease-out"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition duration-150"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    @click.outside="open = false"
                                    class="absolute right-0 top-full z-30 mt-2 w-80 max-w-xs origin-top-right rounded-3xl border border-slate-200 bg-white ring-1 ring-slate-900/10 shadow-xl shadow-slate-900/10"
                                    x-cloak
                                >
                                    <div class="px-4 py-3">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Notifikasi</p>
                                            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500 transition hover:text-slate-700">Lihat semua</a>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-400">Update operasional terbaru</p>
                                    </div>
                                    <div class="max-h-72 space-y-2 overflow-y-auto border-t border-slate-100 px-4 pb-4 pt-2">
                                        @forelse($dashboardNotifications as $notification)
                                            <a
                                                href="{{ $notification['url'] }}"
                                                class="flex items-start justify-between gap-3 rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm text-slate-700 transition hover:border-slate-200 hover:bg-slate-50"
                                            >
                                                <div class="flex-1">
                                                    <p class="text-sm font-semibold text-slate-900">{{ $notification['title'] }}</p>
                                                    <p class="mt-0.5 text-xs text-slate-500">{{ $notification['description'] }}</p>
                                                    <p class="mt-0.5 text-xs text-slate-400">{{ $notification['meta'] }} · {{ $notification['time'] }}</p>
                                                </div>
                                                <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M9 18 15 12 9 6" />
                                                </svg>
                                            </a>
                                        @empty
                                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                                                Belum ada notifikasi terbaru.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <details class="relative">
                                <summary class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-200 text-slate-600">AC</span>
                                    <span>Admin Cupid</span>
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </summary>
                                <div class="absolute right-0 top-full mt-2 w-48 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                            <circle cx="9" cy="7" r="4" />
                                        </svg>
                                        Profil
                                    </a>
                                    <a href="{{ route('profile.settings') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915" /><circle cx="12" cy="12" r="3" />
                                        </svg>
                                        Settings
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 rounded-xl border border-rose-100 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-600 shadow-sm transition hover:bg-rose-100">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m16 17 5-5-5-5" />
                                                <path d="M21 12H9" />
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </details>
                        </div>
                    </div>
                </header>
                <main class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-7xl space-y-6">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
        <script>
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggle = document.getElementById('sidebar-toggle');
            let sidebarOpen = false;

            const setSidebar = (open) => {
                if (!sidebar || !backdrop) {
                    return;
                }
                if (open) {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                }
            };

            if (toggle) {
                toggle.addEventListener('click', () => {
                    sidebarOpen = !sidebarOpen;
                    setSidebar(sidebarOpen);
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', () => {
                    sidebarOpen = false;
                    setSidebar(sidebarOpen);
                });
            }
        </script>
        <script>
            function notificationBell(initialUnread = 0, unreadIds = []) {
                return {
                    open: false,
                    badgeCount: initialUnread,
                    unreadIds: Array.isArray(unreadIds) ? unreadIds : [],
                    toggle() {
                        this.open = !this.open;
                        if (this.open && this.badgeCount > 0) {
                            this.markRead();
                        }
                    },
                    async markRead() {
                        if (!this.unreadIds.length) {
                            this.badgeCount = 0;
                            return;
                        }

                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                        if (!token) {
                            this.badgeCount = 0;
                            this.unreadIds = [];
                            return;
                        }

                        try {
                            await fetch('{{ route('admin.notifications.markRead') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                },
                                body: JSON.stringify({ ids: this.unreadIds }),
                            });
                        } catch (error) {
                            console.error('Failed to mark notifications read', error);
                        } finally {
                            this.badgeCount = 0;
                            this.unreadIds = [];
                        }
                    },
                };
            }
        </script>
        @include('components.toasts')
    </body>
</html>
