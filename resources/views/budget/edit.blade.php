@extends('layouts.app')

@section('title', 'Create Budget')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('budget.index') }}">Budget</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form id="budget-form" action="{{ route('budget.update', $budget) }}" method="POST">
            @csrf
            @method('put')
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button class="btn btn-primary">Update Budget <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required
                                            value="{{ $budget->getAttributes()['date'] }}">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="amount">Amount <span class="text-danger">*</span></label>
                                        <input id="amount" type="number" class="form-control" name="amount"
                                            min="0" step="0.01" required value="{{ $budget->amount }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="details">Details</label>
                                <textarea class="form-control" rows="6" name="details">{{ $budget->details }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('page_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    {{-- <script>
        $(document).ready(function() {
            $('#amount').maskMoney({
                prefix: '{{ settings()->currency->symbol }}',
                thousands: '{{ settings()->currency->thousand_separator }}',
                decimal: '{{ settings()->currency->decimal_separator }}',
            });

            $('#amount').maskMoney('mask');

            $('#budget-form').submit(function() {
                var amount = $('#amount').maskMoney('unmasked')[0];
                $('#amount').val(amount);
            });
        });
    </script> --}}
@endpush
