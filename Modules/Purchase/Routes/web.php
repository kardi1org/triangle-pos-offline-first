<?php

use Modules\Purchase\Http\Controllers\PurchaseController;
use Modules\Purchase\Http\Controllers\PurchasePaymentsController;

Route::group(['middleware' => 'auth'], function () {

    // Generate PDF
    Route::get('/purchases/pdf/{id}', function ($id) {
        $purchase = \Modules\Purchase\Entities\Purchase::findOrFail($id);
        $supplier = \Modules\People\Entities\Supplier::findOrFail($purchase->supplier_id);

        $pdf = \PDF::loadView('purchase::print', [
            'purchase' => $purchase,
            'supplier' => $supplier,
        ])->setPaper('a4');

        return $pdf->stream('purchase-' . $purchase->reference . '.pdf');
    })->name('purchases.pdf');


    // =========================================================================
    // EXPLICIT ROUTE PURCHASES (Aman untuk Linux Server)
    // =========================================================================
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases/store', [PurchaseController::class, 'store'])->name('purchases.store');

    // Parameter {purchase} wajib sinkron dengan variabel penampung di Controller
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');

    // Menggunakan match agar mendukung @method('patch') maupun @method('put') dari Blade
    Route::match(['PUT', 'PATCH'], '/purchases/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    // =========================================================================


    // Payments (Diubah ke Class Base agar stabil di Linux)
    Route::get('/purchase-payments/{purchase_id}', [PurchasePaymentsController::class, 'index'])->name('purchase-payments.index');
    Route::get('/purchase-payments/{purchase_id}/create', [PurchasePaymentsController::class, 'create'])->name('purchase-payments.create');
    Route::post('/purchase-payments/store', [PurchasePaymentsController::class, 'store'])->name('purchase-payments.store');
    Route::get('/purchase-payments/{purchase_id}/edit/{purchasePayment}', [PurchasePaymentsController::class, 'edit'])->name('purchase-payments.edit');
    Route::patch('/purchase-payments/update/{purchasePayment}', [PurchasePaymentsController::class, 'update'])->name('purchase-payments.update');
    Route::delete('/purchase-payments/destroy/{purchasePayment}', [PurchasePaymentsController::class, 'destroy'])->name('purchase-payments.destroy');
});
