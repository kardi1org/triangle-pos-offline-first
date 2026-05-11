{{-- Tanpa Tag Style agar tidak merusak modal lain --}}
<div id="printable-area">
    {{-- HEADER --}}
    <div class="center">
        <div class="title-prebill">PRE-BILL</div><br>
        <strong style="font-size: 13px;">{{ $settings->company_name ?? 'POS SYSTEM' }}</strong><br>
        {{ $settings->company_address ?? '' }}
        <div class="divider"></div>
    </div>

    {{-- INFO TRANSAKSI --}}
    <table>
        <tr>
            <td class="col-label">Ref.</td>
            <td class="col-value">: {{ $sale->reference }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
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
        @if ($sale->table_list)
            <tr>
                <td>Meja</td>
                <td>: {{ $sale->table_list }}</td>
            </tr>
        @endif
    </table>

    <div class="divider"></div>

    {{-- DETAIL ITEM --}}
    <table>
        @foreach ($saleDetails as $item)
            <tr>
                <td colspan="2">{{ $item->product_name }}</td>
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
        @php
            $subtotal_murni = $saleDetails->sum(fn($item) => $item->quantity * $item->price);
        @endphp

        <tr>
            <td class="col-label">SUBTOTAL</td>
            <td class="col-value text-right">{{ number_format($subtotal_murni, 0, ',', '.') }}</td>
        </tr>

        @if ($sale->discount_amount > 0)
            <tr>
                <td>DISKON</td>
                <td class="text-right">(-) {{ number_format($sale->discount_amount, 0, ',', '.') }}</td>
            </tr>
        @endif

        @if ($sale->order_type == 'dine_in' && ($sale->service_charge ?? 0) > 0)
            <tr>
                <td>SERVICE CHG</td>
                <td class="text-right">(+) {{ number_format($sale->service_charge, 0, ',', '.') }}</td>
            </tr>
        @endif

        @if ($sale->tax_amount > 0)
            <tr>
                <td>PPN</td>
                <td class="text-right">(+) {{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
            </tr>
        @endif

        <tr class="font-bold">
            <td style="font-size: 14px; border-top: 1px dashed #000;">TOTAL</td>
            <td class="text-right" style="font-size: 14px; border-top: 1px dashed #000;">
                {{ number_format($sale->total_amount, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="center">
        <p>Bukan Bukti Pembayaran Resmi</p>
        <p>Silahkan Menuju Kasir</p>
    </div>
</div>
