<div class="input-group d-flex justify-content-center">
    <div class="input-group-append">
        <button type="button" wire:click="updateQuantityMin('{{ $cart_item->rowId }}', {{ $cart_item->id }})"
            class="btn btn-info">
            -{{-- <i class="bi bi-check"></i> --}}
        </button>
    </div>
    {{-- <input type="number" wire:model="quantity.{{ $cart_item->id }}" style="min-width: 40px;max-width: 60px;" class="form-control" value="{{ $cart_item->qty }}" min="1"> --}}
    <input type="text" wire:model="quantity.{{ $cart_item->id }}" style="min-width: 40px;max-width: 50px;"
        class="form-control" value="{{ $cart_item->qty }}" min="1">
    <div class="input-group-append">
        <button type="button" wire:click="updateQuantityPlus('{{ $cart_item->rowId }}', {{ $cart_item->id }})"
            class="btn btn-info">
            +{{-- <i class="bi bi-check"></i> --}}
        </button>
    </div>
</div>
