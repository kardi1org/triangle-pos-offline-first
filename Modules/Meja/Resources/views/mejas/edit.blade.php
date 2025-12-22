@extends('layouts.app')

@section('title', 'Edit Table')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('mejas.index') }}">Table Shape</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('mejas.update', $meja) }}" method="POST">
            @csrf
            @method('patch')
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button class="btn btn-primary">Update Table <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="meja_name">Table Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="meja_name" required value="{{ $meja->name }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="meja_email">Qty Pax <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="qtypack" required value="{{ $meja->qty_pax }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="meja_phone">Location <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="lokasi" required value="{{ $meja->location }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="city">Shape <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="bentuk" required value="{{ $meja->shape }}">
                                    </div>
                                </div>
                                {{-- <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="country">Country <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="country" required value="{{ $Meja->country }}">
                                    </div>
                                </div> --}}
                            </div>

                            <div class="form-row">
                                {{-- <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="address">Address <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address" required value="{{ $Meja->address }}">
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

