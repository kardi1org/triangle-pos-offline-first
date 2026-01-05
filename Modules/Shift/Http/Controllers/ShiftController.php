<?php

namespace Modules\Shift\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shift\Entities\Shift;
use Modules\Shift\Entities\CashTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    // Fungsi Helper Private untuk hitung sales agar tidak copy-paste kode berulang
    private function calculateNetCashSales($userId, $startTime, $endTime)
    {
        return DB::table('sale_payments')
            ->join('sales', 'sale_payments.sale_id', '=', 'sales.id')
            ->where('sales.user_id', $userId)
            ->whereBetween('sale_payments.created_at', [$startTime, $endTime])
            // PERBAIKAN DI SINI: Cashpay - Change
            ->sum(DB::raw('sale_payments.cashpay - COALESCE(sale_payments.change, 0)'));
    }

    public function index()
    {
        $userId = Auth::id();
        $activeShift = Shift::where('user_id', $userId)->where('status', 'open')->first();

        $totalCashSales = 0;
        $totalIncome = 0;
        $totalExpense = 0;

        if ($activeShift) {
            $now = now();

            // Panggil fungsi helper perhitungan
            $totalCashSales = $this->calculateNetCashSales($userId, $activeShift->open_time, $now);

            $totalIncome = CashTransaction::where('user_id', $userId)
                ->where('type', 'pemasukan')
                ->whereBetween('transaction_date', [$activeShift->open_time, $now])
                ->sum('amount');

            $totalExpense = CashTransaction::where('user_id', $userId)
                ->where('type', 'pengeluaran')
                ->whereBetween('transaction_date', [$activeShift->open_time, $now])
                ->sum('amount');
        }

        return view('shift::index', compact('activeShift', 'totalCashSales', 'totalIncome', 'totalExpense'));
    }

    public function openShift(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0'
        ]);

        Shift::create([
            'user_id'       => Auth::id(),
            'open_time'     => now(),
            'starting_cash' => $request->starting_cash,
            'status'        => 'open'
        ]);
        toast('Shift berhasil dibuka.', 'success');
        return back();
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pemasukan,pengeluaran',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string',
        ]);

        CashTransaction::create([
            'user_id' => Auth::id(), // Pastikan user_id tersimpan
            'type' => $request->type,
            'amount' => $request->amount,
            'category' => $request->category,
            'note' => $request->note,
            'transaction_date' => now(),
        ]);
        toast('Transaksi kas ' . $request->type . ' berhasil dicatat.', 'success');
        return back();
    }

    public function closeShift(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);
        $userId = $shift->user_id;
        $now = now();

        // Panggil fungsi helper perhitungan
        $sales = $this->calculateNetCashSales($userId, $shift->open_time, $now);

        $income = CashTransaction::where('user_id', $userId)
            ->where('type', 'pemasukan')
            ->whereBetween('transaction_date', [$shift->open_time, $now])
            ->sum('amount');

        $expense = CashTransaction::where('user_id', $userId)
            ->where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [$shift->open_time, $now])
            ->sum('amount');

        $expected = ($shift->starting_cash + $sales + $income) - $expense;

        $shift->update([
            'close_time' => $now,
            'ending_cash' => $request->ending_cash,
            'expected_ending_cash' => $expected,
            'status' => 'closed',
            'note' => "Penjualan: $sales, Masuk: $income, Keluar: $expense. " . $request->note
        ]);
        toast('Shift ditutup.', 'success');
        return redirect()->route('shift.index')->with('message', 'Shift ditutup.');
        //return redirect()->route('shift.show', $shift->id)->with('success', 'Shift ditutup.');
    }

    public function show($id)
    {
        $shift = Shift::where('id', $id)->firstOrFail();

        // Gunakan close_time jika sudah tutup, atau now() jika belum (untuk preview)
        $endTime = $shift->close_time ?? now();

        // Panggil fungsi helper perhitungan
        $sales = $this->calculateNetCashSales($shift->user_id, $shift->open_time, $endTime);

        $income = CashTransaction::where('user_id', $shift->user_id)
            ->where('type', 'pemasukan')
            ->whereBetween('transaction_date', [$shift->open_time, $endTime])
            ->sum('amount');

        $expense = CashTransaction::where('user_id', $shift->user_id)
            ->where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [$shift->open_time, $endTime])
            ->sum('amount');

        return view('shift::show', compact('shift', 'sales', 'income', 'expense'));
    }

    public function reportIndex(Request $request)
    {
        $query = Shift::with('user')->orderBy('created_at', 'desc');

        // Filter Tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereDate('open_time', '>=', $request->start_date)
                ->whereDate('open_time', '<=', $request->end_date);
        }

        // Jika bukan Admin, hanya melihat shift sendiri (opsional)
        // if (!auth()->user()->hasRole('Admin')) {
        //     $query->where('user_id', auth()->id());
        // }

        $shifts = $query->paginate(10); // Menampilkan 10 shift per halaman

        return view('shift::report.index', compact('shifts'));
    }

    public function getShiftDetails($id)
    {
        $shift = Shift::with('user')->findOrFail($id);
        $endTime = $shift->close_time ?? now();

        // Panggil fungsi helper perhitungan
        $sales = $this->calculateNetCashSales($shift->user_id, $shift->open_time, $endTime);

        $income = CashTransaction::where('user_id', $shift->user_id)
            ->where('type', 'pemasukan')
            ->whereBetween('transaction_date', [$shift->open_time, $endTime])
            ->sum('amount');

        $expense = CashTransaction::where('user_id', $shift->user_id)
            ->where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [$shift->open_time, $endTime])
            ->sum('amount');

        return view('shift::report.details_partial', compact('shift', 'sales', 'income', 'expense'));
    }
}
