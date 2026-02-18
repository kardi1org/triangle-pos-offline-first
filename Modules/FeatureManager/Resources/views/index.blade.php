@extends('layouts.app')

@section('title', 'Feature & Package Management')

@section('third_party_stylesheets')
    <style>
        .table-group-header {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .custom-switch-lg .custom-control-label::before {
            height: 1.5rem;
            width: 2.75rem;
            border-radius: 1rem;
        }

        .custom-switch-lg .custom-control-label::after {
            width: calc(1.5rem - 4px);
            height: calc(1.5rem - 4px);
            border-radius: calc(1rem - (1.5rem / 2));
        }

        .custom-switch-lg .custom-control-input:checked~.custom-control-label::after {
            transform: translateX(1.25rem);
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
                    <div
                        class="card-header bg-primary text-white font-weight-bold d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-gear-fill"></i> Feature & Package Access Control
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40%;">Feature Name</th>
                                        <th class="text-center" style="width: 20%;">
                                            <span class="badge badge-secondary p-2">Package 1 (Basic)</span>
                                        </th>
                                        <th class="text-center" style="width: 20%;">
                                            <span class="badge badge-info p-2 text-white">Package 2 (Pro)</span>
                                        </th>
                                        <th class="text-center" style="width: 20%;">
                                            <span class="badge badge-success p-2">Package 3 (Premium)</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($features as $group => $items)
                                        <tr class="table-group-header">
                                            <td colspan="4" class="py-3">
                                                <i class="bi bi-collection-fill text-primary mr-2"></i> {{ $group }}
                                            </td>
                                        </tr>
                                        @foreach ($items as $item)
                                            <tr>
                                                <td class="align-middle pl-4">
                                                    <span class="font-weight-bold">{{ $item->feature_name }}</span><br>
                                                    <small class="text-muted">{{ $item->feature_key }}</small>
                                                </td>
                                                @for ($i = 1; $i <= 3; $i++)
                                                    <td class="text-center align-middle">
                                                        <div class="custom-control custom-switch custom-switch-lg">
                                                            <input type="checkbox"
                                                                class="custom-control-input update-feature"
                                                                id="switch_{{ $item->id }}_{{ $i }}"
                                                                data-id="{{ $item->id }}" data-pkg="{{ $i }}"
                                                                {{ $item->{'package_' . $i} ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="switch_{{ $item->id }}_{{ $i }}"></label>
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
                    <div class="card-footer bg-light text-muted small">
                        <i class="bi bi-info-circle"></i> Perubahan pada pengaturan ini akan langsung berdampak pada akses
                        menu di sisi user sesuai paket yang mereka miliki.
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

                    // Berikan feedback visual sementara (opsional)
                    checkbox.closest('td').css('opacity', '0.5');

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
                            checkbox.closest('td').css('opacity', '1');
                            // Jika ada library toastr/sweetalert bisa ditambahkan di sini
                            console.log('Update Success:', response.message);
                        },
                        error: function(xhr) {
                            checkbox.closest('td').css('opacity', '1');
                            alert('Something went wrong! Please refresh.');
                            checkbox.prop('checked', !checkbox.is(':checked')); // Revert switch
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
