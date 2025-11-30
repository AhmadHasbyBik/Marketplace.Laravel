@php
    $formatCurrency = fn ($value) => 'Rp' . number_format($value, 0, ',', '.');
    $purchase = $materialPurchase;
    $paidAmount = $purchase->paid_amount;
    $balance = $purchase->balance;
    $supplierName = $purchase->supplier?->name ?? '-';
    $dateLabel = $purchase->purchase_date?->format('d M Y') ?? '-';
@endphp

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>PO {{ $purchase->purchase_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #111;
            margin: 0;
        }

        .page {
            width: 100%;
            max-width: 720px;
            padding: 24px 28px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header .meta {
            text-align: right;
            font-size: 11px;
            color: #4b5563;
        }

        .box {
            background: #fff;
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 18px;
            border: 1px solid #e5e7eb;
        }

        .box strong {
            display: block;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            table-layout: fixed;
            word-break: break-word;
        }

        table thead th {
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
            color: #6b7280;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tbody td {
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .totals {
            max-width: 320px;
            margin-left: auto;
            font-size: 12px;
        }

        .totals div {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }

        .grand {
            font-weight: 700;
            margin-top: 6px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            background: #e0f2fe;
            color: #0369a1;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="header">
            <div>
                <p class="badge">Purchase Order</p>
                <h1>{{ $purchase->purchase_number }}</h1>
            </div>
            <div class="meta">
                <div>Tanggal PO: {{ $dateLabel }}</div>
                <div>Status: {{ ucfirst($purchase->status) }}</div>
            </div>
        </div>

        <div class="box">
            <strong>Supplier</strong>
            <div>{{ $supplierName }}</div>
            <div class="meta" style="margin-top:6px;">Catatan: {{ $purchase->notes ?? '-' }}</div>
        </div>

        <div class="box">
            <strong>Rincian Bahan</strong>
            <table>
                <thead>
                    <tr>
                        <th>Nama Bahan</th>
                        <th>Qty</th>
                        <th class="text-right">Harga Satuan</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ $item->material?->name ?? 'Bahan Habis' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="text-right">{{ $formatCurrency($item->unit_cost) }}</td>
                            <td class="text-right">{{ $formatCurrency($item->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div>
                    <span>Total PO</span>
                    <span>{{ $formatCurrency($purchase->total) }}</span>
                </div>
                <div>
                    <span>Terbayar</span>
                    <span>{{ $formatCurrency($paidAmount) }}</span>
                </div>
                <div class="grand">
                    <span>Sisa</span>
                    <span>{{ $formatCurrency($balance) }}</span>
                </div>
            </div>
        </div>

        <div class="box">
            <strong>Riwayat Transaksi</strong>
            @if($purchase->transactions->isEmpty())
                <p style="font-size:11px;color:#6b7280;margin:0;">Belum ada transaksi tercatat.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th class="text-right">Jumlah</th>
                            <th class="text-right">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date?->format('d M Y') ?? '-' }}</td>
                                <td>{{ $transaction->payment_method }}</td>
                                <td class="text-right">{{ $formatCurrency($transaction->amount) }}</td>
                                <td class="text-right">{{ $transaction->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>

</html>
