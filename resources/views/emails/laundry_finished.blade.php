<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laundry Anda Selesai Diproses!</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 25px;
        }
        .welcome-text {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 6px 0;
            font-size: 14px;
        }
        .meta-table td.label {
            font-weight: bold;
            color: #475569;
            width: 35%;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .details-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 12px 10px;
            font-size: 13px;
            border-bottom: 2px solid #e2e8f0;
        }
        .details-table td {
            padding: 12px 10px;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .total-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        .total-table {
            width: 100%;
        }
        .total-table td {
            padding: 4px 0;
            font-size: 14px;
        }
        .total-table td.grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #166534;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>LAUNDRYSYNC</h1>
            <p>Laundry Anda Siap Diambil! ✨</p>
        </div>
        
        <div class="content">
            <div class="welcome-text">
                Kabar gembira, <strong>{{ $order->customer->name }}</strong>!<br>
                Proses pengerjaan pakaian Anda pada nomor invoice <strong>{{ $order->invoice_no }}</strong> telah selesai dilakukan dengan teliti dan higienis. Pakaian Anda kini dalam kondisi bersih, rapi, harum, dan siap diambil.
            </div>

            <!-- Meta Data Box -->
            <div class="meta-box">
                <table class="meta-table">
                    <tr>
                        <td class="label">Nomor Invoice</td>
                        <td>: <strong>{{ $order->invoice_no }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Selesai</td>
                        <td>: {{ \Carbon\Carbon::parse($order->updated_at)->translatedFormat('d F Y H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td class="label">Status Layanan</td>
                        <td>: <strong style="color: #16a34a;">SELESAI DIPROSES (SIAP DIAMBIL)</strong></td>
                    </tr>
                </table>
            </div>

            <!-- Details Table Summary -->
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Paket Laundry</th>
                        <th style="text-align: center; width: 80px;">Jumlah</th>
                        <th style="text-align: right; width: 120px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->details as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->service->name }}</strong>
                            </td>
                            <td style="text-align: center;">{{ number_format($item->qty, 2) }} {{ $item->service->unit }}</td>
                            <td style="text-align: right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Financials Box -->
            <div class="total-box">
                <table class="total-table">
                    <tr>
                        <td class="grand-total">Total Tagihan</td>
                        <td class="grand-total" style="text-align: right;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $sisa = $order->grand_total - $order->down_payment;
                    @endphp
                    @if ($sisa > 0)
                        <tr>
                            <td style="color: #b91c1c; font-weight: bold;">Telah Dibayar (DP)</td>
                            <td style="text-align: right; color: #b91c1c; font-weight: bold;">-Rp {{ number_format($order->down_payment, 0, ',', '.') }}</td>
                        </tr>
                        <tr style="border-top: 1px dashed #bbf7d0;">
                            <td class="grand-total" style="color: #dc2626; padding-top: 10px;">Sisa Tagihan (Pelunasan)</td>
                            <td class="grand-total" style="text-align: right; color: #dc2626; padding-top: 10px;">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="grand-total" style="color: #166534; padding-top: 10px;">Status Pembayaran</td>
                            <td class="grand-total" style="text-align: right; color: #166534; padding-top: 10px;">LUNAS 🎉</td>
                        </tr>
                    @endif
                </table>
            </div>

            <div style="font-size: 14px; line-height: 1.6; color: #475569; background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 4px; margin-bottom: 25px;">
                <strong>📍 Lokasi Pengambilan:</strong><br>
                Silakan kunjungi outlet kami untuk mengambil pakaian Anda dengan membawa/menunjukkan bukti email ini.<br>
                Jika Anda memilih layanan antar-jemput, kurir kami akan segera menjadwalkan pengantaran ke alamat Anda.
            </div>

        </div>

        <div class="footer">
            <p><strong>LAUNDRYSYNC POS</strong> - Pilihan Terbaik Laundry Keluarga Anda</p>
            <p>Email ini dikirim secara otomatis oleh sistem POS. Harap tidak membalas email ini.</p>
        </div>
    </div>

</body>
</html>
