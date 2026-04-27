@extends('layouts.app')

@section('title', 'Edit Inventory Movement')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('inventory-movements.index') }}">Inventory Movements</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                <livewire:search-product />
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        @include('utils.alerts')
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('inventory-movements.update', $movement->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="reference">Reference</label>
                                        <input type="text" class="form-control" name="reference"
                                            value="{{ $movement->reference }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required
                                            value="{{ $movement->date }}">
                                    </div>
                                </div>
                                {{-- <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="Completed"
                                                {{ $movement->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Pending" {{ $movement->status == 'Pending' ? 'selected' : '' }}>
                                                Pending</option>
                                        </select>
                                    </div>
                                </div> --}}
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="from_warehouse_id">From Warehouse <span
                                                class="text-danger">*</span></label>
                                        <select name="from_warehouse_id" class="form-control" required>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}"
                                                    {{ $movement->from_warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="to_warehouse_id">To Warehouse <span class="text-danger">*</span></label>
                                        <select name="to_warehouse_id" class="form-control" required>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}"
                                                    {{ $movement->to_warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Pass data produk lama ke Livewire --}}
                            <livewire:inventory.movement-product-table :movementProducts="$movementProducts" />

                            <div class="form-group mt-3">
                                <label for="note">Note (Optional)</label>
                                <textarea name="note" id="note" rows="3" class="form-control">{{ $movement->note }}</textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary px-4">
                                    Update Movement <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
