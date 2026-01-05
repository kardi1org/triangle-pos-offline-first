<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
//use  Illuminate\Snappy\Facades\SnappyPdf;
//use Knp\Snappy\Pdf;
//use Milon\Barcode\PDF417;
//use Barryvdh\Snappy\Facade\Pdf;
//use PDF;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth', 'check.shift']], function () {

    //POS
    Route::get('/app/pos', 'PosController@index')->name('app.pos.index');
    Route::post('/app/pos', 'PosController@store')->name('app.pos.store');
    Route::post('/pos/update', 'PosController@update')->name('app.pos.update');

    //  Route::get('/show', 'PosController@showorder')->name('show.showorder'); //Add by Chris
    Route::post('/save-order', 'PosController@saveorder')->name('save.saveorder');  //Add by Chris
    //  Route::get('/app/pos', 'PosController@index')->name('app.pos.index');  //Add by Chris

    // Rute untuk mencetak struk
    Route::get('/app/pos/sales/print/{reference}', 'PosController@printReceipt')
        ->name('app.sales.pos.print_receipt');

    //Print Struk '/print/receipt/{salesId}'
    // Route::get('/cetakstruk/{sale_id}', 'PosController@cetakstruk')->name('order.cetakstruk'); // Add by Chris
    // Route::get('/{sale_id}', 'PosController@cetakstruk')->name('order.cetakstruk'); // Add by Chris
    //  Route::post('/order/print', 'PosController@print')->name('order.print');
    //  Route::get('/print-direct', 'PosController@cetaklangsung')->name('app.pos.print');  //Add by Chris
    //  Route::get('/app/pos', 'PosController@index')->name('app.pos.index');  //Add by Chris
    /*  Route::get('/sales/{salesId}', 'PosController@cetakstruk', function ($salesId) {
        $sale = \Modules\Sale\Entities\Sales::findOrFail($salesId);
    })->name('sales.cetakstruk');  */

    // Route::get('/transaksi/{transaksi}/cetak', \App\Livewire\Transaksi\Cetak::class)->name('transaksi.cetak');
    //Route::get('/transactions/print', [PosController::class, 'print'])->name('apps.transactions.print');

    //Generate PDF
    Route::get('/sales/pdf/{id}', function ($id) {
        $sale = \Modules\Sale\Entities\Sale::findOrFail($id);
        //  $customer = \Modules\People\Entities\Customer::findOrFail($sale->customer_id);
        //$customer = \Modules\People\Entities\Customer::findOrFail($sale->customer_name);

        $pdf = \PDF::loadView('sale::print', [
            'sale' => $sale,
            //'customer' => $customer,
        ])->setPaper('a4');

        return $pdf->stream('sale-' . $sale->reference . '.pdf');
    })->name('sales.pdf');

    Route::get('/sales/pos/pdf/{id}', function ($id) {
        $sale = \Modules\Sale\Entities\Sale::findOrFail($id);

        $pdf = \PDF::loadView('sale::print-pos', [
            'sale' => $sale,
        ])->setPaper('a7')
            ->setOption('margin-top', 8)
            ->setOption('margin-bottom', 8)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5);

        return $pdf->stream('sale-' . $sale->reference . '.pdf');
    })->name('sales.pos.pdf');

    //Sales
    Route::resource('sales', 'SaleController');

    //Payments
    Route::get('/sale-payments/{sale_id}', 'SalePaymentsController@index')->name('sale-payments.index');
    Route::get('/sale-payments/{sale_id}/create', 'SalePaymentsController@create')->name('sale-payments.create');
    Route::post('/sale-payments/store', 'SalePaymentsController@store')->name('sale-payments.store');
    Route::get('/sale-payments/{sale_id}/edit/{salePayment}', 'SalePaymentsController@edit')->name('sale-payments.edit');
    Route::patch('/sale-payments/update/{salePayment}', 'SalePaymentsController@update')->name('sale-payments.update');
    Route::delete('/sale-payments/destroy/{salePayment}', 'SalePaymentsController@destroy')->name('sale-payments.destroy');
});
