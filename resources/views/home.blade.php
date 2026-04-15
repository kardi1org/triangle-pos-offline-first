@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .nav-pills-custom {
            background-color: #f8f9fc;
            /* Abu-abu sangat muda untuk container */
            border-color: #e3e6f0 !important;
        }

        .nav-pills-custom .nav-link {
            color: #858796;
            /* Warna teks saat tidak aktif */
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        /* Styling saat tab Aktif */
        .nav-pills-custom .nav-link.active {
            background-color: #ffffff !important;
            /* Latar putih agar terlihat elevated */
            color: #4e73df !important;
            /* Warna biru primary Laravel/Bootstrap */
            border-color: #e3e6f0 !important;
            box-shadow: 0 0.125rem 0.25rem rgba(238, 236, 236, 0.075) !important;
        }

        /* Efek hover untuk tab yang tidak aktif */
        .nav-pills-custom .nav-link:not(.active):hover {
            background-color: #eaecf4;
            color: #4e73df;
        }

        .hover-bg-light:hover {
            background-color: #f8fafc;
            cursor: default;
        }

        .text-orange {
            color: #fd7e14;
        }
    </style>
    <div class="container-fluid pb-5" style="background-color: #f8f9fc;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0 mt-2 text-dark">Dashboard Overview</h3>
                <p class="text-muted small">Welcome back, {{ auth()->user()->name }}!</p>
            </div>
            <div class="badge bg-white text-dark p-2 px-3 shadow-sm border rounded-3 d-flex align-items-center">

                <span class="fw-semibold">{{ now()->format('d F Y') }}</span>
            </div>
        </div>
        @if (auth()->user()->level == 'admin' || auth()->user()->level == 'owner')
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.7rem;">Best
                                        Performing Outlet</p>
                                    <h5 class="fw-bold mb-1">{{ $highPerformers->first()['name'] ?? 'N/A' }}</h5>
                                    <span class="text-success small fw-bold">
                                        Rp {{ number_format($highPerformers->first()['total'] ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                <i class="bi bi-trophy text-primary opacity-10" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.7rem;">Sales
                                        Growth</p>
                                    <h5 class="fw-bold mb-1">{{ number_format($salesGrowth, 1) }}%</h5>
                                    <span class="{{ $salesGrowth >= 0 ? 'text-success' : 'text-danger' }} small fw-bold">
                                        {{ $salesGrowth >= 0 ? '+' : '' }}{{ number_format($salesGrowth, 1) }}% MoM
                                        <i
                                            class="bi {{ $salesGrowth >= 0 ? 'bi-arrow-up-short' : 'bi-arrow-down-short' }}"></i>
                                    </span>
                                </div>
                                <i class="bi bi-speedometer2 text-primary opacity-10" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <p class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.7rem;">Profit Margin
                            </p>
                            <h4 class="fw-bold mb-2">{{ number_format($profitMargin, 1) }}%</h4>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $profitMargin }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <p class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.7rem;">Total Revenue
                            </p>
                            <h4 class="fw-bold mb-1">Rp {{ number_format($totalAllOutlets, 0, ',', '.') }}</h4>
                            <span class="text-muted small">All-time record</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4 d-flex align-items-stretch">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4">Sales Performance (7 Days)</h6>
                            <div style="height: 300px;"><canvas id="mainSalesChart"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 text-center">
                            <h6 class="fw-bold mb-4">Revenue Sources</h6>
                            <div style="height: 250px;"><canvas id="distributionChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div
                            class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0">Outlet Performance Analytics</h6>
                                <small class="text-muted">Total Sales: <strong>Rp
                                        {{ number_format($totalAllOutlets, 0, ',', '.') }}</strong></small>
                            </div>
                            <ul class="nav nav-pills nav-pills-custom bg-light p-1 rounded-3 border" id="performanceTab"
                                role="tablist" style="width: fit-content;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active rounded-2 py-1 px-3 fw-bold small" id="high-tab"
                                        data-bs-toggle="pill" data-bs-target="#high-perf" type="button" role="tab"
                                        aria-controls="high-perf" aria-selected="true">
                                        Top High
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-2 py-1 px-3 fw-bold small text-muted" id="low-tab"
                                        data-bs-toggle="pill" data-bs-target="#low-perf" type="button" role="tab"
                                        aria-controls="low-perf" aria-selected="false">
                                        Top Low
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            <div class="tab-content" id="performanceTabContent">
                                <div class="tab-pane fade show active" id="high-perf" role="tabpanel"
                                    aria-labelledby="high-tab">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-borderless">
                                            <tbody>
                                                @foreach ($highPerformers as $item)
                                                    <tr>
                                                        <td style="width: 40px;"><i
                                                                class="bi bi-graph-up text-success"></i>
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold small">{{ $item['name'] }}</div>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="fw-bold small">Rp
                                                                {{ number_format($item['total'], 0, ',', '.') }}</div>
                                                            <div class="progress mt-1" style="height: 4px;">
                                                                <div class="progress-bar bg-success"
                                                                    style="width: {{ $totalAllOutlets > 0 ? ($item['total'] / $totalAllOutlets) * 100 : 0 }}%">
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="low-perf" role="tabpanel" aria-labelledby="low-tab">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-borderless">
                                            <tbody>
                                                @foreach ($lowPerformers as $item)
                                                    <tr>
                                                        <td style="width: 40px;"><i
                                                                class="bi bi-graph-down text-danger"></i></td>
                                                        <td>
                                                            <div class="fw-bold small">{{ $item['name'] }}</div>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="fw-bold small">Rp
                                                                {{ number_format($item['total'], 0, ',', '.') }}</div>
                                                            <div class="progress mt-1" style="height: 4px;">
                                                                <div class="progress-bar bg-danger"
                                                                    style="width: {{ $totalAllOutlets > 0 ? ($item['total'] / $totalAllOutlets) * 100 : 0 }}%">
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-primary text-white border-0 py-3 px-4 rounded-bottom-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small opacity-75">Consolidated Revenue</span>
                                <span class="fw-bold">Rp {{ number_format($totalAllOutlets, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold">Top Products</h6>
                        </div>
                        <div class="card-body px-4">
                            @foreach ($topProducts as $product)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-light rounded-circle p-1"
                                        style="width: 45px; height: 45px; flex-shrink: 0;">
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                                            class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div class="ms-3 overflow-hidden">
                                        <div class="fw-bold text-truncate small" title="{{ $product['name'] }}">
                                            {{ $product['name'] }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $product['qty'] }} Sold
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4" style="color: #1a202c;">Outlet Performance Metrics</h5>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr class="text-muted small fw-bold text-uppercase"
                                            style="letter-spacing: 0.5px; background-color: #f8fafc;">
                                            <th class="border-0 ps-3 py-3">Outlet Name</th>
                                            <th class="border-0 py-3 text-center">Gross Margin</th>
                                            <th class="border-0 py-3 text-center">Avg Transaction</th>
                                            <th class="border-0 py-3 text-center">Growth (MoM)</th>
                                            <th class="border-0 pe-3 py-3 text-end">Total Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($highPerformers as $item)
                                            <tr>
                                                <td class="ps-3 py-3">
                                                    <div class="fw-bold" style="color: #2d3748;">{{ $item['name'] }}
                                                    </div>
                                                    <div class="text-muted small">{{ $item['location'] }}</div>
                                                </td>
                                                <td class="text-center py-3">
                                                    <span class="fw-bold text-dark">{{ $item['margin'] }}%</span>
                                                </td>
                                                <td class="text-center py-3 fw-bold">
                                                    Rp {{ number_format($item['avg'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center py-3">
                                                    <span
                                                        class="badge {{ $item['growth'] >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2 rounded-pill fw-bold">
                                                        {{ $item['growth'] >= 0 ? '+' : '' }}{{ number_format($item['growth'], 1) }}%
                                                    </span>
                                                </td>
                                                <td class="pe-3 py-3 text-end fw-bold text-primary">
                                                    Rp {{ number_format($item['total'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                /* Custom Styling untuk menyamai Screenshot */
                .bg-success-subtle {
                    background-color: #e6fcf5 !important;
                }

                .table thead th {
                    font-size: 0.7rem;
                    color: #94a3b8 !important;
                }

                .table tbody td {
                    border-bottom: 1px solid #f1f5f9;
                    font-size: 0.9rem;
                }

                .card-header h6 {
                    font-size: 1.1rem;
                    color: #1e293b;
                }
            </style>
        @elseif (auth()->user()->level == 'manager')
            {{-- Row 1: Summary Cards --}}
            <div class="row g-3 mb-4">
                @php
                    $cards = [
                        [
                            'title' => 'Total Sales',
                            'val' => $sales,
                            'icon' => 'bi-cart-check',
                            'color' => '#0d6efd',
                            'bg' => 'rgba(13, 110, 253, 0.1)',
                        ],
                        [
                            'title' => 'Total Purchase',
                            'val' => $purchases,
                            'icon' => 'bi-bag-dash',
                            'color' => '#0dcaf0',
                            'bg' => 'rgba(13, 202, 240, 0.1)',
                        ],
                        [
                            'title' => 'Expenses',
                            'val' => $expenses,
                            'icon' => 'bi-wallet2',
                            'color' => '#dc3545',
                            'bg' => 'rgba(220, 53, 69, 0.1)',
                        ],
                        [
                            'title' => 'Net Profit',
                            'val' => $profit,
                            'icon' => 'bi-graph-up-arrow',
                            'color' => '#198754',
                            'bg' => 'rgba(25, 135, 84, 0.1)',
                        ],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold text-uppercase mb-1"
                                            style="font-size: 0.75rem;">
                                            {{ $card['title'] }}
                                        </p>
                                        <h4 class="fw-bold mb-0" style="color: #2d3748;">
                                            Rp {{ number_format($card['val'], 0, ',', '.') }}
                                        </h4>
                                    </div>
                                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px; background-color: {{ $card['bg'] }};">
                                        <i class="bi {{ $card['icon'] }}"
                                            style="color: {{ $card['color'] }}; font-size: 1.3rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Row 2: Sales Chart & Top Products --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4">Sales Performance (7 Days)</h6>
                            <div style="height: 300px;"><canvas id="mainSalesChart"></canvas></div>
                        </div>
                    </div>
                </div>

                {{-- Sisi Kanan: Top Products menggantikan Revenue Sources --}}
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div
                            class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Top Products</h6>
                            <i class="bi bi-fire text-orange" style="color: #f6ad55;"></i>
                        </div>
                        <div class="card-body px-4 pt-2">
                            @forelse ($topProducts as $index => $product)
                                <div class="d-flex align-items-center mb-2 p-2 rounded-3 hover-bg-light"
                                    style="transition: all 0.2s;">

                                    {{-- Badge Urutan - Diberi fixed width agar sejajar --}}
                                    <div class="text-muted small fw-bold" style="width: 25px; flex-shrink: 0;">
                                        {{ $index + 1 }}
                                    </div>

                                    {{-- Foto Produk --}}
                                    <div class="bg-light rounded-3 overflow-hidden"
                                        style="width: 45px; height: 45px; flex-shrink: 0; border: 1px solid #edf2f7;">
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>

                                    {{-- Info Produk - Menggunakan margin-left inline agar pasti ada jarak --}}
                                    <div class="flex-grow-1 overflow-hidden" style="margin-left: 16px;">
                                        <div class="fw-bold text-truncate"
                                            style="font-size: 0.9rem; color: #2d3748; line-height: 1.2;"
                                            title="{{ $product['name'] }}">
                                            {{ $product['name'] }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.8rem; margin-top: 2px;">
                                            {{ number_format($product['qty']) }} Sold
                                        </div>
                                    </div>

                                </div>

                                @if (!$loop->last)
                                    <hr class="my-1" style="border-top: 1px solid #f7fafc; opacity: 1;">
                                @endif
                            @empty
                                <div class="text-center py-5">
                                    <i class="bi bi-box-seam text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted small mt-2">No products sold yet</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Footer opsional agar card terlihat penuh/kokoh --}}
                        <div class="card-footer bg-transparent border-0 pb-4 px-4">
                            <a href="#" class="btn btn-light btn-sm w-100 rounded-3 fw-bold text-muted"
                                style="font-size: 0.7rem;">VIEW ALL PRODUCTS</a>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row g-4 mb-4">
                {{-- KOLOM UTAMA (KIRI) --}}
                <div class="col-lg-12">

                    {{-- Card 1: Ringkasan & Tren --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0">Ringkasan & Tren Hari Ini</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 border border-light-subtle bg-light bg-opacity-25">
                                        <p class="text-muted small fw-bold text-uppercase mb-1"
                                            style="font-size: 0.65rem;">Total Transaksi</p>
                                        <h4 class="fw-bold mb-0">{{ number_format($todayTransactions ?? 0, 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3 border border-light-subtle bg-light bg-opacity-25">
                                        <p class="text-muted small fw-bold text-uppercase mb-1"
                                            style="font-size: 0.65rem;">Rata-rata Transaksi</p>
                                        <h4 class="fw-bold mb-0">Rp
                                            {{ number_format($averageBasketSize ?? 0, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            {{-- Grafik Batang 24 Jam --}}
                            <div style="height: 300px;">
                                <canvas id="peakHoursChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Performa Kasir (Ditaruh di Bawah Card Ringkasan) --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div
                            class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Performa Kasir</h6>
                            <span
                                class="badge bg-light text-muted fw-normal rounded-pill px-3">{{ now()->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="card-body p-0"> {{-- p-0 agar tabel penuh ke samping --}}
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="bg-light bg-opacity-50">
                                        <tr class="text-muted text-uppercase" style="font-size: 0.7rem;">
                                            <th class="border-0 ps-4 py-3">Kasir</th>
                                            <th class="border-0 text-center">Open Shift</th>
                                            <th class="border-0 text-center">Close Shift</th>
                                            <th class="border-0 text-end">Cash Awal</th>
                                            <th class="border-0 text-end">Cash Akhir</th>
                                            <th class="border-0 text-end">Total Sales</th>
                                            <th class="border-0 pe-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($cashierPerformance ?? [] as $cashier)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        {{-- <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2"
                                                            style="width: 30px; height: 30px;">
                                                            <i class="bi bi-person text-primary"
                                                                style="font-size: 0.8rem;"></i>
                                                        </div> --}}
                                                        <div>
                                                            <span
                                                                class="fw-bold d-block text-dark">{{ $cashier['name'] }}</span>
                                                            {{-- <small class="text-muted">{{ $cashier['transactions_count'] }}
                                                                Transaksi</small> --}}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center text-primary fw-medium">{{ $cashier['open_time'] }}
                                                </td>
                                                <td class="text-center text-muted">{{ $cashier['close_time'] }}</td>
                                                <td class="text-end text-muted">Rp
                                                    {{ number_format($cashier['starting_cash'], 0, ',', '.') }}</td>
                                                <td class="text-end text-muted">Rp
                                                    {{ number_format($cashier['ending_cash'], 0, ',', '.') }}</td>
                                                <td class="text-end fw-bold text-dark">Rp
                                                    {{ number_format($cashier['total_sales'], 0, ',', '.') }}</td>
                                                <td class="text-center pe-4">
                                                    <span
                                                        class="badge {{ $cashier['status'] == 'open' ? 'bg-success' : 'bg-secondary' }} rounded-pill"
                                                        style="font-size: 0.65rem;">
                                                        {{ strtoupper($cashier['status']) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted small">Belum ada
                                                    shift hari ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-3 mb-2">
                @php
                    $cards = [
                        [
                            'title' => 'Total Sales',
                            'val' => $sales,
                            'icon' => 'bi-cart-check',
                            'color' => '#0d6efd',
                            'bg' => 'rgba(13, 110, 253, 0.1)',
                        ],
                        [
                            'title' => 'Total Purchase',
                            'val' => $purchases,
                            'icon' => 'bi-bag-dash',
                            'color' => '#0dcaf0',
                            'bg' => 'rgba(13, 202, 240, 0.1)',
                        ],
                        [
                            'title' => 'Expenses',
                            'val' => $expenses,
                            'icon' => 'bi-wallet2',
                            'color' => '#dc3545',
                            'bg' => 'rgba(220, 53, 69, 0.1)',
                        ],
                        [
                            'title' => 'Net Profit',
                            'val' => $profit,
                            'icon' => 'bi-graph-up-arrow',
                            'color' => '#198754',
                            'bg' => 'rgba(25, 135, 84, 0.1)',
                        ],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 h-80">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold text-uppercase mb-1"
                                            style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                            {{ $card['title'] }}
                                        </p>
                                        <h4 class="fw-bold mb-0" style="color: #2d3748;">
                                            Rp {{ number_format($card['val'], 0, ',', '.') }}
                                        </h4>
                                    </div>
                                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                                        style="width: 52px; height: 52px; background-color: {{ $card['bg'] }}; flex-shrink: 0;">
                                        <i class="bi {{ $card['icon'] }}"
                                            style="color: {{ $card['color'] }}; font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row g-4 mb-4 d-flex align-items-stretch">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4">Sales Performance (7 Days)</h6>
                            <div style="height: 300px;"><canvas id="mainSalesChart"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 text-center">
                            <h6 class="fw-bold mb-4">Revenue Sources</h6>
                            <div style="height: 250px;"><canvas id="distributionChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('page_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('peakHoursChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.8)');
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0.05)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Array.from({
                    length: 24
                }, (_, i) => `${i.toString().padStart(2, '0')}:00`),
                datasets: [{
                    label: 'Transaksi',
                    data: @json($peakHoursData),
                    backgroundColor: gradient,
                    borderRadius: 4,
                    barPercentage: 0.8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            display: false
                        },
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 9
                            }
                        }
                    }
                }
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const salesCtx = document.getElementById('mainSalesChart');

            if (salesCtx) {
                fetch("{{ route('home.chart-data') }}")
                    .then(response => {
                        if (!response.ok) throw new Error('Server Error');
                        return response.json();
                    })
                    .then(json => {
                        if (json.error) {
                            console.error("Backend Error:", json.error);
                            return;
                        }

                        new Chart(salesCtx.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: json.labels,
                                datasets: [{
                                    label: 'Sales Performance',
                                    data: json.data,
                                    borderColor: '#4e73df',
                                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                                    fill: true,
                                    tension: 0.3
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(err => {
                        console.error("Gagal parse JSON. Kemungkinan response bukan JSON murni.");
                    });
            }

            // Doughnut Chart (Tetap menggunakan data dari Index)
            const distCtx = document.getElementById('distributionChart');
            if (distCtx) {
                new Chart(distCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Sales', 'Purchases', 'Expenses'],
                        datasets: [{
                            data: [{{ $sales }}, {{ $purchases }},
                                {{ $expenses }}
                            ],
                            backgroundColor: ['#4e73df', '#36b9cc', '#e74a3b'],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        cutout: '70%'
                    }
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Manual Tab Switch (Hanya jika Bootstrap JS bawaan tidak jalan)
            const tabs = document.querySelectorAll('#performanceTab button');
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();

                    // 1. Reset Buttons
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // 2. Reset Panes
                    const targetSelector = this.getAttribute('data-bs-target');
                    const allPanes = document.querySelectorAll('#performanceTabContent .tab-pane');

                    allPanes.forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // 3. Show Target
                    const targetPane = document.querySelector(targetSelector);
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }
                });
            });
        });
    </script>
@endpush
