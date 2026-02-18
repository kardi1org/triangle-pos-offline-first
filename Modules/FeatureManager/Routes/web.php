<?php

use Illuminate\Support\Facades\Route;
use Modules\FeatureManager\Http\Controllers\FeatureManagerController;

Route::prefix('feature-manager')->group(function () {
    Route::get('/', [FeatureManagerController::class, 'index'])->name('feature-manager.index');
    Route::post('/update-permission', [FeatureManagerController::class, 'updatePermission'])->name('feature-manager.update');
});
