<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kitchen Order</title>

    <style>
        /* 1. KUNCI LEBAR AREA CETAK */
        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            font-family: monospace;
            font-size: 11px;
            /* Ukuran standar dapur agar ringkas */
            line-height: 1.2;
            margin: 0;
            padding: 0;
            /* Lebar aman 48mm dari kertas 58mm */
            width: 48mm;
            background-color: #fff;
            color: #000;
        }

        /* 2. WRAPPER UNTUK MENJAGA JARAK AMAN */
        .order-wrapper {
            width: 100%;
            padding: 10px 0 10px 2mm;
            /* Ada gap 2mm dari kiri besi printer */
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Mencegah kolom meluber ke kanan */
        }

        td {
            vertical-align: top;
            word-wrap: break-word;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 4px 0;
            width: 100%;
        }

        .center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        @media print {
            body {
                width: 48mm;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="order-wrapper">
        {{-- HEADER --}}
        <div class="center">
            <strong style="font-size:14px;">KITCHEN ORDER</strong><br>
            <div class="divider"></div>
        </div>

        {{-- INFO ORDER --}}
        <table>
            <tr>
                <td style="width:30%">Ref</td>
                <td>: {{ $sale->reference }}</td>
            </tr>
            <tr>
                <td>Type</td>
                <td>: <strong>{{ strtoupper($sale->order_type) }}</strong></td>
            </tr>
            <tr>
                <td>Cust</td>
                <td>: {{ substr($sale->customer_name ?? '-', 0, 12) }}</td>
            </tr>
            <tr>
                <td>Meja</td>
                <td>: <strong>{{ $sale->table->name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Tgl</td>
                <td>: {{ now()->format('d/m/y H:i') }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        {{-- ITEMS --}}
        @foreach ($sale->saleDetails as $item)
            @php
                // Logika Aggregation Variant Anda tetap sama
                $variants = [];
                $aggregatedVariants = [];
                if (!empty($item->variant_detail)) {
                    $decoded = is_array($item->variant_detail)
                        ? $item->variant_detail
                        : json_decode($item->variant_detail, true);
                    if (is_array($decoded)) {
                        $variants = $decoded;
                    }
                }

                if (!empty($variants)) {
                    foreach ($variants as $variant) {
                        $variantText = trim($variant['variant'] ?? '');
                        $typeOrder = trim($variant['typeOrder'] ?? 'dine_in');
                        $key = $variantText === '' ? 'TYPE-' . $typeOrder : $variantText . '-' . $typeOrder;
                        $label =
                            $variantText === ''
                                ? 'TYPE ' . strtoupper($typeOrder)
                                : strtoupper($variantText) . ' (' . $typeOrder . ')';

                        if (!isset($aggregatedVariants[$key])) {
                            $aggregatedVariants[$key] = ['label' => $label, 'qty' => 0];
                        }
                        $aggregatedVariants[$key]['qty']++;
                    }
                }
            @endphp

            {{-- PRODUCT TABLE (Pengganti Float Right agar tidak terpotong) --}}
            <table style="margin-top:5px;">
                <tr>
                    <td style="width: 80%; font-weight: bold; font-size: 12px;">
                        {{ strtoupper($item->product_name) }}
                    </td>
                    <td style="width: 20%; font-weight: bold; text-align: right; font-size: 12px;">
                        x{{ $item->quantity }}
                    </td>
                </tr>
            </table>

            {{-- VARIANT --}}
            <div style="padding-left:4px; font-size:10px; margin-bottom: 4px;">
                @if (!empty($aggregatedVariants))
                    @foreach ($aggregatedVariants as $v)
                        <div>- {{ $v['label'] }} x{{ $v['qty'] }}</div>
                    @endforeach
                @else
                    <div>- TYPE {{ strtoupper($sale->order_type) }}</div>
                @endif
            </div>

            <div class="divider"></div>
        @endforeach

        {{-- FOOTER --}}
        <div class="center" style="margin-top:10px;">
            <strong>--- END ORDER ---</strong>
        </div>
    </div>

    <script>
        window.onafterprint = function() {
            window.close();
        };
        // Fallback untuk Firefox agar tab otomatis tertutup
        window.onfocus = function() {
            setTimeout(function() {
                window.close();
            }, 500);
        };
    </script>

</body>

</html>
