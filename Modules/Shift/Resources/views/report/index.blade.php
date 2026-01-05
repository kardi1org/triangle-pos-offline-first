@extends('layouts.app')

@section('title', 'Laporan Riwayat Shift')

@section('third_party_stylesheets')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Laporan Riwayat Shift</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="card mb-3">
            <div class="card-body py-3">
                <form action="{{ route('shift.reports') }}" method="GET" class="row align-items-center">
                    <div class="col-md-3">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-block"><i class="bi bi-filter"></i>
                            Filter</button>
                    </div>
                    <div class="col-md-2 mt-4">
                        <a href="{{ route('shift.reports') }}" class="btn btn-secondary btn-block">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card" style="height: 75vh; overflow-y: auto;">
                    <div class="card-header bg-white font-weight-bold">
                        Daftar Shift
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($shifts as $item)
                            <a href="javascript:void(0)" onclick="loadDetail({{ $item->id }})"
                                class="list-group-item list-group-item-action flex-column align-items-start shift-item"
                                id="shift-item-{{ $item->id }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 text-primary font-weight-bold">{{ $item->user->name }}</h6>
                                    <small
                                        class="text-muted">{{ \Carbon\Carbon::parse($item->open_time)->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">
                                    <small>Buka:
                                        {{ \Carbon\Carbon::parse($item->open_time)->format('d/m H:i') }}</small><br>
                                    <small>Tutup:
                                        {{ $item->close_time ? \Carbon\Carbon::parse($item->close_time)->format('d/m H:i') : '-' }}</small>
                                </p>
                                @if ($item->status == 'open')
                                    <span class="badge badge-success">Sedang Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Closed</span>
                                    @php $diff = $item->ending_cash - $item->expected_ending_cash; @endphp
                                    @if ($diff < 0)
                                        <span class="badge badge-danger">Selisih Minus</span>
                                    @elseif($diff > 0)
                                        <span class="badge badge-success">Surplus</span>
                                    @endif
                                @endif
                            </a>
                        @empty
                            <div class="text-center p-3 text-muted">Tidak ada data shift.</div>
                        @endforelse
                    </div>
                    <div class="p-2">
                        {{ $shifts->links() }}
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card" style="height: 75vh; overflow-y: auto;">
                    <div class="card-body d-flex align-items-center justify-content-center" id="detail-container">
                        <div class="text-center text-muted">
                            <i class="bi bi-arrow-left-circle" style="font-size: 3rem;"></i>
                            <h5>Pilih salah satu shift di kiri untuk melihat rincian</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('page_scripts')
        <script>
            function loadDetail(id) {
                // Efek loading
                $('#detail-container').html(
                    '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p>Memuat Rincian...</p></div>'
                );

                // Highlight item yang dipilih
                $('.shift-item').removeClass('active');
                $('#shift-item-' + id).addClass('active');

                // AJAX Request
                $.ajax({
                    url: "{{ url('/shift/reports/detail') }}/" + id,
                    type: "GET",
                    success: function(response) {
                        $('#detail-container').html(response);
                    },
                    error: function() {
                        $('#detail-container').html(
                            '<div class="text-danger text-center">Gagal memuat data. Silakan coba lagi.</div>');
                    }
                });
            }

            function printDiv(divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;

                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;

                // Refresh page agar JS kembali berfungsi setelah print (karena DOM diganti)
                location.reload();
            }
        </script>
    @endpush
@endsection
