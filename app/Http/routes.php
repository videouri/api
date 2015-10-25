<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::get('/', [
    'as'   => 'home',
    'uses' => 'HomeController@index'
]);

Route::get('results', [
    'as'   => 'results',
    'uses' => 'SearchController@getVdeos'
]);

Route::get('video/{id}/{videoSlug?}', 'VideoController@show');
Route::get('info/{view}/{part?}', 'InfoController@show');

// User auth related methods
Route::get('join', 'Auth\AuthController@getRegister');
Route::post('join', 'Auth\AuthController@postRegister');
Route::get('login/{provider?}', 'Auth\AuthController@getLogin');
Route::post('login', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

// User panel
Route::group([
    'prefix'     => 'user/{name}',
    'middleware' => 'auth',
    'namespace'  => 'User'
], function () {
    Route::resource('profile', 'ProfileController');
    Route::resource('history', 'HistoryController');
    Route::resource('favorites', 'FavoritesController');

    // Route::get('favorites', [
    //     'as' => 'user-favorites',
    //     'uses' => 'User\FavoritesController'
    // ]);

    // Route::get('history/{type}', [
    //     'as' => 'user-favorites',
    //     'uses' => 'User\HistoryController'
    // ]);
});

// Route::group(['before' => 'auth', 'prefix' => 'history'], function() {
//     Route::get('/', function() {
//         return redirect()->route('videos-history');
//     });

//     // Route::get('videos', [
//     //     'as' => 'videos-history',
//     //     'uses' => 'Auth\UserController@history'
//     // ]);

//     // Route::get('search', [
//     //     'as' => 'search-history',
//     //     'uses' => 'Auth\UserController@history'
//     // ]);
// });

Route::controllers([
    // 'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController'
]);
