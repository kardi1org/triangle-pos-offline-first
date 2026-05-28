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

    public function stockCardReport(Request $request)
    {
        $warehouses = \Modules\Setting\Entities\Warehouse::all();
        $products = \Modules\Product\Entities\Product::all();

        $movements = collect();
        $stock_awal = 0;

        if ($request->filled(['start_date', 'end_date', 'warehouse_id', 'product_id'])) {
            $warehouse_id = $request->warehouse_id;
            $product_id = $request->product_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            // --- 1. HITUNG STOCK AWAL (Sebelum start_date) ---

            $prev_purchase = \Modules\Purchase\Entities\PurchaseDetail::whereHas('purchase', function ($q) use ($warehouse_id, $start_date) {
                $q->where('warehouse_id', $warehouse_id)->where('date', '<', $start_date)->where('status', 'Completed');
            })->where('product_id', $product_id)->sum('quantity');

            $prev_purchase_return = \Modules\PurchasesReturn\Entities\PurchaseReturnDetail::whereHas('purchaseReturn', function ($q) use ($warehouse_id, $start_date) {
                $q->where('warehouse_id', $warehouse_id)->where('date', '<', $start_date)->where('status', 'Completed');
            })->where('product_id', $product_id)->sum('quantity');

            $prev_adjustment = \Modules\Adjustment\Entities\AdjustedProduct::whereHas('adjustment', function ($q) use ($warehouse_id, $start_date) {
                $q->where('warehouse_id', $warehouse_id)->where('date', '<', $start_date);
            })->where('product_id', $product_id)
                ->selectRaw("SUM(CASE WHEN type = 'add' THEN quantity ELSE -quantity END) as total")
                ->value('total');

            // Tambahan: Stock Awal dari Work Order (Produk Jadi - Masuk)
            $prev_wo_in = \Modules\Production\Entities\WorkOrder::where('product_id', $product_id)
                ->where('warehouse_id', $warehouse_id)
                ->where('date', '<', $start_date)
                ->sum('quantity');

            // Tambahan: Stock Awal dari Work Order (Bahan Baku - Keluar)
            $prev_wo_out = \Modules\Production\Entities\WorkOrderDetail::whereHas('workOrder', function ($q) use ($warehouse_id, $start_date) {
                $q->where('warehouse_id', $warehouse_id)->where('date', '<', $start_date);
            })->where('product_id', $product_id)->sum('quantity');

            // 🎯 TAMBAHAN: Stock Awal dari Sales (Status Completed - Mengurangi Stok)
            $prev_sales = \Modules\Sale\Entities\SaleDetails::whereHas('sale', function ($q) use ($warehouse_id, $start_date) {
                $q->where('warehouse_id', $warehouse_id)->where('date', '<', $start_date)->where('status', 'Completed');
            })->where('product_id', $product_id)->sum('quantity');

            // Hitung akumulasi stock awal murni (Sales mengurangi stock)
            $stock_awal = $prev_purchase - $prev_purchase_return + ($prev_adjustment ?? 0) + $prev_wo_in - $prev_wo_out - $prev_sales;


            // --- 2. AMBIL MUTASI TRANSAKSI (Dalam Periode Tanggal) ---

            // A. Ambil Transaksi (Purchase)
            \Modules\Purchase\Entities\PurchaseDetail::with('purchase')
                ->whereHas('purchase', function ($q) use ($warehouse_id, $start_date, $end_date) {
                    $q->where('warehouse_id', $warehouse_id)->whereBetween('date', [$start_date, $end_date])->where('status', 'Completed');
                })->where('product_id', $product_id)->get()->each(function ($item) use ($movements) {
                    $movements->push([
                        'date' => $item->purchase->date,
                        'ref'  => $item->purchase->reference,
                        'type' => 'Purchase',
                        'in'   => $item->quantity,
                        'out'  => 0
                    ]);
                });

            // B. Ambil Transaksi (Purchase Return)
            \Modules\PurchasesReturn\Entities\PurchaseReturnDetail::with('purchaseReturn')
                ->whereHas('purchaseReturn', function ($q) use ($warehouse_id, $start_date, $end_date) {
                    $q->where('warehouse_id', $warehouse_id)->whereBetween('date', [$start_date, $end_date])->where('status', 'Completed');
                })->where('product_id', $product_id)->get()->each(function ($item) use ($movements) {
                    $movements->push([
                        'date' => $item->purchaseReturn->date,
                        'ref'  => $item->purchaseReturn->reference,
                        'type' => 'Purchase Return',
                        'in'   => 0,
                        'out'  => $item->quantity
                    ]);
                });

            // C. Ambil Transaksi (Adjustment)
            \Modules\Adjustment\Entities\AdjustedProduct::with('adjustment')
                ->whereHas('adjustment', function ($q) use ($warehouse_id, $start_date, $end_date) {
                    $q->where('warehouse_id', $warehouse_id)->whereBetween('date', [$start_date, $end_date]);
                })->where('product_id', $product_id)->get()->each(function ($item) use ($movements) {
                    $movements->push([
                        'date' => $item->adjustment->date,
                        'ref'  => $item->adjustment->reference,
                        'type' => 'Adjustment (' . ucfirst($item->type) . ')',
                        'in'   => $item->type == 'add' ? $item->quantity : 0,
                        'out'  => $item->type == 'sub' ? $item->quantity : 0
                    ]);
                });

            // D. Ambil Transaksi Work Order (Output / IN)
            \Modules\Production\Entities\WorkOrder::where('product_id', $product_id)
                ->where('warehouse_id', $warehouse_id)
                ->whereBetween('date', [$start_date, $end_date])
                ->get()->each(function ($item) use ($movements) {
                    $movements->push([
                        'date' => $item->date,
                        'ref'  => $item->reference,
                        'type' => 'Work Order (Output)',
                        'in'   => $item->quantity,
                        'out'  => 0
                    ]);
                });

            // E. Ambil Transaksi Work Order (Material / OUT)
            \Modules\Production\Entities\WorkOrderDetail::with('workOrder')
                ->whereHas('workOrder', function ($q) use ($warehouse_id, $start_date, $end_date) {
                    $q->where('warehouse_id', $warehouse_id)->whereBetween('date', [$start_date, $end_date]);
                })
                ->where('product_id', $product_id)
                ->get()->each(function ($item) use ($movements) {
                    if ($item->workOrder) {
                        $movements->push([
                            'date' => $item->workOrder->date,
                            'ref'  => $item->workOrder->reference,
                            'type' => 'Work Order (Material)',
                            'in'   => 0,
                            'out'  => $item->quantity
                        ]);
                    }
                });

            // 🎯 F. TAMBAHAN: Ambil Transaksi Sales (Status Completed / OUT)
            \Modules\Sale\Entities\SaleDetails::with('sale')
                ->whereHas('sale', function ($q) use ($warehouse_id, $start_date, $end_date) {
                    $q->where('warehouse_id', $warehouse_id)->whereBetween('date', [$start_date, $end_date])->where('status', 'Completed');
                })
                ->where('product_id', $product_id)
                ->get()->each(function ($item) use ($movements) {
                    if ($item->sale) {
                        $movements->push([
                            'date' => $item->sale->date,
                            'ref'  => $item->sale->reference,
                            'type' => 'Sale',
                            'in'   => 0,
                            'out'  => $item->quantity
                        ]);
                    }
                });

            // Urutkan pergerakan berdasarkan tanggal secara kronologis
            $movements = $movements->sortBy('date');
        }

        return view('reports::stock-card.index', compact('warehouses', 'products', 'movements', 'stock_awal'));
    }
}
