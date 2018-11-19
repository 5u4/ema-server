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
       Route::post('/search', 'TransactionController@search');
       Route::put('/{id}', 'TransactionController@update');
       Route::get('/', 'TransactionController@index');
       Route::get('/{id}', 'TransactionController@show');
       Route::delete('/{id}', 'TransactionController@delete');
    });

    /* Tag */
    Route::group(['prefix' => 'tags', 'middleware' => 'auth'], function () {
        Route::get('/', 'TagController@index');
        Route::post('/', 'TagController@store');
        Route::patch('/{tagId}', 'TagController@update');
        Route::delete('/{tagId}', 'TagController@destroy');
    });

    /* User */
    Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {
        Route::get('/', 'UserController@index');
        Route::get('/{user}', 'UserController@show');
        Route::get('/friends/suggestions', 'UserController@commonfriends');
        Route::get('/search/{input}','UserController@search');
        Route::post('/followings/{user}', 'UserController@follow');
        Route::delete('/followings/{user}', 'UserController@unfollow');
    });

    /* Avatar */
    Route::group(['prefix' => 'avatars'], function () {
        Route::get('/{user}', 'AvatarController@show');
        Route::post('/', 'AvatarController@store')->middleware('auth');
    });

    /* Dining */
    Route::group(['prefix' => 'dining', 'middleware' => 'auth'], function () {
        Route::get('/restaurant_search/{input_text}', 'DiningController@index');
    });

    /* Movie */
    Route::group(['prefix' => 'movies', 'middleware' => 'auth'], function(){
        Route::get('/', 'MovieController@index');
        Route::post('/','MovieController@store');
        Route::delete('/{movieId}','MovieController@destroy');
    });
});
