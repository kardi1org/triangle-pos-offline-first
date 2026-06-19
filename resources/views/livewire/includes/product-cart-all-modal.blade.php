    <!-- ✅ Modal List Meja -->
    <div class="modal fade" id="tableSelectionModal" tabindex="-1" role="dialog" aria-labelledby="tableSelectionModalLabel"
        aria-hidden="true" wire:ignore.self>

        <style>
            .modal-floor-plan-canvas {
                width: 100%;
                height: 550px;
                background-color: #f8fafc;
                background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
                background-size: 24px 24px;
                border: 3px dashed #94a3b8;
                border-radius: 12px;
                position: relative;
                overflow: auto;
            }

            /* Elemen Dasar Meja Dinamis */
            .modal-table-wrapper {
                position: absolute;
                cursor: pointer;
                user-select: none;
                transform-origin: center center;
                z-index: 2;
                overflow: visible !important;
                /* Agar kursi di luar border tidak terpotong */
                transition: opacity 0.2s ease;
            }

            .modal-table-wrapper:hover {
                opacity: 0.85;
            }

            .modal-table-body {
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

            /* State jika Meja Terpilih (Selected) */
            .modal-table-wrapper.selected .modal-table-body {
                border-color: #10b981 !important;
                background-color: #ecfdf5 !important;
                box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
            }

            /* Kelestarian Bentuk Meja Masing-Masing Kategori */
            .shape-round-4 .modal-table-body,
            .shape-round-6 .modal-table-body,
            .shape-round-8 .modal-table-body,
            .shape-round-10 .modal-table-body,
            .shape-round-12 .modal-table-body {
                border-radius: 50% !important;
            }

            .shape-square-2-h .modal-table-body,
            .shape-square-2-v .modal-table-body,
            .shape-square-4 .modal-table-body {
                border-radius: 4px !important;
            }

            .shape-rectangle-4-h .modal-table-body,
            .shape-rectangle-6-h .modal-table-body,
            .shape-rectangle-8-h .modal-table-body,
            .shape-rectangle-4-v .modal-table-body,
            .shape-rectangle-6-v .modal-table-body,
            .shape-rectangle-8-v .modal-table-body {
                border-radius: 6px !important;
            }

            /* FIX DIAGONAL SEGI 4 (Belah Ketupat / Diamond) */
            .shape-diagonal-8 .modal-table-body {
                border: none !important;
                background-color: #3b82f6 !important;
                padding: 2px !important;
                clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%) !important;
                border-radius: 0px !important;
            }

            .modal-table-wrapper.selected.shape-diagonal-8 .modal-table-body {
                background-color: #10b981 !important;
            }

            .shape-diagonal-8 .modal-table-body .modal-content-container {
                background-color: #ffffff !important;
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%) !important;
            }

            .modal-table-wrapper.selected.shape-diagonal-8 .modal-table-body .modal-content-container {
                background-color: #ecfdf5 !important;
            }

            /* Konten Teks di Dalam Meja */
            .modal-content-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 6;
                text-align: center;
            }

            .modal-table-label {
                font-weight: bold;
                font-size: 11px;
                color: #1e293b;
            }

            .modal-table-pax {
                font-size: 9px;
                color: #64748b;
            }

            /* Overlay Checkmark Badge */
            .modal-selection-badge {
                position: absolute;
                top: -10px;
                right: -10px;
                width: 22px;
                height: 22px;
                background-color: #10b981;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 11px;
                z-index: 12;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            }

            /* ========================================================
           ATURAN POSISI CSS CHAIRS 1:1 DARI LAYOUT UTAMA
           ======================================================== */
            .modal-table-wrapper .chair {
                position: absolute;
                background-color: #94a3b8;
                border: 1px solid #64748b;
                border-radius: 3px;
                z-index: 1;
                /* Berada di bawah table-body agar rapi */
            }

            /* --- RECTANGLE HORIZONTAL (UKURAN KURSI) --- */
            .shape-rectangle-4-h .chair,
            .shape-rectangle-6-h .chair,
            .shape-rectangle-8-h .chair {
                width: 22px !important;
                height: 12px !important;
            }

            /* Koordinat Kursi */
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

            /* --- RECTANGLE VERTICAL (UKURAN KURSI) --- */
            .shape-rectangle-4-v .chair,
            .shape-rectangle-6-v .chair,
            .shape-rectangle-8-v .chair {
                width: 12px !important;
                height: 22px !important;
            }

            /* Koordinat Kursi */
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

            .shape-rectangle-8-v .chair.l1 {
                left: -16px;
                top: calc(50% - 56px);
            }

            .shape-rectangle-8-v .chair.l2 {
                left: -16px;
                top: calc(50% - 28px);
            }

            .shape-rectangle-8-v .chair.l3 {
                left: -16px;
                top: calc(50% + 0px);
            }

            .shape-rectangle-8-v .chair.l4 {
                left: -16px;
                top: calc(50% + 28px);
            }

            .shape-rectangle-8-v .chair.r1 {
                right: -16px;
                top: calc(50% - 56px);
            }

            .shape-rectangle-8-v .chair.r2 {
                right: -16px;
                top: calc(50% - 28px);
            }

            .shape-rectangle-8-v .chair.r3 {
                right: -16px;
                top: calc(50% + 0px);
            }

            .shape-rectangle-8-v .chair.r4 {
                right: -16px;
                top: calc(50% + 28px);
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
                width: 22px !important;
                height: 12px !important;
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

            /* --- ROUND SERIES (FIXED: DITAMBAHKAN RADIAL KOORDINAT 10 & 12) --- */
            [class*="shape-round-"] .chair {
                width: 16px;
                height: 16px;
                border-radius: 50%;
                top: calc(50% - 8px + (var(--y-dir, 0) * 62%));
                left: calc(50% - 8px + (var(--x-dir, 0) * 62%));
            }

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

            /* ✅ PERBAIKAN: Ditambahkan koordinat lingkar untuk Round 10 */
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

            /* ✅ PERBAIKAN: Ditambahkan koordinat lingkar untuk Round 12 */
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

        <div class="modal-dialog modal-lg" role="document" x-data="{
            localSelectedIds: [],
            syncLivewireState() {
                this.localSelectedIds = $wire.table_ids_array.map(id => parseInt(id));
            },
            toggleLocalTable(id) {
                const intId = parseInt(id);
                const index = this.localSelectedIds.indexOf(intId);
                if (index > -1) {
                    this.localSelectedIds.splice(index, 1);
                } else {
                    this.localSelectedIds.push(intId);
                }
            }
        }" x-init="syncLivewireState()"
            x-on:show.bs.modal="syncLivewireState()" x-on:sync-table-selection.window="syncLivewireState()">

            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold text-dark" id="tableSelectionModalLabel">
                        <i class="bi bi-grid-3x3-gap mr-1 text-primary"></i> Denah Meja & Kapasitas (Floor Plan)
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body bg-white p-3">
                    <div class="modal-floor-plan-canvas" id="modalFloorPlanCanvas">

                        @foreach ($tables as $table)
                            @php
                                $s = $table->shape;
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

                                $w = $table->width ?? $defaultW;
                                $h = $table->height ?? $defaultH;

                                if ($s === 'diagonal-8' && (!isset($table->width) || $table->width != 120)) {
                                    $w = 120;
                                    $h = 120;
                                }

                                $r = $table->rotation ?? 0;
                                $x = $table->position_x ?? 20;
                                $y = $table->position_y ?? 20;
                            @endphp

                            <div class="modal-table-wrapper shape-{{ $table->shape }} pax-{{ $table->qty_pax }}"
                                id="modal-meja-{{ $table->id }}"
                                :class="{ 'selected': localSelectedIds.includes({{ $table->id }}) }"
                                @click.stop="toggleLocalTable({{ $table->id }})"
                                style="transform: translate({{ $x }}px, {{ $y }}px) rotate({{ $r }}deg); width: {{ $w }}px; height: {{ $h }}px;">

                                @if ($table->shape === 'rectangle-4-h')
                                    <div class="chair t1"></div>
                                    <div class="chair t2"></div>
                                    <div class="chair b1"></div>
                                    <div class="chair b2"></div>
                                @elseif ($table->shape === 'rectangle-6-h')
                                    <div class="chair t1"></div>
                                    <div class="chair t2"></div>
                                    <div class="chair t3"></div>
                                    <div class="chair b1"></div>
                                    <div class="chair b2"></div>
                                    <div class="chair b3"></div>
                                @elseif ($table->shape === 'rectangle-8-h')
                                    <div class="chair t1"></div>
                                    <div class="chair t2"></div>
                                    <div class="chair t3"></div>
                                    <div class="chair t4"></div>
                                    <div class="chair b1"></div>
                                    <div class="chair b2"></div>
                                    <div class="chair b3"></div>
                                    <div class="chair b4"></div>
                                @elseif ($table->shape === 'rectangle-4-v')
                                    <div class="chair l1"></div>
                                    <div class="chair l2"></div>
                                    <div class="chair r1"></div>
                                    <div class="chair r2"></div>
                                @elseif ($table->shape === 'rectangle-6-v')
                                    <div class="chair l1"></div>
                                    <div class="chair l2"></div>
                                    <div class="chair l3"></div>
                                    <div class="chair r1"></div>
                                    <div class="chair r2"></div>
                                    <div class="chair r3"></div>
                                @elseif ($table->shape === 'rectangle-8-v')
                                    <div class="chair l1"></div>
                                    <div class="chair l2"></div>
                                    <div class="chair l3"></div>
                                    <div class="chair l4"></div>
                                    <div class="chair r1"></div>
                                    <div class="chair r2"></div>
                                    <div class="chair r3"></div>
                                    <div class="chair r4"></div>
                                @elseif ($table->shape === 'square-2-h')
                                    <div class="chair l1"></div>
                                    <div class="chair r1"></div>
                                @elseif ($table->shape === 'square-2-v')
                                    <div class="chair t1"></div>
                                    <div class="chair b1"></div>
                                @elseif ($table->shape === 'square-4')
                                    <div class="chair t1"></div>
                                    <div class="chair b1"></div>
                                    <div class="chair l1"></div>
                                    <div class="chair r1"></div>
                                @elseif ($table->shape === 'diagonal-8')
                                    <div class="chair tl-1"></div>
                                    <div class="chair tl-2"></div>
                                    <div class="chair tr-1"></div>
                                    <div class="chair tr-2"></div>
                                    <div class="chair bl-1"></div>
                                    <div class="chair bl-2"></div>
                                    <div class="chair br-1"></div>
                                    <div class="chair br-2"></div>
                                @else
                                    @for ($i = 1; $i <= (int) $table->qty_pax; $i++)
                                        <div class="chair c{{ $i }}"></div>
                                    @endfor
                                @endif

                                <div class="modal-table-body">
                                    <div class="modal-selection-badge"
                                        x-show="localSelectedIds.includes({{ $table->id }})">
                                        <i class="bi bi-check-lg"></i>
                                    </div>

                                    <div class="modal-content-container">
                                        <span class="modal-table-label">{{ $table->name }}</span>
                                        <span class="modal-table-pax">{{ $table->qty_pax }} Pax</span>
                                        <small style="font-size: 8px; color:#64748b;">({{ $table->location }})</small>
                                    </div>
                                </div>

                            </div>
                        @endforeach

                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary shadow-sm font-weight-bold" data-dismiss="modal"
                        @click="$wire.set('table_ids_array', localSelectedIds); $wire.call('updateNameString');">
                        Konfirmasi Meja <i class="bi bi-check-all ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal List Pending Orders -->
    <div wire:ignore.self class="modal fade" id="pendingOrdersModal" tabindex="-1" role="dialog"
        aria-labelledby="pendingOrdersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="pendingOrdersModalLabel">List Orders</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (collect($this->pendingOrders)->isEmpty())
                        <div class="text-center text-muted py-3">
                            No pending orders found.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Reference</th>
                                        <th>Customer</th>
                                        <th>Table</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->pendingOrders as $order)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $order->reference }}</td>
                                            <td>{{ $order->customer_name }}</td>
                                            <td>
                                                @if (isset($order->table_names) && is_array($order->table_names) && count($order->table_names) > 0)
                                                    {{ implode(', ', $order->table_names) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $order->date }}</td>
                                            <td>{{ format_currency($order->total_amount) }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    @php
                                                        $kitchenLogs = $order->kitchenLogs ?? collect();

                                                        // Ada log yang belum diprint
                                                        $hasPendingUpdate =
                                                            $kitchenLogs->where('is_printed', 0)->count() > 0;

                                                        // Ada log yang belum diprint DAN sudah diapprove
                                                        // Gunakan != null karena approved_by biasanya berisi ID User (bukan sekedar angka 1)
                                                        $isApproved =
                                                            $kitchenLogs
                                                                ->where('is_printed', 0)
                                                                ->whereNotNull('approved_by')
                                                                ->count() > 0;
                                                    @endphp

                                                    @if ($order->is_printed == 1 && $hasPendingUpdate)
                                                        {{-- TOMBOL UPDATE (VOID/NEW) --}}
                                                        <button wire:click="previewVoidOrder({{ $order->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="previewVoidOrder({{ $order->id }})"
                                                            class="btn btn-sm btn-danger mr-1" style="width: 100px;">

                                                            <span wire:loading
                                                                wire:target="previewVoidOrder({{ $order->id }})">...</span>
                                                            <span wire:loading.remove
                                                                wire:target="previewVoidOrder({{ $order->id }})">
                                                                <i class="bi bi-printer-fill"></i> Print Update
                                                            </span>
                                                        </button>
                                                    @else
                                                        {{-- TOMBOL PRINT BIASA (AWAL) --}}
                                                        <button wire:click="previewOrder({{ $order->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="previewOrder({{ $order->id }})"
                                                            class="btn btn-sm {{ $order->is_printed ? 'btn-secondary' : 'btn-primary' }} mr-1"
                                                            style="width: 100px;">

                                                            <span wire:loading
                                                                wire:target="previewOrder({{ $order->id }})">...</span>
                                                            <span wire:loading.remove
                                                                wire:target="previewOrder({{ $order->id }})">
                                                                <i class="bi bi-printer"></i>
                                                                {{ $order->is_printed ? 'Re-Print' : 'Print KOT' }}
                                                            </span>
                                                        </button>
                                                    @endif
                                                    <button wire:click="showOrderDetail({{ $order->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="showOrderDetail({{ $order->id }})"
                                                        class="btn btn-sm btn-info mr-1">

                                                        <span wire:loading
                                                            wire:target="showOrderDetail({{ $order->id }})">
                                                            Loading...
                                                        </span>

                                                        <span wire:loading.remove
                                                            wire:target="showOrderDetail({{ $order->id }})">
                                                            Detail
                                                        </span>
                                                    </button>
                                                    {{-- 🆕 TOMBOL PRE-BILL --}}
                                                    <button wire:click="printPreBill({{ $order->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="printPreBill({{ $order->id }})"
                                                        class="btn btn-sm btn-warning mr-1">

                                                        <span wire:loading
                                                            wire:target="printPreBill({{ $order->id }})">
                                                            Loading...
                                                            {{-- <i class="bi bi-hourglass-split"></i> --}}
                                                        </span>
                                                        <span wire:loading.remove
                                                            wire:target="printPreBill({{ $order->id }})">
                                                            <i class="bi bi-file-earmark-text"></i> Pre-Bill
                                                        </span>
                                                    </button>
                                                    <button wire:click="restorePendingOrder({{ $order->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="restorePendingOrder({{ $order->id }})"
                                                        class="btn btn-sm btn-success">

                                                        <span wire:loading
                                                            wire:target="restorePendingOrder({{ $order->id }})">
                                                            Processing...
                                                        </span>

                                                        <span wire:loading.remove
                                                            wire:target="restorePendingOrder({{ $order->id }})">
                                                            Select
                                                        </span>
                                                    </button>
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <!-- ✅ Footer dengan tombol Close -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ✅ Modal Pre-Bill -->
    <div class="modal fade" id="preBillModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pre-Bill Preview</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" id="prebill-content">
                    <p class="text-center">Memuat data...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="printPreBill()">Print</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <style>
        /* =======================
               PREVIEW (SCREEN)
            ======================= */
        #print-area {
            font-family: monospace;
            font-size: 12px;
            line-height: 1.35;
            color: #000;
        }

        /* =======================
               PRINT THERMAL MODE
            ======================= */
        @media print {

            /* RESET TOTAL */
            @page {
                size: auto;
                margin: 0;
                /* 🔥 WAJIB */
            }

            html,
            body {
                margin: 0;
                padding: 0;
                height: auto;
                overflow: visible;
            }

            /* SEMBUNYIKAN SEMUA */
            body * {
                visibility: hidden !important;
            }

            /* TAMPILKAN HANYA AREA PRINT */
            #print-area,
            #print-area * {
                visibility: visible !important;
            }

            #print-area {
                position: absolute;
                top: 0;
                left: 0;

                width: 100%;
                max-width: 80mm;
                /* thermal 80mm */
                padding: 2px 3px;
                /* 🔥 super irit */
                margin: 0;

                box-sizing: border-box;
            }

            /* HILANGKAN MODAL BOOTSTRAP */
            .modal,
            .modal-dialog,
            .modal-content,
            .modal-header,
            .modal-footer,
            .modal-backdrop,
            .close,
            button {
                display: none !important;
            }

            /* TEKS */
            h4,
            h5 {
                margin: 3px 0;
                font-size: 13px;
                text-align: center;
            }

            div {
                margin: 0;
                padding: 0;
            }

            hr {
                border: 0;
                border-top: 1px dashed #000;
                margin: 4px 0;
            }
        }

        /* =======================
               MODE PRINTER BIASA (A4)
               Opsional manual
            ======================= */
        #print-area.print-a4 {
            max-width: 210mm;
            font-size: 13px;
            padding: 10px;
        }
    </style>

    <!-- ✅ Print preview Order dapur -->
    <div wire:ignore.self class="modal fade" id="kitchenPreviewModal" tabindex="-1" role="dialog"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                {{-- Header Modal --}}
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Kitchen Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        {{-- ✅ TAMBAHKAN ONCLICK INI --}} onclick="unblurPendingOrdersModal()">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                @if ($previewOrderData)
                    <div class="modal-body" id="print-area"
                        style="font-family: monospace; font-size: 12px; line-height: 1.35;">

                        {{-- HEADER --}}
                        <div style="text-align:center; margin-bottom:6px;">
                            @if (isset($previewOrderData['is_combined']))
                                <strong style="font-size:16px;">UPDATE ORDER</strong><br>
                            @elseif (isset($previewOrderData['is_void']))
                                <strong style="font-size:18px; color:red;">VOID ORDER</strong><br>
                            @else
                                <strong style="font-size:14px;">KITCHEN ORDER</strong><br>
                            @endif
                            <span>-----------------------------</span>
                        </div>

                        {{-- INFO ORDER --}}
                        <div style="margin-bottom:6px;">
                            <div>Ref : {{ $previewOrderData['reference'] }}</div>
                            <div>Type: <strong>{{ strtoupper($previewOrderData['typeOrder']) }}</strong></div>
                            <div>Cust: {{ $previewOrderData['customer_name'] }}</div>
                            <div>Meja: {{ $previewOrderData['meja_name'] }}</div>
                            <div>Tgl : {{ $previewOrderData['date'] }}</div>
                        </div>

                        @php
                            $details = collect($previewOrderData['details']);
                            $voidItems = $details->where('type', 'void');
                            $newItems = $details->where('type', 'new');
                            // Jika print awal (bukan update), anggap semua item adalah item yang harus diprint
                            $isUpdate = isset($previewOrderData['is_combined']) || isset($previewOrderData['is_void']);
                        @endphp

                        {{-- ========================================== --}}
                        {{-- SECTION VOID (Hanya muncul jika update) --}}
                        {{-- ========================================== --}}
                        @if ($voidItems->count() > 0)
                            <div
                                style="text-align:center; background-color:#000; color:#fff; padding:2px; margin-top:5px;">
                                <strong>*** VOID (BATAL) ***</strong>
                            </div>
                            @foreach ($voidItems as $item)
                                @php
                                    // --- LOGIKA AGREGASI VARIANT UNTUK VOID ---
                                    $variantDetail = $item['variant_detail'] ?? null;
                                    $variants = [];
                                    $aggregatedVariants = [];

                                    if (isset($variantDetail)) {
                                        if (
                                            is_array($variantDetail) &&
                                            !empty($variantDetail) &&
                                            $variantDetail !== [[]]
                                        ) {
                                            $variants = $variantDetail;
                                        } elseif (is_string($variantDetail)) {
                                            $decoded = json_decode($variantDetail, true);
                                            if (is_array($decoded)) {
                                                $variants = $decoded;
                                            }
                                        }
                                    }

                                    foreach ($variants as $variant) {
                                        $vText = trim($variant['variant'] ?? '');
                                        $vType = trim($variant['typeOrder'] ?? $previewOrderData['typeOrder']);
                                        $key = $vText . '-' . $vType;
                                        $label =
                                            $vText === ''
                                                ? 'TYPE ' . strtoupper($vType)
                                                : strtoupper($vText) . ' (' . $vType . ')';

                                        if (!isset($aggregatedVariants[$key])) {
                                            $aggregatedVariants[$key] = ['label' => $label, 'qty' => 0];
                                        }
                                        $aggregatedVariants[$key]['qty']++;
                                    }
                                @endphp

                                <div style="margin-top:6px;">
                                    <strong
                                        style="text-decoration: line-through;">{{ strtoupper($item['product_name']) }}</strong>
                                    <span style="float:right;">x{{ $item['quantity'] }}</span>
                                </div>

                                {{-- Render Variant untuk Void --}}
                                @if (!empty($aggregatedVariants))
                                    @foreach ($aggregatedVariants as $v)
                                        <div style="padding-left:8px; font-size:11px; text-decoration: line-through;">
                                            - {{ $v['label'] }} x{{ $v['qty'] }}
                                        </div>
                                    @endforeach
                                @else
                                    <div style="padding-left:8px; font-size:11px; text-decoration: line-through;">
                                        - TYPE {{ strtoupper($previewOrderData['typeOrder']) }}
                                    </div>
                                @endif

                                @if (isset($item['note']) && $item['note'])
                                    <div
                                        style="padding-left:8px; font-size:10px; font-style: italic; text-decoration: line-through;">
                                        Note: {{ $item['note'] }}
                                    </div>
                                @endif
                                <div>-----------------------------</div>
                            @endforeach
                        @endif

                        {{-- ========================================== --}}
                        {{-- SECTION NEW / NORMAL ORDER --}}
                        {{-- ========================================== --}}
                        @if ($newItems->count() > 0 || !$isUpdate)
                            @if ($isUpdate && $newItems->count() > 0)
                                <div style="text-align:center; border:1px solid #000; padding:2px; margin-top:10px;">
                                    <strong>*** NEW (TAMBAHAN) ***</strong>
                                </div>
                            @endif

                            {{-- Looping item (Normal atau New) --}}
                            @php $targetItems = $isUpdate ? $newItems : $details; @endphp

                            @foreach ($targetItems as $item)
                                @php
                                    // --- LOGIKA AGREGASI VARIANT ---
                                    $variantDetail = $item['variant_detail'] ?? null;
                                    $variants = [];
                                    $aggregatedVariants = [];

                                    if (isset($variantDetail)) {
                                        if (
                                            is_array($variantDetail) &&
                                            !empty($variantDetail) &&
                                            $variantDetail !== [[]]
                                        ) {
                                            $variants = $variantDetail;
                                        } elseif (is_string($variantDetail)) {
                                            $decoded = json_decode($variantDetail, true);
                                            if (is_array($decoded)) {
                                                $variants = $decoded;
                                            }
                                        }
                                    }

                                    foreach ($variants as $variant) {
                                        $vText = trim($variant['variant'] ?? '');
                                        $vType = trim($variant['typeOrder'] ?? $previewOrderData['typeOrder']);
                                        $key = $vText . '-' . $vType;
                                        $label =
                                            $vText === ''
                                                ? 'TYPE ' . strtoupper($vType)
                                                : strtoupper($vText) . ' (' . $vType . ')';

                                        if (!isset($aggregatedVariants[$key])) {
                                            $aggregatedVariants[$key] = ['label' => $label, 'qty' => 0];
                                        }
                                        $aggregatedVariants[$key]['qty']++;
                                    }
                                @endphp

                                <div style="margin-top:6px;">
                                    <strong>{{ strtoupper($item['product_name']) }}</strong>
                                    <span style="float:right;">x{{ $item['quantity'] }}</span>
                                </div>

                                {{-- Render Variant --}}
                                @if (!empty($aggregatedVariants))
                                    @foreach ($aggregatedVariants as $v)
                                        <div style="padding-left:8px; font-size:11px;">- {{ $v['label'] }}
                                            x{{ $v['qty'] }}</div>
                                    @endforeach
                                @else
                                    <div style="padding-left:8px; font-size:11px;">- TYPE
                                        {{ strtoupper($previewOrderData['typeOrder']) }}</div>
                                @endif

                                @if (isset($item['note']) && $item['note'])
                                    <div style="padding-left:8px; font-size:10px; font-style: italic;">Note:
                                        {{ $item['note'] }}</div>
                                @endif

                                <div>-----------------------------</div>
                            @endforeach
                        @endif

                        {{-- FOOTER --}}
                        <div style="text-align:center; margin-top:10px;">
                            <strong>--- END ORDER ---</strong>
                        </div>
                    </div>
                    <div style="height:1px;"></div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" {{-- ✅ TAMBAHKAN ONCLICK INI --}}
                            onclick="unblurPendingOrdersModal()">Close</button>

                        <button type="button" class="btn btn-primary"
                            onclick="printKOT('{{ $previewOrderData['reference'] }}')">Print</button>
                    </div>
                @else
                    <div class="modal-body text-center text-muted">Reload data...</div>
                @endif
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="orderDetailModal" tabindex="-1" role="dialog"
        aria-labelledby="orderDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="orderDetailModalLabel">Order Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @if ($selectedOrderDetails && $selectedOrderDetails->count() > 0)
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selectedOrderDetails as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ format_currency($item->unit_price) }}</td>
                                            <td>{{ format_currency($item->sub_total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end">
                            <div class="border-top pt-2" style="width: 300px;">
                                {{-- ✅ Subtotal (Sebelum Pajak & Biaya Lain) --}}
                                @php
                                    // Menghitung subtotal murni dari total detail item
                                    $subtotal_items = $selectedOrderDetails->sum('sub_total');
                                @endphp
                                <div class="d-flex justify-content-between py-1 font-weight-bold">
                                    <span>Subtotal</span>
                                    <span>{{ format_currency($subtotal_items) }}</span>
                                </div>
                                {{-- Discount --}}
                                @if (($selectedOrderSummary['discount_amount'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Discount
                                            ({{ $selectedOrderSummary['discount_percentage'] ?? 0 }}%)</span>
                                        <span>(-)
                                            {{ format_currency($selectedOrderSummary['discount_amount'] ?? 0) }}</span>
                                    </div>
                                @endif
                                {{-- Rule Service Charge: Muncul jika fitur Aktif, Dine In, & nilai > 0 --}}
                                @if (isFeatureEnabled('summary_service') &&
                                        ($selectedOrderSummary['order_type'] ?? '') == 'dine_in' &&
                                        ($selectedOrderSummary['service_charge'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Service Charge (5%)</span>
                                        <span>(+)
                                            {{ format_currency($selectedOrderSummary['service_charge'] ?? 0) }}</span>
                                    </div>
                                @endif
                                {{-- Order Tax --}}
                                @if (($selectedOrderSummary['tax_amount'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Order Tax
                                            ({{ $selectedOrderSummary['tax_percentage'] ?? 0 }}%)</span>
                                        <span>(+)
                                            {{ format_currency($selectedOrderSummary['tax_amount'] ?? 0) }}</span>
                                    </div>
                                @endif

                                {{-- Rule Delivery: Muncul jika fitur Aktif & nilai > 0 --}}
                                @if (isFeatureEnabled('summary_pkg') && ($selectedOrderSummary['shipping_amount'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Delivery</span>
                                        <span>(+)
                                            {{ format_currency($selectedOrderSummary['shipping_amount'] ?? 0) }}</span>
                                    </div>
                                @endif

                                {{-- Rule Lain-lain: Muncul jika fitur Others Aktif --}}
                                @if (isFeatureEnabled('summary_others'))
                                    @if (($selectedOrderSummary['lain_a'] ?? 0) > 0)
                                        <div class="d-flex justify-content-between py-1">
                                            <span>Lain-lain A</span>
                                            <span>(+)
                                                {{ format_currency($selectedOrderSummary['lain_a'] ?? 0) }}</span>
                                        </div>
                                    @endif

                                    @if (($selectedOrderSummary['lain_b'] ?? 0) > 0)
                                        <div class="d-flex justify-content-between py-1">
                                            <span>Lain-lain B</span>
                                            <span>(+)
                                                {{ format_currency($selectedOrderSummary['lain_b'] ?? 0) }}</span>
                                        </div>
                                    @endif
                                @endif

                                <hr class="my-1">
                                <div class="d-flex justify-content-between font-weight-bold mt-2">
                                    <span style="font-size: 1.1rem;">Total</span>
                                    <span style="font-size: 1.1rem;" class="text-primary">
                                        {{ format_currency($selectedOrderSummary['total_amount'] ?? 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                            <p class="mt-2">No order details found.</p>
                        </div>
                    @endif
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal Variant -->
    <div wire:ignore.self class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="variantModalLabel">Product Variants</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>

                </div>
                <div class="modal-body">
                    <div id="variantModalContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveVariantData()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal List Variant -->
    <div class="modal fade" id="variantListModal" tabindex="-1" aria-labelledby="variantListLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-3">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="variantListLabel">Select Variant</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="variantListContent">
                    <!-- diisi via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>

                    <button type="button" class="btn btn-success btn-sm" id="applyToAllVariants">Apply for
                        All</button>
                    <button type="button" class="btn btn-primary btn-sm" id="selectVariantConfirm">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal Print Struk -->
    <div class="modal fade" id="printReceiptModal" tabindex="-1" aria-labelledby="printReceiptModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printReceiptModalLabel">Cetak Struk Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="receiptContent">
                        Loading...
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="printButton"><i class="bi bi-printer"></i>
                        Print Struk</button>
                    <!-- 🆕 Tombol Kitchen Order -->
                    <button type="button" class="btn btn-warning" id="kitchenOrderButton">
                        <i class="bi bi-printer"></i> Print Kitchen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal Preview Kitchen Order -->
    <div class="modal fade" id="kitchenOrderModal" tabindex="-1" aria-labelledby="kitchenOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="kitchenOrderModalLabel">
                        Kitchen Order
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div id="kitchenOrderContent">
                        Loading...
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" id="printKitchenButton">
                        <i class="bi bi-printer"></i> Print Kitchen
                    </button>
                </div>

            </div>
        </div>
    </div>

    @if ($showApprovalModal)
        <div class="modal fade show" style="display:block; background: rgba(0,0,0,0.6); z-index: 1050;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title font-weight-bold">
                            <i class="bi bi-shield-lock-fill mr-2"></i> Otorisasi Perubahan Order
                        </h5>
                        <button type="button" class="close" wire:click="$set('showApprovalModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Detail perubahan pada order <strong>#{{ $current_reference }}</strong>:
                        </p>

                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th class="text-left">Item Name</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                        <th class="text-left">System Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items_to_approve as $item)
                                        <tr>
                                            <td class="align-middle px-2">{{ $item['name'] }}</td>
                                            <td class="text-center align-middle font-weight-bold">{{ $item['qty'] }}
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge {{ $item['class'] }} px-2 py-1"
                                                    style="min-width: 50px;">
                                                    {{ $item['type'] }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-muted small px-2">{{ $item['reason'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Admin Username</label>
                                    <input type="text" wire:model.defer="approver_username" class="form-control"
                                        placeholder="Username">
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Admin Password</label>
                                    <input type="password" wire:model.defer="approver_password" class="form-control"
                                        placeholder="••••••••">
                                    @error('approver_password')
                                        <small class="text-danger font-weight-bold">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-primary">Admin Note (Alasan Otorisasi)</label>
                                    <textarea wire:model.defer="approval_note" class="form-control" rows="4"
                                        placeholder="Contoh: Salah input oleh kasir / Permintaan customer..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showApprovalModal', false)">Batal</button>
                        <button type="button" class="btn btn-primary shadow-sm px-4" wire:click="confirmApproval"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="confirmApproval">Konfirmasi Perubahan</span>
                            <span wire:loading wire:target="confirmApproval">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
