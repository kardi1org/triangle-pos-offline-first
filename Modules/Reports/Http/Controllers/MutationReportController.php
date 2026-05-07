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
            // 1. Hitung Saldo Awal (Opening Balance)
            // Saldo = (Total Masuk ke akun ini) - (Total Keluar dari akun ini)
            $totalInBefore = CashTransfer::where('date', '<', $startDate)
                ->where('receive_type_transferto', $receiveType)
                ->sum('amount_transferto');

            $totalOutBefore = CashTransfer::where('date', '<', $startDate)
                ->where('receive_type_transferfrom', $receiveType)
                ->sum('amount_transferfrom');

            $openingBalance = $totalInBefore - $totalOutBefore;

            // 2. Ambil data mutasi dalam rentang tanggal
            $data = CashTransfer::whereBetween('date', [$startDate, $endDate])
                ->where(function ($query) use ($receiveType) {
                    $query->where('receive_type_transferfrom', $receiveType)
                        ->orWhere('receive_type_transferto', $receiveType);
                })
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $runningBalance = $openingBalance;

            foreach ($data as $item) {
                // Jika dia ada di 'transferto' maka uang masuk (Debet)
                $debet = ($item->receive_type_transferto == $receiveType) ? $item->amount_transferto : 0;
                // Jika dia ada di 'transferfrom' maka uang keluar (Kredit)
                $kredit = ($item->receive_type_transferfrom == $receiveType) ? $item->amount_transferfrom : 0;

                $runningBalance += ($debet - $kredit);

                $mutations->push([
                    'date'      => $item->date,
                    'reference' => $item->reference,
                    'details'   => $item->details,
                    'debet'     => $debet,
                    'kredit'    => $kredit,
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
}
