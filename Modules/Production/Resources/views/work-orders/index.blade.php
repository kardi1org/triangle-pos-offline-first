@extends('layouts.app')

@section('title', 'Work Orders')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-journal-text"></i> Daftar Work Order</strong>
                <a href="{{ route('work-orders.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Buat WO Baru
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Ref. Number</th>
                                <th>Tanggal</th>
                                <th>Produk Hasil</th>
                                <th>Warehouse</th>
                                <th class="text-center">Qty Produksi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workOrders as $wo)
                                <tr>
                                    <td><strong>{{ $wo->reference }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($wo->date)->format('d/m/Y') }}</td>
                                    <td>{{ optional($wo->product)->product_name ?? '-' }}</td>
                                    <td>{{ optional($wo->warehouse)->name ?? '-' }}</td>
                                    <td class="text-center">{{ $wo->quantity }} {{ $wo->unit }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('work-orders.edit', $wo->id) }}" class="btn btn-info btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('work-orders.destroy', $wo->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Hapus data ini? Stok tidak akan kembali otomatis!')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data produksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $workOrders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
