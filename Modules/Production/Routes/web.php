<?php

use Illuminate\Support\Facades\Route;
use Modules\Production\Http\Controllers\WorkOrderController;

Route::group(['middleware' => ['auth']], function () {

    // 1. AJAX Route (Taruh paling atas)
    Route::get('/work-orders/get-recipe/{id}', [WorkOrderController::class, 'getRecipe'])
        ->name('work-orders.get-recipe');

    // 2. Route untuk Print (Biasanya sangat dibutuhkan di Produksi)
    Route::get('/work-orders/print/{id}', [WorkOrderController::class, 'print'])
        ->name('work-orders.print');

    // 3. Resource Route
    // Pastikan nama resource menggunakan jamak 'work-orders' agar sesuai dengan controller
    Route::resource('work-orders', WorkOrderController::class);
});
