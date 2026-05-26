<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Laundry Baru</title>
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
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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
            border: 1px border-solid #e2e8f0;
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
            <p>Bukti Terima Pakaian Baru</p>
        </div>
        
        <div class="content">
            <div class="welcome-text">
                Halo <strong>{{ $order->customer->name }}</strong>,<br>
                Terima kasih telah mempercayakan pakaian Anda kepada <strong>LAUNDRYSYNC</strong>. Pakaian Anda telah kami terima dengan detail sebagai berikut:
            </div>

            <!-- Meta Data Box -->
            <div class="meta-box">
                <table class="meta-table">
                    <tr>
                        <td class="label">Nomor Invoice</td>
                        <td>: <strong>{{ $order->invoice_no }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Diterima</td>
                        <td>: {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td class="label">Estimasi Selesai</td>
                        <td>: <strong style="color: #2563eb;">{{ $order->estimated_completed_at ? \Carbon\Carbon::parse($order->estimated_completed_at)->translatedFormat('d F Y H:i') : '-' }} WIB</strong></td>
                    </tr>
                </table>
            </div>

            <!-- Details Table -->
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Layanan Paket</th>
                        <th style="text-align: center; width: 80px;">Jumlah</th>
                        <th style="text-align: right; width: 120px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->details as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->service->name }}</strong>
                                @if($item->item_condition)
                                    <div style="font-size: 11px; color: #dc2626; margin-top: 2px;">* Diag: {{ $item->item_condition }}</div>
                                @endif
                                @if($item->notes)
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">* Note: {{ $item->notes }}</div>
                                @endif
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
                        <td>Subtotal Tagihan</td>
                        <td style="text-align: right;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($order->discount_amount > 0)
                        <tr>
                            <td>Potongan Diskon</td>
                            <td style="text-align: right; color: #b91c1c;">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td>Pajak ({{ $order->tax > 0 ? round(($order->tax / ($order->subtotal - $order->discount_amount)) * 100) : 0 }}%)</td>
                        <td style="text-align: right;">Rp {{ number_format($order->tax, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-top: 1px dashed #bbf7d0;">
                        <td class="grand-total" style="padding-top: 10px;">Grand Total</td>
                        <td class="grand-total" style="text-align: right; padding-top: 10px;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #166534;">Uang Muka (DP Paid)</td>
                        <td style="text-align: right; font-weight: bold; color: #166534;">Rp {{ number_format($order->down_payment, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $sisa = $order->grand_total - $order->down_payment;
                    @endphp
                    <tr>
                        <td class="grand-total" style="color: #991b1b; padding-top: 8px;">Sisa Tagihan (COD)</td>
                        <td class="grand-total" style="text-align: right; color: #991b1b; padding-top: 8px;">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            <div style="font-size: 14px; line-height: 1.6; color: #475569; background: #f8fafc; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 4px;">
                <strong>💡 Catatan Penting:</strong>
                <ul style="margin: 5px 0 0; padding-left: 20px;">
                    <li>Mohon simpan email/nota ini sebagai bukti sah saat pengambilan pakaian.</li>
                    <li>Sisa tagihan (jika ada) dapat dilunasi saat pengambilan pakaian secara tunai maupun cashless.</li>
                </ul>
            </div>

        </div>

        <div class="footer">
            <p><strong>LAUNDRYSYNC POS</strong> - Solusi Kelola Laundry Modern & Profesional</p>
            <p>Email ini dikirim secara otomatis oleh sistem POS. Harap tidak membalas email ini.</p>
        </div>
    </div>

</body>
</html>
