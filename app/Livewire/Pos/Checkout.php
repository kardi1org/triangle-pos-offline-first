<?php

namespace App\Livewire\Pos;

use Modules\Meja\Entities\Meja;
use Livewire\Component;
//use App\Livewire\Pos\StorePosOrderRequest;
//use App\Modules\Order\Http\Requests\StorePosOrderRequest;
//use App\Order\Http\Requests\StorePosOrderRequest;
use Mike42\Escpos\Printer;
use Livewire\Attributes\On;
use Illuminate\Http\Request;
use Modules\Sale\Entities\Sale;
use Modules\Order\Entities\Order;
use Illuminate\Support\Facades\DB;
//use App\Http\Controllers\PosOrderController;
//use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Modules\Product\Entities\Product;
use Modules\Sale\Entities\SaleDetails;
use Modules\Order\Entities\OrderDetails;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Modules\Sale\Resource\Views\Prints;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use App\Modules\Order\Http\Controllers\PosOrderController;
use Modules\Order\Http\Controllers\PosController\StorePosOrderRequest;


class Checkout extends Component
{
    //use HasFactory;

    protected $listeners = [
        'productSelected' => 'productSelected',
        'discountModalRefresh' => 'discountModalRefresh',
        'reloadPendingOrders' => 'loadPendingOrders',
        'updateVariant' => 'updateVariant',
        'refresh-cart' => '$refresh',
    ];

    public $cart_instance;
    public $customers;
    public $global_discount;
    public $global_tax;
    public $shipping;
    public $quantity;
    public $check_quantity;
    public $discount_type;
    public $item_discount;
    public $data;
    public $customer_id;
    public $customer_name;
    public $total_amount;
    public $prevQty;
    public $cash;
    public $payments;
    public $qty1;
    public $tables;
    public $table_id;
    public $order_type = 'dine_in'; // default
    public $alertMessage = null;
    public $alertType = 'warning';
    public $current_reference = null;
    public $selectedOrder = null;
    public $selectedOrderDetails = [];
    public $selectedOrderSummary = [];
    public $cartItems = [];

    public $item_variant = [];
    public $item_typeOrder = [];
    public $previewOrderData = null;

    // Properties untuk menyimpan data meja yang dipilih
    public $table_ids_array = [];    // Array of selected table IDs (e.g., [1, 5, 8])
    public $selectedTableNames = ''; // String of selected table names (e.g., "Meja A01, Meja B12")
    public $lain_a = 0;
    public $lain_b = 0;
    public $tax_amount = 0; // Tambahkan ini
    public $service_charge = 0; // Pastikan ini juga ada
    public $service_charge_percentage = 0;

    // Properti untuk Modal Approval
    public $showApprovalModal = false;
    public $approver_username;
    public $approver_password;
    public $approval_note;
    public $items_to_approve = []; // Untuk menampilkan list apa yang berubah di modal
    public $approval_trigger;
    public $approved_by_id;
    // ✅ event listener untuk clear alert
    #[On('clear-alert')]
    public function clearAlert()
    {
        $this->reset(['alertMessage', 'alertType']);
    }
    private function refreshCartTaxAndDiscount()
    {
        Cart::instance('sale')->setGlobalTax($this->global_tax ?? 0);
        Cart::instance('sale')->setGlobalDiscount($this->global_discount ?? 0);
    }

    public function mount($cartInstance, $customers, $payments)
    {
        $this->cart_instance = $cartInstance;
        $this->customers = $customers;
        $this->global_discount = 0;
        $this->global_tax = 0;
        $this->shipping = 0.00;
        $this->lain_a = 0;
        $this->lain_b = 0;
        $this->check_quantity = [];
        $this->quantity = [];
        $this->discount_type = [];
        $this->item_discount = [];
        $this->total_amount = 0;
        $this->cash = 4000;
        $this->payments = $payments;

        $this->tables = Meja::orderBy('no_meja')->get();
        $this->order_type = 'dine_in';

        $this->approved_by_id = '';
        $this->approval_note = '';

        foreach (Cart::instance('sale')->content() as $item) {
            $this->cartItems[$item->rowId] = [
                'order_type' => $item->options->order_type ??
                    'dine_in',
                'variants' => $item->options->variants ?? // [cite: 16] <- PASTIKAN KUNCI INI SUDAH 'variants'
                    [],
            ];
        }

        // Panggil ini untuk memastikan selectedTableNames terisi saat mount
        $this->updateNameString();

        $settings = \Modules\Setting\Entities\OrderSummarySetting::where('is_active', true)->get();

        // Petakan nilai default ke properti Livewire
        foreach ($settings as $setting) {
            switch ($setting->feature_key) {
                case 'order_tax':
                    $this->global_tax = $setting->default_value;
                    break;
                case 'discount_global':
                    $this->global_discount = $setting->default_value;
                    break;
                case 'service_charge':
                    // Untuk persentase service charge, kita simpan nilainya
                    // agar calculateTotal() bisa menggunakannya
                    $this->service_charge_percentage = $setting->default_value;
                    break;
                case 'delivery_fee':
                    $this->shipping = $setting->default_value;
                    break;
                case 'lain_a':
                    $this->lain_a = $setting->default_value;
                    break;
                case 'lain_b':
                    $this->lain_b = $setting->default_value;
                    break;
            }
        }

        // Jalankan kalkulasi pertama kali agar angka langsung muncul
        $this->calculateTotal();
    }

    public function previewOrder($orderId)
    {
        // 1. Ambil data Sale
        $sale = Sale::with('saleDetails')->findOrFail($orderId);

        // --- START: LOGIKA KONVERSI ID MEJA KE NAMA MEJA ---

        // 2. 🎯 FORCE DECODE: Selalu dekode untuk memastikan ini adalah array
        // Jika casting berhasil, json_decode akan menerima array, tetapi akan mengembalikan array yang sama.
        // Jika casting GAGAL (seperti yang terjadi sekarang), ini akan memperbaiki masalah.
        $tableIds = json_decode($sale->selected_table_ids, true);

        // Fallback jika json_decode mengembalikan null (jika data benar-benar bukan JSON)
        if (!is_array($tableIds)) {
            $tableIds = [];
        }

        $mejaNameList = 'Take Away'; // Default value

        // 3. Cek apakah array ID meja valid
        if (!empty($tableIds)) {

            // 4. Ambil Nama Meja dari database
            $tableNames = Meja::whereIn('id', $tableIds)
                ->pluck('name', 'id')
                ->toArray();

            // 5. Gabungkan nama meja menjadi satu string
            $mejaNameList = collect($tableIds)
                ->map(fn ($id) => $tableNames[$id] ?? "ID: {$id}")
                ->implode(', ');
        }

        // --- AKHIR LOGIKA KONVERSI ---

        $this->previewOrderData = [
            'reference' => $sale->reference,
            'typeOrder' => $sale->order_type ?? 'Dine In',
            'customer_name' => $sale->customer_name,
            'date' => $sale->date,

            // Mengisi dengan nama yang sudah dikonversi
            'meja_name' => $mejaNameList,

            'details' => $sale->saleDetails->map(function ($detail) {
                return [
                    'product_name' => $detail->product_name,
                    'quantity' => $detail->quantity,
                    'variant_detail' => $detail->variant_detail,
                ];
            })
        ];

        // ... Dispatch Perintah ...
        $this->dispatch('blur-pending-orders-modal');
        $this->dispatch('show-kitchen-preview-modal');
        $this->dispatch('show-kitchen-preview');
    }

    // app/Http/Livewire/PosComponent.php (atau sejenisnya)

    public function getPendingOrdersProperty()
    {
        // 1. Ambil orders yang pending
        //$orders = Sale::where('status', 'pending')->get();
        // Di Class Livewire Anda

        $orders = Sale::with('kitchenLogs') // WAJIB ada ini
            ->where('status', 'Pending')
            ->get();


        // 2. Ambil semua ID meja unik dari SEMUA orders, DECODE dulu.

        $allTableIds = $orders->pluck('selected_table_ids')
            ->map(function ($ids) {
                // 🎯 BARIS PENTING: Mendekode JSON string ke array
                $decodedIds = json_decode($ids, true);

                // Memastikan hasilnya adalah array
                return is_array($decodedIds) ? $decodedIds : [];
            })
            ->flatten()
            ->unique()
            ->filter()
            ->toArray();

        // 3. Ambil Nama Meja berdasarkan ID
        $tables = Meja::whereIn('id', $allTableIds)
            ->pluck('name', 'id')
            ->toArray();

        // 4. Proses dan gabungkan nama meja
        foreach ($orders as $order) {
            // Dekode lagi untuk order spesifik jika casting gagal
            $orderTableIds = json_decode($order->selected_table_ids, true);

            if (is_array($orderTableIds)) {
                $tableNames = collect($orderTableIds)
                    ->map(fn ($id) => $tables[$id] ?? "ID: {$id} (Meja Tidak Ditemukan)")
                    ->all();

                // Simpan nama meja yang sudah diproses
                $order->table_names = $tableNames;
            } else {
                $order->table_names = [];
            }
        }

        return $orders;
    }

    #[On('updateVariant')]
    public function updateVariant($productId, $variants = [])
    {
        // Logging untuk debugging
        logger('🔥 PRODUCT ID', [$productId]);
        logger('🔥 VARIANTS', [$variants]);

        if (!$productId) {
            logger('❌ productId kosong, update dibatalkan');
            return;
        }

        // Simpan sementara ke session (optional)
        session()->put("variant_session.$productId", $variants);

        // Ambil cart instance
        $cart = Cart::instance('sale');

        // Cari item berdasarkan id product
        $cartItem = $cart->search(fn ($item) => $item->id == $productId)->first();

        logger('🛒 CART ITEM', [$cartItem ? $cartItem->toArray() : 'NOT FOUND']);

        if (!$cartItem) {
            logger('❌ CART ITEM NOT FOUND UNTUK PRODUCT ID:', [$productId]);
            return;
        }

        // Ambil options lama
        $options = $cartItem->options->toArray();

        logger('📦 OPTIONS SEBELUM UPDATE', [$options]);
        logger('📦 VARIANTS BARU', [$variants]);

        // Tambahkan variants ke options
        $options['variants'] = $variants;

        // Update cart item
        $cart->update($cartItem->rowId, [
            'qty'     => $cartItem->qty,
            'options' => $options,
        ]);

        // Ambil ulang untuk memastikan data masuk
        $after = $cart->get($cartItem->rowId)->options ?? [];

        logger('📦 OPTIONS SETELAH UPDATE', [$after]);
    }

    public function hydrate()
    {
        $this->total_amount = $this->calculateTotal();
    }

    public function render()
    {
        $cart_items = Cart::instance($this->cart_instance)->content();

        return view('livewire.pos.checkout', [
            'cart_items' => $cart_items,
            'tables' => $this->tables, // ✅ tambahkan ini
        ]);

        // Ambil semua meja untuk ditampilkan di modal
        $tables = Meja::all();

        return view('livewire.checkout-component', [
            'tables' => $tables,
        ]);
    }

    // --- Metode Pemilihan Meja ---
    public function toggleTable($tableId)
    {
        // Perubahan: Pengecekan status meja (Meja::find($tableId) dan if ($table->status == 2)) telah dihapus.
        // Logika kini hanya fokus pada pengelolaan daftar ID meja.

        $key = array_search($tableId, $this->table_ids_array);

        if ($key !== false) {
            // Meja sudah ada di daftar: Hapus ID dari array
            unset($this->table_ids_array[$key]);
        } else {
            // Meja belum ada di daftar: Tambahkan ID ke array
            $this->table_ids_array[] = $tableId;
        }

        // Merapikan ulang indeks array setelah unset (Penting untuk Livewire dan PHP)
        $this->table_ids_array = array_values($this->table_ids_array);

        // Panggil ini untuk memperbarui nama/string meja yang terlihat di input text (sesuai kode Anda)
        $this->updateNameString();
    }

    public function removeTableByIndex($index)
    {
        // Ambil ID dari array berdasarkan index yang diklik
        // Kita harus memastikan index sesuai dengan $this->table_ids_array

        // Solusi: Ambil semua ID, hilangkan ID di posisi $index
        $ids = $this->table_ids_array;
        if (isset($ids[$index])) {
            // Hapus elemen pada index tersebut
            unset($ids[$index]);
        }

        // Reset dan update array IDs
        $this->table_ids_array = array_values($ids);

        // Update string nama meja yang ditampilkan
        $this->updateNameString();
    }

    // --- Metode Update Nama Meja ---
    public function updateNameString()
    {
        if (empty($this->table_ids_array)) {
            $this->selectedTableNames = '';
            return;
        }

        // Ambil data meja berdasarkan ID yang dipilih
        $mejas = Meja::whereIn('id', $this->table_ids_array)->get();

        // Buat string nama meja (e.g., "M1, M5, M8")
        $names = $mejas->map(function ($table) {
            return $table->name ?? 'Meja ' . $table->no_meja;
        })->implode(', ');

        $this->selectedTableNames = $names;
    }

    public function proceed()
    {
        if (Cart::instance('sale')->count() == 0) {
            $this->alertMessage = 'Keranjang masih kosong!';
            return;
        }

        // 1. Cek apakah ini update dari order yang sudah ada
        if (!empty($this->current_reference)) {
            $sale = Sale::where('reference', $this->current_reference)->first();

            // TAMBAHKAN PENGECEKAN: Hanya butuh approval jika struk SUDAH DIPRINT
            if ($sale && $sale->is_printed == 1) {
                $currentCart = Cart::instance('sale')->content();
                $oldDetails = SaleDetails::where('sale_id', $sale->id)->get();
                $changes = [];

                // --- Logika Cek VOID ---
                foreach ($oldDetails as $old) {
                    $match = $currentCart->where('id', $old->product_id)->first();
                    $qtyVoid = 0;
                    $reason = '';

                    if (!$match) {
                        $qtyVoid = $old->quantity;
                        $reason = 'Dihapus dari list';
                    } elseif ($match->qty < $old->quantity) {
                        $qtyVoid = $old->quantity - $match->qty;
                        $reason = 'Pengurangan Qty';
                    }

                    if ($qtyVoid > 0) {
                        $changes[] = [
                            'name' => $old->product_name,
                            'qty' => $qtyVoid,
                            'type' => 'VOID',
                            'class' => 'badge-danger',
                            'reason' => $reason
                        ];
                    }
                }

                // --- Logika Cek NEW ---
                foreach ($currentCart as $new) {
                    $match = $oldDetails->where('product_id', $new->id)->first();
                    $qtyNew = 0;
                    $reason = '';

                    if (!$match) {
                        $qtyNew = $new->qty;
                        $reason = 'Menu Baru';
                    } elseif ($new->qty > $match->quantity) {
                        $qtyNew = $new->qty - $match->quantity;
                        $reason = 'Penambahan Qty';
                    }

                    if ($qtyNew > 0) {
                        $changes[] = [
                            'name' => $new->name,
                            'qty' => $qtyNew,
                            'type' => 'NEW',
                            'class' => 'badge-success',
                            'reason' => $reason
                        ];
                    }
                }

                // 2. Jika terdeteksi perubahan pada order yang sudah terprint, munculkan Modal Approval
                if (count($changes) > 0) {
                    $this->items_to_approve = $changes;
                    $this->approval_trigger = 'proceed';
                    $this->showApprovalModal = true;
                    return; // Berhenti (menunggu password admin)
                }
            }
        }

        // 3. Jika Order Baru, Belum Print, atau Tidak Ada Perubahan, buka checkout
        $this->dispatch('showCheckoutModal');
    }

    public function saveOrder()
    {
        if ($this->customer_name != null) {
            //  $this->dispatch('showCheckoutModal');
            //return redirect()->route('app.pos.saveorder');
            //redirect()->route('app.pos.store');
            //redirect()->route('saveorder.store');

            /*   if ($result->save()) {
                    alert()->success('Data Berhasil Disimpan ke Database.','Tersimpan!')->autoclose(4000);
                    return redirect()->route('admin.order');
                } else {
                   alert()->info('Harap Periksa lagi data Formulir anda.','Tidak Tersimpan!')->autoclose(4000);
                }

                alert()->success('Data Successfully Saved to Database','Saved !')->autoclose(4000);
//-------------------------------------------------------------------------//
           } else {
              session()->flash('message', 'Please fill in the Customer Name !'); */
            //-------------------------------------------------------------------------//
            try {
                $cart_items = Cart::instance($this->cart_instance)->content();
                return view('livewire.pos.checkout', ['cart_items' => $cart_items]);

                foreach (Cart::instance('sale')->content() as $cart_item) {
                    $data = [
                        'product_name' => $cart_item->name,
                        'product_code' => $cart_item->options->code,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                        'message' => 'Sale Created!'
                    ];
                    return response()->json($data);
                }
                //  return response()->json(['data' => $order]);
                //  return view('sales.index', compact('orders'));
                //  return redirect()->route('sales.index')->with('message', 'Data Successfully Saved to Database!');
                //  return redirect()->route('sales.cetakstruk');
                //   return view('prints.receipt_thermal', compact('orders'));
                //  return view('order::pos.index', compact('orders'));
                //  return redirect()->route('show.showorder')->with('message', 'Data Order Successfully Saved to Database!');
                //   redirect()->route('save.saveorder');

            } catch (ValidationException $err) {
                return response()->json([
                    'status' => 422,
                    'msg' => 'error',
                    'errors' => $err->errors(),
                ], 422);
            }
            //-------------------------------------------------------------------------//
        } else {
            session()->flash('message', 'Please fill in the Customer Name !');
        }
    }

    public function getdatacart()
    {
        if ($this->customer_name != null) {

            try {
                $cart_items = Cart::instance($this->cart_instance)->content();
                return view('livewire.pos.checkout', ['cart_items' => $cart_items]);

                foreach (Cart::instance('sale')->content() as $cart_item) {
                    $data = [
                        'product_name' => $cart_item->name,
                        'product_code' => $cart_item->options->code,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                        'message' => 'Sale Created!'
                    ];
                    return response()->json($data);
                }
            } catch (ValidationException $err) {
                return response()->json([
                    'status' => 422,
                    'msg' => 'error',
                    'errors' => $err->errors(),
                ], 422);
            }
        } else {
            session()->flash('message', 'Please fill in the Customer Name !');
        }
    }

    // Di dalam class Livewire Anda

    public function updatedOrderType($value)
    {
        // Setiap kali tombol Dine In / Take Out diklik, hitung ulang total
        $this->total_amount = $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $settings = \Modules\Setting\Entities\OrderSummarySetting::where('is_active', true)->get();

        // 1. Ambil data service charge terbaru
        $scData = \Modules\ServiceCharge\Entities\ServiceCharge::where('is_active', 1)->first();
        $sc_percent = $scData->percentage ?? 0;
        $sc_type = $scData->calculation_type ?? 1; // 1: Gross, 2: Netto

        // 2. Hitung Pure Subtotal
        $pure_subtotal = 0;
        foreach (Cart::instance('sale')->content() as $cartItem) {
            $pure_subtotal += ($cartItem->price * $cartItem->qty);
        }

        $tax_base = $pure_subtotal;
        $after_tax_charges = 0;
        $this->service_charge = 0; // Reset nilai awal

        // 3. Loop Kalkulasi
        foreach ($settings as $setting) {
            $current_value = 0;

            switch ($setting->feature_key) {
                case 'service_charge':
                    // TAMBAHKAN KONDISI isFeatureEnabled DI SINI
                    if (isFeatureEnabled('summary_service') && $this->order_type == 'dine_in') {
                        $discount_now = (float) str_replace(',', '', Cart::instance('sale')->discount());

                        if ($sc_type == \Modules\ServiceCharge\Entities\ServiceCharge::TYPE_NETTO) {
                            $current_value = ($pure_subtotal - $discount_now) * ($sc_percent / 100);
                        } else {
                            $current_value = $pure_subtotal * ($sc_percent / 100);
                        }
                        $this->service_charge = $current_value;
                    }
                    break;

                case 'delivery_fee':
                    if (isFeatureEnabled('summary_pkg')) {
                        $current_value = (float) ($this->shipping ?? 0);
                    }
                    break;

                case 'discount_global':
                    // Diskon selalu dihitung jika ada nilainya
                    $current_value = (float) str_replace(',', '', Cart::instance('sale')->discount());
                    break;

                case 'lain_a':
                    if (isFeatureEnabled('summary_others')) {
                        $current_value = (float) ($this->lain_a ?? 0);
                    }
                    break;

                case 'lain_b':
                    if (isFeatureEnabled('summary_others')) {
                        $current_value = (float) ($this->lain_b ?? 0);
                    }
                    break;
            }

            // 4. Distribusi Posisi (Before vs After Tax)
            if ($setting->tax_position == 'before') {
                if ($setting->feature_key == 'discount_global') {
                    $tax_base -= $current_value;
                } else {
                    $tax_base += $current_value;
                }
            } else {
                if ($setting->feature_key != 'order_tax') {
                    if ($setting->feature_key == 'discount_global') {
                        $after_tax_charges -= $current_value;
                    } else {
                        $after_tax_charges += $current_value;
                    }
                }
            }
        }

        // 5. Hitung Pajak dari Tax Base (DPP)
        $tax_percentage = (float) ($this->global_tax ?? 0) / 100;
        $this->tax_amount = max(0, $tax_base * $tax_percentage);

        // 6. Final Grand Total
        $grand_total = $tax_base + $this->tax_amount + $after_tax_charges;

        return max(0, $grand_total);
    }

    public function saveOrderPending()
    {
        if (Cart::instance('sale')->count() == 0) {
            $this->alertType = 'warning';
            $this->alertMessage = 'Keranjang masih kosong!';
            $this->dispatch('auto-hide-alert');
            return;
        }

        $currentCart = Cart::instance('sale')->content();

        // CEK: Jika ini Update Order (Ada Reference)
        if (!empty($this->current_reference)) {
            $sale = Sale::where('reference', $this->current_reference)->first();

            // Tambahkan pengecekan: Hanya proses approval jika sale SUDAH DIPRINT
            if ($sale && $sale->is_printed == 1) {
                $oldDetails = SaleDetails::where('sale_id', $sale->id)->get();
                $changes = [];

                // --- CEK VOID ---
                foreach ($oldDetails as $old) {
                    $match = $currentCart->where('id', $old->product_id)->first();

                    $qtyVoid = 0;
                    if (!$match) {
                        $qtyVoid = $old->quantity;
                    } elseif ($match->qty < $old->quantity) {
                        $qtyVoid = $old->quantity - $match->qty;
                    }

                    if ($qtyVoid > 0) {
                        $changes[] = [
                            'name' => $old->product_name,
                            'qty'  => $qtyVoid,
                            'type' => 'VOID',
                            'class' => 'badge-danger',
                            'reason' => !$match ? 'Dihapus dari daftar' : 'Pengurangan jumlah'
                        ];
                    }
                }

                // --- CEK NEW ---
                foreach ($currentCart as $new) {
                    $match = $oldDetails->where('product_id', $new->id)->first();

                    $qtyNew = 0;
                    if (!$match) {
                        $qtyNew = $new->qty;
                    } elseif ($new->qty > $match->quantity) {
                        $qtyNew = $new->qty - $match->quantity;
                    }

                    if ($qtyNew > 0) {
                        $changes[] = [
                            'name' => $new->name,
                            'qty'  => $qtyNew,
                            'type' => 'NEW',
                            'class' => 'badge-success',
                            'reason' => !$match ? 'Menu tambahan baru' : 'Penambahan jumlah'
                        ];
                    }
                }

                // Jika ada perubahan dan status sudah terprint, minta approval
                if (count($changes) > 0) {
                    $this->items_to_approve = $changes;
                    $this->showApprovalModal = true;
                    $this->approval_trigger = 'saveorder';
                    return;
                }
            }
        }

        // Jika Order Baru, atau Order Pending yang BELUM diprint (is_printed = 0),
        // langsung eksekusi tanpa modal approval.
        $this->executeSaveOrder();
    }

    private function executeSaveOrder($approvedBy = null)
    {
        // 1. Kalkulasi Total & Persiapan Data
        $grand_total_float = $this->calculateTotal();
        $shipping = (float) ($this->shipping ?? 0);
        $discount = (float) str_replace(',', '', Cart::instance('sale')->discount());
        $lain_a = isFeatureEnabled('summary_others') ? (float)$this->lain_a : 0;
        $lain_b = isFeatureEnabled('summary_others') ? (float)$this->lain_b : 0;
        $encoded_table_ids = json_encode($this->table_ids_array);
        $sale = null;

        // Ambil alasan admin sekali di awal untuk digunakan di dalam closure transaction
        $adminNote = $this->approval_note ? " | Admin Note: " . $this->approval_note : "";

        $userOutletId = session('selected_outlet_id') ?? auth()->user()->outlets()->first()?->id;
        $warehouse = \Modules\Setting\Entities\Warehouse::where('outlet_id', $userOutletId)
            ->where('is_active', 1)
            ->first();

        if (!$warehouse) {
            $this->alertType = 'danger';
            $this->alertMessage = 'Gudang/Warehouse untuk outlet Anda tidak ditemukan atau tidak aktif! ID Outlet: ' . ($userOutletId ?? 'Tidak terdeteksi');
            $this->dispatch('auto-hide-alert');
            return;
        }

        $warehouse_id = $warehouse->id;

        $saleData = [
            'customer_name'       => $this->customer_name ?? 'Guest',
            'order_type'          => $this->order_type,
            'user_id'             => auth()->id(),
            'warehouse_id'        => $warehouse_id,
            'tax_percentage'      => (float) ($this->global_tax ?? 0),
            'discount_percentage' => (float) ($this->global_discount ?? 0),
            'shipping_amount'     => $shipping * 100,
            'tax_amount'          => $this->tax_amount * 100,
            'discount_amount'     => $discount * 100,
            'total_amount'        => $grand_total_float * 100,
            'status'              => 'Pending',
            'payment_status'      => 'Unpaid',
            'selected_table_ids'  => $encoded_table_ids,
            'service_charge'      => $this->service_charge * 100,
            'lain_a'              => $lain_a * 100,
            'lain_b'              => $lain_b * 100,
        ];

        \DB::transaction(function () use ($saleData, &$sale, $adminNote, $approvedBy, $warehouse_id) {
            $currentCart = Cart::instance('sale')->content();

            // 2. Logika Update Jika Memiliki Reference (Edit Mode)
            if (!empty($this->current_reference)) {
                $sale = Sale::where('reference', $this->current_reference)->first();

                if ($sale) {
                    $oldDetails = SaleDetails::where('sale_id', $sale->id)->get();

                    // --- LOGIKA LOG DAPUR ---
                    if ($sale->is_printed == 1) {
                        // JIKA SUDAH DIPRINT: Catat selisihnya (Void & New) agar dapur tahu apa yang berubah

                        // --- CEK VOID ---
                        foreach ($oldDetails as $oldItem) {
                            $matchInCart = $currentCart->where('id', $oldItem->product_id)->first();
                            $voidQty = 0;
                            if (!$matchInCart) {
                                $voidQty = $oldItem->quantity;
                                $systemReason = 'Dihapus dari list';
                            } elseif ($matchInCart->qty < $oldItem->quantity) {
                                $voidQty = $oldItem->quantity - $matchInCart->qty;
                                $systemReason = 'Pengurangan Qty';
                            }

                            if ($voidQty > 0) {
                                \App\Models\OrderKitchenLog::create([
                                    'sale_id'      => $sale->id,
                                    'reference'    => $sale->reference,
                                    'product_name' => $oldItem->product_name,
                                    'qty'          => $voidQty,
                                    'type'         => 'void',
                                    'note'         => $systemReason . ($adminNote ? ' - ' . $adminNote : ''),
                                    'user_id'      => auth()->id(),
                                    'approved_by'  => $approvedBy,
                                    'is_printed'   => 0
                                ]);
                            }
                        }

                        // --- CEK NEW ---
                        foreach ($currentCart as $newItem) {
                            $matchInOld = $oldDetails->where('product_id', $newItem->id)->first();
                            $newQty = 0;
                            if (!$matchInOld) {
                                $newQty = $newItem->qty;
                                $systemReason = 'Menu Baru';
                            } elseif ($newItem->qty > $matchInOld->quantity) {
                                $newQty = $newItem->qty - $matchInOld->quantity;
                                $systemReason = 'Penambahan Qty';
                            }

                            if ($newQty > 0) {
                                \App\Models\OrderKitchenLog::create([
                                    'sale_id'      => $sale->id,
                                    'reference'    => $sale->reference,
                                    'product_name' => $newItem->name,
                                    'qty'          => $newQty,
                                    'type'         => 'new',
                                    'note'         => $systemReason . ($adminNote ? ' - ' . $adminNote : ''),
                                    'user_id'      => auth()->id(),
                                    'approved_by'  => $approvedBy,
                                    'is_printed'   => 0
                                ]);
                            }
                        }
                    } else {
                        // JIKA BELUM DIPRINT: Update totalan log saja (Fresh Start)
                        // Kita hapus log lama yang belum diprint, lalu masukkan data keranjang terbaru sebagai 'new'
                        \App\Models\OrderKitchenLog::where('sale_id', $sale->id)->where('is_printed', 0)->delete();

                        foreach ($currentCart as $item) {
                            \App\Models\OrderKitchenLog::create([
                                'sale_id'      => $sale->id,
                                'reference'    => $sale->reference,
                                'product_name' => $item->name,
                                'qty'          => $item->qty,
                                'type'         => 'new',
                                'note'         => 'Update sebelum print',
                                'user_id'      => auth()->id(),
                                'is_printed'   => 0
                            ]);
                        }
                    }

                    // --- UPDATE DATA TRANSAKSI ---
                    SaleDetails::where('sale_id', $sale->id)->delete();
                    $sale->update($saleData); // $saleData berisi total_amount, dll
                }
            }

            // 3. Logika Jika Order Baru
            if (!$sale) {
                $reference = $this->generateSalesNumber();
                $saleData['date'] = now()->format('Y-m-d');
                $saleData['reference'] = $reference;
                $sale = Sale::create($saleData);
                $this->current_reference = $reference;

                foreach ($currentCart as $item) {
                    \App\Models\OrderKitchenLog::create([
                        'sale_id'      => $sale->id,
                        'reference'    => $sale->reference,
                        'product_name' => $item->name,
                        'qty'          => $item->qty,
                        'type'         => 'new',
                        'note'         => 'Order Baru', // Order baru murni tidak butuh admin note tambahan
                        'user_id'      => auth()->id(),
                    ]);
                }
            }

            // 4. Simpan Detail Produk Final & Potong Stok
            foreach ($currentCart as $cart_item) {
                $product = Product::find($cart_item->id);

                // Ambil Warehouse ID (Sesuaikan dengan cara Anda menyimpan ID Warehouse/Outlet)
                // Contoh: auth()->user()->warehouse_id atau session('warehouse_id')
                //$warehouse_id = auth()->user()->warehouse_id;

                SaleDetails::create([
                    'sale_id'                 => $sale->id,
                    'reference'               => $sale->reference,
                    'product_id'              => $cart_item->id,
                    'product_name'            => $cart_item->name,
                    'product_code'            => $cart_item->options->code,
                    'quantity'                => $cart_item->qty,
                    'price'                   => (float) $cart_item->price * 100,
                    'unit_price'              => (float) ($cart_item->options->unit_price ?? $cart_item->price) * 100,
                    'sub_total'               => (float) ($cart_item->price * $cart_item->qty) * 100,
                    'product_discount_amount' => (float) ($cart_item->options->product_discount ?? 0) * 100,
                    'product_discount_type'   => $cart_item->options->product_discount_type ?? 'fixed',
                    'product_tax_amount'      => (float) ($cart_item->options->product_tax ?? 0) * 100,
                    'variant_detail'          => json_encode($cart_item->options->variants ?? []),
                ]);

                // --- LOGIKA POTONG STOK ---
                // if ($product) {
                //     if ($product->is_recipe == 'Y') {
                //         // 1. Cari Header Recipe untuk produk menu ini
                //         $recipeHeader = \Modules\Setting\Entities\Recipe::where('product_id', $product->id)->first();

                //         if ($recipeHeader) {
                //             // 2. Ambil detail bahan baku dari relasi details()
                //             // Sesuai tabel Anda: menggunakan 'product_id' sebagai ID bahan baku
                //             foreach ($recipeHeader->details as $detail) {
                //                 // Total yang harus dikurangi: (jumlah bahan per porsi * qty jual)
                //                 $qty_to_reduce = (float)$detail->quantity * $cart_item->qty;

                //                 // 3. Cari stok bahan tersebut di tabel product_warehouse
                //                 $productWarehouse = \Modules\Setting\Entities\ProductWarehouse::where('product_id', $detail->product_id)
                //                     ->where('warehouse_id', $warehouse_id)
                //                     ->first();

                //                 if ($productWarehouse) {
                //                     $productWarehouse->decrement('qty', $qty_to_reduce);
                //                 }
                //             }
                //         }
                //     } else {
                //         // JIKA BUKAN RECIPE (STANDARD): Potong stok produk itu sendiri
                //         $productWarehouse = \Modules\Setting\Entities\ProductWarehouse::where('product_id', $product->id)
                //             ->where('warehouse_id', $warehouse_id)
                //             ->first();

                //         if ($productWarehouse) {
                //             $productWarehouse->decrement('qty', $cart_item->qty);
                //         }
                //     }
                // }
            }
        });

        // 5. Bersihkan State & Modal
        Cart::instance('sale')->destroy();
        $this->reset([
            'customer_name', 'order_type', 'current_reference', 'table_ids_array',
            'selectedTableNames', 'lain_a', 'lain_b', 'service_charge', 'tax_amount',
            'approver_username', 'approver_password', 'approval_note', 'approval_trigger'
        ]);

        $this->showApprovalModal = false;
        $this->resetCart();
        session()->flash('message', 'Order berhasil diproses!');
    }

    public function confirmApproval()
    {
        $this->validate([
            'approver_username' => 'required',
            'approver_password' => 'required',
        ]);

        // Cari user dengan level Admin dan tenant_database yang sama
        $admin = \App\Models\User::where('email', $this->approver_username)
            ->where('level', 'Admin')
            ->where('tenant_database', auth()->user()->tenant_database)
            ->first();

        if ($admin && \Hash::check($this->approver_password, $admin->password)) {
            $this->approved_by_id = $admin->id;
            $this->showApprovalModal = false;
            $this->reset(['approver_username', 'approver_password']);

            // CEK TRIGGER:
            if ($this->approval_trigger == 'proceed') {
                // Jika tadi klik proceed, sekarang buka modal checkoutnya
                $this->dispatch('showCheckoutModal');
            } else {
                // Jika tadi klik save order pending, langsung jalankan simpan
                $this->executeSaveOrder($admin->id);
            }
        } else {
            $this->addError('approver_password', 'Otorisasi Admin Gagal! Username atau Password salah.');
        }
    }

    public function previewVoidOrder($id)
    {
        $order = Sale::findOrFail($id);

        // Ambil SEMUA log (void & new) yang belum diprint untuk sale ini
        $allLogs = \App\Models\OrderKitchenLog::where('sale_id', $id)
            ->where('is_printed', 0)
            ->where('approved_by', 1)
            ->orderBy('type', 'desc') // Void biasanya di atas, New di bawah
            ->get();

        if ($allLogs->isEmpty()) {
            $this->dispatch('alert', type: 'warning', message: 'Tidak ada data pesanan baru atau void!');
            return;
        }

        $this->previewOrderData = [
            'reference'     => $order->reference,
            'customer_name' => $order->customer_name,
            'typeOrder'     => $order->order_type,
            'meja_name'     => $order->table_id,
            'date'          => now()->format('d/m/Y H:i'),
            'details'       => $allLogs->map(function ($item) {
                return [
                    'product_name'   => $item->product_name,
                    'quantity'       => $item->qty,
                    'type'           => $item->type, // Kita kirim tipe 'void' atau 'new'
                    'variant_detail' => null,
                    'note'           => $item->note
                ];
            }),
            'is_combined'   => true // Flag penanda ini adalah print gabungan
        ];

        $this->dispatch('openKitchenPreviewModal');
    }

    public function updatedLainA()
    {
        $this->total_amount = $this->calculateTotal();
    }

    public function updatedLainB()
    {
        $this->total_amount = $this->calculateTotal();
    }

    public function resetCart()
    {
        session()->forget('variant_session');
        Cart::instance($this->cart_instance)->destroy();

        $this->reset([
            'quantity',
            'check_quantity',
            'discount_type',
            'item_discount',
            'total_amount',

            'customer_name',
            'order_type',
            'table_id',
            'check_quantity',
            'current_reference',
            'table_ids_array',
            'selectedTableNames'
        ]);

        // reset default
        $this->global_discount = 0;
        $this->global_tax = 0;
        $this->shipping = 0.00;
        $this->lain_a = 0; // Tambahkan ini
        $this->lain_b = 0; // Tambahkan ini

        // 🔹 dispatch event untuk reset JS modal variant
        $this->dispatch('variant-modal-reset-all');

        $this->dispatch('$refresh');
    }


    public function productSelected($product)
    {
        $cart = Cart::instance($this->cart_instance);
        // Cek apakah produk sudah ada di cart
        $exists = $cart->search(function ($cartItem, $rowId) use ($product) {
            return $cartItem->id == $product['id'];
        });

        //
        if ($exists->isNotEmpty()) {
            $rowId = $exists->first()->rowId;
            $cartItem = $cart->get($rowId);
            $newQty = $cartItem->qty + 1;

            if ($newQty > $product['product_quantity']) {
                session()->flash('message', 'Stok tidak cukup untuk ' . $product['product_name']);
                return;
            }

            // 1. Update Qty saja (ini tidak menghapus options)
            $cart->update($rowId, $newQty);

            // 2. Ambil item yang sudah di-update QTY-nya
            $updatedItem = $cart->get($rowId);

            // 🔥 FIX: Ambil semua opsi lama, termasuk 'variants'
            $options = $updatedItem->options->toArray();

            // 3. Perbarui sub_total (dan opsi lain jika perlu)
            $options['sub_total'] = $updatedItem->price * $updatedItem->qty;

            // 4. Simpan kembali opsi lengkap ke keranjang (mempertahankan variants)
            $cart->update($rowId, [
                'options' => $options
            ]);

            //
            $this->refreshCartTaxAndDiscount();
        } else {
            $cart->add([
                'id'      => $product['id'],
                'name'    => $product['product_name'],
                'qty'     => 1,
                'price'   => $this->calculate($product)['price'],
                'weight'  => 1,
                'options' => [
                    'product_discount'      => 0.00,
                    'product_discount_type' => 'fixed',
                    'sub_total'             => $this->calculate($product)['sub_total'],
                    'code'                  => $product['product_code'],
                    'stock'                 => $product['product_quantity'],
                    'unit'                  => $product['product_unit'],
                    'product_tax'           => $this->calculate($product)['product_tax'],
                    'unit_price'            => $this->calculate($product)['unit_price'],
                ]
            ]);
            // 🧩 Tambahkan baris ini
            $this->refreshCartTaxAndDiscount();
        }

        $this->check_quantity[$product['id']] = $product['product_quantity'];
        $this->quantity[$product['id']] = isset($this->quantity[$product['id']])
            ? $this->quantity[$product['id']] + 1
            : 1;
        $this->discount_type[$product['id']] = 'fixed';
        $this->item_discount[$product['id']] = 0;

        $this->total_amount = $this->calculateTotal();
    }


    public function removeItem($row_id)
    {
        $cart = Cart::instance($this->cart_instance);

        $item = $cart->get($row_id);

        if ($item) {
            $productId = $item->id;

            // Reset Livewire & Session state
            $this->resetProductState($productId);

            // Remove item dari cart
            $cart->remove($row_id);
        }

        // Hitung ulang total
        $this->total_amount = $this->calculateTotal();

        // Reset modal variant (UI) — kirim productId supaya JS hapus cache JS juga
        $this->dispatch('variant-modal-reset', ['productId' => $productId ?? null]);

        // Refresh livewire
        $this->dispatch('$refresh');
    }

    private function resetProductState($productId)
    {
        // 1. Hapus session variant
        session()->forget("variant_session.$productId");

        // 2. Hapus quantity Livewire
        if (isset($this->quantity[$productId])) {
            unset($this->quantity[$productId]);
        }

        // 3. Hapus check_quantity (variant per qty)
        if (isset($this->check_quantity[$productId])) {
            unset($this->check_quantity[$productId]);
        }

        // 4. Hapus cartItems yg berhubungan
        foreach ($this->cartItems as $key => $item) {
            if (!empty($item['product_id']) && $item['product_id'] == $productId) {
                unset($this->cartItems[$key]);
            }
        }

        // 5. Jika punya array variant lain, hapus juga (opsional)
        if (property_exists($this, 'variant_by_product') && isset($this->variant_by_product[$productId])) {
            unset($this->variant_by_product[$productId]);
        }

        // Hapus variants dari cartItems jika ada
        foreach ($this->cartItems as $row => $item) {
            if (isset($item['variants'])) {
                unset($this->cartItems[$row]['variants']);
            }
        }
    }



    public function updatedGlobalTax()
    {
        Cart::instance($this->cart_instance)->setGlobalTax((int)$this->global_tax);
    }

    public function updatedGlobalDiscount()
    {
        Cart::instance($this->cart_instance)->setGlobalDiscount((int)$this->global_discount);
    }



    public function updateQuantity($row_id, $product_id)
    {
        $cart = Cart::instance($this->cart_instance);

        // Ambil cart item awal
        $cart_item = $cart->get($row_id);
        if (!$cart_item) return;

        $newQty = $this->quantity[$product_id] ?? $cart_item->qty;

        // Stock
        $availableStock = $this->check_quantity[$product_id]
            ?? $cart_item->options->stock
            ?? 0;

        // Validasi minimal
        if ($newQty < 1) $newQty = 1;

        // Validasi stok
        if ($newQty > $availableStock) {
            session()->flash('message', 'The requested quantity is not available in stock.');
            $newQty = $cart_item->qty;
        }

        // 1️⃣ Update qty dulu
        $cart->update($row_id, $newQty);

        // 2️⃣ Hapus variant (TAPI jangan pakai row_id lama!)
        $this->removeVariantAfterQtyDecrease($product_id, $newQty);

        // 3️⃣ Normalize variant (TANPA ROW_ID)
        $this->normalizeVariantsAfterQtyChange($product_id, $newQty);

        // 4️⃣ Simpan qty ke state
        $this->quantity[$product_id] = $newQty;
        $this->total_amount = $this->calculateTotal();

        // 5️⃣ ***AMBIL CART ITEM TERBARU BERDASARKAN PRODUCT ID***
        $newCartItem = $cart->search(fn ($i) => $i->id == $product_id)->first();
        if (!$newCartItem) return; // antisipasi

        // 6️⃣ Update options menggunakan ROW ID BARU
        $cart->update($newCartItem->rowId, [
            'options' => [
                'sub_total'             => $newCartItem->price * $newCartItem->qty,
                'code'                  => $newCartItem->options->code,
                'stock'                 => $newCartItem->options->stock,
                'unit'                  => $newCartItem->options->unit,
                'product_tax'           => $newCartItem->options->product_tax,
                'unit_price'            => $newCartItem->options->unit_price,
                'product_discount'      => $newCartItem->options->product_discount,
                'product_discount_type' => $newCartItem->options->product_discount_type,
                'variants'              => $newCartItem->options->variants ?? [],
            ]
        ]);
    }

    public function updatedDiscountType($value, $name)
    {
        $this->item_discount[$name] = 0;
    }

    public function discountModalRefresh($product_id, $row_id)
    {
        $this->updateQuantity($row_id, $product_id);
    }

    public function setProductDiscount($row_id, $product_id)
    {
        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        if ($this->discount_type[$product_id] == 'fixed') {
            Cart::instance($this->cart_instance)
                ->update($row_id, [
                    'price' => ($cart_item->price + $cart_item->options->product_discount) - $this->item_discount[$product_id]
                ]);

            $discount_amount = $this->item_discount[$product_id];

            $this->updateCartOptions($row_id, $product_id, $cart_item, $discount_amount);
        } elseif ($this->discount_type[$product_id] == 'percentage') {
            $discount_amount = ($cart_item->price + $cart_item->options->product_discount) * ($this->item_discount[$product_id] / 100);

            Cart::instance($this->cart_instance)
                ->update($row_id, [
                    'price' => ($cart_item->price + $cart_item->options->product_discount) - $discount_amount
                ]);

            $this->updateCartOptions($row_id, $product_id, $cart_item, $discount_amount);
        }

        session()->flash('discount_message' . $product_id, 'Discount added to the product!');
    }

    public function calculate($product)
    {
        $price = 0;
        $unit_price = 0;
        $product_tax = 0;
        $sub_total = 0;

        if ($product['product_tax_type'] == 1) {
            $price = $product['product_price'] + ($product['product_price'] * ($product['product_order_tax'] / 100));
            $unit_price = $product['product_price'];
            $product_tax = $product['product_price'] * ($product['product_order_tax'] / 100);
            $sub_total = $product['product_price'] + ($product['product_price'] * ($product['product_order_tax'] / 100));
        } elseif ($product['product_tax_type'] == 2) {
            $price = $product['product_price'];
            $unit_price = $product['product_price'] - ($product['product_price'] * ($product['product_order_tax'] / 100));
            $product_tax = $product['product_price'] * ($product['product_order_tax'] / 100);
            $sub_total = $product['product_price'];
        } else {
            $price = $product['product_price'];
            $unit_price = $product['product_price'];
            $product_tax = 0.00;
            $sub_total = $product['product_price'];
        }

        return ['price' => $price, 'unit_price' => $unit_price, 'product_tax' => $product_tax, 'sub_total' => $sub_total];
    }

    public function updateCartOptions($row_id, $product_id, $cart_item, $discount_amount)
    {
        Cart::instance($this->cart_instance)->update($row_id, ['options' => [
            'sub_total'             => $cart_item->price * $cart_item->qty,
            'code'                  => $cart_item->options->code,
            'stock'                 => $cart_item->options->stock,
            'unit'                 => $cart_item->options->unit,
            'product_tax'           => $cart_item->options->product_tax,
            'unit_price'            => $cart_item->options->unit_price,
            'product_discount'      => $discount_amount,
            'product_discount_type' => $this->discount_type[$product_id],
        ]]);
    }

    public function updateQuantityPlus($row_id, $product_id)
    {
        $cart = Cart::instance($this->cart_instance);

        // Cari rowId yang valid berdasarkan product_id
        $cart_item = $cart->search(fn ($item) => $item->id == $product_id)->first();

        if (!$cart_item) {
            return; // Item tidak ditemukan (mungkin sedang dihapus / race condition)
        }

        $newQty = ($this->quantity[$product_id] ?? $cart_item->qty) + 1;

        // Cek stok
        // if ($cart_item->options->stock < $newQty) {
        //     session()->flash('message', 'Stok tidak mencukupi untuk produk ini.');
        //     return;
        // }

        $this->normalizeVariantsAfterQtyChange($product_id, $newQty);

        // Update qty
        $cart->update($cart_item->rowId, $newQty);

        // Update data Livewire
        $this->quantity[$product_id] = $newQty;
        $this->total_amount = $this->calculateTotal();
    }


    public function updateQuantityMin($row_id, $product_id)
    {
        $cart = Cart::instance($this->cart_instance);

        $cart_item = $cart->search(fn ($item) => $item->id == $product_id)->first();

        if (!$cart_item) {
            return;
        }

        // Qty baru → minimal 1
        $newQty = max(1, ($this->quantity[$product_id] ?? $cart_item->qty) - 1);

        // Update qty di cart
        $cart->update($cart_item->rowId, $newQty);

        // 🟦 HAPUS VARIANT OTOMATIS: buang semua variant yang index > qty baru
        $this->removeVariantAfterQtyDecrease($product_id, $newQty);

        // Normalisasi variant Anda yang lama
        $this->normalizeVariantsAfterQtyChange($product_id, $newQty);

        $this->quantity[$product_id] = $newQty;
        $this->total_amount = $this->calculateTotal();
    }

    public function removeVariantAfterQtyDecrease($productId, $newQty)
    {
        // === SESSION ===
        $sessionVariants = session()->get("variant_session.$productId", []);

        if (count($sessionVariants) > $newQty) {
            $sessionVariants = array_slice($sessionVariants, 0, $newQty);
            session()->put("variant_session.$productId", $sessionVariants);
        }

        // === CART ===
        $cart = Cart::instance('sale');

        // SELALU DAPATKAN CART ITEM TERBARU BERDASARKAN PRODUCT ID
        $cartItem = $cart->search(fn ($item) => $item->id == $productId)->first();

        if (!$cartItem) return;

        $options = $cartItem->options->toArray();

        if (isset($options['variants']) && count($options['variants']) > $newQty) {
            $options['variants'] = array_slice($options['variants'], 0, $newQty);
        }

        // 🟦 GUNAKAN ROWID TERBARU DARI $cartItem — BUKAN ROWID LAMA DARI updateQuantity()
        $cart->update($cartItem->rowId, [
            'qty'     => $newQty,
            'options' => $options,
        ]);
    }

    public function generateSalesNumber(): string
    {
        // Mulai transaksi database untuk mencegah race condition
        return DB::transaction(function () {
            $prefix = 'SL/' . date('Ym') . '/';

            // Cari order terakhir untuk bulan dan tahun ini dengan lock
            // lockForUpdate() akan mencegah baris lain membaca record ini sampai transaksi selesai
            $lastSale = Sale::where('reference', 'like', $prefix . '%')
                ->orderBy('reference', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastSale) {
                // Ambil nomor urut dari nomor order terakhir
                // Contoh: dari "DO/202510/0005", kita ambil "0005"
                $lastNumber = (int) substr($lastSale->reference, -4);
                $newNumber = $lastNumber + 1;
            } else {
                // Ini adalah order pertama di bulan ini
                $newNumber = 1;
            }
            // Format nomor baru dengan padding 4 digit
            $paddedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            return $prefix . $paddedNumber;
        });
    }

    // 🔹 Ambil semua order pending
    // public function loadPendingOrders()
    // {
    //     $this->pendingOrders = Sale::with('meja')->where('status', 'Pending') // ✅ Sudah pakai with('meja')
    //         ->orderByDesc('created_at')
    //         ->take(20)
    //         ->get();
    // }
    // 🔹 Restore isi order pending ke cart
    public function restorePendingOrder($orderId)
    {
        $order = Sale::find($orderId);

        if (!$order) {
            $this->alertType = 'warning';
            $this->alertMessage = 'Order tidak ditemukan.';
            $this->dispatch('auto-hide-alert');
            return;
        }

        // 🧹 Kosongkan cart dulu
        Cart::instance('sale')->destroy();
        // 🧹 Bersihkan semua state internal cart Livewire
        $this->reset([
            'customer_name',
            'order_type',
            'table_id',
            'quantity',
            'check_quantity',
            'discount_type',
            'item_discount',
            'total_amount',
            'table_ids_array',
            'selectedTableNames',
        ]);

        // Kosongkan Livewire property quantity juga
        $this->quantity = [];

        $details = SaleDetails::where('sale_id', $order->id)->get();

        foreach ($details as $item) {

            // Ambil stok terupdate dari database (atau tabel stock product Anda)
            $productStock = Product::find($item->product_id)->product_quantity ?? 0;

            // Set check_quantity agar validasi stok tidak NULL
            $this->check_quantity[$item->product_id] = $productStock;

            // 🔹 Decode variant_detail per item (jika ada)
            $variantsForCart = json_decode($item->variant_detail, true) ?? [];

            $cartItem = Cart::instance('sale')->add([
                'id'      => $item->product_id,
                'name'    => $item->product_name,
                'qty'     => $item->quantity,
                'price'   => $item->unit_price,
                'weight'  => 0,
                'options' => [
                    'code' => $item->product_code,
                    'unit_price' => $item->unit_price,
                    'sub_total' => $item->sub_total,
                    'product_discount' => $item->product_discount_amount,
                    'product_discount_type' => $item->product_discount_type,
                    'product_tax' => $item->product_tax_amount,

                    // 🔥 Load variant & typeOrder
                    'variants' => $variantsForCart,
                ],
            ]);

            // 🔹 Set Livewire variable agar input variant terisi


            // 🔹 Set quantity
            $this->quantity[$item->product_id] = $item->quantity;
        }


        // Simpan reference dan data lain untuk edit
        $this->total_amount = $this->calculateTotal();
        $this->current_reference = $order->reference;
        $this->global_tax = $order->tax_percentage;
        $this->global_discount = $order->discount_percentage;
        $this->shipping = $order->shipping_amount;
        $this->order_type = $order->order_type;
        $this->table_id = $order->table_id;
        $this->customer_name = $order->customer_name;
        $this->lain_a = $order->lain_a;
        $this->lain_b = $order->lain_b;

        if (!empty($order->selected_table_ids)) {

            // Cek casting model (baik array atau string JSON)
            $tableIds = is_array($order->selected_table_ids)
                ? $order->selected_table_ids
                : json_decode($order->selected_table_ids, true);

            // Pastikan hasil decode adalah array yang tidak kosong
            if (!empty($tableIds)) {
                // ✅ Set Livewire property array ID meja
                $this->table_ids_array = array_map('intval', $tableIds);

                // ✅ Ambil dan set string nama meja untuk badge
                $mejas = Meja::whereIn('id', $this->table_ids_array)->get();
                $this->selectedTableNames = $mejas->map(fn ($t) => $t->name ?? 'Meja ' . $t->no_meja)->implode(', ');
            }
        }

        // Fallback: Jika field lama 'table_id' masih digunakan dan tidak ada selected_table_ids
        $this->table_id = $order->table_id;

        // =========================================================================

        $this->refreshCartTaxAndDiscount();

        // ✅ Tutup modal list order
        $this->dispatch('refresh-modal-state');
        $this->dispatch('close-order-detail-modal');
        $this->dispatch('close-pending-orders-modal');

        $this->dispatch('syncTableSelection', scope: true);
        // $this->alertType = 'info';
        // $this->alertMessage = 'Order berhasil dimuat.';
        // $this->dispatch('auto-hide-alert');
    }
    public function showOrderDetail($orderId)
    {
        $order = Sale::find($orderId);

        if (!$order) {
            $this->alertType = 'warning';
            $this->alertMessage = 'Order tidak ditemukan.';
            $this->dispatch('auto-hide-alert');
            return;
        }

        $this->selectedOrderDetails = SaleDetails::where('sale_id', $order->id)->get();

        $this->selectedOrderSummary = [
            'tax_percentage' => $order->tax_percentage,
            'tax_amount' => $order->tax_amount,
            'discount_percentage' => $order->discount_percentage,
            'discount_amount' => $order->discount_amount,
            'shipping_amount' => $order->shipping_amount,
            'service_charge' => $order->service_charge, // Tambahkan ini
            'lain_a' => $order->lain_a,                 // Tambahkan ini
            'lain_b' => $order->lain_b,
            'total_amount' => $order->total_amount,
            'order_type' => $order->order_type,
        ];

        // Buka modal detail
        $this->dispatch('show-order-detail-modal');
    }

    private function normalizeVariantsAfterQtyChange($productId, $newQty)
    {
        $variants = session()->get("variant_session.$productId", []);

        if (count($variants) > $newQty) {
            // Potong array variant sesuai qty baru
            $variants = array_slice($variants, 0, $newQty);

            session()->put("variant_session.$productId", $variants);
        }
    }
}
