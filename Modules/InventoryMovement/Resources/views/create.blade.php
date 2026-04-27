@extends('layouts.app')

@section('title', 'Create Inventory Movement')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inventory-movements.index') }}">Inventory Movements</a></li>
        <li class="breadcrumb-item active">Add</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                {{-- Gunakan search product yang sama dengan Adjustment --}}
                <livewire:search-product />
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        @include('utils.alerts')
                        <form action="{{ route('inventory-movements.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="reference">Reference <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly
                                            value="MVT-{{ strtoupper(bin2hex(random_bytes(3))) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required
                                            value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                {{-- <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="Completed">Completed</option>
                                            <option value="Pending">Pending</option>
                                        </select>
                                    </div>
                                </div> --}}
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="from_warehouse_id">From Warehouse (Source) <span
                                                class="text-danger">*</span></label>
                                        <select name="from_warehouse_id" class="form-control" required>
                                            @foreach (\Modules\Setting\Entities\Warehouse::all() as $warehouse)
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="to_warehouse_id">To Warehouse (Destination) <span
                                                class="text-danger">*</span></label>
                                        <select name="to_warehouse_id" class="form-control" required>
                                            @foreach (\Modules\Setting\Entities\Warehouse::all() as $warehouse)
                                                <option value="{{ $warehouse->id }}"
                                                    {{ $loop->iteration == 2 ? 'selected' : '' }}>{{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Memanggil Livewire Table khusus Movement --}}
                            <livewire:inventory.movement-product-table />

                            <div class="form-group mt-3">
                                <label for="note">Note (Optional)</label>
                                <textarea name="note" id="note" rows="3" class="form-control"></textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary px-4">
                                    Save Movement <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
