<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use Modules\Product\Http\Controllers\VariantController;

Route::group(['middleware' => 'auth'], function () {
    //Print Barcode
    Route::get('/products/print-barcode', 'BarcodeController@printBarcode')->name('barcode.print');
    //Product
    Route::resource('products', 'ProductController');
    //Product Category
    Route::resource('product-categories', 'CategoriesController')->except('create', 'show');

    Route::post('/products/{id}/variants/save', [\Modules\Product\Http\Controllers\ProductController::class, 'saveVariants'])
        ->name('products.variants.save');

    Route::get('/variants/list/{productId}', [VariantController::class, 'listByProduct']);

    Route::get('/variants/list/{productId}', [VariantController::class, 'listByProduct'])
        ->name('variants.list');
});
