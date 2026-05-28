@extends('layouts.app')

@section('title', 'POS')

@section('third_party_stylesheets')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">POS</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('utils.alerts')
            </div>
            <div class="col-lg-7">
                <livewire:search-product :cart-instance="'sale'" />
                <livewire:pos.product-list :categories="$product_categories" />
            </div>
            <div class="col-lg-5">
                <livewire:pos.checkout :cart-instance="'sale'" :customers="$customers" :payments="$payments" />
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        $(document).ready(function() {
            window.addEventListener('showCheckoutModal', event => {
                $('#checkoutModal').modal('show');
                $('.modal-body input,.modal-body textarea').each(function() {
                    $(this).val(0);
                });

                $('#paid_amount').maskMoney({
                    prefix: '{{ settings()->currency->symbol }}',
                    thousands: '{{ settings()->currency->thousand_separator }}',
                    decimal: '{{ settings()->currency->decimal_separator }}',
                    allowZero: false,
                });

                $('#total_amount').maskMoney({
                    prefix: '{{ settings()->currency->symbol }}',
                    thousands: '{{ settings()->currency->thousand_separator }}',
                    decimal: '{{ settings()->currency->decimal_separator }}',
                    allowZero: true,
                });
                // $('#total_receipt').maskMoney({
                //     prefix: '{{ settings()->currency->symbol }}',
                //     thousands: '{{ settings()->currency->thousand_separator }}',
                //     decimal: '{{ settings()->currency->decimal_separator }}',
                //     allowZero: true,
                // });
                // $('#kembalian').maskMoney({
                //     prefix: '{{ settings()->currency->symbol }}',
                //     thousands: '{{ settings()->currency->thousand_separator }}',
                //     decimal: '{{ settings()->currency->decimal_separator }}',
                //     allowZero: true,
                // });

                $('#paid_amount').maskMoney('mask');
                $('#total_amount').maskMoney('mask');
                // $('#total_receipt').maskMoney('mask');
                // $('#kembalian').maskMoney('mask');

                $('#checkout-form').submit(function() {
                    var paid_amount = $('#paid_amount').maskMoney('unmasked')[0];
                    $('#paid_amount').val(paid_amount);
                    var total_amount = $('#total_amount').maskMoney('unmasked')[0];
                    // $('#total_amount').val(total_amount);
                    // var total_amount = $('#total_amount').maskMoney('unmasked')[0];
                    // $('#total_receipt').val(total_amount);
                });
            });
        });
    </script>
@endpush
