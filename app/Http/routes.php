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

Route::get('/', 'HomeController@index');

Route::get('results', [
    'as'   => 'results',
    'uses' => 'SearchController@getVdeos'
]);

Route::get('video/{id}/{videoSlug?}', 'VideoController@show');
Route::get('info/{view}/{part?}', 'InfoController@show');


// User auth related methods
// Route::get('join', 'Auth\AuthController@getRegister');
// Route::post('join', 'Auth\AuthController@postRegister');
// Route::get('login/{provider?}', 'Auth\AuthController@getLogin');
// Route::post('login', 'Auth\AuthController@postLogin');
// Route::get('logout', 'Auth\AuthController@getLogout');

Route::controllers([
    // 'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);