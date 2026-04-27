<div>
    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <span>{{ session('message') }}</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="bg-secondary text-white text-uppercase small font-weight-bold">
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Code</th>
                    <th class="text-center">Current Stock</th>
                    <th style="width: 200px;">Quantity to Move</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $key => $product)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $product['product_name'] }}</td>
                        <td>{{ $product['product_code'] }}</td>
                        <td class="text-center">
                            <span class="badge badge-info">
                                {{ $product['product_quantity'] }} {{ $product['product_unit'] }}
                            </span>
                        </td>
                        <td class="align-middle">
                            <input type="hidden" name="product_ids[]" value="{{ $product['id'] }}">

                            {{-- Gunakan wire:model agar Livewire menyimpan perubahan Qty --}}
                            <input type="number" wire:model.live="products.{{ $key }}.quantity"
                                name="quantities[]" min="1" class="form-control">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm"
                                wire:click="removeProduct({{ $key }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-danger">
                            Please search and select products to move.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
