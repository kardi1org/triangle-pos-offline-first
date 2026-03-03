<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Setting\Entities\OrderSummarySetting;
use Illuminate\Support\Facades\Gate;

class OrderSummaryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('access_settings'), 403);

        $settings = OrderSummarySetting::all();
        return view('setting::summary.index', compact('settings'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('access_settings'), 403);

        $request->validate([
            'feature_name'        => 'required|string|max:255',
            'tax_position'        => 'required|in:before,after',
            'default_value'       => 'required|numeric',
            'formula_description' => 'nullable|string'
        ]);

        $setting = OrderSummarySetting::findOrFail($id);

        $setting->update([
            'feature_name'        => $request->feature_name,
            'tax_position'        => $request->tax_position,
            'default_value'       => $request->default_value,
            'formula_description' => $request->formula_description,
            'is_active'           => $request->has('is_active') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Konfigurasi ' . $setting->feature_name . ' berhasil diperbarui!');
    }
}
