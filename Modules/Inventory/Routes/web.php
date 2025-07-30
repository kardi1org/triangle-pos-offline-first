<?php

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

    //Generate PDF
    Route::get('/inventories/pdf/{id}', function ($id) {
        $Inventory = \Modules\inventory\Entities\inventory::findOrFail($id);
        $supplier = \Modules\People\Entities\Supplier::findOrFail($Inventory->supplier_id);

        $pdf = \PDF::loadView('Inventory::print', [
            'Inventory' => $Inventory,
            'supplier' => $supplier,
        ])->setPaper('a4');

        return $pdf->stream('Inventory-' . $Inventory->reference . '.pdf');
    })->name('Inventories.pdf');

    //Sales
    Route::resource('Inventories', 'InventoryController');

    //Payments
    // Route::get('/inventory-payments/{inventory_id}', 'inventoryPaymentsController@index')->name('inventory-payments.index');
    // Route::get('/inventory-payments/{inventory_id}/create', 'inventoryPaymentsController@create')->name('inventory-payments.create');
    // Route::post('/inventory-payments/store', 'inventoryPaymentsController@store')->name('inventory-payments.store');
    // Route::get('/inventory-payments/{inventory_id}/edit/{inventoryPayment}', 'inventoryPaymentsController@edit')->name('inventory-payments.edit');
    // Route::patch('/inventory-payments/update/{inventoryPayment}', 'inventoryPaymentsController@update')->name('inventory-payments.update');
    // Route::delete('/inventory-payments/destroy/{inventoryPayment}', 'inventoryPaymentsController@destroy')->name('inventory-payments.destroy');
});
