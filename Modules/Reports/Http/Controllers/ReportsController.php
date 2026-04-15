<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Exports\MutationCashExport;
use Modules\Reports\Entities\CashReport;

class ReportsController extends Controller
{

    public function profitLossReport()
    {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::profit-loss.index');
    }

    public function paymentsReport()
    {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::payments.index');
    }

    public function salesReport()
    {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::sales.index');
    }

    public function purchasesReport()
    {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::purchases.index');
    }

    public function salesReturnReport()
    {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::sales-return.index');
    }

    public function purchasesReturnReport()
    {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::purchases-return.index');
    }

    public function kitchenLogReport()
    {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::kitchen-log.index');
    }

    public function mutationCashReport()
    {
        abort_if(Gate::denies('access_reports'), 403);

        return view('reports::mutation-cash.index');
    }

    /* public function exporttoxl()
    {
        $cashs = CashTransfer::whereDate('date', '>=', $this->start_date)
        ->whereDate('date', '<=', $this->end_date)
        /* ->when($this->methode_name, function ($query) {
            return $query->where('receive_type_transferto', $this->methode_name);
        }) */
    /* ->orderBy('date', 'desc')->paginate(10);
        return Excel::download(new MutationCashExport, 'cash_transfer.xlsx');
    } */

    public function exportExcel()
    {
        /* $cashs = CashTransfer::whereDate('date', '>=', $this->start_date)
        ->whereDate('date', '<=', $this->end_date)
        ->orderBy('id', 'asc')->paginate(10); */
        $cashs = DB::select('CALL GetCashBalanceByPeriod(?, ?, ?)', [$this->start_date, $this->end_date, $this->methode_name]);
        $cashs = CashReport::all();
        return view('livewire.reports.mutation-cash-report', ['cashs' => $cashs]);
        return (new MutationCashExport())->download('cash_transfer.xlsx');
    }

    public function filtercashtransfer()
    {

        $data = DB::select('CALL GetCashBalanceByPeriod(?, ?, ?)', [$this->start_date, $this->end_date, $this->methode_name]);
        $cashs = CashReport::all();
        return view('livewire.reports.mutation-cash-report', ['cashs' => $cashs, 'data' => $data]);
    }
}
