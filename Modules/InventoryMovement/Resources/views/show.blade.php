@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-primary py-4 px-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 small opacity-75">Referensi Mutasi</p>
                                <h4 class="fw-bold mb-0">{{ $movement->reference }}</h4>
                            </div>
                            <div class="text-end">
                                <p class="mb-1 small opacity-75">Tanggal</p>
                                <h5 class="mb-0">{{ \Carbon\Carbon::parse($movement->date)->format('d M Y') }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-5">
                            <div class="col-md-5">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-2">Dari Gudang</label>
                                <div class="p-3 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-10">
                                    <h6 class="fw-bold text-danger mb-1">{{ $movement->fromWarehouse->name }}</h6>
                                    <span class="small text-muted">{{ $movement->fromWarehouse->code }}</span>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center justify-content-center py-3">
                                <i class="bi bi-arrow-right-circle fs-3 text-muted"></i>
                            </div>
                            <div class="col-md-5">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-2">Ke Gudang</label>
                                <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-10">
                                    <h6 class="fw-bold text-success mb-1">{{ $movement->toWarehouse->name }}</h6>
                                    <span class="small text-muted">{{ $movement->toWarehouse->code }}</span>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3">Detail Item</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr class="small text-uppercase">
                                        <th>Nama Produk</th>
                                        <th class="text-center">QTY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($movement->details as $item)
                                        <tr>
                                            <td>{{ $item->product->product_name }}</td>
                                            <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($movement->note)
                            <div class="mt-4">
                                <label class="text-muted small text-uppercase fw-bold">Catatan:</label>
                                <p class="p-3 bg-light rounded-3 small italic">{{ $movement->note }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 text-end">
                        <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-printer me-1"></i> Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
