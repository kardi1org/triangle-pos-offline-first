@extends('layouts.app')

@section('title', 'Mutation Report')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Mutation Report</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:reports.mutation-cash-report :methodes="\Modules\MethodePay\Entities\MethodePay::all()" /> 
    </div>
@endsection
