<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/work_place', 'WorkPlaceController@store');
    Route::put('/work_place/{workPlace}', 'WorkPlaceController@update');
    Route::delete('/work_place/{workPlace}', 'WorkPlaceController@destroy');
    Route::get('/work_place/{workPlace}', 'WorkPlaceController@show');
    Route::get('/work_place', 'WorkPlaceController@index');

    Route::post('/worker', 'WorkerController@store');
    Route::put('/worker/{worker}', 'WorkerController@update');
    Route::delete('/worker/{worker}', 'WorkerController@destroy');
    Route::get('/worker/{shift}', 'WorkerController@show');
    Route::get('/workers/{?workPlace}', 'WorkerController@index');

    Route::post('/shift', 'ShiftController@store');
    Route::put('/shift/{shift}', 'ShiftController@update');
    Route::delete('/shift/{shift}', 'ShiftController@destroy');
    Route::get('/shift/{shift}', 'ShiftController@show');
    Route::get('/shifts/{?workPlace}', 'ShiftController@index');
});
