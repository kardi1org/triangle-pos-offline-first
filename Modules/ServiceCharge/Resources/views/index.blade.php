@extends('layouts.app')

@section('title', 'Pengaturan Service Charge')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-primary">Konfigurasi Service Charge</h5>
                @if ($service_charges->count() == 0)
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addServiceModal">
                        <i class="bi bi-plus-circle"></i> Tambah Baru
                    </button>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Status</th>
                                <th>Nama Layanan</th>
                                <th>Persentase</th>
                                <th>Metode Hitung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($service_charges as $item)
                                <tr>
                                    <td>
                                        <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $item->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $item->name }}</strong></td>
                                    <td>{{ $item->percentage }}%</td>
                                    <td>
                                        <span class="text-muted small">
                                            {{ \Modules\ServiceCharge\Entities\ServiceCharge::getTypes()[$item->calculation_type] }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" data-toggle="modal"
                                            data-target="#editModal{{ $item->id }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>

                                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1"
                                            role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <form action="{{ route('service-charge.update', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT') {{-- Penting: Gunakan method PUT untuk update --}}
                                                    <div class="modal-content text-left">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Service Charge: {{ $item->name }}
                                                            </h5>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Nama Biaya</label>
                                                                <input type="text" name="name" class="form-control"
                                                                    value="{{ $item->name }}" required>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Nilai (%)</label>
                                                                    <input type="number" step="0.01" name="percentage"
                                                                        class="form-control"
                                                                        value="{{ $item->percentage }}" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Status</label>
                                                                    <select name="is_active" class="form-control">
                                                                        <option value="1"
                                                                            {{ $item->is_active ? 'selected' : '' }}>Aktif
                                                                        </option>
                                                                        <option value="0"
                                                                            {{ !$item->is_active ? 'selected' : '' }}>
                                                                            Non-Aktif</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group border p-2 bg-light">
                                                                <label class="font-weight-bold">Aturan Perhitungan</label>
                                                                @foreach (\Modules\ServiceCharge\Entities\ServiceCharge::getTypes() as $id => $label)
                                                                    <div class="custom-control custom-radio mb-2">
                                                                        <input type="radio"
                                                                            id="type_edit_{{ $item->id }}_{{ $id }}"
                                                                            name="calculation_type"
                                                                            value="{{ $id }}"
                                                                            class="custom-control-input"
                                                                            {{ $item->calculation_type == $id ? 'checked' : '' }}>
                                                                        <label class="custom-control-label"
                                                                            for="type_edit_{{ $item->id }}_{{ $id }}">{{ $label }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-info btn-block">Update
                                                                Perubahan</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('service-charge.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Konfigurasi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Biaya</label>
                            <input type="text" name="name" class="form-control" placeholder="Service Charge 5%"
                                required>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Nilai (%)</label>
                                <input type="number" step="0.01" name="percentage" class="form-control" value="0.00"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Status</label>
                                <select name="is_active" class="form-control">
                                    <option value="1">Aktifkan Sekarang</option>
                                    <option value="0">Simpan Draft</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group border p-2 bg-light">
                            <label class="font-weight-bold">Aturan Perhitungan</label>
                            @foreach (\Modules\ServiceCharge\Entities\ServiceCharge::getTypes() as $id => $label)
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" id="type_{{ $id }}" name="calculation_type"
                                        value="{{ $id }}" class="custom-control-input"
                                        {{ $loop->first ? 'checked' : '' }}>
                                    <label class="custom-control-label"
                                        for="type_{{ $id }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-block">Simpan Pengaturan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
