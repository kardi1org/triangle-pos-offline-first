<?php

use Illuminate\Support\Facades\Route;
use Modules\FeatureManager\Http\Controllers\FeatureManagerController;

// Tambahkan middleware 'auth' untuk memastikan user harus login
// Opsional: tambahkan middleware 'role:Super Admin' jika Anda menggunakan Spatie Permission
Route::middleware(['auth'])->prefix('feature-manager')->group(function () {

    Route::get('/', [FeatureManagerController::class, 'index'])
        ->name('feature-manager.index');

    Route::post('/update-permission', [FeatureManagerController::class, 'updatePermission'])
        ->name('feature-manager.update');
});
