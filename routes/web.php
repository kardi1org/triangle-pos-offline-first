<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SliderController;
use App\Modules\Order\Http\Controllers\PosOrderController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Auth::routes(['register' => false]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')
        ->name('home');

    //   Route::get('/app/pos', 'PosOrderController@saveorder')->name('app.pos.saveorder');   //Add by Chris

    // Route::prefix('app/pos')->group(function () {
    //     Route::get('/', 'PosOrderController@index')->name('order.index');
    //     Route::get('/save', 'PosOrderController@saveorder')->name('save.saveorder'); //Add by Chris
    // });

    //Route::resource('budgets', 'budgetController')->except('show');

    //Print Struk
    // Route::get('/print/receipt/{salesId}', 'PosController@cetakStruk')->name('print.receipt'); // Add by Chris

    //   Route::post('/save', 'PosOrderController@saveorder')->name('save.saveorder'); //Add by Chris

    Route::get('/sales-purchases/chart-data', 'HomeController@salesPurchasesChart')
        ->name('sales-purchases.chart');

    Route::get('/current-month/chart-data', 'HomeController@currentMonthChart')
        ->name('current-month.chart');

    Route::get('/payment-flow/chart-data', 'HomeController@paymentChart')
        ->name('payment-flow.chart');

    // Route::get('/sliders', [SliderController::class, 'index']);
    //Route::get('/budget', [BudgetController::class, 'index']);

    //Route::get('/budget', function () {
    //    echo 'Hello World';
    // });

    // Route::get('/budget', 'BudgetController@index')
    //     ->name('budget.index');
    // Route::get('/budget/create', 'BudgetController@create')
    //     ->name('budget.create');
    // Route::post('/budget/store', 'BudgetController@store')
    //     ->name('budget.store');
    // Route::get('/budget/edit/{id}', 'BudgetController@edit')
    //     ->name('budget.edit');
    // Route::put('/budget/update/{id}', 'BudgetController@update')
    //     ->name('budget.update');
    // Route::delete('/budget/{id}', 'BudgetController@destroy')
    //     ->name('budget.destroy');


});
