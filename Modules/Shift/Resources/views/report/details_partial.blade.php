<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div id="printableArea" class="p-4 bg-white position-relative">

            <div
                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.03; z-index: 0; pointer-events: none;">
                <i class="bi bi-shop" style="font-size: 15rem;"></i>
            </div>

            <div style="position: relative; z-index: 1;">
                <div class="text-center mb-4 border-bottom pb-3">
                    <h4 class="font-weight-bold mb-1 text-uppercase">{{ settings()->company_name ?? 'POS SYSTEM' }}</h4>
                    <p class="text-muted small mb-2">{{ settings()->company_address ?? 'Cashier Shift Closure Report' }}
                    </p>
                    <h6 class="font-weight-bold mt-2">SHIFT SLIP #{{ $shift->id }}</h6>
                </div>

                <div class="row mb-3 small">
                    <div class="col-6">
                        <span class="text-muted d-block">Cashier:</span>
                        <strong class="text-dark">{{ $cashier->name ?? 'Unknown' }}</strong>
                    </div>
                    <div class="col-6 text-right">
                        <span class="text-muted d-block">Print Date:</span>
                        <span class="text-dark">{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-3 small bg-light p-2 rounded">
                    <div>
                        <span class="text-muted">Open:</span><br>
                        <strong>{{ \Carbon\Carbon::parse($shift->open_time)->format('d M H:i') }}</strong>
                    </div>
                    <div class="text-right">
                        <span class="text-muted">Close:</span><br>
                        <strong>{{ $shift->close_time ? \Carbon\Carbon::parse($shift->close_time)->format('d M H:i') : 'Still Active' }}</strong>
                    </div>
                </div>

                <table class="table table-sm table-borderless mb-2">
                    <tbody>
                        <tr>
                            <td class="pl-0">Starting Cash</td>
                            <td class="text-right pr-0">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="pl-0">Total Cash Sales</td>
                            <td class="text-right pr-0 text-success">+ Rp {{ number_format($sales, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="pl-0">Other Income</td>
                            <td class="text-right pr-0 text-success">+ Rp {{ number_format($income, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="pl-0">Cash Expenses</td>
                            <td class="text-right pr-0 text-danger">- Rp {{ number_format($expense, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div style="border-top: 2px dashed #ccc; margin: 10px 0;"></div>

                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr class="font-weight-bold text-dark">
                            <td class="pl-0">System Total (Exp)</td>
                            <td class="text-right pr-0">Rp
                                {{ number_format($shift->expected_ending_cash, 0, ',', '.') }}</td>
                        </tr>
                        @if ($shift->status == 'closed')
                            <tr class="font-weight-bold" style="font-size: 1rem;">
                                <td class="pl-0 text-primary">Physical Cash (Actual)</td>
                                <td class="text-right pr-0 text-primary">Rp
                                    {{ number_format($shift->ending_cash, 0, ',', '.') }}</td>
                            </tr>
                            @php $diff = $shift->ending_cash - $shift->expected_ending_cash; @endphp
                            <tr>
                                <td class="pl-0 pt-3">Difference</td>
                                <td class="text-right pr-0 pt-3">
                                    <span class="badge {{ $diff >= 0 ? 'badge-success' : 'badge-danger' }} p-2"
                                        style="font-size: 0.9rem;">
                                        {{ $diff >= 0 ? '+' : '' }} Rp {{ number_format($diff, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                @if ($shift->note)
                    <div class="mt-4">
                        <small class="text-muted font-weight-bold">NOTE:</small>
                        <p class="small text-muted border border-secondary p-2 rounded mb-0"
                            style="background-color: #fdfdfd; border-style: dashed !important;">
                            {{ $shift->note }}
                        </p>
                    </div>
                @endif

                {{-- <div class="row mt-5 pt-3 text-center small text-muted">
                    <div class="col-6">
                        <p class="mb-5">Created By,</p>
                        <p class="font-weight-bold border-top d-inline-block px-4 pt-1 border-secondary">
                            {{ $shift->user->name }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-5">Verified By,</p>
                        <p class="font-weight-bold border-top d-inline-block px-4 pt-1 border-secondary">Supervisor</p>
                    </div>
                </div> --}}
            </div>
        </div>

        <div class="card-footer bg-white text-right no-print">
            <button onclick="printDiv('printableArea')" class="btn btn-dark">
                <i class="bi bi-printer-fill"></i> Print Slip
            </button>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .no-print {
            display: none !important;
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
            margin: 0;
            padding: 20px !important;
            border: none;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
