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
                @if ($alertMessage)
                    <div id="autoHideAlert" class="alert alert-{{ $alertType }} alert-dismissible fade show"
                        role="alert">
                        <div class="alert-body">
                            <span>{{ $alertMessage }}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    </div>
                @endif


                {{--   <form id="checkout-form" action="{{ route('app.pos.store') }}" method="POST">   --}} {{-- --Add by Chris --}}
                {{-- route('sale-payments.index', $data->id) //route('sales.cetakstruk', $salesId->sale_id) --}}
                <form id="checkout-form">

                    {{--  <form id="checkout-form" action="{{ route('app.pos.print') }}" method="GET">   --}}{{-- --Add by Chris --}}
                    @csrf
                    <!-- ✅ Toggle Dine In / Take Out -->
                    <div class="form-group mb-2">
                        <div class="btn-group" role="group" aria-label="Order Type">
                            <button type="button"
                                class="btn btn-sm {{ $order_type == 'dine_in' ? 'btn-primary' : 'btn-outline-primary' }}"
                                wire:click="$set('order_type', 'dine_in')">
                                Dine In
                            </button>
                            <button type="button"
                                class="btn btn-sm {{ $order_type == 'take_out' ? 'btn-primary' : 'btn-outline-primary' }}"
                                wire:click="$set('order_type', 'take_out')">
                                Take Out
                            </button>
                        </div>

                        {{-- ✅ hidden input agar ikut terkirim saat submit form --}}
                        <input type="hidden" name="order_type" value="{{ $order_type }}">
                    </div>

                    <div class="form-group">
                        <label for="customer_name">Customer Name<span class="text-danger"> *</span></label>

                        <div class="input-group">

                            <input type="text" id="customer_name" name="customer_name" style="margin-right: 2px;"
                                wire:model.blur="customer_name" class="form-control" placeholder="Enter customer name">

                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal"
                                data-target="#tableSelectionModal" title="Select Table">
                                Select Table <i class="bi bi-grid-3x3-gap"></i>
                            </button>
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                            </div>

                            @if (!empty($selectedTableNames))
                                <div class="mt-2">

                                    <div class="d-flex align-items-center flex-wrap">

                                        <small class="text-muted mb-1" style="margin-right: 4px;">Table:</small>

                                        <div class="d-flex flex-wrap">
                                            @php
                                                $names = explode(', ', $selectedTableNames);
                                            @endphp
                                            @foreach ($names as $index => $name)
                                                <span class="badge bg-primary text-white mb-1 p-1 text-sm"
                                                    style="margin-right: 2px;">
                                                    {{ $name }}
                                                    <i class="bi bi-x-circle ms-1"
                                                        style="font-size: 0.8em; cursor: pointer;"
                                                        wire:click="removeTableByIndex({{ $index }})"
                                                        title="Hapus Meja"></i>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <input type="hidden" name="table_ids" wire:model="table_ids_array">
                                </div>
                            @endif

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
                                            {{-- @include('livewire.includes.product-cart-modal') --}}
                                            {{-- Tambahkan variant --}}
                                            @php
                                                // Pastikan kunci yang digunakan tetap 'variants'
                                                $variantsJson = json_encode($cart_item->options->variants ?? []);
                                                // Encode seluruh JSON string menjadi Base64
                                                $variantsBase64 = base64_encode($variantsJson);
                                            @endphp

                                            <span class="badge badge-info ms-1" style="cursor:pointer"
                                                onclick="openVariantModal(
                                                '{{ $cart_item->id }}',
                                                '{{ $cart_item->qty }}',
                                                '{{ $order_type }}',
                                                '{{ $cart_item->name }}',
                                                '{{ $variantsBase64 }}' {{-- <== MELEWATKAN STRING BASE64 --}}
                                                )">
                                                {{-- onclick="openVariantModal('{{ $cart_item->rowId }}', '{{ $cart_item->qty }}', '{{ $order_type }}', '{{ $cart_item->name }}', @json($cart_item->options->variant_detail ?? []))"> --}}

                                                Instruction
                                            </span>
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

                    @include('livewire.includes.product-cart-style')

                    @include('livewire.includes.product-cart-summary')

                    <div class="form-group d-flex justify-content-center flex-wrap mb-0">
                        <button type="button" wire:click="saveOrderPending" wire:loading.attr="disabled"
                            wire:target="saveOrderPending" class="btn btn-pill btn-primary mr-2 mb-1"
                            @disabled($cart_items->isEmpty() || $total_amount <= 0)>
                            <span wire:loading.remove wire:target="saveOrderPending">
                                <i class="bi bi-check"></i> Save Order
                            </span>
                            <span wire:loading wire:target="saveOrderPending">
                                <i class="bi bi-hourglass"></i> Saving...
                            </span>
                        </button>
                        {{-- ✅ List Pending Orders --}}
                        <button type="button" class="btn btn-pill btn-info mr-2 mb-1"
                            wire:click="$dispatch('show-pending-orders-modal')">
                            List Orders
                        </button>
                        {{-- ✅ Proceed --}}
                        <button type="button" wire:click="proceed" wire:loading.attr="disabled"
                            wire:target="proceed" class="btn btn-pill btn-primary mr-2 mb-1"
                            @disabled($cart_items->isEmpty() || $total_amount <= 0)>
                            <span wire:loading.remove wire:target="proceed">
                                <i class="bi bi-check"></i> Proceed
                            </span>
                            <span wire:loading wire:target="proceed">
                                <i class="bi bi-hourglass"></i> Processing...
                            </span>
                        </button>
                        {{-- ✅ Reset --}}
                        <button type="button" wire:click="resetCart" wire:loading.attr="disabled"
                            wire:target="resetCart"
                            class="btn btn-pill btn-danger mb-1 w-auto"{{ $total_amount == 0 || $cart_items->isEmpty() ? 'disabled' : '' }}>
                            <span wire:loading.remove wire:target="resetCart">
                                <i class="bi bi-x"></i> Reset
                            </span>
                            <span wire:loading wire:target="resetCart">
                                <i class="bi bi-hourglass"></i> Clearing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @include('livewire.includes.product-cart-all-modal')



        @include('livewire.pos.includes.checkout-modal')

        @push('page_scripts')
            @include('livewire.includes.product-cart-scriptjs')
        @endpush
    </div>
