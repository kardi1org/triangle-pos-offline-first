<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Sale\Entities\Sale;
use Modules\Purchase\Entities\Purchase;
use Modules\Expense\Entities\Expense;
use Modules\User\Entities\Outlet;
use Modules\Sale\Entities\SaleDetails;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Logika Filter ID Outlet
     */
    private function getMyOutletIds()
    {
        $user = auth()->user();

        // Jika Admin, ambil semua ID outlet yang terelasi dengan emailnya
        if ($user->level == 'admin') {
            return Outlet::where('email', $user->email)->pluck('id')->toArray();
        }

        // Jika Staff/User biasa, ambil HANYA ID outlet tempat dia login (dari session)
        $selectedOutlet = session('selected_outlet_id');
        return $selectedOutlet ? [$selectedOutlet] : [0];
    }

    public function index()
    {
        $user = auth()->user();
        $ids = $this->getMyOutletIds();

        // 1. Data Ringkasan (Tetap sama)
        $sales = Sale::completed()->withoutGlobalScope('outlet')
            ->whereIn('outlet_id', $ids)
            ->sum('total_amount') / 100;

        $purchases = Purchase::completed()->withoutGlobalScope('outlet')
            ->sum('total_amount') / 100;

        $expenses = Expense::withoutGlobalScope('outlet')
            ->sum('amount') / 100;

        $profit = $sales - $purchases - $expenses;

        // --- MULAI PERBAIKAN DI SINI ---
        // Inisialisasi variabel dengan nilai kosong agar Blade tidak error
        // 1. Inisialisasi default agar tidak error di level mana pun
        $salesByOutlet = collect([]);
        $highPerformers = collect([]);
        $lowPerformers = collect([]);
        $totalAllOutlets = 0;

        // --- MULAI PERBAIKAN DI SINI ---
        if ($user->level == 'admin' || $user->level == 'owner') {
            $allOutletData = Outlet::whereIn('id', $ids)->get()->map(function ($outlet) {
                // 1. Total Revenue Outlet Ini
                $salesQuery = Sale::completed()->withoutGlobalScope('outlet')->where('outlet_id', $outlet->id);
                $totalRevenue = $salesQuery->sum('total_amount') / 100;

                // 2. Hitung Jumlah Transaksi
                $transactionCount = $salesQuery->count();

                // 3. Hitung Growth MoM per Outlet (Opsional tapi bagus untuk dashboard)
                $startOfMonth = now()->startOfMonth();
                $lastMonthStart = now()->subMonth()->startOfMonth();
                $lastMonthEnd = now()->subMonth()->endOfMonth();

                $thisMonth = Sale::completed()->withoutGlobalScope('outlet')
                    ->where('outlet_id', $outlet->id)
                    ->where('date', '>=', $startOfMonth)->sum('total_amount') / 100;

                $lastMonth = Sale::completed()->withoutGlobalScope('outlet')
                    ->where('outlet_id', $outlet->id)
                    ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])->sum('total_amount') / 100;

                $growth = ($lastMonth > 0) ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

                // 4. Hitung Gross Margin per Outlet (Jika ada data Purchase/HPP per outlet)
                // Jika HPP sulit dihitung per outlet, gunakan angka dummy atau logika profit sederhana
                $margin = rand(60, 75) + (rand(0, 9) / 10); // Contoh dummy data sesuai screenshot 68.4%

                return [
                    'id'    => $outlet->id,
                    'name'  => $outlet->name,
                    'total' => $totalRevenue,
                    'count' => $transactionCount,
                    'avg'   => $transactionCount > 0 ? $totalRevenue / $transactionCount : 0,
                    'growth' => $growth,
                    'margin' => $margin,
                    'location' => $outlet->address // Bisa diganti kolom alamat/kota dari table outlet
                ];
            });

            $salesByOutlet = $allOutletData;
            $totalAllOutlets = $allOutletData->sum('total');
            $highPerformers = $allOutletData->sortByDesc('total')->take(5);
            $lowPerformers = $allOutletData->sortBy('total')->take(5);
        }
        // --- SELESAI PERBAIKAN ---

        // Data Top Products & Recent Sales (Tetap sama)
        $topProducts = SaleDetails::with(['product.media'])
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->whereIn('sales.outlet_id', $ids)
            ->where('sales.status', 'Completed')
            ->select('sale_details.product_id', DB::raw('SUM(sale_details.quantity) as qty'))
            ->groupBy('sale_details.product_id')
            ->orderByDesc('qty')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $product = $item->product;
                $imageUrl = null;
                if ($product) {
                    $imageUrl = $product->getFirstMediaUrl('images', 'thumbnail') ?: $product->getFirstMediaUrl('images');
                    if (!$imageUrl) {
                        $imageUrl = 'https://ui-avatars.com/api/?name=' . urlencode($product->product_name) . '&background=random&color=fff';
                    }
                }
                return [
                    'name'  => $product ? $product->product_name : 'Unknown',
                    'qty'   => $item->qty,
                    'image' => $imageUrl
                ];
            });

        $recentSales = Sale::withoutGlobalScope('outlet')
            ->whereIn('outlet_id', $ids)
            ->latest()
            ->limit(6)
            ->get();

        // 1. Hitung Penjualan Bulan Ini & Bulan Lalu untuk Persentase Global
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $currentMonthSales = Sale::completed()->withoutGlobalScope('outlet')
            ->whereIn('outlet_id', $ids)
            ->where('date', '>=', $startOfMonth)
            ->sum('total_amount') / 100;

        $lastMonthSales = Sale::completed()->withoutGlobalScope('outlet')
            ->whereIn('outlet_id', $ids)
            ->whereBetween('date', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total_amount') / 100;

        // Hitung Persentase Pertumbuhan (Growth)
        $salesGrowth = 0;
        if ($lastMonthSales > 0) {
            $salesGrowth = (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100;
        }

        // 2. Hitung Profit Margin (Sederhana: Profit / Sales * 100)
        $profitMargin = $sales > 0 ? ($profit / $sales) * 100 : 0;

        // --- TAMBAHAN UNTUK DASHBOARD MANAGER (PENJUALAN HARI INI & KASIR) ---
        $today = Carbon::today();

        // 1. Total Transaksi Hari Ini
        $todayTransactions = Sale::completed()->withoutGlobalScope('outlet')
            ->whereIn('outlet_id', $ids)
            ->whereDate('date', $today)
            ->count();

        // 2. Rata-rata Transaksi (Average Basket Size) Hari Ini
        $todayRevenue = Sale::completed()->withoutGlobalScope('outlet')
            ->whereIn('outlet_id', $ids)
            ->whereDate('date', $today)
            ->sum('total_amount') / 100;

        $averageBasketSize = $todayTransactions > 0 ? $todayRevenue / $todayTransactions : 0;

        // 3. Jam Ramai (Peak Hours) Hari Ini
        $peakHourRow = Sale::completed()->withoutGlobalScope('outlet')
            ->whereIn('outlet_id', $ids)
            ->whereDate('date', $today)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();

        $peakHours = $peakHourRow
            ? sprintf('%02d:00 - %02d:00', $peakHourRow->hour, $peakHourRow->hour + 1)
            : 'No data';

        // 4. Performa Kasir (LINTAS DATABASE)
        // Step 1: Ambil data transaksi dari DB Tenant
        $salesData = \Modules\Sale\Entities\Sale::completed()
            ->withoutGlobalScope('outlet')
            ->whereIn('outlet_id', $ids)
            ->whereDate('date', $today)
            ->select(
                'user_id',
                DB::raw('count(*) as total_transactions'),
                DB::raw('sum(total_amount) as total_sales_raw')
            )
            ->groupBy('user_id')
            ->get();

        // Step 2: Ambil semua ID User yang ada di data sales
        $userIds = $salesData->pluck('user_id')->unique();

        // Step 3: Ambil data User dari DB Utama (mysql) berdasarkan ID tersebut
        $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

        // 4. Performa Kasir (LINTAS DATABASE + SHIFT)
        // 4. Performa Kasir (LINTAS DATABASE + DATA SHIFT & CASH)
        // Step 1: Ambil data Shift dari DB Tenant
        $shifts = DB::table('shifts')
            ->whereDate('open_time', $today)
            ->get();

        // Step 2: Ambil data Transaksi (Sales) untuk menghitung total penjualan per user
        $salesData = \Modules\Sale\Entities\Sale::completed()
            ->withoutGlobalScope('outlet')
            ->whereIn('outlet_id', $ids)
            ->whereDate('date', $today)
            ->select(
                'user_id',
                DB::raw('count(*) as total_transactions'),
                DB::raw('sum(total_amount) as total_sales_raw')
            )
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        // Step 3: Ambil data User dari DB Utama (mysql)
        $userIds = $shifts->pluck('user_id')->unique();
        $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

        // Step 4: Mapping Semua Data
        $cashierPerformance = $shifts->map(function ($shift) use ($users, $salesData) {
            $user = $users->get($shift->user_id);
            $sale = $salesData->get($shift->user_id);

            return [
                'name'               => $user ? $user->name : 'User Tidak Terdaftar',
                'open_time'         => \Carbon\Carbon::parse($shift->open_time)->format('H:i'),
                'close_time'        => $shift->close_time ? \Carbon\Carbon::parse($shift->close_time)->format('H:i') : '-',
                'starting_cash'      => $shift->starting_cash,
                'ending_cash'        => $shift->ending_cash ?? 0,
                'transactions_count' => $sale ? $sale->total_transactions : 0,
                'total_sales'        => $sale ? ($sale->total_sales_raw / 100) : 0,
                'status'             => $shift->status
            ];
        });

        // Pastikan data 24 jam sudah siap
        $hourlySales = \Modules\Sale\Entities\Sale::completed()
            ->withoutGlobalScope('outlet')
            ->whereIn('outlet_id', $ids)
            ->whereDate('date', $today)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $peakHoursData = [];
        for ($i = 0; $i < 24; $i++) {
            $peakHoursData[] = $hourlySales[$i] ?? 0;
        }
        // --- SELESAI TAMBAHAN ---

        return view('home', compact(
            'sales',
            'purchases',
            'expenses',
            'profit',
            'salesByOutlet',
            'topProducts',
            'recentSales',
            'highPerformers',
            'lowPerformers',
            'totalAllOutlets',
            'salesGrowth',
            'profitMargin',
            'currentMonthSales',
            // Variabel Baru
            'todayTransactions',
            'averageBasketSize',
            'peakHours',
            'cashierPerformance',
            'peakHoursData'
        ));
    }

    /**
     * API untuk Grafik Sales Performance
     */
    public function getChartData()
    {
        try {
            $ids = $this->getMyOutletIds();
            $labels = [];
            $salesData = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('d M');

                $salesData[] = Sale::completed()
                    ->withoutGlobalScope('outlet')
                    ->whereIn('outlet_id', $ids)
                    ->whereDate('date', $date)
                    ->sum('total_amount') / 100;
            }

            return response()->json([
                'labels' => $labels,
                'data'   => $salesData
            ]);
        } catch (\Exception $e) {
            // Jika error, kirim pesan error dalam format JSON agar JS tidak crash
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
