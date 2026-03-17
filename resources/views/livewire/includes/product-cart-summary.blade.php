<div class="card-body p-3">
    @php
        // 1. Ambil setting dinamis label
        $summarySettings = \Modules\Setting\Entities\OrderSummarySetting::where('is_active', true)->get();

        // 2. Ambil Data Service Charge
        $serviceChargeData = \Modules\ServiceCharge\Entities\ServiceCharge::where('is_active', 1)->first();
        $sc_percentage = $serviceChargeData->percentage ?? 0;

        // 3. Hitung Subtotal tampilan
        $display_subtotal = 0;
        foreach (Cart::instance($cart_instance)->content() as $cart_item) {
            $display_subtotal += $cart_item->price * $cart_item->qty;
        }

        $grand_total = $this->calculateTotal();

        // Ambil data diskon
        $disc_setting = $summarySettings->where('feature_key', 'discount_global')->first();
        $discount_val = (float) str_replace(',', '', Cart::instance($cart_instance)->discount());
    @endphp

    {{-- 1. Sub Total --}}
    @if (isFeatureEnabled('summary_mamin'))
        <div class="d-flex justify-content-between py-1 font-weight-bold text-dark">
            <span>Sub Total</span>
            <span>{{ format_currency($display_subtotal) }}</span>
        </div>
        <hr class="my-1">
    @endif

    {{-- 2. DISKON (Di atas Service Charge) --}}
    @if ($disc_setting && $discount_val > 0)
        <div class="d-flex justify-content-between py-1">
            <span class="text-dark">{{ $disc_setting->feature_name }}
                ({{ $global_discount }}%)</span>
            <span class="text-success">(-) {{ format_currency($discount_val) }}</span>
        </div>
    @endif

    {{-- 3. Komponen BEFORE TAX --}}
    @foreach ($summarySettings->where('tax_position', 'before')->where('feature_key', '!=', 'discount_global') as $set)
        @php
            $val = 0;
            $showItem = true;

            if ($set->feature_key == 'service_charge') {
                // Tambah kondisi pengecekan fitur service charge
                if (isFeatureEnabled('summary_service') && $order_type == 'dine_in') {
                    $val = $this->service_charge;
                } else {
                    $showItem = false;
                }
            } elseif ($set->feature_key == 'delivery_fee') {
                $val = (float) $this->shipping;
            } elseif ($set->feature_key == 'lain_a') {
                $val = (float) $this->lain_a;
            } elseif ($set->feature_key == 'lain_b') {
                $val = (float) $this->lain_b;
            }
        @endphp

        @if ($showItem && $val > 0)
            <div class="d-flex justify-content-between py-1">
                <span>{{ $set->feature_name }}
                    {{ $set->feature_key == 'service_charge' ? "($sc_percentage%)" : '' }}
                </span>
                <span class="text-danger">(+) {{ format_currency($val) }}</span>
            </div>
        @endif
    @endforeach

    {{-- 4. Order Tax --}}
    <div class="d-flex justify-content-between py-1">
        <span>Order Tax ({{ $global_tax }}%)</span>
        <span class="text-danger">(+) {{ format_currency($this->tax_amount) }}</span>
    </div>

    {{-- 5. Komponen AFTER TAX --}}
    @foreach ($summarySettings->where('tax_position', 'after')->where('feature_key', '!=', 'discount_global') as $set)
        @php
            $val_after = 0;
            $showItemAfter = true;

            if ($set->feature_key == 'service_charge') {
                // Tambah kondisi pengecekan fitur service charge
                if (isFeatureEnabled('summary_service') && $order_type == 'dine_in') {
                    $val_after = $this->service_charge;
                } else {
                    $showItemAfter = false;
                }
            } elseif ($set->feature_key == 'delivery_fee') {
                $val_after = (float) $this->shipping;
            } elseif ($set->feature_key == 'lain_a') {
                $val_after = (float) $this->lain_a;
            } elseif ($set->feature_key == 'lain_b') {
                $val_after = (float) $this->lain_b;
            }
        @endphp

        @if ($showItemAfter && $val_after > 0)
            <div class="d-flex justify-content-between py-1">
                <span>{{ $set->feature_name }}
                    {{ $set->feature_key == 'service_charge' ? "($sc_percentage%)" : '' }}
                </span>
                <span class="text-danger">(+) {{ format_currency($val_after) }}</span>
            </div>
        @endif
    @endforeach

    <hr class="my-2" style="border-top: 2px dashed #ddd;">

    {{-- Grand Total --}}
    @if (isFeatureEnabled('summary_grandtotal'))
        <div class="d-flex justify-content-between py-2 text-primary font-weight-bold" style="font-size: 1.2rem;">
            <span>Grand Total</span>
            <span>{{ format_currency($grand_total) }}</span>
        </div>
    @endif
</div>

<div class="form-row">
    {{-- Input Tax --}}
    <div class="col-lg-4">
        <div class="form-group">
            <label>Order Tax (%)</label>
            <input wire:model.live="global_tax" type="number" class="form-control" min="0" max="100">
        </div>
    </div>

    {{-- Input Discount --}}
    <div class="col-lg-4">
        <div class="form-group">
            <label>Discount (%)</label>
            <input wire:model.live="global_discount" type="number" class="form-control"
                {{ !isFeatureEnabled('summary_disc') ? 'readonly' : '' }}>
        </div>
    </div>

    {{-- Input Delivery --}}
    <div class="col-lg-4">
        <div class="form-group">
            <label>Delivery</label>
            <input wire:model.live="shipping" type="number" class="form-control"
                {{ !isFeatureEnabled('summary_pkg') ? 'readonly' : '' }}>
        </div>
    </div>

    {{-- Input Lain-lain A --}}
    <div class="col-lg-6">
        <div class="form-group">
            <label>Lain-lain A</label>
            <input wire:model.live="lain_a" type="number" class="form-control"
                {{ !isFeatureEnabled('summary_others') ? 'readonly' : '' }}>
        </div>
    </div>

    {{-- Input Lain-lain B --}}
    <div class="col-lg-6">
        <div class="form-group">
            <label>Lain-lain B</label>
            <input wire:model.live="lain_b" type="number" class="form-control"
                {{ !isFeatureEnabled('summary_others') ? 'readonly' : '' }}>
        </div>
    </div>
</div>
