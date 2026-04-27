<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Setting\Entities\Warehouse;

class WarehouseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Cari email admin tetap dari tabel users di database utama (db_pos)
        // Kita gunakan koneksi 'db_pos' agar mencari di tabel users yang menjadi master
        $adminEmail = DB::connection('db_pos')->table('users')
            ->where('tenant_database', $user->tenant_database)
            ->where('level', 'admin')
            ->value('email');

        // 2. Ambil daftar outlet dari db_pos berdasarkan email admin tersebut
        $outlets = [];
        if ($adminEmail) {
            $outlets = DB::connection('db_pos')
                ->table('outlets')
                ->where('email', $adminEmail)
                ->get();
        }

        $warehouses = Warehouse::latest()->get();

        return view('setting::warehouses.index', compact('warehouses', 'outlets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:255|unique:warehouses,code',
            'outlet_id' => 'required' // Pastikan outlet dipilih
        ]);

        Warehouse::create([
            'name'      => $request->name,
            'code'      => $request->code,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'outlet_id' => $request->outlet_id, // Simpan outlet_id
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse created successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:255|unique:warehouses,code,' . $id,
            'outlet_id' => 'required'
        ]);

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update([
            'name'      => $request->name,
            'code'      => $request->code,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'outlet_id' => $request->outlet_id,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully!');
    }

    public function destroy($id)
    {
        Warehouse::findOrFail($id)->delete();
        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully!');
    }
}
