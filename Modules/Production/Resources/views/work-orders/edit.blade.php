@extends('layouts.app')

@section('title', 'Edit Work Order')

@section('content')
    <div class="container-fluid">
        <form action="{{ route('work-orders.update', $workOrder->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <strong><i class="bi bi-pencil-square"></i> Edit Work Order: {{ $workOrder->reference }}</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Warehouse</label>
                            <input type="text" class="form-control bg-light" value="{{ $workOrder->warehouse->name }}"
                                readonly>
                            <input type="hidden" name="warehouse_id" value="{{ $workOrder->warehouse_id }}">
                        </div>
                        <div class="col-md-5">
                            <label>Produk Hasil</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ $workOrder->product->product_name }}" readonly>
                            <input type="hidden" name="product_id" id="product_id" value="{{ $workOrder->product_id }}">
                        </div>
                        <div class="col-md-2">
                            <label>Qty Produksi <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="quantity" id="wo_qty" class="form-control"
                                value="{{ $workOrder->quantity }}" required>
                        </div>
                        <div class="col-md-2">
                            <label>Unit</label>
                            <input type="text" name="unit" class="form-control bg-light"
                                value="{{ $workOrder->unit }}" readonly>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mt-4"><i class="bi bi-basket3"></i> Detail Bahan Baku yang Digunakan:</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="wo-table">
                            <thead class="bg-light">
                                <tr>
                                    <th width="20%">Kode</th>
                                    <th>Nama Bahan</th>
                                    <th width="15%">Qty per Unit</th>
                                    <th width="20%">Total Kebutuhan</th>
                                    <th width="10%">Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workOrder->details as $detail)
                                    <tr>
                                        <td>{{ $detail->product->product_code }}</td>
                                        <td>
                                            {{ $detail->product->product_name }}
                                            <input type="hidden" name="ingredient_id[]" value="{{ $detail->product_id }}">
                                        </td>
                                        <td class="text-center qty-per-unit">
                                            {{-- Kita hitung qty per unit dari total qty dibagi qty WO --}}
                                            {{ $workOrder->quantity > 0 ? $detail->quantity / $workOrder->quantity : 0 }}
                                        </td>
                                        <td>
                                            <input type="number" step="0.0001" name="ing_qty[]"
                                                class="form-control bg-light ing-total-qty" value="{{ $detail->quantity }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="ing_unit[]" class="form-control bg-light"
                                                value="{{ $detail->unit }}" readonly>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group mt-3">
                        <label>Catatan / Note</label>
                        <textarea name="note" class="form-control" rows="3">{{ $workOrder->note }}</textarea>
                    </div>
                </div>
                <div class="card-footer bg-white text-right">
                    <a href="{{ route('work-orders.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- Memastikan Library Select2 dimuat jika diperlukan --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 jika ada elemen select baru (opsional di edit)
            if ($.fn.select2) {
                $('.select2').select2({
                    width: '100%',
                    theme: 'bootstrap4'
                });
            }

            // Fungsi kalkulasi ulang saat Qty Produksi diubah
            $('#wo_qty').on('input change', function() {
                let newWoQty = parseFloat($(this).val()) || 0;

                $('#wo-table tbody tr').each(function() {
                    let row = $(this);
                    // Ambil angka "Qty per Unit" yang tadi kita taruh di kolom 3 (index 2)
                    let qtyPerUnit = parseFloat(row.find('.qty-per-unit').text()) || 0;

                    // Hitung total baru
                    let newTotal = (qtyPerUnit * newWoQty).toFixed(4);

                    // Masukkan ke input total pakai
                    row.find('.ing-total-qty').val(newTotal);
                });
            });
        });
    </script>
@endpush
