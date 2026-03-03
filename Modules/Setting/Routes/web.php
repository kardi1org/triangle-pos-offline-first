<?php

use Modules\Setting\Http\Controllers\OrderSummaryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'auth'], function () {

    //Mail Settings
    Route::patch('/settings/smtp', 'SettingController@updateSmtp')->name('settings.smtp.update');
    //General Settings
    Route::get('/settings', 'SettingController@index')->name('settings.index');
    Route::patch('/settings', 'SettingController@update')->name('settings.update');
    // Units
    Route::resource('units', 'UnitsController')->except('show');
    //Route::resource('payment', 'PaymentsController')->except('show');
    Route::get('payment', 'PaymentsController@index')->name('payment.index');
    Route::patch('payment', 'PaymentsController@update')->name('payment.update');

    Route::get('/order-summary-settings', [OrderSummaryController::class, 'index'])->name('order-summary.index');
    Route::put('/order-summary-settings/{id}', [OrderSummaryController::class, 'update'])->name('order-summary.update');
});
