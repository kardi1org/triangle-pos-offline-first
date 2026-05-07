@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Tampilkan Error jika ada --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('work-orders.store') }}" method="POST">
            @csrf
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <strong><i class="bi bi-gear-wide-connected"></i> Buat Work Order Baru</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Warehouse (Lokasi Stok) <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-control select2" required>
                                <option value="">-- Pilih Warehouse --</option>
                                @foreach ($warehouses as $w)
                                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label>Produk Hasil (Finish Good) <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-control select2" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->product_code }} | {{ $p->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Qty Produksi</label>
                            <input type="number" step="0.01" name="quantity" id="wo_qty" class="form-control"
                                value="1" min="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <label>Unit</label>
                            <input type="text" name="unit" id="wo_unit" class="form-control bg-light" readonly
                                placeholder="-">
                        </div>
                    </div>

                    <hr>
                    <h5 class="mt-4"><i class="bi bi-basket3"></i> Estimasi Bahan yang Akan Dipotong:</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="wo-table">
                            <thead class="bg-light">
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th>Nama Bahan Baku</th>
                                    <th width="15%">Qty per Unit</th>
                                    <th width="20%">Total Dibutuhkan</th>
                                    <th width="10%">Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center text-muted italic">Pilih produk hasil untuk
                                        melihat kebutuhan bahan baku</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <label>Catatan (Opsional)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Contoh: Produksi Batch A1"></textarea>
                    </div>
                </div>
                <div class="card-footer bg-white text-right">
                    <a href="{{ route('work-orders.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-success px-4" id="btn-submit">
                        <i class="bi bi-check-circle"></i> Proses Produksi & Potong Stok
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- 1. Tambahkan CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- Optional: Tambahkan tema Bootstrap 4 jika Anda menggunakannya --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    {{-- 2. Tambahkan JS Select2 (WAJIB setelah jQuery) --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Pastikan Select2 diinisialisasi setelah library dimuat
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    width: '100%',
                    theme: 'bootstrap4'
                });
            }

            let currentRecipeDetails = [];

            $('#product_id').change(function() {
                let id = $(this).val();
                let woQty = $('#wo_qty').val();

                if (id) {
                    let url = "{{ route('work-orders.get-recipe', ':id') }}".replace(':id', id);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(res) {
                            if (res.status === 'success') {
                                $('#wo_unit').val(res.unit);
                                currentRecipeDetails = res.details;
                                renderTable();
                            } else {
                                alert(res.message);
                                $('#wo-table tbody').html('');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('Gagal mengambil data resep. Pastikan Route sudah benar.');
                        }
                    });
                }
            });

            $('#wo_qty').on('input', function() {
                renderTable();
            });

            function renderTable() {
                let woQty = parseFloat($('#wo_qty').val()) || 0;
                let html = '';
                if (currentRecipeDetails.length > 0) {
                    currentRecipeDetails.forEach(function(item) {
                        let totalQty = (item.quantity * woQty);
                        html += `<tr>
                            <td>${item.product.product_code}</td>
                            <td>
                                ${item.product.product_name}
                                <input type="hidden" name="ingredient_id[]" value="${item.product_id}">
                            </td>
                            <td>${item.quantity}</td>
                            <td><input type="number" name="ing_qty[]" class="form-control" value="${totalQty}" readonly></td>
                            <td><input type="text" name="ing_unit[]" class="form-control" value="${item.unit}" readonly></td>
                        </tr>`;
                    });
                    $('#wo-table tbody').html(html);
                }
            }
        });
    </script>
@endpush
