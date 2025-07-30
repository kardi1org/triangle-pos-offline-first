{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> --}}


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
                            const debitcard = document.getElementById('debitcard');
                            const creditcard = document.getElementById('creditcard');
                            const gopay = document.getElementById('gopay');

                            // Update kembalian's value with cash's value
                            kembalian.value = parseInt(cash.value) + parseInt(debitcard.value) + parseInt(creditcard.value) + parseInt(gopay
                                .value) - parseInt(
                                total_amount.value);
                        }
                    </script>

                    <div class="row">
                        <div class="col-lg-7"> {{-- ==> Ini script yg asli --}}
                            {{-- <div class="col-lg-10"> --}}
                            {{-- <div class="col-lg-8">  --}}
                            {{--   <input type="hidden" value="{{ $customer_id }}" name="customer_id"> --}}
                            <input type="hidden" value="{{ $customer_name }}" name="customer_name">
                            <input type="hidden" value="{{ $global_tax }}" name="tax_percentage">
                            <input type="hidden" value="{{ $global_discount }}" name="discount_percentage">
                            <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {{-- @php
                                            $sub_total = $total_amount  //Cart::instance($cart_instance)->total()
                                            $this->$sub_total = $this->calculateTotal();
                                        @endphp --}}
                                        <label for="total_amount">Total Amount <span
                                                class="text-danger">*</span></label>
                                        <input type="hidden" name="total_amount" id="total_amount" class="form-control"
                                            value="{{ $total_amount }}" readonly required>
                                        {{-- <input type="hidden" name="total_amount" wire:model.blur="total_amount"
                                            class="form-control" value="{{ $total_amount }}"></input> --}}
                                        {{-- <td> {{ format_currency($total_amount) }}</td> --}}
                                        <div class="form-group">
                                            <td> {{ format_currency($total_amount) }}</td>
                                            {{-- <td>Rp {{ number_format($total_amount) }}</td> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="paid_amount">Received Amount <span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="kembalian" name="kembalian"
                                            class="form-control"></input>
                                        {{-- <input type="text" name="paid_amount" wire:model.blur="paid_amount"
                                            class="form-control" value="{{ $total_amount }}"></input> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                {{-- <select class="form-control" name="payment_method" id="payment_method" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Other">Other</option>
                                </select> --}}
                                <div class="container">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
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
                                            <div class="form-group">
                                                <tr>
                                                    <td>Debit Card</td>
                                                    <td>
                                                        <input type="number" name="debitcard"
                                                            onchange="updatekembalian()" id="debitcard" height="30px"
                                                            width="100px" class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                            <div class="form-group">
                                                <tr>
                                                    <td>Go Pay</td>
                                                    <td>
                                                        <input type="number" id="gopay" name="gopay"
                                                            onchange="updatekembalian()" height="30px" width="100px"
                                                            class="form-control"
                                                            onblur="if (this.value == '') {this.value = 0;}"
                                                            onfocus="if (this.value == 0) {this.value = '';}"
                                                            value=0></input>
                                                    </td>
                                                </tr>
                                            </div>
                                            <div class="form-group">
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
                                            <div class="form-group">
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
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
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
                                            <div class="form-group">
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
                                            <div class="form-group">
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
                                            <div class="form-group">
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
                                            <div class="form-group">
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
                                        </div>
                                    </div>
                                </div>
                                {{-- <table class="col-lg-6">
                                  <tr>
                                    <td>Cash</td>
                                    <td>
                                        <input type="text" name="txtcash" wire:model.blur="txtcash" height="30px" width="55px" class="form-control"></input>
                                    </td>
                                  </tr>
                                </table>
                                <table class="col-lg-6">
                                </table>    --}}
                            </div>
                            {{-- --------------------------Add by Chris------------------------- --}}
                            {{-- <div class="form-group"><label for="note">Cash</label>
                                <input type="text" name="txtcash" wire:model.blur="txtcash" class="form-control"></input>
                              </div> --}}
                            {{-- <table class="table">
                                <tr>
                                <th>Cash</th>
                                <td>
                                    <input type="text" name="txtcash" wire:model.blur="txtcash" class="form-control"></input>
                                </td>
                               </tr> <button type="button" color="#fff" data-dismiss="modal">
                               <tr>
                                <td>
                                    {{-- <img class="c-sidebar-brand-full" src="{{ asset('images/qris.png') }}" alt="Site Logo" width="55px">&nbsp;&nbsp;
                                    <img class="c-sidebar-brand-full" src="{{ asset('images/gopay.png') }}" alt="Site Logo" width="55px">&nbsp;&nbsp;
                                    <img class="c-sidebar-brand-full" src="{{ asset('images/ovo.png') }}" alt="Site Logo" width="55px"> --}}
                            {{-- <button type="button" color="#fff" data-dismiss="modal">
                                        <img class="c-sidebar-brand-full" src="{{ asset('images/qris.png') }}" alt="Site Logo" width="55px">
                                    </button> --}}
                            {{-- <button type="button" color="#fff" data-dismiss="modal">
                                        <img class="c-sidebar-brand-full" src="{{ asset('images/gopay.png') }}" alt="Site Logo" height="30px" width="55px">
                                    </button>
                                    <button type="button" color="#fff" data-dismiss="modal">
                                        <img class="c-sidebar-brand-full" src="{{ asset('images/ovo.png') }}" alt="Site Logo" height="30px" width="55px">
                                    </button>
                                    <button type="button" color="#fff" data-dismiss="modal">
                                        <img class="c-sidebar-brand-full" src="{{ asset('images/shopeepay.png') }}" alt="Site Logo" height="30px" width="55px">
                                    </button>
                                    <button type="button" color="#fff" data-dismiss="modal">
                                        <img class="c-sidebar-brand-full" src="{{ asset('images/dana.png') }}" alt="Site Logo" height="30px" width="55px">
                                    </button>
                                    <button type="button" color="#fff" data-dismiss="modal">
                                        <img class="c-sidebar-brand-full" src="{{ asset('images/kredivo.png') }}" alt="Site Logo" height="30px" width="55px">
                                    </button>
                                </td>
                               </tr>
                              </table> --}}
                            {{-- --------------------------------------------------------------- --}}
                            {{-- </div> --}}
                            {{-- <div class="form-group">
                                <label for="note">Note (If Needed)</label>
                                <textarea name="note" id="note" rows="5" class="form-control"></textarea>
                            </div> --}}
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
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
