@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Mutasi Antar Gudang</h4>
            <a href="{{ route('inventory-movements.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Buat Mutasi
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <form action="{{ route('inventory-movements.index') }}" method="GET" class="row g-2">
                    <div class="col-md-4">
                        <select name="warehouse_id" class="form-select border-light-subtle bg-light"
                            onchange="this.form.submit()">
                            <option value="">Semua Gudang (Mutasi by Warehouse)</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}"
                                    {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4">Tgl / Ref</th>
                                <th>Dari Gudang</th>
                                <th>Ke Gudang</th>
                                <th>Operator</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $m)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold d-block">{{ $m->date }}</span>
                                        <small class="text-muted">{{ $m->reference }}</small>
                                    </td>
                                    <td><span
                                            class="badge bg-danger-subtle text-danger px-3">{{ $m->fromWarehouse->name }}</span>
                                    </td>
                                    <td><span
                                            class="badge bg-success-subtle text-success px-3">{{ $m->toWarehouse->name }}</span>
                                    </td>
                                    <td>{{ $m->user->name }}</td>
                                    <td class="text-center text-nowrap pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            {{-- Tombol Detail --}}
                                            <a href="{{ route('inventory-movements.show', $m->id) }}"
                                                class="btn btn-sm btn-light border rounded-pill px-3 fw-medium">
                                                <i class="bi bi-eye text-primary"></i>
                                            </a>

                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('inventory-movements.edit', $m->id) }}"
                                                class="btn btn-sm btn-light border rounded-pill px-3 fw-medium">
                                                <i class="bi bi-pencil text-info"></i>
                                            </a>

                                            {{-- Tombol Cetak --}}
                                            <button type="button"
                                                onclick="window.open('{{ route('inventory-movements.show', $m->id) }}?print=true')"
                                                class="btn btn-sm btn-light border rounded-pill px-2">
                                                <i class="bi bi-printer text-secondary"></i>
                                            </button>

                                            {{-- Tombol Delete --}}
                                            <form action="{{ route('inventory-movements.destroy', $m->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-light border rounded-pill px-3 fw-medium">
                                                    <i class="bi bi-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada pergerakan stok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $movements->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
