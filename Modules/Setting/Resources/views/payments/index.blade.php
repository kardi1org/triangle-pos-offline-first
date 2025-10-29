@extends('layouts.app')

@section('title', 'Create Unit')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Receive Methode</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('payment.update') }}" method="POST">
            @csrf
            @method('patch')
            <div class="row">
                <!-- Products Permission -->
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow">
                        <div class="card-header">
                            Receive Methode Setting
                        </div>
                        {{-- @dd($payments->Cash) --}}

                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-1">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="cash" name="cash"
                                            value="Y" {{ $payments->Cash == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="cash">Cash</label>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="debitcard" name="debitcard"
                                            value="Y" {{ $payments->DebitCard == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="debitcard">Debit Card</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-1">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="gopay" name="gopay"
                                            value="Y" {{ $payments->Gopay == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="gopay">Gopay</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="creditcard"
                                            name="creditcard" value="Y"
                                            {{ $payments->CreditCard == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="creditcard">Credit Card</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-1">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ovo" name="ovo"
                                            value="Y" {{ $payments->OVO == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="ovo">OVO</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="shopeepay" name="shopeepay"
                                            value="Y" {{ $payments->ShopeePay == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="shopeepay">Shopee
                                            Pay</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-1">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="kredivo" name="kredivo"
                                            value="Y" {{ $payments->Kredivo == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="kredivo">Kredivo</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="dana" name="dana"
                                            value="Y" {{ $payments->Dana == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="dana">Dana</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="grabpay" name="grabpay"
                                            value="Y" {{ $payments->GrabPay == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="grabpay">Grab
                                            Pay</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="qris" name="qris"
                                            value="Y" {{ $payments->QRIS == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="qris">QRIS</label>
                                    </div>
                                </div>
                                {{-- <div class="col-12">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="print_barcodes"
                                            name="permissions[]" value="print_barcodes">
                                        <label class="custom-control-label" for="print_barcodes">Kredivo</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="print_barcodes"
                                            name="permissions[]" value="print_barcodes">
                                        <label class="custom-control-label" for="print_barcodes">Kredivo</label>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            {{-- <div class="form-group"> --}}
                            <button class="btn btn-primary">Update Setting <i class="bi bi-check"></i></button>
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
