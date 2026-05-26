<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Laundry #{{ $order->invoice_no }}</title>
    <style>
        /* CSS Khusus Printer Thermal (Kertas 58mm) */
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            background-color: #fff;
        }

        .ticket {
            width: 58mm;
            max-width: 58mm;
            padding: 5px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        .border-top {
            border-top: 1px dashed #000;
        }

        .border-bottom {
            border-bottom: 1px dashed #000;
        }

        .mb-1 {
            margin-bottom: 5px;
        }

        .mt-1 {
            margin-top: 5px;
        }

        .fs-small {
            font-size: 9px;
            color: #555;
        }

        @media print {
            body {
                margin: 0cm;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="ticket">
        <div class="text-center bold" style="font-size: 14px;">{{ $setting->store_name ?? 'LAUNDRYSYNC' }}</div>
        <div class="text-center mb-1">
            {!! nl2br(e($setting->address ?? '')) !!}<br>
            Telp: {{ $setting->phone ?? '' }}
        </div>

        <div class="border-bottom mb-1"></div>

        <table>
            <tr>
                <td width="30%">Invoice</td>
                <td width="5%">:</td>
                <td>{{ $order->invoice_no }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/y H:i') }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>:</td>
                <td>{{ $order->customer->name ?? $order->customer_name }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>:</td>
                <td>{{ strtoupper($order->customer->member_status ?? 'REGULAR') }}</td>
            </tr>
        </table>

        <div class="border-bottom mt-1 mb-1"></div>

        <table>
            @foreach ($order->details as $item)
                <tr>
                    <td colspan="3" class="bold">
                        {{ $item->service->name ?? 'Layanan Dihapus' }}
                    </td>
                </tr>
                <tr>
                    <td width="35%">{{ number_format($item->qty, 2) }} {{ $item->service->unit ?? 'kg' }}</td>
                    <td width="30%">x {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td width="35%" class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if ($item->item_condition || $item->notes)
                    <tr>
                        <td colspan="3" class="fs-small">
                            @if ($item->item_condition)
                                <div>* Diag: {{ $item->item_condition }}</div>
                            @endif
                            @if ($item->notes)
                                <div>* Note: {{ $item->notes }}</div>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>

        <div class="border-top mt-1 mb-1"></div>

        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if ($order->discount_amount > 0)
                <tr>
                    <td>Diskon</td>
                    <td class="text-right">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td>Pajak ({{ $setting->tax_rate ?? 0 }}%)</td>
                <td class="text-right">Rp {{ number_format($order->tax, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="bold">GRAND TOTAL</td>
                <td class="text-right bold" style="font-size: 12px;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
            </tr>
            @php
                $sisa = $order->grand_total - $order->down_payment;
            @endphp
            @if ($order->payment_method === 'down_payment' && $sisa > 0)
                <tr>
                    <td>Uang Muka (DP)</td>
                    <td class="text-right">Rp {{ number_format($order->down_payment, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="bold">SISA TAGIHAN</td>
                    <td class="text-right bold" style="font-size: 11px; color: #d9534f;">
                        Rp {{ number_format($sisa, 0, ',', '.') }}
                    </td>
                </tr>
            @else
                <tr>
                    <td>TOTAL BAYAR</td>
                    <td class="text-right">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                </tr>
                @if ($order->cash_received !== null && $order->cash_received > 0)
                    <tr>
                        <td>Cash Diterima</td>
                        <td class="text-right">Rp {{ number_format($order->cash_received, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Kembalian</td>
                        <td class="text-right">Rp {{ number_format($order->cash_change ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endif
            <tr>
                <td>Metode Bayar</td>
                <td class="text-right text-uppercase">{{ str_replace('_', ' ', $order->payment_method) }}</td>
            </tr>
            <tr>
                <td>Status Bayar</td>
                <td class="text-right bold">{{ $sisa <= 0 ? 'LUNAS' : 'BELUM LUNAS' }}</td>
            </tr>
        </table>

        <div class="border-top mt-1 mb-1"></div>

        <table>
            <tr>
                <td class="bold" colspan="3">ESTIMASI SELESAI:</td>
            </tr>
            <tr>
                <td colspan="3" class="bold" style="font-size: 12px;">
                    {{ $order->estimated_completed_at ? \Carbon\Carbon::parse($order->estimated_completed_at)->translatedFormat('d M Y H:i') : '-' }} WIB
                </td>
            </tr>
            @if ($order->special_instructions)
                <tr>
                    <td colspan="3" class="fs-small mt-1">
                        Instruksi: {{ $order->special_instructions }}
                    </td>
                </tr>
            @endif
        </table>

        <div class="border-top mt-1 mb-1"></div>

        <div class="text-center mt-1" style="font-size: 10px;">
            Terima Kasih atas Kepercayaan Anda!<br>
            Harap simpan nota ini untuk pengambilan pakaian.<br>
            * Barang yang tidak diambil dalam 30 hari diluar tanggung jawab kami.
        </div>
        <br><br>
    </div>

</body>

</html>
