@php
    use Illuminate\Support\Str;

    $shippingAddress = $order->address;
    $recipientName = $shippingAddress?->recipient_name ?? ($order->user?->name ?? 'Pelanggan');
    $addressSegments = array_filter([
        $shippingAddress?->street,
        $shippingAddress?->city,
        $shippingAddress?->province,
        $order->shipping_district,
        $order->shipping_city,
        $order->shipping_province,
    ]);
    $addressLine = $addressSegments ? implode(', ', array_unique($addressSegments)) : '-';
    $phoneNumber = $shippingAddress?->phone ?? ($order->user?->phone ?? '-');
    $paymentLabel = $order->payment_method ? Str::title(str_replace('_', ' ', $order->payment_method)) : 'Manual';
    $formatRp = fn($value) => 'Rp' . number_format($value, 0, ',', '.');
    $statusLabel = ucfirst(str_replace('_', ' ', $order->status));
    $dateLabel = $order->created_at?->format('d M Y H:i') ?? '-';

    // Tokopedia-style status colors
    $statusColors = [
        'pending' => '#fbbf24',
        'processing' => '#3b82f6',
        'completed' => '#10b981',
        'cancelled' => '#ef4444',
    ];
    $statusColor = $statusColors[$order->status] ?? '#3b82f6';
@endphp

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background: #f3f4f6;
            color: #111827;
            font-size: 12px;
        }

        .container {
            width: 750px;
            margin: 20px auto;
            padding: 0 10px;
        }

        .header {
            background: #16a34a;
            /* fallback solid green (Tokopedia style) */
            background: linear-gradient(135deg, #059669, #16a34a);
            color: black;
            padding: 24px 32px;
            border-radius: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 22px;
            font-weight: bold;
        }

        .header-meta {
            text-align: right;
            font-size: 11px;
        }

        .status-badge {
            display: inline-flex;
            padding: 3px 10px;
            font-size: 10px;
            font-weight: 600;
            color: white;
            background: {{ $statusColor }};
            border-radius: 20px;
        }

        /* Section box seperti Tokopedia */
        .box {
            background: white;
            border-radius: 12px;
            padding: 18px 22px;
            margin-top: 18px;
            border: 1px solid #e5e7eb;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        thead th {
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody td {
            padding: 14px 0;
            font-size: 12px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }

        .price-col {
            text-align: right;
            white-space: nowrap;
        }

        /* Totals */
        .totals {
            width: 260px;
            margin-left: auto;
            margin-top: 14px;
            font-size: 12px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .totals-grand {
            border-top: 1px solid #e5e7eb;
            margin-top: 12px;
            padding-top: 10px;
            font-weight: 700;
            font-size: 14px;
        }

        .note-text {
            font-size: 11px;
            color: #4b5563;
            margin-top: 4px;
            line-height: 1.4;
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- HEADER Tokopedia -->
        <div class="header">
            <div>
                <div class="header-title">Invoice Pesanan</div>
                <div style="font-size: 12px; opacity: .9">UMKM Dapoer Cupid · Cake & Cookies</div>
            </div>
            <div class="header-meta">
                <div>No. Order <strong>{{ $order->order_number }}</strong></div>
                <div>{{ $dateLabel }}</div>
                <span class="status-badge">{{ $statusLabel }}</span>
            </div>
        </div>

        <!-- INFORMASI RINGKAS -->
        <div class="box">
            <div class="section-title">Informasi Pembeli</div>
            <div><strong>{{ $recipientName }}</strong></div>
            <div class="note-text">{{ $phoneNumber }}</div>
        </div>

        <div class="box">
            <div class="section-title">Alamat Pengiriman</div>
            <div>{{ $addressLine }}</div>
        </div>

        <div class="box">
            <div class="section-title">Metode Pembayaran</div>
            <div><strong>{{ $paymentLabel }}</strong></div>
            <div class="note-text">Metode pembayaran manual via transfer</div>
        </div>

        <div class="box">
            <div class="section-title">Pengiriman</div>
            <div><strong>{{ $order->shippingMethod?->name ?? 'Kurir eksternal' }}</strong></div>
            <div class="note-text">
                {{ strtoupper($order->shipping_courier ?? '-') }} · {{ $order->shipping_service ?? 'Standar' }}
            </div>
        </div>

        <!-- ITEMS -->
        <div class="box">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th class="price-col">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product?->name ?? 'Produk terhapus' }}</strong><br>
                                <span class="note-text">{{ $item->product?->sku ?? '' }}</span>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td class="price-col">{{ $formatRp($item->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- TOTALS -->
            <div class="totals">
                <div class="totals-row"><span>Subtotal</span><span>{{ $formatRp($order->subtotal) }}</span></div>
                <div class="totals-row"><span>Ongkir</span><span>{{ $formatRp($order->shipping_cost) }}</span></div>
                <div class="totals-row"><span>Diskon</span><span>-{{ $formatRp($order->discount) }}</span></div>
                <div class="totals-row totals-grand"><span>Total</span><span>{{ $formatRp($order->total) }}</span>
                </div>
            </div>
        </div>

        <!-- CATATAN -->
        <div class="box">
            <div class="section-title">Catatan</div>
            <div class="note-text">
                {{ $order->notes ?? 'Tidak ada catatan tambahan.' }}
            </div>
        </div>

        <!-- STATUS PEMBAYARAN -->
        <div class="box">
            <div class="section-title">Status Pembayaran</div>
            <div><strong>{{ $order->is_paid ? 'Terbayar' : 'Belum dibayar' }}</strong></div>
            <div class="note-text">{{ $paymentLabel }}</div>
        </div>

    </div>

</body>

</html>
