<?php

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

use App\Http\Middleware\CanDisableUser;
use App\Http\Middleware\CanModUser;
use App\Http\Middleware\CanReadUser;

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
        Route::post('/invite', 'UserController@sendmail');
        Route::get('/', 'UserController@index')->middleware(CanReadUser::class);
        Route::get('/{user}', 'UserController@show');
        Route::get("/friends/index", 'UserController@friends');
        Route::get('/friends/suggestions', 'UserController@commonfriends');
        Route::post('/search','UserController@search');
        Route::get('/follows/{user}', 'UserController@isFollowing');
        Route::post('/followings/{user}', 'UserController@follow');
        Route::patch('/', 'UserController@update');
        Route::delete('/followings/{user}', 'UserController@unfollow');
        Route::put('/{user}/disable', 'UserController@disable')->middleware(CanDisableUser::class);
        Route::put('/{id}/restore', 'UserController@enable')->middleware(CanDisableUser::class);
        Route::patch('/{user}/permissions/{permission}/enable', 'UserController@enableUserPermission')->middleware(CanModUser::class);
        Route::patch('/{user}/permissions/{permission}/disable', 'UserController@disableUserPermission')->middleware(CanModUser::class);
    });

    /* Permission */
    Route::group(['prefix' => 'permissions'], function () {
        Route::get('/', 'PermissionController@index');
        Route::get('/{user}', 'PermissionController@show')->middleware('auth');
    });

    /* Avatar */
    Route::group(['prefix' => 'avatars'], function () {
        Route::get('/{user}', 'AvatarController@show');
        Route::post('/', 'AvatarController@store')->middleware('auth');
    });

    /* Dining */
    Route::group(['prefix' => 'dining','middleware' => 'auth'], function () {
        Route::post('/search', 'DiningController@index');
        Route::get('/search', 'DiningController@findFavouriteRestaurants');
    });

    /* Movie */
    Route::group(['prefix' => 'movies', 'middleware' => 'auth'], function(){
        Route::get('/', 'MovieController@index');
        Route::post('/','MovieController@store');
        Route::delete('/{movieId}','MovieController@destroy');
    });

    /* Review */
    Route::group(['prefix' => 'review', 'middleware' => 'auth'], function(){
        Route::get('/{movieId}', 'ReviewController@index');
        Route::post('/','ReviewController@store');
        Route::delete('/{reviewId}','ReviewController@destroy');
    });

    /* Log */
    Route::group(['prefix' => 'logs', 'middleware' => 'auth'], function () {
        Route::get('/', 'LogController@index');
        Route::get('/{user}', 'LogController@show');
    });

    /* Stat */
    Route::group(['prefix' => 'stats', 'middleware' => ['auth', CanReadUser::class]], function () {
        Route::get('/new-users', 'StatController@getNewUserStats');
        Route::get('/user-activities', 'StatController@getActivityStats');
    });
});
