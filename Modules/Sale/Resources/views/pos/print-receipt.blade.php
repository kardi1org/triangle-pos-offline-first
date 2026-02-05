<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Penjualan {{ $sale->reference }}</title>
    <style>
        /* =========================
           RESET PRINT THERMAL
        ========================== */
        @media print {
            @page {
                size: auto;
                margin: 0;
                /* 🔥 penting */
            }

            body {
                margin: 0;
                padding: 3px 4px;
                /* atas-bawah | kiri-kanan */
            }
        }

        body {
            font-family: monospace;
            /* thermal lebih presisi */
            font-size: 10px;
            line-height: 1.3;

            /* Width thermal */
            @if (!isset($isModal) || !$isModal)
                width: 80mm;
            @endif

            margin: 0;
            padding: 3px 4px;
        }

        /* =========================
           RESET DEFAULT TAG
        ========================== */
        h1,
        h2,
        h3,
        p {
            margin: 0;
            padding: 0;
        }

        .center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        th,
        td {
            padding: 1px 0;
            border-bottom: 1px dashed #000;
            vertical-align: top;
        }

        .no-border th,
        .no-border td {
            border-bottom: none;
        }

        .total-row {
            font-weight: bold;
        }

        hr {
            border: 0;
            border-top: 1px dashed #000;
            margin: 4px 0;
        }
    </style>

</head>

<body onload="window.print()">

    <div class="center">
        <h3>DATAPRIMA POS</h3>
        <p>Jl. Trembesi Kemayoran</p>
        <p>Telp: 0812-XXXX-XXXX</p>
        <hr style="border-top: 1px dashed #000; margin: 5px 0;">
    </div>

    <div class="no-border">
        <table>
            <tr>
                <td width="30%">Ref.</td>
                <td>: {{ $sale->reference }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($sale->date)->format('d-m-Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ auth()->user()->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>: {{ $sale->customer_name }}</td>
            </tr>
            @if (isset($sale->table_list))
                <tr>
                    <td>Meja</td>
                    <td>: {{ $sale->table_list }}</td>
                </tr>
            @endif
        </table>
    </div>

    <hr style="border-top: 1px dashed #000; margin: 5px 0;">

    <table>
        @foreach ($sale->saleDetails as $item)
            <tr>
                <td colspan="2" class="no-border">
                    {{ $item->product_name ?? $item->name }}
                </td>
            </tr>
            <tr>
                <td>
                    {{ $item->quantity }} x {{ format_currency($item->price) }}
                </td>
                <td style="text-align: right">
                    {{ format_currency($item->quantity * $item->price) }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td colspan="2" class="no-border">&nbsp;</td>
        </tr>

        <tr class="total-row">
            <td>SUBTOTAL</td>
            <td style="text-align: right">{{ format_currency($sale->total_amount) }}</td>
        </tr>
        <tr>
            <td>PPN ({{ $sale->tax_percentage }}%)</td>
            <td style="text-align: right">{{ format_currency($sale->tax_amount) }}</td>
        </tr>
        <tr>
            <td>Diskon ({{ $sale->discount_percentage }}%)</td>
            <td style="text-align: right">(-) {{ format_currency($sale->discount_amount) }}</td>
        </tr>
        <tr class="total-row">
            <td>GRAND TOTAL</td>
            <td style="text-align: right">{{ format_currency($sale->total_amount) }}</td>
        </tr>
        <tr>
            <td>TUNAI/DITERIMA</td>
            <td style="text-align: right">{{ format_currency($sale->paid_amount) }}</td>
        </tr>
        <tr>
            <td>KEMBALIAN</td>
            <td style="text-align: right">{{ format_currency($sale->paid_amount - $sale->total_amount) }}</td>
        </tr>
    </table>

    <hr style="border-top: 1px dashed #000; margin: 5px 0;">

    <div class="center">
        <p>Terima kasih atas kunjungan Anda.</p>
        <p>Selamat menikmati!</p>
    </div>

    <script>
        // Hanya picu print otomatis jika BUKAN dipanggil dari modal
        @if (!isset($isModal) || !$isModal)
            window.print();

            // Pilihan: Tutup jendela setelah mencetak atau jika print dibatalkan
            window.onafterprint = function() {
                setTimeout(function() {
                    window.close();
                }, 500);
            };
        @endif
    </script>
</body>

</html>
