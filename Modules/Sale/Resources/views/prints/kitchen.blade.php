<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kitchen Order</title>

    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            line-height: 1.35;

            /* ✅ JARAK AMAN */
            padding: 8px 6px 6px 6px;
            /* atas kanan bawah kiri */
            margin: 0;
        }

        @media print {
            @page {
                size: auto;
                margin: 0;
            }

            body {
                margin: 0;
                padding: 3px 4px 6px 6px;
            }
        }
    </style>

</head>

<body>

    {{-- HEADER --}}
    <div style="text-align:center; margin-bottom:6px;">
        <strong style="font-size:14px;">KITCHEN ORDER</strong><br>
        -----------------------------
    </div>

    {{-- INFO ORDER --}}
    <div style="margin-bottom:6px;">
        <div>Ref : {{ $sale->reference }}</div>
        <div>Type: <strong>{{ strtoupper($sale->order_type) }}</strong></div>
        <div>Cust: {{ $sale->customer_name ?? '-' }}</div>
        <div>Meja: {{ $sale->table->name ?? '-' }}</div>
        <div>Tgl : {{ now()->format('d-m-Y H:i') }}</div>
    </div>

    -----------------------------

    {{-- ITEMS --}}
    @foreach ($sale->saleDetails as $item)
        @php
            $variants = [];
            $aggregatedVariants = [];

            if (!empty($item->variant_detail)) {
                if (is_array($item->variant_detail)) {
                    $variants = $item->variant_detail;
                } else {
                    $decoded = json_decode($item->variant_detail, true);
                    if (is_array($decoded)) {
                        $variants = $decoded;
                    }
                }
            }

            if (!empty($variants)) {
                foreach ($variants as $variant) {
                    $variantText = trim($variant['variant'] ?? '');
                    $typeOrder = trim($variant['typeOrder'] ?? 'dine_in');

                    if ($variantText === '') {
                        $key = 'TYPE-' . $typeOrder;
                        $label = 'TYPE ' . strtoupper($typeOrder);
                    } else {
                        $key = $variantText . '-' . $typeOrder;
                        $label = strtoupper($variantText) . ' (' . $typeOrder . ')';
                    }

                    if (!isset($aggregatedVariants[$key])) {
                        $aggregatedVariants[$key] = [
                            'label' => $label,
                            'qty' => 0,
                        ];
                    }

                    $aggregatedVariants[$key]['qty']++;
                }
            }
        @endphp

        {{-- PRODUCT --}}
        <div style="margin-top:6px; page-break-inside: avoid;">
            <strong>{{ strtoupper($item->product_name) }}</strong>
            <span style="float:right;">x{{ $item->quantity }}</span>
        </div>

        {{-- VARIANT --}}
        @if (!empty($aggregatedVariants))
            @foreach ($aggregatedVariants as $v)
                <div style="padding-left:8px; font-size:11px;">
                    - {{ $v['label'] }} x{{ $v['qty'] }}
                </div>
            @endforeach
        @else
            <div style="padding-left:8px; font-size:11px;">
                - TYPE {{ strtoupper($sale->order_type) }}
            </div>
        @endif

        -----------------------------
    @endforeach

    {{-- FOOTER --}}
    <div style="text-align:center; margin-top:6px;">
        <strong>--- END ORDER ---</strong>
    </div>

    {{-- CUT HACK --}}
    <div style="height:1px;"></div>

</body>

</html>
