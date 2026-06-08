@extends('layouts.app')

@section('title', 'Laporan Laba Rugi Detail')

<style>
    /* 🎯 1. MEMBERI MARGIN PADA TANDA PANAH (JARAK AMAN DARI SIDEBAR) */
    body.c-app .c-main {
        padding-left: 15px !important;
        /* Menghilangkan efek terlalu mepet menu */
        padding-right: 15px !important;
        /* Keseimbangan jarak kanan */
        padding-top: 1.5rem !important;
    }

    body.c-app .container-fluid {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }

    /* 🎯 2. MEMPERBAGUS STRUKTUR DAN LEBAR KOLOM TABEL */
    .table-responsive-custom {
        max-height: 70vh;
        overflow-y: auto;
        overflow-x: auto;
        /* Aktifkan scroll horizontal jika outlet penuh */
        border: 1px solid #c8ced3;
        border-radius: 4px;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.05);
    }

    .table-custom-matrix {
        table-layout: fixed;
        /* Mengunci lebar kolom agar konsisten di setiap page */
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    /* Penataan Lebar Kolom yang Ideal & Konsisten */
    .table-custom-matrix th.col-keterangan,
    .table-custom-matrix td.col-keterangan {
        width: 250px;
        /* Lebar tetap untuk kolom teks Keterangan */
        min-width: 250px;
        position: sticky;
        left: 0;
        z-index: 4;
        text-align: left;
        padding-left: 12px !important;
        border-right: 2px solid #a4b7c1 !important;
    }

    .table-custom-matrix th.col-outlet,
    .table-custom-matrix td.col-outlet {
        width: 120px;
        /* Lebar kolom outlet seragam, tidak akan mengecil/menumpuk */
        min-width: 120px;
        text-align: right;
        padding-right: 12px !important;
    }

    .table-custom-matrix th.col-subtotal,
    .table-custom-matrix td.col-subtotal {
        width: 160px;
        /* Lebar tetap untuk Sub Total */
        min-width: 160px;
        position: sticky;
        right: 0;
        z-index: 4;
        text-align: right;
        padding-right: 12px !important;
        border-left: 2px solid #a4b7c1 !important;
    }

    /* 🎯 3. FIXING SCROLL & KEBOCORAN WARNA (STICKY HEADER) */
    .table-custom-matrix thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background-color: #2f353a !important;
        /* Warna Gelap Premium CoreUI */
        color: #fff;
        height: 40px;
        vertical-align: middle !important;
        border: 1px solid #2f353a !important;
    }

    /* Titik silang antara sticky header dan sticky column */
    .table-custom-matrix thead th.col-keterangan {
        z-index: 6;
        left: 0;
    }

    .table-custom-matrix thead th.col-subtotal {
        z-index: 6;
        right: 0;
    }

    /* Background Solid untuk baris Data agar tidak transparan saat ditimpa scroll */
    .bg-white-sticky,
    .table-custom-matrix tr.bg-white-sticky td {
        background-color: #ffffff !important;
    }

    .bg-light-sticky,
    .table-custom-matrix tr.bg-light-sticky td {
        background-color: #f0f3f5 !important;
    }

    /* Efek Hover yang Cantik */
    .table-custom-matrix tbody tr:hover td {
        background-color: #f1f4f6 !important;
    }

    /* Perbaikan Khusus Baris Sekat Kosong */
    .row-sekat {
        background-color: #e4e7ea !important;
    }
</style>

@section('content')
    <div class="container-fluid">

        {{-- FORM FILTER TANGGAL --}}
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <form action="{{ request()->url() }}" method="GET">
                    <div class="form-row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label for="start_date" class="font-weight-bold">Tanggal Mulai</label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                    value="{{ $startDate }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label for="end_date" class="font-weight-bold">Tanggal Akhir</label>
                                <input type="date" id="end_date" name="end_date" class="form-control"
                                    value="{{ $endDate }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary px-4">Filter <i
                                        class="bi bi-filter"></i></button>
                                <a href="{{ request()->url() }}" class="btn btn-secondary px-3">Reset <i
                                        class="bi bi-arrow-clockwise"></i></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @php
            $perPage = 5;
            $currentPage = request('page_outlet', 1);

            $outletCollection = collect($outlets);
            $totalOutlets = $outletCollection->count();
            $totalPages = ceil($totalOutlets / $perPage);

            $paginatedOutlets = $outletCollection->forPage($currentPage, $perPage);
        @endphp

        {{-- KARTU DATA LAPORAN --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3"
                style="border-bottom: 1px solid #dee2e6;">
                <div>
                    <h5 class="m-0 font-weight-bold text-dark">Laporan Laba Rugi Detail</h5>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</small>
                </div>

                @if ($totalPages > 1)
                    <div class="btn-group">
                        <a href="{{ request()->fullUrlWithQuery(['page_outlet' => max(1, $currentPage - 1)]) }}"
                            class="btn btn-sm btn-outline-primary {{ $currentPage == 1 ? 'disabled' : '' }}">
                            <i class="bi bi-chevron-left"></i> Prev 5 Outlet
                        </a>
                        <span class="btn btn-sm btn-light disabled text-dark font-weight-bold px-3">
                            {{ $currentPage }} / {{ $totalPages }}
                        </span>
                        <a href="{{ request()->fullUrlWithQuery(['page_outlet' => min($totalPages, $currentPage + 1)]) }}"
                            class="btn btn-sm btn-outline-primary {{ $currentPage == $totalPages ? 'disabled' : '' }}">
                            Next 5 Outlet <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="table-responsive-custom">
                    <table class="table table-bordered table-sm table-custom-matrix m-0">
                        <thead>
                            <tr>
                                <th class="align-middle col-keterangan">Keterangan</th>
                                @foreach ($paginatedOutlets as $outlet)
                                    <th class="align-middle text-center col-outlet">{{ $outlet->name }}</th>
                                @endforeach
                                <th class="align-middle text-center col-subtotal">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grandTotalSales = 0;
                                $grandTotalCost = 0;
                                $outletLabaKotor = [];
                                $totalPerOutletExpense = [];
                                $grandTotalExpenses = 0;

                                foreach ($outlets as $ol) {
                                    $sales = $salesData[$ol->id] ?? 0;
                                    $grandTotalSales += $sales;

                                    $cost = $costData[$ol->id] ?? 0;
                                    $grandTotalCost += $cost;

                                    $labaKotor = $sales - $cost;
                                    $outletLabaKotor[$ol->id] = $labaKotor;

                                    foreach ($expenseCategories as $category) {
                                        $empAmt = $expenses[$category->id][$ol->id] ?? 0;
                                        $totalPerOutletExpense[$ol->id] =
                                            ($totalPerOutletExpense[$ol->id] ?? 0) + $empAmt;
                                        $grandTotalExpenses += $empAmt;
                                    }
                                }
                                $globalLabaBersih = $grandTotalSales - $grandTotalCost - $grandTotalExpenses;
                            @endphp

                            {{-- BARIS PENJUALAN --}}
                            <tr class="bg-white-sticky">
                                <td class="font-weight-bold col-keterangan" style="color: #23282c !important;">Penjualan
                                </td>
                                @foreach ($paginatedOutlets as $outlet)
                                    {{-- 🎯 PAKSA WARNA HITAM PEKAT DENGAN INLINE STYLE --}}
                                    <td class="col-outlet text-right font-weight-bold" style="color: #23282c !important;">
                                        {{ format_currency($salesData[$outlet->id] ?? 0) }}
                                    </td>
                                @endforeach
                                <td class="text-right font-weight-bold text-primary col-subtotal">
                                    {{ format_currency($grandTotalSales) }}
                                </td>
                            </tr>

                            {{-- BARIS HPP --}}
                            <tr class="bg-white-sticky">
                                <td class="font-weight-bold text-danger col-keterangan">HPP</td>
                                @foreach ($paginatedOutlets as $outlet)
                                    <td class="col-outlet text-right text-danger">
                                        {{ format_currency($costData[$outlet->id] ?? 0) }}</td>
                                @endforeach
                                <td class="text-right font-weight-bold text-danger col-subtotal">
                                    {{ format_currency($grandTotalCost) }}</td>
                            </tr>

                            {{-- BARIS LABA KOTOR --}}
                            {{-- 🎯 Menggunakan text-custom-dark agar huruf tidak berwarna putih --}}
                            <tr class="table-warning font-weight-bold text-custom-dark"
                                style="background-color: #feefc3 !important;">
                                <td class="col-keterangan">Laba Kotor</td>
                                @foreach ($paginatedOutlets as $outlet)
                                    <td class="col-outlet text-right">
                                        {{ format_currency($outletLabaKotor[$outlet->id] ?? 0) }}</td>
                                @endforeach
                                <td class="text-right col-subtotal" style="background-color: #feefc3 !important;">
                                    {{ format_currency($grandTotalSales - $grandTotalCost) }}</td>
                            </tr>

                            {{-- SEKAT KOSONG --}}
                            <tr class="row-sekat">
                                <td colspan="{{ $paginatedOutlets->count() + 2 }}" style="height: 12px; border: none;">
                                </td>
                            </tr>

                            {{-- BIAYA OPERASIONAL LOKAL OUTLET --}}
                            @foreach ($expenseCategories as $category)
                                <tr class="bg-white-sticky">
                                    <td class="col-keterangan" style="padding-left: 20px !important; color: #5c6873;">
                                        {{ $category->category_name }}</td>
                                    @php $globalRowExpenseTotal = 0; @endphp

                                    @foreach ($outlets as $ol)
                                        @php $globalRowExpenseTotal += ($expenses[$category->id][$ol->id] ?? 0); @endphp
                                    @endforeach

                                    @foreach ($paginatedOutlets as $outlet)
                                        @php $empAmt = $expenses[$category->id][$outlet->id] ?? 0; @endphp
                                        <td class="col-outlet text-right text-muted">
                                            {{ $empAmt > 0 ? format_currency($empAmt) : '-' }}
                                        </td>
                                    @endforeach
                                    <td class="text-right font-weight-bold text-muted col-subtotal">
                                        {{ format_currency($globalRowExpenseTotal) }}</td>
                                </tr>
                            @endforeach

                            {{-- BARIS LABA KOTOR / OUTLET --}}
                            {{-- 🎯 Menggunakan text-custom-dark agar huruf tidak berwarna putih --}}
                            <tr class="table-info font-weight-bold text-custom-dark"
                                style="background-color: #d1ecf1 !important;">
                                <td class="col-keterangan">Laba Kotor / Outlet</td>
                                @foreach ($paginatedOutlets as $outlet)
                                    @php
                                        $labaKotorOutlet =
                                            ($outletLabaKotor[$outlet->id] ?? 0) -
                                            ($totalPerOutletExpense[$outlet->id] ?? 0);
                                    @endphp
                                    <td class="col-outlet text-right">{{ format_currency($labaKotorOutlet) }}</td>
                                @endforeach
                                <td class="text-right col-subtotal" style="background-color: #d1ecf1 !important;">
                                    {{ format_currency($grandTotalSales - $grandTotalCost - $grandTotalExpenses) }}</td>
                            </tr>

                            {{-- SEKAT KOSONG --}}
                            <tr class="row-sekat">
                                <td colspan="{{ $paginatedOutlets->count() + 2 }}" style="height: 12px; border: none;">
                                </td>
                            </tr>

                            {{-- BARIS LABA BERSIH --}}
                            {{-- 🎯 Menggunakan text-custom-dark agar huruf tidak berwarna putih --}}
                            <tr class="table-success font-weight-bold text-custom-dark"
                                style="font-size: 1.05rem; background-color: #d4edda !important;">
                                <td class="col-keterangan">Laba Bersih</td>
                                @foreach ($paginatedOutlets as $outlet)
                                    <td class="col-outlet"></td>
                                @endforeach
                                <td class="text-right col-subtotal" style="background-color: #d4edda !important;">
                                    {{ format_currency($globalLabaBersih) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
