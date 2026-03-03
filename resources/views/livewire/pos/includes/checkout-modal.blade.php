<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">
                    <i class="bi bi-cart-check text-primary"></i> Confirm Sale
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="checkout-form" method="POST"
                action="{{ !empty($current_reference) ? route('app.pos.update') : route('app.pos.store') }}">
                @csrf
                <input type="hidden" name="current_reference" value="{{ $current_reference }}">

                <div class="modal-body">
                    @if (session()->has('checkout_message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="alert-body">
                                <span>{{ session('checkout_message') }}</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    <script>
                        function updatekembalian() {
                            const el = (id) => document.getElementById(id);
                            const num = (v) => isNaN(parseFloat(v)) ? 0 : parseFloat(v);
                            const rupiah = (number) => new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR"
                            }).format(number);

                            const totalAmount = num(el('total_amount').value);
                            const actionButton = el('actionbutton');
                            const paymentIds = ['cash', 'debitcard', 'creditcard', 'gopay', 'ovo', 'shopeepay', 'kredivo', 'dana',
                                'grabpay', 'qris'
                            ];

                            let paidAmount = 0;
                            paymentIds.forEach(id => {
                                const input = el(id);
                                if (input && input.type !== 'hidden') paidAmount += num(input.value);
                            });

                            el('paid_amount').value = paidAmount;
                            el('lblreceipt').innerHTML = rupiah(paidAmount);

                            let change = paidAmount - totalAmount;
                            if (change < 0) change = 0;

                            el('kembalian').value = change;
                            el('lblkembalian').innerHTML = rupiah(change);

                            const receiptLabel = el('lblreceipt');
                            if (paidAmount < totalAmount) {
                                actionButton.disabled = true;
                                receiptLabel.classList.add('text-danger');
                                receiptLabel.classList.remove('text-primary');
                            } else {
                                actionButton.disabled = false;
                                receiptLabel.classList.remove('text-danger');
                                receiptLabel.classList.add('text-primary');
                            }
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            const paymentInputs = document.querySelectorAll('.payment-input');
                            paymentInputs.forEach(input => {
                                input.addEventListener('input', updatekembalian);
                            });
                        });
                    </script>

                    @php
                        $summarySettings = \Modules\Setting\Entities\OrderSummarySetting::where('is_active', true)
                            ->orderBy('id', 'asc')
                            ->get();

                        $pure_subtotal = 0;
                        foreach (Cart::instance($cart_instance)->content() as $item) {
                            $pure_subtotal += $item->price * $item->qty;
                        }

                        $tax_base = $pure_subtotal;
                        $after_tax_charges = 0;
                        $discount_val = (float) str_replace(',', '', Cart::instance($cart_instance)->discount());
                        $service_charge_val = 0;

                        // Sync values with main calculation logic
                        $shipping_val = isFeatureEnabled('summary_pkg') ? (float) ($shipping ?? 0) : 0;
                        $lain_a_val = isFeatureEnabled('summary_others') ? (float) ($lain_a ?? 0) : 0;
                        $lain_b_val = isFeatureEnabled('summary_others') ? (float) ($lain_b ?? 0) : 0;

                        foreach ($summarySettings as $setting) {
                            $current_value = 0;
                            switch ($setting->feature_key) {
                                case 'service_charge':
                                    if (isFeatureEnabled('summary_service') && $order_type == 'dine_in') {
                                        $scConfig = \Modules\ServiceCharge\Entities\ServiceCharge::where(
                                            'is_active',
                                            true,
                                        )->first();
                                        $sc_percent = $scConfig->percentage ?? 0;
                                        $sc_type = $scConfig->calculation_type ?? 1;

                                        if ($sc_type == 2) {
                                            // Netto
                                            $current_value = ($pure_subtotal - $discount_val) * ($sc_percent / 100);
                                        } else {
                                            // Gross
                                            $current_value = $pure_subtotal * ($sc_percent / 100);
                                        }
                                        $service_charge_val = $current_value;
                                    }
                                    break;
                                case 'delivery_fee':
                                    $current_value = $shipping_val;
                                    break;
                                case 'discount_global':
                                    $current_value = $discount_val;
                                    break;
                                case 'lain_a':
                                    $current_value = $lain_a_val;
                                    break;
                                case 'lain_b':
                                    $current_value = $lain_b_val;
                                    break;
                            }

                            if ($setting->tax_position == 'before') {
                                $setting->feature_key == 'discount_global'
                                    ? ($tax_base -= $current_value)
                                    : ($tax_base += $current_value);
                            } else {
                                if ($setting->feature_key != 'order_tax') {
                                    $setting->feature_key == 'discount_global'
                                        ? ($after_tax_charges -= $current_value)
                                        : ($after_tax_charges += $current_value);
                                }
                            }
                        }
                        $tax_val = max(0, $tax_base * (($global_tax ?? 0) / 100));
                        $grand_total = $tax_base + $tax_val + $after_tax_charges;
                    @endphp

                    <div class="row">
                        <div class="col-lg-7 border-right">
                            {{-- Form Hidden Fields --}}
                            <input type="hidden" name="tax_percentage" value="{{ (int) $global_tax }}">
                            <input type="hidden" name="discount_percentage" value="{{ (int) $global_discount }}">
                            <input type="hidden" value="{{ $customer_name }}" name="customer_name">
                            <input type="hidden" value="{{ $tax_val }}" name="tax_amount">
                            <input type="hidden" value="{{ $discount_val }}" name="discount_amount">
                            <input type="hidden" value="{{ $shipping_val }}" name="shipping_amount">
                            <input type="hidden" name="order_type" value="{{ $order_type }}">
                            <input type="hidden" name="table_id" value="{{ $table_id }}">
                            <input type="hidden" name="service_charge" value="{{ $service_charge_val }}">
                            <input type="hidden" name="lain_a" value="{{ $lain_a_val }}">
                            <input type="hidden" name="lain_b" value="{{ $lain_b_val }}">
                            <input type="hidden" name="total_amount" id="total_amount" value="{{ $grand_total }}">
                            <input type="hidden" id="paid_amount" name="paid_amount" value="0">
                            <input type="hidden" id="kembalian" name="kembalian" value="0">

                            @foreach (Cart::instance($cart_instance)->content() as $item)
                                <input type="hidden" name="variants[{{ $item->id }}]"
                                    value="{{ json_encode($item->options->variants ?? []) }}">
                            @endforeach
                            <input type="hidden" name="selected_table_ids"
                                value="{{ json_encode($table_ids_array ?? []) }}">

                            {{-- Display Panel --}}
                            <div class="card p-3 shadow-sm mb-3 bg-light">
                                <div class="row text-center">
                                    <div class="col-4 border-right">
                                        <label class="small text-muted mb-0">Tagihan</label>
                                        <div class="font-weight-bold">{{ format_currency($grand_total) }}</div>
                                    </div>
                                    <div class="col-4 border-right">
                                        <label class="small text-muted mb-0">Bayar</label>
                                        <div class="font-weight-bold text-primary" id="lblreceipt">Rp 0</div>
                                    </div>
                                    <div class="col-4">
                                        <label class="small text-muted mb-0">Kembali</label>
                                        <div class="font-weight-bold text-success" id="lblkembalian">Rp 0</div>
                                    </div>
                                </div>
                            </div>

                            <label class="font-weight-bold small text-dark">METODE PEMBAYARAN <span
                                    class="text-danger">*</span></label>
                            <div class="row">
                                @php
                                    $available_payments = [
                                        'Cash' => 'cash',
                                        'DebitCard' => 'debitcard',
                                        'CreditCard' => 'creditcard',
                                        'Gopay' => 'gopay',
                                        'OVO' => 'ovo',
                                        'ShopeePay' => 'shopeepay',
                                        'Dana' => 'dana',
                                        'GrabPay' => 'grabpay',
                                        'QRIS' => 'qris',
                                        'Kredivo' => 'kredivo',
                                    ];
                                    $multipay_enabled = isFeatureEnabled('pos_multipay');
                                @endphp

                                @foreach ($available_payments as $key => $input_id)
                                    @php
                                        $is_allowed = $multipay_enabled || $key === 'Cash';
                                        $is_active = isset($payments->$key) && $payments->$key == 'Y';
                                    @endphp
                                    @if ($is_active && $is_allowed)
                                        <div class="col-6 mb-2">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text text-dark"
                                                        style="width: 85px; font-size: 10px; font-weight: bold;">{{ $key }}</span>
                                                </div>
                                                <input type="number" id="{{ $input_id }}"
                                                    name="{{ $input_id }}" class="form-control payment-input"
                                                    value="0" onclick="this.select()">
                                            </div>
                                        </div>
                                    @else
                                        <input type="hidden" id="{{ $input_id }}" name="{{ $input_id }}"
                                            value="0">
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="table-responsive">
                                <table class="table table-sm small">
                                    <tr class="bg-light text-dark">
                                        <th>Item</th>
                                        <td class="text-right"><span
                                                class="badge badge-secondary">{{ Cart::instance($cart_instance)->count() }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Subtotal</td>
                                        <td class="text-right">{{ format_currency($pure_subtotal) }}</td>
                                    </tr>

                                    {{-- Global Discount (Posisi di atas Service Charge sesuai request) --}}
                                    @if ($discount_val > 0)
                                        <tr class="text-success">
                                            <td>Diskon ({{ $global_discount }}%)</td>
                                            <td class="text-right">-{{ format_currency($discount_val) }}</td>
                                        </tr>
                                    @endif

                                    {{-- Komponen Lainnya --}}
                                    @foreach ($summarySettings->where('feature_key', '!=', 'discount_global') as $set)
                                        @php
                                            $val = 0;
                                            if (
                                                $set->feature_key == 'service_charge' &&
                                                isFeatureEnabled('summary_service') &&
                                                $order_type == 'dine_in'
                                            ) {
                                                $val = $service_charge_val;
                                            } elseif (
                                                $set->feature_key == 'delivery_fee' &&
                                                isFeatureEnabled('summary_pkg')
                                            ) {
                                                $val = $shipping_val;
                                            } elseif (
                                                ($set->feature_key == 'lain_a' || $set->feature_key == 'lain_b') &&
                                                isFeatureEnabled('summary_others')
                                            ) {
                                                $val = $set->feature_key == 'lain_a' ? $lain_a_val : $lain_b_val;
                                            }
                                        @endphp
                                        @if ($val > 0)
                                            <tr>
                                                <td>{{ $set->feature_name }}</td>
                                                <td class="text-right text-danger">+{{ format_currency($val) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    @if ($tax_val > 0)
                                        <tr>
                                            <td>Pajak ({{ $global_tax }}%)</td>
                                            <td class="text-right text-danger">+{{ format_currency($tax_val) }}</td>
                                        </tr>
                                    @endif

                                    <tr class="table-primary">
                                        <th class="h6">GRAND TOTAL</th>
                                        <th class="h6 text-right">{{ format_currency($grand_total) }}</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="actionbutton" disabled>Submit Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>
