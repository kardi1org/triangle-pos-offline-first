@extends('layouts.app')

@section('title', 'Create Table Layout')

@push('page_css')
    <style>
        /* Area Kanvas Floor Plan */
        .floor-plan-canvas {
            width: 100%;
            height: 450px;
            background-color: #f4f6f9;
            background-image: radial-gradient(#d7dbdd 1px, transparent 1px);
            background-size: 20px 20px;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }

        /* Elemen Meja Interaktif Tunggal */
        .draggable-table-element {
            width: 120px;
            height: 80px;
            background-color: #ffffff;
            border: 2px solid #4f46e5;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: absolute;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: move;
            touch-action: none;
            user-select: none;
            transition: background-color 0.2s;
        }

        .draggable-table-element:hover {
            background-color: #f0fdf4;
        }

        /* Jika bentuk meja Lingkaran */
        .draggable-table-element.shape-circle {
            border-radius: 50%;
            width: 100px;
            height: 100px;
        }

        .table-label {
            font-weight: bold;
            font-size: 14px;
            color: #1e293b;
        }

        .table-pax {
            font-size: 11px;
            color: #64748b;
        }
    </style>
@endpush

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('mejas.index') }}">Table Shape</a></li>
        <li class="breadcrumb-item active">Add Layout</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('mejas.store') }}" method="POST">
            @csrf

            <input type="hidden" name="position_x" id="position_x" value="50">
            <input type="hidden" name="position_y" id="position_y" value="50">
            <input type="hidden" name="status" value="1">

            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            Create Table & Layout <i class="bi bi-check"></i>
                        </button>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-3 text-secondary">Table Specification</h5>

                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="no_meja">No. Meja</label>
                                        <input type="number" class="form-control" id="no_meja" name="no_meja"
                                            min="1" placeholder="6">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="name">Table Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="e.g., Table 6" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="qty_pax">Qty Pax <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="qty_pax" name="qty_pax" value="4"
                                    min="1" required>
                            </div>

                            <div class="form-group">
                                <label for="location">Location <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="e.g., Lantai 1, VIP, Outdoor" required>
                            </div>

                            <div class="form-group">
                                <label for="shape">Shape <span class="text-danger">*</span></label>
                                <select class="form-control" name="shape" id="shape" required>
                                    <option value="rectangle">Rectangle (Kotak)</option>
                                    <option value="circle">Circle (Bulat)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white font-weight-bold">
                            <i class="bi bi-grid-3x3-gap text-primary mr-1"></i> Drag & Position Your Table
                        </div>
                        <div class="card-body">
                            <div class="floor-plan-canvas" id="floorPlanCanvas">
                                <div class="draggable-table-element" id="livePreviewTable"
                                    style="transform: translate(50px, 50px);" data-x="50" data-y="50">
                                    <span class="table-label" id="lbl_name">Table X</span>
                                    <span class="table-pax" id="lbl_pax">4 Pax</span>
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                * Silakan geser kotak meja di atas untuk menetapkan posisi koordinat default awal.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('page_scripts')
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputName = document.getElementById('name');
            const inputPax = document.getElementById('qty_pax');
            const selectShape = document.getElementById('shape');

            const lblName = document.getElementById('lbl_name');
            const lblPax = document.getElementById('lbl_pax');
            const liveTable = document.getElementById('livePreviewTable');

            const inputX = document.getElementById('position_x');
            const inputY = document.getElementById('position_y');

            inputName.addEventListener('input', (e) => {
                lblName.textContent = e.target.value || 'Table X';
            });

            inputPax.addEventListener('input', (e) => {
                lblPax.textContent = (e.target.value || '0') + ' Pax';
            });

            selectShape.addEventListener('change', (e) => {
                if (e.target.value === 'circle') {
                    liveTable.classList.add('shape-circle');
                } else {
                    liveTable.classList.remove('shape-circle');
                }
            });

            interact('#livePreviewTable').draggable({
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: '#floorPlanCanvas',
                        endOnly: false
                    })
                ],
                autoScroll: true,
                listeners: {
                    move(event) {
                        const target = event.target;
                        const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                        const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                        target.style.transform = `translate(${x}px, ${y}px)`;
                        target.setAttribute('data-x', x);
                        target.setAttribute('data-y', y);

                        inputX.value = Math.round(x);
                        inputY.value = Math.round(y);
                    }
                }
            });
        });
    </script>
@endpush
