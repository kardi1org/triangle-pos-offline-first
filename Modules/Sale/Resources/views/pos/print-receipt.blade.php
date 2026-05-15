<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan {{ $sale->reference }}</title>
    <style>
        /* 1. SETTING KERTAS THERMAL */
        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            font-family: monospace;
            font-size: 11px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            /* Area cetak aman printer 58mm adalah 48mm */
            width: 48mm;
            background-color: #fff;
            color: #000;
        }

        /* 2. WRAPPER UTAMA */
        .ticket-container {
            width: 100%;
            padding: 10px 0 10px 2mm;
            /* Geser 2mm dari kiri agar tidak kena besi printer */
            box-sizing: border-box;
        }

        .center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        /* 3. TABEL FIXED (Kunci agar kanan tidak terpotong) */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* WAJIB: Mengunci lebar kolom */
            margin-top: 4px;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
            word-wrap: break-word;
            /* Lipat teks jika kepanjangan */
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        /* Lebar Kolom Struk */
        .col-desc {
            width: 60%;
        }

        .col-price {
            width: 40%;
        }

        .col-label {
            width: 35%;
        }

        .col-value {
            width: 65%;
        }

        @media print {
            body {
                width: 48mm;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="ticket-container">
        {{-- HEADER --}}
        <div class="center">
            <strong style="font-size: 13px;">{{ $settings->company_name ?? 'POS SYSTEM' }}</strong><br>

            {{-- Memanggil outlet_data --}}
            @if ($sale->outlet_data)
                <strong style="font-size: 11px;">{{ $sale->outlet_data->name }}</strong><br>
                {{-- Telp: {{ (string) ($sale->outlet_data->telp ?? '-') }}<br> --}}
            @endif

            {{ $settings->company_address ?? '' }}<br>
            Telp: {{ $settings->company_phone ?? '' }}
            <div class="divider"></div>
        </div>

        {{-- INFO TRANSAKSI --}}
        <table>
            <tr>
                <td class="col-label">Ref.</td>
                <td class="col-value">: {{ $sale->reference }}</td>
            </tr>
            <tr>
                <td>Close</td>
                <td>: {{ \Carbon\Carbon::parse($sale->updated_at)->format('d-m-Y H:i') }}</td>
            </tr>
            <tr>
                <td>Time In</td>
                <td>: {{ \Carbon\Carbon::parse($sale->created_at)->format('d-m-Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ auth()->user()->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Cust.</td>
                <td>: {{ substr($sale->customer_name, 0, 15) }}</td>
            </tr>
            @if (isset($sale->table_list))
                <tr>
                    <td>Meja</td>
                    <td>: {{ $sale->table_list }}</td>
                </tr>
            @endif
        </table>

        <div class="divider"></div>

        {{-- DETAIL ITEM --}}
        <table>
            @foreach ($sale->saleDetails as $item)
                <tr>
                    <td colspan="2">
                        {{ $item->product_name ?? $item->name }}
                    </td>
                </tr>
                <tr>
                    <td class="col-desc">
                        {{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}
                    </td>
                    <td class="col-price text-right">
                        {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </table>

        <div class="divider" style="margin-top: 8px;"></div>

        {{-- TOTALAN --}}
        <table>
            {{-- Hitung Subtotal Murni (Total - Tax - Shipping - SC - LainA - LainB + Discount) --}}
            @php
                $subtotal_murni =
                    $sale->total_amount -
                    $sale->tax_amount -
                    $sale->shipping_amount -
                    ($sale->service_charge ?? 0) -
                    ($sale->lain_a ?? 0) -
                    ($sale->lain_b ?? 0) +
                    $sale->discount_amount;
            @endphp

            <tr>
                <td class="col-label">SUBTOTAL</td>
                <td class="col-value text-right">{{ number_format($subtotal_murni, 0, ',', '.') }}</td>
            </tr>

            @if ($sale->discount_amount > 0)
                <tr>
                    <td>DISKON ({{ $sale->discount_percentage }}%)</td>
                    <td class="text-right">(-) {{ number_format($sale->discount_amount, 0, ',', '.') }}</td>
                </tr>
            @endif

            {{-- Rule: Service Charge muncul jika order_type dine_in dan nilainya > 0 --}}
            @if ($sale->order_type == 'dine_in' && ($sale->service_charge ?? 0) > 0)
                <tr>
                    <td>SERVICE CHARGE (5%)</td>
                    <td class="text-right">(+) {{ number_format($sale->service_charge, 0, ',', '.') }}</td>
                </tr>
            @endif

            @if ($sale->tax_amount > 0)
                <tr>
                    <td>PPN ({{ $sale->tax_percentage }}%)</td>
                    <td class="text-right">(+) {{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
                </tr>
            @endif

            @if ($sale->shipping_amount > 0)
                <tr>
                    <td>DELIVERY</td>
                    <td class="text-right">(+) {{ number_format($sale->shipping_amount, 0, ',', '.') }}</td>
                </tr>
            @endif

            {{-- Lain-lain A --}}
            @if (($sale->lain_a ?? 0) > 0)
                <tr>
                    <td>LAIN-LAIN A</td>
                    <td class="text-right">(+) {{ number_format($sale->lain_a, 0, ',', '.') }}</td>
                </tr>
            @endif

            {{-- Lain-lain B --}}
            @if (($sale->lain_b ?? 0) > 0)
                <tr>
                    <td>LAIN-LAIN B</td>
                    <td class="text-right">(+) {{ number_format($sale->lain_b, 0, ',', '.') }}</td>
                </tr>
            @endif

            <tr class="font-bold">
                <td style="font-size: 14px; border-top: 1px dashed #000;">GRAND TOTAL</td>
                <td class="text-right" style="font-size: 14px; border-top: 1px dashed #000;">
                    {{ number_format($sale->total_amount, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td>TUNAI</td>
                <td class="text-right">{{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td class="font-bold">KEMBALI</td>
                <td class="text-right font-bold">
                    {{ number_format($sale->paid_amount - $sale->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        {{-- FOOTER --}}
        <div class="center">
            @if ($sale->outlet_data->info)
                <div
                    style="margin-bottom: 5px; white-space: pre-line; font-size: 10px; line-height: 1.4; border-top: 1px dashed #000; padding-top: 1px;">
                    {!! e($sale->outlet_data->info) !!}
                    {{-- {{ $sale->outlet_data->info }}  --}}
                </div>
            @endif

            <div class="divider"></div>
            <p>Terima kasih atas kunjungan Anda.</p>
        </div>
    </div>

    <script>
        @if (!isset($isModal) || !$isModal)
            window.onafterprint = function() {
                setTimeout(function() {
                    window.close();
                }, 500);
            };

            // Pengaman Firefox
            window.onfocus = function() {
                setTimeout(function() {
                    window.close();
                }, 800);
            };
        @endif
    </script>
</body>

</html>
