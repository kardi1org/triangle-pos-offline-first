@extends('layouts.app')

@section('title', 'Shift History Report')

@section('third_party_stylesheets')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Shift History Report</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-5">

        <div class="card mb-4 shadow-sm">
            <div class="card-body py-3">
                <form action="{{ route('shift.reports') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="font-weight-bold">From Date</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="font-weight-bold">To Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary mr-2"><i class="bi bi-filter"></i> Filter</button>
                            <a href="{{ route('shift.reports') }}" class="btn btn-light border mr-2">Reset</a>
                            <a href="{{ route('shift.reports.export', request()->all()) }}"
                                class="btn btn-success text-white">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white font-weight-bold py-3">
                        Shift Summary List
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Cashier</th>
                                        <th>Shift Time</th>
                                        <th class="text-right">Exp. Cash</th>
                                        <th class="text-right">Act. Cash</th>
                                        <th class="text-right">Diff</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shifts as $item)
                                        @php
                                            $diff = $item->ending_cash - $item->expected_ending_cash;
                                            $isClosed = $item->status == 'closed';
                                        @endphp
                                        <tr onclick="loadDetail({{ $item->id }})" id="row-{{ $item->id }}"
                                            style="cursor: pointer;" class="shift-row">
                                            <td class="align-middle">
                                                <strong
                                                    class="text-dark">{{ $users[$item->user_id]->name ?? 'Unknown User' }}</strong>
                                                <div class="small text-muted">ID: #{{ $item->id }}</div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex flex-column">
                                                    <small class="text-success"><i class="bi bi-box-arrow-in-right"></i>
                                                        {{ \Carbon\Carbon::parse($item->open_time)->format('d/m H:i') }}</small>
                                                    <small class="text-danger"><i class="bi bi-box-arrow-left"></i>
                                                        {{ $item->close_time ? \Carbon\Carbon::parse($item->close_time)->format('d/m H:i') : 'Active' }}</small>
                                                </div>
                                            </td>
                                            <td class="text-right align-middle font-weight-bold">
                                                {{ number_format($item->expected_ending_cash, 0, ',', '.') }}
                                            </td>
                                            <td class="text-right align-middle font-weight-bold">
                                                {{ $isClosed ? number_format($item->ending_cash, 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="text-right align-middle">
                                                @if ($isClosed)
                                                    @if ($diff < 0)
                                                        <span
                                                            class="badge badge-danger px-2">{{ number_format($diff, 0, ',', '.') }}</span>
                                                    @elseif($diff > 0)
                                                        <span
                                                            class="badge badge-success px-2">+{{ number_format($diff, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="badge badge-light border px-2">OK</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-warning text-white">Open</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                No shift data found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <small class="text-muted">Showing {{ $shifts->count() }} of {{ $shifts->total() }} records</small>
                        <div>
                            {{ $shifts->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div style="position: sticky; top: 20px;">
                    <div class="card shadow-sm border-0">
                        <div class="card-body" id="detail-container" style="min-height: 300px;">
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-receipt" style="font-size: 3rem; opacity: 0.3;"></i>
                                <h6 class="mt-3">Click a row on the left<br>to view receipt details.</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('page_scripts')
        <style>
            .table-active-custom {
                background-color: #e8f0fe !important;
                border-left: 4px solid #007bff;
            }

            .page-item svg {
                width: 1em;
                height: 1em;
            }
        </style>
        <script>
            function loadDetail(id) {
                $('#detail-container').html(
                    '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading Data...</p></div>'
                );

                $('.shift-row').removeClass('table-active-custom');
                $('#row-' + id).addClass('table-active-custom');

                $.ajax({
                    url: "{{ url('/shift/reports/detail') }}/" + id,
                    type: "GET",
                    success: function(response) {
                        $('#detail-container').html(response);
                    },
                    error: function() {
                        $('#detail-container').html(
                            '<div class="text-danger text-center p-4">Failed to load details.</div>');
                    }
                });
            }

            function printDiv(divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                location.reload();
            }
        </script>
    @endpush
@endsection
