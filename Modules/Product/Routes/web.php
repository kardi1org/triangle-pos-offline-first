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
    // Ganti baris ini:
    // Route::resource('products', 'ProductController');

    // Menjadi seperti ini (menggunakan {id} berupa string biasa, bukan {product} instansiasi model):
    Route::get('products', 'ProductController@index')->name('products.index');
    Route::get('products/create', 'ProductController@create')->name('products.create');
    Route::post('products', 'ProductController@store')->name('products.store');
    Route::get('products/{id}', 'ProductController@show')->name('products.show');
    Route::get('products/{id}/edit', 'ProductController@edit')->name('products.edit');
    Route::match(['put', 'patch'], 'products/{id}', 'ProductController@update')->name('products.update');
    Route::delete('products/{id}', 'ProductController@destroy')->name('products.destroy');
    //Product Category
    Route::resource('product-categories', 'CategoriesController')->except('create', 'show');

    Route::post('/products/{id}/variants/save', [\Modules\Product\Http\Controllers\ProductController::class, 'saveVariants'])
        ->name('products.variants.save');

    Route::get('/variants/list/{productId}', [VariantController::class, 'listByProduct']);

    Route::get('/variants/list/{productId}', [VariantController::class, 'listByProduct'])
        ->name('variants.list');
});
