<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Setting\Entities\Payment;
use Illuminate\Support\Facades\DB;

class   PaymentsController extends Controller
{

    public function index()
    {
        $payments = Payment::firstOrFail();
        return view('setting::payments.index', compact('payments'));

        // $payments = Payment::all();
        // return view('setting::payments.index', [
        //     'payments' => $payments
        // ]);
    }

    // public function create() {
    //     return view('setting::units.create');
    // }

    // public function store(Request $request) {
    //     $request->validate([
    //         'name'       => 'required|string|max:255',
    //         'short_name' => 'required|string|max:255'
    //     ]);

    //     Unit::create([
    //         'name'            => $request->name,
    //         'short_name'      => $request->short_name,
    //         'operator'        => $request->operator,
    //         'operation_value' => $request->operation_value,
    //     ]);

    //     toast('Unit Created!', 'success');

    //     return redirect()->route('units.index');
    // }

    // public function edit(Unit $unit) {
    //     return view('setting::units.edit', [
    //         'unit' => $unit
    //     ]);
    // }

    public function update(Request $request)
    {
        DB::table('payments')
            ->where('code', 1)
            ->update([
                'cash'        => $request->cash        ?? 'N',
                'debitcard'   => $request->debitcard   ?? 'N',
                'gopay'       => $request->gopay       ?? 'N',
                'creditcard'  => $request->creditcard  ?? 'N',
                'ovo'         => $request->ovo         ?? 'N',
                'shopeepay'   => $request->shopeepay   ?? 'N',
                'kredivo'     => $request->kredivo     ?? 'N',
                'dana'        => $request->dana         ?? 'N',
                'grabpay'     => $request->grabpay     ?? 'N',
                'qris'        => $request->qris         ?? 'N',

            ]);

        toast('Payment Updated!', 'info');

        return redirect()->route('payment.index');
    }

    // public function destroy(Unit $unit) {
    //     $unit->delete();

    //     toast('Unit Deleted!', 'warning');

    //     return redirect()->route('units.index');
    // }
}
