<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\Setting\Entities\Recipe;
use Modules\Setting\Entities\RecipeDetail;
use Modules\Purchase\Entities\PurchaseDetail;
use Modules\Adjustment\Entities\AdjustedProduct;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with(['product', 'details'])->get();
        return view('setting::recipes.index', compact('recipes'));
    }

    public function create()
    {
        // 🎯 Mengambil data produk dari database tenant yang sedang aktif saat ini
        $products = Product::latest()->get();
        return view('setting::recipes.create', compact('products'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|unique:recipes,product_id',
            'quantity' => 'required|numeric',
            'unit' => 'required',
            'ingredient_id' => 'required|array',
            'ing_qty' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $recipe = Recipe::create([
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
                'unit'       => $request->unit,
            ]);

            foreach ($request->ingredient_id as $key => $ing_id) {
                RecipeDetail::create([
                    'recipe_id'  => $recipe->id,
                    'product_id' => $ing_id,
                    'quantity'   => $request->ing_qty[$key],
                    'unit'       => $request->ing_unit[$key],
                    'cost'       => $request->ing_cost[$key],
                ]);
            }

            DB::commit();
            return redirect()->route('recipes.index')->with('success', 'Recipe created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function show(Recipe $recipe)
    {
        return view('setting::recipes.show', compact('recipe'));
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete(); // Detail akan terhapus jika menggunakan onDelete cascade di migration
        return redirect()->route('recipes.index')->with('success', 'Recipe deleted successfully!');
    }

    /**
     * AJAX Helper: Mendapatkan data unit dan harga modal terakhir dari tenant aktif
     */
    public function getProductData($id)
    {
        try {
            // 🎯 Mencari produk di database tenant aktif
            $product = Product::select('id', 'product_unit', 'product_cost')->find($id);

            if (!$product) {
                return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan'], 404);
            }

            // 🎯 Ambil harga beli terakhir dari tabel purchase detail milik tenant aktif (Tanpa hardcode db_pos)
            $cost = PurchaseDetail::join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
                ->where('purchases.status', 'Completed')
                ->where('purchase_details.product_id', $id)
                ->latest('purchase_details.created_at')
                ->value('unit_price');

            // Jika masih tidak ada di pembelian, ambil dari harga modal default master produk tenant
            if (!$cost) {
                $cost = $product->product_cost ?? 0;
            }

            return response()->json([
                'status' => 'success',
                'unit'   => $product->product_unit ?? '-',
                'cost'   => (float)$cost
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        // 🎯 1. Cari data resep murni dari DB Tenant aktif menggunakan ID biasa
        $recipe = Recipe::with(['details'])->findOrFail($id);

        // 🎯 2. Ambil produk murni dari DB Tenant yang aktif saat ini
        $products = \Modules\Product\Entities\Product::latest()->get();

        return view('setting::recipes.edit', compact('recipe', 'products'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'product_id' => 'required|unique:recipes,product_id,' . $recipe->id,
            'quantity' => 'required|numeric',
            'ingredient_id' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // Update Header
            $recipe->update([
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
                'unit'       => $request->unit,
            ]);

            // Hapus detail lama dan ganti dengan yang baru (Sync manual)
            $recipe->details()->delete();

            foreach ($request->ingredient_id as $key => $ing_id) {
                $recipe->details()->create([
                    'product_id' => $ing_id,
                    'quantity'   => $request->ing_qty[$key],
                    'unit'       => $request->ing_unit[$key],
                    'cost'       => $request->ing_cost[$key],
                ]);
            }

            DB::commit();
            return redirect()->route('recipes.index')->with('success', 'Recipe updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }
}
