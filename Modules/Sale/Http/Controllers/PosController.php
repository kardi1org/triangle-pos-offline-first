<?php

namespace Modules\Sale\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\People\Entities\Customer;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;
use Modules\Sale\Http\Requests\StorePosSaleRequest;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Sale\DataTables\SalePaymentsDataTable;
use Rawilk\Printing\Factory;
use Rawilk\Printing\Contracts\Driver;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Illuminate\Validation\ValidationException;
use Modules\Sale\Http\Controller\Escpos;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Modules\Setting\Entities\Setting;
use Exception;


class PosController extends Controller
{
    public $alignmentClass;
    private $printer;
    private $width;
    private $currencySymbol;

    public function index()
    {
        Cart::instance('sale')->destroy();

        $customers = Customer::all();
        $customer_name = Sale::all();  // Add by Chris
        $product_categories = Category::all();

        return view('sale::pos.index', compact('product_categories', 'customers'));
    }

    public function store(StorePosSaleRequest $request)
    {
        DB::transaction(function () use ($request) {
            $due_amount = $request->total_amount - $request->paid_amount;
            $customer_id = '.';

            if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            /* $customer = Customer::firstOrCreate(
                ['customer_name' => $request->customer_name]
            ); */

            $sale = Sale::create([
                'date' => now()->format('Y-m-d'),
                'reference' => $this->generateSalesNumber(),
                'customer_id' => $request->$customer_id,
                //   'customer_name' => Customer::findOrFail($request->customer_id)->customer_name,
                'customer_name' => $request->input('customer_name'),
                'tax_percentage' => $request->tax_percentage,
                'discount_percentage' => $request->discount_percentage,
                'shipping_amount' => $request->shipping_amount,  // * 100,
                'paid_amount' => $request->paid_amount,   //* 100,
                'total_amount' => $request->total_amount,   //* 100,
                //   'due_amount' => $due_amount,   //* 100,
                'status' => 'Completed',
                'payment_status' => $payment_status,
                //   'payment_method' => $request->payment_method,
                'note' => $request->note,
                'tax_amount' => Cart::instance('sale')->tax() * 100,
                'discount_amount' => Cart::instance('sale')->discount() * 100,
                /* 'debitcard' => $request->input('txtdebitcard'),
                'creditcard' => $request->input('txtcreditcard'),
                'gopay' => $request->input('txtgopay'),
                'grabpay' => $request->input('txtgrabpay'),
                'ovopay' => $request->input('txtovo'),
                'shopeepay' => $request->input('txtshopeepay'),
                'danapay' => $request->input('txtdana'),
                'kredivopay' => $request->input('txtkredivo'),
                'qrispay' => $request->input('txtqris'), */
            ]);

            foreach (Cart::instance('sale')->content() as $cart_item) {
                SaleDetails::create([
                    'sale_id' => $sale->id,
                    'reference' => $sale->reference,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    'quantity' => $cart_item->qty,
                    'price' => $cart_item->price, //* 100,
                    'unit_price' => $cart_item->options->unit_price, // * 100,
                    'sub_total' => $cart_item->options->sub_total, //* 100,
                    'product_discount_amount' => $cart_item->options->product_discount, //* 100,
                    'product_discount_type' => $cart_item->options->product_discount_type,
                    'product_tax_amount' => $cart_item->options->product_tax,  //* 100,
                ]);

                /*  $product = Product::findOrFail($cart_item->id);
                $product->update([
                    'product_quantity' => $product->product_quantity - $cart_item->qty
                ]); */
            }

            //  Cart::instance('sale')->destroy();

            if ($sale->paid_amount > 0) {
                SalePayment::create([
                    'date' => now()->format('Y-m-d'),
                    'reference' => 'INV/' . $sale->reference,
                    'amount' => $sale->paid_amount,
                    'sale_id' => $sale->id,
                    //  'payment_method' => $request->payment_method
                    'debitcard' => $request->debitcard,
                    'creditcard' => $request->creditcard,
                    'gopay' => $request->gopay,
                    'grabpay' => $request->grabpay,
                    'ovopay' => $request->ovo,
                    'shopeepay' => $request->shopeepay,
                    'danapay' => $request->dana,
                    'kredivopay' => $request->kredivo,
                    'qrispay' => $request->qris,
                ]);
            }
        });
        //------------------------------------------------------------------------------//
        try {
            // Buat koneksi ke printer
            $settings = Setting::first();
            $connector = new WindowsPrintConnector($settings->name_printer);
            //  $connector = new NetworkPrintConnector($printerIp, $port);
            //  $connector = new WindowsPrintConnector("\\SVR-01\POS-58");
            //  $connector = new WindowsPrintConnector("//10.10.10.150/POS-58");
            $printer = new Printer($connector);
            $lineWidth = 32;
            // Fungsi untuk merapikan teks
            function alignRight($name, $qty, $price, $lineWidth)
            {
                $nameWidth = 16; // Alokasi 16 karakter untuk nama produk
                $qtyWidth = 8;   // Alokasi 8 karakter untuk Qty
                $priceWidth = 8; // Alokasi 8 karakter untuk Harga

                // Bungkus nama produk jika panjangnya melebihi alokasi
                $nameLines = str_split($name, $nameWidth);
                // Siapkan variabel untuk hasil format
                $output = '';
                // Tambahkan semua baris nama produk kecuali yang terakhir
                for ($i = 0; $i < count($nameLines) - 1; $i++) {
                    $output .= str_pad($nameLines[$i], $lineWidth) . "\n"; // Baris dengan nama saja
                }
                // Baris terakhir dengan Qty dan Harga
                $lastLine = $nameLines[count($nameLines) - 1]; // Baris terakhir dari nama
                $lastLine = str_pad($lastLine, $nameWidth);   // Tambahkan padding untuk nama
                $qty = str_pad($qty, $qtyWidth, " ", STR_PAD_BOTH); // Qty di tengah
                $price = str_pad($price, $priceWidth, " ", STR_PAD_LEFT); // Harga di kanan
                // Gabungkan semua
                $output .= $lastLine . $qty . $price;
                return $output;
            }
            /* Mulai mencetak */
            $number = Sale::max('reference');
            $totpay = DB::table('sales')->where('reference', $number)->first();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($settings->company_name . "\n");
            $printer->text($settings->company_address . "\n");
            $printer->text("Tlp : " . $settings->company_phone . "\n");
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No. Sales : " . $number . "\n");
            $pembeli = $request->input('customer_name');
            $tgl = now()->format('d-m-Y H:i:s');  //date("Y-m-d H:i:s")
            $printer->text("Tgl : ");
            $printer->text($tgl . "\n");
            $printer->text("Nama Customer : ");
            $printer->text($pembeli . "\n");
            $printer->text("--------------------------------\n");
            $printer->text(alignRight("Nama Produk", "Qty", "Harga", $lineWidth) . "\n");
            $printer->text("--------------------------------\n");
            $total = 0;
            foreach (Cart::instance('sale')->content() as $cart_item) {
                SaleDetails::create([
                    $nama = $cart_item->name,
                    $qty = number_format($cart_item->qty),
                    $sub_total =  ($cart_item->qty * $cart_item->price),
                ]);
                $printer->text(alignRight($nama, $qty, number_format($sub_total), $lineWidth) . "\n");
                $total += ($cart_item->qty * $cart_item->price);
            }

            $printer->text("--------------------------------\n");
            $printer->setEmphasis(true); // Tebal
            $printer->text(alignRight("SUB TOTAL", "", number_format($total), $lineWidth) . "\n");
            $printer->text(alignRight("TOTAL BAYAR", "", number_format($totpay->paid_amount), $lineWidth) . "\n");
            $printer->text(alignRight("KEMBALIAN", "", number_format($totpay->paid_amount - $total), $lineWidth) . "\n");
            $printer->setEmphasis(false); // Tebal
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            //  $printer->text("Terima Kasih Atas Kunjungan Anda!\n");
            $printer->text("--------------------------------\n");
            // Potong kertas
            $printer->cut();
            // Tutup koneksi
            $printer->close();
            //  return "Struk berhasil dicetak!";
        } catch (Exception $err) {
            return "Gagal mencetak: " . $err->getMessage() . "\n";
        }
        //------------------------------------------------------------------------------//
        Cart::instance('sale')->destroy();

        toast('POS Sale Created!', 'success');

        //  return redirect()->route('sales.cetakstruk');
        /* foreach (Cart::instance('sale')->content() as $cart_item) {
         $data = [
          'product_name' => $cart_item->name,
          'product_code' => $cart_item->options->code,
          'created_at' => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s"),
          'message' => 'Sale Created!'
         ];
         return response()->json($data);
        } */
        //return response()->json( [$data] );
        //return response()->json($data);
        //return redirect()->route('sales.cetakstruk');
        //   return redirect()->route('sales.index')->with('message', 'Data Sales Successfully Saved to Database!');
        return redirect()->route('app.pos.index')->with('message', 'Data Sales Successfully Saved to Database!'); //==>ini kembali kelayar inputan POS
    }

    /*--------------------------Add by Chris----------------------------------------------- */
    public function saveorder(Request $request)
    {
        DB::transaction(function () use ($request) {

            $order = Order::create([
                'date' => now()->format('Y-m-d'),
                'reference' => $this->generateOrderNumber(),
                //   'reference' => $order->reference,
                //   'customer_id' => $request->customer_id,
                //   'customer_name' => Customer::findOrFail($request->customer_id)->customer_name,
                'customer_name' => $request->input('customer_name'),
                'total_amount' => $request->total_amount,
            ]);

            $total = 0;
            foreach (Cart::instance('sale')->content() as $cart_item) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'reference' => $order->reference,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    // 'product_code' => Cart::instance($this->cart_instance)->content(),
                    'quantity' => $cart_item->qty,
                    'price' => $cart_item->price,
                    'unit_price' => $cart_item->options->unit_price,
                    'sub_total' => $cart_item->options->sub_total,
                ]);
                $total += ($cart_item->qty * $cart_item->price);
                $order->total_amount = $total;
                $order->save();
            }
            //  Cart::instance('sale')->destroy();
        });

        //------------------------------------------------------------------------------//
        try {
            // Buat koneksi ke printer
            $settings = Setting::first();
            //  $connector = new NetworkPrintConnector($printerIp, $port);
            //  $connector = new WindowsPrintConnector("\\SVR-01\POS-58");
            $connector = new WindowsPrintConnector($settings->name_printer);
            //  $connector = new WindowsPrintConnector("//10.10.10.150/POS-58");
            $printer = new Printer($connector);
            $lineWidth = 32;
            // Fungsi untuk merapikan teks
            function rataKanan($name, $qty, $price, $lineWidth)
            {
                $nameWidth = 16; // Alokasi 16 karakter untuk nama produk
                $qtyWidth = 8;   // Alokasi 8 karakter untuk Qty
                $priceWidth = 8; // Alokasi 8 karakter untuk Harga

                // Bungkus nama produk jika panjangnya melebihi alokasi
                $nameLines = str_split($name, $nameWidth);
                // Siapkan variabel untuk hasil format
                $output = '';
                // Tambahkan semua baris nama produk kecuali yang terakhir
                for ($i = 0; $i < count($nameLines) - 1; $i++) {
                    $output .= str_pad($nameLines[$i], $lineWidth) . "\n"; // Baris dengan nama saja
                }
                // Baris terakhir dengan Qty dan Harga
                $lastLine = $nameLines[count($nameLines) - 1]; // Baris terakhir dari nama
                $lastLine = str_pad($lastLine, $nameWidth);   // Tambahkan padding untuk nama
                $qty = str_pad($qty, $qtyWidth, " ", STR_PAD_BOTH); // Qty di tengah
                $price = str_pad($price, $priceWidth, " ", STR_PAD_LEFT); // Harga di kanan
                // Gabungkan semua
                $output .= $lastLine . $qty . $price;
                return $output;
            }
            /* Mulai mencetak */
            $number = Order::max('reference');
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($settings->company_name . "\n");
            $printer->text($settings->company_address . "\n");
            $printer->text("Tlp : " . $settings->company_phone . "\n");
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No. Order : " . $number . "\n");
            //  $printer->text($number. "\n");
            $pembeli = $request->input('customer_name');
            $tgl = now()->format('d-m-Y H:i:s');  //date("Y-m-d H:i:s")
            $printer->text("Tgl : ");
            $printer->text($tgl . "\n");
            $printer->text("Nama Customer : ");
            $printer->text($pembeli . "\n");
            $printer->text("--------------------------------\n");
            $printer->text(rataKanan("Nama Produk", "Qty", "Harga", $lineWidth) . "\n");
            $printer->text("--------------------------------\n");
            $total = 0;
            foreach (Cart::instance('sale')->content() as $cart_item) {
                OrderDetails::create([
                    $nama = $cart_item->name,
                    $qty = number_format($cart_item->qty),
                    $sub_total =  ($cart_item->qty * $cart_item->price),
                ]);
                $printer->text(rataKanan($nama, $qty, number_format($sub_total), $lineWidth) . "\n");
                $total += ($cart_item->qty * $cart_item->price);
            }

            $printer->text("--------------------------------\n");
            $printer->setEmphasis(true); // Tebal
            $printer->text(rataKanan("TOTAL", "", number_format($total), $lineWidth) . "\n");
            $printer->setEmphasis(false); // Tebal
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            //    $printer->text("Terima Kasih Atas Kunjungan Anda!\n");
            $printer->text("--------------------------------\n");
            // Potong kertas
            $printer->cut();
            // Tutup koneksi
            $printer->close();
            //  return "Struk berhasil dicetak!";
        } catch (Exception $err) {
            return "Gagal mencetak: " . $err->getMessage() . "\n";
        }
        //------------------------------------------------------------------------------//
        Cart::instance('sale')->destroy();
        toast('POS Order Created!', 'success');

        /* foreach (Cart::instance('sale')->content() as $cart_item) {
            $data = [
              'product_name' => $cart_item->name,
              'product_code' => $cart_item->options->code,
              'created_at' => date("Y-m-d H:i:s"),
              'updated_at' => date("Y-m-d H:i:s"),
              'message' => 'Order Created!'
            ];
            return response()->json($data);
        } */
        return redirect()->route('app.pos.index')->with('message', 'Data Order Successfully Saved to Database!'); //==>ini kembali kelayar inputan POS
        // return view('prints.receipt_thermal')->with('message', 'Data Order Successfully Saved to Database!');
        /*  $sale = Order::find('id');
      $saleDetail = OrderDetails::find('order_id');
      return cetakStruk($salesId); */
        //  return view('prints.receipt_thermal')->with('message', 'Print Order Successfully!');
        // $order = Order::with('products')->where('reference', $request->reference)->first();

        //  $transaction = Order::with('order_details.product', 'order')->where('reference', $request->reference)->firstOrFail();
        //  return view('print.nota', compact('transaction'))->with('message', 'Print Order Successfully!');
    }

    public function showorder()
    {
        Cart::instance('sale')->destroy();

        $orders = Order::all();  // Add by Chris
        //return view('order::pos.index', compact('orders'));
        return view('app.pos.index', compact('orders'));
    }

    public function cetakstruk($salesId)
    {
        // Ambil data Sale dari database
        $sale = Order::find($salesId);
        $saleDetail = OrderDetails::find($salesId);
        //    $sale = Sale::findOrFail($salesId);
        //return
        // $dataTable->render('sale::payments.index', compact('order'));
        //  $order = Order::with('orders')->where('id', $salesId)->first();
        // Kirim data ke view struk
        return view('prints.receipt_thermal', compact('order'))->with('message', 'Print Order Successfully!');
    }

    public function invoice($no_order)
    {
        $order = Order::with('productOrder')->where('no_order', $no_order)->first();
        return view('kasir.invoice', compact('order'));
    }

    public function printInvoice($id)
    {
        /* $this->authorize('view', Sale::class);
        $companySetting = CompanySetting::first();
        $sale = Sale::with(['saleDetails.product','customer', 'salePayment'])->where('id',$id)->first();
        return response()->stream(function() use($companySetting, $sale) {
            echo view('admin.sales._invoice-print', ['sale' => $sale, 'companySetting' => $companySetting ])->render();
        }
        ); */
    }

    public function printNota()
    {
        try {
            //  $sale = Order::find($salesId);
            //  $saleDetail = OrderDetails::find($salesId);

            //  $connector = new WindowsPrintConnector("\\wind7\usb\epson");
            //  $connector = new WindowsPrintConnector("POS-5802DD");
            $connector = new WindowsPrintConnector("POS-58");
            // $printer = new Escpos($connector);
            $printer = new Printer($connector);

            foreach (Cart::instance('sale')->content() as $cart_item) {
                OrderDetails::create([
                    $printer->text($cart_item->name),
                    $printer->text($cart_item->qty),
                    $printer->text($cart_item->price),
                    /*    'unit_price' => $cart_item->options->unit_price,
                        'sub_total' => $cart_item->options->sub_total,  */
                ]);
            }
            // $printer -> text("Hello World!\n");
            $printer->cut();
            $printer->close();
        } catch (ValidationException $err) {
            echo "Couldn't print to this printer: " . $err->getMessage() . "\n";
        }
        return view('pos.receipt_thermal', compact('order'))->with('message', 'Print Order Successfully!');
    }

    public function print(Request $request)
    {
        //get transaction
        $transaction = Order::with('order_details.product', 'customer')->where('reference', $request->reference)->firstOrFail();
        //return view
        return view('print.nota', compact('transaction'))->with('message', 'Print Order Successfully!');
    }

    public function cetaklangsung(Request $request)
    {
        // Ganti dengan IP Address printer Anda
        // $printerIp = '192.168.1.100';
        // $printerIp ='//10.10.10.150/POS-58';
        //  $printerIp = '10.10.10.150';
        //  $port = 9100; //9001; // Port default untuk printer Epson/POS //Printer thermal bluetooth Taffware 5802.

        try {
            // Buat koneksi ke printer
            $settings = Setting::first();
            $connector = new WindowsPrintConnector($settings->name_printer);
            //  $connector = new NetworkPrintConnector($printerIp, $port);
            //  $connector = new WindowsPrintConnector("\\SVR-01\POS-58");
            //  $connector = new WindowsPrintConnector("POS-58");
            //  $connector = new WindowsPrintConnector("//10.10.10.150/POS-58");
            $printer = new Printer($connector);
            //------------------------------------------------------------------------------//
            $lineWidth = 32;
            // Fungsi untuk merapikan teks
            function formatRow($name, $qty, $price, $lineWidth)
            {
                $nameWidth = 16; // Alokasi 16 karakter untuk nama produk
                $qtyWidth = 8;   // Alokasi 8 karakter untuk Qty
                $priceWidth = 8; // Alokasi 8 karakter untuk Harga

                // Bungkus nama produk jika panjangnya melebihi alokasi
                $nameLines = str_split($name, $nameWidth);
                // Siapkan variabel untuk hasil format
                $output = '';
                // Tambahkan semua baris nama produk kecuali yang terakhir
                for ($i = 0; $i < count($nameLines) - 1; $i++) {
                    $output .= str_pad($nameLines[$i], $lineWidth) . "\n"; // Baris dengan nama saja
                }
                // Baris terakhir dengan Qty dan Harga
                $lastLine = $nameLines[count($nameLines) - 1]; // Baris terakhir dari nama
                $lastLine = str_pad($lastLine, $nameWidth);   // Tambahkan padding untuk nama
                $qty = str_pad($qty, $qtyWidth, " ", STR_PAD_BOTH); // Qty di tengah
                $price = str_pad($price, $priceWidth, " ", STR_PAD_LEFT); // Harga di kanan
                // Gabungkan semua
                $output .= $lastLine . $qty . $price;
                return $output;
            }
            //------------------------------------------------------------------------------//
            //  $settings = Setting::first();
            /* Mulai mencetak */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($settings->company_name . "\n");
            $printer->text($settings->company_address . "\n");
            $printer->text("Tlp : " . $settings->company_phone . "\n");
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $pembeli = $request->input('customer_name');
            $tgl = now()->format('d-m-Y H:i:s');  //date("Y-m-d H:i:s")
            $printer->text("Tgl : ");
            $printer->text($tgl . "\n");
            $printer->text("Nama Customer : ");
            $printer->text($pembeli . "\n");
            $printer->text("--------------------------------\n");
            //   $printer->text("================================\n");
            $printer->text(formatRow("Nama Produk", "Qty", "Harga", $lineWidth) . "\n");
            $printer->text("--------------------------------\n");
            //  $printer->feed(1);
            $total = 0;
            foreach (Cart::instance('sale')->content() as $cart_item) {
                OrderDetails::create([
                    $nama = $cart_item->name,
                    $qty = number_format($cart_item->qty),
                    //  $harga = number_format($cart_item->price),
                    //  $sub_total =  number_format($cart_item->qty * $cart_item->price),
                    $sub_total =  ($cart_item->qty * $cart_item->price),
                ]);
                $printer->text(formatRow($nama, $qty, number_format($sub_total), $lineWidth) . "\n");
                $total += ($cart_item->qty * $cart_item->price);
            }

            $printer->text("--------------------------------\n");
            $printer->setEmphasis(true); // Tebal
            $printer->text(formatRow("TOTAL", "", number_format($total), $lineWidth) . "\n");
            $printer->setEmphasis(false); // Tebal
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            //  $printer->text("================================\n");
            $printer->text("Terima Kasih Atas Kunjungan Anda!\n");
            $printer->text("--------------------------------\n");

            // Potong kertas
            $printer->cut();

            // Tutup koneksi
            $printer->close();

            //  return "Struk berhasil dicetak!";
            //  $transaction = Order::with('order_details.product', 'order')->where('reference', $request->customer_name)->firstOrFail();
            //  return view('print.nota', compact('transaction'))->with('message', 'Order Successfully to print!');
            return redirect()->route('app.pos.index')->with('message', 'Struk berhasil dicetak!'); //==>ini kembali kelayar inputan POS
        } catch (Exception $err) {
            return "Gagal mencetak: " . $err->getMessage() . "\n";
        }
    }

    public function printdata($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);

        try {
            // Enter the IP address and port of your thermal printer
            // Port is usually 9100
            $connector = new NetworkPrintConnector("192.168.1.123", 9100);

            // For USB printers on Linux, it might be:
            // $connector = new FilePrintConnector("/dev/usb/lp0");

            $printer = new Printer($connector);

            /* Center align */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("My Awesome Shop\n");
            $printer->text("123 Laravel Lane\n\n");

            /* Left align */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Order #: {$order->id}\n");
            $printer->text("Date: {$order->created_at->format('Y-m-d H:i')}\n");
            $printer->text("--------------------------------\n");

            // To align columns, we use str_pad to create fixed-width lines
            $lineFormat = "%-20s %10s"; // Left-aligned string, then a right-aligned string
            $printer->text(sprintf($lineFormat, "Item", "Price") . "\n");
            $printer->text("--------------------------------\n");

            foreach ($order->items as $item) {
                $price = 'Rp' . number_format($item->price, 2);
                $printer->text(sprintf($lineFormat, $item->name, $price) . "\n");
            }

            $printer->text("--------------------------------\n");

            /* RIGHT ALIGN a line of text */
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text('Subtotal: $' . number_format($order->subtotal, 2) . "\n");
            $printer->text('Tax: $' . number_format($order->tax, 2) . "\n");
            $printer->setEmphasis(true); // Bold
            $printer->text('Total: $' . number_format($order->total, 2) . "\n");
            $printer->setEmphasis(false); // Unbold

            /* Feed, cut, and close */
            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return "Receipt sent to printer!";
        } catch (Exception $e) {
            return "Couldn't print to this printer: " . $e->getMessage() . "\n";
        }
    }

    public function generateOrderNumber(): string
    {
        // Mulai transaksi database untuk mencegah race condition
        return DB::transaction(function () {
            $prefix = 'DO/' . date('Ym') . '/';

            // Cari order terakhir untuk bulan dan tahun ini dengan lock
            // lockForUpdate() akan mencegah baris lain membaca record ini sampai transaksi selesai
            $lastOrder = Order::where('reference', 'like', $prefix . '%')
                ->orderBy('reference', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastOrder) {
                // Ambil nomor urut dari nomor order terakhir
                // Contoh: dari "DO/202510/0005", kita ambil "0005"
                $lastNumber = (int) substr($lastOrder->reference, -4);
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

    /*------------------------------------------------------------------------------- */
}
