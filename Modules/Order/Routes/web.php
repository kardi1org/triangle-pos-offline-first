<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Modules\Order\Http\Controllers\PosOrderController;
//use  Illuminate\Snappy\Facades\SnappyPdf;
//use Spatie\LaravelPdf\Facades\Pdf;
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

Route::group(['middleware' => 'auth'], function () {

    //POS
    Route::get('/app/pos', 'PosController@index')->name('app.pos.index'); //bawaan asli project

    //Route::get('/app/pos', 'PosController@name')->name('app.pos.index');
   // Route::post('/app/pos', 'PosController@store')->name('app.pos.store');
  //  Route::get('/saveorder', 'PosOrderController@saveorder');
  //  Route::post('/saveorder', 'PosOrderController@saveorder')->name('app.pos.saveorder');  //Add by Chris
    //Route::post('/saveorder', 'PosOrderController@store')->name('save-order'); //Add by Chris
    Route::post('/saveorder', 'PosOrderController@store')->name('saveorder.store'); //Add by Chris

    //Generate PDF
    /* Route::get('/sales/pdf/{id}', function ($id) {
        $sale = \Modules\Order\Entities\Order::findOrFail($id);
      //  $customer = \Modules\People\Entities\Customer::findOrFail($sale->customer_id);
        $customer = \Modules\People\Entities\Customer::findOrFail($sale->customer_name);

        $pdf = \PDF::loadView('sale::print', [
            'sale' => $sale,
            'customer' => $customer,
        ])->setPaper('a4');

        return $pdf->stream('sale-'. $sale->reference .'.pdf');
    })->name('sales.pdf'); */

    /* Route::get('/sales/pos/pdf/{id}', function ($id) {
        $sale = \Modules\Order\Entities\Order::findOrFail($id);

        $pdf = \PDF::loadView('sale::print-pos', ['sale' => $sale,
        ])->setPaper('a7')
            ->setOption('margin-top', 8)
            ->setOption('margin-bottom', 8)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5);

        return $pdf->stream('sale-'. $sale->reference .'.pdf');
    })->name('sales.pos.pdf'); */

    //Order
    //Route::resource('orders', 'OrderController');

    //Payments
    Route::get('/sale-payments/{sale_id}', 'SalePaymentsController@index')->name('sale-payments.index');
    Route::get('/sale-payments/{sale_id}/create', 'SalePaymentsController@create')->name('sale-payments.create');
    Route::post('/sale-payments/store', 'SalePaymentsController@store')->name('sale-payments.store');
    Route::get('/sale-payments/{sale_id}/edit/{salePayment}', 'SalePaymentsController@edit')->name('sale-payments.edit');
    Route::patch('/sale-payments/update/{salePayment}', 'SalePaymentsController@update')->name('sale-payments.update');
    Route::delete('/sale-payments/destroy/{salePayment}', 'SalePaymentsController@destroy')->name('sale-payments.destroy');
});

