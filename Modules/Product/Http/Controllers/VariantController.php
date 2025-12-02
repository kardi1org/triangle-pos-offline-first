<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Product\Entities\Variant;

class VariantController extends Controller
{
    public function listByProduct($productId)
    {
        try {
            $variants = Variant::where('product_id', $productId)
                ->select('id', 'variant_name')
                ->get();

            return response()->json($variants);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
