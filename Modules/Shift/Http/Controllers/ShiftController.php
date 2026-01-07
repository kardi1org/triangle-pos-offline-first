<?php

namespace Modules\Shift\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shift\Entities\Shift;
use Modules\Shift\Entities\CashTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        toast('Shift opened successfully.', 'success');
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
        toast('Cash transaction saved successfully.', 'success');
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
            'note' => "Sales: $sales, Income: $income, Expense: $expense. " . $request->note
        ]);
        toast('Shift closed successfully.', 'success');
        //return redirect()->route('shift.index')->with('message', 'Shift ditutup.');
        return redirect()->route('shift.show', $shift->id)->with('success', 'Shift closed successfully.');
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

        $cashier = DB::connection('db_pos')->table('users')->where('id', $shift->user_id)->first();

        return view('shift::show', compact('shift', 'sales', 'income', 'expense', 'cashier'));
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

        // Get all unique user IDs from the current page of shifts
        $userIds = $shifts->pluck('user_id')->unique();

        // Fetch those users from the CENTRAL database in ONE query
        $users = DB::connection('db_pos')
            ->table('users')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id'); // Key by ID for easy lookup

        return view('shift::report.index', compact('shifts', 'users'));
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

        $cashier = DB::connection('db_pos')
            ->table('users')
            ->where('id', $shift->user_id)
            ->first();

        return view('shift::report.details_partial', compact('shift', 'sales', 'income', 'expense', 'cashier'));
    }

    public function exportExcel(Request $request)
    {
        // 1. Ambil data dengan filter yang sama dengan halaman index
        $query = Shift::query();

        if ($request->start_date) {
            $query->whereDate('open_time', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('open_time', '<=', $request->end_date);
        }

        $shifts = $query->orderBy('created_at', 'desc')->get();

        // 2. Ambil semua User Name dari DB Central sekaligus (agar tidak error/null)
        $userIds = $shifts->pluck('user_id')->unique();
        $users = DB::connection('db_pos')
            ->table('users')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        // 3. Setup Header untuk Download File
        $fileName = 'Shift_Report_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Shift ID', 'Cashier', 'Open Time', 'Close Time', 'Expected Cash', 'Actual Cash', 'Difference', 'Status'];

        $callback = function () use ($shifts, $columns, $users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($shifts as $item) {
                $diff = $item->ending_cash - $item->expected_ending_cash;
                $cashierName = $users[$item->user_id]->name ?? 'Unknown';

                fputcsv($file, [
                    $item->id,
                    $cashierName,
                    $item->open_time,
                    $item->close_time ?? 'Active',
                    number_format($item->expected_ending_cash, 0, '', ''),
                    number_format($item->ending_cash, 0, '', ''),
                    number_format($diff, 0, '', ''),
                    ucfirst($item->status)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
