<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Version 1 */
Route::group(['prefix' => 'v1'], function () {
    /* Auth */
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
    });

    /* Transaction */
    Route::group(['prefix' => 'transaction', 'middleware' => 'auth'], function () {
       Route::post('/', 'TransactionController@create');
       Route::get('/', 'TransactionController@index');
       Route::get('/{id}', 'TransactionController@show');
    });

    /* Movie */
    Route::group(['prefix' => 'movie', 'middleware' => 'auth'], function () {
        Route::post('/', 'MovieCOntroller@create');
        Route::get('/', 'MovieController@name');
    });
});
