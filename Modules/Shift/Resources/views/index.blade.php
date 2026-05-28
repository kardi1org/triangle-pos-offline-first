@extends('layouts.app')

@section('title', 'Shift Management')

@section('third_party_stylesheets')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Shift Management</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @include('utils.alerts')

                @if ($activeShift)
                    <div class="row mb-4">
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-success btn-lg btn-block shadow-sm" data-toggle="modal"
                                data-target="#modalKas" onclick="setKasType('pemasukan')">
                                <i class="bi bi-plus-circle"></i> CASH IN (INCOME)
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-warning btn-lg btn-block shadow-sm text-white"
                                data-toggle="modal" data-target="#modalKas" onclick="setKasType('pengeluaran')">
                                <i class="bi bi-dash-circle"></i> CASH OUT (EXPENSE)
                            </button>
                        </div>
                    </div>

                    <div class="card border-danger shadow-sm">
                        <div class="card-header bg-danger text-white font-weight-bold">
                            <i class="bi bi-clock-history"></i> Active Shift Information
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 text-dark">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td>Starting Cash</td>
                                            <td class="text-right font-weight-bold">Rp
                                                {{ number_format($activeShift->starting_cash, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Cash Sales</td>
                                            <td class="text-right text-success">+ Rp
                                                {{ number_format($totalCashSales ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Other Cash Income</td>
                                            <td class="text-right text-success">+ Rp
                                                {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Cash Expenses</td>
                                            <td class="text-right text-danger">- Rp
                                                {{ number_format($totalExpense ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="pt-2 font-weight-bold">Expected Cash in Drawer</td>
                                            <td class="pt-2 text-right text-primary font-weight-bold"
                                                style="font-size: 1.1rem;">
                                                Rp
                                                {{ number_format($activeShift->starting_cash + ($totalCashSales ?? 0) + ($totalIncome ?? 0) - ($totalExpense ?? 0), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6 border-left">
                                    <div class="text-center py-4">
                                        <h5 class="text-muted">Shift ID: #{{ $activeShift->id }}</h5>
                                        @if ($activeShift->terminal_id)
                                            <div class="badge badge-info mb-2">
                                                <i class="bi bi-cpu"></i> TERMINAL: {{ $activeShift->terminal_id }}
                                            </div>
                                        @endif
                                        <p class="mb-1">Opened by: <strong>{{ Auth::user()->name }}</strong></p>
                                        <p class="text-muted small">
                                            {{ \Carbon\Carbon::parse($activeShift->open_time)->format('l, d F Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <form action="{{ route('shift.close', $activeShift->id) }}" method="POST" id="formCloseShift">
                                @csrf
                                <div class="form-group">
                                    <label class="font-weight-bold">Input Actual Cash Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" name="ending_cash" id="ending_cash_input"
                                            class="form-control form-control-lg" placeholder="0" required>
                                    </div>
                                    <small class="text-muted font-italic">* Count all physical money currently in the
                                        drawer.</small>
                                </div>
                                <div class="form-group mt-3">
                                    <label>Closing Note (Optional)</label>
                                    <textarea name="note" id="note_input" class="form-control" rows="2"
                                        placeholder="Example: Difference due to lack of small change..."></textarea>
                                </div>
                                <button type="button" class="btn btn-danger btn-block mt-4 py-3 font-weight-bold"
                                    onclick="confirmCloseShift()">
                                    <i class="bi bi-lock-fill"></i> CLOSE SHIFT & SAVE REPORT
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm mt-5" style="max-width: 600px; margin: 0 auto;">
                        <div class="card-header bg-primary text-white text-center py-3">
                            <h4 class="mb-0 font-weight-bold"><i class="bi bi-shop"></i> Open New Shift</h4>
                        </div>
                        {{-- Di dalam form action="{{ route('shift.open') }}" --}}
                        <div class="card-body p-4">
                            <form action="{{ route('shift.open') }}" method="POST">
                                @csrf

                                {{-- TAMBAHKAN INI: Input Terminal ID --}}
                                <div class="form-group">
                                    <label class="font-weight-bold">Terminal / No. Kasir</label>
                                    <input type="text" name="terminal_id" class="form-control mb-3"
                                        value="{{ request()->cookie('terminal_id') }}" placeholder="Contoh: 01" required>
                                    <small class="form-text text-muted">Identitas komputer/terminal ini.</small>
                                </div>

                                <div class="form-group text-center mb-4">
                                    {{-- Input starting_cash yang sudah ada --}}
                                    <label class="font-weight-bold h5">Starting Cash (Cash in Hand)</label>
                                    <div class="input-group input-group-lg mt-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" name="starting_cash" class="form-control" placeholder="0"
                                            required autofocus>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    Start Shift
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKas" tabindex="-1" role="dialog" aria-labelledby="modalKasLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header shadow-sm" id="modalHeader">
                    <h5 class="modal-title text-white" id="modalKasLabel">Cash Transaction</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('shift.transaction.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" id="kas_type">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Category <span class="text-danger">*</span></label>
                            <input type="text" name="category" class="form-control"
                                placeholder="e.g. Parking, Supplies, Add Change" required>
                        </div>
                        <div class="form-group">
                            <label>Amount (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label>Note</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Additional details..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmClose" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Shift Closure</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">Are you sure?</h4>
                    <p class="text-muted">Ensure the physical cash has been counted correctly.<br>This action cannot be
                        undone.</p>

                    <div class="alert alert-warning text-left">
                        <div class="mb-2">
                            <small class="text-muted">Total Cash Input:</small><br>
                            <strong style="font-size: 1.2rem;" id="display_ending_cash">Rp 0</strong>
                        </div>

                        <div class="border-top pt-2">
                            <small class="text-muted">Note:</small><br>
                            <em id="display_note" class="text-dark"></em>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" onclick="submitCloseShift()">Yes, Close
                        Shift</button>
                </div>
            </div>
        </div>
    </div>

    @push('page_scripts')
        <script>
            // Set Transaction Modal Type (Color & Title)
            function setKasType(type) {
                document.getElementById('kas_type').value = type;
                const header = document.getElementById('modalHeader');
                const title = document.getElementById('modalKasLabel');

                // Note: 'pemasukan' and 'pengeluaran' are DB ENUM values, do not translate the comparison string
                if (type === 'pemasukan') {
                    header.className = 'modal-header bg-success shadow-sm';
                    title.innerText = 'Input Cash In (Income)';
                } else {
                    header.className = 'modal-header bg-warning shadow-sm';
                    title.innerText = 'Input Cash Out (Expense)';
                }
            }

            // Confirmation Logic
            function confirmCloseShift() {
                const form = document.getElementById('formCloseShift');
                const inputCash = document.getElementById('ending_cash_input');
                const inputNote = document.getElementById('note_input');

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const cashValue = parseFloat(inputCash.value || 0).toLocaleString('id-ID');
                document.getElementById('display_ending_cash').innerText = "Rp " + cashValue;

                const noteValue = inputNote.value.trim();
                document.getElementById('display_note').innerText = noteValue ? noteValue : "- No notes -";

                $('#modalConfirmClose').modal('show');
            }

            function submitCloseShift() {
                document.getElementById('formCloseShift').submit();
            }
        </script>
    @endpush
@endsection
