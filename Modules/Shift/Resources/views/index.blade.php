@extends('layouts.app')

@section('title', 'Management Shift & Kas')

@section('third_party_stylesheets')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Management Shift & Kas</li>
    </ol>
@endsection

@section('content')
    <div class="container">
        <div class="card">
            {{-- <div class="card-header bg-primary text-white">Management Shift & Kas</div> --}}
            <div class="card-body">
                @if (!$activeShift)
                    <h4>Buka Shift Baru</h4>
                    <form action="{{ route('shift.open') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Modal Awal (Uang Cash)</label>
                            <input type="number" name="starting_cash" class="form-control" placeholder="0" required>
                        </div>
                        <button type="submit" class="btn btn-success mt-2">Buka Shift</button>
                    </form>
                @else
                    <div class="alert alert-info">Shift sedang berjalan sejak: {{ $activeShift->open_time }}</div>
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">Tutup Shift</div>
                        <div class="card-body">
                            <div class="row mb-3 text-dark">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Modal Awal</td>
                                            <td class="text-right">Rp {{ number_format($activeShift->starting_cash) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Penjualan Tunai</td>
                                            <td class="text-right text-success">+ Rp
                                                {{ number_format($totalCashSales ?? 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Pemasukan Kas Lainnya</td>
                                            <td class="text-right text-success">+ Rp {{ number_format($totalIncome ?? 0) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Pengeluaran Kas</td>
                                            <td class="text-right text-danger">- Rp {{ number_format($totalExpense ?? 0) }}
                                            </td>
                                        </tr>
                                        <tr class="font-weight-bold">
                                            <td>Kas Seharusnya di Laci</td>
                                            <td class="text-right text-primary">Rp
                                                {{ number_format($activeShift->starting_cash + ($totalCashSales ?? 0) + ($totalIncome ?? 0) - ($totalExpense ?? 0)) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-success btn-block shadow-sm" data-toggle="modal"
                                        data-target="#modalKas" onclick="setKasType('pemasukan')">
                                        <i class="bi bi-plus-circle"></i> Catat Pemasukan Kas
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-warning btn-block shadow-sm text-white"
                                        data-toggle="modal" data-target="#modalKas" onclick="setKasType('pengeluaran')">
                                        <i class="bi bi-dash-circle"></i> Catat Pengeluaran Kas
                                    </button>
                                </div>
                            </div>
                            <form action="{{ route('shift.close', $activeShift->id) }}" method="POST" id="formCloseShift">
                                @csrf
                                <div class="form-group">
                                    <label><b>Input Total Uang Cash Terakhir</b></label>
                                    <input type="number" name="ending_cash" id="ending_cash_input"
                                        class="form-control form-control-lg" placeholder="0" required>
                                </div>
                                <div class="form-group mt-2">
                                    <label>Catatan Tutup Shift</label>
                                    <textarea name="note" id="note_input" class="form-control" rows="2"
                                        placeholder="Contoh: Ada selisih karena uang kembalian kurang..."></textarea>
                                </div>
                                <button type="button" class="btn btn-danger btn-block mt-3" onclick="confirmCloseShift()">
                                    <i class="bi bi-lock-fill"></i> Tutup & Simpan Laporan
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKas" tabindex="-1" role="dialog" aria-labelledby="modalKasLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header shadow-sm" id="modalHeader">
                    <h5 class="modal-title text-white" id="modalKasLabel">Input Transaksi Kas</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('shift.transaction.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" id="kas_type">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="category" class="form-control"
                                placeholder="Contoh: Parkir, Bensin, Tambah Modal" required>
                        </div>
                        <div class="form-group">
                            <label>Jumlah (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Keterangan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmClose" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Konfirmasi Tutup Shift</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-3">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    <h4 class="mt-2">Apakah Anda Yakin?</h4>
                    <p class="text-muted">Pastikan uang fisik sudah dihitung dengan benar.</p>

                    <div class="alert alert-warning text-left">
                        <div class="mb-2">
                            <small class="text-muted">Total Uang Inputan:</small><br>
                            <strong style="font-size: 1.2rem;" id="display_ending_cash">Rp 0</strong>
                        </div>

                        <div class="border-top pt-2">
                            <small class="text-muted">Catatan:</small><br>
                            <em id="display_note" class="text-dark"></em>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger px-4" onclick="submitCloseShift()">Ya, Tutup
                        Shift</button>
                </div>
            </div>
        </div>
    </div>

    @push('page_scripts')
        <script>
            function setKasType(type) {
                document.getElementById('kas_type').value = type;
                const header = document.getElementById('modalHeader');
                const title = document.getElementById('modalKasLabel');

                if (type === 'pemasukan') {
                    header.className = 'modal-header bg-success shadow-sm';
                    title.innerText = 'Input Kas Masuk (Pemasukan)';
                } else {
                    header.className = 'modal-header bg-warning shadow-sm';
                    title.innerText = 'Input Kas Keluar (Pengeluaran)';
                }
            }
        </script>
        <script>
            function confirmCloseShift() {
                const form = document.getElementById('formCloseShift');
                const inputCash = document.getElementById('ending_cash_input');
                const inputNote = document.getElementById('note_input'); // Ambil elemen note

                // 1. Cek Validasi HTML5
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // 2. Tampilkan Uang
                const cashValue = parseFloat(inputCash.value || 0).toLocaleString('id-ID');
                document.getElementById('display_ending_cash').innerText = "Rp " + cashValue;

                // 3. Tampilkan Note (Jika kosong, tampilkan tanda strip)
                const noteValue = inputNote.value.trim();
                document.getElementById('display_note').innerText = noteValue ? noteValue : "- Tidak ada catatan -";

                // 4. Tampilkan Modal
                $('#modalConfirmClose').modal('show');
            }

            function submitCloseShift() {
                document.getElementById('formCloseShift').submit();
            }
        </script>
    @endpush
@endsection
