@extends('layouts.app')

@section('title', 'Create Table')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('mejas.index') }}">Table Shape</a></li>
        <li class="breadcrumb-item active">Add</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('mejas.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button class="btn btn-primary">Create Table<i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="Meja_name">Table Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="meja_name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="Meja_email">Qty Pax <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="qtypack" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                {{-- <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="Meja_phone">Qty Pack <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="qtypack" required>
                                    </div>
                                </div> --}}
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="city">Location<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="lokasi" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="country">Shape <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="bentuk" required>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="form-row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="address">Bentuk Meja <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="bentuk" required>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

