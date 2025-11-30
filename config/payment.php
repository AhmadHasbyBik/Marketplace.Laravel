<?php

return [
    'options' => [
        'manual_bca' => [
            'label' => 'Transfer Bank BCA',
            'tagline' => 'No. Rek 774-123-0050 · a.n. UMKM Dapoer Cupid',
            'details' => 'Kirim bukti transfer ke WhatsApp kami di 0812-3456-7890 atau unggah via tombol konfirmasi pesanan agar admin dapat memverifikasi pembayaran.',
        ],
        'manual_mandiri' => [
            'label' => 'Transfer Bank Mandiri',
            'tagline' => 'No. Rek 123-456-789 · a.n. Dapoer Cupid Logistics',
            'details' => 'Gunakan pesan catatan bank untuk menyertakan nomor pesanan agar tim kami dapat mempercepat konfirmasi.',
        ],
        'manual_bni' => [
            'label' => 'Transfer Bank BNI',
            'tagline' => 'No. Rek 009-876-5432 · a.n. Cupid Trading',
            'details' => 'Setelah transfer, simpan bukti pembayaran dan unggah di halaman pesanan agar status segera berubah menjadi “Paid”.',
        ],
        'qris' => [
            'label' => 'QRIS',
            'tagline' => 'Scan QR dengan e-wallet pilihan Anda',
            'details' => 'Gunakan aplikasi e-wallet apapun yang mendukung QRIS dan simpan notifikasi pembayaran untuk keperluan audit.',
        ],
    ],
];
