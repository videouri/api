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
    'uses' => 'PagesController@home',
]);

Route::get('search', [
    'as'   => 'search',
    'uses' => 'PagesController@search',
]);

Route::get('info/{view}/{part?}', 'PagesController@ifno');

Route::get('video', 'VideoController@index');
Route::get('video/{id}/{videoSlug?}', 'VideoController@show');

////////////
// Topics //
////////////
Route::group(['prefix' => 'topic'], function () {
    Route::get('music', [
        'as'   => 'topic.music',
        'uses' => 'TopicsController@music',
    ]);

    Route::get('sports', [
        'as'   => 'topic.sports',
        'uses' => 'TopicsController@sports',
    ]);

    Route::get('trailers', [
        'as'   => 'topic.trailers',
        'uses' => 'TopicsController@trailers',
    ]);

    Route::get('news', [
        'as'   => 'topic.news',
        'uses' => 'TopicsController@news',
    ]);

    Route::get('best-of-week', [
        'as'   => 'topic.best-of-week',
        'uses' => 'TopicsController@bestOfWeek',
    ]);
});

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
    'namespace'  => 'User',
], function () {
    Route::resource('profile', 'ProfileController', [
        'only' => [
            'index',
        ],
    ]);

    Route::resource('settings', 'SettingsController', [
        'only' => [
            'index',
        ],
    ]);

    Route::resource('history', 'HistoryController', [
        'only' => [
            'index',
            'show',
        ],
    ]);

    Route::resource('favorites', 'FavoritesController', [
        'only' => [
            'index',
        ],
    ]);

    Route::resource('watch-later', 'WatchLaterController', [
        'only' => [
            'index',
        ],
    ]);
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
    $api->controller('history', 'App\Http\Controllers\Api\HistoryController');
    $api->controller('search', 'App\Http\Controllers\Api\SearchController');
    $api->controller('user', 'App\Http\Controllers\Api\UserController');
});

//protected with JWT
// $api->version('v1', ['middleware' => 'api.auth'], function ($api) {
// });
