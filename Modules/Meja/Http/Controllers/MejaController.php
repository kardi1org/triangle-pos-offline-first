<?php

namespace Modules\Meja\Http\Controllers;

use Modules\Meja\DataTables\MejasDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Meja\Entities\Meja;
use Illuminate\Support\Facades\DB;


class MejaController extends Controller
{

    public function index(MejasDataTable $dataTable)
    {
        abort_if(Gate::denies('access_mejas'), 403);

        return $dataTable->render('meja::mejas.index');
    }


    public function create()
    {
        abort_if(Gate::denies('create_mejas'), 403);

        return view('meja::mejas.create');
    }


    public function store(Request $request)
    {
        abort_if(Gate::denies('create_mejas'), 403);

        /* $request->validate([
            'name'  => 'required|string|max:255',
            'qty_pack' => 'required|max:255',
            'lokasi' => 'required|string|max:255',
            'bentuk'           => 'required|string|max:255',
            'status'        => 'required|string|max:255',
        ]); */

        Meja::create([
            'no_meja' => $this->generateUrutNumber(),
            'name'     => $request->input('meja_name'),
            'qty_pax' => $request->input('qtypack'),
            'location'   => $request->input('lokasi'),
            'shape'   => $request->input('bentuk'),
            'status'   => '0'
        ]);

        toast('Table Created!', 'success');

        return redirect()->route('mejas.index');
    }


    public function show(Meja $meja)
    {
        abort_if(Gate::denies('show_mejas'), 403);

        return view('meja::mejas.show', compact('meja'));
    }


    public function edit(Meja $meja)
    {
        abort_if(Gate::denies('edit_mejas'), 403);

        return view('meja::mejas.edit', compact('meja'));
    }


    public function update(Request $request, Meja $meja)
    {
        abort_if(Gate::denies('update_mejas'), 403);

        /* $request->validate([
            'name'  => 'required|string|max:255',
            'qty_pack' => 'required|max:255',
            'lokasi' => 'required|string|max:255',
            'bentuk'           => 'required|string|max:255',
            'status'        => 'required|string|max:255',
        ]); */

        $meja->update([
            'name'     => $request->input('meja_name'),
            'qty_pax' => $request->input('qtypack'),
            'location'   => $request->input('lokasi'),
            'shape'   => $request->input('bentuk')
        ]);

        toast('Table Updated!', 'info');

        return redirect()->route('mejas.index');
    }


    public function destroy(Meja $meja)
    {
        abort_if(Gate::denies('delete_mejas'), 403);

        $meja->delete();

        toast('Table Deleted!', 'warning');

        return redirect()->route('mejas.index');
    }

    public function generateUrutNumber(): string
    {
        // Mulai transaksi database untuk mencegah race condition
        return DB::transaction(function () {
            // $prefix = '0';

            // Cari order terakhir untuk bulan dan tahun ini dengan lock
            // lockForUpdate() akan mencegah baris lain membaca record ini sampai transaksi selesai
            //$lastSale = Meja::where('no_meja', '=', $prefix)
            $lastSale = Meja::orderBy('no_meja', 'desc')
                // ->orderBy('no_meja', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastSale) {
                // Ambil nomor urut dari nomor order terakhir
                $lastNumber = (int) substr($lastSale->no_meja, -2);
                $newNumber = $lastNumber + 1;
            } else {
                // Ini adalah order pertama di bulan ini
                $newNumber = 1;
            }
            // Format nomor baru dengan padding 2 digit
            $paddedNumber = str_pad($newNumber, 2, '0', STR_PAD_LEFT);

            //   return $prefix . $paddedNumber;
            return $paddedNumber;
        });
    }
}
