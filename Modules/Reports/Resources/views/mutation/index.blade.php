@extends('layouts.app')

@section('title', 'Mutation Report')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Mutation</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('report.mutation.index') }}" method="GET">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ $startDate }}">
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                                </div>
                                <div class="col-md-4">
                                    <label>Receive Type (Account)</label>
                                    <select name="receive_type" class="form-control" required>
                                        <option value="">-- Select Method --</option>
                                        @foreach ($paymentMethods as $method)
                                            <option value="{{ $method->methode_name }}"
                                                {{ $receiveType == $method->methode_name ? 'selected' : '' }}>
                                                {{ $method->methode_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="bi bi-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Details</th>
                                        <th class="text-right">Debet (+)</th>
                                        <th class="text-right">Kredit (-)</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($receiveType)
                                        <!-- Saldo Awal Row -->
                                        <tr class="bg-light text-dark">
                                            <td colspan="2"></td>
                                            <td><strong>SALDO AWAL</strong></td>
                                            <td class="text-right">-</td>
                                            <td class="text-right">-</td>
                                            <td class="text-right">
                                                <strong>{{ number_format($openingBalance, 0, ',', '.') }}</strong>
                                            </td>
                                        </tr>

                                        @forelse($mutations as $m)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($m['date'])->format('d-m-Y') }}</td>
                                                <td><span class="badge badge-info">{{ $m['reference'] }}</span></td>
                                                <td>{{ $m['details'] }}</td>
                                                <td class="text-right text-success">
                                                    {{ $m['debet'] > 0 ? number_format($m['debet'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right text-danger">
                                                    {{ $m['kredit'] > 0 ? number_format($m['kredit'], 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="text-right font-weight-bold">
                                                    {{ number_format($m['saldo'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No transactions found in this period.
                                                </td>
                                            </tr>
                                        @endforelse
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">Please select an Account to view
                                                mutation.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
