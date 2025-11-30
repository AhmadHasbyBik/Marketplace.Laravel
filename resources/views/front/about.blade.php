@extends('layouts.front')

@section('title', 'Tentang UMKM Dapoer Cupid')

@section('content')
    <section class="max-w-6xl mx-auto space-y-16 px-6 py-12 lg:py-16">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 to-rose-600 p-10 text-white shadow-[0_20px_60px_rgba(15,23,42,0.4)]">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.25),_transparent_60%)] opacity-60"></div>
            <div class="relative space-y-6">
                <p class="text-xs uppercase tracking-[0.6em] text-white/70">UMKM Dapoer Cupid</p>
                <h1 class="text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">Transformasi digital untuk menjaga keberlangsungan UMKM kuliner lokal.</h1>
                <p class="text-base text-white/80 sm:text-lg">Sistem e-commerce kini menjadi pondasi wajib agar UMKM Dapoer Cupid dapat mengelola order, stok, dan bahan baku tanpa kehilangan momen penting bagi pelanggan di dalam maupun luar Jawa.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="#solution" class="rounded-full bg-white px-6 py-2.5 text-xs font-semibold uppercase tracking-[0.4em] text-rose-600 transition hover:bg-rose-50">Lihat Solusi</a>
                    <a href="#story" class="rounded-full border border-white/50 px-6 py-2.5 text-xs font-semibold uppercase tracking-[0.4em] text-white transition hover:border-white">Cerita Kami</a>
                </div>
                <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/80">Sejak 2021</p>
                        <p class="text-sm text-white/70">Berawal dari dapur rumah, kini jadi merk kuliner disegani.</p>
                    </div>
                    <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/80">200+ pesanan</p>
                        <p class="text-sm text-white/70">Rata-rata tercatat tiap minggu, menuntut sistem konsisten.</p>
                    </div>
                    <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/80">10-15 order</p>
                        <p class="text-sm text-white/70">Pelanggan luar Jawa membuktikan daya tarik yang meluas.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="story" class="grid gap-8 lg:grid-cols-[1.1fr,0.9fr]">
            <article class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg">
                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.5em] text-slate-400">Latar Belakang</p>
                    <h2 class="text-2xl font-semibold text-slate-900">Operasional manual jadi hambatan pertumbuhan</h2>
                </div>
                <p class="mt-4 text-slate-600 leading-relaxed">UMKM Dapoer Cupid adalah usaha mikro rumahan yang bergerak di jual beli kue dan snack. Bisnis ini bertumbuh meski pemesanan masih dilakukan langsung atau melalui WhatsApp dan pencatatan penjualan serta bahan baku masih manual.</p>
                <p class="text-slate-600 leading-relaxed">Akibatnya pencatatan order sering keliru dan membutuhkan verifikasi ulang, sehingga waktu layanan terbuang dan kepuasan pelanggan menurun. Tekanan meningkat saat permintaan membesar di hari besar keagamaan, membuat pemilik kewalahan menerima pesan di WhatsApp dan Instagram.</p>
                <p class="text-slate-600 leading-relaxed">Manajemen bahan baku juga minim panduan frekuensi pembelian, menyebabkan stok tidak siap bila permintaan melonjak, sementara permintaan mingguan sudah mencapai 200 order.</p>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-900">Kesalahan pencatatan penjualan</p>
                        <p class="text-sm text-slate-600">Order harus dicek ulang satu per satu sehingga efisiensi waktu berkurang.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-900">Manajemen bahan baku manual</p>
                        <p class="text-sm text-slate-600">Tidak ada frekuensi beli, membuat stok tampak habis pada momen kritis.</p>
                    </div>
                </div>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-gradient-to-br from-rose-500 to-orange-400 p-8 text-white shadow-2xl">
                <p class="text-xs uppercase tracking-[0.4em] text-white/80">Statistik Operasional</p>
                <div class="mt-6 space-y-6 text-sm">
                    <div>
                        <p class="text-4xl font-semibold">200+</p>
                        <p class="text-white/80">Pesanan mingguan yang tercatat manual, berpotensi hilang tanpa sistem.</p>
                    </div>
                    <div>
                        <p class="text-4xl font-semibold">10-15</p>
                        <p class="text-white/80">Order dari luar Jawa sejak 2021 yang menegaskan perluasan pasar.</p>
                    </div>
                    <div>
                        <p class="text-4xl font-semibold">2</p>
                        <p class="text-white/80">Masalah utama: pencatatan penjualan dan manajemen bahan baku.</p>
                    </div>
                </div>
            </article>
        </div>

        <div id="solution" class="space-y-8 rounded-3xl border border-slate-200 bg-white p-8 shadow-lg">
            <div class="space-y-2">
                <p class="text-xs uppercase tracking-[0.5em] text-slate-400">Solusi digital</p>
                <h2 class="text-2xl font-semibold text-slate-900">Sistem informasi penjualan kue berbasis web</h2>
            </div>
            <p class="text-slate-600 leading-relaxed">Inovasi ini menggabungkan transaksi jual beli, pencatatan profit, dan pengelolaan bahan baku dengan rekomendasi frekuensi pembelian sehingga pemilik bisa meminimalkan kerugian dan meningkat keuntungan.</p>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['title' => 'Penjualan', 'body' => 'Dashboard pesanan realtime dengan keranjang online yang mudah diakses'],
                    ['title' => 'Pengiriman', 'body' => 'Integrasi ongkos kirim agar pelanggan tahu estimasi biaya tanpa menunggu'],
                    ['title' => 'Pembelian Bahan', 'body' => 'Rekomendasi frekuensi beli menyesuaikan volume permintaan'],
                    ['title' => 'Laporan', 'body' => 'Cetak laporan penjualan, pembelian, dan laba-rugi dalam hitungan menit'],
                ] as $card)
                    <article class="flex h-full flex-col justify-between rounded-2xl border border-slate-100 bg-slate-50 p-5 text-sm shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="space-y-2">
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-500">{{ $card['title'] }}</p>
                            <p class="text-slate-900 font-semibold leading-snug">{{ $card['body'] }}</p>
                        </div>
                        <span class="text-rose-500 text-xs font-semibold uppercase tracking-[0.4em]">Fitur</span>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-2xl">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.5em] text-white/70">Tujuan</p>
                    <h3 class="text-2xl font-semibold">Mempermudah pelanggan sekaligus meringankan pemilik</h3>
                    <p class="text-sm text-white/80">Pelanggan bisa melihat katalog dan memesan kue dari rumah, sementara pemilik dapat mengelola bahan baku dan laporan otomatis.</p>
                </div>
                <a href="{{ route('front.products.index') }}" class="inline-flex items-center justify-center rounded-full bg-rose-500 px-6 py-3 text-xs font-semibold uppercase tracking-[0.4em] text-white shadow-lg transition hover:bg-rose-400">Buka Toko</a>
            </div>
        </div>
    </section>
@endsection
