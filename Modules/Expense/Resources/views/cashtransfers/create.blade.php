@extends('layouts.app')

@section('title', 'Create Cash Transfer')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        {{-- <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Cash Transfer</a></li> --}}
        <li class="breadcrumb-item"><a href="{{ route('cashtransfer.index') }}">Cash Transfer</a></li>
        <li class="breadcrumb-item active">Add</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- <form id="expense-form" action="{{ route('expenses.store') }}" method="POST"> --}}
        <form id="expense-form" action="{{ route('cashtransfer.store') }}" method="POST">    
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button class="btn btn-primary">Create Cash Transfer <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="reference">Reference <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly value="CTR">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                            <script>
//-------------------------------------------------------------------------------------//
                                function update_totalamount() {
                                    const from_amount = document.getElementById('amount_transfer_from');
                                    const charge = document.getElementById('amount_charge');
                                    const to_amount = document.getElementById('amount_transfer_to');
                                    //const actionButton = document.getElementById('actionbutton');

                                    // Create our number formatter.
                                    const formatter = new Intl.NumberFormat('en-US', {
                                        style: 'currency',
                                        currency: 'USD',

                                        trailingZeroDisplay: 'stripIfInteger', // This is probably what most people
                                        minimumFractionDigits: 0, // This suffices for whole numbers, but will
                                    });

                                    const rupiah = (number) => {
                                        return new Intl.NumberFormat("id-ID", {
                                            style: "currency",
                                            currency: "IDR"
                                        }).format(number);
                                    }

                                    to_amount.value = (isNaN(parseFloat(from_amount.value)) ? 0 : parseFloat(from_amount.value)) -
                                        (isNaN(parseFloat(charge.value)) ? 0 : parseFloat(charge.value))

                                    if ((from_amount.value - parseFloat(charge.value)) < 0) {
                                        charge.value = 0; to_amount.value = 0;
                                       // document.getElementById('lblcharge').innerHTML = 'Rp 0,00';
                                    } else {
                                        to_amount.value = from_amount.value - parseFloat(charge.value);
                                       // document.getElementById('lblcharge').innerHTML = rupiah(kembalian.value);
                                    }    

                                }
                            </script>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="category_id">Transfer From <span class="text-danger">*</span></label>
                                        <select name="receive_type_from" id="receive_type_from" class="form-control" required>
                                            <option value="" >Receive Type</option>
                                            @foreach(\Modules\MethodePay\Entities\MethodePay::all() as $methode)
                                                {{-- <option value="{{ $methode->code }}">{{ $methode->methode_name }}</option> selected--}}
                                                <option value="{{ $methode->methode_name }}">{{ $methode->methode_name }}</option>
                                            @endforeach 
                                        </select>
                                    </div>
                                </div> 
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="amount">From Amount <span class="text-danger">*</span></label>
                                        {{-- <input id="amount_transfer_from" type="number" class="form-control" name="amount_transfer_from" required> --}}
                                        <input type="number" id="amount_transfer_from" name="amount_transfer_from"
                                            onchange="update_totalamount()" 
                                            class="form-control"
                                            onblur="if (this.value == '') {this.value = 0;}"
                                            onfocus="if (this.value == 0) {this.value = '';}"
                                            value=0>
                                        </input>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="yype">Transaction Type <span class="text-danger">*</span></label>
                                        <select name="transtype" id="transtype" class="form-control" required>{{--  style="width:115px;"  --}}                                             
                                              <option name="in" value="0">In</option>   
                                              <option name="out" value="1">Out</option>  
                                        </select>
                                    </div>
                                </div> 
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="amount">Charge Amount <span class="text-danger">*</span></label>
                                        {{-- <input id="amount_charge" type="text" class="form-control" name="amount_charge" required> --}}
                                        <input type="number" id="amount_charge" name="amount_charge"
                                            onchange="update_totalamount()" 
                                            class="form-control"
                                            onblur="if (this.value == '') {this.value = 0;}"
                                            onfocus="if (this.value == 0) {this.value = '';}"
                                            value=0>
                                        </input>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="category_id">Transfer To <span class="text-danger">*</span></label>
                                        <select name="receive_type_to" id="receive_type_to" class="form-control" required>
                                            <option value="" >Receive Type</option>
                                            @foreach(\Modules\MethodePay\Entities\MethodePay::all() as $methode)
                                                {{-- <option value="{{ $methode->code }}">{{ $methode->methode_name }}</option> selected--}}
                                                <option value="{{ $methode->methode_name }}">{{ $methode->methode_name }}</option>
                                            @endforeach  
                                        </select>
                                    </div>
                                </div> 
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="amount">To Amount <span class="text-danger">*</span></label>
                                        <input type="number" id="amount_transfer_to" class="form-control" name="amount_transfer_to"
                                          onblur="if (this.value == '') {this.value = 0;}"
                                          onfocus="if (this.value == 0) {this.value = '';}"
                                          value=0 required readonly="true">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="details">Details</label>
                                <textarea class="form-control" rows="6" name="details"></textarea>
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
    <script>
       /*  $(document).ready(function () {
            $('#amount_transfer_from').maskMoney({
                precision: 0, 
                prefix:'{{ settings()->currency->symbol }}',
                thousands:'{{ settings()->currency->thousand_separator }}',
              //  decimal:'{{ settings()->currency->decimal_separator }}',
            });  
            $('#amount_transfer_from').val();
            $('#expense-form').submit(function () {
                var amount = $('#amount_transfer_from').maskMoney('unmasked')[0];
               // var amount = $('#amount').val();
                $('#amount_transfer_from').val(amount*1000);
            });

            $('#amount_charge').maskMoney({
                precision: 0, 
                prefix:'{{ settings()->currency->symbol }}',
                thousands:'{{ settings()->currency->thousand_separator }}',
              //  decimal:'{{ settings()->currency->decimal_separator }}',
            });  
            $('#amount_charge').val();
            $('#expense-form').submit(function () {
                var amount = $('#amount_charge').maskMoney('unmasked')[0];
               // var amount = $('#amount').val();
                $('#amount_charge').val(amount*1000);
            });

            $('#amount_transfer_to').maskMoney({
                precision: 0, 
                prefix:'{{ settings()->currency->symbol }}',
                thousands:'{{ settings()->currency->thousand_separator }}',
              //  decimal:'{{ settings()->currency->decimal_separator }}',
            });  
            $('#amount_transfer_to').val();
            $('#expense-form').submit(function () {
                var amount = $('#amount_transfer_to').maskMoney('unmasked')[0];
               // var amount = $('#amount').val();
                $('#amount_transfer_to').val(amount*1000);
            }); 

//-------------------------------------------------------------------------------------//
        }); */
    </script>
@endpush

