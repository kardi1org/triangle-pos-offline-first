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
        //abort_if(Gate::denies('access_mejas'), 403);

        return $dataTable->render('meja::mejas.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('create_mejas'), 403);

        $outlets = \Modules\User\Entities\Outlet::all();
        return view('meja::mejas.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        //abort_if(Gate::denies('create_mejas'), 403);

        $request->validate([
            'no_meja'    => 'nullable|numeric',
            'name'       => 'required|string|max:100',
            'qty_pax'    => 'required|integer|min:1',
            'location'   => 'required|string|max:100',
            'shape'      => 'required|string|max:50',
            'position_x' => 'required|integer',
            'position_y' => 'required|integer',
            'status'     => 'required|integer',
        ]);

        $noMeja = $request->input('no_meja') ?? $this->generateUrutNumber();
        $activeOutletId = session('selected_outlet_id');

        \Modules\Meja\Entities\Meja::create([
            'outlet_id'  => $activeOutletId, // 🎯 BIND OUTLET ID
            'no_meja'    => $noMeja,
            'name'       => $request->input('name'),
            'qty_pax'    => $request->input('qty_pax'),
            'location'   => $request->input('location'),
            'shape'      => $request->input('shape'),
            'position_x' => $request->input('position_x'),
            'position_y' => $request->input('position_y'),
            'status'     => $request->input('status'),
        ]);

        toast('Table Created with Layout!', 'success');

        return redirect()->route('mejas.index');
    }

    /**
     * 🎯 MENAMPILKAN HALAMAN FLOOR PLAN DESIGNER MASSAL
     */
    public function floorPlanDesigner()
    {
        // 🎯 Bypass sementara untuk testing developer agar tidak terhadang 403
        // abort_if(Gate::denies('access_mejas'), 403);

        $activeOutletId = session('selected_outlet_id');
        $mejas = Meja::where('outlet_id', $activeOutletId)->get();

        return view('meja::mejas.layout', compact('mejas'));
    }

    /**
     * 🎯 MENYIMPAN KOORDINAT, DIMENSI, DAN ROTASI SEMUA MEJA (AJAX REQUEST)
     */
    public function saveMassLayout(Request $request)
    {
        // 🎯 REKAYASA: Jika sebelumnya Anda membypass 'access_mejas', samakan atau sesuaikan di sini
        // abort_if(Gate::denies('edit_mejas'), 403);

        $request->validate([
            'mejas'              => 'required|array',
            'mejas.*.id'         => 'nullable|integer', // Ditambahkan validasi id meja jika ada
            'mejas.*.is_new'      => 'required|boolean',
            'mejas.*.no_meja'     => 'nullable',
            'mejas.*.name'        => 'required|string|max:100',
            'mejas.*.qty_pax'     => 'required|integer',
            'mejas.*.location'    => 'required|string',
            'mejas.*.shape'       => 'required|string',
            'mejas.*.position_x'  => 'required|integer',
            'mejas.*.position_y'  => 'required|integer',
            'mejas.*.width'       => 'required|integer|min:1',
            'mejas.*.height'      => 'required|integer|min:1',
            'mejas.*.rotation'    => 'required|integer',
            'deleted_ids'         => 'nullable|array',   // Validasi array ID meja yang dihapus dari frontend
            'deleted_ids.*'       => 'integer'
        ]);

        $mejasData = $request->input('mejas');
        $deletedIds = $request->input('deleted_ids', []); // Tangkap ID yang dihapus (default array kosong jika tidak ada)
        $activeOutletId = session('selected_outlet_id');

        // Jika session outlet hilang di level tenant, kembalikan response JSON
        if (!$activeOutletId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi outlet tidak ditemukan. Silakan pilih outlet terlebih dahulu.'
            ], 422);
        }

        DB::transaction(function () use ($mejasData, $deletedIds, $activeOutletId) {

            // 1. PROSES DELETE: Hapus meja-meja lama yang ditiadakan dari layout
            if (!empty($deletedIds)) {
                Meja::whereIn('id', $deletedIds)
                    ->where('outlet_id', $activeOutletId)
                    ->delete();
            }

            // 2. PROSES UPSERT (CREATE / UPDATE MEJA)
            foreach ($mejasData as $data) {
                if ($data['is_new'] === true || $data['is_new'] === 'true') {
                    // --- OPSI BARU (CREATE) ---
                    $noMeja = !empty($data['no_meja']) ? $data['no_meja'] : $this->generateUrutNumber();

                    Meja::create([
                        'outlet_id'  => $activeOutletId,
                        'no_meja'    => $noMeja,
                        'name'       => $data['name'],
                        'qty_pax'    => $data['qty_pax'],
                        'location'   => $data['location'],
                        'shape'      => $data['shape'],
                        'position_x' => $data['position_x'],
                        'position_y' => $data['position_y'],
                        'width'      => $data['width'],
                        'height'     => $data['height'],
                        'rotation'   => $data['rotation'],
                        'status'     => 1
                    ]);
                } else {
                    // --- OPSI MEJA LAMA (UPDATE TOTAL) ---
                    // Pastikan $data['id'] tersedia sebelum melakukan update
                    if (!empty($data['id'])) {
                        Meja::where('id', $data['id'])
                            ->where('outlet_id', $activeOutletId)
                            ->update([
                                'no_meja'    => $data['no_meja'], // Ditambahkan agar no_meja hasil edit ikut terupdate
                                'name'       => $data['name'],    // Ditambahkan agar nama hasil edit ikut terupdate
                                'qty_pax'    => $data['qty_pax'], // Ditambahkan agar jumlah pax hasil edit ikut terupdate
                                'location'   => $data['location'], // Ditambahkan agar lokasi hasil edit ikut terupdate
                                'shape'      => $data['shape'],   // Ditambahkan agar bentuk hasil edit ikut terupdate
                                'position_x' => $data['position_x'],
                                'position_y' => $data['position_y'],
                                'width'      => $data['width'],
                                'height'     => $data['height'],
                                'rotation'   => $data['rotation'],
                            ]);
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Floor plan layout processed safely!'
        ]);
    }

    public function show(Meja $meja)
    {
        //abort_if(Gate::denies('show_mejas'), 403);

        return view('meja::mejas.show', compact('meja'));
    }

    public function edit(Meja $meja)
    {
        //abort_if(Gate::denies('edit_mejas'), 403);

        return view('meja::mejas.edit', compact('meja'));
    }

    public function update(Request $request, Meja $meja)
    {
        //abort_if(Gate::denies('update_mejas'), 403);

        $meja->update([
            'name'     => $request->input('meja_name'),
            'qty_pax'  => $request->input('qtypack'),
            'location' => $request->input('lokasi'),
            'shape'    => $request->input('bentuk')
        ]);

        toast('Table Updated!', 'info');

        return redirect()->route('mejas.index');
    }

    public function destroy(Meja $meja)
    {
        //abort_if(Gate::denies('delete_mejas'), 403);

        $meja->delete();

        toast('Table Deleted!', 'warning');

        return redirect()->route('mejas.index');
    }

    public function generateUrutNumber(): string
    {
        return DB::transaction(function () {
            $activeOutletId = session('selected_outlet_id');
            $lastSale = Meja::where('outlet_id', $activeOutletId)
                ->orderBy('no_meja', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastSale) {
                $lastNumber = (int) substr($lastSale->no_meja, -2);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            return str_pad($newNumber, 2, '0', STR_PAD_LEFT);
        });
    }
}
