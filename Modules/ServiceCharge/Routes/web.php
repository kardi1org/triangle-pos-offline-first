<?php

use Illuminate\Support\Facades\Route;
use Modules\ServiceCharge\Http\Controllers\ServiceChargeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth'], function () {

    // Route Prefix untuk Service Charge
    Route::prefix('service-charge')->group(function () {

        // Halaman List & Form (Index)
        Route::get('/', [ServiceChargeController::class, 'index'])
            ->name('service-charge.index');

        // Simpan Data Baru
        Route::post('/store', [ServiceChargeController::class, 'store'])
            ->name('service-charge.store');

        // Update Data (Jika diperlukan nantinya)
        Route::put('/update/{id}', [ServiceChargeController::class, 'update'])
            ->name('service-charge.update');

        // Toggle Status Aktif/Non-Aktif (Opsional tapi berguna)
        Route::get('/toggle-status/{id}', [ServiceChargeController::class, 'toggleStatus'])
            ->name('service-charge.toggle');
    });
});
