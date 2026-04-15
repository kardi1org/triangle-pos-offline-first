<?php

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

    //Expense Category
    Route::resource('expense-categories', 'ExpenseCategoriesController')->except('show', 'create');
    //Expense
    Route::resource('expenses', 'ExpenseController')->except('show');

    Route::get('/cashtransfer/index', 'TunaiTransferController@index')->name('cashtransfer.index'); //Add by Chris
    Route::get('/cashtransfer/create', 'TunaiTransferController@create')->name('cashtransfer.create'); //Add by Chris
    Route::post('/cashtransfer/store', 'TunaiTransferController@store')->name('cashtransfer.store'); //Add by Chris
    Route::get('/cashtransfer/{expense?}/edit', 'TunaiTransferController@edit')->name('cashtransfer.edit'); //Add by Chris
    //Route::get('/cashtransfer/{transfer?}', 'TunaiTransferController@edit')->name('cashtransfer.edit'); //Add by Chris
    Route::delete('/cashtransfer/destroy/{expense?}', 'TunaiTransferController@destroy')->name('cashtransfer.destroy'); //Add by Chris
    Route::patch('/cashtransfer/update/{expense?}', 'TunaiTransferController@update')->name('cashtransfer.update'); //Add by Chris
});
