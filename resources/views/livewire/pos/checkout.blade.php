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

                                                Variant
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
                    <style>
                        .modal.is-blurred .modal-dialog {
                            /* Filter blur hanya diterapkan pada dialog */
                            filter: blur(4px);
                            pointer-events: none;
                        }
                    </style>

                    <style>
                        .table-grid-container {
                            display: grid;
                            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                            gap: 15px;
                            padding: 10px;
                        }

                        .table-card {
                            border: 1px solid #ddd;
                            border-radius: 8px;
                            padding: 15px;
                            text-align: center;
                            cursor: pointer;
                            transition: all 0.2s;
                            position: relative;
                            overflow: hidden;
                        }

                        /* Style untuk Meja yang terpilih */
                        .table-card.selected {
                            border: 3px solid #007bff;
                            /* Warna biru untuk yang terpilih */
                            background-color: #e9f5ff;
                        }

                        /* Style berdasarkan Status */
                        .table-card.available {
                            background-color: #e6ffed;
                            /* Hijau muda */
                            border-color: #28a745;
                        }

                        .table-card.occupied {
                            background-color: #fff0f0;
                            /* Merah muda */
                            border-color: #dc3545;
                            cursor: not-allowed;
                            opacity: 0.6;
                        }

                        .table-card.cleaning {
                            background-color: #fffbe6;
                            /* Kuning muda */
                            border-color: #ffc107;
                        }

                        .table-card h3 {
                            margin: 0;
                            font-size: 1.5rem;
                            font-weight: bold;
                        }

                        .table-status {
                            margin-top: 10px;
                            font-size: 0.9rem;
                        }

                        .selection-overlay {
                            position: absolute;
                            top: 5px;
                            right: 5px;
                            color: #007bff;
                            font-size: 1.5rem;
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
                                <input wire:model.blur="global_tax" type="number" class="form-control"
                                    min="0" max="100" value="{{ $global_tax }}" required>
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
                        <input type="hidden" value="{{ $global_tax }}" name="tax_percentage">
                        <input type="hidden" value="{{ $global_discount }}" name="discount_percentage">
                        <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                    </div>

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

        <!-- ✅ Modal List Meja -->
        <div class="modal fade" id="tableSelectionModal" tabindex="-1" role="dialog"
            aria-labelledby="tableSelectionModalLabel" aria-hidden="true" wire:ignore.self>

            <div class="modal-dialog modal-lg" role="document" x-data="{
                localSelectedIds: [],

                // Fungsi untuk sinkronisasi dari Livewire ke Alpine
                syncLivewireState() {
                    // 🚀 PERBAIKAN: Konversi semua elemen array dari Livewire menjadi Integer
                    this.localSelectedIds = $wire.table_ids_array.map(id => parseInt(id));
                    // console.log('Synced IDs:', this.localSelectedIds); // DEBUG: Coba cek di console browser
                },

                // Fungsi untuk toggle ID di state lokal
                toggleLocalTable(id) {
                    // 🚀 PERBAIKAN: Konversi ID yang diterima dari klik ke Integer
                    const intId = parseInt(id);
                    const index = this.localSelectedIds.indexOf(intId);

                    if (index > -1) {
                        this.localSelectedIds.splice(index, 1);
                    } else {
                        this.localSelectedIds.push(intId);
                    }
                }
            }" x-init="syncLivewireState()"
                x-on:show.bs.modal="syncLivewireState()" x-on:sync-table-selection.window="syncLivewireState()">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tableSelectionModalLabel">Pilih Meja</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="table-grid-container" id="tableGridContainer">
                            @foreach ($tables as $table)
                                {{-- 🛑 KODE PHP PENENTU STATUS (statusClass, statusIcon, statusText) DIHAPUS --}}

                                <div class="table-card" {{-- Hanya bergantung pada localSelectedIds --}}
                                    :class="{ 'selected': localSelectedIds.includes({{ $table->id }}) }"
                                    @click.stop="toggleLocalTable({{ $table->id }})">

                                    <div class="table-info">
                                        <h3>{{ $table->no_meja }}</h3>
                                        <p>{{ $table->name }}</p>
                                    </div>

                                    {{-- 🛑 DIV table-status DIHAPUS, Ganti dengan info PAX sederhana --}}
                                    <div class="table-status-simple">
                                        <span>Kapasitas: {{ $table->qty_pax }} Pax</span>
                                    </div>

                                    <div class="selection-overlay"
                                        x-show="localSelectedIds.includes({{ $table->id }})">
                                        <i class="bi bi-check-lg"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>

                        <button type="button" class="btn btn-primary" data-dismiss="modal"
                            @click="$wire.set('table_ids_array', localSelectedIds); $wire.call('updateNameString');">
                            Konfirmasi Pilihan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Modal List Pending Orders -->
        <div wire:ignore.self class="modal fade" id="pendingOrdersModal" tabindex="-1" role="dialog"
            aria-labelledby="pendingOrdersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="pendingOrdersModalLabel">List Orders</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if (collect($this->pendingOrders)->isEmpty())
                            <div class="text-center text-muted py-3">
                                No pending orders found.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Reference</th>
                                            <th>Customer</th>
                                            <th>Table</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($this->pendingOrders as $order)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $order->reference }}</td>
                                                <td>{{ $order->customer_name }}</td>
                                                <td>
                                                    @if (isset($order->table_names) && is_array($order->table_names) && count($order->table_names) > 0)
                                                        {{ implode(', ', $order->table_names) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $order->date }}</td>
                                                <td>{{ format_currency($order->total_amount) }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        <button wire:click="previewOrder({{ $order->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="previewOrder({{ $order->id }})"
                                                            class="btn btn-sm btn-secondary mr-1">

                                                            <span wire:loading
                                                                wire:target="previewOrder({{ $order->id }})">
                                                                ...
                                                            </span>
                                                            <span wire:loading.remove
                                                                wire:target="previewOrder({{ $order->id }})">
                                                                Print
                                                            </span>
                                                        </button>
                                                        <button wire:click="showOrderDetail({{ $order->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="showOrderDetail({{ $order->id }})"
                                                            class="btn btn-sm btn-info mr-1">

                                                            <span wire:loading
                                                                wire:target="showOrderDetail({{ $order->id }})">
                                                                Loading...
                                                            </span>

                                                            <span wire:loading.remove
                                                                wire:target="showOrderDetail({{ $order->id }})">
                                                                Detail
                                                            </span>
                                                        </button>
                                                        <button wire:click="restorePendingOrder({{ $order->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="restorePendingOrder({{ $order->id }})"
                                                            class="btn btn-sm btn-success">

                                                            <span wire:loading
                                                                wire:target="restorePendingOrder({{ $order->id }})">
                                                                Processing...
                                                            </span>

                                                            <span wire:loading.remove
                                                                wire:target="restorePendingOrder({{ $order->id }})">
                                                                Select
                                                            </span>
                                                        </button>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <!-- ✅ Footer dengan tombol Close -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <style>
            /* =======================
               PREVIEW (SCREEN)
            ======================= */
            #print-area {
                font-family: monospace;
                font-size: 12px;
                line-height: 1.35;
                color: #000;
            }

            /* =======================
               PRINT THERMAL MODE
            ======================= */
            @media print {

                /* RESET TOTAL */
                @page {
                    size: auto;
                    margin: 0;
                    /* 🔥 WAJIB */
                }

                html,
                body {
                    margin: 0;
                    padding: 0;
                    height: auto;
                    overflow: visible;
                }

                /* SEMBUNYIKAN SEMUA */
                body * {
                    visibility: hidden !important;
                }

                /* TAMPILKAN HANYA AREA PRINT */
                #print-area,
                #print-area * {
                    visibility: visible !important;
                }

                #print-area {
                    position: absolute;
                    top: 0;
                    left: 0;

                    width: 100%;
                    max-width: 80mm;
                    /* thermal 80mm */
                    padding: 2px 3px;
                    /* 🔥 super irit */
                    margin: 0;

                    box-sizing: border-box;
                }

                /* HILANGKAN MODAL BOOTSTRAP */
                .modal,
                .modal-dialog,
                .modal-content,
                .modal-header,
                .modal-footer,
                .modal-backdrop,
                .close,
                button {
                    display: none !important;
                }

                /* TEKS */
                h4,
                h5 {
                    margin: 3px 0;
                    font-size: 13px;
                    text-align: center;
                }

                div {
                    margin: 0;
                    padding: 0;
                }

                hr {
                    border: 0;
                    border-top: 1px dashed #000;
                    margin: 4px 0;
                }
            }

            /* =======================
               MODE PRINTER BIASA (A4)
               Opsional manual
            ======================= */
            #print-area.print-a4 {
                max-width: 210mm;
                font-size: 13px;
                padding: 10px;
            }
        </style>

        <!-- ✅ Print preview Order dapur -->
        <div wire:ignore.self class="modal fade" id="kitchenPreviewModal" tabindex="-1" role="dialog"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    {{-- Header Modal --}}
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">Kitchen Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            {{-- ✅ TAMBAHKAN ONCLICK INI --}} onclick="unblurPendingOrdersModal()">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    @if ($previewOrderData)
                        <div class="modal-body" id="print-area"
                            style="font-family: monospace; font-size: 12px; line-height: 1.35;">

                            {{-- HEADER --}}
                            <div style="text-align:center; margin-bottom:6px;">
                                <strong style="font-size:14px;">KITCHEN ORDER</strong><br>
                                <span>-----------------------------</span>
                            </div>

                            {{-- INFO ORDER --}}
                            <div style="margin-bottom:6px;">
                                <div>Ref : {{ $previewOrderData['reference'] }}</div>
                                <div>Type: <strong>{{ strtoupper($previewOrderData['typeOrder']) }}</strong></div>
                                <div>Cust: {{ $previewOrderData['customer_name'] }}</div>
                                <div>Meja: {{ $previewOrderData['meja_name'] }}</div>
                                <div>Tgl : {{ $previewOrderData['date'] }}</div>
                            </div>

                            <div>-----------------------------</div>

                            {{-- ITEMS --}}
                            @foreach ($previewOrderData['details'] as $item)
                                @php
                                    $variantDetail = $item['variant_detail'] ?? null;
                                    $variants = [];
                                    $aggregatedVariants = [];

                                    if (isset($variantDetail)) {
                                        if (
                                            is_array($variantDetail) &&
                                            !empty($variantDetail) &&
                                            $variantDetail !== [[]]
                                        ) {
                                            $variants = $variantDetail;
                                        } elseif (is_string($variantDetail)) {
                                            $decoded = json_decode($variantDetail, true);
                                            if (is_array($decoded) && !empty($decoded)) {
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

                                {{-- NAMA PRODUK --}}
                                <div style="margin-top:6px;">
                                    <strong>{{ strtoupper($item['product_name']) }}</strong>
                                    <span style="float:right;">x{{ $item['quantity'] }}</span>
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
                                        - TYPE {{ strtoupper($previewOrderData['typeOrder']) }}
                                    </div>
                                @endif

                                <div>-----------------------------</div>
                            @endforeach

                            {{-- FOOTER --}}
                            <div style="text-align:center; margin-top:6px;">
                                <strong>--- END ORDER ---</strong>
                            </div>
                        </div>
                        <div style="height:1px;"></div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                {{-- ✅ TAMBAHKAN ONCLICK INI --}} onclick="unblurPendingOrdersModal()">Close</button>

                            <button type="button" class="btn btn-primary" onclick="printKOT()">Print</button>
                        </div>
                    @else
                        <div class="modal-body text-center text-muted">Reload data...</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ✅ Modal Detail Order -->
        <div wire:ignore.self class="modal fade" id="orderDetailModal" tabindex="-1" role="dialog"
            aria-labelledby="orderDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="orderDetailModalLabel">Order Detail</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @if ($selectedOrderDetails && $selectedOrderDetails->count() > 0)
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedOrderDetails as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->product_name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ format_currency($item->unit_price) }}</td>
                                                <td>{{ format_currency($item->sub_total) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- ✅ Ringkasan Total di Sebelah Kanan -->
                            <div class="d-flex justify-content-end">
                                <div class="border-top pt-2" style="width: 300px;">
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Order Tax ({{ $selectedOrderSummary['tax_percentage'] ?? 0 }}%)</span>
                                        <span>(+)
                                            {{ format_currency($selectedOrderSummary['tax_amount'] ?? 0) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Discount
                                            ({{ $selectedOrderSummary['discount_percentage'] ?? 0 }}%)</span>
                                        <span>(-)
                                            {{ format_currency($selectedOrderSummary['discount_amount'] ?? 0) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Shipping</span>
                                        <span>(+)
                                            {{ format_currency($selectedOrderSummary['shipping_amount'] ?? 0) }}</span>
                                    </div>
                                    <hr class="my-1">
                                    <div class="d-flex justify-content-between font-weight-bold">
                                        <span>Total</span>
                                        <span>{{ format_currency($selectedOrderSummary['total_amount'] ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-muted py-3">No order details found.</div>
                        @endif
                    </div>

                    <!-- ✅ Tombol Close -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Modal Variant -->
        <div wire:ignore.self class="modal fade" id="variantModal" tabindex="-1"
            aria-labelledby="variantModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="variantModalLabel">Product Variants</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>

                    </div>
                    <div class="modal-body">
                        <div id="variantModalContent"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveVariantData()">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Modal List Variant -->
        <div class="modal fade" id="variantListModal" tabindex="-1" aria-labelledby="variantListLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-3">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold" id="variantListLabel">Select Variant</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body" id="variantListContent">
                        <!-- diisi via AJAX -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>

                        <button type="button" class="btn btn-success btn-sm" id="applyToAllVariants">Apply for
                            All</button>
                        <button type="button" class="btn btn-primary btn-sm"
                            id="selectVariantConfirm">Apply</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Modal Print Struk -->
        <div class="modal fade" id="printReceiptModal" tabindex="-1" aria-labelledby="printReceiptModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="printReceiptModalLabel">Cetak Struk Penjualan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="receiptContent">
                            Loading...
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" id="printButton"><i
                                class="bi bi-printer"></i> Print Struk</button>
                        <!-- 🆕 Tombol Kitchen Order -->
                        <button type="button" class="btn btn-warning" id="kitchenOrderButton">
                            <i class="bi bi-printer"></i> Print Kitchen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Modal Preview Kitchen Order -->
        <div class="modal fade" id="kitchenOrderModal" tabindex="-1" aria-labelledby="kitchenOrderModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="kitchenOrderModalLabel">
                            Kitchen Order
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div id="kitchenOrderContent">
                            Loading...
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button class="btn btn-danger" id="printKitchenButton">
                            <i class="bi bi-printer"></i> Print Kitchen
                        </button>
                    </div>

                </div>
            </div>
        </div>



        @include('livewire.pos.includes.checkout-modal')

        @push('page_scripts')
            <script>
                document.addEventListener('livewire:navigated', () => {
                    const livewire = window.Livewire;

                    // Saat event auto-hide dipicu dari Livewire
                    livewire.on('auto-hide-alert', () => {
                        setTimeout(() => {
                            const alert = document.getElementById('autoHideAlert');
                            if (alert) {
                                alert.classList.remove('show');
                                alert.classList.add('fade');
                                setTimeout(() => alert.remove(), 500);
                            }
                        }, 3000);
                    });

                    // Event baru untuk menghapus alert dari state Livewire
                    livewire.on('clear-alert-after', (delay = 3000) => {
                        setTimeout(() => {
                            livewire.dispatch('clear-alert');
                        }, delay);
                    });
                });
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    // 🔹 Tutup modal dan bersihkan backdrop
                    window.Livewire.on('close-pending-orders-modal', () => {
                        const modal = $('#pendingOrdersModal');
                        modal.modal('hide');

                        // Tunggu sedikit, lalu bersihkan backdrop & class yang tersisa
                        setTimeout(() => {
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            $('body').css({
                                'overflow': 'auto',
                                'padding-right': '0'
                            });
                        }, 800);
                    });

                    // 🔹 Tutup modal detail
                    window.Livewire.on('close-order-detail-modal', () => {
                        const modal = $('#orderDetailModal');
                        modal.modal('hide');
                        setTimeout(() => {
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open').css({
                                'overflow': 'auto',
                                'padding-right': '0'
                            });
                        }, 800);
                    });

                    // 🔹 Buka modal detail
                    window.Livewire.on('show-order-detail-modal', () => {
                        $('#orderDetailModal').modal('show');
                    });

                    // 🔹 Buka modal list orders (pastikan data tampil)
                    window.Livewire.on('show-pending-orders-modal', () => {
                        // Bersihkan dulu backdrop
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css({
                            'overflow': 'auto',
                            'padding-right': '0'
                        });

                        // Tunggu sedikit biar render Livewire selesai, baru buka modal
                        setTimeout(() => {
                            Livewire.dispatch('reloadPendingOrders'); // 🟢 trigger refresh data
                            $('#pendingOrdersModal').modal('show');
                        }, 400);
                    });

                    // 🔹 Manual refresh modal state (bersihkan blur)
                    Livewire.on('refresh-modal-state', () => {
                        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style = '';
                    });
                });
            </script>
            <script>
                document.addEventListener('livewire:load', function() {
                    Livewire.on('variantUpdated', () => {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css('overflow', 'auto');
                    });
                });
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    let currentInputTarget = null; // Menyimpan input variant aktif

                    let variantSession = {};

                    function normalizeVariantSession(productId, qty) {
                        if (!variantSession[productId]) return;

                        // Filter hanya index yang <= qty terbaru
                        variantSession[productId] = variantSession[productId]
                            .filter(v => v.index <= qty);

                        // Re-index ulang jika diperlukan
                        variantSession[productId].forEach((v, i) => {
                            v.index = v.index; // posisi tetap sesuai order
                        });
                    }


                    // 🔹 Buka modal variant utama

                    window.openVariantModal = function(productId, qty, defaultOrderType, productName, variantDetail = '') {

                        let variants = []; // Array untuk menampung data varian yang sudah diparse (dari Base64)

                        // -----------------------------------------------------------
                        // 1. BASE64 DECODE UNTUK DATA ORDER PENDING (Fix SyntaxError)
                        // -----------------------------------------------------------
                        // Jika input adalah string (Base64 dari order pending), dekode dan parse.
                        if (typeof variantDetail === 'string' && variantDetail.trim() !== "") {
                            try {
                                // 1. Dekode Base64 string ke JSON string (menggunakan atob)
                                const jsonString = atob(variantDetail);

                                // 2. Parse JSON string ke objek/array Javascript
                                variants = JSON.parse(jsonString);

                                // Pastikan hasil akhirnya adalah array
                                if (!Array.isArray(variants)) {
                                    variants = [];
                                }

                                console.log('✅ Varian Berhasil Dimuat dari Base64:', variants);

                            } catch (e) {
                                console.error("❌ Error saat dekode/parse JSON varian:", e);
                                variants = [];
                            }
                        } else {
                            // Jika input bukan string (mode normal/tanpa Base64)
                            variants = [];
                        }

                        // 🛑 Baris yang ini tidak perlu lagi karena 'variants' sudah didefinisikan sebagai array
                        // if (!Array.isArray(variantDetail)) { variantDetail = []; }

                        normalizeVariantSession(productId, qty); // 🟢 Logika Session tetap dipakai

                        const modalContent = document.getElementById('variantModalContent');
                        modalContent.innerHTML = '';
                        document.getElementById('variantModalLabel').innerText = `${productName} - Variants`;
                        modalContent.dataset.productId = productId;

                        let html = `
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;">#</th>
                                <th style="width:120px;">Type Order</th>
                                <th>Variant</th>
                            </tr>
                        </thead>
                        <tbody>`;

                        for (let i = 1; i <= qty; i++) {

                            // -----------------------------------------------------------
                            // 2. MEMUAT DATA VARIAN KE DALAM HTML (Fix ReferenceError)
                            // -----------------------------------------------------------
                            const prefillData = variants[i - 1] || {};

                            const prefillVariant = prefillData.variant || '';

                            // 🔥 FIX ReferenceError: Deklarasi prefillTypeOrder sebelum digunakan
                            const prefillTypeOrder = prefillData.typeOrder || defaultOrderType;

                            // Gunakan prefillTypeOrder yang sudah didefinisikan
                            const dineActive = prefillTypeOrder === 'dine_in' ? 'btn-primary' : 'btn-outline-primary';
                            const takeActive = prefillTypeOrder === 'take_out' ? 'btn-primary' : 'btn-outline-primary';

                            // -----------------------------------------------------------

                            html += `
                    <tr>
                        <td class="text-center">${i}</td>
                        <td>
                            <div class="btn-group btn-group-sm w-100">
                                <button type="button" class="btn ${dineActive} type-btn px-2 py-1" style="width:66px;"
                                        data-type="dine_in" data-index="${i}">
                                    Dine In
                                </button>
                                <button type="button" class="btn ${takeActive} type-btn px-2 py-1" style="width:66px;"
                                        data-type="take_out" data-index="${i}">
                                    Take Out
                                </button>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-md">
                                <input type="text"
                                    class="form-control form-control-sm variant-input rounded-sm"
                                    readonly id="variant-input-${i}" value="${prefillVariant}">
                                <button type="button" class="btn btn-outline-secondary btn-sm select-variant-btn ml-1 d-none"
                                        data-index="${i}" data-product-id="${productId}">
                                    Select
                                </button>
                            </div>
                        </td>
                    </tr>`;
                        }

                        html += '</tbody></table></div>';
                        modalContent.innerHTML = html;

                        // ===============================================================================
                        // 🛑 PENTING: LOGIKA PENGISIAN ULANG DARI variantDetail (ORDER PENDING)
                        //            dan variantSession (MODE NORMAL) TELAH DIHAPUS DARI SINI
                        //            karena sudah dihandle di dalam loop 'for' di atas.
                        // ===============================================================================

                        // 🔹 Toggle type order
                        modalContent.querySelectorAll('.type-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const parent = this.closest('tr');
                                parent.querySelectorAll('.type-btn').forEach(b => {
                                    b.classList.remove('btn-primary');
                                    b.classList.add('btn-outline-primary');
                                });
                                this.classList.remove('btn-outline-primary');
                                this.classList.add('btn-primary');
                            });
                        });

                        // 🔹 Tombol select variant → buka list variant
                        modalContent.querySelectorAll('.select-variant-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                currentInputTarget = document.getElementById(
                                    `variant-input-${this.dataset.index}`);
                                loadVariantList(this.dataset.productId);
                            });
                        });

                        // 🔹 Klik input variant → buka list variant (efek sama dengan tombol Select)
                        modalContent.querySelectorAll('.variant-input').forEach(input => {
                            input.addEventListener('click', function() {
                                const index = this.id.split('-').pop();
                                const btn = modalContent.querySelector(
                                    `.select-variant-btn[data-index="${index}"]`);
                                if (btn) {
                                    currentInputTarget = this;
                                    loadVariantList(btn.dataset.productId);
                                }
                            });
                        });

                        $('#variantModal').modal('show');
                    };


                    // 🔹 Load list variant dari backend (tampil sebagai tombol checklist)
                    window.loadVariantList = function(productId) {
                        const listContainer = document.getElementById('variantListContent');
                        listContainer.innerHTML = '<p class="text-muted small mb-0">Loading...</p>';

                        fetch(`/variants/list/${productId}`)
                            .then(res => res.json())
                            .then(data => {
                                if (!data.length) {
                                    listContainer.innerHTML =
                                        '<p class="text-muted small mb-0">No variants available.</p>';
                                } else {
                                    listContainer.innerHTML = data.map(v => `
                    <button type="button"
                        class="btn btn-outline-primary btn-sm variant-btn me-2 mb-2"
                        data-variant="${v.variant_name}">
                        ${v.variant_name}
                    </button>
                `).join('');
                                }
                                $('#variantListModal').modal('show');
                            })
                            .catch(err => {
                                console.error('Error loading variants:', err);
                                listContainer.innerHTML =
                                    '<p class="text-danger small">Failed to load variants.</p>';
                            });
                    };

                    // 🔹 Klik tombol variant → toggle aktif seperti checklist
                    document.addEventListener('click', function(e) {
                        if (e.target.classList.contains('variant-btn')) {
                            e.target.classList.toggle('active');
                        }
                    });

                    // 🔹 Saat user klik "Select" di modal list variant
                    document.getElementById('selectVariantConfirm').addEventListener('click', function() {
                        // Ambil semua tombol variant yang aktif
                        const selected = Array.from(document.querySelectorAll('.variant-btn.active'))
                            .map(el => el.dataset.variant)
                            .join(', ');

                        // Isi ke input target
                        if (currentInputTarget) {
                            currentInputTarget.value = selected;
                        }

                        // Tutup modal
                        $('#variantListModal').modal('hide');
                    });

                    // 🔹 LOGIKA BARU: SELECT FOR ALL (Isi ke semua baris)
                    const applyAllBtn = document.getElementById('applyToAllVariants');

                    if (applyAllBtn) {
                        applyAllBtn.addEventListener('click', function() {
                            // 1. Ambil variant yang sedang dipilih (Aktif) di modal list
                            // Hasil: "Asin, Manis"
                            const selectedText = Array.from(document.querySelectorAll(
                                    '#variantListContent .variant-btn.active'))
                                .map(el => el.dataset.variant)
                                .join(', ');

                            // 2. Ambil SEMUA elemen input target di Modal Utama
                            // Kita cari berdasarkan class '.variant-input' yang ada di dalam #variantModalContent
                            const allInputs = document.querySelectorAll('#variantModalContent .variant-input');

                            // 3. Loop ke setiap input dan isi valuenya
                            allInputs.forEach(input => {
                                input.value = selectedText;
                            });

                            // 4. Tutup modal list variant
                            $('#variantListModal').modal('hide');

                            // (Opsional) Reset target input karena kita sudah mengisi semuanya
                            currentInputTarget = null;
                        });
                    }

                    // 🔹 Save hasil input variant
                    window.saveVariantData = function() {
                        const rows = document.querySelectorAll('#variantModalContent tbody tr');
                        const productId = document.getElementById('variantModalContent').dataset.productId;

                        const variants = Array.from(rows).map((row, i) => {
                            const variant = row.querySelector('.variant-input').value.trim();
                            const activeTypeBtn = row.querySelector('.type-btn.btn-primary');
                            const typeOrder = activeTypeBtn ? activeTypeBtn.dataset.type : 'dine_in';
                            return {
                                index: i + 1,
                                variant,
                                typeOrder
                            };
                        });
                        variantSession[productId] = variants;

                        console.log('✅ Variant saved:', {
                            productId,
                            variants
                        });
                        console.log("KIRIM updateVariant →", productId, variants);

                        // Kirim ke Livewire (update cart item)
                        Livewire.dispatch('updateVariant', [productId, variants]);

                        // Tutup modal
                        $('#variantModal').modal('hide');
                    };

                    window.addEventListener('variant-modal-reset', (e) => {
                        try {
                            // Ambil productId dari event (Livewire kirim di detail)
                            const productId = (e && e.detail && e.detail.productId) ? e.detail.productId : null;

                            console.log('variant-modal-reset event received for productId:', productId);

                            // 1) Hapus seluruh isi modal variant (ini yang benar)
                            // modal content adalah #variantModalContent (bukan #variantBody)
                            $('#variantModalContent').html('');

                            // 2) Reset current input target agar tidak menunjuk input lama
                            currentInputTarget = null;

                            // 3) Hapus cache variantSession untuk productId yang dihapus
                            if (productId) {
                                if (variantSession[productId]) {
                                    delete variantSession[productId];
                                    console.log('Cleared variantSession for product', productId);
                                }
                            } else {
                                // Jika tidak ada productId, kosongkan seluruh cache (aman)
                                variantSession = {};
                                console.log('Cleared entire variantSession');
                            }

                            // 4) Reset any temporary JS state used for variant modal
                            if (window.selectedVariants) window.selectedVariants = {};
                            if (window.variantState) window.variantState = {};
                            if (window.variantData) window.variantData = {};
                            window.defaultOrderType = 'dine_in';

                            // 5) Jika modal sedang terbuka, tutup modal untuk memastikan state bersih
                            $('#variantModal').modal('hide');
                            $('#variantListModal').modal('hide');

                        } catch (err) {
                            console.error('Error in variant-modal-reset handler:', err);
                        }
                    });

                    // 🔹 Reset seluruh variant & modal ketika klik Reset Cart
                    window.addEventListener('variant-modal-reset-all', () => {
                        try {
                            console.log('Reset Cart → clearing all variant JS state');

                            // 1) Kosongkan isi modal
                            $('#variantModalContent').html('');
                            $('#variantModal').modal('hide');
                            $('#variantListModal').modal('hide');

                            // 2) Reset variantSession JS global
                            window.variantSession = {};
                            variantSession = window.variantSession;

                            // 3) Reset input target
                            currentInputTarget = null;

                            // 4) Reset order type default
                            window.defaultOrderType = 'dine_in';

                            // 5) Reset variable lain yg dipakai di modal
                            if (window.selectedVariants) window.selectedVariants = {};
                            if (window.variantState) window.variantState = {};
                            if (window.variantData) window.variantData = {};

                        } catch (err) {
                            console.error('Error in variant-modal-reset-all:', err);
                        }
                    });

                });
            </script>
            <script>
                // --- Variabel Global (Pastikan ID modal benar) ---
                const PENDING_ORDERS_MODAL_ID = 'pendingOrdersModal';
                const KITCHEN_PREVIEW_MODAL_ID = 'kitchenPreviewModal';

                // ✅ FUNGSI UNTUK UNBLUR
                function unblurPendingOrdersModal() {
                    const pendingOrdersModal = document.getElementById(PENDING_ORDERS_MODAL_ID);
                    if (pendingOrdersModal && pendingOrdersModal.classList.contains('is-blurred')) {
                        pendingOrdersModal.classList.remove('is-blurred');
                        // Jika ada event listener yang terlewat, ini menjamin unblur.
                    }
                }

                // --- 1. LIVEWIRE LISTENERS (Tetap) ---
                document.addEventListener('livewire:initialized', () => {
                    // Event saat Print Preview Dapur dibuka
                    Livewire.on('show-kitchen-preview', () => {
                        // Tampilkan modal preview
                        $('#' + KITCHEN_PREVIEW_MODAL_ID).modal('show');

                        // BLUR: Terapkan kelas 'is-blurred'
                        const pendingOrdersModal = document.getElementById(PENDING_ORDERS_MODAL_ID);
                        if (pendingOrdersModal) {
                            pendingOrdersModal.classList.add('is-blurred');
                        }
                    });

                    // Event saat List Order dibuka (tetap normal)
                    Livewire.on('show-pending-orders-modal', () => {
                        $('#' + PENDING_ORDERS_MODAL_ID).modal('show');
                    });
                });

                // --- 2. BOOTSTRAP EVENT LISTENERS (Dikompromikan/Dihapus) ---
                // Kami mengabaikan listener hidden.bs.modal karena tidak konsisten.
                // Jika masih ada di kode Anda, hapuslah.


                // --- 3. FUNGSI PRINT KOT YANG DIJAMIN UNBLUR ---
                function printKOT() {
                    const printArea = document.getElementById('print-area');
                    if (!printArea) return;

                    const win = window.open('', 'PRINT_KOT', 'width=400,height=600');

                    win.document.write(`
                                        <html>
                                        <head>
                                            <title>Kitchen Order</title>
                                            <style>
                                                @page {
                                                    size: 58mm auto; /* Memaksa ukuran kertas thermal */
                                                    margin: 0;
                                                }
                                                html, body {
                                                    margin: 0;
                                                    padding: 0;
                                                    font-family: monospace;
                                                    /* Disamakan dengan font struk penjualan agar seragam */
                                                    font-size: 11px;
                                                    line-height: 1.2;
                                                    background-color: #fff;
                                                    color: #000;
                                                }
                                                #print-area {
                                                    /* Menggunakan 48mm sebagai area cetak aman agar kanan tidak terpotong */
                                                    width: 48mm;
                                                    padding: 10px 0 10px 2mm; /* Ada jarak aman dari pinggir kiri */
                                                    box-sizing: border-box;
                                                }
                                                /* Memastikan tabel di dalam print-area tidak meluber */
                                                table {
                                                    width: 100%;
                                                    border-collapse: collapse;
                                                    table-layout: fixed;
                                                }
                                                td {
                                                    word-wrap: break-word;
                                                    vertical-align: top;
                                                }
                                                hr {
                                                    border: 0;
                                                    border-top: 1px dashed #000;
                                                    margin: 5px 0;
                                                }
                                                .center { text-align: center; }
                                                .text-right { text-align: right; }

                                                /* Tambahan CSS agar tampilan teks tebal lebih jelas */
                                                strong { font-weight: bold; }
                                            </style>
                                        </head>

                                        <body onload="window.print(); setTimeout(() => window.close(), 500);">
                                            <div id="print-area">
                                                ${printArea.innerHTML}
                                            </div>
                                        </body>
                                        </html>
                                    `);

                    win.document.close();

                    // rapikan UI utama
                    unblurPendingOrdersModal();
                    $('#kitchenPreviewModal').modal('hide');
                }
            </script>

            <script>
                // Ini memastikan backdrop (blur layer) dihapus jika tertinggal
                $('#tableSelectionModal').on('hidden.bs.modal', function() {
                    if ($('.modal-backdrop').length) {
                        $('.modal-backdrop').remove();
                    }
                    // Pastikan class 'modal-open' juga dihapus dari body
                    if ($('body').hasClass('modal-open')) {
                        $('body').removeClass('modal-open');
                    }
                });
            </script>

            <script>
                $(document).ready(function() {

                    const saleReference = "{{ session('showPrintModal') }}";

                    if (saleReference) {
                        loadAndShowPrintModal(saleReference);
                    }

                    /* ============================
                     * STRUK (SUDAH ADA - TETAP)
                     * ============================ */
                    function loadAndShowPrintModal(reference) {
                        const printUrl = `/app/pos/sales/print/${reference}`;

                        $('#receiptContent').html('<div class="text-center">Memuat Struk...</div>');

                        const iframeHtml = `
                <iframe
                    src="${printUrl}?modal=true"
                    style="width: 100%; height: 400px; border: none;"
                    id="receiptIframe">
                </iframe>
            `;

                        $('#receiptContent').html(iframeHtml);
                        $('#printReceiptModal').modal('show');
                    }

                    $('#printButton').on('click', function() {
                        const iframe = document.getElementById('receiptIframe');
                        if (iframe) {
                            iframe.contentWindow.print();
                        } else {
                            alert('Struk belum termuat.');
                        }
                    });

                    /* ============================
                     * 🆕 KITCHEN ORDER
                     * ============================ */

                    $('#kitchenOrderButton').on('click', function() {
                        if (!saleReference) {
                            alert('Order belum tersedia.');
                            return;
                        }
                        loadAndShowKitchenModal(saleReference);
                    });

                    function loadAndShowKitchenModal(reference) {
                        const kitchenUrl = `/app/pos/sales/print-kitchen/${reference}`;

                        $('#kitchenOrderContent').html('<div class="text-center">Memuat Kitchen Order...</div>');

                        const iframeHtml = `
                <iframe
                    src="${kitchenUrl}?modal=true"
                    style="width: 100%; height: 400px; border: none;"
                    id="kitchenIframe">
                </iframe>
            `;

                        $('#kitchenOrderContent').html(iframeHtml);
                        $('#kitchenOrderModal').modal('show');
                    }

                    $('#printKitchenButton').on('click', function() {
                        const iframe = document.getElementById('kitchenIframe');
                        if (iframe) {
                            iframe.contentWindow.print();
                        } else {
                            alert('Kitchen order belum termuat.');
                        }
                    });

                });
            </script>
        @endpush
    </div>
