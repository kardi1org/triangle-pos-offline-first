@extends('layouts.app')

@section('title', 'Recipes List')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Manage Recipes</strong>
                        <a href="{{ route('recipes.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Add Recipe
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>Product Result</th>
                                    <th>Quantity</th>
                                    <th>Ingredients Count</th>
                                    <th>Total Cost Est.</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recipes as $recipe)
                                    <tr>
                                        <td>
                                            <strong>{{ $recipe->product->product_name }}</strong><br>
                                            <small class="text-muted">{{ $recipe->product->product_code }}</small>
                                        </td>
                                        <td>{{ $recipe->quantity }} {{ $recipe->unit }}</td>
                                        <td><span class="badge badge-info">{{ $recipe->details->count() }} Items</span></td>
                                        <td>{{ number_format($recipe->details->sum(fn($d) => $d->quantity * $d->cost), 2) }}
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('recipes.edit', $recipe->id) }}"
                                                    class="btn btn-sm btn-info text-white">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('recipes.destroy', $recipe->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this recipe?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-danger"><i
                                                            class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
