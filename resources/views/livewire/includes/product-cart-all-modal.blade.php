    <!-- ✅ Modal List Meja -->
    <div class="modal fade" id="tableSelectionModal" tabindex="-1" role="dialog" aria-labelledby="tableSelectionModalLabel"
        aria-hidden="true" wire:ignore.self>

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

                                <div class="selection-overlay" x-show="localSelectedIds.includes({{ $table->id }})">
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
                                                    @php
                                                        $kitchenLogs = $order->kitchenLogs ?? collect();

                                                        // Ada log yang belum diprint
                                                        $hasPendingUpdate =
                                                            $kitchenLogs->where('is_printed', 0)->count() > 0;

                                                        // Ada log yang belum diprint DAN sudah diapprove
                                                        // Gunakan != null karena approved_by biasanya berisi ID User (bukan sekedar angka 1)
                                                        $isApproved =
                                                            $kitchenLogs
                                                                ->where('is_printed', 0)
                                                                ->whereNotNull('approved_by')
                                                                ->count() > 0;
                                                    @endphp

                                                    @if ($order->is_printed == 1 && $hasPendingUpdate)
                                                        {{-- TOMBOL UPDATE (VOID/NEW) --}}
                                                        <button wire:click="previewVoidOrder({{ $order->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="previewVoidOrder({{ $order->id }})"
                                                            class="btn btn-sm btn-danger mr-1" style="width: 100px;">

                                                            <span wire:loading
                                                                wire:target="previewVoidOrder({{ $order->id }})">...</span>
                                                            <span wire:loading.remove
                                                                wire:target="previewVoidOrder({{ $order->id }})">
                                                                <i class="bi bi-printer-fill"></i> Print Update
                                                            </span>
                                                        </button>
                                                    @else
                                                        {{-- TOMBOL PRINT BIASA (AWAL) --}}
                                                        <button wire:click="previewOrder({{ $order->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="previewOrder({{ $order->id }})"
                                                            class="btn btn-sm {{ $order->is_printed ? 'btn-secondary' : 'btn-primary' }} mr-1"
                                                            style="width: 100px;">

                                                            <span wire:loading
                                                                wire:target="previewOrder({{ $order->id }})">...</span>
                                                            <span wire:loading.remove
                                                                wire:target="previewOrder({{ $order->id }})">
                                                                <i class="bi bi-printer"></i>
                                                                {{ $order->is_printed ? 'Re-Print' : 'Print KOT' }}
                                                            </span>
                                                        </button>
                                                    @endif
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
                                                    {{-- 🆕 TOMBOL PRE-BILL --}}
                                                    <button wire:click="printPreBill({{ $order->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="printPreBill({{ $order->id }})"
                                                        class="btn btn-sm btn-warning mr-1">

                                                        <span wire:loading
                                                            wire:target="printPreBill({{ $order->id }})">
                                                            Loading...
                                                            {{-- <i class="bi bi-hourglass-split"></i> --}}
                                                        </span>
                                                        <span wire:loading.remove
                                                            wire:target="printPreBill({{ $order->id }})">
                                                            <i class="bi bi-file-earmark-text"></i> Pre-Bill
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

    <!-- ✅ Modal Pre-Bill -->
    <div class="modal fade" id="preBillModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pre-Bill Preview</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" id="prebill-content">
                    <p class="text-center">Memuat data...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="printPreBill()">Print</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                            @if (isset($previewOrderData['is_combined']))
                                <strong style="font-size:16px;">UPDATE ORDER</strong><br>
                            @elseif (isset($previewOrderData['is_void']))
                                <strong style="font-size:18px; color:red;">VOID ORDER</strong><br>
                            @else
                                <strong style="font-size:14px;">KITCHEN ORDER</strong><br>
                            @endif
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

                        @php
                            $details = collect($previewOrderData['details']);
                            $voidItems = $details->where('type', 'void');
                            $newItems = $details->where('type', 'new');
                            // Jika print awal (bukan update), anggap semua item adalah item yang harus diprint
                            $isUpdate = isset($previewOrderData['is_combined']) || isset($previewOrderData['is_void']);
                        @endphp

                        {{-- ========================================== --}}
                        {{-- SECTION VOID (Hanya muncul jika update) --}}
                        {{-- ========================================== --}}
                        @if ($voidItems->count() > 0)
                            <div
                                style="text-align:center; background-color:#000; color:#fff; padding:2px; margin-top:5px;">
                                <strong>*** VOID (BATAL) ***</strong>
                            </div>
                            @foreach ($voidItems as $item)
                                @php
                                    // --- LOGIKA AGREGASI VARIANT UNTUK VOID ---
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
                                            if (is_array($decoded)) {
                                                $variants = $decoded;
                                            }
                                        }
                                    }

                                    foreach ($variants as $variant) {
                                        $vText = trim($variant['variant'] ?? '');
                                        $vType = trim($variant['typeOrder'] ?? $previewOrderData['typeOrder']);
                                        $key = $vText . '-' . $vType;
                                        $label =
                                            $vText === ''
                                                ? 'TYPE ' . strtoupper($vType)
                                                : strtoupper($vText) . ' (' . $vType . ')';

                                        if (!isset($aggregatedVariants[$key])) {
                                            $aggregatedVariants[$key] = ['label' => $label, 'qty' => 0];
                                        }
                                        $aggregatedVariants[$key]['qty']++;
                                    }
                                @endphp

                                <div style="margin-top:6px;">
                                    <strong
                                        style="text-decoration: line-through;">{{ strtoupper($item['product_name']) }}</strong>
                                    <span style="float:right;">x{{ $item['quantity'] }}</span>
                                </div>

                                {{-- Render Variant untuk Void --}}
                                @if (!empty($aggregatedVariants))
                                    @foreach ($aggregatedVariants as $v)
                                        <div style="padding-left:8px; font-size:11px; text-decoration: line-through;">
                                            - {{ $v['label'] }} x{{ $v['qty'] }}
                                        </div>
                                    @endforeach
                                @else
                                    <div style="padding-left:8px; font-size:11px; text-decoration: line-through;">
                                        - TYPE {{ strtoupper($previewOrderData['typeOrder']) }}
                                    </div>
                                @endif

                                @if (isset($item['note']) && $item['note'])
                                    <div
                                        style="padding-left:8px; font-size:10px; font-style: italic; text-decoration: line-through;">
                                        Note: {{ $item['note'] }}
                                    </div>
                                @endif
                                <div>-----------------------------</div>
                            @endforeach
                        @endif

                        {{-- ========================================== --}}
                        {{-- SECTION NEW / NORMAL ORDER --}}
                        {{-- ========================================== --}}
                        @if ($newItems->count() > 0 || !$isUpdate)
                            @if ($isUpdate && $newItems->count() > 0)
                                <div style="text-align:center; border:1px solid #000; padding:2px; margin-top:10px;">
                                    <strong>*** NEW (TAMBAHAN) ***</strong>
                                </div>
                            @endif

                            {{-- Looping item (Normal atau New) --}}
                            @php $targetItems = $isUpdate ? $newItems : $details; @endphp

                            @foreach ($targetItems as $item)
                                @php
                                    // --- LOGIKA AGREGASI VARIANT ---
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
                                            if (is_array($decoded)) {
                                                $variants = $decoded;
                                            }
                                        }
                                    }

                                    foreach ($variants as $variant) {
                                        $vText = trim($variant['variant'] ?? '');
                                        $vType = trim($variant['typeOrder'] ?? $previewOrderData['typeOrder']);
                                        $key = $vText . '-' . $vType;
                                        $label =
                                            $vText === ''
                                                ? 'TYPE ' . strtoupper($vType)
                                                : strtoupper($vText) . ' (' . $vType . ')';

                                        if (!isset($aggregatedVariants[$key])) {
                                            $aggregatedVariants[$key] = ['label' => $label, 'qty' => 0];
                                        }
                                        $aggregatedVariants[$key]['qty']++;
                                    }
                                @endphp

                                <div style="margin-top:6px;">
                                    <strong>{{ strtoupper($item['product_name']) }}</strong>
                                    <span style="float:right;">x{{ $item['quantity'] }}</span>
                                </div>

                                {{-- Render Variant --}}
                                @if (!empty($aggregatedVariants))
                                    @foreach ($aggregatedVariants as $v)
                                        <div style="padding-left:8px; font-size:11px;">- {{ $v['label'] }}
                                            x{{ $v['qty'] }}</div>
                                    @endforeach
                                @else
                                    <div style="padding-left:8px; font-size:11px;">- TYPE
                                        {{ strtoupper($previewOrderData['typeOrder']) }}</div>
                                @endif

                                @if (isset($item['note']) && $item['note'])
                                    <div style="padding-left:8px; font-size:10px; font-style: italic;">Note:
                                        {{ $item['note'] }}</div>
                                @endif

                                <div>-----------------------------</div>
                            @endforeach
                        @endif

                        {{-- FOOTER --}}
                        <div style="text-align:center; margin-top:10px;">
                            <strong>--- END ORDER ---</strong>
                        </div>
                    </div>
                    <div style="height:1px;"></div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" {{-- ✅ TAMBAHKAN ONCLICK INI --}}
                            onclick="unblurPendingOrdersModal()">Close</button>

                        <button type="button" class="btn btn-primary"
                            onclick="printKOT('{{ $previewOrderData['reference'] }}')">Print</button>
                    </div>
                @else
                    <div class="modal-body text-center text-muted">Reload data...</div>
                @endif
            </div>
        </div>
    </div>

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

                        <div class="d-flex justify-content-end">
                            <div class="border-top pt-2" style="width: 300px;">
                                {{-- ✅ Subtotal (Sebelum Pajak & Biaya Lain) --}}
                                @php
                                    // Menghitung subtotal murni dari total detail item
                                    $subtotal_items = $selectedOrderDetails->sum('sub_total');
                                @endphp
                                <div class="d-flex justify-content-between py-1 font-weight-bold">
                                    <span>Subtotal</span>
                                    <span>{{ format_currency($subtotal_items) }}</span>
                                </div>
                                {{-- Discount --}}
                                @if (($selectedOrderSummary['discount_amount'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Discount
                                            ({{ $selectedOrderSummary['discount_percentage'] ?? 0 }}%)</span>
                                        <span>(-)
                                            {{ format_currency($selectedOrderSummary['discount_amount'] ?? 0) }}</span>
                                    </div>
                                @endif
                                {{-- Rule Service Charge: Muncul jika fitur Aktif, Dine In, & nilai > 0 --}}
                                @if (isFeatureEnabled('summary_service') &&
                                        ($selectedOrderSummary['order_type'] ?? '') == 'dine_in' &&
                                        ($selectedOrderSummary['service_charge'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Service Charge (5%)</span>
                                        <span>(+)
                                            {{ format_currency($selectedOrderSummary['service_charge'] ?? 0) }}</span>
                                    </div>
                                @endif
                                {{-- Order Tax --}}
                                @if (($selectedOrderSummary['tax_amount'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Order Tax
                                            ({{ $selectedOrderSummary['tax_percentage'] ?? 0 }}%)</span>
                                        <span>(+)
                                            {{ format_currency($selectedOrderSummary['tax_amount'] ?? 0) }}</span>
                                    </div>
                                @endif

                                {{-- Rule Delivery: Muncul jika fitur Aktif & nilai > 0 --}}
                                @if (isFeatureEnabled('summary_pkg') && ($selectedOrderSummary['shipping_amount'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Delivery</span>
                                        <span>(+)
                                            {{ format_currency($selectedOrderSummary['shipping_amount'] ?? 0) }}</span>
                                    </div>
                                @endif

                                {{-- Rule Lain-lain: Muncul jika fitur Others Aktif --}}
                                @if (isFeatureEnabled('summary_others'))
                                    @if (($selectedOrderSummary['lain_a'] ?? 0) > 0)
                                        <div class="d-flex justify-content-between py-1">
                                            <span>Lain-lain A</span>
                                            <span>(+)
                                                {{ format_currency($selectedOrderSummary['lain_a'] ?? 0) }}</span>
                                        </div>
                                    @endif

                                    @if (($selectedOrderSummary['lain_b'] ?? 0) > 0)
                                        <div class="d-flex justify-content-between py-1">
                                            <span>Lain-lain B</span>
                                            <span>(+)
                                                {{ format_currency($selectedOrderSummary['lain_b'] ?? 0) }}</span>
                                        </div>
                                    @endif
                                @endif

                                <hr class="my-1">
                                <div class="d-flex justify-content-between font-weight-bold mt-2">
                                    <span style="font-size: 1.1rem;">Total</span>
                                    <span style="font-size: 1.1rem;" class="text-primary">
                                        {{ format_currency($selectedOrderSummary['total_amount'] ?? 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                            <p class="mt-2">No order details found.</p>
                        </div>
                    @endif
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal Variant -->
    <div wire:ignore.self class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel"
        aria-hidden="true">
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
                    <button type="button" class="btn btn-primary btn-sm" id="selectVariantConfirm">Apply</button>
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
                    <button type="button" class="btn btn-primary" id="printButton"><i class="bi bi-printer"></i>
                        Print Struk</button>
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

    @if ($showApprovalModal)
        <div class="modal fade show" style="display:block; background: rgba(0,0,0,0.6); z-index: 1050;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title font-weight-bold">
                            <i class="bi bi-shield-lock-fill mr-2"></i> Otorisasi Perubahan Order
                        </h5>
                        <button type="button" class="close" wire:click="$set('showApprovalModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Detail perubahan pada order <strong>#{{ $current_reference }}</strong>:
                        </p>

                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th class="text-left">Item Name</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                        <th class="text-left">System Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items_to_approve as $item)
                                        <tr>
                                            <td class="align-middle px-2">{{ $item['name'] }}</td>
                                            <td class="text-center align-middle font-weight-bold">{{ $item['qty'] }}
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge {{ $item['class'] }} px-2 py-1"
                                                    style="min-width: 50px;">
                                                    {{ $item['type'] }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-muted small px-2">{{ $item['reason'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Admin Username</label>
                                    <input type="text" wire:model.defer="approver_username" class="form-control"
                                        placeholder="Username">
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Admin Password</label>
                                    <input type="password" wire:model.defer="approver_password" class="form-control"
                                        placeholder="••••••••">
                                    @error('approver_password')
                                        <small class="text-danger font-weight-bold">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">Admin Note (Alasan Otorisasi)</label>
                                    <textarea wire:model.defer="approval_note" class="form-control" rows="4"
                                        placeholder="Contoh: Salah input oleh kasir / Permintaan customer..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showApprovalModal', false)">Batal</button>
                        <button type="button" class="btn btn-primary shadow-sm px-4" wire:click="confirmApproval"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="confirmApproval">Konfirmasi Perubahan</span>
                            <span wire:loading wire:target="confirmApproval">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
