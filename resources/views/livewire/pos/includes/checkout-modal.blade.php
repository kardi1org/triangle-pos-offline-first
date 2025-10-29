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
            <form id="checkout-form" action="{{ route('app.pos.store') }}" method="POST">
                @csrf
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

                            const cash = document.getElementById('cash');
                            const kembalian = document.getElementById('kembalian');
                            const total_amount = document.getElementById('total_amount');
                            const paid_amount = document.getElementById('paid_amount');
                            const debitcard = document.getElementById('debitcard');
                            const creditcard = document.getElementById('creditcard');
                            const gopay = document.getElementById('gopay');
                            const ovo = document.getElementById('ovo');
                            const shopeepay = document.getElementById('shopeepay');
                            const kredivo = document.getElementById('kredivo');
                            const dana = document.getElementById('dana');
                            const grabpay = document.getElementById('grabpay');
                            const qris = document.getElementById('qris');
                            const actionButton = document.getElementById('actionbutton');

                            // Create our number formatter.
                            const formatter = new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: 'USD',

                                // These options can be used to round to whole numbers.
                                trailingZeroDisplay: 'stripIfInteger', // This is probably what most people
                                // want. It will only stop printing
                                // the fraction when the input
                                // amount is a round number (int)
                                // already. If that's not what you
                                // need, have a look at the options
                                // below.
                                minimumFractionDigits: 0, // This suffices for whole numbers, but will
                                // print 2500.10 as $2,500.1
                                //maximumFractionDigits: 0, // Causes 2500.99 to be printed as $2,501
                            });

                            const rupiah = (number) => {
                                return new Intl.NumberFormat("id-ID", {
                                    style: "currency",
                                    currency: "IDR"
                                }).format(number);
                            }

                            paid_amount.value = (isNaN(parseFloat(cash.value)) ? 0 : parseFloat(cash.value)) +
                                (isNaN(parseFloat(debitcard.value)) ? 0 : parseFloat(debitcard.value)) +
                                (isNaN(parseFloat(creditcard.value)) ? 0 : parseFloat(creditcard.value)) +
                                (isNaN(parseFloat(gopay.value)) ? 0 : parseFloat(gopay.value)) +
                                (isNaN(parseFloat(ovo.value)) ? 0 : parseFloat(ovo.value)) +
                                (isNaN(parseFloat(shopeepay.value)) ? 0 : parseFloat(shopeepay.value)) +
                                (isNaN(parseFloat(kredivo.value)) ? 0 : parseFloat(kredivo.value)) +
                                (isNaN(parseFloat(dana.value)) ? 0 : parseFloat(dana.value)) +
                                (isNaN(parseFloat(grabpay.value)) ? 0 : parseFloat(grabpay.value)) +
                                (isNaN(parseFloat(qris.value)) ? 0 : parseFloat(qris.value))

                            document.getElementById('lblreceipt').innerHTML = rupiah(paid_amount.value);

                            if ((paid_amount.value - parseFloat(total_amount.value)) < 0) {
                                kembalian.value = 0;
                                document.getElementById('lblkembalian').innerHTML = 'Rp 0,00';
                            } else {
                                kembalian.value = paid_amount.value - parseFloat(total_amount.value);
                                document.getElementById('lblkembalian').innerHTML = rupiah(kembalian.value);
                            }

                            if (paid_amount.value < parseFloat(total_amount.value)) {
                                actionButton.disabled = true;
                            } else {
                                actionButton.disabled = false;
                            }
                        }
                    </script>

                    <div class="row">
                        <div class="col-lg-7">
                            <input type="hidden" value="{{ $customer_name }}" name="customer_name">
                            <input type="hidden" value="{{ $global_tax }}" name="tax_percentage">
                            <input type="hidden" value="{{ $global_discount }}" name="discount_percentage">
                            <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                            <div class="card p-0 border-1 shadow-sm">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="total_amount">Total Amount <span
                                                        class="text-danger"></span></label>
                                                <input type="hidden" name="total_amount" id="total_amount"
                                                    class="form-control" value="{{ $total_amount }}" readonly required>
                                                <div class="form-group">
                                                    <td> {{ format_currency($total_amount) }}</td>
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
                                                            onchange="updatekembalian()" height="30px" width="100px"
                                                            class="form-control"
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
                                <table class="table table-striped">
                                    <tr>
                                        <th>Total Products</th>
                                        <td>
                                            <span class="badge badge-success">
                                                {{ Cart::instance($cart_instance)->count() }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Order Tax ({{ $global_tax }}%)</th>
                                        <td>(+) {{ format_currency(Cart::instance($cart_instance)->tax()) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Discount ({{ $global_discount }}%)</th>
                                        <td>(-) {{ format_currency(Cart::instance($cart_instance)->discount()) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Shipping</th>
                                        <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                                        <td>(+) {{ format_currency($shipping) }}</td>
                                    </tr>
                                    <tr class="text-primary">
                                        <th>Grand Total</th>
                                        @php
                                            $total_with_shipping =
                                                Cart::instance($cart_instance)->total() + (float) $shipping;
                                            // $sub_total = Cart::instance($cart_instance)->total()
                                        @endphp
                                        <th>
                                            (=) {{ format_currency($total_with_shipping) }}
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
