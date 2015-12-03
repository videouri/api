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
Route::get('register', [
    'as'   => 'register',
    'uses' => 'Auth\AuthController@getRegister'
]);
Route::get('login/{provider?}', [
    'as' => 'login',
    'uses' => 'Auth\AuthController@getLogin'
]);

Route::post('join', 'Auth\AuthController@postRegister');
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
});

Route::controllers([
    // 'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController'
]);

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    /*
     * used for Json Web Token Authentication - https://scotch.io/tutorials/token-based-authentication-for-angularjs-and-laravel-apps
     * Make sure to re-enable CSRF middleware if you're disabling JWT
     */
    $api->controller('authenticate', 'App\Http\Controllers\AuthenticateController');
    $api->controller('videos', 'App\Http\Controllers\Api\VideosController');
});

//protected with JWT
// $api->version('v1', ['middleware' => 'api.auth'], function ($api) {
// });
