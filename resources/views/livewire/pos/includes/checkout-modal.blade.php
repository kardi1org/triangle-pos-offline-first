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

                            // ===============================
                            // HELPER
                            // ===============================
                            const el = (id) => document.getElementById(id);
                            const num = (v) => isNaN(parseFloat(v)) ? 0 : parseFloat(v);

                            const rupiah = (number) => new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR"
                            }).format(number);

                            // ===============================
                            // ELEMENTS
                            // ===============================
                            const totalAmount = num(el('total_amount').value);
                            const paidInput = el('paid_amount');
                            const changeInput = el('kembalian');
                            const actionButton = el('actionbutton');

                            // ===============================
                            // PAYMENT INPUTS (LIST)
                            // ===============================
                            const paymentIds = [
                                'cash',
                                'debitcard',
                                'creditcard',
                                'gopay',
                                'ovo',
                                'shopeepay',
                                'kredivo',
                                'dana',
                                'grabpay',
                                'qris'
                            ];

                            // ===============================
                            // HITUNG TOTAL BAYAR
                            // ===============================
                            let paidAmount = 0;
                            paymentIds.forEach(id => {
                                const input = el(id);
                                if (input) paidAmount += num(input.value);
                            });

                            paidInput.value = paidAmount;
                            el('lblreceipt').innerHTML = rupiah(paidAmount);

                            // ===============================
                            // HITUNG KEMBALIAN
                            // ===============================
                            let change = paidAmount - totalAmount;

                            if (change < 0) {
                                change = 0;
                                actionButton.disabled = true;
                            } else {
                                actionButton.disabled = false;
                            }

                            changeInput.value = change;
                            el('lblkembalian').innerHTML = rupiah(change);

                            const receiptLabel = document.getElementById('lblreceipt');
                            const changeLabel = document.getElementById('lblkembalian');

                            if (paidAmount < totalAmount) {
                                receiptLabel.classList.add('text-danger', 'font-weight-bold');
                                changeLabel.classList.add('text-danger', 'font-weight-bold');
                            } else {
                                receiptLabel.classList.remove('text-danger', 'font-weight-bold');
                                changeLabel.classList.remove('text-danger', 'font-weight-bold');
                            }

                        }
                    </script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const paymentIds = [
                                'cash', 'debitcard', 'creditcard', 'gopay', 'ovo',
                                'shopeepay', 'kredivo', 'dana', 'grabpay', 'qris'
                            ];

                            paymentIds.forEach(id => {
                                const el = document.getElementById(id);
                                if (el) {
                                    el.addEventListener('keyup', updatekembalian);
                                }
                            });

                        });
                    </script>

                    @php
                        /*
    |--------------------------------------------------------------------------
    | CENTRALIZED ORDER CALCULATION (SATU SUMBER KEBENARAN)
    |--------------------------------------------------------------------------
    */

                        // 1️⃣ Subtotal ASLI (tanpa diskon)
                        $pure_subtotal = 0;
                        foreach (Cart::instance($cart_instance)->content() as $item) {
                            $pure_subtotal += $item->price * $item->qty;
                        }

                        // 2️⃣ Discount
                        $discount_val = (float) str_replace(',', '', Cart::instance($cart_instance)->discount());

                        // 3️⃣ Subtotal setelah diskon
                        $subtotal_after_discount = max($pure_subtotal - $discount_val, 0);

                        // 4️⃣ Tax
                        $tax_val = (float) str_replace(',', '', Cart::instance($cart_instance)->tax());

                        // 5️⃣ Service charge (SETELAH diskon)
                        $service_charge =
                            isFeatureEnabled('summary_service') && $order_type == 'dine_in'
                                ? $subtotal_after_discount * 0.05
                                : 0;

                        // 6️⃣ Delivery
                        $shipping_val = isFeatureEnabled('summary_pkg') ? (float) ($shipping ?? 0) : 0;

                        // 7️⃣ Others
                        $lain_a_val = isFeatureEnabled('summary_others') ? (float) ($lain_a ?? 0) : 0;

                        $lain_b_val = isFeatureEnabled('summary_others') ? (float) ($lain_b ?? 0) : 0;

                        // 8️⃣ GRAND TOTAL FINAL
                        $grand_total =
                            $subtotal_after_discount +
                            $tax_val +
                            $service_charge +
                            $shipping_val +
                            $lain_a_val +
                            $lain_b_val;
                    @endphp

                    <div class="row">
                        <div class="col-lg-7">
                            <input type="hidden" value="{{ $customer_name }}" name="customer_name">
                            <input type="hidden" value="{{ $global_tax }}" name="tax_percentage">
                            <input type="hidden" value="{{ $global_discount }}" name="discount_percentage">
                            <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                            <input type="hidden" name="order_type" value="{{ $order_type }}">
                            <input type="hidden" name="table_id" value="{{ $table_id }}">
                            @foreach (Cart::instance($cart_instance)->content() as $item)
                                <input type="hidden" name="variants[{{ $item->id }}]"
                                    value="{{ json_encode($item->options->variants ?? []) }}">
                            @endforeach
                            <input type="hidden" name="selected_table_ids"
                                value="{{ json_encode($table_ids_array ?? []) }}">
                            <input type="hidden" name="service_charge" value="{{ $service_charge }}">
                            <input type="hidden" name="lain_a"
                                value="{{ isFeatureEnabled('summary_others') ? $lain_a_val : 0 }}">
                            <input type="hidden" name="lain_b"
                                value="{{ isFeatureEnabled('summary_others') ? $lain_b_val : 0 }}">
                            <div class="card p-0 border-1 shadow-sm">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="total_amount">Total Amount <span
                                                        class="text-danger"></span></label>
                                                <input type="hidden" name="total_amount" id="total_amount"
                                                    class="form-control" value="{{ $grand_total }}" readonly required>
                                                <div class="form-group">
                                                    <td> {{ format_currency($grand_total) }}</td>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="paid_amount">Received Amount <span
                                                        class="text-danger"></span></label>
                                                <input type="hidden" id="paid_amount" name="paid_amount"
                                                    class="form-control"placeholder="0"></input>
                                                <div class="form-group" id="lblreceipt">
                                                    Rp 0,00
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="Kembalian">Kembalian <span
                                                        class="text-danger"></span></label>
                                                <input type="hidden" id="kembalian" name="kembalian"
                                                    class="form-control"placeholder="0"></input>
                                                <div class="form-group" id="lblkembalian">
                                                    Rp 0,00
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                <div class="container">
                                    <div class="row">
                                        {{-- <div class="col"> --}}
                                        {{-- {{ dd($payments) }} --}}
                                        @if ($payments->Cash == 'Y')
                                            <div class="form-group col-6">
                                                <tr>
                                                    <td>Cash</td>
                                                    <td>
                                                        <input type="number" id="cash" name="cash"
                                                            onchange="updatekembalian()" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                        @else
                                            <input type="hidden" id="cash" name="cash" class="form-control"
                                                value=0></input>
                                        @endif

                                        @if ($payments->DebitCard == 'Y')
                                            <div class="form-group col-6">
                                                <tr>
                                                    <td>Debit Card</td>
                                                    <td>
                                                        <input type="number" id="debitcard" name="debitcard"
                                                            onchange="updatekembalian()" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                        @else
                                            <input type="hidden" id="debitcard" name="debitcard" value=0></input>
                                        @endif

                                        @if ($payments->Gopay == 'Y')
                                            <div class="form-group col-6">
                                                <tr>
                                                    <td>Go Pay</td>
                                                    <td>
                                                        <input type="number" id="gopay" name="gopay"
                                                            onchange="updatekembalian()" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                        @else
                                            <input type="hidden" id="gopay" name="gopay" class="form-control"
                                                value=0></input>
                                        @endif

                                        @if ($payments->CreditCard == 'Y')
                                            <div class="form-group col-6">
                                                <tr>
                                                    <td>Credit Card</td>
                                                    <td>
                                                        <input type="number" id="creditcard" name="creditcard"
                                                            onchange="updatekembalian()" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                        @else
                                            <input type="hidden" id="creditcard" name="creditcard" value=0></input>
                                        @endif

                                        @if ($payments->OVO == 'Y')
                                            <div class="form-group col-6">
                                                <tr>
                                                    <td>OVO</td>
                                                    <td>
                                                        <input type="number" id="ovo" name="ovo"
                                                            onchange="updatekembalian()" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                        @else
                                            <input type="hidden" id="ovo" name="ovo" class="form-control"
                                                value=0></input>
                                        @endif
                                        {{-- </div> --}}
                                        {{-- <div class="col"> --}}
                                        @if ($payments->ShopeePay == 'Y')
                                            <div class="form-group col-6">
                                                <tr>
                                                    <td>Shopee Pay</td>
                                                    <td>
                                                        <input type="number" id="shopeepay" name="shopeepay"
                                                            onchange="updatekembalian()" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                        @else
                                            <input type="hidden" id="shopeepay" name="shopeepay" value=0></input>
                                        @endif

                                        @if ($payments->Kredivo == 'Y')
                                            <div class="form-group col-6">
                                                <tr>
                                                    <td>Kredivo</td>
                                                    <td>
                                                        <input type="number" id="kredivo" name="kredivo"
                                                            onchange="updatekembalian()" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                        @else
                                            <input type="hidden" id="kredivo" name="kredivo" class="form-control"
                                                value=0></input>
                                        @endif

                                        @if ($payments->Dana == 'Y')
                                            <div class="form-group col-6">
                                                <tr>
                                                    <td>Dana</td>
                                                    <td>
                                                        <input type="number" id="dana" name="dana"
                                                            onchange="updatekembalian()" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                        @else
                                            <input type="hidden" id="dana" name="dana" class="form-control"
                                                value=0></input>
                                        @endif

                                        @if ($payments->GrabPay == 'Y')
                                            <div class="form-group col-6">
                                                <tr>
                                                    <td>Grab Pay</td>
                                                    <td>
                                                        <input type="number" id="grabpay" name="grabpay"
                                                            onchange="updatekembalian()" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                        @else
                                            <input type="hidden" id="grabpay" name="grabpay" class="form-control"
                                                value=0></input>
                                        @endif

                                        @if ($payments->QRIS == 'Y')
                                            <div class="form-group col-6">
                                                <tr>
                                                    <td>QRIS</td>
                                                    <td>
                                                        <input type="number" id="qris" name="qris"
                                                            onchange="updatekembalian()" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                        @else
                                            <input type="hidden" id="qris" name="qris" class="form-control"
                                                value=0></input>
                                        @endif
                                        {{-- </div> --}}
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="col-lg-5">
                            <div class="table-responsive">
                                @php
                                    // Subtotal asli (tanpa diskon)
                                    $pure_subtotal = 0;
                                    foreach (Cart::instance($cart_instance)->content() as $item) {
                                        $pure_subtotal += $item->price * $item->qty;
                                    }

                                    // Discount value
                                    $discount_val = (float) str_replace(
                                        ',',
                                        '',
                                        Cart::instance($cart_instance)->discount(),
                                    );

                                    // Subtotal setelah diskon
                                    $subtotal_after_discount = max($pure_subtotal - $discount_val, 0);

                                    // Service charge dihitung SETELAH diskon
                                    $service_charge =
                                        isFeatureEnabled('summary_service') && $order_type == 'dine_in'
                                            ? $subtotal_after_discount * 0.05
                                            : 0;
                                @endphp

                                <table class="table table-striped">
                                    <tr>
                                        <th>Total Products</th>
                                        <td>
                                            <span class="badge badge-success">
                                                {{ Cart::instance($cart_instance)->count() }}
                                            </span>
                                        </td>
                                    </tr>
                                    {{-- Subtotal (Sebelum Pajak & Biaya Lain) --}}
                                    <tr>
                                        <th>Subtotal</th>
                                        <td>{{ format_currency($pure_subtotal) }}</td>
                                    </tr>

                                    {{-- Order Tax --}}
                                    <tr>
                                        <th>Order Tax ({{ $global_tax }}%)</th>
                                        <td>(+) {{ format_currency(Cart::instance($cart_instance)->tax()) }}</td>
                                    </tr>

                                    {{-- Discount --}}
                                    <tr>
                                        <th>Discount ({{ $global_discount }}%)</th>
                                        <td>(-) {{ format_currency(Cart::instance($cart_instance)->discount()) }}</td>
                                    </tr>

                                    {{-- Delivery (Rule: summary_pkg) --}}
                                    @if (isFeatureEnabled('summary_pkg'))
                                        <tr>
                                            <th>Delivery</th>
                                            <td>(+) {{ format_currency($shipping) }}</td>
                                        </tr>
                                    @endif

                                    {{-- Service Charge (Rule: summary_service DAN Dine In) --}}
                                    @if (isFeatureEnabled('summary_service') && $order_type == 'dine_in')
                                        <tr>
                                            <th>Service Charge (5%)</th>
                                            <td>(+) {{ format_currency($service_charge) }}</td>
                                        </tr>
                                    @endif


                                    {{-- Lain-lain A (Rule: summary_others) --}}
                                    @if (isFeatureEnabled('summary_others') && $lain_a > 0)
                                        <tr>
                                            <th>Lain-lain A</th>
                                            <td>(+) {{ format_currency($lain_a) }}</td>
                                        </tr>
                                    @endif

                                    {{-- Lain-lain B (Rule: summary_others) --}}
                                    @if (isFeatureEnabled('summary_others') && $lain_b > 0)
                                        <tr>
                                            <th>Lain-lain B</th>
                                            <td>(+) {{ format_currency($lain_b) }}</td>
                                        </tr>
                                    @endif

                                    <tr class="text-primary">
                                        <th>Grand Total</th>

                                        <th>
                                            (=) {{ format_currency($grand_total) }}
                                        </th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="actionbutton" disabled>Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
