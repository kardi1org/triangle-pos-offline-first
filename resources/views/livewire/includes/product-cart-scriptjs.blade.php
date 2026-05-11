<script>
    document.addEventListener('livewire:navigated', () => {
        const livewire = window.Livewire;

        // Saat event auto-hide dipicu dari Livewire
        livewire.on('auto-hide-alert', () => {
            setTimeout(() => {
                const alert = document.getElementById('autoHideAlert');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 500);
                }
            }, 3000);
        });

        // Event baru untuk menghapus alert dari state Livewire
        livewire.on('clear-alert-after', (delay = 3000) => {
            setTimeout(() => {
                livewire.dispatch('clear-alert');
            }, delay);
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 🔹 Tutup modal dan bersihkan backdrop
        window.Livewire.on('close-pending-orders-modal', () => {
            const modal = $('#pendingOrdersModal');
            modal.modal('hide');

            // Tunggu sedikit, lalu bersihkan backdrop & class yang tersisa
            setTimeout(() => {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css({
                    'overflow': 'auto',
                    'padding-right': '0'
                });
            }, 800);
        });

        // 🔹 Tutup modal detail
        window.Livewire.on('close-order-detail-modal', () => {
            const modal = $('#orderDetailModal');
            modal.modal('hide');
            setTimeout(() => {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({
                    'overflow': 'auto',
                    'padding-right': '0'
                });
            }, 800);
        });

        // 🔹 Buka modal detail
        window.Livewire.on('show-order-detail-modal', () => {
            $('#orderDetailModal').modal('show');
        });

        // 🔹 Buka modal list orders (pastikan data tampil)
        window.Livewire.on('show-pending-orders-modal', () => {
            // Bersihkan dulu backdrop
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({
                'overflow': 'auto',
                'padding-right': '0'
            });

            // Tunggu sedikit biar render Livewire selesai, baru buka modal
            setTimeout(() => {
                Livewire.dispatch('reloadPendingOrders'); // 🟢 trigger refresh data
                $('#pendingOrdersModal').modal('show');
            }, 400);
        });

        // 🔹 Manual refresh modal state (bersihkan blur)
        Livewire.on('refresh-modal-state', () => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style = '';
        });
    });
</script>
<script>
    document.addEventListener('livewire:load', function() {
        Livewire.on('variantUpdated', () => {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('overflow', 'auto');
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        let currentInputTarget = null; // Menyimpan input variant aktif

        let variantSession = {};

        function normalizeVariantSession(productId, qty) {
            if (!variantSession[productId]) return;

            // Filter hanya index yang <= qty terbaru
            variantSession[productId] = variantSession[productId]
                .filter(v => v.index <= qty);

            // Re-index ulang jika diperlukan
            variantSession[productId].forEach((v, i) => {
                v.index = v.index; // posisi tetap sesuai order
            });
        }


        // 🔹 Buka modal variant utama

        window.openVariantModal = function(productId, qty, defaultOrderType, productName, variantDetail = '') {

            let variants = []; // Array untuk menampung data varian yang sudah diparse (dari Base64)

            // -----------------------------------------------------------
            // 1. BASE64 DECODE UNTUK DATA ORDER PENDING (Fix SyntaxError)
            // -----------------------------------------------------------
            // Jika input adalah string (Base64 dari order pending), dekode dan parse.
            if (typeof variantDetail === 'string' && variantDetail.trim() !== "") {
                try {
                    // 1. Dekode Base64 string ke JSON string (menggunakan atob)
                    const jsonString = atob(variantDetail);

                    // 2. Parse JSON string ke objek/array Javascript
                    variants = JSON.parse(jsonString);

                    // Pastikan hasil akhirnya adalah array
                    if (!Array.isArray(variants)) {
                        variants = [];
                    }

                    console.log('✅ Varian Berhasil Dimuat dari Base64:', variants);

                } catch (e) {
                    console.error("❌ Error saat dekode/parse JSON varian:", e);
                    variants = [];
                }
            } else {
                // Jika input bukan string (mode normal/tanpa Base64)
                variants = [];
            }

            // 🛑 Baris yang ini tidak perlu lagi karena 'variants' sudah didefinisikan sebagai array
            // if (!Array.isArray(variantDetail)) { variantDetail = []; }

            normalizeVariantSession(productId, qty); // 🟢 Logika Session tetap dipakai

            const modalContent = document.getElementById('variantModalContent');
            modalContent.innerHTML = '';
            document.getElementById('variantModalLabel').innerText = `${productName} - Variants`;
            modalContent.dataset.productId = productId;

            let html = `
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:120px;">Type Order</th>
                    <th>Variant</th>
                </tr>
            </thead>
            <tbody>`;

            for (let i = 1; i <= qty; i++) {

                // -----------------------------------------------------------
                // 2. MEMUAT DATA VARIAN KE DALAM HTML (Fix ReferenceError)
                // -----------------------------------------------------------
                const prefillData = variants[i - 1] || {};

                const prefillVariant = prefillData.variant || '';

                // 🔥 FIX ReferenceError: Deklarasi prefillTypeOrder sebelum digunakan
                const prefillTypeOrder = prefillData.typeOrder || defaultOrderType;

                // Gunakan prefillTypeOrder yang sudah didefinisikan
                const dineActive = prefillTypeOrder === 'dine_in' ? 'btn-primary' : 'btn-outline-primary';
                const takeActive = prefillTypeOrder === 'take_out' ? 'btn-primary' : 'btn-outline-primary';

                // -----------------------------------------------------------

                html += `
        <tr>
            <td class="text-center">${i}</td>
            <td>
                <div class="btn-group btn-group-sm w-100">
                    <button type="button" class="btn ${dineActive} type-btn px-2 py-1" style="width:66px;"
                            data-type="dine_in" data-index="${i}">
                        Dine In
                    </button>
                    <button type="button" class="btn ${takeActive} type-btn px-2 py-1" style="width:66px;"
                            data-type="take_out" data-index="${i}">
                        Take Out
                    </button>
                </div>
            </td>
            <td>
                <div class="input-group input-group-md">
                    <input type="text"
                        class="form-control form-control-sm variant-input rounded-sm"
                        readonly id="variant-input-${i}" value="${prefillVariant}">
                    <button type="button" class="btn btn-outline-secondary btn-sm select-variant-btn ml-1 d-none"
                            data-index="${i}" data-product-id="${productId}">
                        Select
                    </button>
                </div>
            </td>
        </tr>`;
            }

            html += '</tbody></table></div>';
            modalContent.innerHTML = html;

            // ===============================================================================
            // 🛑 PENTING: LOGIKA PENGISIAN ULANG DARI variantDetail (ORDER PENDING)
            //            dan variantSession (MODE NORMAL) TELAH DIHAPUS DARI SINI
            //            karena sudah dihandle di dalam loop 'for' di atas.
            // ===============================================================================

            // 🔹 Toggle type order
            modalContent.querySelectorAll('.type-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const parent = this.closest('tr');
                    parent.querySelectorAll('.type-btn').forEach(b => {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-outline-primary');
                    });
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');
                });
            });

            // 🔹 Tombol select variant → buka list variant
            modalContent.querySelectorAll('.select-variant-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentInputTarget = document.getElementById(
                        `variant-input-${this.dataset.index}`);
                    loadVariantList(this.dataset.productId);
                });
            });

            // 🔹 Klik input variant → buka list variant (efek sama dengan tombol Select)
            modalContent.querySelectorAll('.variant-input').forEach(input => {
                input.addEventListener('click', function() {
                    const index = this.id.split('-').pop();
                    const btn = modalContent.querySelector(
                        `.select-variant-btn[data-index="${index}"]`);
                    if (btn) {
                        currentInputTarget = this;
                        loadVariantList(btn.dataset.productId);
                    }
                });
            });

            $('#variantModal').modal('show');
        };


        // 🔹 Load list variant dari backend (tampil sebagai tombol checklist)
        window.loadVariantList = function(productId) {
            const listContainer = document.getElementById('variantListContent');
            listContainer.innerHTML = '<p class="text-muted small mb-0">Loading...</p>';

            fetch(`/variants/list/${productId}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.length) {
                        listContainer.innerHTML =
                            '<p class="text-muted small mb-0">No variants available.</p>';
                    } else {
                        listContainer.innerHTML = data.map(v => `
        <button type="button"
            class="btn btn-outline-primary btn-sm variant-btn me-2 mb-2"
            data-variant="${v.variant_name}">
            ${v.variant_name}
        </button>
    `).join('');
                    }
                    $('#variantListModal').modal('show');
                })
                .catch(err => {
                    console.error('Error loading variants:', err);
                    listContainer.innerHTML =
                        '<p class="text-danger small">Failed to load variants.</p>';
                });
        };

        // 🔹 Klik tombol variant → toggle aktif seperti checklist
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('variant-btn')) {
                e.target.classList.toggle('active');
            }
        });

        // 🔹 Saat user klik "Select" di modal list variant
        document.getElementById('selectVariantConfirm').addEventListener('click', function() {
            // Ambil semua tombol variant yang aktif
            const selected = Array.from(document.querySelectorAll('.variant-btn.active'))
                .map(el => el.dataset.variant)
                .join(', ');

            // Isi ke input target
            if (currentInputTarget) {
                currentInputTarget.value = selected;
            }

            // Tutup modal
            $('#variantListModal').modal('hide');
        });

        // 🔹 LOGIKA BARU: SELECT FOR ALL (Isi ke semua baris)
        const applyAllBtn = document.getElementById('applyToAllVariants');

        if (applyAllBtn) {
            applyAllBtn.addEventListener('click', function() {
                // 1. Ambil variant yang sedang dipilih (Aktif) di modal list
                // Hasil: "Asin, Manis"
                const selectedText = Array.from(document.querySelectorAll(
                        '#variantListContent .variant-btn.active'))
                    .map(el => el.dataset.variant)
                    .join(', ');

                // 2. Ambil SEMUA elemen input target di Modal Utama
                // Kita cari berdasarkan class '.variant-input' yang ada di dalam #variantModalContent
                const allInputs = document.querySelectorAll('#variantModalContent .variant-input');

                // 3. Loop ke setiap input dan isi valuenya
                allInputs.forEach(input => {
                    input.value = selectedText;
                });

                // 4. Tutup modal list variant
                $('#variantListModal').modal('hide');

                // (Opsional) Reset target input karena kita sudah mengisi semuanya
                currentInputTarget = null;
            });
        }

        // 🔹 Save hasil input variant
        window.saveVariantData = function() {
            const rows = document.querySelectorAll('#variantModalContent tbody tr');
            const productId = document.getElementById('variantModalContent').dataset.productId;

            const variants = Array.from(rows).map((row, i) => {
                const variant = row.querySelector('.variant-input').value.trim();
                const activeTypeBtn = row.querySelector('.type-btn.btn-primary');
                const typeOrder = activeTypeBtn ? activeTypeBtn.dataset.type : 'dine_in';
                return {
                    index: i + 1,
                    variant,
                    typeOrder
                };
            });
            variantSession[productId] = variants;

            console.log('✅ Variant saved:', {
                productId,
                variants
            });
            console.log("KIRIM updateVariant →", productId, variants);

            // Kirim ke Livewire (update cart item)
            Livewire.dispatch('updateVariant', [productId, variants]);

            // Tutup modal
            $('#variantModal').modal('hide');
        };

        window.addEventListener('variant-modal-reset', (e) => {
            try {
                // Ambil productId dari event (Livewire kirim di detail)
                const productId = (e && e.detail && e.detail.productId) ? e.detail.productId : null;

                console.log('variant-modal-reset event received for productId:', productId);

                // 1) Hapus seluruh isi modal variant (ini yang benar)
                // modal content adalah #variantModalContent (bukan #variantBody)
                $('#variantModalContent').html('');

                // 2) Reset current input target agar tidak menunjuk input lama
                currentInputTarget = null;

                // 3) Hapus cache variantSession untuk productId yang dihapus
                if (productId) {
                    if (variantSession[productId]) {
                        delete variantSession[productId];
                        console.log('Cleared variantSession for product', productId);
                    }
                } else {
                    // Jika tidak ada productId, kosongkan seluruh cache (aman)
                    variantSession = {};
                    console.log('Cleared entire variantSession');
                }

                // 4) Reset any temporary JS state used for variant modal
                if (window.selectedVariants) window.selectedVariants = {};
                if (window.variantState) window.variantState = {};
                if (window.variantData) window.variantData = {};
                window.defaultOrderType = 'dine_in';

                // 5) Jika modal sedang terbuka, tutup modal untuk memastikan state bersih
                $('#variantModal').modal('hide');
                $('#variantListModal').modal('hide');

            } catch (err) {
                console.error('Error in variant-modal-reset handler:', err);
            }
        });

        // 🔹 Reset seluruh variant & modal ketika klik Reset Cart
        window.addEventListener('variant-modal-reset-all', () => {
            try {
                console.log('Reset Cart → clearing all variant JS state');

                // 1) Kosongkan isi modal
                $('#variantModalContent').html('');
                $('#variantModal').modal('hide');
                $('#variantListModal').modal('hide');

                // 2) Reset variantSession JS global
                window.variantSession = {};
                variantSession = window.variantSession;

                // 3) Reset input target
                currentInputTarget = null;

                // 4) Reset order type default
                window.defaultOrderType = 'dine_in';

                // 5) Reset variable lain yg dipakai di modal
                if (window.selectedVariants) window.selectedVariants = {};
                if (window.variantState) window.variantState = {};
                if (window.variantData) window.variantData = {};

            } catch (err) {
                console.error('Error in variant-modal-reset-all:', err);
            }
        });

    });
</script>
<script>
    // --- Variabel Global (Pastikan ID modal benar) ---
    const PENDING_ORDERS_MODAL_ID = 'pendingOrdersModal';
    const KITCHEN_PREVIEW_MODAL_ID = 'kitchenPreviewModal';

    // ✅ FUNGSI UNTUK UNBLUR
    function unblurPendingOrdersModal() {
        const pendingOrdersModal = document.getElementById(PENDING_ORDERS_MODAL_ID);
        if (pendingOrdersModal && pendingOrdersModal.classList.contains('is-blurred')) {
            pendingOrdersModal.classList.remove('is-blurred');
            // Jika ada event listener yang terlewat, ini menjamin unblur.
        }
    }

    // --- 1. LIVEWIRE LISTENERS (Tetap) ---
    document.addEventListener('livewire:initialized', () => {
        // Event saat Print Preview Dapur dibuka
        Livewire.on('show-kitchen-preview', () => {
            // Tampilkan modal preview
            $('#' + KITCHEN_PREVIEW_MODAL_ID).modal('show');

            // BLUR: Terapkan kelas 'is-blurred'
            const pendingOrdersModal = document.getElementById(PENDING_ORDERS_MODAL_ID);
            if (pendingOrdersModal) {
                pendingOrdersModal.classList.add('is-blurred');
            }
        });

        // Event saat List Order dibuka (tetap normal)
        Livewire.on('show-pending-orders-modal', () => {
            $('#' + PENDING_ORDERS_MODAL_ID).modal('show');
        });

        // Menangkap event 'show-prebill-preview' dari Livewire
        Livewire.on('show-prebill-preview', (event) => {

            // LOGIKA PERBAIKAN:
            // Di Livewire 3, data biasanya ada di dalam array pertama jika dikirim
            // dengan dispatch('event', {key: value})

            const data = Array.isArray(event) ? event[0] : event;
            const ref = data.reference;

            console.log("Menerima Reference:", ref); // Debugging

            if (ref) {
                if (typeof loadAndShowPreBillModal === "function") {
                    loadAndShowPreBillModal(ref);
                } else {
                    console.error("Fungsi loadAndShowPreBillModal tidak ditemukan!");
                }
            } else {
                console.error("Data reference kosong!");
            }
        });
    });

    // --- 2. BOOTSTRAP EVENT LISTENERS (Dikompromikan/Dihapus) ---
    // Kami mengabaikan listener hidden.bs.modal karena tidak konsisten.
    // Jika masih ada di kode Anda, hapuslah.

    // Jika Anda menangkap event alert
    window.addEventListener('alert', event => {
        // Di Livewire 3, data ada di event.detail (tanpa .[0] jika menggunakan named parameters)
        // Atau bisa langsung menggunakan event.detail saja
        alert(event.detail.message);
    });

    // Jika Anda menangkap event buka modal
    window.addEventListener('openKitchenPreviewModal', event => {
        $('#kitchenPreviewModal').modal('show');
    });

    // --- 3. FUNGSI PRINT KOT YANG DIJAMIN UNBLUR ---
    function printKOT(reference) {
        const printArea = document.getElementById('print-area');
        if (!printArea) return;

        // --- AWAL TAMBAHAN UPDATE DATABASE ---
        // Logika Update Database
        if (reference) {
            console.log("Mengirim permintaan update untuk Ref: " + reference);

            fetch(`/pos/update-print-status/${reference}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("✅ Database Updated: is_printed = 1");
                    } else {
                        console.error("❌ Gagal Update:", data.message);
                    }
                })
                .catch(error => console.error('❌ Network Error:', error));
        } else {
            console.warn("⚠️ Reference tidak ditemukan, update database dilewati.");
        }
        // --- AKHIR TAMBAHAN UPDATE DATABASE ---

        const win = window.open('', 'PRINT_KOT', 'width=400,height=600');

        win.document.write(`
                            <html>
                            <head>
                                <title>Kitchen Order</title>
                                <style>
                                    @page {
                                        size: 58mm auto; /* Memaksa ukuran kertas thermal */
                                        margin: 0;
                                    }
                                    html, body {
                                        margin: 0;
                                        padding: 0;
                                        font-family: monospace;
                                        /* Disamakan dengan font struk penjualan agar seragam */
                                        font-size: 11px;
                                        line-height: 1.2;
                                        background-color: #fff;
                                        color: #000;
                                    }
                                    #print-area {
                                        /* Menggunakan 48mm sebagai area cetak aman agar kanan tidak terpotong */
                                        width: 48mm;
                                        padding: 10px 0 10px 2mm; /* Ada jarak aman dari pinggir kiri */
                                        box-sizing: border-box;
                                    }
                                    /* Memastikan tabel di dalam print-area tidak meluber */
                                    table {
                                        width: 100%;
                                        border-collapse: collapse;
                                        table-layout: fixed;
                                    }
                                    td {
                                        word-wrap: break-word;
                                        vertical-align: top;
                                    }
                                    hr {
                                        border: 0;
                                        border-top: 1px dashed #000;
                                        margin: 5px 0;
                                    }
                                    .center { text-align: center; }
                                    .text-right { text-align: right; }

                                    /* Tambahan CSS agar tampilan teks tebal lebih jelas */
                                    strong { font-weight: bold; }
                                </style>
                            </head>

                            <body onload="window.print(); setTimeout(() => window.close(), 500);">
                                <div id="print-area">
                                    ${printArea.innerHTML}
                                </div>
                            </body>
                            </html>
                        `);

        win.document.close();

        // rapikan UI utama
        unblurPendingOrdersModal();
        $('#kitchenPreviewModal').modal('hide');
    }

    function printPreBill() {
        const content = document.getElementById('prebill-content').innerHTML;
        if (!content) {
            alert("Konten tidak ditemukan!");
            return;
        }

        const win = window.open('', 'PRINT_PREBILL', 'width=450,height=600');

        win.document.write(`
        <html>
        <head>
            <title>Print Pre-Bill</title>
            <style>
                @page { size: 58mm auto; margin: 0; }
                body {
                    font-family: monospace;
                    font-size: 12px; /* Sedikit diperbesar agar mudah dibaca */
                    line-height: 1.2;
                    margin: 0;
                    padding: 0;
                    width: 48mm;
                    background-color: #fff;
                    color: #000;
                }
                .prebill-container {
                    width: 100%;
                    padding: 5px 0 10px 2mm;
                    box-sizing: border-box;
                }
                .center { text-align: center; }
                .text-right { text-align: right; }
                .font-bold { font-weight: bold; }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed; /* Mengunci lebar kolom */
                }
                td {
                    padding: 1px 0;
                    vertical-align: top;
                    overflow: hidden;
                }
                .divider {
                    border-top: 1px dashed #000;
                    margin: 5px 0;
                }

                /* Pengaturan Lebar Kolom agar tidak pecah seperti di gambar */
                .col-label { width: 30%; }
                .col-separator { width: 5%; }
                .col-value { width: 65%; }

                .col-desc { width: 50%; }
                .col-qty-price { width: 50%; }

                .title-prebill {
                    font-size: 14px;
                    font-weight: bold;
                    margin-bottom: 2px;
                    display: block;
                }
            </style>
        </head>
        <body onload="window.print(); setTimeout(() => { window.close(); }, 500);">
            <div class="prebill-container">
                ${content}
            </div>
        </body>
        </html>
    `);

        win.document.close();
    }
    // ✅ FUNGSI UNTUK MENGAMBIL DATA DAN MENAMPILKAN MODAL PRE-BILL
    function loadAndShowPreBillModal(reference) {
        console.log("Memuat Pre-Bill untuk Ref: " + reference);

        // Ganti URL ini sesuai dengan route Laravel Anda yang menangani preview pre-bill
        // Contoh: /pos/prebill-preview/{reference}
        fetch(`/pos/prebill-preview/${reference}`)
            .then(response => response.text())
            .then(html => {
                // 1. Masukkan konten HTML ke dalam area konten modal pre-bill Anda
                // Pastikan Anda punya elemen dengan ID 'prebill-content' di dalam modal
                const contentArea = document.getElementById('prebill-content');
                if (contentArea) {
                    contentArea.innerHTML = html;
                }

                // 2. Tampilkan Modal Pre-Bill
                // Pastikan ID modal sesuai, misal: 'preBillModal'
                $('#preBillModal').modal('show');

                // 3. Pastikan UI lain rapi (unblur jika perlu)
                unblurPendingOrdersModal();
            })
            .catch(error => {
                console.error('Gagal memuat preview pre-bill:', error);
                alert('Gagal memuat data pre-bill.');
            });
    }
</script>

<script>
    // Ini memastikan backdrop (blur layer) dihapus jika tertinggal
    $('#tableSelectionModal').on('hidden.bs.modal', function() {
        if ($('.modal-backdrop').length) {
            $('.modal-backdrop').remove();
        }
        // Pastikan class 'modal-open' juga dihapus dari body
        if ($('body').hasClass('modal-open')) {
            $('body').removeClass('modal-open');
        }
    });
</script>

<script>
    $(document).ready(function() {

        const saleReference = "{{ session('showPrintModal') }}";

        if (saleReference) {
            loadAndShowPrintModal(saleReference);
        }

        /* ============================
         * STRUK (SUDAH ADA - TETAP)
         * ============================ */
        function loadAndShowPrintModal(reference) {
            const printUrl = `/app/pos/sales/print/${reference}`;

            $('#receiptContent').html('<div class="text-center">Memuat Struk...</div>');

            const iframeHtml = `
            <iframe
                src="${printUrl}?modal=true"
                style="width: 100%; height: 400px; border: none;"
                id="receiptIframe">
            </iframe>
        `;

            $('#receiptContent').html(iframeHtml);
            $('#printReceiptModal').modal('show');
        }

        $('#printButton').on('click', function() {
            const iframe = document.getElementById('receiptIframe');
            if (iframe) {
                iframe.contentWindow.print();
            } else {
                alert('Struk belum termuat.');
            }
        });

        /* ============================
         * 🆕 KITCHEN ORDER
         * ============================ */

        $('#kitchenOrderButton').on('click', function() {
            if (!saleReference) {
                alert('Order belum tersedia.');
                return;
            }
            loadAndShowKitchenModal(saleReference);
        });

        function loadAndShowKitchenModal(reference) {
            const kitchenUrl = `/app/pos/sales/print-kitchen/${reference}`;

            $('#kitchenOrderContent').html('<div class="text-center">Memuat Kitchen Order...</div>');

            const iframeHtml = `
            <iframe
                src="${kitchenUrl}?modal=true"
                style="width: 100%; height: 400px; border: none;"
                id="kitchenIframe">
            </iframe>
        `;

            $('#kitchenOrderContent').html(iframeHtml);
            $('#kitchenOrderModal').modal('show');
        }

        $('#printKitchenButton').on('click', function() {
            const iframe = document.getElementById('kitchenIframe');
            if (iframe) {
                iframe.contentWindow.print();
            } else {
                alert('Kitchen order belum termuat.');
            }
        });

    });
</script>
