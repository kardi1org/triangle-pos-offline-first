@extends('layouts.app')

@section('title', 'Receive Method Setting')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Receive Method</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('payment.update') }}" method="POST">
            @csrf
            @method('patch')
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow">
                        <div class="card-header">
                            Receive Method Setting
                        </div>

                        <div class="card-body">
                            @php
                                // Cek apakah fitur receive method kustom diaktifkan
                                $isReceiveEnabled = isFeatureEnabled('set_receive');
                            @endphp

                            <div class="row">
                                {{-- 1. CASH: Selalu aktif/bisa diubah --}}
                                <div class="col-6 mb-1">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="cash" name="cash"
                                            value="Y" {{ $payments->Cash == 'Y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="cash">Cash</label>
                                    </div>
                                </div>

                                {{-- Group Metode Pembayaran Lainnya --}}
                                @php
                                    $otherPayments = [
                                        'debitcard' => ['label' => 'Debit Card', 'db' => 'DebitCard'],
                                        'creditcard' => ['label' => 'Credit Card', 'db' => 'CreditCard'],
                                        'gopay' => ['label' => 'Gopay', 'db' => 'Gopay'],
                                        'ovo' => ['label' => 'OVO', 'db' => 'OVO'],
                                        'shopeepay' => ['label' => 'Shopee Pay', 'db' => 'ShopeePay'],
                                        'kredivo' => ['label' => 'Kredivo', 'db' => 'Kredivo'],
                                        'dana' => ['label' => 'Dana', 'db' => 'Dana'],
                                        'grabpay' => ['label' => 'Grab Pay', 'db' => 'GrabPay'],
                                        'qris' => ['label' => 'QRIS', 'db' => 'QRIS'],
                                    ];
                                @endphp

                                @foreach ($otherPayments as $id => $data)
                                    <div class="col-6 mb-1">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="{{ $id }}"
                                                name="{{ $id }}" value="Y" {{-- Jika fitur OFF, paksa uncheck dan disable. Jika ON, ambil dari DB --}}
                                                {{ $isReceiveEnabled && $payments->{$data['db']} == 'Y' ? 'checked' : '' }}
                                                {{ !$isReceiveEnabled ? 'disabled' : '' }}>
                                            <label class="custom-control-label {{ !$isReceiveEnabled ? 'text-muted' : '' }}"
                                                for="{{ $id }}">
                                                {{ $data['label'] }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if (!$isReceiveEnabled)
                                <div class="alert alert-warning mt-3 mb-0" style="font-size: 0.8rem;">
                                    <i class="bi bi-info-circle"></i> Fitur pembayaran non-tunai dinonaktifkan dari
                                    sistem.<br>
                                    Upgrade Paket agar fitur aktif.
                                </div>
                            @endif
                        </div>

                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                Update Setting <i class="bi bi-check"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
