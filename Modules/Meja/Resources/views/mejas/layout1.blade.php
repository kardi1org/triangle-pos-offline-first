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

        /* Elemen Dasar Meja Dinamis (Wadah Utama Meja + Kursi) */
        .draggable-table-wrapper {
            position: absolute;
            display: inline-block;
            cursor: move;
            touch-action: none;
            user-select: none;
            padding: 24px;
            /* Ruang bernafas untuk kursi di sekeliling meja */
            transform-origin: center center;
        }

        .table-body {
            width: 120px;
            height: 80px;
            min-width: 75px;
            min-height: 75px;
            background-color: #ffffff;
            border: 2px solid #3b82f6;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            position: relative;
            z-index: 2;
            box-sizing: border-box;
            padding: 5px;
        }

        /* Jika Meja Bertipe Bulat (Circle) */
        .shape-circle .table-body {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            aspect-ratio: 1 / 1;
        }

        /* Jika Meja Bertipe Diagonal / Diamond (Belah Ketupat) */
        .shape-diagonal-8 .table-body {
            transform: rotate(45deg);
            width: 100px;
            height: 100px;
            aspect-ratio: 1 / 1;
            border-radius: 8px;
        }

        /* Menjaga konten teks di dalam meja diagonal agar tidak ikut miring */
        .shape-diagonal-8 .table-content-container,
        .shape-diagonal-8 .rotate-handle {
            transform: rotate(-45deg);
        }

        /* Warna pembeda jika sedang digeser/diubah */
        .draggable-table-wrapper.is-dragging .table-body,
        .draggable-table-wrapper.is-resizing .table-body {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        /* Meja Baru yang belum disimpan ke DB */
        .draggable-table-wrapper.is-new-table .table-body {
            border-color: #f59e0b;
            border-style: dashed;
        }

        /* KONTEN DI DALAM MEJA */
        .table-content-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .table-label {
            font-weight: bold;
            font-size: 11px;
            color: #1e293b;
            text-align: center;
            line-height: 1.1;
            word-break: break-all;
        }

        .table-pax {
            font-size: 9px;
            color: #64748b;
        }

        /* ICON PUTAR POSISI */
        .rotate-handle {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #3b82f6;
            cursor: grab;
            margin-bottom: 2px;
            transition: color 0.2s;
            z-index: 5;
        }

        .rotate-handle:hover {
            color: #1d4ed8;
        }

        .rotate-handle:active {
            cursor: grabbing;
        }

        /* ICON RESIZE */
        .resize-handle {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 14px;
            height: 14px;
            color: #64748b;
            cursor: se-resize;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            transform: rotate(-45deg);
        }

        .shape-diagonal-8 .resize-handle {
            bottom: 12px;
            right: 12px;
            transform: rotate(-90deg);
            /* Penyesuaian rotasi handle khusus meja diagonal */
        }

        .resize-handle:hover {
            color: #334155;
        }

        /* CSS VISUALISASI KURSI */
        .chair {
            position: absolute;
            background-color: #94a3b8;
            border: 1px solid #64748b;
            border-radius: 3px;
            z-index: 1;
        }

        /* Aturan Posisi Kursi Meja KOTAK (Rectangle) */
        .shape-rectangle .chair.left-1 {
            width: 10px;
            height: 20px;
            left: 8px;
            top: calc(50% - 10px);
        }

        .shape-rectangle .chair.right-1 {
            width: 10px;
            height: 20px;
            right: 8px;
            top: calc(50% - 10px);
        }

        .shape-rectangle .chair.top-1 {
            width: 20px;
            height: 10px;
            top: 8px;
            left: calc(50% - 10px);
        }

        .shape-rectangle .chair.bottom-1 {
            width: 20px;
            height: 10px;
            bottom: 8px;
            left: calc(50% - 10px);
        }

        .shape-rectangle .chair.top-2-1 {
            width: 20px;
            height: 10px;
            top: 8px;
            left: calc(50% - 25px);
        }

        .shape-rectangle .chair.top-2-2 {
            width: 20px;
            height: 10px;
            top: 8px;
            left: calc(50% + 5px);
        }

        .shape-rectangle .chair.bottom-2-1 {
            width: 20px;
            height: 10px;
            bottom: 8px;
            left: calc(50% - 25px);
        }

        .shape-rectangle .chair.bottom-2-2 {
            width: 20px;
            height: 10px;
            bottom: 8px;
            left: calc(50% + 5px);
        }

        .shape-rectangle .chair.top-3-1 {
            width: 18px;
            height: 10px;
            top: 8px;
            left: calc(50% - 35px);
        }

        .shape-rectangle .chair.top-3-2 {
            width: 18px;
            height: 10px;
            top: 8px;
            left: calc(50% - 9px);
        }

        .shape-rectangle .chair.top-3-3 {
            width: 18px;
            height: 10px;
            top: 8px;
            left: calc(50% + 17px);
        }

        .shape-rectangle .chair.bottom-3-1 {
            width: 18px;
            height: 10px;
            bottom: 8px;
            left: calc(50% - 35px);
        }

        .shape-rectangle .chair.bottom-3-2 {
            width: 18px;
            height: 10px;
            bottom: 8px;
            left: calc(50% - 9px);
        }

        .shape-rectangle .chair.bottom-3-3 {
            width: 18px;
            height: 10px;
            bottom: 8px;
            left: calc(50% + 17px);
        }

        /* Aturan Posisi Kursi Meja BULAT (Circle) */
        .shape-circle .chair {
            width: 14px;
            height: 14px;
            border-radius: 50%;
        }

        .shape-circle.pax-4 .chair.c1 {
            top: 8px;
            left: calc(50% - 7px);
        }

        .shape-circle.pax-4 .chair.c2 {
            bottom: 8px;
            left: calc(50% - 7px);
        }

        .shape-circle.pax-4 .chair.c3 {
            left: 8px;
            top: calc(50% - 7px);
        }

        .shape-circle.pax-4 .chair.c4 {
            right: 8px;
            top: calc(50% - 7px);
        }

        .shape-circle.pax-6 .chair.c1 {
            top: 6px;
            left: calc(50% - 7px);
        }

        .shape-circle.pax-6 .chair.c2 {
            bottom: 6px;
            left: calc(50% - 7px);
        }

        .shape-circle.pax-6 .chair.c3 {
            top: calc(25% + 2px);
            left: 10px;
        }

        .shape-circle.pax-6 .chair.c4 {
            bottom: calc(25% + 2px);
            left: 10px;
        }

        .shape-circle.pax-6 .chair.c5 {
            top: calc(25% + 2px);
            right: 10px;
        }

        .shape-circle.pax-6 .chair.c6 {
            bottom: calc(25% + 2px);
            right: 10px;
        }

        .shape-circle.pax-8 .chair.c1 {
            top: 6px;
            left: calc(50% - 7px);
        }

        .shape-circle.pax-8 .chair.c2 {
            bottom: 6px;
            left: calc(50% - 7px);
        }

        .shape-circle.pax-8 .chair.c3 {
            left: 6px;
            top: calc(50% - 7px);
        }

        .shape-circle.pax-8 .chair.c4 {
            right: 6px;
            top: calc(50% - 7px);
        }

        .shape-circle.pax-8 .chair.c5 {
            top: calc(18% + 2px);
            left: calc(18% + 2px);
        }

        .shape-circle.pax-8 .chair.c6 {
            top: calc(18% + 2px);
            right: calc(18% + 2px);
        }

        .shape-circle.pax-8 .chair.c7 {
            bottom: calc(18% + 2px);
            left: calc(18% + 2px);
        }

        .shape-circle.pax-8 .chair.c8 {
            bottom: calc(18% + 2px);
            right: calc(18% + 2px);
        }

        /* Aturan Posisi Kursi Meja DIAGONAL 8 (Jarak diperlebar agar tidak terlalu rapat) */
        .shape-diagonal-8 .chair {
            width: 18px;
            height: 10px;
        }

        /* Sisi Kiri Atas (Top-Left Side) */
        .shape-diagonal-8 .chair.tl-1 {
            transform: rotate(-45deg);
            top: 8px;
            left: 28px;
        }

        .shape-diagonal-8 .chair.tl-2 {
            transform: rotate(-45deg);
            top: 28px;
            left: 8px;
        }

        /* Sisi Kanan Atas (Top-Right Side) */
        .shape-diagonal-8 .chair.tr-1 {
            transform: rotate(45deg);
            top: 8px;
            right: 28px;
        }

        .shape-diagonal-8 .chair.tr-2 {
            transform: rotate(45deg);
            top: 28px;
            right: 8px;
        }

        /* Sisi Kiri Bawah (Bottom-Left Side) */
        .shape-diagonal-8 .chair.bl-1 {
            transform: rotate(45deg);
            bottom: 8px;
            left: 28px;
        }

        .shape-diagonal-8 .chair.bl-2 {
            transform: rotate(45deg);
            bottom: 28px;
            left: 8px;
        }

        /* Sisi Kanan Bawah (Bottom-Right Side) */
        .shape-diagonal-8 .chair.br-1 {
            transform: rotate(-45deg);
            bottom: 8px;
            right: 28px;
        }

        .shape-diagonal-8 .chair.br-2 {
            transform: rotate(-45deg);
            bottom: 28px;
            right: 8px;
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
                                <option value="rectangle-4" data-shape="rectangle" data-pax="4">Rectangle - 4 Pax</option>
                                <option value="rectangle-6" data-shape="rectangle" data-pax="6">Rectangle - 6 Pax</option>
                                <option value="rectangle-8" data-shape="rectangle" data-pax="8">Rectangle - 8 Pax</option>
                                <option value="circle-4" data-shape="circle" data-pax="4">Circle - 4 Pax</option>
                                <option value="circle-6" data-shape="circle" data-pax="6">Circle - 6 Pax</option>
                                <option value="circle-8" data-shape="circle" data-pax="8">Circle - 8 Pax</option>
                                <option value="diagonal-8" data-shape="diagonal-8" data-pax="8">Diagonal - 8 Pax (2 per
                                    sisi)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quick_qty_pax">Qty Pax (Kapasitas Terkunci)</label>
                            <input type="number" class="form-control" id="quick_qty_pax" value="4" readonly
                                style="background-color: #e2e8f0;">
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
                                    $isDiag = $meja->shape === 'diagonal-8';
                                    $w = $meja->width ?? ($meja->shape === 'circle' || $isDiag ? 100 : 120);
                                    $h = $meja->height ?? ($meja->shape === 'circle' || $isDiag ? 100 : 80);
                                    $r = $meja->rotation ?? 0;
                                @endphp
                                <div class="draggable-table-wrapper shape-{{ $meja->shape }} pax-{{ $meja->qty_pax }}"
                                    id="meja-{{ $meja->id }}" data-id="{{ $meja->id }}" data-is-new="false"
                                    data-no-meja="{{ $meja->no_meja }}" data-name="{{ $meja->name }}"
                                    data-qty-pax="{{ $meja->qty_pax }}" data-location="{{ $meja->location }}"
                                    data-shape="{{ $meja->shape }}" data-x="{{ $meja->position_x }}"
                                    data-y="{{ $meja->position_y }}" data-angle="{{ $r }}"
                                    style="transform: translate({{ $meja->position_x }}px, {{ $meja->position_y }}px) rotate({{ $r }}deg);">

                                    {!! getChairHtml($meja->shape, $meja->qty_pax) !!}

                                    <div class="table-body"
                                        style="width: {{ $w }}px; height: {{ $h }}px;">
                                        <div class="rotate-handle"><i class="bi bi-arrow-clockwise"></i></div>
                                        <div class="resize-handle"><i class="bi bi-arrow-left-right"></i></div>

                                        <div class="table-content-container">
                                            <span class="table-label">{{ $meja->name }}</span>
                                            <span class="table-pax">{{ $meja->qty_pax }} Pax</span>
                                            <small class="text-muted-custom"
                                                style="font-size: 8px;">({{ $meja->location }})</small>
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
            if (shape === 'rectangle') {
                html += '<div class="chair left-1"></div><div class="chair right-1"></div>';
                if (parseInt(pax) === 4) {
                    html += '<div class="chair top-1"></div><div class="chair bottom-1"></div>';
                } else if (parseInt(pax) === 6) {
                    html +=
                        '<div class="chair top-2-1"></div><div class="chair top-2-2"></div><div class="chair bottom-2-1"></div><div class="chair bottom-2-2"></div>';
                } else if (parseInt(pax) === 8) {
                    html +=
                        '<div class="chair top-3-1"></div><div class="chair top-3-2"></div><div class="chair top-3-3"></div><div class="chair bottom-3-1"></div><div class="chair bottom-3-2"></div><div class="chair bottom-3-3"></div>';
                }
            } else if (shape === 'diagonal-8') {
                html += '<div class="chair tl-1"></div><div class="chair tl-2"></div>';
                html += '<div class="chair tr-1"></div><div class="chair tr-2"></div>';
                html += '<div class="chair bl-1"></div><div class="chair bl-2"></div>';
                html += '<div class="chair br-1"></div><div class="chair br-2"></div>';
            } else {
                for (let i = 1; i <= parseInt(pax); i++) {
                    html += `<div class="chair c${i}"></div>`;
                }
            }
            return html;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('floorPlanCanvas');
            const quickShapeSelect = document.getElementById('quick_shape');
            const quickQtyInput = document.getElementById('quick_qty_pax');

            quickShapeSelect.addEventListener('change', function() {
                quickQtyInput.value = this.options[this.selectedIndex].getAttribute('data-pax');
            });

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
                            start(event) {
                                event.target.classList.add('is-dragging');
                            },
                            move(event) {
                                const target = event.target;
                                const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                                const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
                                const angle = parseFloat(target.getAttribute('data-angle')) || 0;

                                target.style.transform = `translate(${x}px, ${y}px) rotate(${angle}deg)`;
                                target.setAttribute('data-x', Math.round(x));
                                target.setAttribute('data-y', Math.round(y));
                            },
                            end(event) {
                                event.target.classList.remove('is-dragging');
                            }
                        }
                    })
                    .resizable({
                        edges: {
                            right: '.resize-handle',
                            bottom: '.resize-handle'
                        },
                        listeners: {
                            start(event) {
                                event.target.classList.add('is-resizing');
                            },
                            move(event) {
                                const target = event.target;
                                const tableBody = target.querySelector('.table-body');

                                let width = event.rect.width - 48;
                                let height = event.rect.height - 48;

                                // Bentuk lingkaran dan diagonal dikunci aspek rasio 1:1 saat diperbesar
                                if (target.classList.contains('shape-circle') || target.classList.contains(
                                        'shape-diagonal-8')) {
                                    width = Math.max(width, height);
                                    height = width;
                                }

                                if (width > 65 && height > 65) {
                                    tableBody.style.width = `${width}px`;
                                    tableBody.style.height = `${height}px`;
                                }
                            },
                            end(event) {
                                event.target.classList.remove('is-resizing');
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

            document.getElementById('btnQuickAdd').addEventListener('click', function() {
                const noMeja = document.getElementById('quick_no_meja').value;
                const name = document.getElementById('quick_name').value;
                const location = document.getElementById('quick_location').value;

                const selectedOption = quickShapeSelect.options[quickShapeSelect.selectedIndex];
                const shape = selectedOption.getAttribute('data-shape');
                const qtyPax = selectedOption.getAttribute('data-pax');

                if (!name || !location) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Mohon isi Name dan Location!'
                    });
                    return;
                }

                const tempId = 'new-' + Date.now();
                const defaultW = (shape === 'circle' || shape === 'diagonal-8') ? 100 : 120;
                const defaultH = (shape === 'circle' || shape === 'diagonal-8') ? 100 : 80;

                const newTableWrapper = document.createElement('div');
                newTableWrapper.className =
                    `draggable-table-wrapper is-new-table shape-${shape} pax-${qtyPax}`;
                newTableWrapper.id = tempId;
                newTableWrapper.style.transform = 'translate(20px, 20px) rotate(0deg)';

                newTableWrapper.setAttribute('data-id', '');
                newTableWrapper.setAttribute('data-is-new', 'true');
                newTableWrapper.setAttribute('data-no-meja', noMeja);
                newTableWrapper.setAttribute('data-name', name);
                newTableWrapper.setAttribute('data-qty-pax', qtyPax);
                newTableWrapper.setAttribute('data-location', location);
                newTableWrapper.setAttribute('data-shape', shape);
                newTableWrapper.setAttribute('data-x', '20');
                newTableWrapper.setAttribute('data-y', '20');
                newTableWrapper.setAttribute('data-angle', '0');

                const chairsHtml = generateChairsJs(shape, qtyPax);
                newTableWrapper.innerHTML = `
                    ${chairsHtml}
                    <div class="table-body" style="width: ${defaultW}px; height: ${defaultH}px;">
                        <div class="rotate-handle"><i class="bi bi-arrow-clockwise"></i></div>
                        <div class="resize-handle"><i class="bi bi-arrow-left-right"></i></div>
                        <div class="table-content-container">
                            <span class="table-label">${name}</span>
                            <span class="table-pax">${qtyPax} Pax</span>
                            <small class="text-muted-custom" style="font-size: 8px;">(${location})</small>
                        </div>
                    </div>
                `;

                canvas.appendChild(newTableWrapper);
                initTableInteractions('#' + tempId);

                document.getElementById('quick_no_meja').value = '';
                document.getElementById('quick_name').value = '';

                Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    .fire({
                        icon: 'success',
                        title: `${name} injected!`
                    });
            });

            document.getElementById('btnSaveLayout').addEventListener('click', function() {
                const elements = document.querySelectorAll('.draggable-table-wrapper');
                let layoutData = [];

                elements.forEach(el => {
                    const tBody = el.querySelector('.table-body');
                    layoutData.push({
                        id: el.getAttribute('data-id'),
                        is_new: el.getAttribute('data-is-new') === 'true',
                        no_meja: el.getAttribute('data-no-meja'),
                        name: el.getAttribute('data-name'),
                        qty_pax: el.getAttribute('data-qty-pax'),
                        location: el.getAttribute('data-location'),
                        shape: el.getAttribute('data-shape'),
                        position_x: el.getAttribute('data-x'),
                        position_y: el.getAttribute('data-y'),
                        width: parseInt(tBody.style.width) || 120,
                        height: parseInt(tBody.style.height) || 80,
                        rotation: el.getAttribute('data-angle') || 0
                    });
                });

                if (layoutData.length === 0) return;

                Swal.fire({
                    title: 'Saving Changes...',
                    text: 'Updating layout properties...',
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
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                    icon: 'success',
                                    title: 'Saved!',
                                    text: 'Layout saved successfully!'
                                })
                                .then(() => {
                                    window.location.reload();
                                });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: data.message || 'Server error.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Connection failed.'
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
        if ($shape === 'rectangle') {
            $html .= '<div class="chair left-1"></div><div class="chair right-1"></div>';
            if ((int) $pax === 4) {
                $html .= '<div class="chair top-1"></div><div class="chair bottom-1"></div>';
            } elseif ((int) $pax === 6) {
                $html .=
                    '<div class="chair top-2-1"></div><div class="chair top-2-2"></div><div class="chair bottom-2-1"></div><div class="chair bottom-2-2"></div>';
            } elseif ((int) $pax === 8) {
                $html .=
                    '<div class="chair top-3-1"></div><div class="chair top-3-2"></div><div class="chair top-3-3"></div><div class="chair bottom-3-1"></div><div class="chair bottom-3-2"></div><div class="chair bottom-3-3"></div>';
            }
        } elseif ($shape === 'diagonal-8') {
            $html .= '<div class="chair tl-1"></div><div class="chair tl-2"></div>';
            $html .= '<div class="chair tr-1"></div><div class="chair tr-2"></div>';
            $html .= '<div class="chair bl-1"></div><div class="chair bl-2"></div>';
            $html .= '<div class="chair br-1"></div><div class="chair br-2"></div>';
        } else {
            for ($i = 1; $i <= (int) $pax; $i++) {
                $html .= '<div class="chair c' . $i . '"></div>';
            }
        }
        return $html;
    }
@endphp
