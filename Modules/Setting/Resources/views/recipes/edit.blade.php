@extends('layouts.app')

@section('title', 'Edit Recipe')

@section('content')
    <div class="container-fluid">
        <form action="{{ route('recipes.update', $recipe->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                {{-- Produk Hasil --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <strong><i class="bi bi-pencil-square"></i> Edit Produk Hasil</strong>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <label>Pilih Barang Jadi <span class="text-danger">*</span></label>
                                    <select name="product_id" class="form-control select2-main" required>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ $recipe->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->product_code }} | {{ $product->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Qty Hasil</label>
                                    <input type="number" step="0.01" name="quantity" class="form-control"
                                        value="{{ $recipe->quantity }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Unit</label>
                                    <input type="text" name="unit" class="form-control" value="{{ $recipe->unit }}"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Bahan Baku --}}
                <div class="col-12 mt-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-list-check"></i> <strong>Detail Bahan Baku</strong></span>
                            <button type="button" class="btn btn-light btn-sm" id="addRow">
                                <i class="bi bi-plus-circle"></i> Tambah Bahan
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="recipe-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 40%">Bahan Baku</th>
                                            <th>Qty</th>
                                            <th>Unit</th>
                                            <th>Est. Harga</th>
                                            <th>Total</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recipe->details as $detail)
                                            <tr>
                                                <td>
                                                    <select name="ingredient_id[]"
                                                        class="form-control item-select select2-dyn" required>
                                                        @foreach ($products as $p)
                                                            <option value="{{ $p->id }}"
                                                                {{ $detail->product_id == $p->id ? 'selected' : '' }}>
                                                                {{ $p->product_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" step="0.0001" name="ing_qty[]"
                                                        class="form-control qty-input" value="{{ $detail->quantity }}">
                                                </td>
                                                <td><input type="text" name="ing_unit[]" class="form-control unit-input"
                                                        value="{{ $detail->unit }}" readonly></td>
                                                <td><input type="number" name="ing_cost[]" class="form-control cost-input"
                                                        value="{{ $detail->cost }}" readonly></td>
                                                <td class="subtotal text-center">
                                                    {{ number_format($detail->quantity * $detail->cost, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm removeRow"><i
                                                            class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="4" class="text-right">Total Biaya Produksi:</td>
                                            <td class="text-center" id="grand-total">0</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-primary px-4">Update Resep <i
                                        class="bi bi-save"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- Memastikan Library Select2 tersedia --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function($) {
            $(document).ready(function() {

                function initSelect2(element) {
                    if ($.fn.select2) {
                        $(element).select2({
                            width: '100%',
                            theme: 'bootstrap4'
                        });
                    }
                }

                // Inisialisasi awal untuk Select2 yang sudah ada (saat Edit dimuat)
                initSelect2('.select2-main');
                initSelect2('.select2-dyn');

                // Hitung total awal saat halaman dimuat
                calculateGrandTotal();

                // Tambah Baris Baru
                $('#addRow').on('click', function(e) {
                    e.preventDefault();
                    let rowId = Date.now();
                    let row = `
                    <tr id="row_${rowId}">
                        <td>
                            <select name="ingredient_id[]" class="form-control item-select select2-dyn-new" required>
                                <option value="">-- Pilih Bahan --</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->product_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" step="0.0001" name="ing_qty[]" class="form-control qty-input" value="1"></td>
                        <td><input type="text" name="ing_unit[]" class="form-control unit-input" readonly></td>
                        <td><input type="number" name="ing_cost[]" class="form-control cost-input" readonly></td>
                        <td class="subtotal text-center">0</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm removeRow"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;

                    $('#recipe-table tbody').append(row);
                    initSelect2(`#row_${rowId} .select2-dyn-new`);
                });

                // AJAX Ambil Data Produk
                $(document).on('change', '.item-select', function() {
                    let tr = $(this).closest('tr');
                    let id = $(this).val();

                    if (id) {
                        // Beri indikasi loading
                        tr.find('.unit-input, .cost-input').val('...');

                        $.ajax({
                            url: "{{ url('/recipes/product-data') }}/" + id,
                            type: 'GET',
                            success: function(data) {
                                tr.find('.unit-input').val(data.unit);
                                tr.find('.cost-input').val(data.cost);
                                calculateRow(tr);
                            },
                            error: function() {
                                alert('Gagal mengambil data produk');
                                tr.find('.unit-input, .cost-input').val('');
                            }
                        });
                    }
                });

                $(document).on('input', '.qty-input', function() {
                    calculateRow($(this).closest('tr'));
                });

                $(document).on('click', '.removeRow', function() {
                    $(this).closest('tr').remove();
                    calculateGrandTotal();
                });

                function calculateRow(tr) {
                    let qty = parseFloat(tr.find('.qty-input').val()) || 0;
                    let cost = parseFloat(tr.find('.cost-input').val()) || 0;
                    let subtotal = qty * cost;
                    tr.find('.subtotal').text(subtotal.toLocaleString('id-ID'));
                    calculateGrandTotal();
                }

                function calculateGrandTotal() {
                    let total = 0;
                    $('.subtotal').each(function() {
                        // Hilangkan titik ribuan dan ganti koma desimal ke titik sebelum diproses
                        let textVal = $(this).text().replace(/\./g, '').replace(/,/g, '.');
                        total += parseFloat(textVal) || 0;
                    });
                    $('#grand-total').text(total.toLocaleString('id-ID'));
                }

                $('form').on('submit', function(e) {
                    let isInvalid = false;

                    // Cek semua cost-input, jika ada yang '...' atau kosong, batalkan submit
                    $('.cost-input').each(function() {
                        if ($(this).val() === "" || $(this).val() === "...") {
                            isInvalid = true;
                        }
                    });

                    if (isInvalid) {
                        e.preventDefault();
                        alert(
                            'Mohon tunggu hingga semua data bahan baku terisi otomatis atau pastikan data bahan lengkap.'
                            );
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
