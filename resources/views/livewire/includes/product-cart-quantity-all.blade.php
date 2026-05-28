<div class="input-group d-flex justify-content-center">
    {{-- <div class="input-group-append">
        <button type="button"
            wire:click.debounce.400ms="updateQuantityMin('{{ $cart_item->rowId }}', {{ $cart_item->id }})"
            class="btn btn-info" style="min-width: 30px;max-width: 30px;">-</button>
    </div> --}}

    <input type="number" wire:model="quantity.{{ $cart_item->id }}"
        wire:change="updateQuantity('{{ $cart_item->rowId }}', {{ $cart_item->id }})"
        style="min-width: 40px;max-width: 60px;" class="form-control" min="1">


    {{-- <div class="input-group-append">
        <button type="button"
            wire:click.debounce.400ms="updateQuantityPlus('{{ $cart_item->rowId }}', {{ $cart_item->id }})"
            class="btn btn-info" style="min-width: 30px;max-width: 30px;">+</button>
    </div> --}}

</div>
