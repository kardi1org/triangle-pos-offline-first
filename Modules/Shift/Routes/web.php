<?php

use Illuminate\Support\Facades\Route;
use Modules\Shift\Http\Controllers\ShiftController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('shift')->group(function () {
        Route::get('/', [ShiftController::class, 'index'])->name('shift.index');
        Route::post('/open', [ShiftController::class, 'openShift'])->name('shift.open');
        Route::post('/close/{id}', [ShiftController::class, 'closeShift'])->name('shift.close');
        Route::post('/transaction/store', [ShiftController::class, 'storeTransaction'])->name('shift.transaction.store');
        Route::get('/report/{id}', [ShiftController::class, 'show'])->name('shift.show');
        Route::get('/reports', [ShiftController::class, 'reportIndex'])->name('shift.reports');
        Route::get('/reports/detail/{id}', [ShiftController::class, 'getShiftDetails'])->name('shift.reports.detail');
        Route::get('/reports/export', [ShiftController::class, 'exportExcel'])->name('shift.reports.export');
        Route::get('/shift/print/{id}', [ShiftController::class, 'print'])->name('shift.print');
    });
});
