<style>
    .cart-list {
        border-radius: 6px;
        overflow: hidden;
        background-color: #fff;
    }

    .cart-item {
        margin-bottom: 0;
        transition: background 0.15s ease-in-out;
    }

    .cart-item:hover {
        background: #faf8fa;
    }

    /* ===== Versi Desktop ===== */
    @media (min-width: 768px) {

        /* Geser quantity lebih ke kanan */
        .cart-item .quantity-section {
            transform: translateX(20px);
        }

        /* Geser tombol action sedikit ke kiri */
        .cart-item .action-section {
            transform: translateX(-10px);
        }
    }

    /* ===== Versi Mobile ===== */
    @media (max-width: 767.98px) {

        .cart-item .col-12,
        .cart-item .col-6 {
            text-align: left !important;
        }

        .cart-item {
            padding-left: 10px;
            padding-right: 10px;
        }

        /* Quantity & Action sejajar di HP */
        .cart-item .col-6.d-flex {
            justify-content: space-between;
            align-items: center;
        }

        .cart-item .action-section {
            margin-left: 10px;
            transform: none;
            /* posisi normal di HP */
        }

        .cart-item .quantity-section {
            transform: none;
            /* posisi normal di HP */
        }
    }

    /* Lebarkan input quantity */
    .cart-item input[type="number"] {
        min-width: 60px;
        max-width: 100px;
        text-align: center;
    }

    .cart-item .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
<style>
    .modal.is-blurred .modal-dialog {
        /* Filter blur hanya diterapkan pada dialog */
        filter: blur(4px);
        pointer-events: none;
    }
</style>

<style>
    .table-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        padding: 10px;
    }

    .table-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }

    /* Style untuk Meja yang terpilih */
    .table-card.selected {
        border: 3px solid #007bff;
        /* Warna biru untuk yang terpilih */
        background-color: #e9f5ff;
    }

    /* Style berdasarkan Status */
    .table-card.available {
        background-color: #e6ffed;
        /* Hijau muda */
        border-color: #28a745;
    }

    .table-card.occupied {
        background-color: #fff0f0;
        /* Merah muda */
        border-color: #dc3545;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .table-card.cleaning {
        background-color: #fffbe6;
        /* Kuning muda */
        border-color: #ffc107;
    }

    .table-card h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .table-status {
        margin-top: 10px;
        font-size: 0.9rem;
    }

    .selection-overlay {
        position: absolute;
        top: 5px;
        right: 5px;
        color: #007bff;
        font-size: 1.5rem;
    }
</style>
