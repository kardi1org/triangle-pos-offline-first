<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProfitLossReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        // 🎯 1. Dapatkan outlet_id dari user yang sedang login
        $currentOutletId = auth()->user()->outlet_id;

        // 🎯 2. Cari email dari outlet tersebut di database pusat (db_pos)
        $outletInfo = DB::table('db_pos.outlets')
            ->where('id', $currentOutletId)
            ->first();

        // Ambil emailnya, jika tidak ditemukan default ke string kosong atau email user
        $targetEmail = $outletInfo ? $outletInfo->email : auth()->user()->email;

        // 🎯 3. Ambil semua list outlet yang memiliki email yang sama dengan target email
        $outlets = DB::table('db_pos.outlets')
            ->select('id', 'name')
            ->where('email', $targetEmail) // Menampilkan semua cabang dengan email group yang sama
            ->get();

        // Ambil kumpulan ID outlet hasil filter untuk optimasi query di bawah
        $outletIds = $outlets->pluck('id')->toArray();

        // 4. Ambil Data Penjualan per Outlet
        $salesData = DB::table('sales')
            ->select('outlet_id', DB::raw('SUM(total_amount / 100) as total_sales'))
            ->where('status', 'Completed')
            ->whereIn('outlet_id', $outletIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('outlet_id')
            ->pluck('total_sales', 'outlet_id')
            ->toArray();

        // 5. Ambil Data HPP (Harga Pokok Penjualan) per Outlet
        $costData = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select(
                'sales.outlet_id',
                DB::raw('SUM((products.product_cost * sale_details.quantity) / 100) as total_cost')
            )
            ->where('sales.status', 'Completed')
            ->whereIn('sales.outlet_id', $outletIds)
            ->whereBetween('sales.date', [$startDate, $endDate])
            ->groupBy('sales.outlet_id')
            ->pluck('total_cost', 'outlet_id')
            ->toArray();

        // 6. Ambil Kategori Pengeluaran (Expense Categories) untuk baris laporan
        $expenseCategories = DB::table('expense_categories')->get();

        // 7. Ambil Matriks Pengeluaran per Kategori per Outlet
        $expenses = DB::table('expenses')
            ->select('category_id', 'outlet_id', DB::raw('SUM(amount / 100) as total_amount'))
            ->whereIn('outlet_id', $outletIds)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category_id', 'outlet_id')
            ->get()
            ->groupBy('category_id')
            ->map(function ($item) {
                return $item->pluck('total_amount', 'outlet_id')->toArray();
            })
            ->toArray();

        return view('reports::profit-loss-detail.index', compact(
            'outlets',
            'salesData',
            'costData',
            'expenseCategories',
            'expenses',
            'startDate',
            'endDate'
        ));
    }
}
