@extends('layouts.app')

@section('title', 'Feature & Package Management')

@section('third_party_stylesheets')
    <style>
        /* Gaya Header Group Tabel */
        .table-group-header {
            background-color: #f1f3f5;
            font-weight: 700;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        /* Perbaikan Custom Switch agar lebih Modern */
        .custom-switch-lg .custom-control-label {
            padding-left: 3rem;
            cursor: pointer;
        }

        .custom-switch-lg .custom-control-label::before {
            height: 1.6rem;
            width: 2.8rem;
            border-radius: 50rem;
            background-color: #dee2e6;
            border: none;
            transition: background-color 0.3s ease;
        }

        .custom-switch-lg .custom-control-label::after {
            width: calc(1.6rem - 4px);
            height: calc(1.6rem - 4px);
            background-color: white;
            border-radius: 50%;
            top: calc(2px + 0.125rem);
            /* Penyesuaian posisi tengah */
            left: calc(-3rem + 2px + 0.5rem);
            /* Penyesuaian posisi awal */
            transition: transform 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .custom-switch-lg .custom-control-input:checked~.custom-control-label::before {
            background-color: #28a745;
            /* Warna hijau sukses saat aktif */
        }

        .custom-switch-lg .custom-control-input:checked~.custom-control-label::after {
            transform: translateX(1.2rem);
            background-color: white;
        }

        /* Efek Hover pada Baris */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
            transition: background-color 0.2s ease;
        }

        /* Styling Badge Header agar lebih menonjol */
        .pkg-header {
            font-size: 0.75rem;
            font-weight: 800;
            display: block;
            margin-bottom: 5px;
        }

        /* Container untuk Header Package */
        .col-pkg-center {
            text-align: center !important;
            vertical-align: bottom !important;
        }

        .pkg-header {
            font-size: 0.7rem;
            font-weight: 800;
            display: block;
            margin-bottom: 2px;
            color: #6c757d;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Pastikan toggle di bawahnya juga center agar sejajar vertikal */
        .td-center {
            text-align: center !important;
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Feature Manager</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @include('utils.alerts')

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 text-primary font-weight-bold">
                            <i class="bi bi-shield-check mr-2"></i> Feature & Package Access Control
                        </h5>
                        <span class="badge badge-pill badge-light border">Admin Only</span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-left border-0 py-3 pl-4" style="width: 40%;">Feature Name</th>

                                        @php
                                            $packages = [
                                                1 => [
                                                    'name' => 'Basic',
                                                    'class' => 'badge-secondary',
                                                    'text' => 'text-muted',
                                                ],
                                                2 => ['name' => 'Pro', 'class' => 'badge-info', 'text' => 'text-info'],
                                                3 => [
                                                    'name' => 'Premium',
                                                    'class' => 'badge-success',
                                                    'text' => 'text-success',
                                                ],
                                            ];
                                        @endphp

                                        @foreach ($packages as $id => $pkg)
                                            <th class="border-0 py-3 col-pkg-center" style="width: 20%;">
                                                <small class="pkg-header {{ $pkg['text'] }}">PACKAGE
                                                    {{ $id }}</small>
                                                <span
                                                    class="badge {{ $pkg['class'] }} px-3 py-1 {{ $id == 2 ? 'text-white' : '' }}">
                                                    {{ $pkg['name'] }}
                                                </span>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($features as $group => $items)
                                        <tr class="table-group-header">
                                            <td colspan="4" class="py-2 pl-4">
                                                <i class="bi bi-folder2-open mr-2"></i> {{ $group }}
                                            </td>
                                        </tr>
                                        @foreach ($items as $item)
                                            <tr>
                                                <td class="align-middle pl-4">
                                                    <div class="d-flex flex-column">
                                                        <span
                                                            class="font-weight-bold text-dark">{{ $item->feature_name }}</span>
                                                        <code class="small text-muted"
                                                            style="font-size: 75%;">{{ $item->feature_key }}</code>
                                                    </div>
                                                </td>
                                                @for ($i = 1; $i <= 3; $i++)
                                                    <td class="td-center align-middle">
                                                        <div
                                                            class="custom-control custom-switch custom-switch-lg d-inline-block">
                                                            <input type="checkbox"
                                                                class="custom-control-input update-feature"
                                                                id="switch_{{ $item->id }}_{{ $i }}"
                                                                data-id="{{ $item->id }}"
                                                                data-pkg="{{ $i }}"
                                                                {{ $item->{'package_' . $i} ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="switch_{{ $item->id }}_{{ $i }}"
                                                                style="padding-left: 0rem;"></label>
                                                            {{-- Padding left disesuaikan agar tombol bulat berada tepat di tengah --}}
                                                        </div>
                                                    </td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-flex align-items-center text-muted">
                            <i class="bi bi-info-circle-fill text-info mr-2"></i>
                            <span class="small">Perubahan status fitur akan disimpan secara otomatis dan langsung
                                diterapkan pada hak akses user.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('page_scripts')
        <script>
            $(document).ready(function() {
                $('.update-feature').on('change', function() {
                    let checkbox = $(this);
                    let id = checkbox.data('id');
                    let package_num = checkbox.data('pkg');
                    let status = checkbox.is(':checked') ? 1 : 0;

                    // Feedback visual saat loading
                    let parentCell = checkbox.closest('td');
                    parentCell.css('opacity', '0.4');

                    $.ajax({
                        url: "{{ route('feature-manager.update') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            package_num: package_num,
                            status: status
                        },
                        success: function(response) {
                            parentCell.css('opacity', '1');
                            // Opsional: Gunakan toastr jika tersedia
                            // toastr.success('Akses fitur diperbarui');
                        },
                        error: function(xhr) {
                            parentCell.css('opacity', '1');
                            alert('Gagal memperbarui status. Silakan coba lagi.');
                            checkbox.prop('checked', !checkbox.is(':checked'));
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
