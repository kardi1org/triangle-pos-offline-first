@extends('layouts.app')

@section('title', 'Order Summary Settings')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Settings</li>
        <li class="breadcrumb-item active">Order Summary</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('utils.alerts')
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="bi bi-sliders2-vertical mr-0 text-primary"></i>
                                Pengaturan Komponen Order Summary
                            </h5>
                            <small class="text-muted">Kelola bagaimana pajak, biaya layanan, dan biaya lainnya dihitung di
                                kasir.</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="align-middle" style="width: 200px;">Fitur & Nama Tampilan</th>
                                        <th class="align-middle">Deskripsi Formula</th>
                                        <th class="align-middle text-center" style="width: 160px;">Posisi Pajak</th>
                                        <th class="align-middle text-center" style="width: 150px;">Nilai Default</th>
                                        <th class="align-middle text-center" style="width: 80px;">Status</th>
                                        <th class="align-middle text-center" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($settings as $setting)
                                        <tr>
                                            <form action="{{ route('order-summary.update', $setting->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                {{-- Fitur & Nama --}}
                                                <td class="align-middle">
                                                    <div class="form-group mb-0">
                                                        <input type="text" name="feature_name"
                                                            class="form-control form-control-sm font-weight-bold"
                                                            value="{{ $setting->feature_name }}">
                                                        <small class="badge badge-secondary mt-1">ID:
                                                            {{ $setting->feature_key }}</small>
                                                    </div>
                                                </td>

                                                {{-- Deskripsi Formula --}}
                                                <td class="align-middle">
                                                    <textarea name="formula_description" class="form-control form-control-sm" rows="2"
                                                        placeholder="Jelaskan cara kerja fitur ini...">{{ $setting->formula_description }}</textarea>
                                                </td>

                                                {{-- Tax Position --}}
                                                <td class="align-middle text-center">
                                                    <select name="tax_position"
                                                        class="form-control form-control-sm custom-select shadow-none">
                                                        <option value="before"
                                                            {{ $setting->tax_position == 'before' ? 'selected' : '' }}>
                                                            🟢 Before Tax
                                                        </option>
                                                        <option value="after"
                                                            {{ $setting->tax_position == 'after' ? 'selected' : '' }}>
                                                            🔵 After Tax
                                                        </option>
                                                    </select>
                                                </td>

                                                {{-- Nilai Default --}}
                                                <td class="align-middle">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="0.01" name="default_value"
                                                            class="form-control text-right"
                                                            value="{{ $setting->default_value }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                {{ in_array($setting->feature_key, ['order_tax', 'service_charge', 'discount_global']) ? '%' : '#' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Status Aktif --}}
                                                <td class="align-middle text-center">
                                                    <div class="custom-control custom-switch custom-switch-md">
                                                        <input type="checkbox" name="is_active" class="custom-control-input"
                                                            id="customSwitch{{ $setting->id }}" value="1"
                                                            {{ $setting->is_active ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="customSwitch{{ $setting->id }}"></label>
                                                    </div>
                                                </td>

                                                {{-- Button --}}
                                                <td class="align-middle text-center">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-sm btn-block shadow-sm">
                                                        <i class="bi bi-save mr-1"></i> Update
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                Data pengaturan belum tersedia. Silakan jalankan Seeder.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-0 small text-muted">
                                    <span class="font-weight-bold text-dark">Info:</span><br>
                                    - <strong>Before Tax:</strong> Biaya akan dijumlahkan ke subtotal SEBELUM dikali
                                    persentase pajak.<br>
                                    - <strong>After Tax:</strong> Biaya ditambahkan ke total AKHIR setelah perhitungan pajak
                                    selesai.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_css')
    <style>
        .custom-switch-md .custom-control-label::before {
            height: 1.5rem;
            width: 2.5rem;
            border-radius: 2rem;
        }

        .custom-switch-md .custom-control-label::after {
            width: calc(1.5rem - 4px);
            height: calc(1.5rem - 4px);
            border-radius: 2rem;
        }

        .custom-switch-md .custom-control-input:checked~.custom-control-label::after {
            transform: translateX(1rem);
        }
    </style>
@endpush
