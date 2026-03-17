@extends('layouts.app')

@section('title', 'Kitchen Log Report')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Kitchen Log Report</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Memanggil Livewire Component untuk Kitchen Log --}}
        <livewire:reports.kitchen-log-report />
    </div>
@endsection
