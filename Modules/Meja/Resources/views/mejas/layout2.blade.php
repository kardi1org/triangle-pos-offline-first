@extends('layouts.app')

@section('title', 'Manage Floor Plan Layout')

@push('page_css')
    <style>
        /* Area Kanvas Besar Restoran */
        .floor-plan-canvas {
            width: 100%;
            height: 600px;
            background-color: #f8fafc;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
            border: 3px dashed #94a3b8;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }

        /* Elemen Dasar Meja Dinamis */
        .draggable-table-wrapper {
            position: absolute;
            cursor: move;
            touch-action: none;
            user-select: none;
            transform-origin: center center;
            z-index: 2;
            overflow: visible !important;
        }

        .table-body {
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            border: 2px solid #3b82f6;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            box-sizing: border-box;
            padding: 5px;
            z-index: 5;
        }

        /* Kelestarian Bentuk Meja Masing-Masing Kategori */
        .shape-round-4 .table-body,
        .shape-round-6 .table-body,
        .shape-round-8 .table-body,
        .shape-round-10 .table-body,
        .shape-round-12 .table-body {
            border-radius: 50% !important;
        }

        .shape-square-2-h .table-body,
        .shape-square-2-v .table-body,
        .shape-square-4 .table-body {
            border-radius: 4px !important;
        }

        .shape-rectangle-4-h .table-body,
        .shape-rectangle-6-h .table-body,
        .shape-rectangle-8-h .table-body,
        .shape-rectangle-4-v .table-body,
        .shape-rectangle-6-v .table-body,
        .shape-rectangle-8-v .table-body {
            border-radius: 6px !important;
        }

        /* FIX DIAGONAL SEGI 4 (Belah Ketupat / Diamond) */
        .shape-diagonal-8 .table-body {
            border: none !important;
            background-color: #3b82f6 !important;
            padding: 2px !important;
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%) !important;
            border-radius: 0px !important;
        }

        /* Isi dalam meja diagonal segi 4 agar tetap putih dan presisi */
        .shape-diagonal-8 .table-body .table-content-container {
            background-color: #ffffff !important;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%) !important;
        }

        /* Warna Status Interaksi */
        .draggable-table-wrapper.is-dragging .table-body,
        .draggable-table-wrapper.is-resizing .table-body {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        .shape-diagonal-8.is-dragging .table-body,
        .shape-diagonal-8.is-resizing .table-body {
            background-color: #10b981 !important;
        }

        .draggable-table-wrapper.is-new-table .table-body {
            border-color: #f59e0b;
            border-style: dashed;
        }

        /* Konten Teks di Dalam Meja */
        .table-content-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 6;
        }

        .table-label {
            font-weight: bold;
            font-size: 11px;
            color: #1e293b;
            text-align: center;
        }

        .table-pax {
            font-size: 9px;
            color: #64748b;
        }

        /* Handle Kontrol */
        .rotate-handle {
            position: absolute;
            top: 6px;
            left: calc(50% - 6px);
            font-size: 11px;
            color: #3b82f6;
            cursor: grab;
            z-index: 10;
        }

        .shape-diagonal-8 .rotate-handle {
            color: #3b82f6;
            top: 22px;
        }

        .resize-handle {
            position: absolute;
            bottom: 4px;
            right: 6px;
            width: 14px;
            height: 14px;
            color: #64748b;
            cursor: se-resize;
            z-index: 10;
            font-size: 10px;
        }

        .shape-diagonal-8 .resize-handle {
            bottom: 22px;
            right: 22px;
        }

        /* =====================================
                                                                               ATURAN POSISI KURSI (CHAIRS OUTSIDE TABLE)
                                                                               ===================================== */
        .chair {
            position: absolute;
            background-color: #94a3b8;
            border: 1px solid #64748b;
            border-radius: 3px;
            z-index: 1;
        }

        /* --- RECTANGLE HORIZONTAL --- */
        .shape-rectangle-4-h .chair,
        .shape-rectangle-6-h .chair,
        .shape-rectangle-8-h .chair {
            width: 22px;
            height: 12px;
        }

        .shape-rectangle-4-h .chair.t1 {
            top: -16px;
            left: calc(50% - 26px);
        }

        .shape-rectangle-4-h .chair.t2 {
            top: -16px;
            left: calc(50% + 4px);
        }

        .shape-rectangle-4-h .chair.b1 {
            bottom: -16px;
            left: calc(50% - 26px);
        }

        .shape-rectangle-4-h .chair.b2 {
            bottom: -16px;
            left: calc(50% + 4px);
        }

        .shape-rectangle-6-h .chair.t1 {
            top: -16px;
            left: calc(50% - 41px);
        }

        .shape-rectangle-6-h .chair.t2 {
            top: -16px;
            left: calc(50% - 11px);
        }

        .shape-rectangle-6-h .chair.t3 {
            top: -16px;
            left: calc(50% + 19px);
        }

        .shape-rectangle-6-h .chair.b1 {
            bottom: -16px;
            left: calc(50% - 41px);
        }

        .shape-rectangle-6-h .chair.b2 {
            bottom: -16px;
            left: calc(50% - 11px);
        }

        .shape-rectangle-6-h .chair.b3 {
            bottom: -16px;
            left: calc(50% + 19px);
        }

        .shape-rectangle-8-h .chair.t1 {
            top: -16px;
            left: calc(50% - 56px);
        }

        .shape-rectangle-8-h .chair.t2 {
            top: -16px;
            left: calc(50% - 28px);
        }

        .shape-rectangle-8-h .chair.t3 {
            top: -16px;
            left: calc(50% + 0px);
        }

        .shape-rectangle-8-h .chair.t4 {
            top: -16px;
            left: calc(50% + 28px);
        }

        .shape-rectangle-8-h .chair.b1 {
            bottom: -16px;
            left: calc(50% - 56px);
        }

        .shape-rectangle-8-h .chair.b2 {
            bottom: -16px;
            left: calc(50% - 28px);
        }

        .shape-rectangle-8-h .chair.b3 {
            bottom: -16px;
            left: calc(50% + 0px);
        }

        .shape-rectangle-8-h .chair.b4 {
            bottom: -16px;
            left: calc(50% + 28px);
        }

        /* --- RECTANGLE VERTICAL --- */
        .shape-rectangle-4-v .chair,
        .shape-rectangle-6-v .chair,
        .shape-rectangle-8-v .chair {
            width: 12px;
            height: 22px;
        }

        .shape-rectangle-4-v .chair.l1 {
            left: -16px;
            top: calc(50% - 26px);
        }

        .shape-rectangle-4-v .chair.l2 {
            left: -16px;
            top: calc(50% + 4px);
        }

        .shape-rectangle-4-v .chair.r1 {
            right: -16px;
            top: calc(50% - 26px);
        }

        .shape-rectangle-4-v .chair.r2 {
            right: -16px;
            top: calc(50% + 4px);
        }

        .shape-rectangle-6-v .chair.l1 {
            left: -16px;
            top: calc(50% - 41px);
        }

        .shape-rectangle-6-v .chair.l2 {
            left: -16px;
            top: calc(50% - 11px);
        }

        .shape-rectangle-6-v .chair.l3 {
            left: -16px;
            top: calc(50% + 19px);
        }

        .shape-rectangle-6-v .chair.r1 {
            right: -16px;
            top: calc(50% - 41px);
        }

        .shape-rectangle-6-v .chair.r2 {
            right: -16px;
            top: calc(50% - 11px);
        }

        .shape-rectangle-6-v .chair.r3 {
            right: -16px;
            top: calc(50% + 19px);
        }

        /* --- SQUARE SERIES --- */
        .shape-square-2-h .chair {
            width: 12px;
            height: 22px;
            top: calc(50% - 11px);
        }

        .shape-square-2-h .chair.l1 {
            left: -15px;
        }

        .shape-square-2-h .chair.r1 {
            right: -15px;
        }

        .shape-square-2-v .chair {
            width: 22px;
            height: 12px;
            left: calc(50% - 11px);
        }

        .shape-square-2-v .chair.t1 {
            top: -15px;
        }

        .shape-square-2-v .chair.b1 {
            bottom: -15px;
        }

        .shape-square-4 .chair {
            width: 22px;
            height: 12px;
        }

        .shape-square-4 .chair.t1 {
            top: -15px;
            left: calc(50% - 11px);
        }

        .shape-square-4 .chair.b1 {
            bottom: -15px;
            left: calc(50% - 11px);
        }

        .shape-square-4 .chair.l1 {
            width: 12px;
            height: 22px;
            left: -15px;
            top: calc(50% - 11px);
        }

        .shape-square-4 .chair.r1 {
            width: 12px;
            height: 22px;
            right: -15px;
            top: calc(50% - 11px);
        }

        /* --- DIAGONAL SERIES --- */
        .shape-diagonal-8 .chair {
            width: 22px;
            height: 12px;
        }

        .shape-diagonal-8 .chair.tl-1 {
            transform: rotate(-45deg);
            top: 6px;
            left: 16px;
        }

        .shape-diagonal-8 .chair.tl-2 {
            transform: rotate(-45deg);
            top: 24px;
            left: -2px;
        }

        .shape-diagonal-8 .chair.tr-1 {
            transform: rotate(45deg);
            top: 6px;
            right: 16px;
        }

        .shape-diagonal-8 .chair.tr-2 {
            transform: rotate(45deg);
            top: 24px;
            right: -2px;
        }

        .shape-diagonal-8 .chair.bl-1 {
            transform: rotate(45deg);
            bottom: 24px;
            left: -2px;
        }

        .shape-diagonal-8 .chair.bl-2 {
            transform: rotate(45deg);
            bottom: 6px;
            left: 16px;
        }

        .shape-diagonal-8 .chair.br-1 {
            transform: rotate(-45deg);
            bottom: 24px;
            right: -2px;
        }

        .shape-diagonal-8 .chair.br-2 {
            transform: rotate(-45deg);
            bottom: 6px;
            right: 16px;
        }

        /* --- ROUND SERIES (FIX GAP: Dinaikkan ke 62% agar memberi sedikit jarak dari meja) --- */
        [class*="shape-round-"] .chair {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            top: calc(50% - 8px + (var(--y-dir, 0) * 62%));
            left: calc(50% - 8px + (var(--x-dir, 0) * 62%));
        }

        /* 4 Pax Round */
        .shape-round-4 .chair.c1 {
            --x-dir: 0;
            --y-dir: -1;
        }

        .shape-round-4 .chair.c2 {
            --x-dir: 0;
            --y-dir: 1;
        }

        .shape-round-4 .chair.c3 {
            --x-dir: -1;
            --y-dir: 0;
        }

        .shape-round-4 .chair.c4 {
            --x-dir: 1;
            --y-dir: 0;
        }

        /* 6 Pax Round */
        .shape-round-6 .chair.c1 {
            --x-dir: 0;
            --y-dir: -1;
        }

        .shape-round-6 .chair.c2 {
            --x-dir: 0;
            --y-dir: 1;
        }

        .shape-round-6 .chair.c3 {
            --x-dir: -0.866;
            --y-dir: -0.5;
        }

        .shape-round-6 .chair.c4 {
            --x-dir: -0.866;
            --y-dir: 0.5;
        }

        .shape-round-6 .chair.c5 {
            --x-dir: 0.866;
            --y-dir: -0.5;
        }

        .shape-round-6 .chair.c6 {
            --x-dir: 0.866;
            --y-dir: 0.5;
        }

        /* 8 Pax Round */
        .shape-round-8 .chair.c1 {
            --x-dir: 0;
            --y-dir: -1;
        }

        .shape-round-8 .chair.c2 {
            --x-dir: 0;
            --y-dir: 1;
        }

        .shape-round-8 .chair.c3 {
            --x-dir: -1;
            --y-dir: 0;
        }

        .shape-round-8 .chair.c4 {
            --x-dir: 1;
            --y-dir: 0;
        }

        .shape-round-8 .chair.c5 {
            --x-dir: -0.707;
            --y-dir: -0.707;
        }

        .shape-round-8 .chair.c6 {
            --x-dir: 0.707;
            --y-dir: -0.707;
        }

        .shape-round-8 .chair.c7 {
            --x-dir: -0.707;
            --y-dir: 0.707;
        }

        .shape-round-8 .chair.c8 {
            --x-dir: 0.707;
            --y-dir: 0.707;
        }

        /* 10 Pax Round */
        .shape-round-10 .chair.c1 {
            --x-dir: 0;
            --y-dir: -1;
        }

        .shape-round-10 .chair.c2 {
            --x-dir: 0;
            --y-dir: 1;
        }

        .shape-round-10 .chair.c3 {
            --x-dir: -0.588;
            --y-dir: -0.809;
        }

        .shape-round-10 .chair.c4 {
            --x-dir: 0.588;
            --y-dir: -0.809;
        }

        .shape-round-10 .chair.c5 {
            --x-dir: -0.951;
            --y-dir: -0.309;
        }

        .shape-round-10 .chair.c6 {
            --x-dir: 0.951;
            --y-dir: -0.309;
        }

        .shape-round-10 .chair.c7 {
            --x-dir: -0.951;
            --y-dir: 0.309;
        }

        .shape-round-10 .chair.c8 {
            --x-dir: 0.951;
            --y-dir: 0.309;
        }

        .shape-round-10 .chair.c9 {
            --x-dir: -0.588;
            --y-dir: 0.809;
        }

        .shape-round-10 .chair.c10 {
            --x-dir: 0.588;
            --y-dir: 0.809;
        }

        /* 12 Pax Round */
        .shape-round-12 .chair.c1 {
            --x-dir: 0;
            --y-dir: -1;
        }

        .shape-round-12 .chair.c2 {
            --x-dir: 0.5;
            --y-dir: -0.866;
        }

        .shape-round-12 .chair.c3 {
            --x-dir: 0.866;
            --y-dir: -0.5;
        }

        .shape-round-12 .chair.c4 {
            --x-dir: 1;
            --y-dir: 0;
        }

        .shape-round-12 .chair.c5 {
            --x-dir: 0.866;
            --y-dir: 0.5;
        }

        .shape-round-12 .chair.c6 {
            --x-dir: 0.5;
            --y-dir: 0.866;
        }

        .shape-round-12 .chair.c7 {
            --x-dir: 0;
            --y-dir: 1;
        }

        .shape-round-12 .chair.c8 {
            --x-dir: -0.5;
            --y-dir: 0.866;
        }

        .shape-round-12 .chair.c9 {
            --x-dir: -0.866;
            --y-dir: 0.5;
        }

        .shape-round-12 .chair.c10 {
            --x-dir: -1;
            --y-dir: 0;
        }

        .shape-round-12 .chair.c11 {
            --x-dir: -0.866;
            --y-dir: -0.5;
        }

        .shape-round-12 .chair.c12 {
            --x-dir: -0.5;
            --y-dir: -0.866;
        }
    </style>
@endpush

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('mejas.index') }}">Tables</a></li>
        <li class="breadcrumb-item active">Floor Plan Designer</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @include('utils.alerts')

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="m-0 font-weight-bold text-dark">Floor Plan Designer & Quick Add</h4>
                        <small class="text-muted">Outlet Active ID:
                            <strong>{{ session('selected_outlet_id') ?? 'Default Outlet' }}</strong></small>
                    </div>
                    <button type="button" class="btn btn-success btn-lg shadow-sm" id="btnSaveLayout">
                        Save Layout & Tables <i class="bi bi-save ml-1"></i>
                    </button>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white font-weight-bold">
                        <i class="bi bi-plus-circle mr-1"></i> Quick Add Table
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="quick_no_meja">No. Meja</label>
                            <input type="number" class="form-control" id="quick_no_meja" placeholder="e.g., 7">
                        </div>
                        <div class="form-group">
                            <label for="quick_name">Table Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="quick_name" placeholder="e.g., Table 7">
                        </div>
                        <div class="form-group">
                            <label for="quick_shape">Shape & Capacity <span class="text-danger">*</span></label>
                            <select class="form-control" id="quick_shape">
                                <option value="rectangle-4-h" data-shape="rectangle-4-h" data-pax="4">Rectangle Horizontal
                                    - 4 Pax</option>
                                <option value="rectangle-6-h" data-shape="rectangle-6-h" data-pax="6">Rectangle Horizontal
                                    - 6 Pax</option>
                                <option value="rectangle-8-h" data-shape="rectangle-8-h" data-pax="8">Rectangle Horizontal
                                    - 8 Pax</option>
                                <option value="rectangle-4-v" data-shape="rectangle-4-v" data-pax="4">Rectangle Vertical -
                                    4 Pax</option>
                                <option value="rectangle-6-v" data-shape="rectangle-6-v" data-pax="6">Rectangle Vertical -
                                    6 Pax</option>
                                <option value="rectangle-8-v" data-shape="rectangle-8-v" data-pax="8">Rectangle Vertical -
                                    8 Pax</option>
                                <option value="square-2-h" data-shape="square-2-h" data-pax="2">Square Horizontal - 2 Pax
                                </option>
                                <option value="square-2-v" data-shape="square-2-v" data-pax="2">Square Vertical - 2 Pax
                                </option>
                                <option value="square-4" data-shape="square-4" data-pax="4">Square - 4 Pax</option>
                                <option value="diagonal-8" data-shape="diagonal-8" data-pax="8">Diagonal - 8 Pax</option>
                                <option value="round-4" data-shape="round-4" data-pax="4">Round - 4 Pax</option>
                                <option value="round-6" data-shape="round-6" data-pax="6">Round - 6 Pax</option>
                                <option value="round-8" data-shape="round-8" data-pax="8">Round - 8 Pax</option>
                                <option value="round-10" data-shape="round-10" data-pax="10">Round - 10 Pax</option>
                                <option value="round-12" data-shape="round-12" data-pax="12">Round - 12 Pax</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quick_location">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="quick_location" placeholder="e.g., Utama, VIP">
                        </div>

                        <button type="button" class="btn btn-primary btn-block shadow-sm" id="btnQuickAdd">
                            Inject to Layout <i class="bi bi-arrow-right-short"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="floor-plan-canvas" id="floorPlanCanvas">

                            @foreach ($mejas as $meja)
                                @php
                                    $s = $meja->shape;
                                    $defaultW = 120;
                                    $defaultH = 80;
                                    if ($s === 'rectangle-6-h') {
                                        $defaultW = 160;
                                    } elseif ($s === 'rectangle-8-h') {
                                        $defaultW = 200;
                                    } elseif ($s === 'rectangle-4-v') {
                                        $defaultW = 80;
                                        $defaultH = 120;
                                    } elseif ($s === 'rectangle-6-v') {
                                        $defaultW = 80;
                                        $defaultH = 160;
                                    } elseif ($s === 'rectangle-8-v') {
                                        $defaultW = 80;
                                        $defaultH = 200;
                                    } elseif (strpos($s, 'square-') !== false || $s === 'square-4') {
                                        $defaultW = 85;
                                        $defaultH = 85;
                                    } elseif ($s === 'diagonal-8') {
                                        $defaultW = 120;
                                        $defaultH = 120;
                                    } elseif ($s === 'round-4') {
                                        $defaultW = 90;
                                        $defaultH = 90;
                                    } elseif ($s === 'round-6') {
                                        $defaultW = 110;
                                        $defaultH = 110;
                                    } elseif ($s === 'round-8') {
                                        $defaultW = 135;
                                        $defaultH = 135;
                                    } elseif ($s === 'round-10') {
                                        $defaultW = 155;
                                        $defaultH = 155;
                                    } elseif ($s === 'round-12') {
                                        $defaultW = 175;
                                        $defaultH = 175;
                                    }

                                    $w = $meja->width ?? $defaultW;
                                    $h = $meja->height ?? $defaultH;

                                    if ($s === 'diagonal-8' && (!isset($meja->width) || $meja->width != 120)) {
                                        $w = 120;
                                        $h = 120;
                                    }

                                    $r = $meja->rotation ?? 0;
                                @endphp
                                <div class="draggable-table-wrapper shape-{{ $meja->shape }} pax-{{ $meja->qty_pax }}"
                                    id="meja-{{ $meja->id }}" data-id="{{ $meja->id }}" data-is-new="false"
                                    data-no-meja="{{ $meja->no_meja }}" data-name="{{ $meja->name }}"
                                    data-qty-pax="{{ $meja->qty_pax }}" data-location="{{ $meja->location }}"
                                    data-shape="{{ $meja->shape }}" data-x="{{ $meja->position_x }}"
                                    data-y="{{ $meja->position_y }}" data-angle="{{ $r }}"
                                    style="transform: translate({{ $meja->position_x }}px, {{ $meja->position_y }}px) rotate({{ $r }}deg); width: {{ $w }}px; height: {{ $h }}px;">

                                    {!! getChairHtml($meja->shape, $meja->qty_pax) !!}

                                    <div class="table-body">
                                        <div class="rotate-handle"><i class="bi bi-arrow-clockwise"></i></div>
                                        <div class="resize-handle"><i class="bi bi-arrow-left-right"></i></div>

                                        <div class="table-content-container">
                                            <span class="table-label">{{ $meja->name }}</span>
                                            <span class="table-pax">{{ $meja->qty_pax }} Pax</span>
                                            <small style="font-size: 8px; color:#64748b;">({{ $meja->location }})</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function generateChairsJs(shape, pax) {
            let html = '';
            let p = parseInt(pax);

            if (shape === 'rectangle-4-h') {
                html +=
                    '<div class="chair t1"></div><div class="chair t2"></div><div class="chair b1"></div><div class="chair b2"></div>';
            } else if (shape === 'rectangle-6-h') {
                html +=
                    '<div class="chair t1"></div><div class="chair t2"></div><div class="chair t3"></div><div class="chair b1"></div><div class="chair b2"></div><div class="chair b3"></div>';
            } else if (shape === 'rectangle-8-h') {
                html +=
                    '<div class="chair t1"></div><div class="chair t2"></div><div class="chair t3"></div><div class="chair t4"></div><div class="chair b1"></div><div class="chair b2"></div><div class="chair b3"></div><div class="chair b4"></div>';
            } else if (shape === 'rectangle-4-v') {
                html +=
                    '<div class="chair l1"></div><div class="chair l2"></div><div class="chair r1"></div><div class="chair r2"></div>';
            } else if (shape === 'rectangle-6-v') {
                html +=
                    '<div class="chair l1"></div><div class="chair l2"></div><div class="chair l3"></div><div class="chair r1"></div><div class="chair r2"></div><div class="chair r3"></div>';
            } else if (shape === 'rectangle-8-v') {
                html +=
                    '<div class="chair l1"></div><div class="chair l2"></div><div class="chair l3"></div><div class="chair l4"></div><div class="chair r1"></div><div class="chair r2"></div><div class="chair r3"></div><div class="chair r4"></div>';
            } else if (shape === 'square-2-h') {
                html += '<div class="chair l1"></div><div class="chair r1"></div>';
            } else if (shape === 'square-2-v') {
                html += '<div class="chair t1"></div><div class="chair b1"></div>';
            } else if (shape === 'square-4') {
                html +=
                    '<div class="chair t1"></div><div class="chair b1"></div><div class="chair l1"></div><div class="chair r1"></div>';
            } else if (shape === 'diagonal-8') {
                html +=
                    '<div class="chair tl-1"></div><div class="chair tl-2"></div><div class="chair tr-1"></div><div class="chair tr-2"></div><div class="chair bl-1"></div><div class="chair bl-2"></div><div class="chair br-1"></div><div class="chair br-2"></div>';
            } else {
                for (let i = 1; i <= p; i++) {
                    html += `<div class="chair c${i}"></div>`;
                }
            }
            return html;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('floorPlanCanvas');

            function initTableInteractions(selector) {
                interact(selector)
                    .draggable({
                        ignoreFrom: '.rotate-handle, .resize-handle',
                        modifiers: [
                            interact.modifiers.restrictRect({
                                restriction: '#floorPlanCanvas',
                                endOnly: false
                            })
                        ],
                        listeners: {
                            move(event) {
                                const target = event.target;
                                const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                                const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
                                const angle = parseFloat(target.getAttribute('data-angle')) || 0;

                                target.style.transform = `translate(${x}px, ${y}px) rotate(${angle}deg)`;
                                target.setAttribute('data-x', Math.round(x));
                                target.setAttribute('data-y', Math.round(y));
                            }
                        }
                    })
                    .resizable({
                        edges: {
                            right: '.resize-handle',
                            bottom: '.resize-handle'
                        },
                        listeners: {
                            move(event) {
                                const target = event.target;
                                let width = event.rect.width;
                                let height = event.rect.height;

                                if (target.className.includes('round-') || target.className.includes(
                                        'square-') || target.className.includes('diagonal-')) {
                                    width = Math.max(width, height);
                                    height = width;
                                }

                                if (width > 60 && height > 60) {
                                    target.style.width = `${width}px`;
                                    target.style.height = `${height}px`;
                                }
                            }
                        }
                    });

                interact(selector + ' .rotate-handle').draggable({
                    onstart: function(event) {
                        const handle = event.target;
                        const wrapper = handle.closest('.draggable-table-wrapper');
                        const rect = wrapper.getBoundingClientRect();
                        handle.setAttribute('data-center-x', rect.left + rect.width / 2);
                        handle.setAttribute('data-center-y', rect.top + rect.height / 2);
                    },
                    onmove: function(event) {
                        const handle = event.target;
                        const wrapper = handle.closest('.draggable-table-wrapper');
                        const cx = parseFloat(handle.getAttribute('data-center-x'));
                        const cy = parseFloat(handle.getAttribute('data-center-y'));

                        const angle = Math.atan2(event.clientY - cy, event.clientX - cx);
                        let degree = angle * (180 / Math.PI) - 90;
                        if (degree < 0) {
                            degree += 360;
                        }

                        const x = parseFloat(wrapper.getAttribute('data-x')) || 0;
                        const y = parseFloat(wrapper.getAttribute('data-y')) || 0;

                        wrapper.style.transform =
                            `translate(${x}px, ${y}px) rotate(${Math.round(degree)}deg)`;
                        wrapper.setAttribute('data-angle', Math.round(degree));
                    }
                });
            }

            initTableInteractions('.draggable-table-wrapper');

            // Quick Add Event Injector
            document.getElementById('btnQuickAdd').addEventListener('click', function() {
                const noMeja = document.getElementById('quick_no_meja').value;
                const name = document.getElementById('quick_name').value;
                const location = document.getElementById('quick_location').value;

                const selectEl = document.getElementById('quick_shape');
                const shape = selectEl.value;
                const qtyPax = selectEl.options[selectEl.selectedIndex].getAttribute('data-pax');

                if (!name || !location) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Mohon isi Name dan Location!'
                    });
                    return;
                }

                let defaultW = 92,
                    defaultH = 61;
                if (shape === 'rectangle-6-h') defaultW = 116;
                else if (shape === 'rectangle-8-h') defaultW = 135;
                else if (shape === 'rectangle-4-v') {
                    defaultW = 68;
                    defaultH = 86;
                } else if (shape === 'rectangle-6-v') {
                    defaultW = 68;
                    defaultH = 116;
                } else if (shape === 'rectangle-8-v') {
                    defaultW = 68;
                    defaultH = 135;
                } else if (shape.includes('square-2')) {
                    defaultW = 61;
                    defaultH = 61;
                } else if (shape === 'square-4') {
                    defaultW = 75;
                    defaultH = 75;
                } else if (shape === 'diagonal-8') {
                    defaultW = 120;
                    defaultH = 120;
                } else if (shape === 'round-4') {
                    defaultW = 76;
                    defaultH = 76;
                } else if (shape === 'round-6') {
                    defaultW = 92;
                    defaultH = 92;
                } else if (shape === 'round-8') {
                    defaultW = 108;
                    defaultH = 108;
                } else if (shape === 'round-10') {
                    defaultW = 123;
                    defaultH = 123;
                } else if (shape === 'round-12') {
                    defaultW = 175;
                    defaultH = 175;
                }

                const tempId = 'new-' + Date.now();
                const newTable = document.createElement('div');
                newTable.className = `draggable-table-wrapper is-new-table shape-${shape} pax-${qtyPax}`;
                newTable.id = tempId;
                newTable.style.transform = 'translate(20px, 20px) rotate(0deg)';
                newTable.style.width = `${defaultW}px`;
                newTable.style.height = `${defaultH}px`;

                newTable.setAttribute('data-id', '');
                newTable.setAttribute('data-is-new', 'true');
                newTable.setAttribute('data-no-meja', noMeja);
                newTable.setAttribute('data-name', name);
                newTable.setAttribute('data-qty-pax', qtyPax);
                newTable.setAttribute('data-location', location);
                newTable.setAttribute('data-shape', shape);
                newTable.setAttribute('data-x', '20');
                newTable.setAttribute('data-y', '20');
                newTable.setAttribute('data-angle', '0');

                newTable.innerHTML = `
                    ${generateChairsJs(shape, qtyPax)}
                    <div class="table-body">
                        <div class="rotate-handle"><i class="bi bi-arrow-clockwise"></i></div>
                        <div class="resize-handle"><i class="bi bi-arrow-left-right"></i></div>
                        <div class="table-content-container">
                            <span class="table-label">${name}</span>
                            <span class="table-pax">${qtyPax} Pax</span>
                            <small style="font-size: 8px; color:#64748b;">(${location})</small>
                        </div>
                    </div>
                `;

                canvas.appendChild(newTable);
                initTableInteractions('#' + tempId);

                document.getElementById('quick_no_meja').value = '';
                document.getElementById('quick_name').value = '';
            });

            // Save Layout Ajax Request
            document.getElementById('btnSaveLayout').addEventListener('click', function() {
                const elements = document.querySelectorAll('.draggable-table-wrapper');
                let layoutData = [];

                elements.forEach(el => {
                    layoutData.push({
                        id: el.getAttribute('data-id'),
                        is_new: el.getAttribute('data-is-new') === 'true',
                        no_meja: el.getAttribute('data-no-meja'),
                        name: el.getAttribute('data-name'),
                        qty_pax: el.getAttribute('data-qty-pax'),
                        location: el.getAttribute('data-location'),
                        shape: el.getAttribute('data-shape'),
                        position_x: parseInt(el.getAttribute('data-x')) || 0,
                        position_y: parseInt(el.getAttribute('data-y')) || 0,
                        width: parseInt(el.style.width) || 120,
                        height: parseInt(el.style.height) || 80,
                        rotation: parseInt(el.getAttribute('data-angle')) || 0
                    });
                });

                Swal.fire({
                    title: 'Saving Layout...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch("{{ route('mejas.save_layout') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            mejas: layoutData
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: data.message
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error'
                        });
                    });
            });
        });
    </script>
@endpush

@php
    function getChairHtml($shape, $pax)
    {
        $html = '';
        if ($shape === 'rectangle-4-h') {
            $html .=
                '<div class="chair t1"></div><div class="chair t2"></div><div class="chair b1"></div><div class="chair b2"></div>';
        } elseif ($shape === 'rectangle-6-h') {
            $html .=
                '<div class="chair t1"></div><div class="chair t2"></div><div class="chair t3"></div><div class="chair b1"></div><div class="chair b2"></div><div class="chair b3"></div>';
        } elseif ($shape === 'rectangle-8-h') {
            $html .=
                '<div class="chair t1"></div><div class="chair t2"></div><div class="chair t3"></div><div class="chair t4"></div><div class="chair b1"></div><div class="chair b2"></div><div class="chair b3"></div><div class="chair b4"></div>';
        } elseif ($shape === 'rectangle-4-v') {
            $html .=
                '<div class="chair l1"></div><div class="chair l2"></div><div class="chair r1"></div><div class="chair r2"></div>';
        } elseif ($shape === 'rectangle-6-v') {
            $html .=
                '<div class="chair l1"></div><div class="chair l2"></div><div class="chair l3"></div><div class="chair r1"></div><div class="chair r2"></div><div class="chair r3"></div>';
        } elseif ($shape === 'rectangle-8-v') {
            $html .=
                '<div class="chair l1"></div><div class="chair l2"></div><div class="chair l3"></div><div class="chair l4"></div><div class="chair r1"></div><div class="chair r2"></div><div class="chair r3"></div><div class="chair r4"></div>';
        } elseif ($shape === 'square-2-h') {
            $html .= '<div class="chair l1"></div><div class="chair r1"></div>';
        } elseif ($shape === 'square-2-v') {
            $html .= '<div class="chair t1"></div><div class="chair b1"></div>';
        } elseif ($shape === 'square-4') {
            $html .=
                '<div class="chair t1"></div><div class="chair b1"></div><div class="chair l1"></div><div class="chair r1"></div>';
        } elseif ($shape === 'diagonal-8') {
            $html .=
                '<div class="chair tl-1"></div><div class="chair tl-2"></div><div class="chair tr-1"></div><div class="chair tr-2"></div><div class="chair bl-1"></div><div class="chair bl-2"></div><div class="chair br-1"></div><div class="chair br-2"></div>';
        } else {
            for ($i = 1; $i <= (int) $pax; $i++) {
                $html .= '<div class="chair c' . $i . '"></div>';
            }
        }
        return $html;
    }
@endphp
