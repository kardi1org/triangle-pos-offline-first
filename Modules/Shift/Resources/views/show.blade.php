@extends('layouts.app')

@section('title', 'Shift Summary Result')

@section('content')
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7">

                <div class="alert alert-success shadow-sm mb-4 border-0 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill h2 mb-0 mr-3"></i>
                    <div>
                        <h5 class="mb-0 font-weight-bold">Shift Successfully Closed!</h5>
                        <p class="mb-0 small">The final report has been saved to the database.</p>
                    </div>
                </div>

                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">

                        <div class="text-center mb-4 pb-3 border-bottom">
                            <i class="bi bi-shield-check text-success" style="font-size: 3.5rem;"></i>
                            <h3 class="font-weight-bold mt-3 mb-1">Final Shift Summary</h3>
                            <p class="text-muted text-uppercase small">Shift ID: #{{ $shift->id }}</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <small class="text-muted d-block">Cashier Name:</small>
                                <span class="font-weight-bold">{{ $cashier->name ?? 'Unknown' }}</span>
                            </div>
                            <div class="col-6 text-right">
                                <small class="text-muted d-block">Status:</small>
                                <span class="badge badge-secondary px-3 py-1">CLOSED</span>
                            </div>
                        </div>

                        <div class="bg-light rounded p-3 mb-4">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block">Open Time:</small>
                                    <strong
                                        class="small">{{ \Carbon\Carbon::parse($shift->open_time)->format('M d, Y - H:i') }}</strong>
                                </div>
                                <div class="col-6 text-right">
                                    <small class="text-muted d-block">Close Time:</small>
                                    <strong
                                        class="small">{{ \Carbon\Carbon::parse($shift->close_time)->format('M d, Y - H:i') }}</strong>
                                </div>
                            </div>
                        </div>

                        <table class="table table-borderless mb-0">
                            <tr class="text-muted">
                                <td>Starting Cash</td>
                                <td class="text-right">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="text-muted">
                                <td>Total Cash Sales</td>
                                <td class="text-right text-success">+ Rp {{ number_format($sales ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="text-muted">
                                <td>Other Cash Income</td>
                                <td class="text-right text-success">+ Rp {{ number_format($income ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="text-muted">
                                <td>Cash Expenses</td>
                                <td class="text-right text-danger">- Rp {{ number_format($expense ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-top">
                                <td class="pt-3 font-weight-bold">Expected Cash</td>
                                <td class="pt-3 text-right font-weight-bold">Rp
                                    {{ number_format($shift->expected_ending_cash, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="pt-1 font-weight-bold h5 text-primary">Actual Physical Cash</td>
                                <td class="pt-1 text-right font-weight-bold h5 text-primary">Rp
                                    {{ number_format($shift->ending_cash, 0, ',', '.') }}</td>
                            </tr>
                        </table>

                        @php $diff = $shift->ending_cash - $shift->expected_ending_cash; @endphp
                        <div class="mt-4 p-3 rounded text-center {{ $diff < 0 ? 'bg-danger-light text-danger' : 'bg-success-light text-success' }}"
                            style="border: 1px dashed;">
                            <span class="small text-uppercase font-weight-bold">Difference (Gap)</span><br>
                            <h4 class="mb-0 font-weight-bold">
                                {{ $diff >= 0 ? '+' : '' }} Rp {{ number_format($diff, 0, ',', '.') }}
                            </h4>
                        </div>

                        @if ($shift->note)
                            <div class="mt-4">
                                <small class="text-muted font-weight-bold">CLOSING NOTE:</small>
                                <p class="font-italic text-muted small p-2 bg-light border rounded">{{ $shift->note }}</p>
                            </div>
                        @endif

                    </div>

                    <div class="card-footer bg-white border-top p-4">
                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('shift.index') }}" class="btn btn-outline-primary btn-block">
                                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('shift.print', $shift->id) }}" target="_blank"
                                    class="btn btn-dark btn-block">
                                    <i class="bi bi-printer"></i> Print Summary
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Light color alerts for the difference box */
        .bg-danger-light {
            background-color: #fff5f5;
            border-color: #feb2b2 !important;
        }

        .bg-success-light {
            background-color: #f0fff4;
            border-color: #9ae6b4 !important;
        }

        @media print {

            .btn,
            .alert,
            .navbar,
            .main-sidebar,
            .main-footer {
                display: none !important;
            }

            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
            }

            .card-body {
                padding: 10px !important;
            }
        }
    </style>
@endsection
