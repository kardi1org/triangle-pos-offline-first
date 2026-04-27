<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryMovement\Http\Controllers\InventoryMovementController;

Route::group(['middleware' => 'auth'], function () {
    Route::resource('inventory-movement', InventoryMovementController::class)->names([
        'index'   => 'inventory-movements.index',
        'create'  => 'inventory-movements.create',
        'store'   => 'inventory-movements.store',
        'show'    => 'inventory-movements.show',
        'edit'    => 'inventory-movements.edit',
        'update'  => 'inventory-movements.update',
        'destroy' => 'inventory-movements.destroy',
    ]);
});
