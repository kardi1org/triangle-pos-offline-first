@extends('layouts.app')

@section('title', 'Inventories Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('Inventories.index') }}">Inventories</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div>
                            Reference: <strong>{{ $Inventory->reference }}</strong>
                        </div>
                        {{-- <a target="_blank" class="btn btn-sm btn-secondary mfs-auto mfe-1 d-print-none"
                            href="{{ route('Inventories.pdf', $Inventory->id) }}">
                            <i class="bi bi-printer"></i> Print
                        </a>
                        <a target="_blank" class="btn btn-sm btn-info mfe-1 d-print-none"
                            href="{{ route('Inventories.pdf', $Inventory->id) }}">
                            <i class="bi bi-save"></i> Save
                        </a> --}}
                    </div>
                    <div class="card-body">


                        <div class="table-responsive-sm">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="align-middle">Product</th>
                                        <th class="align-middle">Net Unit Price</th>
                                        <th class="align-middle">Quantity</th>
                                        <th class="align-middle">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($Inventory->inventoryDetails as $item)
                                        <tr>
                                            <td class="align-middle">
                                                {{ $item->product_name }} <br>
                                                <span class="badge badge-success">
                                                    {{ $item->product_code }}
                                                </span>
                                            </td>

                                            <td class="align-middle">{{ format_currency($item->unit_price) }}</td>

                                            <td class="align-middle">
                                                {{ $item->quantity }}
                                            </td>

                                            <td class="align-middle">
                                                {{ format_currency($item->sub_total) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-5 col-sm-5 ml-md-auto">
                                <table class="table">
                                    <tbody>

                                        <tr>
                                            <td class="left"><strong>Grand Total</strong></td>
                                            <td class="right">
                                                <strong>{{ format_currency($Inventory->total_amount) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
