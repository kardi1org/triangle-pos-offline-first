<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div>
                @if (session()->has('message'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            <span>{{ session('message') }}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    </div>
                @endif
                {{--   <form id="checkout-form" action="{{ route('app.pos.store') }}" method="POST">   --}} {{-- --Add by Chris --}}
                {{-- route('sale-payments.index', $data->id) //route('sales.cetakstruk', $salesId->sale_id) --}}
                <form id="checkout-form" action="{{ route('save.saveorder') }}" method="POST"> {{-- --Add by Chris --}}
                    {{--  <form id="checkout-form" action="{{ route('app.pos.print') }}" method="GET">   --}}{{-- --Add by Chris --}}
                    @csrf
                    <div class="form-group">
                        <label for="customer_id">Customer Name<span class="text-danger"> *</span></label>
                        <div class="input-group">
                            <input type="text" id="customer_name" name="customer_name"
                                wire:model.blur="customer_name" class="form-control"></input>
                        </div>
                    </div>

                    <div class="cart-list">
                        @if ($cart_items->isNotEmpty())

                            {{-- === Header Kolom (Desktop) === --}}
                            <div class="d-none d-md-flex font-weight-bold text-center border-bottom py-2 bg-light">
                                <div class="col-md-4 text-left">Product</div>
                                <div class="col-md-2">Price</div>
                                <div class="col-md-4">Quantity</div>
                                <div class="col-md-2">Action</div>
                            </div>

                            {{-- === Daftar Produk === --}}
                            @foreach ($cart_items as $cart_item)
                                <div class="cart-item border-bottom py-2">
                                    <div class="row align-items-center text-center text-md-left">

                                        {{-- === Product === --}}
                                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                                            <div class="d-md-none text-muted small font-weight-bold mb-1">Product</div>
                                            <strong>{{ $cart_item->name }}</strong><br>
                                            <span class="badge badge-success">{{ $cart_item->options->code }}</span>
                                            @include('livewire.includes.product-cart-modal')
                                        </div>

                                        {{-- === Price === --}}
                                        <div class="col-6 col-md-2 mb-2 mb-md-0">
                                            <div class="d-md-none text-muted small font-weight-bold mb-1">Price</div>
                                            Rp {{ number_format($cart_item->price, 0, ',', '.') }}
                                        </div>

                                        {{-- === Quantity + Action (gabung di mobile) === --}}
                                        <div
                                            class="col-6 col-md-6 d-flex align-items-center justify-content-between flex-wrap">
                                            <div class="quantity-section">
                                                <div class="d-md-none text-muted small font-weight-bold mb-1">Quantity
                                                </div>
                                                @include('livewire.includes.product-cart-quantity')
                                            </div>

                                            {{-- Action muncul di samping quantity di HP --}}
                                            <div class="action-section mt-2 mt-md-0 text-md-center">

                                                <a href="#"
                                                    wire:click.prevent="removeItem('{{ $cart_item->rowId }}')"
                                                    class="text-danger ms-3">
                                                    <i class="bi bi-x-circle font-2xl"></i>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-danger py-3">
                                Please search & select products!
                            </div>
                        @endif
                    </div>

                    <style>
                        .cart-list {
                            border-radius: 6px;
                            overflow: hidden;
                            background-color: #fff;
                        }

                        .cart-item {
                            margin-bottom: 0;
                            transition: background 0.15s ease-in-out;
                        }

                        .cart-item:hover {
                            background: #f8f9fa;
                        }

                        /* ===== Versi Desktop ===== */
                        @media (min-width: 768px) {

                            /* Geser quantity lebih ke kanan */
                            .cart-item .quantity-section {
                                transform: translateX(20px);
                            }

                            /* Geser tombol action sedikit ke kiri */
                            .cart-item .action-section {
                                transform: translateX(-10px);
                            }
                        }

                        /* ===== Versi Mobile ===== */
                        @media (max-width: 767.98px) {

                            .cart-item .col-12,
                            .cart-item .col-6 {
                                text-align: left !important;
                            }

                            .cart-item {
                                padding-left: 10px;
                                padding-right: 10px;
                            }

                            /* Quantity & Action sejajar di HP */
                            .cart-item .col-6.d-flex {
                                justify-content: space-between;
                                align-items: center;
                            }

                            .cart-item .action-section {
                                margin-left: 10px;
                                transform: none;
                                /* posisi normal di HP */
                            }

                            .cart-item .quantity-section {
                                transform: none;
                                /* posisi normal di HP */
                            }
                        }

                        /* Lebarkan input quantity */
                        .cart-item input[type="number"] {
                            min-width: 60px;
                            max-width: 100px;
                            text-align: center;
                        }

                        .cart-item .btn {
                            padding: 0.25rem 0.5rem;
                        }
                    </style>



                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between py-1">
                                <span>Order Tax ({{ $global_tax }}%)</span>
                                <span>(+)
                                    {{ format_currency(Cart::instance($cart_instance)->tax()) }}</span>
                            </div>

                            <div class="d-flex justify-content-between py-1">
                                <span>Discount ({{ $global_discount }}%)</span>
                                <span>(-)
                                    {{ format_currency(Cart::instance($cart_instance)->discount()) }}</span>
                            </div>

                            <div class="d-flex justify-content-between py-1">
                                <span>Shipping</span>
                                <input type="hidden" name="shipping_amount" value="{{ $shipping }}">
                                <span>(+) {{ format_currency($shipping) }}</span>
                            </div>

                            <hr>

                            @php
                                $total_with_shipping = Cart::instance($cart_instance)->total() + (float) $shipping;
                            @endphp

                            <div class="d-flex justify-content-between py-2 text-primary font-weight-bold">
                                <span>Grand Total</span>
                                <span>(=) {{ format_currency($total_with_shipping) }}</span>
                            </div>
                        </div>
                    </div>


                    <div class="form-row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="tax_percentage">Order Tax (%)</label>
                                <input wire:model.blur="global_tax" type="number" class="form-control" min="0"
                                    max="100" value="{{ $global_tax }}" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="discount_percentage">Discount (%)</label>
                                <input wire:model.blur="global_discount" type="number" class="form-control"
                                    min="0" max="100" value="{{ $global_discount }}" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="shipping_amount">Shipping</label>
                                <input wire:model.blur="shipping" type="number" class="form-control" min="0"
                                    value="0" required step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="form-group d-flex justify-content-center flex-wrap mb-0">
                        <button onclick="saveOrder()" type="submit" value="save"
                            class="btn btn-pill btn-primary mr-2 mb-1 align-content-center"{{ $total_amount == 0 || $cart_items->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-check"></i> Save Order</button>
                        <button onclick="getdatacart()" type="submit" class="btn btn-pill btn-primary mr-2 mb-1">
                            <i class="bi bi-check "></i> Get Data &nbsp;</button>
                        <button wire:loading.attr="disabled" wire:click="proceed" type="button"
                            class="btn btn-pill btn-primary mr-2 mb-1"
                            {{ $total_amount == 0 || $cart_items->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-check"></i> Proceed &nbsp;
                        </button>
                        <button wire:loading.attr="disabled" wire:click="resetCart" type="button"
                            class="btn btn-pill btn-danger mb-1 w-auto"><i class="bi bi-x"></i>
                            Reset &nbsp;

                        </button>
                    </div>
                </form>
            </div>
        </div>

        @include('livewire.pos.includes.checkout-modal')

    </div>
