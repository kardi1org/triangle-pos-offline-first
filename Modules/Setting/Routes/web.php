<?php

use Modules\Setting\Http\Controllers\WarehouseController;
use Modules\Setting\Http\Controllers\OrderSummaryController;
use Modules\Setting\Http\Controllers\RecipeController; // Import ini
use Modules\Setting\Http\Controllers\SettingController;
use Modules\Setting\Http\Controllers\UnitsController;
use Modules\Setting\Http\Controllers\PaymentsController;

Route::group(['middleware' => 'auth'], function () {

    // Mail Settings
    Route::patch('/settings/smtp', [SettingController::class, 'updateSmtp'])->name('settings.smtp.update');

    // General Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Units
    Route::resource('units', UnitsController::class)->except('show');

    // Payments
    Route::get('payment', [PaymentsController::class, 'index'])->name('payment.index');
    Route::patch('payment', [PaymentsController::class, 'update'])->name('payment.update');

    // Order Summary
    Route::get('/order-summary-settings', [OrderSummaryController::class, 'index'])->name('order-summary.index');
    Route::put('/order-summary-settings/{id}', [OrderSummaryController::class, 'update'])->name('order-summary.update');

    // Warehouses
    Route::resource('warehouses', WarehouseController::class)->except(['create', 'show', 'edit']);

    // 🎯 1. Ambil data AJAX wajib di atas resource agar tidak terbaca sebagai ID resep ({recipe})
    Route::get('/recipes/product-data/{id}', [RecipeController::class, 'getProductData'])->name('recipes.product-data');

    // 🎯 2. Cukup panggil SATU resource resep saja
    Route::resource('recipes', RecipeController::class);
});
