<?php

namespace Modules\ServiceCharge\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ServiceCharge\Entities\ServiceCharge;

class ServiceChargeController extends Controller
{
    public function index()
    {
        // Ambil semua data service charge
        $service_charges = ServiceCharge::all();
        return view('servicecharge::index', compact('service_charges'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'percentage' => 'required|numeric',
            'calculation_type' => 'required'
        ]);

        // Aturan: Hanya boleh ada 1 Service Charge yang aktif
        if ($request->is_active == 1) {
            ServiceCharge::where('is_active', true)->update(['is_active' => false]);
        }

        ServiceCharge::create([
            'name' => $request->name,
            'percentage' => $request->percentage,
            'calculation_type' => $request->calculation_type,
            'is_active' => $request->is_active,
        ]);

        return back()->with('success', 'Konfigurasi Service Charge berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'percentage' => 'required|numeric',
            'calculation_type' => 'required'
        ]);

        $serviceCharge = ServiceCharge::findOrFail($id);

        // Jika yang diedit ini diaktifkan, non-aktifkan yang lain
        if ($request->is_active == 1) {
            ServiceCharge::where('id', '!=', $id)->update(['is_active' => false]);
        }

        $serviceCharge->update([
            'name' => $request->name,
            'percentage' => $request->percentage,
            'calculation_type' => $request->calculation_type,
            'is_active' => $request->is_active,
        ]);

        return back()->with('success', 'Data Service Charge berhasil diperbarui!');
    }
}
