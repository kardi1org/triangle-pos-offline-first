<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function () {

    // Rute kustom wajib di atas resource
    Route::get('mejas/floor-plan', [\Modules\Meja\Http\Controllers\MejaController::class, 'floorPlanDesigner'])->name('mejas.floor_plan');
    Route::post('mejas/save-layout', [\Modules\Meja\Http\Controllers\MejaController::class, 'saveMassLayout'])->name('mejas.save_layout');

    Route::resource('mejas', '\Modules\Meja\Http\Controllers\MejaController');
});
