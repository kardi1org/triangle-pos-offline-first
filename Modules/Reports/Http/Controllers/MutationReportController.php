<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Expense\Entities\CashTransfer;
use Modules\MethodePay\Entities\MethodePay;

class MutationReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        $receiveType = $request->receive_type;

        // Ambil data metode pembayaran untuk dropdown
        $paymentMethods = MethodePay::all();

        $mutations = collect();
        $openingBalance = 0;

        if ($receiveType) {

            // ==========================================
            // 1. HITUNG SALDO AWAL (OPENING BALANCE)
            // ==========================================

            // a. Dari Cash Transfer sebelum tanggal mulai
            $cashInBefore = CashTransfer::where('date', '<', $startDate)
                ->where('receive_type_transferto', $receiveType)
                ->sum('amount_transferto');

            $cashOutBefore = CashTransfer::where('date', '<', $startDate)
                ->where('receive_type_transferfrom', $receiveType)
                ->sum('amount_transferfrom');

            // b. Dari Sales Return (Uang Keluar -> Mengurangi Saldo)
            $salesReturnBefore = DB::table('sale_returns')
                ->where('date', '<', $startDate)
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->sum('paid_amount');

            // c. Dari Purchase (Uang Keluar -> Mengurangi Saldo)
            $purchaseBefore = DB::table('purchases')
                ->where('date', '<', $startDate)
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->sum('paid_amount');

            // d. Dari Purchase Return (Uang Masuk -> Menambah Saldo)
            $purchaseReturnBefore = DB::table('purchase_returns')
                ->where('date', '<', $startDate)
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->sum('paid_amount');

            // e. Dari Sale Payments (Uang Masuk -> Menambah Saldo)
            $salesBefore = 0;
            // Tentukan kolom mana yang dibaca berdasarkan $receiveType
            $column = $this->determinePaymentColumn($receiveType);

            if ($column) {
                if ($column === 'cashpay') {
                    // Jika cashpay, total nominal dikurangi kembalian (change)
                    // 🎯 Perbaikan: Ubah :: menjadi . dan tambahkan DB::raw untuk kalkulasi matematika di sum()
                    $salesBefore = DB::table('sale_payments')
                        ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
                        ->where('sales.status', 'Completed')
                        ->where('sale_payments.date', '<', $startDate)
                        ->where('sale_payments.cashpay', '>', 0)
                        ->sum(DB::raw('sale_payments.cashpay - IFNULL(sale_payments.change, 0)'));
                } else {
                    // Jika non-tunai (gopay, debitcard, dll)
                    // 🎯 Perbaikan: Ubah :: menjadi .
                    $salesBefore = DB::table('sale_payments')
                        ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
                        ->where('sales.status', 'Completed')
                        ->where('sale_payments.date', '<', $startDate)
                        ->where("sale_payments.$column", '>', 0)
                        ->sum("sale_payments.$column");
                }
            }

            // Rumus Akhir Saldo Awal Berjalan
            $openingBalance = ($cashInBefore + $purchaseReturnBefore + $salesBefore) - ($cashOutBefore + $salesReturnBefore + $purchaseBefore);

            // ==========================================
            // 2. QUERY UNION UNTUK DATA MUTASI BERJALAN
            // ==========================================

            // Query a: Cash Transfer
            $queryCash = DB::table('cash_transfers')
                ->select(
                    'date',
                    'reference',
                    'details',
                    DB::raw("IF(receive_type_transferto = '$receiveType', amount_transferto, 0) as debet"),
                    DB::raw("IF(receive_type_transferfrom = '$receiveType', amount_transferfrom, 0) as kredit")
                )
                ->whereBetween('date', [$startDate, $endDate])
                ->where(function ($q) use ($receiveType) {
                    $q->where('receive_type_transferfrom', $receiveType)
                        ->orWhere('receive_type_transferto', $receiveType);
                });

            // Query b: Sales Return (Uang Keluar)
            $querySalesReturn = DB::table('sale_returns')
                ->select(
                    'date',
                    'reference',
                    DB::raw("'Retur Penjualan (Sales Return)' as details"),
                    DB::raw("0 as debet"),
                    'paid_amount as kredit'
                )
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->whereBetween('date', [$startDate, $endDate]);

            // Query c: Purchase (Uang Keluar)
            $queryPurchase = DB::table('purchases')
                ->select(
                    'date',
                    'reference',
                    DB::raw("'Pembelian ke Supplier (Purchase)' as details"),
                    DB::raw("0 as debet"),
                    'paid_amount as kredit'
                )
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->whereBetween('date', [$startDate, $endDate]);

            // Query d: Purchase Return (Uang Masuk)
            $queryPurchaseReturn = DB::table('purchase_returns')
                ->select(
                    'date',
                    'reference',
                    DB::raw("'Retur Pembelian Supplier (Purchase Return)' as details"),
                    'paid_amount as debet',
                    DB::raw("0 as kredit")
                )
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->whereBetween('date', [$startDate, $endDate]);

            // Query e: Sale Payments (Uang Masuk dinamis berdasarkan kolom terpilih)
            $querySales = DB::table('sale_payments')
                ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
                ->select(
                    'sale_payments.date',
                    'sale_payments.reference',
                    DB::raw("'Penjualan Kasir (Sales)' as details"),
                    $column === 'cashpay'
                        ? DB::raw('sale_payments.cashpay - IFNULL(sale_payments.change, 0) as debet')
                        : DB::raw("sale_payments.$column as debet"),
                    DB::raw("0 as kredit")
                )
                ->where('sales.status', 'Completed')
                ->whereBetween('sale_payments.date', [$startDate, $endDate]);

            // Jika filter field payment method ditemukan, eksekusi gabungan UNION ALL
            if ($column) {
                // 🎯 FIXED: Mengubah "sale_payments::$column" menjadi "sale_payments.$column"
                $querySales->where("sale_payments.$column", '>', 0);

                $combinedData = $queryCash
                    ->unionAll($querySalesReturn)
                    ->unionAll($queryPurchase)
                    ->unionAll($queryPurchaseReturn)
                    ->unionAll($querySales)
                    ->orderBy('date', 'asc')
                    ->get();
            } else {
                // Jika metode pembayaran tidak terikat ke kolom penjualan (misal tipe transaksi internal)
                $combinedData = $queryCash
                    ->unionAll($querySalesReturn)
                    ->unionAll($queryPurchase)
                    ->unionAll($queryPurchaseReturn)
                    ->orderBy('date', 'asc')
                    ->get();
            }

            // ==========================================
            // 3. HITUNG RUNNING BALANCE (SALDO BERJALAN)
            // ==========================================
            $runningBalance = $openingBalance;

            foreach ($combinedData as $item) {
                $debetValue = $item->debit ?? $item->debet ?? 0;
                $runningBalance += $debetValue - $item->kredit;

                $mutations->push([
                    'date'      => $item->date,
                    'reference' => $item->reference,
                    'details'   => $item->details,
                    'debet'     => $debetValue,
                    'kredit'    => $item->kredit,
                    'saldo'     => $runningBalance
                ]);
            }
        }

        return view('reports::mutation.index', compact(
            'paymentMethods',
            'mutations',
            'openingBalance',
            'startDate',
            'endDate',
            'receiveType'
        ));
    }

    /**
     * Helper privat untuk mencocokkan receive_type dari request dengan nama kolom fisik di tabel sale_payments.
     */
    private function determinePaymentColumn($receiveType)
    {
        $receiveType = strtolower($receiveType);

        if (str_contains($receiveType, 'cash')) return 'cashpay';
        if (str_contains($receiveType, 'debit card')) return 'debitcard';
        if (str_contains($receiveType, 'credit card')) return 'creditcard';
        if (str_contains($receiveType, 'gopay')) return 'gopay';
        if (str_contains($receiveType, 'grabpay')) return 'grabpay';
        if (str_contains($receiveType, 'ovo')) return 'ovopay';
        if (str_contains($receiveType, 'shopeepay')) return 'shopeepay';
        if (str_contains($receiveType, 'dana')) return 'danapay';
        if (str_contains($receiveType, 'kredivo')) return 'kredivopay';
        if (str_contains($receiveType, 'qris')) return 'qrispay';

        return null; // Return null jika tidak ada field yang cocok
    }
}
