<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Expense\DataTables\CashTransfersDataTable;
use Modules\Expense\Entities\CashTransfer;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

class TunaiTransferController extends Controller
{

    public function index(CashTransfersDataTable $dataTable)
    {
        abort_if(Gate::denies('access_expenses'), 403);

        //return $dataTable->render('expense::expenses.index');
        return $dataTable->render('expense::cashtransfers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_expenses'), 403);

        return view('expense::cashtransfers.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_expenses'), 403);

        /* $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'category_id' => 'required',
            'amount' => 'required|numeric|max:2147483647',
            'details' => 'nullable|string|max:1000'
        ]); */

        CashTransfer::create([
            'date' => $request->date,
            'reference' => $this->generateCashTransferNumber(),
            'receive_type_transferfrom' => $request->receive_type_from,  //$request->methode_name,
            'amount_transferfrom' => $request->input('amount_transfer_from'),
            'amount_change' => $request->input('amount_charge'),
            'receive_type_transferto' => $request->receive_type_to,  //$request->methode_name,
            'amount_transferto' => $request->input('amount_transfer_to'),
            'trans_type' => $request->input('transtype'),
            'details' => $request->details
        ]);

        toast('Cash Transfer Created!', 'success');

        //return redirect()->route('cashtransfers.index');
        return redirect()->route('cashtransfer.index');
    }


    public function edit($id)
    {
        abort_if(Gate::denies('edit_expenses'), 403);

        // Ambil data cashtransfer secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
        $expense = CashTransfer::findOrFail($id);

        return view('expense::cashtransfers.edit', compact('expense'));
    }


    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('edit_expenses'), 403);

        /* $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'category_id' => 'required',
            'amount' => 'required|numeric|max:2147483647',
            'details' => 'nullable|string|max:1000'
        ]); */

        // Ambil data cashtransfer secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
        $expense = CashTransfer::findOrFail($id);

        $expense->update([
            'date' => $request->date,
            'reference' => $request->reference,
            'receive_type_transferfrom' => $request->receive_type_from,
            'amount_transferfrom' => $request->input('amount_transfer_from'),
            'amount_change' => $request->input('amount_charge'),
            'receive_type_transferto' => $request->receive_type_to,
            'amount_transferto' => $request->input('amount_transfer_to'),
            'trans_type' => $request->input('transtype'),
            'details' => $request->details
        ]);

        toast('Cash Transfer Updated!', 'info');

        //return redirect()->route('cashtransfers.index');
        return redirect()->route('cashtransfer.index');
    }


    public function destroy($id)
    {
        abort_if(Gate::denies('delete_expenses'), 403);

        // Ambil data cashtransfer secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
        $expense = CashTransfer::findOrFail($id);
        $expense->delete();

        toast('Cash Transfer Deleted!', 'warning');

        //return redirect()->route('cashtransfers.index');
        return redirect()->route('cashtransfer.index');
    }

    public function generateCashTransferNumber(): string
    {
        // Mulai transaksi database untuk mencegah race condition
        return DB::transaction(function () {
            $prefix = 'CTR/' . date('Ym') . '/';

            // Cari order terakhir untuk bulan dan tahun ini dengan lock
            // lockForUpdate() akan mencegah baris lain membaca record ini sampai transaksi selesai
            $lastOrder = CashTransfer::where('reference', 'like', $prefix . '%')
                ->orderBy('reference', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastOrder) {
                // Ambil nomor urut dari nomor order terakhir
                // Contoh: dari "DO/202510/0005", kita ambil "0005"
                $lastNumber = (int) substr($lastOrder->reference, -4);
                $newNumber = $lastNumber + 1;
            } else {
                // Ini adalah order pertama di bulan ini
                $newNumber = 1;
            }

            // Format nomor baru dengan padding 4 digit
            $paddedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            return $prefix . $paddedNumber;
        });
    }
}
