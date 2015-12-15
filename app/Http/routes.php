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
    'uses' => 'PagesController@home'
]);

Route::get('search', [
    'as'   => 'search',
    'uses' => 'PagesController@search'
]);

Route::get('info/{view}/{part?}', 'PagesController@ifno');

Route::get('video', 'VideoController@index');
Route::get('video/{id}/{videoSlug?}', 'VideoController@show');

// Authentication routes...
Route::get('login', 'Auth\AuthController@getLogin');
Route::get('login/{provider}', 'Auth\AuthController@redirectToProvider');
Route::get('login/{provider}/callback', 'Auth\AuthController@handleProviderCallback');
Route::post('login', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('register', 'Auth\AuthController@getRegister');
Route::post('register', 'Auth\AuthController@postRegister');

// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

// User panel
Route::group([
    'prefix'     => 'user/{name}',
    'middleware' => 'auth',
    'namespace'  => 'User'
], function () {
    Route::resource('profile', 'ProfileController');
    Route::resource('settings', 'SettingsController');
    Route::resource('history', 'HistoryController');
    Route::resource('favorites', 'FavoritesController');
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    /*
     * used for Json Web Token Authentication
     *     https://scotch.io/tutorials/token-based-authentication-for-angularjs-and-laravel-apps
     * Make sure to re-enable CSRF middleware if you're disabling JWT
     */
    $api->controller('authenticate', 'App\Http\Controllers\AuthenticateController');
    $api->controller('videos', 'App\Http\Controllers\Api\VideosController');
    $api->controller('search', 'App\Http\Controllers\Api\SearchController');
    $api->controller('user', 'App\Http\Controllers\Api\UserController');
});

//protected with JWT
// $api->version('v1', ['middleware' => 'api.auth'], function ($api) {
// });
