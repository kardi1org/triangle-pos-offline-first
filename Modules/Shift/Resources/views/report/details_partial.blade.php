<div id="printableArea" class="w-100">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h4 class="mb-0 font-weight-bold">Rincian Shift #{{ $shift->id }}</h4>
            <span class="text-muted">Kasir: {{ $shift->user->name }}</span>
        </div>
        <div>
            @if ($shift->status == 'open')
                <span class="badge badge-success p-2">SHIFT MASIH AKTIF</span>
            @else
                <button onclick="printDiv('printableArea')" class="btn btn-primary btn-sm no-print">
                    <i class="bi bi-printer"></i> Cetak Laporan
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <table class="table table-sm table-borderless">
                <tr>
                    <td>Waktu Buka:</td>
                    <td class="font-weight-bold">{{ $shift->open_time }}</td>
                </tr>
                <tr>
                    <td>Waktu Tutup:</td>
                    <td class="font-weight-bold">{{ $shift->close_time ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
        </div>
    </div>

    <table class="table table-bordered mt-2">
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
                <td class="text-right">Rp {{ number_format($shift->expected_ending_cash, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if ($shift->status == 'closed')
        <div class="alert {{ $shift->ending_cash >= $shift->expected_ending_cash ? 'alert-success' : 'alert-danger' }}">
            <div class="row align-items-center">
                <div class="col-6">
                    <small>Uang Fisik di Laci</small><br>
                    <h3 class="mb-0">Rp {{ number_format($shift->ending_cash, 0, ',', '.') }}</h3>
                </div>
                <div class="col-6 text-right">
                    <small>Selisih</small><br>
                    @php $diff = $shift->ending_cash - $shift->expected_ending_cash; @endphp
                    <h3 class="mb-0">{{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    @endif

    @if ($shift->note)
        <div class="mt-3">
            <strong>Catatan Kasir:</strong>
            <p class="text-muted border p-2 rounded bg-light">{{ $shift->note }}</p>
        </div>
    @endif
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            visibility: hidden;
        }

        #printableArea {
            visibility: visible;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>
