@extends('layouts.app')

@section('title', 'Warehouses')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Warehouses</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('utils.alerts')
                <div class="card border-0 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Warehouses</h5>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                            Add Warehouse <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light text-uppercase small font-weight-bold text-dark">
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($warehouses as $w)
                                        <tr>
                                            <td>{{ $w->code }}</td>
                                            <td>
                                                <strong>{{ $w->name }}</strong><br>
                                                <small class="text-muted">
                                                    <i class="bi bi-shop"></i>
                                                    {{ collect($outlets)->firstWhere('id', $w->outlet_id)->name ?? 'Outlet Not Found' }}
                                                </small>
                                            </td>
                                            <td>{{ $w->phone ?? '-' }}</td>
                                            <td>{{ $w->address ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $w->is_active ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $w->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn btn-sm btn-info" data-toggle="modal"
                                                        data-target="#editModal{{ $w->id }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('warehouses.destroy', $w->id) }}" method="POST"
                                                        onsubmit="return confirm('Delete this warehouse?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-danger"><i
                                                                class="bi bi-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        {{-- Modal Edit di sini --}}
                                        @include('setting::warehouses.edit-modal', ['warehouse' => $w])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create --}}
    @include('setting::warehouses.create-modal')
@endsection
