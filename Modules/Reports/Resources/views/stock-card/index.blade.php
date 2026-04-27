@extends('layouts.app')

@section('title', 'Stock Card Report')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('stock-card-report.index') }}" method="GET">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="start_date"
                                        value="{{ request('start_date') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="end_date"
                                        value="{{ request('end_date') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Warehouse</label>
                                    <select class="form-control" name="warehouse_id" required>
                                        <option value="">Select Warehouse</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}"
                                                {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Product</label>
                                    <select class="form-control" name="product_id" required>
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Filter Report</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if (request()->filled('product_id'))
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="bg-light">
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Type</th>
                                <th class="text-success">In (+)</th>
                                <th class="text-danger">Out (-)</th>
                                <th class="text-center">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="font-weight-bold">
                                <td colspan="5" class="text-right">STOCK AWAL :</td>
                                <td class="text-center">{{ $stock_awal }}</td>
                            </tr>
                            @php $balance = $stock_awal; @endphp
                            @foreach ($movements as $m)
                                @php $balance += ($m['in'] - $m['out']); @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($m['date'])->format('Y-m-d') }}</td>
                                    <td>{{ $m['ref'] }}</td>
                                    <td>{{ $m['type'] }}</td>
                                    <td class="text-success">{{ $m['in'] ?: '-' }}</td>
                                    <td class="text-danger">{{ $m['out'] ?: '-' }}</td>
                                    <td class="text-center font-weight-bold">{{ $balance }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
