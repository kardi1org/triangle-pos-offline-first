<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Print Shift #{{ $shift->id }}</title>
    <style>
        /* 1. RESET TOTAL UNTUK THERMAL */
        @page {
            margin: 0;
            /* Menghapus margin kertas bawaan browser */
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
            background-color: #fff;
            color: #000;
            width: 100%;
        }

        /* 2. WRAPPER DENGAN LEBAR DINAMIS */
        .print-wrapper {
            /* Menggunakan width 92% agar ada sedikit nafas di kiri-kanan */
            /* sehingga tidak mentok ke pinggir besi pemotong printer */
            width: 92%;
            margin: 0 auto;
            padding: 10px 0;
            overflow: hidden;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .company-name {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
        }

        .report-title {
            font-size: 10pt;
            margin: 2px 0;
        }

        .shift-id {
            font-size: 8pt;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }

        /* 3. TABEL HARUS LAYOUT FIXED AGAR TIDAK MELEBAR */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            table-layout: fixed;
            /* Memaksa tabel tidak melebihi lebar wrapper */
        }

        td {
            padding: 2px 0;
            vertical-align: top;
            word-wrap: break-word;
            /* Memotong kata jika terlalu panjang */
        }

        .text-right {
            text-align: right;
        }

        /* Mengatur lebar kolom agar nama label tidak mendorong angka keluar */
        .col-label {
            width: 55%;
        }

        .col-value {
            width: 45%;
        }

        .font-bold {
            font-weight: bold;
        }

        .gap-section {
            margin: 8px 0;
            text-align: center;
            border: 1px solid #000;
            padding: 5px;
        }

        .gap-value {
            font-size: 11pt;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            font-size: 8pt;
            margin-top: 10px;
        }

        /* 4. MEDIA PRINT KHUSUS UNTUK MENGHINDARI PEMOTONGAN */
        @media print {
            body {
                width: 84% !important;
            }

            .print-wrapper {
                width: 95% !important;
                /* Sedikit lebih lebar saat diprint */
                margin: 0 auto !important;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="print-wrapper">
        <div class="header">
            <div class="company-name">{{ settings()->company_name ?? 'POS SYSTEM' }}</div>
            <div class="report-title">SHIFT SUMMARY</div>
            <div class="shift-id">#{{ $shift->id }}</div>
        </div>

        <div class="divider"></div>

        <table>
            <tr>
                <td class="col-label">KASIR</td>
                <td class="col-value text-right">{{ strtoupper(substr($cashier->name ?? 'User', 0, 12)) }}</td>
            </tr>
            <tr>
                <td class="col-label">WAKTU</td>
                <td class="col-value text-right">{{ \Carbon\Carbon::parse($shift->close_time)->format('d/m/y H:i') }}
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <table>
            <tr>
                <td class="col-label">MODAL AWAL</td>
                <td class="col-value text-right">{{ number_format($shift->starting_cash, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="col-label">PENJUALAN</td>
                <td class="col-value text-right">+{{ number_format($sales ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="col-label">PENGELUARAN</td>
                <td class="col-value text-right">-{{ number_format($expense ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="font-bold">
                <td style="padding-top:5px">SISTEM</td>
                <td class="text-right" style="padding-top:5px">
                    {{ number_format($shift->expected_ending_cash, 0, ',', '.') }}</td>
            </tr>
            <tr class="font-bold">
                <td>FISIK</td>
                <td class="text-right">Rp{{ number_format($shift->ending_cash, 0, ',', '.') }}</td>
            </tr>
        </table>

        @php $diff = $shift->ending_cash - $shift->expected_ending_cash; @endphp
        <div class="gap-section">
            <div style="font-size: 8pt;">SELISIH (GAP)</div>
            <div class="gap-value">{{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}</div>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <div>{{ now()->format('d/m/y H:i') }}</div>
            <div>- SELESAI -</div>
        </div>
    </div>

    <script>
        window.onafterprint = function() {
            window.close();
        };
        // Fallback fokus untuk Firefox
        setTimeout(function() {
            window.onfocus = function() {
                window.close();
            }
        }, 500);
    </script>
</body>

</html>
