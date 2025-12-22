@extends('layouts.app')

@section('title', 'Table Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('mejas.index') }}">Table Shape</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Table Name</th>
                                    <td>{{ $meja->name }}</td>
                                </tr>
                                <tr>
                                    <th>Qty Pax</th>
                                    <td>{{ $meja->qty_pax }}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $meja->location }}</td>
                                </tr>
                                <tr>
                                    <th>Shape</th>
                                    <td>{{ $meja->shape }}</td>
                                </tr>
                                {{-- <tr>
                                    <th>Country</th>
                                    <td>{{ $meja->country }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $meja->address }}</td>
                                </tr> --}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
