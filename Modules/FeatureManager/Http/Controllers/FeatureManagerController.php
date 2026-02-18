<?php

namespace Modules\FeatureManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\FeatureManager\Entities\FeatureManagement;

class FeatureManagerController extends Controller
{
    public function index()
    {
        // Mengambil semua data dan mengelompokkan berdasarkan kolom feature_group
        $features = \Modules\FeatureManager\Entities\FeatureManagement::all()->groupBy('feature_group');

        return view('featuremanager::index', compact('features'));
    }

    public function updatePermission(Request $request)
    {
        $feature = FeatureManagement::findOrFail($request->id);
        $packageField = 'package_' . $request->package_num; // package_1, 2, atau 3

        $feature->update([
            $packageField => $request->status
        ]);

        return response()->json(['success' => true, 'message' => 'Status Updated!']);
    }
}
