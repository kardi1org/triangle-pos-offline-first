<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Print Shift #{{ $shift->id }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            font-family: monospace;
            font-size: 11px;
            /* Seragam dengan struk penjualan */
            line-height: 1.2;
            margin: 0;
            padding: 0;
            width: 48mm;
            /* Area aman agar tidak terpotong */
            background-color: #fff;
            color: #000;
        }

        .wrapper {
            width: 100%;
            padding: 10px 0 10px 2mm;
            box-sizing: border-box;
        }

        .center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
            word-wrap: break-word;
        }

        .font-bold {
            font-weight: bold;
        }

        .gap-box {
            border: 1px solid #000;
            padding: 5px;
            margin: 8px 0;
            text-align: center;
        }
    </style>
</head>

<body onload="window.print(); setTimeout(() => window.close(), 500);">

    <div class="wrapper">
        <div class="center">
            <strong style="font-size: 13px;">{{ $settings->company_name ?? 'POS SYSTEM' }}</strong><br>
            <span>SHIFT SUMMARY</span><br>
            <small>#{{ $shift->id }}</small>
            <div class="divider"></div>
        </div>

        <table>
            <tr>
                <td style="width: 40%;">KASIR</td>
                <td class="text-right">{{ strtoupper(substr($cashier->name ?? 'N/A', 0, 15)) }}</td>
            </tr>
            <tr>
                <td>MULAI</td>
                <td class="text-right">{{ \Carbon\Carbon::parse($shift->open_time)->format('d/m/y H:i') }}</td>
            </tr>
            <tr>
                <td>SELESAI</td>
                <td class="text-right">
                    {{ $shift->close_time ? \Carbon\Carbon::parse($shift->close_time)->format('d/m/y H:i') : '-' }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <table>
            <tr>
                <td>MODAL AWAL</td>
                <td class="text-right">{{ number_format($shift->starting_cash, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>PENJUALAN (CASH)</td>
                <td class="text-right">+{{ number_format($sales, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>PEMASUKAN LAIN</td>
                <td class="text-right">+{{ number_format($income, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>PENGELUARAN</td>
                <td class="text-right">-{{ number_format($expense, 0, ',', '.') }}</td>
            </tr>
            <tr class="font-bold">
                <td style="padding-top:5px">SALDO SISTEM</td>
                <td class="text-right" style="padding-top:5px">
                    {{ number_format($shift->expected_ending_cash, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="font-bold">
                <td>UANG FISIK</td>
                <td class="text-right">{{ number_format($shift->ending_cash, 0, ',', '.') }}</td>
            </tr>
        </table>

        @php $diff = $shift->ending_cash - $shift->expected_ending_cash; @endphp
        <div class="gap-box">
            <div style="font-size: 9px;">SELISIH (GAP)</div>
            <div class="font-bold" style="font-size: 12px;">
                {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}
            </div>
        </div>

        <div class="divider"></div>
        <div class="center" style="font-size: 9px;">
            <div>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
            <div class="font-bold">- LAPORAN SHIFT -</div>
        </div>
    </div>

</body>

</html>
