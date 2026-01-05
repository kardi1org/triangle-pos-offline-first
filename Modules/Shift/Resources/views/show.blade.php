@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 font-weight-bold">Laporan Penutupan Shift</h4>
                        <a href="{{ route('shift.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="card-body" id="printableArea">
                        <div class="text-center mb-4">
                            <h3>{{ settings()->company_name ?? 'POS System' }}</h3>
                            <p class="text-muted mb-0">Shift Report #{{ $shift->id }}</p>
                            <p class="text-muted">Kasir: <strong>{{ Auth::user()->name }}</strong></p>
                            <hr>
                            <div class="row text-left">
                                <div class="col-6">
                                    <small>Waktu Buka:</small><br>
                                    <strong>{{ \Carbon\Carbon::parse($shift->open_time)->format('d M Y, H:i') }}</strong>
                                </div>
                                <div class="col-6 text-right">
                                    <small>Waktu Tutup:</small><br>
                                    <strong>{{ \Carbon\Carbon::parse($shift->close_time)->format('d M Y, H:i') }}</strong>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Keterangan</th>
                                    <th class="text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Modal Awal (Cash in Hand)</td>
                                    <td class="text-right">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Total Penjualan Tunai (+)</td>
                                    <td class="text-right text-success">Rp {{ number_format($sales, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Pemasukan Kas Lainnya (+)</td>
                                    <td class="text-right text-success">Rp {{ number_format($income, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Pengeluaran Kas (-)</td>
                                    <td class="text-right text-danger">Rp {{ number_format($expense, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="font-weight-bold bg-light">
                                    <td>Ekspektasi Kas (Sistem)</td>
                                    <td class="text-right">Rp
                                        {{ number_format($shift->expected_ending_cash, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div
                            class="alert {{ $shift->ending_cash >= $shift->expected_ending_cash ? 'alert-success' : 'alert-danger' }}">
                            <div class="row">
                                <div class="col-6">
                                    <strong>Uang Fisik di Laci:</strong>
                                    <h4 class="mb-0">Rp {{ number_format($shift->ending_cash, 0, ',', '.') }}</h4>
                                </div>
                                <div class="col-6 text-right">
                                    <strong>Selisih:</strong>
                                    @php $diff = $shift->ending_cash - $shift->expected_ending_cash; @endphp
                                    <h4 class="mb-0">{{ $diff >= 0 ? '+' : '' }}Rp
                                        {{ number_format($diff, 0, ',', '.') }}</h4>
                                    <small>{{ $diff == 0 ? 'Balance (Sesuai)' : ($diff < 0 ? 'Minus (Kurang)' : 'Surplus (Lebih)') }}</small>
                                </div>
                            </div>
                        </div>

                        @if ($shift->note)
                            <div class="mt-3">
                                <strong>Catatan:</strong>
                                <p class="text-muted border p-2 rounded">{{ $shift->note }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-white text-right">
                        <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak
                            Laporan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #printableArea,
            #printableArea * {
                visibility: visible;
            }

            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .card-footer,
            .btn {
                display: none !important;
            }
        }
    </style>
@endsection
