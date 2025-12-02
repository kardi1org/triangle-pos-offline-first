<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Upload\Entities\Upload;
use Illuminate\Support\Facades\Gate;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Variant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Renderable;
use Modules\Product\DataTables\ProductDataTable;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;


class ProductController extends Controller
{

    public function index(ProductDataTable $dataTable)
    {
        abort_if(Gate::denies('access_products'), 403);

        return $dataTable->render('product::products.index');
    }


    public function create()
    {
        abort_if(Gate::denies('create_products'), 403);

        return view('product::products.create');
    }

    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();

            // 🔹 Simpan produk utama
            $product = Product::create($request->except(['document', 'variant_name']));

            // 🔹 Simpan gambar (jika ada)
            if ($request->has('document')) {
                foreach ($request->input('document', []) as $file) {
                    $product->addMedia(Storage::path('temp/dropzone/' . $file))
                        ->toMediaCollection('images');
                }
            }

            // 🔹 Simpan variant (jika ada)
            if ($request->has('variant_name')) {
                // Ambil daftar nama varian unik dari input (case-insensitive)
                $uniqueNames = collect($request->variant_name)
                    ->filter(fn ($name) => !empty(trim($name)))      // hapus yang kosong
                    ->map(fn ($name) => trim($name))                 // trim spasi
                    ->unique(fn ($name) => strtolower($name));       // hilangkan duplikat di input

                foreach ($uniqueNames as $variantName) {
                    // Cek apakah varian dengan nama sama sudah ada (case-insensitive)
                    $exists = \Modules\Product\Entities\Variant::where('product_id', $product->id)
                        ->whereRaw('LOWER(variant_name) = ?', [strtolower($variantName)])
                        ->exists();

                    // Jika belum ada, buat baru
                    if (!$exists) {
                        \Modules\Product\Entities\Variant::create([
                            'product_id'   => $product->id,
                            'variant_name' => $variantName,
                        ]);
                    }
                }
            }

            DB::commit();

            toast('Product and Variants Created!', 'success');
            return redirect()->route('products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('show_products'), 403);
        return view('product::products.show', compact('product'));
    }


    public function edit(Product $product)
    {
        abort_if(Gate::denies('edit_products'), 403);
        return view('product::products.edit', compact('product'));
    }


    public function update(UpdateProductRequest $request, Product $product)
    {
        // Update data utama produk
        $product->update($request->except('document', 'variant_name', 'variant_id'));

        // === Update gambar ===
        $documents = $request->input('document', []);

        // Cek apakah user hapus semua gambar atau ganti gambar baru
        if ($request->has('document')) {

            // Jika user menghapus semua gambar (tidak ada input document)
            if (empty($documents)) {
                // Hapus semua gambar lama
                $product->clearMediaCollection('images');
            } else {
                // Cek apakah ada file baru di folder temp (berarti user ganti gambar)
                $hasNewFiles = collect($documents)->some(function ($file) {
                    return file_exists(Storage::path('temp/dropzone/' . $file));
                });

                if ($hasNewFiles) {
                    // Hapus gambar lama dan tambahkan yang baru
                    $product->clearMediaCollection('images');

                    foreach ($documents as $file) {
                        $filePath = Storage::path('temp/dropzone/' . $file);
                        if (file_exists($filePath)) {
                            $product->addMedia($filePath)->toMediaCollection('images');
                        }
                    }
                }
                // Jika tidak ada file baru dan hanya gambar lama → jangan ubah apa pun
            }
        }

        toast('Product Updated!', 'success');
        return redirect()->route('products.index');
    }


    public function destroy(Product $product)
    {
        abort_if(Gate::denies('delete_products'), 403);

        $product->delete();

        toast('Product Deleted!', 'warning');

        return redirect()->route('products.index');
    }

    public function saveVariants(Request $request, $id)
    {
        $product = \Modules\Product\Entities\Product::findOrFail($id);
        $variants = $request->input('variants', []);

        $sentIds = []; // untuk melacak id yang dipertahankan
        $processedNames = []; // untuk memastikan variant_name unik dalam request

        foreach ($variants as $variantData) {
            $name = trim($variantData['name'] ?? '');
            $variantId = $variantData['id'] ?? null;

            // Skip nama kosong
            if ($name === '') {
                continue;
            }

            // Skip jika sudah pernah diproses (nama sama dalam request)
            if (in_array(strtolower($name), $processedNames)) {
                continue;
            }

            $processedNames[] = strtolower($name);

            // Cek apakah sudah ada variant dengan nama sama di database
            $existing = $product->variants()
                ->whereRaw('LOWER(variant_name) = ?', [strtolower($name)])
                ->first();

            if ($existing) {
                // Jika ada, update (kalau beda ID) dan simpan ID-nya
                if ($variantId && $existing->id !== $variantId) {
                    $existing->update(['variant_name' => $name]);
                }
                $sentIds[] = $existing->id;
            } else {
                // Jika belum ada, buat baru
                $new = $product->variants()->create([
                    'variant_name' => $name,
                ]);
                $sentIds[] = $new->id;
            }
        }

        // Hapus variant yang tidak dikirim
        $product->variants()
            ->whereNotIn('id', $sentIds)
            ->delete();

        return response()->json([
            'success' => true,
            'variants' => $product->variants()->get(['id', 'variant_name']),
        ]);
    }
}
