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

            // a. Dari Cash Transfer sebelum tanggal mulai (Asumsi tidak dikali 100)
            $cashInBefore = CashTransfer::where('date', '<', $startDate)
                ->where('receive_type_transferto', $receiveType)
                ->sum('amount_transferto');

            $cashOutBefore = CashTransfer::where('date', '<', $startDate)
                ->where('receive_type_transferfrom', $receiveType)
                ->sum('amount_transferfrom');

            // 🎯 b. Dari Sales Return (Uang Keluar -> Dibagi 100)
            $salesReturnBefore = DB::table('sale_returns')
                ->where('date', '<', $startDate)
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->sum(DB::raw('paid_amount / 100'));

            // 🎯 c. Dari Purchase (Uang Keluar -> Dibagi 100)
            $purchaseBefore = DB::table('purchases')
                ->where('date', '<', $startDate)
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->sum(DB::raw('paid_amount / 100'));

            // 🎯 d. Dari Purchase Return (Uang Masuk -> Dibagi 100)
            $purchaseReturnBefore = DB::table('purchase_returns')
                ->where('date', '<', $startDate)
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->sum(DB::raw('paid_amount / 100'));

            // 🎯 d.2. DARI EXPENSES SEBELUM TANGGAL MULAI (Uang Keluar -> Dibagi 100)
            $expenseBefore = DB::table('expenses')
                ->where('date', '<', $startDate)
                ->where('payment_method', $receiveType)
                ->sum(DB::raw('amount / 100'));

            // e. Dari Sale Payments (Uang Masuk -> Asumsi cashpay/gopay tidak dikali 100 karena merujuk ke tabel input)
            $salesBefore = 0;
            $column = $this->determinePaymentColumn($receiveType);

            if ($column) {
                if ($column === 'cashpay') {
                    $salesBefore = DB::table('sale_payments')
                        ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
                        ->where('sales.status', 'Completed')
                        ->where('sale_payments.date', '<', $startDate)
                        ->where('sale_payments.cashpay', '>', 0)
                        ->sum(DB::raw('sale_payments.cashpay - IFNULL(sale_payments.change, 0)'));
                } else {
                    $salesBefore = DB::table('sale_payments')
                        ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
                        ->where('sales.status', 'Completed')
                        ->where('sale_payments.date', '<', $startDate)
                        ->where("sale_payments.$column", '>', 0)
                        ->sum("sale_payments.$column");
                }
            }

            // f. Dari Selisih Shift Berdasarkan Kalkulasi Matematika Sebelum Tanggal Mulai
            $shiftDifferenceBefore = 0;
            if ($column === 'cashpay') {
                $shiftDifferenceBefore = DB::table('shifts')
                    ->where('status', 'closed')
                    ->whereRaw('DATE(close_time) < ?', [$startDate])
                    ->sum(DB::raw('IFNULL(ending_cash, 0) - IFNULL(expected_ending_cash, 0)'));
            }

            // Rumus Akhir Saldo Awal Berjalan (Expense mengurangi saldo awal karena uang keluar)
            $openingBalance = ($cashInBefore + $purchaseReturnBefore + $salesBefore + $shiftDifferenceBefore) - ($cashOutBefore + $salesReturnBefore + $purchaseBefore + $expenseBefore);

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

            // 🎯 Query b: Sales Return (Uang Keluar -> kredit dibagi 100)
            $querySalesReturn = DB::table('sale_returns')
                ->select(
                    'date',
                    'reference',
                    DB::raw("'Retur Penjualan (Sales Return)' as details"),
                    DB::raw("0 as debet"),
                    DB::raw("(paid_amount / 100) as kredit")
                )
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->whereBetween('date', [$startDate, $endDate]);

            // 🎯 Query c: Purchase (Uang Keluar -> kredit dibagi 100)
            $queryPurchase = DB::table('purchases')
                ->select(
                    'date',
                    'reference',
                    DB::raw("'Pembelian ke Supplier (Purchase)' as details"),
                    DB::raw("0 as debet"),
                    DB::raw("(paid_amount / 100) as kredit")
                )
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->whereBetween('date', [$startDate, $endDate]);

            // 🎯 Query d: Purchase Return (Uang Masuk -> debet dibagi 100)
            $queryPurchaseReturn = DB::table('purchase_returns')
                ->select(
                    'date',
                    'reference',
                    DB::raw("'Retur Pembelian Supplier (Purchase Return)' as details"),
                    DB::raw("(paid_amount / 100) as debet"),
                    DB::raw("0 as kredit")
                )
                ->where('status', 'Completed')
                ->where('payment_method', $receiveType)
                ->whereBetween('date', [$startDate, $endDate]);

            // 🎯 Query d.2: Expenses (Uang Keluar -> kredit dibagi 100)
            $queryExpense = DB::table('expenses')
                ->select(
                    'date',
                    'reference',
                    DB::raw("IFNULL(details, 'Pengeluaran (Expense)') as details"),
                    DB::raw("0 as debet"),
                    DB::raw("(amount / 100) as kredit")
                )
                ->where('payment_method', $receiveType)
                ->whereBetween('date', [$startDate, $endDate]);

            // Query e: Sale Payments
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

            // Query f: Selisih Shift Kasir
            $queryShift = null;
            if ($column === 'cashpay') {
                $queryShift = DB::table('shifts')
                    ->select(
                        DB::raw('DATE(close_time) as date'),
                        DB::raw("CONCAT('SHIFT-', id) as reference"),
                        DB::raw("'Penyesuaian Selisih Shift Kasir' as details"),
                        DB::raw('IF(IFNULL(ending_cash, 0) - IFNULL(expected_ending_cash, 0) > 0, IFNULL(ending_cash, 0) - IFNULL(expected_ending_cash, 0), 0) as debet'),
                        DB::raw('IF(IFNULL(ending_cash, 0) - IFNULL(expected_ending_cash, 0) < 0, ABS(IFNULL(ending_cash, 0) - IFNULL(expected_ending_cash, 0)), 0) as kredit')
                    )
                    ->where('status', 'closed')
                    ->whereRaw('IFNULL(ending_cash, 0) - IFNULL(expected_ending_cash, 0) != 0')
                    ->whereRaw('DATE(close_time) between ? and ?', [$startDate, $endDate]);
            }

            // Gabungkan menggunakan UNION ALL (Termasuk queryExpense)
            if ($column) {
                $querySales->where("sale_payments.$column", '>', 0);

                $unionQuery = $queryCash
                    ->unionAll($querySalesReturn)
                    ->unionAll($queryPurchase)
                    ->unionAll($queryPurchaseReturn)
                    ->unionAll($queryExpense) // 🎯 Dimasukkan ke jalur utama
                    ->unionAll($querySales);

                if ($queryShift) {
                    $unionQuery = $unionQuery->unionAll($queryShift);
                }

                $combinedData = $unionQuery->orderBy('date', 'asc')->get();
            } else {
                $combinedData = $queryCash
                    ->unionAll($querySalesReturn)
                    ->unionAll($queryPurchase)
                    ->unionAll($queryPurchaseReturn)
                    ->unionAll($queryExpense) // 🎯 Dimasukkan ke jalur fallback
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

        return null;
    }
}
